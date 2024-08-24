<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ตรวจสอบว่ามี course_id ถูกส่งมาหรือไม่
    if (!isset($_POST['course_id']) || empty($_POST['course_id'])) {
        $_SESSION['msg_error'] = "ไม่พบข้อมูล course_id";
        header("Location: ../course.php");  // หรือหน้าที่เหมาะสม
        exit();
    }

    $course_id = $_POST['course_id'];
    $resource_type = $_POST['resource_type'];
    $resource_id = $_POST['resource_id'];
    $quantity = $_POST['quantity'];

    $sql = "INSERT INTO course_resources (course_id, resource_type, resource_id, quantity) 
            VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isid", $course_id, $resource_type, $resource_id, $quantity);
    
    if ($stmt->execute()) {
        $_SESSION['msg_ok'] = "เพิ่มทรัพยากรสำเร็จ";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเพิ่มทรัพยากร: " . $conn->error;
    }
    
    $stmt->close();

    // ส่งกลับไปยังหน้ารายละเอียดคอร์ส
    header("Location: ../course-detail.php?id=" . $course_id);
    exit();
} else {
    $_SESSION['msg_error'] = "ไม่ได้รับข้อมูลจากฟอร์ม";
    header("Location: ../course.php");  // หรือหน้าที่เหมาะสม
    exit();
}
?>