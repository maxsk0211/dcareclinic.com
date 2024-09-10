<?php
session_start();
require '../../dbcon.php';

if (isset($_GET['queue_id'])) {
    $queue_id = intval($_GET['queue_id']);
    
    $sql = "SELECT opd_status FROM opd WHERE queue_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $queue_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['has_opd' => true, 'opd_status' => $row['opd_status']]);
    } else {
        echo json_encode(['has_opd' => false, 'opd_status' => 0]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'No queue ID provided']);
}

$conn->close();