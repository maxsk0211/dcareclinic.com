<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $position_name = mysqli_real_escape_string($conn, $_POST['position_name']);
    
    $sql = "INSERT INTO position (position_name) VALUES ('$position_name')";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['msg_ok'] = "เพิ่มตำแหน่งสำเร็จ";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาด: " . $conn->error;
    }
}

header("Location: ../users.php");
exit();
?>