<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $imageId = $_POST['id'];

    // ดึงข้อมูลรูปภาพ
    $sql = "SELECT image_path FROM before_after_images WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $imageId);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc();

    if ($image) {
        // ลบไฟล์รูปภาพ
        $file_path = "../../img/before-after/" . $image['image_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // ลบข้อมูลจากฐานข้อมูล
        $sql = "DELETE FROM before_after_images WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $imageId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete image data']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Image not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();