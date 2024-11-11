<?php
header('Content-Type: application/json');

$targetDir = "../../img/drawing/default/";

// ตรวจสอบว่ามีการอัพโหลดไฟล์มาหรือไม่
if (!isset($_FILES['new_image'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่พบไฟล์ที่อัพโหลด'
    ]);
    exit;
}

$file = $_FILES['new_image'];
$fileName = basename($file['name']);
$targetPath = $targetDir . $fileName;

// สร้างชื่อไฟล์ใหม่ถ้าชื่อซ้ำ
$newFileName = $fileName;
$counter = 1;
while (file_exists($targetDir . $newFileName)) {
    $info = pathinfo($fileName);
    $newFileName = $info['filename'] . '_' . $counter . '.' . $info['extension'];
    $counter++;
}
$targetPath = $targetDir . $newFileName;

// ตรวจสอบว่าเป็นรูปภาพจริงหรือไม่
$check = getimagesize($file['tmp_name']);
if ($check === false) {
    echo json_encode([
        'success' => false,
        'message' => 'ไฟล์ที่อัพโหลดต้องเป็นรูปภาพเท่านั้น'
    ]);
    exit;
}

// อัพโหลดไฟล์
if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo json_encode([
        'success' => true,
        'message' => 'อัพโหลดไฟล์สำเร็จ'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์'
    ]);
}
?>