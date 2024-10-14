<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../../dbcon.php';

$response = ['success' => false, 'message' => ''];

// Log ข้อมูลที่ได้รับ
error_log("Received POST data: " . print_r($_POST, true));

if (!isset($_POST['roomId']) || empty($_POST['roomId'])) {
    $response['message'] = 'Room ID is missing or invalid';
    echo json_encode($response);
    exit;
}

if (isset($_POST['roomId'], $_POST['status'], $_POST['date'])) {
    $roomId = intval($_POST['roomId']);
    $status = $_POST['status'];
    $date = $_POST['date'];
    $branchId = $_SESSION['branch_id'];

    // ตรวจสอบว่ามีข้อมูลสถานะของห้องในวันนี้หรือไม่
    $checkSql = "SELECT * FROM room_status WHERE room_id = ? AND date = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("is", $roomId, $date);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        // อัปเดตสถานะที่มีอยู่
        $updateSql = "UPDATE room_status SET daily_status = ? WHERE room_id = ? AND date = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("sis", $status, $roomId, $date);
        if ($updateStmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'อัปเดตสถานะห้องเรียบร้อยแล้ว';
        } else {
            $response['message'] = 'เกิดข้อผิดพลาดในการอัปเดตสถานะห้อง';
        }
    } else {
        // เพิ่มสถานะใหม่
        $insertSql = "INSERT INTO room_status (room_id, date, daily_status, branch_id) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("issi", $roomId, $date, $status, $branchId);
        if ($insertStmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'บันทึกสถานะห้องเรียบร้อยแล้ว';
        } else {
            $response['message'] = 'เกิดข้อผิดพลาดในการบันทึกสถานะห้อง';
        }
    }
} else {
    $response['message'] = 'ข้อมูลไม่ครบถ้วน';
}

echo json_encode($response);