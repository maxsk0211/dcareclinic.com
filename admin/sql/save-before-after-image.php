<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $opd_id = $_POST['opd_id'];
    $description = $_POST['imageDescription'];
    $image_type = $_POST['imageType'];

    $target_dir = "../../img/before-after/";
    $file_extension = pathinfo($_FILES["beforeAfterImage"]["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES["beforeAfterImage"]["tmp_name"], $target_file)) {
        $image_path = $new_filename; // เก็บเฉพาะชื่อไฟล์

        $sql = "INSERT INTO before_after_images (opd_id, image_path, description, image_type) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $opd_id, $image_path, $description, $image_type);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Image saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save image data']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();