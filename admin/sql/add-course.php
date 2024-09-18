<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require '../../dbcon.php';

try {
    // ตรวจสอบว่ามีข้อมูลที่จำเป็นครบถ้วน
    if (!isset($_POST['order_id'], $_POST['course_id'], $_POST['amount'], $_POST['price'])) {
        throw new Exception("Missing required data");
    }

    $order_id = intval($_POST['order_id']);
    $course_id = intval($_POST['course_id']);
    $amount = intval($_POST['amount']);
    $price = floatval($_POST['price']);

    // ตรวจสอบความถูกต้องของข้อมูล
    if ($order_id <= 0 || $course_id <= 0 || $amount <= 0 || $price < 0) {
        throw new Exception("Invalid data provided");
    }

    // เริ่ม transaction
    $conn->begin_transaction();

    // เพิ่มคอร์สใหม่ลงในตาราง order_detail
    $sql = "INSERT INTO order_detail (oc_id, course_id, od_amount, od_price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("iiid", $order_id, $course_id, $amount, $price);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $new_od_id = $stmt->insert_id;
    $stmt->close();

    // เพิ่มทรัพยากรเริ่มต้นสำหรับคอร์สนี้
    $sql_resources = "INSERT INTO order_course_resources (order_id, course_id, resource_type, resource_id, quantity)
                      SELECT ?, ?, resource_type, resource_id, quantity
                      FROM course_resources
                      WHERE course_id = ?";
    $stmt_resources = $conn->prepare($sql_resources);
    if (!$stmt_resources) {
        throw new Exception("Prepare failed for resources: " . $conn->error);
    }

    $stmt_resources->bind_param("iii", $order_id, $course_id, $course_id);
    if (!$stmt_resources->execute()) {
        throw new Exception("Execute failed for resources: " . $stmt_resources->error);
    }
    $stmt_resources->close();

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Course added successfully', 'new_od_id' => $new_od_id]);
} catch (Exception $e) {
    // Rollback transaction ในกรณีที่เกิดข้อผิดพลาด
    $conn->rollback();

    error_log('Error in add-course.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}