<?php
session_start();
require '../../dbcon.php';

$response = ['success' => false, 'message' => ''];

$scheduleId = $_POST['scheduleId'];
$roomId = $_POST['roomId'];
$date = $_POST['date'];
$startTime = $_POST['startTime'];
$endTime = $_POST['endTime'];
$intervalMinutes = $_POST['intervalMinutes'];
$courses = isset($_POST['courses']) ? $_POST['courses'] : [];

$conn->begin_transaction();

try {
    if ($scheduleId) {
        // อัปเดตตารางเวลาที่มีอยู่
        $sql = "UPDATE room_schedules SET start_time = ?, end_time = ?, interval_minutes = ? 
                WHERE schedule_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $startTime, $endTime, $intervalMinutes, $scheduleId);
    } else {
        // สร้างตารางเวลาใหม่
        $sql = "INSERT INTO room_schedules (room_id, date, start_time, end_time, interval_minutes) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssi", $roomId, $date, $startTime, $endTime, $intervalMinutes);
    }

    $stmt->execute();

    if (!$scheduleId) {
        $scheduleId = $stmt->insert_id;
    }

    // ลบคอร์สเก่าออก
    $deleteSql = "DELETE FROM room_courses WHERE schedule_id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("i", $scheduleId);
    $deleteStmt->execute();

    // บันทึกคอร์สใหม่
    if (!empty($courses)) {
        $insertSql = "INSERT INTO room_courses (schedule_id, course_id) VALUES (?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        foreach ($courses as $courseId) {
            $insertStmt->bind_param("ii", $scheduleId, $courseId);
            $insertStmt->execute();
        }
    }

    $conn->commit();
    $response['success'] = true;
    $response['message'] = 'บันทึกตารางเวลาและคอร์สสำเร็จ';
} catch (Exception $e) {
    $conn->rollback();
    $response['message'] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage();
}

echo json_encode($response);