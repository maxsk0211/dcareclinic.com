<?php
session_start();
include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $unit_name = $_POST['unit_name'];
    
    // ตรวจสอบความถูกต้องของข้อมูล
    if (empty($unit_name)) {
        $_SESSION['msg_error'] = "กรุณากรอกชื่อหน่วยนับ";
        header("Location: ../drug.php");
        exit();
    }
    
    // เตรียมคำสั่ง SQL INSERT
    $sql = "INSERT INTO unit (unit_name) VALUES (?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        // ผูกค่าพารามิเตอร์กับคำสั่ง SQL
        mysqli_stmt_bind_param($stmt, "s", $unit_name);
        
        // ดำเนินการบันทึกข้อมูล
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['msg_ok'] = "เพิ่มข้อมูลหน่วยนับเรียบร้อยแล้ว";
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . mysqli_stmt_error($stmt);
        }
        
        // ปิด statement
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . mysqli_error($conn);
    }
}

// Redirect กลับไปยังหน้าเดิม
header("Location: ../drug.php");
exit();
?>