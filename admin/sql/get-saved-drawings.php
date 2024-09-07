<?php
require '../../dbcon.php';

if (isset($_GET['opd_id'])) {
    $opd_id = intval($_GET['opd_id']);
    
    $sql = "SELECT id, image_path, created_at FROM opd_drawings WHERE opd_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $opd_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $drawings = [];
    while ($row = $result->fetch_assoc()) {
        $row['created_at'] = date('d/m/Y H:i', strtotime($row['created_at']));
        $drawings[] = $row;
    }
    
    echo json_encode($drawings);
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'No OPD ID provided']);
}

$conn->close();