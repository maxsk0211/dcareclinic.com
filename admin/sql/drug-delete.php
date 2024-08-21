<?php
session_start();
include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $drug_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // ตรวจสอบว่ายานี้ไม่ได้ถูกใช้งานในตารางอื่น (ถ้าจำเป็น)
    // $check_sql = "SELECT COUNT(*) as count FROM [ชื่อตารางที่เกี่ยวข้อง] WHERE drug_id = '$drug_id'";
    // $check_result = mysqli_query($conn, $check_sql);
    // $check_row = mysqli_fetch_object($check_result);
    $check_row =0;
    
    if ($check_row->count > 0) {
        $_SESSION['msg_error'] = "ไม่สามารถลบยานี้ได้เนื่องจากมีการใช้งานในระบบ";
    } else {
        // สร้างคำสั่ง SQL DELETE
        $sql = "DELETE FROM drug WHERE drug_id = '$drug_id'";
        
        // ดำเนินการลบข้อมูล
        if (mysqli_query($conn, $sql)) {
            $_SESSION['msg_ok'] = "ลบข้อมูลยาเรียบร้อยแล้ว";
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . mysqli_error($conn);
        }
    }
} else {
    $_SESSION['msg_error'] = "ไม่พบข้อมูล drug_id ที่ต้องการลบ";
}

// ปิดการเชื่อมต่อ
mysqli_close($conn);

// Redirect กลับไปยังหน้าแสดงรายการยา
header("Location: ../drug.php");
exit();
?>