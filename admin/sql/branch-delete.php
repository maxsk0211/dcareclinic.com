<?php
session_start();
require '../../dbcon.php';

if (isset($_GET['branch_id'])) {
    $branch_id = $_GET['branch_id'];

    // ลบรูปภาพเก่า (ถ้ามี)
    $sql = "SELECT branch_logo FROM branch WHERE branch_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $branch_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($row['branch_logo'] && file_exists('../../uploads/logos/' . $row['branch_logo'])) {
                unlink('../../uploads/logos/' . $row['branch_logo']);
            }
        }
        $stmt->close();
    }

    // ลบข้อมูลจากฐานข้อมูล
    $sql = "DELETE FROM branch WHERE branch_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $branch_id);
        
        if ($stmt->execute()) {
            $_SESSION['msg_ok'] = "ลบข้อมูลสาขาสำเร็จ";
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาด: " . $conn->error;
        }

        $stmt->close();
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL";
    }

    $conn->close();
    header("Location: ../branch.php");
    exit();
}
?>