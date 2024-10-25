<?php
session_start();
require '../../dbcon.php';

$date = $_GET['date'];
$branch_id = $_SESSION['branch_id'];
$response = ['rooms' => [], 'summary' => null];

try {
    // ดึงข้อมูลห้องและตารางเวลา
    $sql = "SELECT 
                r.room_id,
                r.room_name,
                COALESCE(rs.daily_status, 'closed') as status,
                rs2.start_time,
                rs2.end_time,
                rs2.interval_minutes,
                rs2.schedule_id,
                (
                    SELECT COUNT(*)
                    FROM course_bookings cb
                    WHERE cb.room_id = r.room_id 
                    AND DATE(cb.booking_datetime) = ?
                    AND TIME(cb.booking_datetime) BETWEEN rs2.start_time AND rs2.end_time
                ) as booked_slots
            FROM rooms r
            LEFT JOIN room_status rs ON r.room_id = rs.room_id AND rs.date = ?
            LEFT JOIN room_schedules rs2 ON r.room_id = rs2.room_id AND rs2.date = ?
            WHERE r.branch_id = ? AND r.status = 'active'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $date, $date, $date, $branch_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $rooms_data = [];
    while ($row = $result->fetch_assoc()) {
        $room_id = $row['room_id'];
        
        if (!isset($rooms_data[$room_id])) {
            $rooms_data[$room_id] = [
                'room_id' => $room_id,
                'room_name' => $row['room_name'],
                'status' => $row['status'],
                'schedules' => [],
                'total_slots' => 0,
                'booked_slots' => 0
            ];
        }

        // คำนวณจำนวน slots ที่จองได้จากช่วงเวลาและ interval
        if ($row['start_time'] && $row['end_time']) {
            $start = strtotime($row['start_time']);
            $end = strtotime($row['end_time']);
            $interval = $row['interval_minutes'] * 60; // แปลงเป็นวินาที
            $total_slots = floor(($end - $start) / $interval);

            // ดึงข้อมูลคอร์สที่จองได้
            $sql_courses = "SELECT c.course_name 
                          FROM room_courses rc 
                          JOIN course c ON rc.course_id = c.course_id 
                          WHERE rc.schedule_id = ?";
            $stmt_courses = $conn->prepare($sql_courses);
            $stmt_courses->bind_param("i", $row['schedule_id']);
            $stmt_courses->execute();
            $courses_result = $stmt_courses->get_result();
            $available_courses = [];
            while ($course = $courses_result->fetch_assoc()) {
                $available_courses[] = $course['course_name'];
            }

            $schedule_data = [
                'start_time' => $row['start_time'],
                'end_time' => $row['end_time'],
                'interval_minutes' => $row['interval_minutes'],
                'total_slots' => $total_slots,
                'booked_slots' => $row['booked_slots'],
                'available_courses' => $available_courses
            ];

            $rooms_data[$room_id]['schedules'][] = $schedule_data;
            $rooms_data[$room_id]['total_slots'] += $total_slots;
            $rooms_data[$room_id]['booked_slots'] += $row['booked_slots'];
        }
    }

    // คำนวณอัตราการใช้งานและจัดเตรียมข้อมูลสำหรับส่งกลับ
    $total_occupancy = 0;
    $open_rooms = 0;

    foreach ($rooms_data as $room) {
        $occupancy_rate = $room['total_slots'] > 0 ? 
            round(($room['booked_slots'] / $room['total_slots']) * 100) : 0;

        $room_data = [
            'room_id' => $room['room_id'],
            'room_name' => $room['room_name'],
            'status' => $room['status'],
            'total_slots' => $room['total_slots'],
            'booked_slots' => $room['booked_slots'],
            'occupancy_rate' => $occupancy_rate,
            'schedules' => $room['schedules']
        ];

        $response['rooms'][] = $room_data;

        if ($room['status'] == 'open') {
            $total_occupancy += $occupancy_rate;
            $open_rooms++;
        }
    }

    // ข้อมูลสรุป
    if ($open_rooms > 0) {
        $response['summary'] = [
            'open_rooms' => $open_rooms,
            'average_occupancy' => round($total_occupancy / $open_rooms),
            'total_slots_available' => array_sum(array_column($rooms_data, 'total_slots')),
            'total_slots_booked' => array_sum(array_column($rooms_data, 'booked_slots'))
        ];
    }

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>