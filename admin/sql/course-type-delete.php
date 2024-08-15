<?php
session_start();

include '../chk-session.php';
require '../../dbcon.php';

if (isset($_GET['id'])) {
    $course_type_id = $_GET['id'];

    // ทำความสะอาดข้อมูล (Sanitize) เพื่อป้องกัน SQL injection (สำคัญมาก)
    $course_type_id = mysqli_real_escape_string($conn, $course_type_id);

    // ลบข้อมูลประเภทคอร์ส
    $sql = "DELETE FROM course_type WHERE course_type_id = '$course_type_id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['msg_ok'] = "ลบข้อมูลประเภทคอร์สเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . mysqli_error($conn);
    }

    // Redirect กลับไปยังหน้า type-course.php
    header("Location: ../type-course.php");
    exit();
}
?>