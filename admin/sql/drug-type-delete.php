<?php
session_start();

// include '../chk-session.php';
require '../../dbcon.php';

if (isset($_GET['id'])) {
    $drug_type_id = $_GET['id'];

    // ทำความสะอาดข้อมูล (Sanitize) เพื่อป้องกัน SQL injection (สำคัญมาก)
    $drug_type_id = mysqli_real_escape_string($conn, $drug_type_id);

    // ลบข้อมูลประเภทยา
    $sql = "DELETE FROM drug_type WHERE drug_type_id = '$drug_type_id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['msg_ok'] = "ลบข้อมูลประเภทยาเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . mysqli_error($conn);
    }
}

// Redirect กลับไปยังหน้า drug-type.php หรือหน้าอื่นๆ ตามต้องการ
header("Location: ../drug-type.php");
exit();
?>