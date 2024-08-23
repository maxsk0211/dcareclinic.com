<?php
session_start();
include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์มและทำความสะอาด
    // $transaction_date = mysqli_real_escape_string($conn, $_POST['transaction_date']);
    $users_id = mysqli_real_escape_string($conn, $_POST['users_id']);
    $quantity = floatval($_POST['quantity']);
    $cost_per_unit = floatval($_POST['cost_per_unit']);
    $stock_type = mysqli_real_escape_string($conn, $_POST['stock_type']);
    $related_id = intval($_POST['related_id']);
    $branch_id = intval($_POST['branch_id']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    // จัดการวันหมดอายุ
    if (!empty($_POST['transaction_date'])) {
        $transaction_date = DateTime::createFromFormat('d-m-Y H:i', $_POST['transaction_date']); 
        if ($transaction_date) {
            $transaction_date->modify('-543 years'); // แปลงจาก พ.ศ. เป็น ค.ศ.
            $transaction_date_sql = $transaction_date->format('Y-m-d H:i:s'); 
        } else {
            $_SESSION['msg_error'] = "รูปแบบวันที่ทำรายการไม่ถูกต้อง"; 
            header("Location: ../drug-detail.php?drug_id=" . $related_id);
            exit();
        }
    } else {
        $transaction_date_sql = "NULL"; 
    }

    // จัดการวันหมดอายุ
    if (!empty($_POST['expiry_date'])) {
        $expiry_date = DateTime::createFromFormat('d/m/Y', $_POST['expiry_date']);
        if ($expiry_date) {
            $expiry_date->modify('-543 years'); // แปลงจาก พ.ศ. เป็น ค.ศ.
            $expiry_date_sql = $expiry_date->format('Y-m-d');
        } else {
            $_SESSION['msg_error'] = "รูปแบบวันที่หมดอายุไม่ถูกต้อง";
            header("Location: ../drug-detail.php?drug_id=" . $related_id);
            exit();
        }
    } else {
        $expiry_date_sql = "NULL"; // กำหนดเป็น NULL หากไม่มีการระบุวันหมดอายุ
    }

    // สร้างคำสั่ง SQL โดยใช้ mysqli_real_escape_string
    echo $sql = "INSERT INTO stock_transactions (transaction_date, users_id, quantity, expiry_date, cost_per_unit, stock_type, related_id,  branch_id, notes) 
            VALUES ('$transaction_date_sql', '$users_id', $quantity, '$expiry_date_sql', $cost_per_unit, '$stock_type', $related_id,  $branch_id, '$notes')";

    // ดำเนินการคำสั่ง SQL
    if (mysqli_query($conn, $sql)) {
        // อัปเดตจำนวนคงเหลือในตาราง drug (ใช้ mysqli_query)
        $update_sql = "UPDATE drug SET drug_amount = drug_amount + $quantity WHERE drug_id = $related_id";
        if (mysqli_query($conn, $update_sql)) {
            $_SESSION['msg_ok'] = "เพิ่มสต็อกสำเร็จ";
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการอัพเดตจำนวนคงเหลือ: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเพิ่มสต็อก: " . mysqli_error($conn);
    }
} else {
    $_SESSION['msg_error'] = "ไม่พบข้อมูลที่ส่งมา";
}

// ปิดการเชื่อมต่อ
mysqli_close($conn);

// ส่งกลับไปยังหน้าแสดงรายละเอียดยา
// header("Location: ../drug-detail.php?drug_id=" . $related_id);
exit();
?>