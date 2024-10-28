<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$voucherCode = $data['voucher_code'] ?? '';
$cusId = $data['cus_id'] ?? '';

if (empty($voucherCode) || empty($cusId)) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// เริ่ม transaction
$conn->begin_transaction();

try {
    // ตรวจสอบบัตรกำนัลอีกครั้ง
    $query = "SELECT * FROM gift_vouchers 
              WHERE voucher_code = ? 
              AND status = 'unused' 
              AND expire_date >= CURDATE()
              FOR UPDATE";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $voucherCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('บัตรกำนัลไม่สามารถใช้งานได้');
    }

    $voucher = $result->fetch_assoc();

    // อัพเดตสถานะบัตรกำนัลและผูกกับลูกค้า
    $updateQuery = "UPDATE gift_vouchers 
                   SET customer_id = ?, 
                       status = ?, 
                       first_used_at = NOW() 
                   WHERE voucher_code = ?";
    $stmt = $conn->prepare($updateQuery);
    $status = $voucher['discount_type'] === 'percent' ? 'used' : 'unused';
    $stmt->bind_param("iss", $cusId, $status, $voucherCode);
    $stmt->execute();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'บันทึกการใช้บัตรกำนัลสำเร็จ']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}