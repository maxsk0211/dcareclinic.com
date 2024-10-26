<?php
session_start();
require '../../dbcon.php';

if (!isset($_POST['queue_id'])) {
    echo json_encode(['success' => false, 'message' => 'Queue ID is required']);
    exit;
}

$queue_id = intval($_POST['queue_id']);
$branch_id = $_SESSION['branch_id'];
$current_time = date('H:i:s');

// ตรวจสอบห้องของคิวที่ต้องการเริ่มให้บริการ
$sql = "SELECT cb.room_id, r.room_name
        FROM service_queue sq
        LEFT JOIN course_bookings cb ON sq.booking_id = cb.id
        LEFT JOIN rooms r ON cb.room_id = r.room_id
        WHERE sq.queue_id = ? AND sq.branch_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $queue_id, $branch_id);
$stmt->execute();
$result = $stmt->get_result();
$queue = $result->fetch_assoc();

if (!$queue) {
    echo json_encode(['success' => false, 'message' => 'Queue not found']);
    exit;
}

if (!$queue['room_id']) {
    // ถ้าไม่มีห้องที่กำหนด ถือว่าสามารถเริ่มให้บริการได้
    echo json_encode(['success' => true, 'room_available' => true]);
    exit;
}

// ตรวจสอบว่าห้องว่างหรือไม่
$sql_check_room = "SELECT COUNT(*) as room_in_use
                   FROM service_queue sq
                   JOIN course_bookings cb ON sq.booking_id = cb.id
                   WHERE cb.room_id = ?
                   AND sq.queue_date = CURRENT_DATE
                   AND sq.service_status = 'in_progress'
                   AND sq.queue_id != ?";

$stmt = $conn->prepare($sql_check_room);
$stmt->bind_param("ii", $queue['room_id'], $queue_id);
$stmt->execute();
$result = $stmt->get_result();
$room_status = $result->fetch_assoc();

echo json_encode([
    'success' => true,
    'room_available' => $room_status['room_in_use'] == 0,
    'room_id' => $queue['room_id'],
    'room_name' => $queue['room_name']
]);