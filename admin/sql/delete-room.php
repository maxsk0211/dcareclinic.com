<?php
session_start();
require '../../dbcon.php';

$response = ['success' => false, 'message' => ''];

$room_id = $_POST['roomId'];
$branch_id = $_SESSION['branch_id'];

$sql = "DELETE FROM rooms WHERE room_id = ? AND branch_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $room_id, $branch_id);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'ลบห้องสำเร็จ';
} else {
    $response['message'] = 'เกิดข้อผิดพลาดในการลบห้อง: ' . $conn->error;
}

echo json_encode($response);