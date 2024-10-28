<?php
session_start();
require_once '../../dbcon.php';
header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_SESSION['users_id'])) {
        throw new Exception('กรุณาเข้าสู่ระบบใหม่');
    }

    $today = date('Y-m-d');
    
    // แก้ไข SQL โดยไม่ต้องกรองตาม branch_id
    $sql = "SELECT
        COUNT(*) as total,
        SUM(CASE 
            WHEN status = 'unused' AND expire_date >= ? THEN 1 
            ELSE 0 
        END) as unused,
        SUM(CASE 
            WHEN status = 'used' THEN 1 
            ELSE 0 
        END) as used,
        SUM(CASE 
            WHEN status = 'expired' OR (status = 'unused' AND expire_date < ?) THEN 1 
            ELSE 0 
        END) as expired,
        SUM(CASE
            WHEN discount_type = 'fixed' THEN amount
            ELSE 0
        END) as total_fixed_amount,
        SUM(CASE
            WHEN discount_type = 'fixed' AND status = 'used' THEN IFNULL(remaining_amount, 0)
            ELSE 0
        END) as total_remaining_amount
    FROM gift_vouchers";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ss", $today, $today);
    
    if (!$stmt->execute()) {
        throw new Exception("Error executing statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();

    // เพิ่มข้อมูลเพิ่มเติม
    $stats['total_used_amount'] = $stats['total_fixed_amount'] - $stats['total_remaining_amount'];

    echo json_encode($stats);

} catch (Exception $e) {
    error_log("Get Voucher Statistics Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

// ปิดการเชื่อมต่อ
if (isset($stmt) && $stmt instanceof mysqli_stmt) {
    $stmt->close();
}
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>