<?php
require_once '../../dbcon.php';
session_start();

header('Content-Type: application/json');

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['users_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'กรุณาเข้าสู่ระบบใหม่อีกครั้ง'
    ]);
    exit();
}

try {
    // ดึงข้อมูลประเภทอุปกรณ์
    $sql = "SELECT acc_type_id, acc_type_name 
            FROM acc_type 
            WHERE branch_id = 0 OR branch_id = ?
            ORDER BY acc_type_name";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['branch_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    $types = [];
    while ($row = $result->fetch_assoc()) {
        $types[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $types
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage()
    ]);
}

$conn->close();