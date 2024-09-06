<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image'])) {
    $image_data = $_POST['image'];
    $image_data = str_replace('data:image/png;base64,', '', $image_data);
    $image_data = str_replace(' ', '+', $image_data);
    $image_data = base64_decode($image_data);

    $file_name = 'opd_drawing_' . time() . '.png';
    $file_path = '../../img/drawing/' . $file_name;

    if (file_put_contents($file_path, $image_data)) {
        echo "บันทึกภาพสำเร็จ: " . $file_name;
    } else {
        echo "เกิดข้อผิดพลาดในการบันทึกภาพ";
    }
} else {
    echo "ไม่มีข้อมูลภาพที่ส่งมา";
}