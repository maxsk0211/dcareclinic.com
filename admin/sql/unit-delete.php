<?php
session_start();
// include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $unit_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // ตรวจสอบว่าหน่วยนับนี้ไม่ได้ถูกใช้งานในตารางอื่น
    $check_sql = "SELECT COUNT(*) as count FROM drug WHERE drug_unit_id = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "i", $unit_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $check_row = mysqli_fetch_object($check_result);
    
    if ($check_row->count > 0) {
        $_SESSION['msg_error'] = "ไม่สามารถลบหน่วยนับนี้ได้เนื่องจากมีการใช้งานในระบบ";
    } else {
        // เตรียมคำสั่ง SQL DELETE
        $sql = "DELETE FROM unit WHERE unit_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            // ผูกค่าพารามิเตอร์กับคำสั่ง SQL
            mysqli_stmt_bind_param($stmt, "i", $unit_id);
            
            // ดำเนินการลบข้อมูล
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['msg_ok'] = "ลบข้อมูลหน่วยนับเรียบร้อยแล้ว";
            } else {
                $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . mysqli_stmt_error($stmt);
            }
            
            // ปิด statement
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . mysqli_error($conn);
        }
    }
    
    mysqli_stmt_close($check_stmt);
} else {
    $_SESSION['msg_error'] = "ไม่พบข้อมูล unit_id ที่ต้องการลบ";
}

// ปิดการเชื่อมต่อ
mysqli_close($conn);

// Redirect กลับไปยังหน้าเดิม
header("Location: ../drug.php");
exit();
?>