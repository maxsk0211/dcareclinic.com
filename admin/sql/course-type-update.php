<?php
session_start();

include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_type_id = $_POST['course_type_id'];
    $course_type_name = $_POST['course_type_name'];
    $course_type_status = $_POST['course_type_status'];

    // ทำความสะอาดข้อมูล (Sanitize) เพื่อป้องกัน SQL injection (สำคัญมาก)
    $course_type_id = mysqli_real_escape_string($conn, $course_type_id);
    $course_type_name = mysqli_real_escape_string($conn, $course_type_name);
    $course_type_status = mysqli_real_escape_string($conn, $course_type_status);

    // สร้างคำสั่ง SQL UPDATE
    $sql = "UPDATE course_type 
            SET course_type_name = '$course_type_name', 
                course_type_status = '$course_type_status'
            WHERE course_type_id = '$course_type_id'";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['msg_ok'] = "แก้ไขข้อมูลประเภทคอร์สเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล: " . mysqli_error($conn);
    }
}

// Redirect กลับไปยังหน้า type-course.php หรือหน้าอื่นๆ ตามต้องการ
header("Location: ../type-course.php");
exit();
?>