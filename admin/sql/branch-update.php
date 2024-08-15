<?php
session_start(); 

include '../chk-session.php';
require '../../dbcon.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $branch_id = $_POST['branch_id'];
    $branch_name = $_POST['branch_name'];

    // ตรวจสอบความถูกต้องของข้อมูล (ถ้าจำเป็น)
    // ...

    $sql = "UPDATE branch SET branch_name = ? WHERE branch_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("si", $branch_name, $branch_id);

        if ($stmt->execute()) {
            $_SESSION['msg_ok'] = "แก้ไขข้อมูลสาขาเรียบร้อยแล้ว"; 
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error;
    }
}

header("Location: ../branch.php"); 
exit(); 
?>
