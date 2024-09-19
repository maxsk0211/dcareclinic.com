<?php
session_start();
require '../../dbcon.php';

// ตรวจสอบสิทธิ์
if ($_SESSION['position_id'] != 1 && $_SESSION['position_id'] != 2) {
    echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ในการยกเลิกค่ามัดจำ']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);

    // เริ่ม transaction
    $conn->begin_transaction();

    try {
        // ยกเลิกค่ามัดจำ
        $sql = "UPDATE order_course SET 
                deposit_amount = 0, 
                deposit_payment_type = NULL, 
                deposit_slip_image = NULL, 
                deposit_date = NULL 
                WHERE oc_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'ยกเลิกมัดจำสำเร็จ']);
    } catch (Exception $e) {
        $conn->rollback();
        error_log('Error in cancel-deposit.php: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();