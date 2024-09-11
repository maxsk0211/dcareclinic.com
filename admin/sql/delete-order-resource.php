<?php
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $resource_id = intval($_POST['resource_id']);

    $sql = "DELETE FROM order_course_resources 
            WHERE order_id = ? AND id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $resource_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>