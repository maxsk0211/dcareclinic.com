<?php
session_start();

include '../chk-session.php';
require '../../dbcon.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // ทำความสะอาดข้อมูล (Sanitize) เพื่อป้องกัน SQL injection (สำคัญมาก)
    $user_id = mysqli_real_escape_string($conn, $user_id);

    $sql = "DELETE FROM users WHERE users_id = '$user_id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['msg_ok'] = "ลบข้อมูลพนักงานเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . mysqli_error($conn);
    }
}

// Redirect กลับไปยังหน้า users.php หรือหน้าอื่นๆ ตามต้องการ
header("Location: ../users.php"); 
exit();
?>
