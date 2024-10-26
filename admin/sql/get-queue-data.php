<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    $branch_id = (int)$_SESSION['branch_id'];
    $today = date('Y-m-d');

    $sql = "SELECT 
                sq.queue_id,
                sq.queue_number,
                sq.queue_time,
                sq.service_status,
                sq.notes,
                c.cus_firstname,
                c.cus_lastname,
                DATE_FORMAT(cb.booking_datetime, '%H:%i') as appointment_time,
                cb.is_follow_up,
                CONCAT(COALESCE(r.room_name, '-')) as room_name,
                CASE 
                    WHEN cb.id IS NOT NULL THEN 'booked'
                    ELSE 'walk_in'
                END as queue_type,
                DATE_FORMAT(sq.queue_time, '%H:%i') as formatted_queue_time
            FROM service_queue sq
            LEFT JOIN customer c ON sq.cus_id = c.cus_id
            LEFT JOIN course_bookings cb ON sq.booking_id = cb.id
            LEFT JOIN rooms r ON cb.room_id = r.room_id
            WHERE sq.queue_date = '$today' 
            AND sq.branch_id = $branch_id
            ORDER BY 
                CASE sq.service_status
                    WHEN 'waiting' THEN 1
                    WHEN 'in_progress' THEN 2
                    WHEN 'completed' THEN 3
                    WHEN 'cancelled' THEN 4
                END,
                COALESCE(cb.booking_datetime, sq.queue_time)";

    $result = $conn->query($sql);
    if (!$result) {
        throw new Exception("Database error: " . $conn->error);
    }

    $queues = [];
    while ($row = $result->fetch_assoc()) {
        // กำหนดเวลาที่จะแสดง (ใช้เวลาจากการจองถ้ามี ถ้าไม่มีใช้เวลาคิว)
        $row['display_time'] = $row['appointment_time'] ?: $row['formatted_queue_time'];

        // เพิ่มข้อมูลเพิ่มเติมสำหรับการแสดงผล
        $row['status_text'] = getStatusText($row['service_status']);
        $row['type_text'] = $row['is_follow_up'] ? 'ติดตามผล' : 'จองคอร์ส';
        
        // ตรวจสอบให้แน่ใจว่ามีค่าห้องเสมอ
        $row['room_name'] = $row['room_name'] === 'NULL' ? '-' : $row['room_name'];

        $queues[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $queues
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function getStatusText($status) {
    $statusMap = [
        'waiting' => 'รอดำเนินการ',
        'in_progress' => 'กำลังให้บริการ',
        'completed' => 'เสร็จสิ้น',
        'cancelled' => 'ยกเลิก'
    ];
    return $statusMap[$status] ?? $status;
}