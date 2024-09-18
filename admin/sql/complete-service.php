<?php
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $queue_id = isset($_POST['queue_id']) ? intval($_POST['queue_id']) : 0;

    if ($queue_id == 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid queue ID']);
        exit;
    }

    $sql = "UPDATE service_queue SET service_status = 'completed' WHERE queue_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $queue_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating status: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();