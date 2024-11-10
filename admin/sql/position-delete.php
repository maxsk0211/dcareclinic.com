<?php
session_start();
require '../../dbcon.php';

if (isset($_GET['id'])) {
    $position_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // ตรวจสอบว่ามีผู้ใช้งานในตำแหน่งนี้หรือไม่
    $check_sql = "SELECT COUNT(*) as count FROM users WHERE position_id = '$position_id'";
    $result = $conn->query($check_sql);
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        $_SESSION['msg_error'] = "ไม่สามารถลบตำแหน่งได้เนื่องจากมีผู้ใช้งานในตำแหน่งนี้";
    } else {
        $sql = "DELETE FROM position WHERE position_id = '$position_id'";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['msg_ok'] = "ลบตำแหน่งสำเร็จ";
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาด: " . $conn->error;
        }
    }
}

header("Location: ../users.php");
exit();
?>