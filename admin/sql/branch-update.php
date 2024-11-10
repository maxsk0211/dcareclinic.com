<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $branch_id = $_POST['branch_id'];
    $branch_name = $_POST['branch_name'];
    $branch_address = $_POST['branch_address'] ?? null;
    $branch_phone = $_POST['branch_phone'] ?? null;
    $branch_email = $_POST['branch_email'] ?? null;
    $branch_tax_id = $_POST['branch_tax_id'] ?? null;
    $branch_license_no = $_POST['branch_license_no'] ?? null;
    $branch_services = $_POST['branch_services'] ?? null;

    // ตรวจสอบว่ามีการอัพโหลดรูปภาพใหม่หรือไม่
    $branch_logo_update = "";
    if (isset($_FILES['branch_logo']) && $_FILES['branch_logo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['branch_logo']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            // สร้างชื่อไฟล์ใหม่
            $newname = uniqid() . '.' . $filetype;
            $upload_path = '../../img/' . $newname;
            
            if (move_uploaded_file($_FILES['branch_logo']['tmp_name'], $upload_path)) {
                // ลบรูปเก่า (ถ้ามี)
                $sql = "SELECT branch_logo FROM branch WHERE branch_id = ?";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("i", $branch_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        if ($row['branch_logo'] && file_exists('../../img/' . $row['branch_logo'])) {
                            unlink('../../img/' . $row['branch_logo']);
                        }
                    }
                    $stmt->close();
                }
                
                $branch_logo_update = ", branch_logo = '$newname'";
            }
        }
    }

    // อัพเดทข้อมูล
    $sql = "UPDATE branch SET 
            branch_name = ?,
            branch_address = ?,
            branch_phone = ?,
            branch_email = ?,
            branch_tax_id = ?,
            branch_license_no = ?,
            branch_services = ?
            $branch_logo_update
            WHERE branch_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param(
            "sssssssi",
            $branch_name,
            $branch_address,
            $branch_phone,
            $branch_email,
            $branch_tax_id,
            $branch_license_no,
            $branch_services,
            $branch_id
        );

        if ($stmt->execute()) {
            $_SESSION['msg_ok'] = "อัพเดทข้อมูลสาขาสำเร็จ";
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