<?php
session_start();
include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์มและทำการ escape
    $drug_id = mysqli_real_escape_string($conn, $_POST['drug_id']);
    $drug_name = mysqli_real_escape_string($conn, $_POST['drug_name']);
    $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
    $drug_type_id = mysqli_real_escape_string($conn, $_POST['drug_type_id']);
    $drug_properties = mysqli_real_escape_string($conn, $_POST['drug_properties']);
    $drug_advice = mysqli_real_escape_string($conn, $_POST['drug_advice']);
    $drug_warning = mysqli_real_escape_string($conn, $_POST['drug_warning']);
    $drug_unit_id = mysqli_real_escape_string($conn, $_POST['drug_unit_id']);
    $drug_status = mysqli_real_escape_string($conn, $_POST['drug_status']);

    // อัปโหลดรูปภาพ (ถ้ามีการอัปโหลดรูปภาพใหม่)
    if (isset($_FILES['drug_pic']) && $_FILES['drug_pic']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../../img/drug/";
        $originalFileName = basename($_FILES["drug_pic"]["name"]);
        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

        // สร้างชื่อไฟล์แบบสุ่ม
        $randomFileName = uniqid() . '.' . $fileExtension;
        $targetFilePath = $targetDir . $randomFileName;

        $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
        if (in_array($fileExtension, $allowTypes)) {
            if (move_uploaded_file($_FILES["drug_pic"]["tmp_name"], $targetFilePath)) {
                $drug_pic = $randomFileName;

                // ลบรูปภาพเก่า (ถ้ามี)
                 $sql_get_old_image = "SELECT drug_pic FROM drug WHERE drug_id = '$drug_id'";
                $result_get_old_image = mysqli_query($conn, $sql_get_old_image);
                if ($result_get_old_image && $row_get_old_image = mysqli_fetch_object($result_get_old_image)) {
                    $old_image_path = "../../img/drug/" . $row_get_old_image->drug_pic;
                    if (file_exists($old_image_path)) {
                       unlink($old_image_path);
                    }
                }
            } else {
                $_SESSION['msg_error'] = "ขออภัย เกิดข้อผิดพลาดในการอัปโหลดรูปภาพของคุณ";
                header("Location: ../drug.php");
                exit();
            }
        } else {
            $_SESSION['msg_error'] = 'ขออภัย อนุญาตให้อัปโหลดเฉพาะไฟล์ JPG, JPEG, PNG และ GIF เท่านั้น';
            header("Location: ../drug.php");
            exit();
        }
    } else {
        // ถ้าไม่มีการอัปโหลดรูปภาพใหม่ ให้ใช้รูปภาพเดิม
        $sql_get_image = "SELECT drug_pic FROM drug WHERE drug_id = '$drug_id'";
        $result_get_image = mysqli_query($conn, $sql_get_image);
        $row_get_image = mysqli_fetch_object($result_get_image);
        $drug_pic = $row_get_image->drug_pic;
    }


    // สร้างคำสั่ง SQL UPDATE
    $sql = "UPDATE drug SET 
            drug_name = '$drug_name', 
            branch_id = '$branch_id', 
            drug_type_id = '$drug_type_id', 
            drug_properties = '$drug_properties', 
            drug_advice = '$drug_advice', 
            drug_warning = '$drug_warning', 
            drug_unit_id = '$drug_unit_id', 
            drug_pic = '$drug_pic' ,
            drug_status = '$drug_status' 
            WHERE drug_id = '$drug_id'";
    
    // ดำเนินการอัพเดตข้อมูล
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg_ok'] = "อัพเดตข้อมูลยาเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการอัพเดตข้อมูล: " . mysqli_error($conn);
    }
} else {
    $_SESSION['msg_error'] = "ไม่พบข้อมูลที่ส่งมา";
}

// ปิดการเชื่อมต่อ
mysqli_close($conn);

// Redirect กลับไปยังหน้าแสดงรายการยา
header("Location: ../drug.php");
exit();
?>