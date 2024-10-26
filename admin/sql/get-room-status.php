<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    $branch_id = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
    if ($branch_id === 0) {
        throw new Exception("Invalid branch ID");
    }

    $today = date('Y-m-d');
    $now = date('Y-m-d H:i:s');

    $sql = "SELECT 
                r.room_id, 
                r.room_name,
                COALESCE(service_status.room_status, booking_status.room_status, 'available') as status,
                COALESCE(service_status.user_name, booking_status.user_name) as user_name,
                COALESCE(service_status.time_slot, booking_status.time_slot) as time_slot
            FROM rooms r
            -- Check current services
            LEFT JOIN (
                SELECT 
                    cb.room_id,
                    'in_use' as room_status,
                    CONCAT(c.cus_firstname, ' ', c.cus_lastname) as user_name,
                    DATE_FORMAT(cb.booking_datetime, '%H:%i') as time_slot
                FROM service_queue sq
                JOIN course_bookings cb ON sq.booking_id = cb.id
                JOIN customer c ON sq.cus_id = c.cus_id
                WHERE sq.queue_date = '$today'
                AND sq.service_status = 'in_progress'
            ) service_status ON r.room_id = service_status.room_id
            -- Check future bookings
            LEFT JOIN (
                SELECT 
                    cb.room_id,
                    'reserved' as room_status,
                    CONCAT(c.cus_firstname, ' ', c.cus_lastname) as user_name,
                    DATE_FORMAT(cb.booking_datetime, '%H:%i') as time_slot,
                    cb.booking_datetime
                FROM course_bookings cb
                JOIN customer c ON cb.cus_id = c.cus_id
                LEFT JOIN service_queue sq ON cb.id = sq.booking_id
                WHERE DATE(cb.booking_datetime) = '$today'
                AND cb.booking_datetime > '$now'
                AND sq.queue_id IS NULL
            ) booking_status ON r.room_id = booking_status.room_id
            WHERE r.branch_id = $branch_id 
            AND r.status = 'active'
            ORDER BY r.room_name";

    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Database error: " . $conn->error);
    }

    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        // Check status change
        $cache_key = "room_status_{$row['room_id']}";
        $previous_status = isset($_SESSION[$cache_key]) ? $_SESSION[$cache_key] : '';
        $row['status_changed'] = ($previous_status !== '' && $previous_status !== $row['status']);
        $_SESSION[$cache_key] = $row['status'];
        
        // Format empty values
        $row['current_user'] = $row['user_name'] ?: '-';
        $row['time_slot'] = $row['time_slot'] ?: '-';
        
        $rooms[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $rooms
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}