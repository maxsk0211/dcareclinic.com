<?php
session_start();
include '../chk-session.php';
require '../../dbcon.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    // รับค่า id และทำการ escape
    $tool_id = mysqli_real_escape_string($conn, $_GET['id']);

    // ตรวจสอบว่าเครื่องมือนี้ถูกใช้งานอยู่หรือไม่ (ถ้ามีตารางอื่นที่อ้างอิงถึง)
    // ตัวอย่าง: $check_sql = "SELECT * FROM some_table WHERE tool_id = '$tool_id'";
    // $check_result = mysqli_query($conn, $check_sql);
    // if (mysqli_num_rows($check_result) > 0) {
    //     $_SESSION['msg_error'] = "ไม่สามารถลบเครื่องมือนี้ได้ เนื่องจากมีการใช้งานในระบบ";
    //     header("Location: ../tool.php");
    //     exit();
    // }

    // ทำการลบข้อมูล
    $sql = "DELETE FROM tool WHERE tool_id = '$tool_id'";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg_ok'] = "ลบข้อมูลเครื่องมือแพทย์เรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . mysqli_error($conn);
    }
} else {
    $_SESSION['msg_error'] = "ไม่พบข้อมูล ID ที่ต้องการลบ";
}

// ปิดการเชื่อมต่อ
mysqli_close($conn);

// Redirect กลับไปยังหน้าแสดงรายการเครื่องมือแพทย์
header("Location: ../tool.php");
exit();
?>