<?php
session_start();
// include 'chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_id = $_POST['booking_id'];
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $today = date('Y-m-d');

    // สร้างหมายเลขคิว
    $sql_count = "SELECT COUNT(*) as count FROM service_queue WHERE queue_date = '$today' AND branch_id = {$_SESSION['branch_id']}";
    $result_count = $conn->query($sql_count);
    $row_count = $result_count->fetch_assoc();
    $queue_number = 'Q' . str_pad($row_count['count'] + 1, 3, '0', STR_PAD_LEFT);

    if ($booking_id === 'walk_in') {
        $cus_id = mysqli_real_escape_string($conn, $_POST['cus_id']);
        $queue_time = mysqli_real_escape_string($conn, $_POST['queue_time']);
        
        $sql_insert = "INSERT INTO service_queue (branch_id, cus_id, queue_number, queue_date, queue_time, notes, service_status) 
                       VALUES ({$_SESSION['branch_id']}, '$cus_id', '$queue_number', '$today', '$queue_time', '$notes', 'waiting')";
    } else {
        $sql_booking = "SELECT cus_id, booking_datetime FROM course_bookings WHERE id = '$booking_id'";
        $result_booking = $conn->query($sql_booking);
        $booking = $result_booking->fetch_assoc();
        
        $cus_id = $booking['cus_id'];
        $queue_time = date('H:i:s', strtotime($booking['booking_datetime']));
        
        $sql_insert = "INSERT INTO service_queue (branch_id, cus_id, booking_id, queue_number, queue_date, queue_time, notes, service_status) 
                       VALUES ({$_SESSION['branch_id']}, '$cus_id', '$booking_id', '$queue_number', '$today', '$queue_time', '$notes', 'waiting')";
    }

    if ($conn->query($sql_insert) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'เพิ่มคิวสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเพิ่มคิว: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}