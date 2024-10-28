<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

$voucherCode = $_GET['code'] ?? '';
$cusId = $_GET['cus_id'] ?? '';

if (empty($voucherCode) || empty($cusId)) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

try {
    // เริ่ม transaction
    $conn->begin_transaction();

    // ตรวจสอบว่าลูกค้ามีบัตรกำนัลที่ใช้งานได้อยู่แล้วหรือไม่
    $activeVoucherQuery = "SELECT voucher_id 
                          FROM gift_vouchers 
                          WHERE customer_id = ? 
                          AND status IN ('unused', 'used')
                          AND expire_date >= CURDATE()
                          FOR UPDATE";
    $stmt = $conn->prepare($activeVoucherQuery);
    if (!$stmt) {
        throw new Exception("Error preparing active voucher query: " . $conn->error);
    }
    
    $stmt->bind_param("i", $cusId);
    if (!$stmt->execute()) {
        throw new Exception("Error executing active voucher query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        throw new Exception('คุณมีบัตรกำนัลที่ใช้งานอยู่ กรุณายกเลิกบัตรเดิมก่อนใช้บัตรใหม่');
    }

    // ตรวจสอบบัตรกำนัลที่ต้องการใช้
    $query = "SELECT * 
              FROM gift_vouchers 
              WHERE voucher_code = ? 
              AND status = 'unused' 
              AND expire_date >= CURDATE()
              AND (customer_id IS NULL OR customer_id = ?)
              FOR UPDATE";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Error preparing voucher query: " . $conn->error);
    }

    $stmt->bind_param("si", $voucherCode, $cusId);
    if (!$stmt->execute()) {
        throw new Exception("Error executing voucher query: " . $stmt->error);
    }

    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // ตรวจสอบว่าบัตรกำนัลถูกใช้โดยลูกค้าคนอื่นหรือไม่
        $checkOtherCustomerQuery = "SELECT customer_id 
                                   FROM gift_vouchers 
                                   WHERE voucher_code = ?";
        $checkStmt = $conn->prepare($checkOtherCustomerQuery);
        $checkStmt->bind_param("s", $voucherCode);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $voucherData = $checkResult->fetch_assoc();
            if ($voucherData['customer_id'] !== null && $voucherData['customer_id'] != $cusId) {
                throw new Exception('บัตรกำนัลนี้ถูกใช้โดยลูกค้าท่านอื่นแล้ว');
            }
        }
        
        throw new Exception('บัตรกำนัลไม่ถูกต้อง หมดอายุ หรือไม่สามารถใช้งานได้');
    }

    $voucher = $result->fetch_assoc();
    $conn->commit();

    echo json_encode([
        'success' => true, 
        'voucher' => $voucher,
        'has_active_voucher' => false
    ]);

} catch (Exception $e) {
    if ($conn->connect_error === false) {
        $conn->rollback();
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($checkStmt)) {
        $checkStmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}