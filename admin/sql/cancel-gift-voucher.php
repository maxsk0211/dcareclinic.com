<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$cusId = $data['cus_id'] ?? '';

if (empty($cusId)) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสลูกค้า']);
    exit;
}

$conn->begin_transaction();

try {
    // ค้นหาบัตรกำนัลที่กำลังใช้งาน
    $query = "SELECT * FROM gift_vouchers 
              WHERE customer_id = ? 
              AND status IN ('unused', 'used')
              AND expire_date >= CURDATE()";
              
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $cusId);
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute search: ' . $stmt->error);
    }
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('ไม่พบบัตรกำนัลที่กำลังใช้งาน');
    }

    $voucher = $result->fetch_assoc();
    
    // อัพเดตบัตรกำนัล
    $updateQuery = "UPDATE gift_vouchers 
                   SET status = 'unused',
                       customer_id = NULL,
                       first_used_at = NULL,
                       remaining_amount = amount,
                       used_in_order = NULL,
                       used_at = NULL
                   WHERE voucher_id = ?";
                   
    $updateStmt = $conn->prepare($updateQuery);
    if ($updateStmt === false) {
        throw new Exception('Failed to prepare update statement: ' . $conn->error);
    }
    
    $voucherId = $voucher['voucher_id'];
    $updateStmt->bind_param("i", $voucherId);
    if (!$updateStmt->execute()) {
        throw new Exception('Failed to update voucher: ' . $updateStmt->error);
    }

    // บันทึกประวัติการยกเลิก
    $logQuery = "INSERT INTO activity_logs 
                 (user_id, action, entity_type, entity_id, details, branch_id) 
                 VALUES (?, 'cancel', 'voucher', ?, '0', ?)";
                 
    $logStmt = $conn->prepare($logQuery);
    if ($logStmt === false) {
        throw new Exception('Failed to prepare log statement: ' . $conn->error);
    }
    
    $userId = $_SESSION['users_id'] ?? 0;
    $branchId = $_SESSION['branch_id'] ?? 0;
    $logStmt->bind_param("iii", $userId, $voucherId, $branchId);
    $logStmt->execute();

    $conn->commit();

    $response = [
        'success' => true, 
        'message' => 'ยกเลิกบัตรกำนัลสำเร็จ บัตรสามารถนำกลับมาใช้ใหม่ได้',
        'status' => 'unused'
    ];
    
    echo json_encode($response);

} catch (Exception $e) {
    $conn->rollback();
    $error = [
        'success' => false, 
        'message' => $e->getMessage(),
        'error_details' => $conn->error
    ];
    echo json_encode($error);
} finally {
    // ปิด statements ถ้ามีการสร้างขึ้นมา
    if (isset($stmt) && $stmt !== false) $stmt->close();
    if (isset($updateStmt) && $updateStmt !== false) $updateStmt->close();
    if (isset($logStmt) && $logStmt !== false) $logStmt->close();
}