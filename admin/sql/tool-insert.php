<?php
session_start();
// include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์มและทำการ escape
    $tool_name = mysqli_real_escape_string($conn, $_POST['tool_name']);
    $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
    $tool_detail = mysqli_real_escape_string($conn, $_POST['tool_detail']);
    $tool_amount = mysqli_real_escape_string($conn, $_POST['tool_amount']);
    $tool_unit_id = mysqli_real_escape_string($conn, $_POST['tool_unit_id']);
    $tool_status = mysqli_real_escape_string($conn, $_POST['tool_status']);

    // ตรวจสอบว่าชื่อเครื่องมือซ้ำหรือไม่
    $check_sql = "SELECT * FROM tool WHERE tool_name = '$tool_name' AND branch_id = '$branch_id'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        // ถ้ามีชื่อซ้ำ
        $_SESSION['msg_error'] = "มีเครื่องมือชื่อนี้ในสาขานี้อยู่แล้ว กรุณาตรวจสอบอีกครั้ง";
    } else {
        // ถ้าไม่ซ้ำ ทำการเพิ่มข้อมูล
        $sql = "INSERT INTO tool (tool_name, branch_id, tool_detail, tool_amount, tool_unit_id, tool_status) 
                VALUES ('$tool_name', '$branch_id', '$tool_detail', '$tool_amount', '$tool_unit_id', '$tool_status')";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION['msg_ok'] = "เพิ่มเครื่องมือแพทย์ใหม่เรียบร้อยแล้ว";
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . mysqli_error($conn);
        }
    }
} else {
    $_SESSION['msg_error'] = "ไม่พบข้อมูลที่ส่งมา";
}

// ปิดการเชื่อมต่อ
mysqli_close($conn);

// Redirect กลับไปยังหน้าแสดงรายการเครื่องมือแพทย์
header("Location: ../tool.php");
exit();
?>