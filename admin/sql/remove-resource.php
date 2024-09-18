<?php
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resource_id = isset($_POST['resource_id']) ? intval($_POST['resource_id']) : 0;
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

    if ($resource_id == 0 || $order_id == 0 || $course_id == 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }

    $sql = "DELETE FROM order_course_resources WHERE id = ? AND order_id = ? AND course_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare statement failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("iii", $resource_id, $order_id, $course_id);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No resource found to delete']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error executing query: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();