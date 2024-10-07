<?php
session_start();
require '../../dbcon.php';

$response = ['success' => false, 'message' => ''];

$room_id = $_POST['roomId'];
$room_name = $_POST['roomName'];
$status = $_POST['roomStatus'];
$branch_id = $_SESSION['branch_id'];

if (empty($room_id)) {
    // เพิ่มห้องใหม่
    $sql = "INSERT INTO rooms (room_name, status, branch_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $room_name, $status, $branch_id);
} else {
    // อัปเดตห้องที่มีอยู่
    $sql = "UPDATE rooms SET room_name = ?, status = ? WHERE room_id = ? AND branch_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $room_name, $status, $room_id, $branch_id);
}

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = empty($room_id) ? 'เพิ่มห้องสำเร็จ' : 'อัปเดตห้องสำเร็จ';
} else {
    $response['message'] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $conn->error;
}

echo json_encode($response);