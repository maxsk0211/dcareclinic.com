<?php
session_start();
// include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์มและทำการ escape
    $acc_type_id = mysqli_real_escape_string($conn, $_POST['acc_type_id']);
    $acc_type_name = mysqli_real_escape_string($conn, $_POST['acc_type_name']);

    // ตรวจสอบว่าชื่อประเภทอุปกรณ์การแพทย์ซ้ำหรือไม่ (ยกเว้นตัวเอง)
    $check_sql = "SELECT * FROM acc_type WHERE acc_type_name = '$acc_type_name' AND acc_type_id != '$acc_type_id'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        // ถ้ามีชื่อซ้ำ
        $_SESSION['msg_error'] = "ชื่อประเภทอุปกรณ์การแพทย์นี้มีอยู่แล้ว กรุณาใช้ชื่ออื่น";
    } else {
        // ถ้าไม่ซ้ำ ทำการอัพเดตข้อมูล
        $sql = "UPDATE acc_type SET acc_type_name = '$acc_type_name' WHERE acc_type_id = '$acc_type_id'";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['msg_ok'] = "อัพเดตข้อมูลประเภทอุปกรณ์การแพทย์เรียบร้อยแล้ว";
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการอัพเดตข้อมูล: " . mysqli_error($conn);
        }
    }
} else {
    $_SESSION['msg_error'] = "ไม่พบข้อมูลที่ส่งมา";
}

// ปิดการเชื่อมต่อ
mysqli_close($conn);

// Redirect กลับไปยังหน้าแสดงรายการประเภทอุปกรณ์การแพทย์
header("Location: ../acc-type.php");
exit();
?>