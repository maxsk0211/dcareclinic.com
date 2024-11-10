<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $position_id = mysqli_real_escape_string($conn, $_POST['position_id']);
    $position_name = mysqli_real_escape_string($conn, $_POST['position_name']);
    
    $sql = "UPDATE position SET position_name = '$position_name' WHERE position_id = '$position_id'";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['msg_ok'] = "แก้ไขตำแหน่งสำเร็จ";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

header("Location: ../users.php");
exit();
?>