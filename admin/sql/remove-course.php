<?php
require '../../dbcon.php';

$order_id = intval($_POST['order_id']);
$course_id = intval($_POST['course_id']);

$sql = "DELETE FROM order_detail WHERE oc_id = ? AND course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $course_id);

if ($stmt->execute()) {
    // Also remove associated resources
    $sql_resources = "DELETE FROM order_course_resources WHERE order_id = ? AND course_id = ?";
    $stmt_resources = $conn->prepare($sql_resources);
    $stmt_resources->bind_param("ii", $order_id, $course_id);
    $stmt_resources->execute();
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();