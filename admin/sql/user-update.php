<?php
session_start();

// include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $users_id = $_POST['users_id'];
    $users_username = $_POST['users_username'];
    $users_fname = $_POST['users_fname'];
    $users_lname = $_POST['users_lname'];
    $users_nickname = $_POST['users_nickname'];
    $users_tel = $_POST['users_tel'];
    $position_id = $_POST['position_id'];
    $users_license = $_POST['users_license'];
    $branch_id = $_POST['branch_id'];
    $users_status = $_POST['users_status'];

    // ตรวจสอบความถูกต้องของข้อมูล (ถ้าจำเป็น)
    // ... (เพิ่มโค้ดตรวจสอบข้อมูลในส่วนนี้)

    // ทำความสะอาดข้อมูล (Sanitize) เพื่อป้องกัน SQL injection (สำคัญมาก)
    $users_username = mysqli_real_escape_string($conn, $users_username);
    $users_fname = mysqli_real_escape_string($conn, $users_fname);
    $users_lname = mysqli_real_escape_string($conn, $users_lname);
    $users_nickname = mysqli_real_escape_string($conn, $users_nickname);
    $users_tel = mysqli_real_escape_string($conn, $users_tel);
    $users_license = mysqli_real_escape_string($conn, $users_license);

    // สร้างคำสั่ง SQL โดยใช้ mysqli_real_escape_string เพื่อป้องกัน SQL injection
    $sql = "UPDATE users 
            SET users_username = '$users_username', 
                users_fname = '$users_fname',
                users_lname = '$users_lname',
                users_nickname = '$users_nickname',
                users_tel = '$users_tel',
                position_id = '$position_id',
                users_license = '$users_license',
                branch_id = '$branch_id',
                users_status = '$users_status'
            WHERE users_id = '$users_id'";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['msg_ok'] = "แก้ไขข้อมูลพนักงานเรียบร้อยแล้ว"; 
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล: " . mysqli_error($conn);
    }
}

header("Location: ../users.php"); 
exit(); 
?>
