<?php
session_start();
require_once '../../dbcon.php';
header('Content-Type: application/json');

$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
$old_detail = isset($_POST['old_detail']) ? trim($_POST['old_detail']) : '';
$new_detail = isset($_POST['new_detail']) ? trim($_POST['new_detail']) : '';
$updated_by = $_SESSION['users_id'];

if (!$order_id || !$course_id) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

try {
    $conn->begin_transaction();

    // อัพเดทรายละเอียดในตาราง order_detail
    $update_sql = "UPDATE order_detail 
                  SET detail = ? 
                  WHERE oc_id = ? AND course_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sii", $new_detail, $order_id, $course_id);
    $stmt->execute();

    // บันทึกประวัติการแก้ไขใน course_detail_logs
    $log_sql = "INSERT INTO course_detail_logs 
                (order_id, course_id, old_detail, new_detail, updated_by) 
                VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($log_sql);
    $stmt->bind_param("iissi", $order_id, $course_id, $old_detail, $new_detail, $updated_by);
    $stmt->execute();

    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    
} finally {
    $conn->close();
}