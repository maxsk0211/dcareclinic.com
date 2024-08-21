<?php
session_start();
include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์มและทำการ escape
    $drug_id = mysqli_real_escape_string($conn, $_POST['drug_id']);
    $drug_name = mysqli_real_escape_string($conn, $_POST['drug_name']);
    $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
    $drug_type_id = mysqli_real_escape_string($conn, $_POST['drug_type_id']);
    $drug_properties = mysqli_real_escape_string($conn, $_POST['drug_properties']);
    $drug_advice = mysqli_real_escape_string($conn, $_POST['drug_advice']);
    $drug_warning = mysqli_real_escape_string($conn, $_POST['drug_warning']);
    $drug_amount = mysqli_real_escape_string($conn, $_POST['drug_amount']);
    $drug_unit_id = mysqli_real_escape_string($conn, $_POST['drug_unit_id']);
    $drug_status = mysqli_real_escape_string($conn, $_POST['drug_status']);

    // สร้างคำสั่ง SQL UPDATE
    $sql = "UPDATE drug SET 
            drug_name = '$drug_name', 
            branch_id = '$branch_id', 
            drug_type_id = '$drug_type_id', 
            drug_properties = '$drug_properties', 
            drug_advice = '$drug_advice', 
            drug_warning = '$drug_warning', 
            drug_amount = '$drug_amount', 
            drug_unit_id = '$drug_unit_id', 
            drug_status = '$drug_status' 
            WHERE drug_id = '$drug_id'";
    
    // ดำเนินการอัพเดตข้อมูล
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg_ok'] = "อัพเดตข้อมูลยาเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการอัพเดตข้อมูล: " . mysqli_error($conn);
    }
} else {
    $_SESSION['msg_error'] = "ไม่พบข้อมูลที่ส่งมา";
}

// ปิดการเชื่อมต่อ
mysqli_close($conn);

// Redirect กลับไปยังหน้าแสดงรายการยา
header("Location: ../drug.php");
exit();
?>