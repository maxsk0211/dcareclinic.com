<?php
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $course_id = intval($_POST['course_id']);

    // เริ่ม transaction
    $conn->begin_transaction();

    try {
        // ลบทรัพยากรที่เกี่ยวข้องกับคอร์สนี้
        $sql_delete_resources = "DELETE FROM order_course_resources 
                                 WHERE order_id = ? AND course_id = ?";
        $stmt = $conn->prepare($sql_delete_resources);
        $stmt->bind_param("ii", $order_id, $course_id);
        $stmt->execute();

        // ลบคอร์สจาก order_detail
        $sql_delete_course = "DELETE FROM order_detail 
                              WHERE oc_id = ? AND course_id = ?";
        $stmt = $conn->prepare($sql_delete_course);
        $stmt->bind_param("ii", $order_id, $course_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Rollback ในกรณีที่เกิดข้อผิดพลาด
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>