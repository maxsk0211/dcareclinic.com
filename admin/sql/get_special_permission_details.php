<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('ไม่พบ ID ที่ต้องการ');
    }

    $id = intval($_GET['id']);
    
    if ($id <= 0) {
        throw new Exception('ID ไม่ถูกต้อง');
    }

    // ดึงข้อมูลหลัก - ลบ email และ tel ที่ไม่มีในฐานข้อมูล
    $sql = "SELECT 
            usp.*, 
            u.users_fname, 
            u.users_lname,
            p.position_name,
            perm.permission_name, 
            perm.category,
            granter.users_fname as granter_fname, 
            granter.users_lname as granter_lname
        FROM user_specific_permissions usp
        LEFT JOIN users u ON usp.users_id = u.users_id
        LEFT JOIN position p ON u.position_id = p.position_id
        LEFT JOIN permissions perm ON usp.permission_id = perm.permission_id
        LEFT JOIN users granter ON usp.granted_by = granter.users_id
        WHERE usp.id = $id";

    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception('Query error: ' . $conn->error);
    }

    $data = $result->fetch_assoc();
    
    if (!$data) {
        throw new Exception('ไม่พบข้อมูลสิทธิ์พิเศษ');
    }

    // จัดรูปแบบข้อมูล - แก้ไขส่วนข้อมูลผู้ใช้
    $response = [
        'success' => true,
        'data' => [
            'user' => [
                'fullname' => $data['users_fname'] . ' ' . $data['users_lname'],
                'position' => $data['position_name']
            ],
            'permission' => [
                'name' => $data['permission_name'],
                'category' => $data['category']
            ],
            'start_date' => $data['start_date'] ? date('d/m/Y', strtotime($data['start_date'])) : null,
            'start_date_raw' => $data['start_date'] ? date('Y-m-d', strtotime($data['start_date'])) : null,
            'end_date' => $data['end_date'] ? date('d/m/Y', strtotime($data['end_date'])) : null,
            'end_date_raw' => $data['end_date'] ? date('Y-m-d', strtotime($data['end_date'])) : null,
            'note' => $data['note'],
            'granted_by' => $data['granter_fname'] . ' ' . $data['granter_lname'],
            'created_at' => date('d/m/Y H:i', strtotime($data['created_at']))
        ]
    ];

    // คำนวณสถานะ
    if ($data['end_date']) {
        $endDate = strtotime($data['end_date']);
        $now = time();
        $days_remaining = ($endDate - $now) / (60 * 60 * 24);
        
        if ($days_remaining < 0) {
            $response['data']['status'] = [
                'text' => 'หมดอายุ',
                'class' => 'bg-danger'
            ];
        } elseif ($days_remaining <= 7) {
            $response['data']['status'] = [
                'text' => 'ใกล้หมดอายุ',
                'class' => 'bg-warning'
            ];
        } else {
            $response['data']['status'] = [
                'text' => 'ใช้งานอยู่',
                'class' => 'bg-success'
            ];
        }
    } else {
        $response['data']['status'] = [
            'text' => 'ไม่มีกำหนด',
            'class' => 'bg-info'
        ];
    }

    // ดึงประวัติการแก้ไข
    $historySql = "SELECT 
            pl.*, 
            CONCAT(u.users_fname, ' ', u.users_lname) as performed_by_name,
            DATE_FORMAT(pl.created_at, '%d/%m/%Y %H:%i') as formatted_date
        FROM permission_logs pl
        LEFT JOIN users u ON pl.performed_by = u.users_id
        WHERE pl.users_id = {$data['users_id']} 
        AND pl.permission_id = {$data['permission_id']}
        ORDER BY pl.created_at DESC";

    $historyResult = $conn->query($historySql);

    if (!$historyResult) {
        throw new Exception('History query error: ' . $conn->error);
    }
    
    $history = [];
    while ($row = $historyResult->fetch_assoc()) {
        $history[] = [
            'date' => $row['formatted_date'],
            'action' => mapActionType($row['action_type']),
            'action_class' => mapActionClass($row['action_type']),
            'performed_by' => $row['performed_by_name'],
            'details' => formatHistoryDetails(
                $row['action_type'],
                json_decode($row['old_value'], true),
                json_decode($row['new_value'], true)
            )
        ];
    }
    
    $response['data']['history'] = $history;
    
    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Helper functions ยังคงเหมือนเดิม...
