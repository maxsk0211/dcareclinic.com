<?php
session_start();
// include '../chk-session.php';
require '../../dbcon.php';

$sql = "SELECT cb.id, cb.booking_datetime, c.cus_firstname, c.cus_lastname 
        FROM course_bookings cb
        JOIN customer c ON cb.cus_id = c.cus_id
        WHERE DATE(cb.booking_datetime) = CURDATE() 
        AND cb.branch_id = {$_SESSION['branch_id']}
        AND cb.id NOT IN (SELECT booking_id FROM service_queue WHERE booking_id IS NOT NULL)
        ORDER BY cb.booking_datetime ASC";

$result = $conn->query($sql);

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = [
        'id' => $row['id'],
        'cus_firstname' => $row['cus_firstname'],
        'cus_lastname' => $row['cus_lastname'],
        'time' => date('H:i', strtotime($row['booking_datetime']))
    ];
}

echo json_encode($bookings);