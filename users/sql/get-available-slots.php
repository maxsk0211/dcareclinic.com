<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../dbcon.php';

if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

// Get parameters
$course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
$selected_date = isset($_POST['selected_date']) ? $_POST['selected_date'] : date('Y-m-d');
$branch_id = isset($_POST['branch_id']) ? intval($_POST['branch_id']) : 0;

// Validate input
if (!$course_id || !$branch_id || empty($selected_date)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

// Get available slots for the date
$sql = "
    SELECT rs.schedule_id, rs.room_id, r.room_name, rs.start_time, rs.end_time, rs.interval_minutes
    FROM room_schedules rs
    JOIN rooms r ON rs.room_id = r.room_id
    JOIN room_courses rc ON rs.schedule_id = rc.schedule_id
    JOIN room_status rst ON r.room_id = rst.room_id AND rst.date = rs.date
    WHERE rs.date = ?
    AND r.branch_id = ?
    AND rc.course_id = ?
    AND r.status = 'active'
    AND rst.daily_status = 'open'
    ORDER BY rs.start_time
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('sii', $selected_date, $branch_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();

// Get existing bookings
$sql_bookings = "
    SELECT TIME_FORMAT(booking_datetime, '%H:%i') as booking_time, room_id
    FROM course_bookings
    WHERE DATE(booking_datetime) = ?
    AND branch_id = ?
    AND status != 'cancelled'
";

$stmt_bookings = $conn->prepare($sql_bookings);
$stmt_bookings->bind_param('si', $selected_date, $branch_id);
$stmt_bookings->execute();
$booked_slots = [];
$booking_result = $stmt_bookings->get_result();
while ($booking = $booking_result->fetch_object()) {
    if (!isset($booked_slots[$booking->booking_time])) {
        $booked_slots[$booking->booking_time] = [];
    }
    $booked_slots[$booking->booking_time][] = $booking->room_id;
}

$available_slots = [];

while ($schedule = $result->fetch_object()) {
    $start_time = strtotime($schedule->start_time);
    $end_time = strtotime($schedule->end_time);
    $interval = $schedule->interval_minutes * 60; // Convert to seconds

    for ($time = $start_time; $time < $end_time; $time += $interval) {
        $time_key = date('H:i', $time);
        
        if (!isset($available_slots[$time_key])) {
            $available_slots[$time_key] = [
                'time' => $time_key,
                'available_rooms' => [],
                'available_rooms_count' => 0,
                'interval_minutes' => $schedule->interval_minutes,
                'status' => 'available'
            ];
        }

        // Check if room is booked for this time slot
        if (!isset($booked_slots[$time_key]) || !in_array($schedule->room_id, $booked_slots[$time_key])) {
            $available_slots[$time_key]['available_rooms'][] = [
                'room_id' => $schedule->room_id,
                'room_name' => $schedule->room_name
            ];
            $available_slots[$time_key]['available_rooms_count']++;
        }
    }
}

// Update slot status
foreach ($available_slots as &$slot) {
    if ($slot['available_rooms_count'] === 0) {
        $slot['status'] = 'fully_booked';
    } elseif ($slot['available_rooms_count'] < $result->num_rows) {
        $slot['status'] = 'partially_booked';
    }
}

// Convert to array and sort by time
$slots_array = array_values($available_slots);
usort($slots_array, function($a, $b) {
    return strcmp($a['time'], $b['time']);
});

// Get available dates (next 30 days)
$available_dates = [];
$date = new DateTime($selected_date);
$end_date = new DateTime($selected_date);
$end_date->modify('+30 days');

while ($date <= $end_date) {
    $curr_date = $date->format('Y-m-d');
    
    // Check if there are any schedules for this date
    $date_check = "
        SELECT 1
        FROM room_schedules rs
        JOIN room_courses rc ON rs.schedule_id = rc.schedule_id
        JOIN room_status rst ON rs.room_id = rst.room_id AND rst.date = rs.date
        WHERE rs.date = ?
        AND rc.course_id = ?
        AND rst.daily_status = 'open'
        LIMIT 1
    ";
    
    $stmt = $conn->prepare($date_check);
    $stmt->bind_param('si', $curr_date, $course_id);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        $available_dates[] = $curr_date;
    }
    
    $date->modify('+1 day');
}

// Prepare response
$response = [
    'available_dates' => $available_dates,
    'available_slots' => $slots_array
];

// Send response
header('Content-Type: application/json');
echo json_encode($response);