<?php
session_start();
require '../../dbcon.php';

$roomId = $_GET['roomId'];
$date = $_GET['date'];
$response = [];

try {
    $sql = "SELECT 
                rs.schedule_id,
                rs.schedule_name,
                rs.start_time,
                rs.end_time,
                rs.interval_minutes,
                GROUP_CONCAT(c.course_name) as courses
            FROM room_schedules rs
            LEFT JOIN room_courses rc ON rs.schedule_id = rc.schedule_id
            LEFT JOIN course c ON rc.course_id = c.course_id
            WHERE rs.room_id = ? AND rs.date = ?
            GROUP BY rs.schedule_id
            ORDER BY rs.start_time";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $roomId, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $row['courses'] = $row['courses'] ? explode(',', $row['courses']) : [];
        $response[] = $row;
    }
    
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>