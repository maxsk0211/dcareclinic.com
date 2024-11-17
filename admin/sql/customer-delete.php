<?php
session_start();

// include '../chk-session.php';
require '../../dbcon.php';

if (isset($_GET['id'])) {
    $cus_id = $_GET['id'];

    // ทำความสะอาดข้อมูล (Sanitize) เพื่อป้องกัน SQL injection (สำคัญมาก)
    $cus_id = mysqli_real_escape_string($conn, $cus_id);

    // ลบข้อมูลลูกค้า (ไม่ลบรูปภาพ)
    $sql = "DELETE FROM customer WHERE cus_id = '$cus_id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['msg_ok'] = "ลบข้อมูลลูกค้าเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . mysqli_error($conn);
    }
}

// Redirect กลับไปยังหน้า customer.php หรือหน้าอื่นๆ ตามต้องการ
header("Location: ../customer.php"); 
exit();
?>