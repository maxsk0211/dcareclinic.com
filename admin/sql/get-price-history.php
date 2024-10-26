<?php
require_once '../../dbcon.php';
header('Content-Type: application/json');

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if (!$order_id || !$course_id) {
    echo json_encode(['error' => 'Invalid input parameters']);
    exit;
}

try {
    $sql = "SELECT pal.*, 
            CONCAT(u.users_fname, ' ', u.users_lname) as adjusted_by_name
            FROM price_adjustment_logs pal
            LEFT JOIN users u ON pal.adjusted_by = u.users_id
            WHERE pal.order_id = ? AND pal.course_id = ?
            ORDER BY pal.adjusted_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = [
            'adjusted_at' => $row['adjusted_at'],
            'old_price' => number_format($row['old_price'], 2),
            'new_price' => number_format($row['new_price'], 2),
            'reason' => htmlspecialchars($row['reason']),
            'adjusted_by_name' => htmlspecialchars($row['adjusted_by_name'])
        ];
    }
    
    echo json_encode($history);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}