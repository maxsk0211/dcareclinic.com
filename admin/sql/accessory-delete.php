<?php
session_start();
include '../chk-session.php';
require '../../dbcon.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    // รับค่า id และทำการ escape
    $acc_id = mysqli_real_escape_string($conn, $_GET['id']);

    // ตรวจสอบว่าอุปกรณ์นี้ถูกใช้งานอยู่หรือไม่ (ถ้ามีตารางอื่นที่อ้างอิงถึง)
    // ตัวอย่าง: $check_sql = "SELECT * FROM some_table WHERE acc_id = '$acc_id'";
    // $check_result = mysqli_query($conn, $check_sql);
    // if (mysqli_num_rows($check_result) > 0) {
    //     $_SESSION['msg_error'] = "ไม่สามารถลบอุปกรณ์นี้ได้ เนื่องจากมีการใช้งานในระบบ";
    //     header("Location: ../accessories.php");
    //     exit();
    // }

    // ทำการลบข้อมูล
    $sql = "DELETE FROM accessories WHERE acc_id = '$acc_id'";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg_ok'] = "ลบข้อมูลอุปกรณ์การแพทย์เรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . mysqli_error($conn);
    }
} else {
    $_SESSION['msg_error'] = "ไม่พบข้อมูล ID ที่ต้องการลบ";
}

// ปิดการเชื่อมต่อ
mysqli_close($conn);

// Redirect กลับไปยังหน้าแสดงรายการอุปกรณ์การแพทย์
header("Location: ../accessories.php");
exit();
?>