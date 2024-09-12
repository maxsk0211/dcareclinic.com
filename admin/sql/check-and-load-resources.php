<?php
require '../../dbcon.php';

$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

if ($order_id === 0 || $course_id === 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid order_id or course_id']);
    exit;
}

// ตรวจสอบว่ามีข้อมูลใน order_course_resources หรือไม่
$check_sql = "SELECT COUNT(*) as count FROM order_course_resources WHERE order_id = ? AND course_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $order_id, $course_id);
$check_stmt->execute();
$result = $check_stmt->get_result();
$count = $result->fetch_assoc()['count'];

if ($count == 0) {
    // ถ้าไม่มีข้อมูล ให้ดึงจาก course_resources และเพิ่มใน order_course_resources
    $insert_sql = "INSERT INTO order_course_resources (order_id, course_id, resource_type, resource_id, quantity)
                   SELECT ?, ?, resource_type, resource_id, quantity
                   FROM course_resources
                   WHERE course_id = ?";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iii", $order_id, $course_id, $course_id);
    
    if ($insert_stmt->execute()) {
        echo json_encode(['success' => true, 'resourcesAdded' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    $insert_stmt->close();
} else {
    echo json_encode(['success' => true, 'resourcesAdded' => false]);
}

$check_stmt->close();
$conn->close();