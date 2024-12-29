<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();

    try {
        
        // รับค่าจากฟอร์ม
        $acc_id = mysqli_real_escape_string($conn, $_POST['acc_id']);
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

        // ดึงข้อมูลอุปกรณ์ก่อนลบ
        $sql_get = "SELECT a.*, at.acc_type_name 
                    FROM accessories a
                    LEFT JOIN acc_type at ON a.acc_type_id = at.acc_type_id
                    WHERE a.acc_id = ?";
        
        $stmt_get = $conn->prepare($sql_get);
        $stmt_get->bind_param("i", $acc_id);
        $stmt_get->execute();
        $result = $stmt_get->get_result();
        $acc_data = $result->fetch_assoc();

        if (!$acc_data) {
            throw new Exception("ไม่พบข้อมูลอุปกรณ์ที่ต้องการลบ");
        }

        // ตรวจสอบการใช้งานในระบบ
        $sql_check = "SELECT COUNT(*) as count FROM order_course_resources 
                     WHERE resource_type = 'accessory' AND resource_id = ?";
        
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("i", $acc_id);
        $stmt_check->execute();
        $check_result = $stmt_check->get_result();
        $usage_count = $check_result->fetch_object()->count;

        if ($usage_count > 0) {
            throw new Exception("ไม่สามารถลบอุปกรณ์นี้ได้เนื่องจากมีการใช้งานในระบบ");
        }

        // ลบข้อมูล
        $sql_delete = "DELETE FROM accessories WHERE acc_id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $acc_id);
        
        if (!$stmt_delete->execute()) {
            throw new Exception("ไม่สามารถลบข้อมูลได้");
        }

        // บันทึก log
        $log_details = json_encode([
            'reason' => $reason,
            'deleted_data' => [
                'acc_code' => 'ACC-' . str_pad($acc_id, 6, '0', STR_PAD_LEFT),
                'acc_name' => $acc_data['acc_name'],
                'acc_type' => $acc_data['acc_type_name'],
                'properties' => $acc_data['acc_properties'],
                'amount' => $acc_data['acc_amount'],
                'status' => $acc_data['acc_status']
            ]
        ], JSON_UNESCAPED_UNICODE);

        $log_sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, branch_id) 
                    VALUES (?, 'delete', 'accessory', ?, ?, ?)";
        
        $stmt_log = $conn->prepare($log_sql);
        $branch_id = $acc_data['branch_id'];
        $stmt_log->bind_param("iisi", $user_id, $acc_id, $log_details, $branch_id);
        
        if (!$stmt_log->execute()) {
            throw new Exception("ไม่สามารถบันทึกประวัติการลบ");
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'ลบข้อมูลอุปกรณ์เรียบร้อยแล้ว']);

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