<?php
session_start();
require_once '../dbcon.php';
header('Content-Type: application/json');

if (!isset($_SESSION['users_id'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

try {
    // Begin transaction
    $conn->begin_transaction();

    // Get POST data
    $customer_id = $_SESSION['users_id'];
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $branch_id = isset($_POST['branch_id']) ? intval($_POST['branch_id']) : 0;
    $booking_date = $_POST['booking_date'] ?? '';
    $booking_time = $_POST['booking_time'] ?? '';
    $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;

    // Convert date format from Thai to ISO
    $date_parts = explode(' ', $booking_date);
    if (count($date_parts) === 4) {
        $day = $date_parts[0];
        $month = array_search($date_parts[1], [
            'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
            'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
        ]) + 1;
        $year = intval($date_parts[3]) - 543;
        $booking_date = sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    $booking_datetime = $booking_date . ' ' . $booking_time . ':00';

    // Validate input
    if (!$course_id || !$branch_id || !$booking_datetime || !$room_id) {
        throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน');
    }

    // Check if slot is still available
    $check_query = "
        SELECT COUNT(*) as booking_count 
        FROM course_bookings 
        WHERE booking_datetime = ? 
        AND room_id = ? 
        AND status IN ('pending', 'confirmed')
    ";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param('si', $booking_datetime, $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_object();

    if ($row->booking_count > 0) {
        throw new Exception('ช่วงเวลานี้ถูกจองไปแล้ว');
    }

    // Get course details
    $course_query = "SELECT course_price FROM course WHERE course_id = ?";
    $stmt = $conn->prepare($course_query);
    $stmt->bind_param('i', $course_id);
    $stmt->execute();
    $course = $stmt->get_result()->fetch_object();

    if (!$course) {
        throw new Exception('ไม่พบข้อมูลคอร์ส');
    }

    // Insert booking
    $booking_query = "
        INSERT INTO course_bookings (
            branch_id, cus_id, booking_datetime, room_id, 
            created_at, status, is_follow_up
        ) VALUES (?, ?, ?, ?, NOW(), 'pending', 0)
    ";
    $stmt = $conn->prepare($booking_query);
    $stmt->bind_param('iiss', $branch_id, $customer_id, $booking_datetime, $room_id);
    if (!$stmt->execute()) {
        throw new Exception('ไม่สามารถบันทึกการจองได้');
    }
    $booking_id = $stmt->insert_id;

    // Create order
    $order_query = "
        INSERT INTO order_course (
            cus_id, users_id, course_bookings_id, order_datetime,
            order_payment, order_net_total, branch_id
        ) VALUES (?, ?, ?, NOW(), 'ยังไม่จ่ายเงิน', ?, ?)
    ";
    $stmt = $conn->prepare($order_query);
    $zero = 0; // สำหรับ users_id ที่ยังไม่ได้ชำระเงิน
    $stmt->bind_param('iiidi', $customer_id, $zero, $booking_id, $course->course_price, $branch_id);
    if (!$stmt->execute()) {
        throw new Exception('ไม่สามารถสร้างรายการสั่งซื้อได้');
    }
    $order_id = $stmt->insert_id;

    // Create order detail
    $detail_query = "
        INSERT INTO order_detail (
            oc_id, course_id, od_amount, od_price
        ) VALUES (?, ?, 1, ?)
    ";
    $stmt = $conn->prepare($detail_query);
    $stmt->bind_param('iid', $order_id, $course_id, $course->course_price);
    if (!$stmt->execute()) {
        throw new Exception('ไม่สามารถบันทึกรายละเอียดการสั่งซื้อได้');
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'บันทึกการจองเรียบร้อยแล้ว',
        'booking_id' => $booking_id,
        'order_id' => $order_id
    ]);

} catch (Exception $e) {
    // Rollback transaction
    $conn->rollback();
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();