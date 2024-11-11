<?php
header('Content-Type: application/json');

if (!isset($_POST['filename'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่ได้ระบุชื่อไฟล์'
    ]);
    exit;
}

$filename = $_POST['filename'];
$filepath = "../../img/drawing/default/" . $filename;

// ตรวจสอบว่าไฟล์มีอยู่จริง
if (!file_exists($filepath)) {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่พบไฟล์ที่ต้องการลบ'
    ]);
    exit;
}

// ลบไฟล์
if (unlink($filepath)) {
    echo json_encode([
        'success' => true,
        'message' => 'ลบไฟล์สำเร็จ'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่สามารถลบไฟล์ได้'
    ]);
}
?>