<?php
session_start();

include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
    $course_name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $course_detail = mysqli_real_escape_string($conn, $_POST['course_detail']);
    $course_price = mysqli_real_escape_string($conn, $_POST['course_price']);
    $course_amount = mysqli_real_escape_string($conn, $_POST['course_amount']);
    $course_type_id = mysqli_real_escape_string($conn, $_POST['course_type_id']);
    $course_start = mysqli_real_escape_string($conn, $_POST['course_start']);
    $course_end = mysqli_real_escape_string($conn, $_POST['course_end']);
    $course_note = mysqli_real_escape_string($conn, $_POST['course_note']);
    $course_status = mysqli_real_escape_string($conn, $_POST['course_status']);

    // ตรวจสอบความถูกต้องของข้อมูล (ถ้าจำเป็น)
    // ... (เพิ่มโค้ดตรวจสอบข้อมูลในส่วนนี้)

    // อัปโหลดรูปภาพ (ถ้ามี)
    $course_pic = ''; // กำหนดค่าเริ่มต้น
    if (isset($_FILES['course_pic']) && $_FILES['course_pic']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../../img/course/"; // ตรวจสอบให้แน่ใจว่ามีโฟลเดอร์ uploads อยู่
        $originalFileName = basename($_FILES["course_pic"]["name"]);
        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

        // สร้างชื่อไฟล์แบบสุ่ม
        $randomFileName = uniqid() . '.' . $fileExtension; // ใช้ uniqid() เพื่อสร้าง unique ID
        $targetFilePath = $targetDir . $randomFileName;

        // ตรวจสอบชนิดไฟล์ที่อนุญาต (ถ้าจำเป็น)
        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileExtension, $allowTypes)) {
            // ย้ายไฟล์ไปยังโฟลเดอร์ uploads
            if (move_uploaded_file($_FILES["course_pic"]["tmp_name"], $targetFilePath)) {
                $course_pic = $randomFileName; // ใช้ชื่อไฟล์แบบสุ่ม
            } else {
                $_SESSION['msg_error'] = "ขออภัย เกิดข้อผิดพลาดในการอัปโหลดรูปภาพของคุณ";
            }
        } else {
            $_SESSION['msg_error'] = 'ขออภัย อนุญาตให้อัปโหลดเฉพาะไฟล์ JPG, JPEG, PNG และ GIF เท่านั้น';
        }
    }

    // แปลงวันที่จาก พ.ศ. เป็น ค.ศ. 
    $thai_start_date = DateTime::createFromFormat('d/m/Y', $course_start, new DateTimeZone('Asia/Bangkok')); 
    if ($thai_start_date !== false) {
        $thai_start_date->modify('-543 year'); 
        $course_start = $thai_start_date->format('Y-m-d'); 
    } else {
        $_SESSION['msg_error'] = "รูปแบบวันที่เริ่มคอร์สไม่ถูกต้อง";
        header("Location: ../course.php"); 
        exit();
    }

    $thai_end_date = DateTime::createFromFormat('d/m/Y', $course_end, new DateTimeZone('Asia/Bangkok')); 
    if ($thai_end_date !== false) {
        $thai_end_date->modify('-543 year'); 
        $course_end = $thai_end_date->format('Y-m-d'); 
    } else {
        $_SESSION['msg_error'] = "รูปแบบวันที่สิ้นสุดคอร์สไม่ถูกต้อง";
        header("Location: ../course.php"); 
        exit();
    }

    // สร้างคำสั่ง SQL INSERT
    $sql = "INSERT INTO course (
        branch_id,
        course_name,
        course_detail,
        course_price,
        course_amount,
        course_type_id,
        course_start,
        course_end,
        course_pic,
        course_note,
        course_status
    ) VALUES (
        '$branch_id',
        '$course_name',
        '$course_detail',
        '$course_price',
        '$course_amount',
        '$course_type_id',
        '$course_start',
        '$course_end',
        '$course_pic',
        '$course_note',
        '$course_status'
    )";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['msg_ok'] = "เพิ่มข้อมูลคอร์สเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . mysqli_error($conn);
    }
}

header("Location: ../course.php");
exit();
?>