function mapActionType($action) {
    $types = [
        'grant' => 'ให้สิทธิ์',
        'revoke' => 'ยกเลิกสิทธิ์',
        'modify' => 'แก้ไข'
    ];
    return $types[$action] ?? $action;
}

function mapActionClass($action) {
    $classes = [
        'grant' => 'bg-success',
        'revoke' => 'bg-danger',
        'modify' => 'bg-warning'
    ];
    return $classes[$action] ?? 'bg-secondary';
}

function formatHistoryDetails($action_type, $old_value = null, $new_value = null) {
    switch ($action_type) {
        case 'grant':
            $duration = '';
            if (isset($new_value['start_date']) || isset($new_value['end_date'])) {
                $start = isset($new_value['start_date']) ? date('d/m/Y', strtotime($new_value['start_date'])) : 'ไม่ระบุ';
                $end = isset($new_value['end_date']) ? date('d/m/Y', strtotime($new_value['end_date'])) : 'ไม่มีกำหนด';
                $duration = " (ตั้งแต่ {$start} ถึง {$end})";
            }
            return "ได้รับสิทธิ์พิเศษ{$duration}";
            
        case 'revoke':
            $reason = isset($new_value['revoke_reason']) ? ": " . $new_value['revoke_reason'] : '';
            return "ยกเลิกสิทธิ์พิเศษ{$reason}";
            
        case 'modify':
            $changes = [];
            
            // เปรียบเทียบวันที่เริ่มต้น
            if (isset($old_value['start_date']) != isset($new_value['start_date']) ||
                (isset($old_value['start_date']) && isset($new_value['start_date']) &&
                 $old_value['start_date'] !== $new_value['start_date'])) {
                $old_start = isset($old_value['start_date']) ? date('d/m/Y', strtotime($old_value['start_date'])) : 'ไม่ระบุ';
                $new_start = isset($new_value['start_date']) ? date('d/m/Y', strtotime($new_value['start_date'])) : 'ไม่ระบุ';
                $changes[] = "วันที่เริ่มต้นจาก {$old_start} เป็น {$new_start}";
            }

            // เปรียบเทียบวันที่สิ้นสุด
            if (isset($old_value['end_date']) != isset($new_value['end_date']) ||
                (isset($old_value['end_date']) && isset($new_value['end_date']) &&
                 $old_value['end_date'] !== $new_value['end_date'])) {
                $old_end = isset($old_value['end_date']) ? date('d/m/Y', strtotime($old_value['end_date'])) : 'ไม่มีกำหนด';
                $new_end = isset($new_value['end_date']) ? date('d/m/Y', strtotime($new_value['end_date'])) : 'ไม่มีกำหนด';
                $changes[] = "วันที่สิ้นสุดจาก {$old_end} เป็น {$new_end}";
            }

            // เปรียบเทียบหมายเหตุ
            if (isset($old_value['note']) != isset($new_value['note']) ||
                (isset($old_value['note']) && isset($new_value['note']) &&
                 $old_value['note'] !== $new_value['note'])) {
                $changes[] = "แก้ไขหมายเหตุ";
            }

            if (empty($changes)) {
                return "แก้ไขข้อมูลสิทธิ์พิเศษ";
            }
            
            return "แก้ไขข้อมูลสิทธิ์พิเศษ: " . implode(', ', $changes);
            
        default:
            return "มีการเปลี่ยนแปลงสิทธิ์พิเศษ";
    }
}
?>