<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$branch_id = $_SESSION['branch_id'];
$today = date('Y-m-d');

// เริ่ม transaction
$conn->begin_transaction();

try {
    // ดึงหมายเลขคิวถัดไป
    $sql_count = "SELECT COUNT(*) as count FROM service_queue WHERE queue_date = '$today' AND branch_id = $branch_id";
    $result = $conn->query($sql_count);
    $row = $result->fetch_assoc();
    $queue_number = 'Q' . str_pad($row['count'] + 1, 3, '0', STR_PAD_LEFT);

    // กรณีจองล่วงหน้า
    if ($_POST['booking_type'] === 'booked') {
        if (empty($_POST['booking_id'])) {
            throw new Exception('กรุณาเลือกการจอง');
        }

        $booking_id = intval($_POST['booking_id']);
        
        // ตรวจสอบว่าการจองยังไม่ถูกเพิ่มในคิว
        $sql_check = "SELECT 1 FROM service_queue WHERE booking_id = $booking_id";
        $result = $conn->query($sql_check);
        if ($result->num_rows > 0) {
            throw new Exception('การจองนี้ถูกเพิ่มในคิวแล้ว');
        }

        // ดึงข้อมูลการจอง
        $sql_booking = "SELECT cus_id, booking_datetime 
                       FROM course_bookings 
                       WHERE id = $booking_id AND branch_id = $branch_id";
        $result = $conn->query($sql_booking);
        $booking = $result->fetch_assoc();

        if (!$booking) {
            throw new Exception('ไม่พบข้อมูลการจอง');
        }

        $cus_id = $booking['cus_id'];
        $queue_time = date('H:i:s', strtotime($booking['booking_datetime']));
        $notes = $conn->real_escape_string($_POST['notes']);

        // เพิ่มคิวสำหรับการจอง
        $sql_insert = "INSERT INTO service_queue 
                      (branch_id, booking_id, cus_id, queue_number, queue_date, queue_time, notes, service_status) 
                      VALUES 
                      ($branch_id, $booking_id, $cus_id, '$queue_number', '$today', '$queue_time', '$notes', 'waiting')";

    } 
    // กรณี Walk-in
    else {
        if (empty($_POST['cus_id']) || empty($_POST['queue_time']) || empty($_POST['room_id'])) {
            throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน');
        }

        $cus_id = intval($_POST['cus_id']);
        $room_id = intval($_POST['room_id']);
        $queue_time = $conn->real_escape_string($_POST['queue_time']) . ':00';
        $notes = $conn->real_escape_string($_POST['notes']);
        $booking_datetime = $today . ' ' . $queue_time;

        // สร้างการจองสำหรับ Walk-in
        $sql_create_booking = "INSERT INTO course_bookings 
                             (branch_id, cus_id, room_id, booking_datetime, status, created_at) 
                             VALUES 
                             ($branch_id, $cus_id, $room_id, '$booking_datetime', 'confirmed', NOW())";
        
        if (!$conn->query($sql_create_booking)) {
            throw new Exception('ไม่สามารถสร้างการจองได้');
        }
        
        $booking_id = $conn->insert_id;

        // เพิ่มคิว Walk-in
        $sql_insert = "INSERT INTO service_queue 
                      (branch_id, booking_id, cus_id, queue_number, queue_date, queue_time, notes, service_status) 
                      VALUES 
                      ($branch_id, $booking_id, $cus_id, '$queue_number', '$today', '$queue_time', '$notes', 'waiting')";
    }

    if (!$conn->query($sql_insert)) {
        throw new Exception('ไม่สามารถเพิ่มคิวได้: ' . $conn->error);
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'เพิ่มคิวสำเร็จ',
        'queue_number' => $queue_number
    ]);

} catch (Exception $e) {
    // Rollback ถ้าเกิดข้อผิดพลาด
    $conn->rollback();
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}