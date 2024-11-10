<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['permission_id'])) {
        throw new Exception('ไม่พบ Permission ID');
    }

    $permission_id = intval($_GET['permission_id']);
    
    if ($permission_id <= 0) {
        throw new Exception('Invalid Permission ID');
    }

    // ดึงข้อมูลประวัติและข้อมูลที่เกี่ยวข้อง
    $sql = "SELECT 
            pl.*,
            CONCAT(performer.users_fname, ' ', performer.users_lname) as performed_by_name,
            p.position_name,
            perm.permission_name,
            DATE_FORMAT(pl.created_at, '%d/%m/%Y %H:%i') as formatted_date
        FROM permission_logs pl
        LEFT JOIN users performer ON pl.performed_by = performer.users_id
        LEFT JOIN position p ON pl.users_id = p.position_id
        LEFT JOIN permissions perm ON pl.permission_id = perm.permission_id
        WHERE pl.permission_id = ?
        ORDER BY pl.created_at DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("การเตรียมคำสั่ง SQL ผิดพลาด: " . $conn->error);
    }

    $stmt->bind_param("i", $permission_id);
    if (!$stmt->execute()) {
        throw new Exception("การดึงข้อมูลผิดพลาด: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $history = [];

    while ($row = $result->fetch_assoc()) {
        // แปลงค่า old_value และ new_value เป็น array
        $old_value = $row['old_value'] ? json_decode($row['old_value'], true) : null;
        $new_value = $row['new_value'] ? json_decode($row['new_value'], true) : null;

        $history[] = [
            'date' => $row['formatted_date'],
            'action' => mapActionType($row['action_type']),
            'action_class' => mapActionClass($row['action_type']),
            'performed_by' => $row['performed_by_name'],
            'position' => $row['position_name'],
            'permission' => $row['permission_name'],
            'details' => formatHistoryDetails(
                $row['action_type'],
                $old_value,
                $new_value,
                $row['position_name'],
                $row['permission_name']
            )
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $history
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * แปลงประเภทการกระทำเป็นข้อความภาษาไทย
 */
function mapActionType($action) {
    $types = [
        'grant' => 'ให้สิทธิ์',
        'revoke' => 'ยกเลิกสิทธิ์',
        'modify' => 'แก้ไขสิทธิ์'
    ];
    return $types[$action] ?? $action;
}

/**
 * กำหนด class สำหรับแสดงสถานะ
 */
function mapActionClass($action) {
    $classes = [
        'grant' => 'bg-success',
        'revoke' => 'bg-danger',
        'modify' => 'bg-warning'
    ];
    return $classes[$action] ?? 'bg-secondary';
}

/**
 * จัดรูปแบบรายละเอียดการเปลี่ยนแปลง
 */
function formatHistoryDetails($action_type, $old_value, $new_value, $position_name, $permission_name) {
    switch ($action_type) {
        case 'grant':
            return "เพิ่มสิทธิ์ '{$permission_name}' ให้ตำแหน่ง {$position_name}";
            
        case 'revoke':
            return "ยกเลิกสิทธิ์ '{$permission_name}' จากตำแหน่ง {$position_name}";
            
        case 'modify':
            $old_granted = isset($old_value['granted']) ? ($old_value['granted'] ? 'มีสิทธิ์' : 'ไม่มีสิทธิ์') : 'ไม่ระบุ';
            $new_granted = isset($new_value['granted']) ? ($new_value['granted'] ? 'มีสิทธิ์' : 'ไม่มีสิทธิ์') : 'ไม่ระบุ';
            
            if ($old_granted !== $new_granted) {
                return "แก้ไขสิทธิ์ '{$permission_name}' ของตำแหน่ง {$position_name} จาก{$old_granted}เป็น{$new_granted}";
            }
            return "แก้ไขสิทธิ์ '{$permission_name}' ของตำแหน่ง {$position_name}";
            
        default:
            return "มีการเปลี่ยนแปลงสิทธิ์ '{$permission_name}' ของตำแหน่ง {$position_name}";
    }
}
?>