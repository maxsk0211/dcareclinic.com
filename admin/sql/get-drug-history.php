<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    $action_filter = isset($_GET['action']) ? mysqli_real_escape_string($conn, $_GET['action']) : '';
    
    $sql = "SELECT al.*, al.entity_id as drug_id, u.users_fname, u.users_lname 
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.users_id
            WHERE al.entity_type = 'drug'";
            
    if (!empty($action_filter)) {
        $sql .= " AND al.action = '$action_filter'";
    }
    
    $sql .= " ORDER BY al.created_at DESC";
    
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        throw new Exception("Database query failed");
    }
    
    $logs = array();
    while ($row = mysqli_fetch_assoc($result)) {
        // แปลง details จาก JSON string เป็น array
        $row['details'] = json_decode($row['details'], true);
        
        // จัดรูปแบบวันที่
        $row['created_at'] = date('d/m/Y H:i', strtotime($row['created_at']));
        
        $logs[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $logs
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

mysqli_close($conn);
?>