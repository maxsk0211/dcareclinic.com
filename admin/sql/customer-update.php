<?php
session_start();

// include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $cus_id = mysqli_real_escape_string($conn, $_POST['cus_id']);
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

    // ตรวจสอบความถูกต้องของข้อมูล (ถ้าจำเป็น)
    // ... (เพิ่มโค้ดตรวจสอบข้อมูลในส่วนนี้)

    // อัปโหลดรูปภาพ (ถ้ามีการอัปโหลดรูปภาพใหม่)
    if (isset($_FILES['cus_image']) && $_FILES['cus_image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../../img/customer/"; 
        $originalFileName = basename($_FILES["cus_image"]["name"]);
        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

        // สร้างชื่อไฟล์แบบสุ่ม
        $randomFileName = uniqid() . '.' . $fileExtension; 
        $targetFilePath = $targetDir . $randomFileName;

        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileExtension, $allowTypes)) {
            if (move_uploaded_file($_FILES["cus_image"]["tmp_name"], $targetFilePath)) {
                $cus_image = $randomFileName; 
            } else {
                $_SESSION['msg_error'] = "ขออภัย เกิดข้อผิดพลาดในการอัปโหลดรูปภาพของคุณ";
                header("Location: ../customer.php");
                exit();
            }
        } else {
            $_SESSION['msg_error'] = 'ขออภัย อนุญาตให้อัปโหลดเฉพาะไฟล์ JPG, JPEG, PNG และ GIF เท่านั้น';
            header("Location: ../customer.php");
            exit();
        }
    } else {
        // ถ้าไม่มีการอัปโหลดรูปภาพใหม่ ให้ใช้รูปภาพเดิม
        $sql_get_image = "SELECT cus_image FROM customer WHERE cus_id = '$cus_id'";
        $result_get_image = mysqli_query($conn, $sql_get_image);
        $row_get_image = mysqli_fetch_object($result_get_image);
        $cus_image = $row_get_image->cus_image;
    }



    // แปลงวันที่จาก พ.ศ. เป็น ค.ศ. 
    $thai_date = DateTime::createFromFormat('d/m/Y', $cus_birthday, new DateTimeZone('Asia/Bangkok')); 
    if ($thai_date !== false) {
        $thai_date->modify('-543 year'); 
        $cus_birthday = $thai_date->format('Y-m-d'); 
    } else {
        $_SESSION['msg_error'] = "รูปแบบวันเกิดไม่ถูกต้อง";
        header("Location: ../customer.php"); 
        exit();
    }

    // สร้างคำสั่ง SQL UPDATE
    $sql = "UPDATE customer SET 
        cus_id_card_number = '$cus_id_card_number', 
        cus_birthday = '$cus_birthday', 
        cus_firstname = '$cus_firstname', 
        cus_lastname = '$cus_lastname', 
        cus_title = '$cus_title', 
        cus_gender = '$cus_gender', 
        cus_nickname = '$cus_nickname', 
        
        cus_email = '$cus_email', 
        cus_blood = '$cus_blood', 
        cus_tel = '$cus_tel', 
        cus_drugallergy = '$cus_drugallergy', 
        cus_congenital = '$cus_congenital', 
        occupation='$occupation',
        height='$height',
        weight='$weight',
        emergency_name='$emergency_name',
        emergency_tel='$emergency_tel',
        emergency_note='$emergency_note',
        
        cus_address = '$cus_address', 
        cus_district = '$cus_district', 
        cus_city = '$cus_city', 
        cus_province = '$cus_province', 
        cus_postal_code = '$cus_postal_code'
        
    WHERE cus_id = '$cus_id'";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['msg_ok'] = "แก้ไขข้อมูลลูกค้าเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล: " . mysqli_error($conn);
    }
}

header("Location: ../customer.php"); 
exit();
?>