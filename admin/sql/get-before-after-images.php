<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

if (isset($_GET['opd_id'])) {
    $opd_id = $_GET['opd_id'];

    $sql = "SELECT * FROM before_after_images WHERE opd_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $opd_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = [
            'id' => $row['id'],
            'image_path' => $row['image_path'],
            'description' => $row['description'],
            'image_type' => $row['image_type'],
            'created_at' => $row['created_at']
        ];
    }

    echo json_encode(['success' => true, 'images' => $images]);
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'OPD ID not provided']);
}

$conn->close();