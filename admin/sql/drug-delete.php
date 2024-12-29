<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();

    try {
        // รับค่าจากฟอร์ม
        $drug_id = mysqli_real_escape_string($conn, $_POST['drug_id']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $reason = mysqli_real_escape_string($conn, $_POST['reason']);
        $user_id = $_SESSION['users_id'];

        // ตรวจสอบรหัสผ่าน
        $password_sql = "SELECT users_password FROM users WHERE users_id = ?";
        $stmt_pass = $conn->prepare($password_sql);
        $stmt_pass->bind_param("i", $user_id);
        $stmt_pass->execute();
        $pass_result = $stmt_pass->get_result();
        $user_data = $pass_result->fetch_assoc();

        if (!$user_data || $user_data['users_password'] !== $password) {
            throw new Exception("รหัสผ่านไม่ถูกต้อง");
        }

        // ตรวจสอบว่ามีการระบุเหตุผลหรือไม่
        if (empty($reason)) {
            throw new Exception("กรุณาระบุเหตุผลในการลบ");
        }

        // ดึงข้อมูลยาก่อนลบ
        $sql_get = "SELECT d.*, dt.drug_type_name 
                    FROM drug d
                    LEFT JOIN drug_type dt ON d.drug_type_id = dt.drug_type_id
                    WHERE d.drug_id = ?";
        
        $stmt_get = $conn->prepare($sql_get);
        $stmt_get->bind_param("i", $drug_id);
        $stmt_get->execute();
        $result = $stmt_get->get_result();
        $drug_data = $result->fetch_assoc();

        if (!$drug_data) {
            throw new Exception("ไม่พบข้อมูลยาที่ต้องการลบ");
        }

        // เก็บ path ของรูปภาพที่จะลบ
        $image_path = null;
        if (!empty($drug_data['drug_pic'])) {
            $image_path = "../../img/drug/" . $drug_data['drug_pic'];
        }

        // ตรวจสอบการใช้งาน
        $sql_check = "SELECT COUNT(*) as count FROM order_course_resources 
                     WHERE resource_type = 'drug' AND resource_id = ?";
        
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("i", $drug_id);
        $stmt_check->execute();
        $check_result = $stmt_check->get_result();
        $usage_count = $check_result->fetch_object()->count;

        if ($usage_count > 0) {
            throw new Exception("ไม่สามารถลบยานี้ได้เนื่องจากมีการใช้งานในระบบ");
        }

        // ลบข้อมูลยา
        $sql_delete = "DELETE FROM drug WHERE drug_id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $drug_id);
        
        if (!$stmt_delete->execute()) {
            throw new Exception("ไม่สามารถลบข้อมูลได้");
        }

        // บันทึก log การลบ
        $log_details = json_encode([
            'reason' => $reason,
            'deleted_data' => [
                'drug_name' => $drug_data['drug_name'],
                'drug_type' => $drug_data['drug_type_name'],
                'properties' => $drug_data['drug_properties'],
                'amount' => $drug_data['drug_amount'],
                'status' => $drug_data['drug_status']
            ]
        ], JSON_UNESCAPED_UNICODE);

        $log_sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, branch_id) 
                    VALUES (?, 'delete', 'drug', ?, ?, ?)";
        
        $stmt_log = $conn->prepare($log_sql);
        $branch_id = $drug_data['branch_id'];
        $stmt_log->bind_param("iisi", $user_id, $drug_id, $log_details, $branch_id);
        
        if (!$stmt_log->execute()) {
            throw new Exception("ไม่สามารถบันทึกประวัติการลบ");
        }

        // ลบรูปภาพถ้ามี
        if ($image_path && file_exists($image_path)) {
            unlink($image_path);
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'ลบข้อมูลยาเรียบร้อยแล้ว']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    } finally {
        if (isset($stmt_pass)) $stmt_pass->close();
        if (isset($stmt_get)) $stmt_get->close();
        if (isset($stmt_check)) $stmt_check->close();
        if (isset($stmt_delete)) $stmt_delete->close();
        if (isset($stmt_log)) $stmt_log->close();
        if (isset($conn)) $conn->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>