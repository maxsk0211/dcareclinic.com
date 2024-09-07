<?php
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Get the file name before deleting the record
    $sql = "SELECT image_path FROM opd_drawings WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $file_to_delete = $row['image_path'];

    // Delete the record from the database
    $sql = "DELETE FROM opd_drawings WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Delete the file from the server
        $file_path = '../../img/drawing/' . $file_to_delete;
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        echo json_encode(['success' => true, 'deleted_file' => $file_to_delete]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();