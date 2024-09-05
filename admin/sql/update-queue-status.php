<?php
session_start();
// include 'chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $queue_id = $_POST['queue_id'];
    $new_status = $_POST['status'];

    // Sanitize input
    $queue_id = mysqli_real_escape_string($conn, $queue_id);
    $new_status = mysqli_real_escape_string($conn, $new_status);

    // Update status in database
    $sql = "UPDATE service_queue SET service_status = '$new_status' WHERE queue_id = $queue_id AND branch_id = {$_SESSION['branch_id']}";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'อัพเดทสถานะสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัพเดทสถานะ: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}