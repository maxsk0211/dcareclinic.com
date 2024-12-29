<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // เริ่ม Transaction
    mysqli_begin_transaction($conn);
    
    try {
        // รับค่าจากฟอร์ม
        $cus_id_card_number = mysqli_real_escape_string($conn, $_POST['cus_id_card_number']);
        $cus_birthday = mysqli_real_escape_string($conn, $_POST['cus_birthday']);
        $cus_firstname = mysqli_real_escape_string($conn, $_POST['cus_firstname']);
        $cus_lastname = mysqli_real_escape_string($conn, $_POST['cus_lastname']);
        $cus_title = mysqli_real_escape_string($conn, $_POST['cus_title']);
        $cus_gender = mysqli_real_escape_string($conn, $_POST['cus_gender']);
        $cus_nickname = mysqli_real_escape_string($conn, $_POST['cus_nickname']);
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
        $cus_address = mysqli_real_escape_string($conn, $_POST['cus_address']);
        $cus_district = mysqli_real_escape_string($conn, $_POST['cus_district']);
        $cus_city = mysqli_real_escape_string($conn, $_POST['cus_city']);
        $cus_province = mysqli_real_escape_string($conn, $_POST['cus_province']);
        $cus_postal_code = mysqli_real_escape_string($conn, $_POST['cus_postal_code']);

        // แปลงวันที่จาก พ.ศ. เป็น ค.ศ.
        $thai_date = DateTime::createFromFormat('d/m/Y', $cus_birthday, new DateTimeZone('Asia/Bangkok'));
        if ($thai_date !== false) {
            $thai_date->modify('-543 year');
            $cus_birthday = $thai_date->format('Y-m-d');
        } else {
            throw new Exception("รูปแบบวันเกิดไม่ถูกต้อง");
        }

        // จัดการรูปภาพ
        $cus_image = 'customer.png'; // ค่าเริ่มต้น
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
                throw new Exception("อนุญาตเฉพาะไฟล์รูปภาพประเภท JPEG, PNG และ GIF เท่านั้น");
            }

            // ตรวจสอบขนาดไฟล์
            if ($_FILES['cus_image']['size'] > 5 * 1024 * 1024) {
                throw new Exception("ไฟล์มีขนาดใหญ่เกินไป (จำกัดที่ 5MB)");
            }

            // สร้างชื่อไฟล์ใหม่
            $file_extension = pathinfo($_FILES['cus_image']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['cus_image']['tmp_name'], $upload_path)) {
                $cus_image = $new_filename;
            } else {
                throw new Exception("เกิดข้อผิดพลาดในการอัปโหลดไฟล์");
            }
        }

        // SQL สำหรับเพิ่มข้อมูล
        $sql = "INSERT INTO customer (
            cus_id_card_number, cus_birthday, cus_firstname, cus_lastname,
            cus_title, cus_gender, cus_nickname, cus_email, cus_blood,
            cus_tel, cus_drugallergy, cus_congenital, occupation,
            height, weight, emergency_name, emergency_tel, emergency_note,
            cus_address, cus_district, cus_city, cus_province,
            cus_postal_code, cus_image
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssssddssssssssss",
            $cus_id_card_number, $cus_birthday, $cus_firstname, $cus_lastname,
            $cus_title, $cus_gender, $cus_nickname, $cus_email, $cus_blood,
            $cus_tel, $cus_drugallergy, $cus_congenital, $occupation,
            $height, $weight, $emergency_name, $emergency_tel, $emergency_note,
            $cus_address, $cus_district, $cus_city, $cus_province,
            $cus_postal_code, $cus_image
        );

        if (!$stmt->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . $stmt->error);
        }

        // บันทึก log การเพิ่มข้อมูล
        $new_customer_id = $stmt->insert_id;
        $log_details = json_encode([
            'action' => 'create',
            'customer_data' => [
                'cus_id_card_number' => $cus_id_card_number,
                'cus_firstname' => $cus_firstname,
                'cus_lastname' => $cus_lastname,
                'cus_title' => $cus_title,
                // เพิ่มข้อมูลอื่นๆ ตามต้องการ
            ]
        ]);

        $insert_log = "INSERT INTO activity_logs (
            user_id, action, entity_type, entity_id, details, branch_id
        ) VALUES (?, 'create', 'customer', ?, ?, ?)";
        
        $stmt = $conn->prepare($insert_log);
        $user_id = $_SESSION['users_id'];
        $branch_id = $_SESSION['branch_id'];
        $stmt->bind_param("iisi", $user_id, $new_customer_id, $log_details, $branch_id);
        $stmt->execute();

        // Commit transaction
        mysqli_commit($conn);
        $_SESSION['msg_ok'] = "เพิ่มข้อมูลลูกค้าเรียบร้อยแล้ว";

    } catch (Exception $e) {
        // Rollback กรณีเกิดข้อผิดพลาด
        mysqli_rollback($conn);
        $_SESSION['msg_error'] = $e->getMessage();
    }

    $stmt->close();
    $conn->close();
}

header("Location: ../customer.php");
exit();
?>