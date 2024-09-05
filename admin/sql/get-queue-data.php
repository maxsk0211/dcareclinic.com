<?php
session_start();
// include 'chk-session.php';
require '../../dbcon.php';

$today = date('Y-m-d');
$sql = "SELECT sq.*, c.cus_firstname, c.cus_lastname, cb.booking_datetime 
        FROM service_queue sq
        LEFT JOIN customer c ON sq.cus_id = c.cus_id
        LEFT JOIN course_bookings cb ON sq.booking_id = cb.id
        WHERE sq.queue_date = '$today' AND sq.branch_id = {$_SESSION['branch_id']}
        ORDER BY sq.queue_time ASC";

$result = $conn->query($sql);

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>