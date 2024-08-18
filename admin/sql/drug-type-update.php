<?php
session_start();

include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $drug_type_id = $_POST['drug_type_id'];
    $drug_type_name = $_POST['drug_type_name'];

    // ทำความสะอาดข้อมูล (Sanitize) เพื่อป้องกัน SQL injection (สำคัญมาก)
    $drug_type_id = mysqli_real_escape_string($conn, $drug_type_id);
    $drug_type_name = mysqli_real_escape_string($conn, $drug_type_name);

    // สร้างคำสั่ง SQL UPDATE
    $sql = "UPDATE drug_type 
            SET drug_type_name = '$drug_type_name'
            WHERE drug_type_id = '$drug_type_id'";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['msg_ok'] = "แก้ไขข้อมูลประเภทยาเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล: " . mysqli_error($conn);
    }
}

// Redirect กลับไปยังหน้า drug-type.php หรือหน้าอื่นๆ ตามต้องการ
header("Location: ../drug-type.php");
exit();
?>