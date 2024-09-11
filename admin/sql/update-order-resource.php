<?php
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $resource_id = intval($_POST['resource_id']);
    $quantity = floatval($_POST['quantity']);

    $sql = "UPDATE order_course_resources 
            SET quantity = ? 
            WHERE order_id = ? AND id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dii", $quantity, $order_id, $resource_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>