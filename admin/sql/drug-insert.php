<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // เริ่ม transaction
    $conn->begin_transaction();

    try {
        // รับและทำความสะอาดข้อมูล
        $drug_name = mysqli_real_escape_string($conn, $_POST['drug_name']);
        $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
        $drug_type_id = mysqli_real_escape_string($conn, $_POST['drug_type_id']);
        $drug_properties = mysqli_real_escape_string($conn, $_POST['drug_properties']);
        $drug_advice = mysqli_real_escape_string($conn, $_POST['drug_advice']);
        $drug_warning = mysqli_real_escape_string($conn, $_POST['drug_warning']);
        $drug_unit_id = mysqli_real_escape_string($conn, $_POST['drug_unit_id']);
        $drug_status = mysqli_real_escape_string($conn, $_POST['drug_status']);

        // จัดการอัปโหลดรูปภาพ
        $drug_pic = '';
        if (isset($_FILES['drug_pic']) && $_FILES['drug_pic']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "../../img/drug/";
            $fileExtension = pathinfo($_FILES["drug_pic"]["name"], PATHINFO_EXTENSION);
            $newFileName = uniqid() . '.' . $fileExtension;
            $targetFilePath = $targetDir . $newFileName;

            $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($fileExtension, $allowTypes)) {
                if (move_uploaded_file($_FILES["drug_pic"]["tmp_name"], $targetFilePath)) {
                    $drug_pic = $newFileName;
                } else {
                    throw new Exception("ไม่สามารถอัปโหลดรูปภาพได้");
                }
            } else {
                throw new Exception("อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG และ GIF เท่านั้น");
            }
        }

        // SQL สำหรับเพิ่มข้อมูลยา
        $sql = "INSERT INTO drug (drug_name, branch_id, drug_type_id, drug_properties, 
                drug_advice, drug_warning, drug_unit_id, drug_pic, drug_status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL");
        }

        $stmt->bind_param("siisssisi", 
            $drug_name, $branch_id, $drug_type_id, $drug_properties,
            $drug_advice, $drug_warning, $drug_unit_id, $drug_pic, $drug_status
        );

        if (!$stmt->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการบันทึกข้อมูล");
        }

        // ดึง drug_id ที่เพิ่มใหม่
        $new_drug_id = $stmt->insert_id;

        // ดึงข้อมูลประเภทยา
        $drug_type_sql = "SELECT drug_type_name FROM drug_type WHERE drug_type_id = ?";
        $stmt_type = $conn->prepare($drug_type_sql);
        $stmt_type->bind_param("i", $drug_type_id);
        $stmt_type->execute();
        $type_result = $stmt_type->get_result();
        $drug_type_name = $type_result->fetch_object()->drug_type_name;

        // เตรียมข้อมูลสำหรับ log
        $log_details = json_encode([
            'drug_name' => $drug_name,
            'drug_type' => $drug_type_name,
            'properties' => $drug_properties,
            'unit_id' => $drug_unit_id,
            'status' => $drug_status,
            'additional_info' => [
                'advice' => $drug_advice,
                'warning' => $drug_warning
            ]
        ], JSON_UNESCAPED_UNICODE);

        // บันทึก log
        $log_sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, branch_id) 
                    VALUES (?, 'create', 'drug', ?, ?, ?)";
        
        $stmt_log = $conn->prepare($log_sql);
        if (!$stmt_log) {
            throw new Exception("เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL สำหรับ log");
        }

        $user_id = $_SESSION['users_id'];
        $stmt_log->bind_param("iisi", $user_id, $new_drug_id, $log_details, $branch_id);
        
        if (!$stmt_log->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการบันทึก log");
        }

        // ยืนยัน transaction
        $conn->commit();
        $_SESSION['msg_ok'] = "เพิ่มข้อมูลยาเรียบร้อยแล้ว";

    } catch (Exception $e) {
        // ยกเลิก transaction ถ้าเกิดข้อผิดพลาด
        $conn->rollback();
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }

    // ปิดการเชื่อมต่อ
    if (isset($stmt)) $stmt->close();
    if (isset($stmt_type)) $stmt_type->close();
    if (isset($stmt_log)) $stmt_log->close();
    $conn->close();
}

// กลับไปยังหน้ายา
header("Location: ../drug.php");
exit();
?>