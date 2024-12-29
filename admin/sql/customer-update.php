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



    // จัดการรูปภาพ
    $current_image_query = "SELECT cus_image FROM customer WHERE cus_id = ?";
    $stmt = $conn->prepare($current_image_query);
    $stmt->bind_param("i", $cus_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_image = $result->fetch_assoc()['cus_image'];
    $cus_image = $current_image; // ใช้รูปเดิมเป็นค่าเริ่มต้น

    if (isset($_FILES['cus_image']) && $_FILES['cus_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "../../img/customer/";
        
        // ตรวจสอบและสร้างโฟลเดอร์
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        // ตรวจสอบประเภทไฟล์
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['cus_image']['type'];
        if (!in_array($file_type, $allowed_types)) {
            $_SESSION['msg_error'] = "อนุญาตเฉพาะไฟล์รูปภาพประเภท JPEG, PNG และ GIF เท่านั้น";
            header("Location: ../customer.php");
            exit();
        }

        // ตรวจสอบขนาดไฟล์
        if ($_FILES['cus_image']['size'] > 5 * 1024 * 1024) {
            $_SESSION['msg_error'] = "ไฟล์มีขนาดใหญ่เกินไป (จำกัดที่ 5MB)";
            header("Location: ../customer.php");
            exit();
        }

        // สร้างชื่อไฟล์ใหม่
        $file_extension = pathinfo($_FILES['cus_image']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;

        // อัปโหลดไฟล์
        if (move_uploaded_file($_FILES['cus_image']['tmp_name'], $upload_path)) {
            // ลบรูปเก่าถ้าไม่ใช่รูป default
            if ($current_image != 'customer.png' && file_exists($upload_dir . $current_image)) {
                unlink($upload_dir . $current_image);
            }
            $cus_image = $new_filename;
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการอัปโหลดไฟล์";
            header("Location: ../customer.php");
            exit();
        }
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

      // SQL สำหรับอัปเดตข้อมูล
    $sql = "UPDATE customer SET 
        cus_id_card_number = ?,
        cus_birthday = ?,
        cus_firstname = ?,
        cus_lastname = ?,
        cus_title = ?,
        cus_gender = ?,
        cus_nickname = ?,
        cus_email = ?,
        cus_blood = ?,
        cus_tel = ?,
        cus_drugallergy = ?,
        cus_congenital = ?,
        occupation = ?,
        height = ?,
        weight = ?,
        emergency_name = ?,
        emergency_tel = ?,
        emergency_note = ?,
        cus_address = ?,
        cus_district = ?,
        cus_city = ?,
        cus_province = ?,
        cus_postal_code = ?,
        cus_image = ?
    WHERE cus_id = ?";

    // ใช้ Prepared Statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssddssssssssssi",
        $cus_id_card_number,
        $cus_birthday,
        $cus_firstname,
        $cus_lastname,
        $cus_title,
        $cus_gender,
        $cus_nickname,
        $cus_email,
        $cus_blood,
        $cus_tel,
        $cus_drugallergy,
        $cus_congenital,
        $occupation,
        $height,
        $weight,
        $emergency_name,
        $emergency_tel,
        $emergency_note,
        $cus_address,
        $cus_district,
        $cus_city,
        $cus_province,
        $cus_postal_code,
        $cus_image,
        $cus_id
    );

    if ($stmt->execute()) {
        $_SESSION['msg_ok'] = "แก้ไขข้อมูลลูกค้าเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

header("Location: ../customer.php"); 
exit();
?>