<?php
session_start();
require '../../dbcon.php';

// ตรวจสอบสิทธิ์
if ($_SESSION['position_id'] != 1 && $_SESSION['position_id'] != 2) {
    echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ในการยกเลิกค่ามัดจำ']);
    exit;
}

header('Content-Type: application/json');


$response = ['success' => false, 'message' => ''];

try {
    if (!isset($_POST['order_id']) || !isset($_POST['reason'])) {
        throw new Exception('ข้อมูลไม่ครบถ้วน');
    }

    $order_id = intval($_POST['order_id']);
    $reason = trim($_POST['reason']);
    $deposit_amount = floatval($_POST['deposit_amount']);
    $user_id = $_SESSION['users_id'];

    // เริ่ม transaction
    $conn->begin_transaction();

    try {
        // บันทึกข้อมูลการยกเลิกมัดจำลงในตาราง deposit_cancellation_logs
        $sql_log = "INSERT INTO deposit_cancellation_logs 
                   (order_id, cancelled_by, deposit_amount, cancellation_reason) 
                   VALUES (?, ?, ?, ?)";
        $stmt_log = $conn->prepare($sql_log);
        if (!$stmt_log) {
            throw new Exception('Failed to prepare log statement: ' . $conn->error);
        }
        
        $stmt_log->bind_param("iids", $order_id, $user_id, $deposit_amount, $reason);
        if (!$stmt_log->execute()) {
            throw new Exception('Failed to execute log statement: ' . $stmt_log->error);
        }

        // อัพเดทข้อมูลในตาราง order_course
        $sql_update = "UPDATE order_course SET 
                      deposit_amount = 0,
                      deposit_payment_type = NULL,
                      deposit_slip_image = NULL,
                      deposit_date = NULL
                      WHERE oc_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        if (!$stmt_update) {
            throw new Exception('Failed to prepare update statement: ' . $conn->error);
        }
        
        $stmt_update->bind_param("i", $order_id);
        if (!$stmt_update->execute()) {
            throw new Exception('Failed to execute update statement: ' . $stmt_update->error);
        }

        // Commit transaction
        if (!$conn->commit()) {
            throw new Exception('Failed to commit transaction');
        }
        
        $response['success'] = true;
        $response['message'] = 'ยกเลิกค่ามัดจำเรียบร้อยแล้ว';
        
    } catch (Exception $e) {
        $conn->rollback();
        throw new Exception('เกิดข้อผิดพลาดในการยกเลิกมัดจำ: ' . $e->getMessage());
    }

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// Log debugging information
error_log(print_r($response, true));

echo json_encode($response);

// ปิดการเชื่อมต่อ
if (isset($stmt_log)) $stmt_log->close();
if (isset($stmt_update)) $stmt_update->close();
$conn->close();
?>