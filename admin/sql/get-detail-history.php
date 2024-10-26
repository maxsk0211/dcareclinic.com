<?php
require_once '../../dbcon.php';
header('Content-Type: application/json');

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if (!$order_id || !$course_id) {
    echo json_encode([]);
    exit;
}

try {
    $sql = "SELECT cdl.*, 
            CONCAT(u.users_fname, ' ', u.users_lname) as updated_by_name
            FROM course_detail_logs cdl
            LEFT JOIN users u ON cdl.updated_by = u.users_id
            WHERE cdl.order_id = ? AND cdl.course_id = ?
            ORDER BY cdl.updated_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = [
            'updated_at' => $row['updated_at'],
            'old_detail' => $row['old_detail'] ?: 'ไม่มีข้อมูล',
            'new_detail' => $row['new_detail'],
            'updated_by_name' => $row['updated_by_name']
        ];
    }
    
    echo json_encode($history);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    
} finally {
    $conn->close();
}