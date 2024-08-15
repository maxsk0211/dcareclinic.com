<?php
session_start(); 

include '../chk-session.php';
require '../../dbcon.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $branch_name = $_POST['branch_name'];

    // ตรวจสอบความถูกต้องของข้อมูล (ถ้าจำเป็น)
    // ...

    // เตรียมคำสั่ง SQL INSERT
    $sql = "INSERT INTO branch (branch_name) VALUES (?)"; 
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // ผูกค่าพารามิเตอร์กับคำสั่ง SQL
        $stmt->bind_param("s", $branch_name);

        // ดำเนินการบันทึกข้อมูล
        if ($stmt->execute()) {
            $_SESSION['msg_ok'] = "เพิ่มข้อมูลสาขาเรียบร้อยแล้ว"; 
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
        }

        // ปิด statement
        $stmt->close();
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error;
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล (ถ้าจำเป็น)
// ...

// Redirect กลับไปยังหน้าเดิม หรือหน้าอื่นๆ ตามต้องการ
header("Location: ../branch.php"); // หรือเปลี่ยนเป็นหน้าอื่นๆ ตามต้องการ
exit(); 
?>
