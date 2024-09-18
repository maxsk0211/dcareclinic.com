<?php
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $resource_type = isset($_POST['resource_type']) ? $_POST['resource_type'] : '';
    $resource_id = isset($_POST['resource_id']) ? intval($_POST['resource_id']) : 0;
    $quantity = isset($_POST['quantity']) ? floatval($_POST['quantity']) : 0;

    if ($order_id == 0 || $course_id == 0 || empty($resource_type) || $resource_id == 0 || $quantity == 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }

    $sql = "INSERT INTO order_course_resources (order_id, course_id, resource_type, resource_id, quantity) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare statement failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("iisid", $order_id, $course_id, $resource_type, $resource_id, $quantity);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error executing query: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();