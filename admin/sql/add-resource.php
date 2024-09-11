<?php
require '../../dbcon.php';

$order_id = intval($_POST['order_id']);
$course_id = intval($_POST['course_id']);
$resource_type = $_POST['resource_type'];
$resource_id = intval($_POST['resource_id']);
$quantity = floatval($_POST['quantity']);

$sql = "INSERT INTO order_course_resources (order_id, course_id, resource_type, resource_id, quantity) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisid", $order_id, $course_id, $resource_type, $resource_id, $quantity);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'resource_id' => $conn->insert_id]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();