<?php
session_start();
include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์มและทำการ escape
    $acc_id = mysqli_real_escape_string($conn, $_POST['acc_id']);
    $acc_name = mysqli_real_escape_string($conn, $_POST['acc_name']);
    $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
    $acc_type_id = mysqli_real_escape_string($conn, $_POST['acc_type_id']);
    $acc_properties = mysqli_real_escape_string($conn, $_POST['acc_properties']);
    $acc_unit_id = mysqli_real_escape_string($conn, $_POST['acc_unit_id']);
    $acc_status = mysqli_real_escape_string($conn, $_POST['acc_status']);

    // สร้างคำสั่ง SQL UPDATE
    $sql = "UPDATE accessories SET 
            acc_name = '$acc_name', 
            branch_id = '$branch_id', 
            acc_type_id = '$acc_type_id', 
            acc_properties = '$acc_properties', 
            acc_unit_id = '$acc_unit_id', 
            acc_status = '$acc_status' 
            WHERE acc_id = '$acc_id'";
    
    // ดำเนินการอัพเดตข้อมูล
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg_ok'] = "อัพเดตข้อมูลอุปกรณ์เรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการอัพเดตข้อมูล: " . mysqli_error($conn);
    }
} else {
    $_SESSION['msg_error'] = "ไม่พบข้อมูลที่ส่งมา";
}

// ปิดการเชื่อมต่อ
mysqli_close($conn);

// Redirect กลับไปยังหน้าแสดงรายการอุปกรณ์
header("Location: ../accessories.php");
exit();
?>