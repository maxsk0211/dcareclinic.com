<?php
session_start();

include '../chk-session.php';
require '../../dbcon.php';

if (isset($_GET['id'])) {
    $course_id = $_GET['id'];

    // ทำความสะอาดข้อมูล (Sanitize) เพื่อป้องกัน SQL injection (สำคัญมาก)
    $course_id = mysqli_real_escape_string($conn, $course_id);

    // ลบรูปภาพ (ถ้ามี)
    $sql_get_image = "SELECT course_pic FROM course WHERE course_id = '$course_id'";
    $result_get_image = mysqli_query($conn, $sql_get_image);
    if ($result_get_image && $row_get_image = mysqli_fetch_object($result_get_image)) {
        $image_path = "../../img/course/" . $row_get_image->course_pic;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // ลบข้อมูลคอร์ส
    $sql = "DELETE FROM course WHERE course_id = '$course_id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['msg_ok'] = "ลบข้อมูลคอร์สเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . mysqli_error($conn);
    }
}

// Redirect กลับไปยังหน้า course.php หรือหน้าอื่นๆ ตามต้องการ
header("Location: ../course.php");
exit();
?>