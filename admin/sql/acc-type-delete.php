<?php
session_start();
// include '../chk-session.php';
require '../../dbcon.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    // รับค่า id และทำการ escape
    $acc_type_id = mysqli_real_escape_string($conn, $_GET['id']);

    // ตรวจสอบว่าประเภทอุปกรณ์นี้ถูกใช้งานอยู่หรือไม่
    $check_sql = "SELECT * FROM accessories WHERE acc_type_id = '$acc_type_id'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        // ถ้ามีการใช้งานอยู่
        $_SESSION['msg_error'] = "ไม่สามารถลบประเภทอุปกรณ์นี้ได้ เนื่องจากมีอุปกรณ์ที่ใช้ประเภทนี้อยู่";
    } else {
        // ถ้าไม่มีการใช้งาน ทำการลบข้อมูล
        $sql = "DELETE FROM acc_type WHERE acc_type_id = '$acc_type_id'";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['msg_ok'] = "ลบข้อมูลประเภทอุปกรณ์การแพทย์เรียบร้อยแล้ว";
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . mysqli_error($conn);
        }
    }
} else {
    $_SESSION['msg_error'] = "ไม่พบข้อมูล ID ที่ต้องการลบ";
}

// ปิดการเชื่อมต่อ
mysqli_close($conn);

// Redirect กลับไปยังหน้าแสดงรายการประเภทอุปกรณ์การแพทย์
header("Location: ../acc-type.php");
exit();
?>