<?php
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $course_id = intval($_POST['course_id']);
    $resource_type = $_POST['resource_type'];
    $resource_id = intval($_POST['resource_id']);
    $quantity = floatval($_POST['quantity']);

    $sql = "INSERT INTO order_course_resources (order_id, course_id, resource_type, resource_id, quantity) 
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE quantity = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisidi", $order_id, $course_id, $resource_type, $resource_id, $quantity, $quantity);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}