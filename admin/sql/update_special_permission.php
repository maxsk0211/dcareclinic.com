<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $permission_id = $_POST['permission_id'] ?? 0;
    if ($permission_id <= 0) {
        throw new Exception('Invalid permission ID');
    }

    $conn->begin_transaction();

    try {
        // ดึงข้อมูลเดิม
        $sql = "SELECT * FROM user_specific_permissions WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $permission_id);
        $stmt->execute();
        $old_data = $stmt->get_result()->fetch_assoc();

        if (!$old_data) {
            throw new Exception('Permission not found');
        }

        // เตรียมข้อมูลเก่าสำหรับบันทึกประวัติ
        $old_value = json_encode([
            'start_date' => $old_data['start_date'],
            'end_date' => $old_data['end_date'],
            'note' => $old_data['note']
        ]);

        // อัพเดทข้อมูล
        $sql = "UPDATE user_specific_permissions 
                SET start_date = ?, 
                    end_date = ?, 
                    note = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssi",
            $_POST['start_date'] ?: null,
            $_POST['end_date'] ?: null,
            $_POST['note'],
            $permission_id
        );

        if (!$stmt->execute()) {
            throw new Exception("Error updating permission: " . $stmt->error);
        }

        // เตรียมข้อมูลใหม่สำหรับบันทึกประวัติ
        $new_value = json_encode([
            'start_date' => $_POST['start_date'] ?: null,
            'end_date' => $_POST['end_date'] ?: null,
            'note' => $_POST['note']
        ]);

        // บันทึกประวัติการแก้ไข
        $sql = "INSERT INTO permission_logs 
                (users_id, permission_id, action_type, old_value, new_value, performed_by) 
                VALUES (?, ?, 'modify', ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iissi",
            $old_data['users_id'],
            $old_data['permission_id'],
            $old_value,
            $new_value,
            $_SESSION['users_id']
        );

        if (!$stmt->execute()) {
            throw new Exception("Error logging update: " . $stmt->error);
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'อัพเดทสิทธิ์พิเศษเรียบร้อยแล้ว'
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>