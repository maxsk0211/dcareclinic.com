<?php
session_start();
include '../chk-session.php';
require '../../dbcon.php';

$today = date('Y-m-d');
$sql = "SELECT sq.*, c.cus_firstname, c.cus_lastname, cb.booking_datetime, cb.is_follow_up 
        FROM service_queue sq
        LEFT JOIN customer c ON sq.cus_id = c.cus_id
        LEFT JOIN course_bookings cb ON sq.booking_id = cb.id
        WHERE sq.queue_date = '$today' AND sq.branch_id = {$_SESSION['branch_id']}
        ORDER BY COALESCE(cb.booking_datetime, sq.queue_time) ASC";

$result = $conn->query($sql);
$data = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // แปลงเวลาให้อยู่ในรูปแบบที่ JavaScript สามารถอ่านได้
        if ($row['booking_datetime']) {
            $row['booking_datetime'] = date('Y-m-d\TH:i:s', strtotime($row['booking_datetime']));
        }
        if ($row['queue_time']) {
            $row['queue_time'] = date('H:i:s', strtotime($row['queue_time']));
        }

        // เพิ่มข้อมูล is_follow_up
        $row['is_follow_up'] = (bool)($row['is_follow_up'] ?? 0);

        $data[] = $row;
    }
}

echo json_encode($data);