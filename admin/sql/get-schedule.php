<?php
require '../../dbcon.php';

$scheduleId = isset($_GET['scheduleId']) ? intval($_GET['scheduleId']) : 0;

$sql = "SELECT rs.*, GROUP_CONCAT(rc.course_id) as courses
        FROM room_schedules rs
        LEFT JOIN room_courses rc ON rs.schedule_id = rc.schedule_id
        WHERE rs.schedule_id = ?
        GROUP BY rs.schedule_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $scheduleId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $schedule = $result->fetch_assoc();
    // แปลง courses เป็น array ถ้ามีข้อมูล
    $schedule['courses'] = $schedule['courses'] ? explode(',', $schedule['courses']) : [];
    echo json_encode($schedule);
} else {
    echo json_encode(null);
}

$stmt->close();
$conn->close();
?>