<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    $branch_id = (int)$_SESSION['branch_id'];

    $sql = "SELECT 
                cb.id,
                cb.booking_datetime,
                c.cus_firstname,
                c.cus_lastname,
                r.room_id,
                r.room_name,
                cb.is_follow_up
            FROM course_bookings cb
            JOIN customer c ON cb.cus_id = c.cus_id
            LEFT JOIN rooms r ON cb.room_id = r.room_id
            WHERE DATE(cb.booking_datetime) = CURRENT_DATE 
            AND cb.branch_id = $branch_id
            AND cb.id NOT IN (
                SELECT booking_id 
                FROM service_queue 
                WHERE booking_id IS NOT NULL
            )
            ORDER BY cb.booking_datetime ASC";

    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Database error: " . $conn->error);
    }

    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $time = date('H:i', strtotime($row['booking_datetime']));
        $room_text = $row['room_name'] ? $row['room_name'] : 'ไม่ระบุห้อง';
        $prefix = $row['is_follow_up'] ? 'ติดตามผล: ' : 'จองคอร์ส: ';

        $bookings[] = [
            'id' => $row['id'],
            'text' => "{$prefix}{$row['cus_firstname']} {$row['cus_lastname']} - ห้อง {$room_text} - {$time}",
            'time' => $time,
            'room_id' => $row['room_id'],
            'room_name' => $room_text,
            'is_follow_up' => (bool)$row['is_follow_up']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $bookings
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}