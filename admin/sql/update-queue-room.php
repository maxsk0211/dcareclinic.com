<?php
session_start();
require '../../dbcon.php';

if (!isset($_POST['queue_id']) || !isset($_POST['room_id'])) {
    echo json_encode(['success' => false, 'message' => 'Queue ID and Room ID are required']);
    exit;
}

$queue_id = intval($_POST['queue_id']);
$room_id = intval($_POST['room_id']);
$branch_id = $_SESSION['branch_id'];

// เริ่ม transaction
$conn->begin_transaction();

try {
    // ตรวจสอบว่าห้องว่างหรือไม่
    $sql_check = "SELECT COUNT(*) as room_in_use
                  FROM service_queue sq
                  JOIN course_bookings cb ON sq.booking_id = cb.id
                  WHERE cb.room_id = ?
                  AND sq.queue_date = CURRENT_DATE
                  AND sq.service_status = 'in_progress'";
                  
    $stmt = $conn->prepare($sql_check);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $room_status = $result->fetch_assoc();

    if ($room_status['room_in_use'] > 0) {
        throw new Exception("ห้องนี้กำลังถูกใช้งานอยู่");
    }

    // อัพเดทห้องในการจอง
    $sql_update = "UPDATE course_bookings cb
                   JOIN service_queue sq ON cb.id = sq.booking_id
                   SET cb.room_id = ?
                   WHERE sq.queue_id = ? AND sq.branch_id = ?";
                   
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("iii", $room_id, $queue_id, $branch_id);
    $success = $stmt->execute();

    if (!$success) {
        throw new Exception("ไม่สามารถอัพเดทห้องได้");
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'อัพเดทห้องสำเร็จ'
    ]);

} catch (Exception $e) {
    // Rollback ถ้าเกิดข้อผิดพลาด
    $conn->rollback();
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}