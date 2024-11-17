<?php
session_start();
// include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์มและทำการ escape
    $acc_type_name = mysqli_real_escape_string($conn, $_POST['acc_type_name']);

    // ตรวจสอบว่าชื่อประเภทอุปกรณ์การแพทย์ซ้ำหรือไม่
    $check_sql = "SELECT * FROM acc_type WHERE acc_type_name = '$acc_type_name'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        // ถ้ามีชื่อซ้ำ
        $_SESSION['msg_error'] = "ชื่อประเภทอุปกรณ์การแพทย์นี้มีอยู่แล้ว กรุณาใช้ชื่ออื่น";
    } else {
        // ถ้าไม่ซ้ำ ทำการเพิ่มข้อมูล
        $sql = "INSERT INTO acc_type (acc_type_name) VALUES ('$acc_type_name')";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['msg_ok'] = "เพิ่มประเภทอุปกรณ์การแพทย์ใหม่เรียบร้อยแล้ว";
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . mysqli_error($conn);
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