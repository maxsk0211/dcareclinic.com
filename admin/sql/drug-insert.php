<?php
session_start();
include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์มและทำการ escape
    $drug_name = mysqli_real_escape_string($conn, $_POST['drug_name']);
    $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
    $drug_type_id = mysqli_real_escape_string($conn, $_POST['drug_type_id']);
    $drug_properties = mysqli_real_escape_string($conn, $_POST['drug_properties']);
    $drug_advice = mysqli_real_escape_string($conn, $_POST['drug_advice']);
    $drug_warning = mysqli_real_escape_string($conn, $_POST['drug_warning']);
    $drug_unit_id = mysqli_real_escape_string($conn, $_POST['drug_unit_id']);
    $drug_status = mysqli_real_escape_string($conn, $_POST['drug_status']);

    // สร้างคำสั่ง SQL INSERT
     $sql = "INSERT INTO drug (drug_name, branch_id, drug_type_id, drug_properties, drug_advice, drug_warning, drug_unit_id, drug_status) 
            VALUES ('$drug_name', '$branch_id', '$drug_type_id', '$drug_properties', '$drug_advice', '$drug_warning', '$drug_unit_id', '$drug_status')";
    
    // ดำเนินการเพิ่มข้อมูล
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg_ok'] = "เพิ่มข้อมูลยาเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . mysqli_error($conn);
    }
} else {
    $_SESSION['msg_error'] = "ไม่พบข้อมูลที่ส่งมา";
}

// ปิดการเชื่อมต่อ
mysqli_close($conn);

// Redirect กลับไปยังหน้าเดิม
header("Location: ../drug.php");
exit();
?>