<?php
session_start();
// include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์มและทำการ escape
    $acc_name = mysqli_real_escape_string($conn, $_POST['acc_name']);
    $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
    $acc_type_id = mysqli_real_escape_string($conn, $_POST['acc_type_id']);
    $acc_properties = mysqli_real_escape_string($conn, $_POST['acc_properties']);
    $acc_unit_id = mysqli_real_escape_string($conn, $_POST['acc_unit_id']);
    $acc_status = mysqli_real_escape_string($conn, $_POST['acc_status']);

    // สร้างคำสั่ง SQL INSERT
    $sql = "INSERT INTO accessories (acc_name,branch_id, acc_type_id, acc_properties,  acc_unit_id, acc_status) 
            VALUES ('$acc_name','$branch_id', '$acc_type_id', '$acc_properties', '$acc_unit_id', '$acc_status')";
    
    // ดำเนินการเพิ่มข้อมูล
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg_ok'] = "เพิ่มข้อมูลอุปกรณ์ใหม่เรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . mysqli_error($conn);
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