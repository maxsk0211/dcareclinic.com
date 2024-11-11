<?php
header('Content-Type: application/json');

$directory = "../../img/drawing/default/";

// ตรวจสอบว่าโฟลเดอร์มีอยู่หรือไม่
if (!file_exists($directory)) {
    echo json_encode([
        'success' => false,
        'message' => 'ไม่พบโฟลเดอร์',
        'images' => []
    ]);
    exit;
}

// อ่านรายการไฟล์ในโฟลเดอร์
$files = scandir($directory);

// กรองเอาเฉพาะไฟล์รูปภาพ
$images = array_filter($files, function($file) {
    // ไม่เอา . และ ..
    if ($file === '.' || $file === '..') {
        return false;
    }
    // เช็คนามสกุลไฟล์
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    return in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
});

// ส่งข้อมูลกลับในรูปแบบ JSON
echo json_encode([
    'success' => true,
    'images' => array_values($images) // array_values เพื่อรีเซ็ต index ให้เป็นตัวเลขปกติ
]);
?>