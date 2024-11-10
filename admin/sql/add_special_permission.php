<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // ตรวจสอบข้อมูลที่จำเป็น
    $required_fields = ['users_id', 'permission_id'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("กรุณาระบุ $field");
        }
    }

    $conn->begin_transaction();

    try {
        // กำหนดตัวแปรก่อน
        $users_id = $_POST['users_id'];
        $permission_id = $_POST['permission_id'];
        $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
        $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
        $note = $_POST['note'] ?? '';
        $granted_by = $_SESSION['users_id'];

        // เพิ่มข้อมูลสิทธิ์พิเศษ
        $sql = "INSERT INTO user_specific_permissions 
                (users_id, permission_id, start_date, end_date, note, granted_by) 
                VALUES (?, ?, ?, ?, ?, ?)";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iisssi",
            $users_id,
            $permission_id,
            $start_date,
            $end_date,
            $note,
            $granted_by
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error adding permission: " . $stmt->error);
        }

        $permission_id = $stmt->insert_id;

        // บันทึกประวัติ
        $log_data = json_encode([
            'start_date' => $start_date,
            'end_date' => $end_date,
            'note' => $note
        ]);

        $sql = "INSERT INTO permission_logs 
                (users_id, permission_id, action_type, new_value, performed_by) 
                VALUES (?, ?, 'grant', ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iisi",
            $users_id,
            $permission_id,
            $log_data,
            $granted_by
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error logging permission: " . $stmt->error);
        }

        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'เพิ่มสิทธิ์พิเศษเรียบร้อยแล้ว'
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