<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $id = $_POST['id'] ?? 0;
    $reason = $_POST['reason'] ?? '';

    if ($id <= 0) {
        throw new Exception('Invalid permission ID');
    }

    if (empty($reason)) {
        throw new Exception('กรุณาระบุเหตุผลในการยกเลิก');
    }

    $conn->begin_transaction();

    try {
        // ดึงข้อมูลเดิม
        $sql = "SELECT * FROM user_specific_permissions WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $permission = $stmt->get_result()->fetch_assoc();

        if (!$permission) {
            throw new Exception('Permission not found');
        }

        // ยกเลิกสิทธิ์
        $sql = "UPDATE user_specific_permissions 
                SET granted = 0, 
                    end_date = CURRENT_DATE,
                    note = CONCAT(note, ' [ยกเลิก: ', ?, ']'),
                    updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $reason, $id);

        if (!$stmt->execute()) {
            throw new Exception("Error revoking permission: " . $stmt->error);
        }

        // บันทึกประวัติ
        $sql = "INSERT INTO permission_logs 
                (users_id, permission_id, action_type, old_value, new_value, performed_by) 
                VALUES (?, ?, 'revoke', ?, ?, ?)";

        $old_value = json_encode([
            'granted' => $permission['granted'],
            'end_date' => $permission['end_date'],
            'note' => $permission['note']
        ]);

        $new_value = json_encode([
            'granted' => 0,
            'end_date' => date('Y-m-d'),
            'revoke_reason' => $reason
        ]);

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iissi",
            $permission['users_id'],
            $permission['permission_id'],
            $old_value,
            $new_value,
            $_SESSION['users_id']
        );

        if (!$stmt->execute()) {
            throw new Exception("Error logging revocation: " . $stmt->error);
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'ยกเลิกสิทธิ์พิเศษเรียบร้อยแล้ว'
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