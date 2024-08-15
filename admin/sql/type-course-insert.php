<?php
session_start();

include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $course_type_name = mysqli_real_escape_string($conn, $_POST['course_type_name']);

    // ตรวจสอบความถูกต้องของข้อมูล (ถ้าจำเป็น)
    // ... (เพิ่มโค้ดตรวจสอบข้อมูลในส่วนนี้)

    // สร้างคำสั่ง SQL INSERT
    $sql = "INSERT INTO course_type (course_type_name) 
            VALUES ('$course_type_name')";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['msg_ok'] = "เพิ่มข้อมูลประเภทคอร์สเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . mysqli_error($conn);
    }
}

// Redirect กลับไปยังหน้า type-course.php หรือหน้าอื่นๆ ตามต้องการ
header("Location: ../type-course.php"); 
exit();
?>