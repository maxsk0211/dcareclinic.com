<?php
session_start();
require_once '../../dbcon.php';
header('Content-Type: application/json');

$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
$old_price = isset($_POST['old_price']) ? floatval($_POST['old_price']) : 0;
$new_price = isset($_POST['new_price']) ? floatval($_POST['new_price']) : 0;
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
$adjusted_by = $_SESSION['users_id'];

if (!$order_id || !$course_id || !$reason) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

try {
    $conn->begin_transaction();

    // อัพเดทราคาในตาราง order_detail
    $update_sql = "UPDATE order_detail 
                  SET od_price = ? 
                  WHERE oc_id = ? AND course_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("dii", $new_price, $order_id, $course_id);
    $stmt->execute();

    // บันทึกประวัติการแก้ไขราคา
    $log_sql = "INSERT INTO price_adjustment_logs 
                (order_id, course_id, old_price, new_price, reason, adjusted_by) 
                VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($log_sql);
    $stmt->bind_param("iiddsi", $order_id, $course_id, $old_price, $new_price, $reason, $adjusted_by);
    $stmt->execute();

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $conn->close();
}