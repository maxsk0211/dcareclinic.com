<?php
session_start();
require '../../dbcon.php';

if (!isset($_POST['queue_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Queue ID and status are required']);
    exit;
}

$queue_id = intval($_POST['queue_id']);
$new_status = $_POST['status'];
$branch_id = $_SESSION['branch_id'];

// ตรวจสอบความถูกต้องของสถานะ
$valid_statuses = ['waiting', 'in_progress', 'completed', 'cancelled'];
if (!in_array($new_status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// เริ่ม transaction
$conn->begin_transaction();

try {
    // ดึงข้อมูลคิวปัจจุบัน
    $sql_current = "SELECT service_status, booking_id 
                    FROM service_queue 
                    WHERE queue_id = ? AND branch_id = ?";
    $stmt = $conn->prepare($sql_current);
    $stmt->bind_param("ii", $queue_id, $branch_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_queue = $result->fetch_assoc();

    if (!$current_queue) {
        throw new Exception("ไม่พบข้อมูลคิว");
    }

    // ตรวจสอบเงื่อนไขการเปลี่ยนสถานะ
    $current_status = $current_queue['service_status'];
    
    // กรณีเปลี่ยนเป็น in_progress
    if ($new_status === 'in_progress' && $current_status === 'waiting') {
        // ตรวจสอบว่ามีคิวที่กำลังให้บริการในห้องเดียวกันหรือไม่
        if ($current_queue['booking_id']) {
            $sql_check_room = "SELECT COUNT(*) as in_use
                              FROM service_queue sq1
                              JOIN course_bookings cb1 ON sq1.booking_id = cb1.id
                              JOIN course_bookings cb2 ON cb1.room_id = cb2.room_id
                              JOIN service_queue sq2 ON cb2.id = sq2.booking_id
                              WHERE sq2.queue_id = ?
                              AND sq1.service_status = 'in_progress'
                              AND sq1.queue_date = CURRENT_DATE";
            
            $stmt = $conn->prepare($sql_check_room);
            $stmt->bind_param("i", $queue_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $room_status = $result->fetch_assoc();

            if ($room_status['in_use'] > 0) {
                throw new Exception("ห้องกำลังถูกใช้งานอยู่");
            }
        }
    }

    // อัพเดทสถานะคิว
    $sql_update = "UPDATE service_queue 
                   SET service_status = ?
                   WHERE queue_id = ? AND branch_id = ?";
                   
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("sii", $new_status, $queue_id, $branch_id);
    $success = $stmt->execute();

    if (!$success) {
        throw new Exception("ไม่สามารถอัพเดทสถานะได้");
    }

    // ถ้าเปลี่ยนเป็นสถานะ completed หรือ cancelled
    if (($new_status === 'completed' || $new_status === 'cancelled') && $current_queue['booking_id']) {
        // อัพเดทสถานะการจอง
        $sql_update_booking = "UPDATE course_bookings 
                             SET status = ?
                             WHERE id = ?";
        
        $booking_status = ($new_status === 'completed') ? 'completed' : 'cancelled';
        $stmt = $conn->prepare($sql_update_booking);
        $stmt->bind_param("si", $booking_status, $current_queue['booking_id']);
        $stmt->execute();
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'อัพเดทสถานะสำเร็จ'
    ]);

} catch (Exception $e) {
    // Rollback ถ้าเกิดข้อผิดพลาด
    $conn->rollback();
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}