<?php
session_start();
require '../../dbcon.php';

$branch_id = $_SESSION['branch_id'];
$current_time = date('H:i:s');

// ดึงข้อมูลห้องทั้งหมดที่ว่างในขณะนี้
$sql = "SELECT r.room_id, r.room_name,
               CASE 
                   WHEN EXISTS (
                       SELECT 1 
                       FROM service_queue sq
                       JOIN course_bookings cb ON sq.booking_id = cb.id
                       WHERE cb.room_id = r.room_id
                       AND sq.queue_date = CURRENT_DATE
                       AND sq.service_status = 'in_progress'
                   ) THEN false
                   ELSE true
               END as is_available
        FROM rooms r
        WHERE r.branch_id = ?
        AND r.status = 'active'
        ORDER BY r.room_name";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $branch_id);
$stmt->execute();
$result = $stmt->get_result();

$available_rooms = [];
while ($row = $result->fetch_assoc()) {
    if ($row['is_available']) {
        unset($row['is_available']); // ไม่จำเป็นต้องส่งกลับ เพราะส่งเฉพาะห้องที่ว่าง
        $available_rooms[] = $row;
    }
}

// เช็คการจองในอนาคต
$sql_future_bookings = "SELECT cb.room_id, MIN(cb.booking_datetime) as next_booking
                       FROM course_bookings cb
                       WHERE cb.branch_id = ?
                       AND DATE(cb.booking_datetime) = CURRENT_DATE
                       AND cb.booking_datetime > NOW()
                       AND cb.id NOT IN (
                           SELECT booking_id 
                           FROM service_queue 
                           WHERE booking_id IS NOT NULL
                       )
                       GROUP BY cb.room_id";

$stmt = $conn->prepare($sql_future_bookings);
$stmt->bind_param("i", $branch_id);
$stmt->execute();
$result = $stmt->get_result();

$future_bookings = [];
while ($row = $result->fetch_assoc()) {
    $future_bookings[$row['room_id']] = $row['next_booking'];
}

// เพิ่มข้อมูลการจองในอนาคตให้กับห้องที่ว่าง
foreach ($available_rooms as &$room) {
    $room['next_booking'] = isset($future_bookings[$room['room_id']]) 
        ? date('H:i', strtotime($future_bookings[$room['room_id']])) 
        : null;
}

echo json_encode($available_rooms);