<?php
require '../../dbcon.php';

$resource_id = intval($_POST['resource_id']);
$quantity = floatval($_POST['quantity']);

$sql = "UPDATE order_course_resources SET quantity = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("di", $quantity, $resource_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();