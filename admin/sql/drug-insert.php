<?php
session_start();
// include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์มและทำการ escape
    $drug_name = mysqli_real_escape_string($conn, $_POST['drug_name']);
    $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
    $drug_type_id = mysqli_real_escape_string($conn, $_POST['drug_type_id']);
    $drug_properties = mysqli_real_escape_string($conn, $_POST['drug_properties']);
    $drug_advice = mysqli_real_escape_string($conn, $_POST['drug_advice']);
    $drug_warning = mysqli_real_escape_string($conn, $_POST['drug_warning']);
    $drug_unit_id = mysqli_real_escape_string($conn, $_POST['drug_unit_id']);
    $drug_status = mysqli_real_escape_string($conn, $_POST['drug_status']);


    // อัปโหลดรูปภาพ (ถ้ามี)
    $drug_pic = ''; // กำหนดค่าเริ่มต้น
    if (isset($_FILES['drug_pic']) && $_FILES['drug_pic']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../../img/drug/"; // ตรวจสอบให้แน่ใจว่ามีโฟลเดอร์ uploads อยู่
        $originalFileName = basename($_FILES["drug_pic"]["name"]);
        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

        // สร้างชื่อไฟล์แบบสุ่ม
        $randomFileName = uniqid() . '.' . $fileExtension; // ใช้ uniqid() เพื่อสร้าง unique ID
        $targetFilePath = $targetDir . $randomFileName;

        // ตรวจสอบชนิดไฟล์ที่อนุญาต (ถ้าจำเป็น)
        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileExtension, $allowTypes)) {
            // ย้ายไฟล์ไปยังโฟลเดอร์ uploads
            if (move_uploaded_file($_FILES["drug_pic"]["tmp_name"], $targetFilePath)) {
                $drug_pic = $randomFileName; // ใช้ชื่อไฟล์แบบสุ่ม
            } else {
                $_SESSION['msg_error'] = "ขออภัย เกิดข้อผิดพลาดในการอัปโหลดรูปภาพของคุณ";
            }
        } else {
            $_SESSION['msg_error'] = 'ขออภัย อนุญาตให้อัปโหลดเฉพาะไฟล์ JPG, JPEG, PNG และ GIF เท่านั้น';
        }
    }


    // สร้างคำสั่ง SQL INSERT
     $sql = "INSERT INTO drug (drug_name, branch_id, drug_type_id, drug_properties, drug_advice, drug_warning, drug_unit_id,drug_pic, drug_status) 
            VALUES ('$drug_name', '$branch_id', '$drug_type_id', '$drug_properties', '$drug_advice', '$drug_warning', '$drug_unit_id','$drug_pic', '$drug_status')";
    
    // ดำเนินการเพิ่มข้อมูล
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg_ok'] = "เพิ่มข้อมูลยาเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . mysqli_error($conn);
    }
} else {
    $_SESSION['msg_error'] = "ไม่พบข้อมูลที่ส่งมา";
}

// ปิดการเชื่อมต่อ
mysqli_close($conn);

// Redirect กลับไปยังหน้าเดิม
header("Location: ../drug.php");
exit();
?>