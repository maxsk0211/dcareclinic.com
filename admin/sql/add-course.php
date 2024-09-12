<?php
require '../../dbcon.php';

$order_id = intval($_POST['order_id']);
$course_id = intval($_POST['course_id']);
$amount = intval($_POST['amount']);
$price = floatval($_POST['price']);

$sql = "INSERT INTO order_detail (oc_id, course_id, od_amount, od_price) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiid", $order_id, $course_id, $amount, $price);

if ($stmt->execute()) {
    // เพิ่มทรัพยากรเริ่มต้นสำหรับคอร์สนี้
    $sql_resources = "INSERT INTO order_course_resources (order_id, course_id, resource_type, resource_id, quantity)
                      SELECT ?, ?, resource_type, resource_id, quantity
                      FROM course_resources
                      WHERE course_id = ?";
    $stmt_resources = $conn->prepare($sql_resources);
    $stmt_resources->bind_param("iii", $order_id, $course_id, $course_id);
    $stmt_resources->execute();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();