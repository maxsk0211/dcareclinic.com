<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    $action_filter = isset($_GET['action']) ? $_GET['action'] : '';
    $branch_id = $_SESSION['branch_id'];

    $sql = "SELECT al.*, u.users_fname, u.users_lname 
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.users_id
            WHERE al.entity_type = 'accessory' 
            AND al.branch_id = ?";
    
    if (!empty($action_filter)) {
        $sql .= " AND al.action = ?";
    }
    
    $sql .= " ORDER BY al.created_at DESC";

    $stmt = $conn->prepare($sql);
    
    if (!empty($action_filter)) {
        $stmt->bind_param("is", $branch_id, $action_filter);
    } else {
        $stmt->bind_param("i", $branch_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $history = [];

    while ($row = $result->fetch_assoc()) {
        $row['details'] = json_decode($row['details'], true);
        $timestamp = strtotime($row['created_at']);
        $row['created_at'] = date('d/m/Y H:i:s', $timestamp);
        $history[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $history
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>