<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    $permission_id = $_POST['permission_id'] ?? 0;
    $position_id = $_POST['position_id'] ?? 0;
    $granted = isset($_POST['granted']) ? ($_POST['granted'] === 'true' || $_POST['granted'] === '1') : false;

    if ($permission_id <= 0 || $position_id <= 0) {
        throw new Exception('Invalid permission or position ID');
    }

    // ตรวจสอบข้อมูลเดิม
    $checkSql = "SELECT granted FROM role_permissions 
                 WHERE permission_id = ? AND position_id = ?";
    $stmt = $conn->prepare($checkSql);
    if (!$stmt) {
        throw new Exception("Prepare check statement failed: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $permission_id, $position_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $currentPermission = $result->fetch_assoc();

    // แปลงค่า granted จาก database เป็น boolean
    $currentGranted = $currentPermission ? (bool)$currentPermission['granted'] : false;

    // เปรียบเทียบค่า
    if ($currentPermission && $currentGranted === $granted) {
        echo json_encode([
            'success' => true,
            'message' => 'ไม่มีการเปลี่ยนแปลงข้อมูล',
            'changed' => false
        ]);
        exit;
    }

    $conn->begin_transaction();

    try {
        // แปลงค่า boolean เป็น integer สำหรับ database
        $grantedInt = $granted ? 1 : 0;

        if ($currentPermission) {
            // อัพเดทข้อมูลเดิม
            $sql = "UPDATE role_permissions 
                    SET granted = ?,
                        updated_at = CURRENT_TIMESTAMP 
                    WHERE permission_id = ? AND position_id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare update statement failed: " . $conn->error);
            }
            
            $stmt->bind_param("iii", $grantedInt, $permission_id, $position_id);
        } else {
            // เพิ่มข้อมูลใหม่
            $sql = "INSERT INTO role_permissions 
                    (permission_id, position_id, granted) 
                    VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare insert statement failed: " . $conn->error);
            }
            
            $stmt->bind_param("iii", $permission_id, $position_id, $grantedInt);
        }

        if (!$stmt->execute()) {
            throw new Exception("Failed to update permission: " . $stmt->error);
        }

        // ดึง user_id ที่มีตำแหน่งนี้
        $getUserSql = "SELECT users_id FROM users WHERE position_id = ? LIMIT 1";
        $stmt = $conn->prepare($getUserSql);
        if (!$stmt) {
            throw new Exception("Prepare get user statement failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $position_id);
        $stmt->execute();
        $userResult = $stmt->get_result();
        $userData = $userResult->fetch_assoc();
        
        if (!$userData) {
            throw new Exception("ไม่พบผู้ใช้ในตำแหน่งนี้");
        }

        // บันทึกประวัติ
        $action = $currentPermission ? 'modify' : ($granted ? 'grant' : 'revoke');
        
        $logSql = "INSERT INTO permission_logs 
                   (users_id, permission_id, action_type, old_value, new_value, performed_by) 
                   VALUES (?, ?, ?, ?, ?, ?)";
                   
        $stmt = $conn->prepare($logSql);
        if (!$stmt) {
            throw new Exception("Prepare log statement failed: " . $conn->error);
        }

        $old_value = $currentPermission ? json_encode(['granted' => $currentGranted]) : null;
        $new_value = json_encode(['granted' => $granted]);

        $stmt->bind_param(
            "iisssi",
            $userData['users_id'],  // ใช้ users_id จริง
            $permission_id,
            $action,
            $old_value,
            $new_value,
            $_SESSION['users_id']
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to log permission change: " . $stmt->error);
        }

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => $granted ? 'เพิ่มสิทธิ์เรียบร้อยแล้ว' : 'ยกเลิกสิทธิ์เรียบร้อยแล้ว',
            'changed' => true,
            'action' => $action
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