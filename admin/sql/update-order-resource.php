<?php
require '../../dbcon.php';

$resource_id = isset($_POST['resource_id']) ? intval($_POST['resource_id']) : 0;
$quantity = isset($_POST['quantity']) ? floatval($_POST['quantity']) : 0;
$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

if ($resource_id === 0 || $quantity === 0 || $order_id === 0 || $course_id === 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
    exit;
}

$sql = "UPDATE order_course_resources SET quantity = ? WHERE id = ? AND order_id = ? AND course_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("diii", $quantity, $resource_id, $order_id, $course_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No rows affected. Resource might not exist or no changes made.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Execute failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();