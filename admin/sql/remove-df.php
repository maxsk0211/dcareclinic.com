<?php
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $record_id = isset($_POST['record_id']) ? intval($_POST['record_id']) : 0;

    if ($record_id == 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid record ID']);
        exit;
    }

    $sql = "DELETE FROM service_staff_records WHERE staff_record_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $record_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No record found to delete']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting record: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();