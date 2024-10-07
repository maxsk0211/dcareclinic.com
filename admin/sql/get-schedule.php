<?php
session_start();
require '../../dbcon.php';

$roomId = $_GET['roomId'];
$date = $_GET['date'];

$sql = "SELECT rs.*, GROUP_CONCAT(rc.course_id) as courses 
        FROM room_schedules rs 
        LEFT JOIN room_courses rc ON rs.schedule_id = rc.schedule_id 
        WHERE rs.room_id = ? AND rs.date = ?
        GROUP BY rs.schedule_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $roomId, $date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $schedule = $result->fetch_assoc();
    $schedule['courses'] = $schedule['courses'] ? explode(',', $schedule['courses']) : [];
    echo json_encode($schedule);
} else {
    echo json_encode(null);
}