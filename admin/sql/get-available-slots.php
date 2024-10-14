<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../dbcon.php';

$course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
$selected_date = isset($_POST['selected_date']) ? $_POST['selected_date'] : date('Y-m-d');

if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

if ($course_id == 0) {
    die(json_encode(['error' => "Invalid course_id"]));
}

// ดึงวันที่สามารถจองได้
$sql_available_dates = "
    SELECT DISTINCT rs.date
    FROM room_schedules rs
    JOIN room_courses rc ON rs.schedule_id = rc.schedule_id
    JOIN room_status rst ON rs.room_id = rst.room_id AND rs.date = rst.date
    WHERE rc.course_id = ? AND rst.daily_status = 'open' AND rs.date >= CURDATE()
    ORDER BY rs.date
";

$stmt_dates = $conn->prepare($sql_available_dates);
$stmt_dates->bind_param("i", $course_id);
$stmt_dates->execute();
$result_dates = $stmt_dates->get_result();

$available_dates = [];
while ($row = $result_dates->fetch_assoc()) {
    $available_dates[] = $row['date'];
}

// ดึงข้อมูลการจองที่มีอยู่
$sql_bookings = "
    SELECT booking_datetime, room_id
    FROM course_bookings
    WHERE DATE(booking_datetime) = ? AND status != 'cancelled'
";
$stmt_bookings = $conn->prepare($sql_bookings);
$stmt_bookings->bind_param("s", $selected_date);
$stmt_bookings->execute();
$result_bookings = $stmt_bookings->get_result();

$existing_bookings = [];
while ($booking = $result_bookings->fetch_assoc()) {
    $time = date('H:i', strtotime($booking['booking_datetime']));
    $existing_bookings[$time][$booking['room_id']] = true;
}

// ดึงข้อมูลช่วงเวลาและห้องที่ว่าง
$sql = "
    SELECT rs.date, rs.schedule_id, rs.room_id, r.room_name, rs.start_time, rs.end_time, rs.interval_minutes,
           (SELECT COUNT(DISTINCT rs2.room_id) 
            FROM room_schedules rs2 
            WHERE rs2.date = rs.date AND rs2.start_time = rs.start_time AND rs2.end_time = rs.end_time) as total_rooms
    FROM room_schedules rs
    JOIN room_courses rc ON rs.schedule_id = rc.schedule_id
    JOIN room_status rst ON rs.room_id = rst.room_id AND rs.date = rst.date
    JOIN rooms r ON rs.room_id = r.room_id
    WHERE rc.course_id = ? AND rs.date = ? AND rst.daily_status = 'open'
    ORDER BY rs.start_time, rs.room_id
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $course_id, $selected_date);
$stmt->execute();
$result = $stmt->get_result();

$availableSlots = [];
while ($row = $result->fetch_assoc()) {
    $start = new DateTime($row['start_time']);
    $end = new DateTime($row['end_time']);
    $interval = new DateInterval('PT' . $row['interval_minutes'] . 'M');
    $total_rooms = $row['total_rooms'];

    while ($start < $end) {
        $time = $start->format('H:i');
        $slotKey = $time;
        
        if (!isset($availableSlots[$slotKey])) {
            $availableSlots[$slotKey] = [
                'time' => $time,
                'rooms' => [],
                'total_rooms' => intval($row['total_rooms']), // ใช้จำนวนห้องจริงที่ดึงมาจาก query
                'available_rooms_count' => 0,
                'interval_minutes' => intval($row['interval_minutes'])
            ];
        }

        $is_room_available = !isset($existing_bookings[$time][$row['room_id']]);
        $availableSlots[$slotKey]['rooms'][] = [
            'room_id' => $row['room_id'],
            'room_name' => $row['room_name'],
            'is_available' => $is_room_available
        ];

        if ($is_room_available) {
            $availableSlots[$slotKey]['available_rooms_count']++;
        }

        $start->add($interval);
    }
}

// ในส่วนที่กำหนดสถานะ
foreach ($availableSlots as &$slot) {
    if ($slot['available_rooms_count'] == 0) {
        $slot['status'] = 'fully_booked';
    } elseif ($slot['available_rooms_count'] < $slot['total_rooms']) {
        $slot['status'] = 'partially_booked';
    } else {
        $slot['status'] = 'available';
    }
    
    $slot['available_rooms'] = array_values(array_filter($slot['rooms'], function($room) {
        return $room['is_available'];
    }));
}
error_log("Existing bookings: " . print_r($existing_bookings, true));
error_log("Available slots before status calculation: " . print_r($availableSlots, true));
error_log("Available slots after status calculation: " . print_r($availableSlots, true));

echo json_encode([
    'available_dates' => $available_dates,
    'available_slots' => array_values($availableSlots)
]);