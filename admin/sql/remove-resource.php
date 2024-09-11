<?php
require '../../dbcon.php';

$resource_id = intval($_POST['resource_id']);

$sql = "DELETE FROM order_course_resources WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resource_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();