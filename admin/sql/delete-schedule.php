<?php
session_start();
require '../../dbcon.php';

$response = ['success' => false, 'message' => ''];

$scheduleId = $_POST['scheduleId'];

$conn->begin_transaction();

try {
    // ลบคอร์สที่เกี่ยวข้อง
    $deleteCoursesSql = "DELETE FROM room_courses WHERE schedule_id = ?";
    $deleteCoursesStmt = $conn->prepare($deleteCoursesSql);
    $deleteCoursesStmt->bind_param("i", $scheduleId);
    $deleteCoursesStmt->execute();

    // ลบตารางเวลา
    $deleteScheduleSql = "DELETE FROM room_schedules WHERE schedule_id = ?";
    $deleteScheduleStmt = $conn->prepare($deleteScheduleSql);
    $deleteScheduleStmt->bind_param("i", $scheduleId);
    $deleteScheduleStmt->execute();

    $conn->commit();
    $response['success'] = true;
    $response['message'] = 'ลบตารางเวลาและคอร์สสำเร็จ';
} catch (Exception $e) {
    $conn->rollback();
    $response['message'] = 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage();
}

echo json_encode($response);