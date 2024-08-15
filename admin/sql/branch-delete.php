<?php
session_start(); 

include '../chk-session.php';
require '../../dbcon.php'; 

if (isset($_GET['branch_id'])) {
    $branch_id = $_GET['branch_id'];

    $sql = "DELETE FROM branch WHERE branch_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $branch_id);

        if ($stmt->execute()) {
            $_SESSION['msg_ok'] = "ลบข้อมูลสาขาเรียบร้อยแล้ว"; 
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error;
    }
}

header("Location: ../branch.php"); 
exit(); 
?>
