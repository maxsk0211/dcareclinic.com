<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id']) && isset($_GET['course_id'])) {
    $resource_id = $_GET['id'];
    $course_id = $_GET['course_id'];

    // ตรวจสอบว่า resource_id และ course_id เป็นตัวเลข
    if (!is_numeric($resource_id) || !is_numeric($course_id)) {
        $_SESSION['msg_error'] = "ข้อมูลไม่ถูกต้อง";
        header("Location: course-detail.php?id=" . $course_id);
        exit();
    }

    // เตรียมคำสั่ง SQL สำหรับลบข้อมูล
    $sql = "DELETE FROM course_resources WHERE id = ? AND course_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $resource_id, $course_id);
    
    if ($stmt->execute()) {
        $_SESSION['msg_ok'] = "ลบทรัพยากรสำเร็จ";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการลบทรัพยากร: " . $conn->error;
    }
    
    $stmt->close();

    // ส่งกลับไปยังหน้ารายละเอียดคอร์ส
    header("Location: ../course-detail.php?id=" . $course_id);
    exit();
} else {
    $_SESSION['msg_error'] = "ไม่ได้รับข้อมูลที่จำเป็น";
    header("Location: ../course.php");
    exit();
}
?>