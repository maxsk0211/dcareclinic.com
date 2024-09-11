<?php
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $course_id = intval($_POST['course_id']);
    $resource_type = $_POST['resource_type'];
    $resource_id = intval($_POST['resource_id']);

    $sql = "DELETE FROM order_course_resources 
            WHERE order_id = ? AND course_id = ? AND resource_type = ? AND resource_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisi", $order_id, $course_id, $resource_type, $resource_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}