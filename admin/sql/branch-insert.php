<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $branch_name = $_POST['branch_name'];
    $branch_address = $_POST['branch_address'] ?? null;
    $branch_phone = $_POST['branch_phone'] ?? null;
    $branch_email = $_POST['branch_email'] ?? null;
    $branch_tax_id = $_POST['branch_tax_id'] ?? null;
    $branch_license_no = $_POST['branch_license_no'] ?? null;
    $branch_services = $_POST['branch_services'] ?? null;
    
    // จัดการอัพโหลดรูปภาพ
    $branch_logo = null;
    if (isset($_FILES['branch_logo']) && $_FILES['branch_logo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['branch_logo']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            // สร้างชื่อไฟล์ใหม่เพื่อป้องกันการซ้ำ
            $newname = uniqid() . '.' . $filetype;
            $upload_path = '../../img/' . $newname;
            
            // ตรวจสอบและสร้างโฟลเดอร์ถ้ายังไม่มี
            if (!file_exists('../../img/')) {
                mkdir('../../img/', 0777, true);
            }
            
            if (move_uploaded_file($_FILES['branch_logo']['tmp_name'], $upload_path)) {
                $branch_logo = $newname;
            }
        }
    }

    // เตรียม SQL query
    $sql = "INSERT INTO branch (
        branch_name, 
        branch_address, 
        branch_phone, 
        branch_email, 
        branch_tax_id, 
        branch_license_no, 
        branch_services, 
        branch_logo
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // เตรียม statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param(
            "ssssssss",
            $branch_name,
            $branch_address,
            $branch_phone,
            $branch_email,
            $branch_tax_id,
            $branch_license_no,
            $branch_services,
            $branch_logo
        );

        // ทำการ execute
        if ($stmt->execute()) {
            $_SESSION['msg_ok'] = "เพิ่มข้อมูลสาขาสำเร็จ";
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาด: " . $conn->error;
        }

        $stmt->close();
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL";
    }

    $conn->close();
    header("Location: ../branch.php");
    exit();
}
?>