<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();

    try {
        // รับค่าและทำความสะอาดข้อมูล
        $drug_id = mysqli_real_escape_string($conn, $_POST['drug_id']);
        $drug_name = mysqli_real_escape_string($conn, $_POST['drug_name']);
        $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
        $drug_type_id = mysqli_real_escape_string($conn, $_POST['drug_type_id']);
        $drug_properties = mysqli_real_escape_string($conn, $_POST['drug_properties']);
        $drug_advice = mysqli_real_escape_string($conn, $_POST['drug_advice']);
        $drug_warning = mysqli_real_escape_string($conn, $_POST['drug_warning']);
        $drug_unit_id = mysqli_real_escape_string($conn, $_POST['drug_unit_id']);
        $drug_status = mysqli_real_escape_string($conn, $_POST['drug_status']);

        // ดึงข้อมูลเดิมก่อนการอัพเดท
        $old_data_sql = "SELECT d.*, dt.drug_type_name 
                        FROM drug d
                        LEFT JOIN drug_type dt ON d.drug_type_id = dt.drug_type_id
                        WHERE d.drug_id = ?";
        $stmt_old = $conn->prepare($old_data_sql);
        $stmt_old->bind_param("i", $drug_id);
        $stmt_old->execute();
        $old_data = $stmt_old->get_result()->fetch_assoc();

        // จัดการรูปภาพ
        $drug_pic = $old_data['drug_pic'];
        if (isset($_FILES['drug_pic']) && $_FILES['drug_pic']['error'] === UPLOAD_ERR_OK) {
            $targetDir = "../../img/drug/";
            $fileExtension = pathinfo($_FILES["drug_pic"]["name"], PATHINFO_EXTENSION);
            $newFileName = uniqid() . '.' . $fileExtension;
            $targetFilePath = $targetDir . $newFileName;

            $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
            if (in_array($fileExtension, $allowTypes)) {
                if (move_uploaded_file($_FILES["drug_pic"]["tmp_name"], $targetFilePath)) {
                    // ลบรูปเก่า
                    if ($old_data['drug_pic'] && file_exists($targetDir . $old_data['drug_pic'])) {
                        unlink($targetDir . $old_data['drug_pic']);
                    }
                    $drug_pic = $newFileName;
                }
            }
        }

        // อัพเดทข้อมูล
        $sql = "UPDATE drug SET 
                drug_name = ?, 
                branch_id = ?, 
                drug_type_id = ?, 
                drug_properties = ?, 
                drug_advice = ?, 
                drug_warning = ?, 
                drug_unit_id = ?,
                drug_pic = ?,
                drug_status = ? 
                WHERE drug_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siisssissi", 
            $drug_name, $branch_id, $drug_type_id, $drug_properties,
            $drug_advice, $drug_warning, $drug_unit_id, $drug_pic, $drug_status, $drug_id
        );

        if (!$stmt->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการอัพเดทข้อมูล");
        }

        // ดึงข้อมูลประเภทยาใหม่
        $drug_type_sql = "SELECT drug_type_name FROM drug_type WHERE drug_type_id = ?";
        $stmt_type = $conn->prepare($drug_type_sql);
        $stmt_type->bind_param("i", $drug_type_id);
        $stmt_type->execute();
        $new_drug_type = $stmt_type->get_result()->fetch_object()->drug_type_name;

        // เตรียมข้อมูลการเปลี่ยนแปลงสำหรับ log
        $changes = [];
        if ($old_data['drug_name'] !== $drug_name) {
            $changes['drug_name'] = ['from' => $old_data['drug_name'], 'to' => $drug_name];
        }
        if ($old_data['drug_type_name'] !== $new_drug_type) {
            $changes['drug_type'] = ['from' => $old_data['drug_type_name'], 'to' => $new_drug_type];
        }
        if ($old_data['drug_properties'] !== $drug_properties) {
            $changes['properties'] = ['from' => $old_data['drug_properties'], 'to' => $drug_properties];
        }
        if ($old_data['drug_status'] != $drug_status) {
            $changes['status'] = ['from' => $old_data['drug_status'], 'to' => $drug_status];
        }

        // บันทึก log ถ้ามีการเปลี่ยนแปลง
        if (!empty($changes)) {
            $log_details = json_encode([
                'changes' => $changes,
                'drug_name' => $drug_name,
                'drug_id' => $drug_id
            ], JSON_UNESCAPED_UNICODE);

            $log_sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, branch_id) 
                        VALUES (?, 'update', 'drug', ?, ?, ?)";
            
            $stmt_log = $conn->prepare($log_sql);
            $user_id = $_SESSION['users_id'];
            $stmt_log->bind_param("iisi", $user_id, $drug_id, $log_details, $branch_id);
            
            if (!$stmt_log->execute()) {
                throw new Exception("เกิดข้อผิดพลาดในการบันทึก log");
            }
        }

        $conn->commit();
        $_SESSION['msg_ok'] = "อัพเดทข้อมูลยาเรียบร้อยแล้ว";

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }

    // ปิดการเชื่อมต่อ
    if (isset($stmt)) $stmt->close();
    if (isset($stmt_old)) $stmt_old->close();
    if (isset($stmt_type)) $stmt_type->close();
    if (isset($stmt_log)) $stmt_log->close();
    $conn->close();
}

// กลับไปยังหน้ายา
header("Location: ../drug.php");
exit();
?>