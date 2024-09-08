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
        $opd_completed = ($row['opd_status'] == 1);
        echo json_encode(['opd_completed' => $opd_completed]);
    } else {
        echo json_encode(['opd_completed' => false]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'No queue ID provided']);
}

$conn->close();