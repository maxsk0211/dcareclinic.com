<?php
session_start();
require '../../dbcon.php';

// ตรวจสอบว่ามีการส่ง record_id มาหรือไม่
if (!isset($_POST['record_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing record_id']);
    exit;
}

$record_id = intval($_POST['record_id']);

// ลบข้อมูลผู้ขาย
$sql = "DELETE FROM service_staff_records 
        WHERE staff_record_id = ? 
        AND staff_type = 'seller' 
        AND branch_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $record_id, $_SESSION['branch_id']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'ลบข้อมูลผู้ขายสำเร็จ']);
} else {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $conn->error]);
}

$stmt->close();
$conn->close();