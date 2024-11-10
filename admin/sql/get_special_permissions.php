<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT 
        usp.id,
        usp.start_date,
        usp.end_date,
        usp.note,
        CONCAT(u.users_fname, ' ', u.users_lname) as users_fullname,
        p.position_name,
        perm.permission_name,
        CONCAT(g.users_fname, ' ', g.users_lname) as granted_by_name
    FROM user_specific_permissions usp
    JOIN users u ON usp.users_id = u.users_id
    JOIN position p ON u.position_id = p.position_id
    JOIN permissions perm ON usp.permission_id = perm.permission_id
    JOIN users g ON usp.granted_by = g.users_id
    ORDER BY usp.created_at DESC";

    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Error executing query: " . $conn->error);
    }

    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        // คำนวณสถานะ
        $statusInfo = calculateStatus($row['end_date']);
        
        $data[] = [
            "DT_RowId" => "row_" . $row['id'],
            "id" => $row['id'],
            "users_fullname" => $row['users_fullname'],
            "position_name" => $row['position_name'],
            "permission_name" => $row['permission_name'],
            "start_date" => $row['start_date'] ? date('d/m/Y', strtotime($row['start_date'])) : '-',
            "end_date" => $row['end_date'] ? date('d/m/Y', strtotime($row['end_date'])) : 'ไม่มีกำหนด',
            "status_text" => $statusInfo['text'],
            "status_class" => $statusInfo['class'],
            "granted_by" => $row['granted_by_name']
        ];
    }

    // ส่งข้อมูลในรูปแบบที่ DataTables ต้องการ
    echo json_encode([
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        "recordsTotal" => count($data),
        "recordsFiltered" => count($data),
        "data" => $data
    ]);

} catch (Exception $e) {
    http_response_code(200); // เปลี่ยนเป็น 200 แทน 400 เพื่อให้ DataTables แสดงข้อความได้
    echo json_encode([
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => [],
        "error" => $e->getMessage()
    ]);
}

function calculateStatus($end_date) {
    if (!$end_date) {
        return [
            'text' => 'ไม่มีกำหนด',
            'class' => 'bg-info'
        ];
    }

    $endDate = strtotime($end_date);
    $now = time();
    $days_remaining = ($endDate - $now) / (60 * 60 * 24);
    
    if ($days_remaining < 0) {
        return [
            'text' => 'หมดอายุ',
            'class' => 'bg-danger'
        ];
    } elseif ($days_remaining <= 7) {
        return [
            'text' => 'ใกล้หมดอายุ',
            'class' => 'bg-warning'
        ];
    }
    
    return [
        'text' => 'ใช้งานอยู่',
        'class' => 'bg-success'
    ];
}
?>