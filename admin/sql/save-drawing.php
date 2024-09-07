<?php
session_start();
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image']) && isset($_POST['opd_id'])) {
    $image_data = $_POST['image'];
    $opd_id = intval($_POST['opd_id']);
    
    $image_data = str_replace('data:image/png;base64,', '', $image_data);
    $image_data = str_replace(' ', '+', $image_data);
    $image_data = base64_decode($image_data);

    $file_name = 'opd_drawing_' . time() . '.png';
    $file_path = '../../img/drawing/' . $file_name;

    if (file_put_contents($file_path, $image_data)) {
        // บันทึกข้อมูลลงในฐานข้อมูล
        $sql = "INSERT INTO opd_drawings (opd_id, image_path) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $opd_id, $file_name);
        
        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;  // รับ ID ของรายการที่เพิ่งถูกเพิ่ม
            echo json_encode(['success' => true, 'filename' => $file_name, 'id' => $new_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save image']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request or missing data']);
}