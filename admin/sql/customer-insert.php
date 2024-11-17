<?php
session_start();

// include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $cus_id_card_number = mysqli_real_escape_string($conn, $_POST['cus_id_card_number']);
    $cus_birthday = mysqli_real_escape_string($conn, $_POST['cus_birthday']);
    $cus_firstname = mysqli_real_escape_string($conn, $_POST['cus_firstname']);
    $cus_lastname = mysqli_real_escape_string($conn, $_POST['cus_lastname']);
    $cus_title = mysqli_real_escape_string($conn, $_POST['cus_title']);
    $cus_gender = mysqli_real_escape_string($conn, $_POST['cus_gender']);
    $cus_nickname = mysqli_real_escape_string($conn, $_POST['cus_nickname']);
    // $cus_line_id = mysqli_real_escape_string($conn, $_POST['cus_line_id']);
    $cus_email = mysqli_real_escape_string($conn, $_POST['cus_email']);
    $cus_blood = mysqli_real_escape_string($conn, $_POST['cus_blood']);
    $cus_tel = mysqli_real_escape_string($conn, $_POST['cus_tel']);
    $cus_drugallergy = mysqli_real_escape_string($conn, $_POST['cus_drugallergy']);
    $cus_congenital = mysqli_real_escape_string($conn, $_POST['cus_congenital']);

    $occupation = mysqli_real_escape_string($conn, $_POST['occupation']);
    $height = mysqli_real_escape_string($conn, $_POST['height']);
    $weight = mysqli_real_escape_string($conn, $_POST['weight']);
    $emergency_name = mysqli_real_escape_string($conn, $_POST['emergency_name']);
    $emergency_tel = mysqli_real_escape_string($conn, $_POST['emergency_tel']);
    $emergency_note = mysqli_real_escape_string($conn, $_POST['emergency_note']);

    // $cus_remark = mysqli_real_escape_string($conn, $_POST['cus_remark']);
    $cus_address = mysqli_real_escape_string($conn, $_POST['cus_address']);
    $cus_district = mysqli_real_escape_string($conn, $_POST['cus_district']);
    $cus_city = mysqli_real_escape_string($conn, $_POST['cus_city']);
    $cus_province = mysqli_real_escape_string($conn, $_POST['cus_province']);
    $cus_postal_code = mysqli_real_escape_string($conn, $_POST['cus_postal_code']);

    // แปลงวันที่จาก พ.ศ. เป็น ค.ศ. โดยใช้ DateTime::createFromFormat
    $thai_date = DateTime::createFromFormat('d/m/Y', $cus_birthday, new DateTimeZone('Asia/Bangkok')); // ระบุ timezone ให้ชัดเจน
    if ($thai_date !== false) {
        $thai_date->modify('-543 year'); // ลบ 543 ปี
        $cus_birthday = $thai_date->format('Y-m-d'); // จัดรูปแบบเป็น YYYY-MM-DD
    } else {
        // กรณีที่แปลงวันที่ไม่ได้ ให้แจ้งข้อผิดพลาดหรือตั้งค่าเริ่มต้น
        $_SESSION['msg_error'] = "รูปแบบวันเกิดไม่ถูกต้อง";
        header("Location: ../customer.php"); 
        exit();
    }

    // ตรวจสอบความถูกต้องของข้อมูล (ถ้าจำเป็น)
    // ... (เพิ่มโค้ดตรวจสอบข้อมูลในส่วนนี้)

   // อัปโหลดรูปภาพ (ถ้ามี)
    $cus_image = 'customer.png'; // กำหนดค่าเริ่มต้น
    if (isset($_FILES['cus_image']) && $_FILES['cus_image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../../img/customer/"; 
        $originalFileName = basename($_FILES["cus_image"]["name"]);
        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

        // สร้างชื่อไฟล์แบบสุ่ม
        $randomFileName = uniqid() . '.' . $fileExtension; // ใช้ uniqid() เพื่อสร้าง unique ID
        $targetFilePath = $targetDir . $randomFileName;

        // ตรวจสอบชนิดไฟล์ที่อนุญาต (ถ้าจำเป็น)
        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileExtension, $allowTypes)) {
            // ย้ายไฟล์ไปยังโฟลเดอร์ uploads
            if (move_uploaded_file($_FILES["cus_image"]["tmp_name"], $targetFilePath)) {
                $cus_image = $randomFileName; // ใช้ชื่อไฟล์แบบสุ่ม
            } else {
                $_SESSION['msg_error'] = "ขออภัย เกิดข้อผิดพลาดในการอัปโหลดรูปภาพของคุณ";
            }
        } else {
            $_SESSION['msg_error'] = 'ขออภัย อนุญาตให้อัปโหลดเฉพาะไฟล์ JPG, JPEG, PNG และ GIF เท่านั้น';
        }
    }


    // สร้างคำสั่ง SQL INSERT
    $sql = "INSERT INTO customer (
        cus_id_card_number, 
        cus_birthday, 
        cus_firstname, 
        cus_lastname, 
        cus_title, 
        cus_gender, 
        cus_nickname, 
        -- cus_line_id, 
        cus_email, 
        cus_blood, 
        cus_tel, 
        cus_drugallergy, 
        cus_congenital, 

        occupation,
        height,
        weight,
        emergency_name,
        emergency_tel,
        emergency_note,
        -- cus_remark, 
        cus_address, 
        cus_district, 
        cus_city, 
        cus_province, 
        cus_postal_code, 
        cus_image
    ) VALUES (
        '$cus_id_card_number', 
        '$cus_birthday', 
        '$cus_firstname', 
        '$cus_lastname', 
        '$cus_title', 
        '$cus_gender', 
        '$cus_nickname', 
        -- '$cus_line_id', 
        '$cus_email', 
        '$cus_blood', 
        '$cus_tel', 
        '$cus_drugallergy', 
        '$cus_congenital', 

        '$occupation',
        '$height',
        '$weight',
        '$emergency_name',
        '$emergency_tel',
        '$emergency_note',
        -- '$cus_remark', 
        '$cus_address', 
        '$cus_district', 
        '$cus_city', 
        '$cus_province', 
        '$cus_postal_code', 
        '$cus_image'
    )";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['msg_ok'] = "เพิ่มข้อมูลลูกค้าเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . mysqli_error($conn);
    }
}

header("Location: ../customer.php"); // หรือเปลี่ยนเป็นหน้าที่ต้องการ redirect ไปหลังจากบันทึกข้อมูล
exit();
?>
