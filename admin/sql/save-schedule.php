<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../../dbcon.php';

function checkTimeOverlap($roomId, $date, $startTime, $endTime, $excludeScheduleId = null) {
    global $conn;
    
    $sql = "SELECT COUNT(*) as count FROM room_schedules 
            WHERE room_id = ? AND date = ? AND 
            ((start_time < ? AND end_time > ?) OR 
             (start_time >= ? AND start_time < ?) OR 
             (end_time > ? AND end_time <= ?))";
    
    $params = [$roomId, $date, $endTime, $startTime, $startTime, $endTime, $startTime, $endTime];
    $types = "isssssss";

    if ($excludeScheduleId) {
        $sql .= " AND schedule_id != ?";
        $params[] = $excludeScheduleId;
        $types .= "i";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] > 0;
}

// Log ข้อมูลที่ได้รับ
error_log("Received POST data: " . print_r($_POST, true));

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $scheduleId = isset($_POST['scheduleId']) ? intval($_POST['scheduleId']) : 0;
    $roomId = isset($_POST['roomId']) ? intval($_POST['roomId']) : 0;

    // ตรวจสอบว่า roomId มีค่า
    if ($roomId === 0) {
        $response['message'] = 'Room ID is missing or invalid';
        echo json_encode($response);
        exit;
    }

    $date = $_POST['date'];
    $scheduleName = $_POST['scheduleName'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $intervalMinutes = intval($_POST['intervalMinutes']);
    $courses = isset($_POST['courses']) && is_array($_POST['courses']) ? $_POST['courses'] : [];

    // ตรวจสอบความซ้ำซ้อนของเวลา
    $isOverlap = checkTimeOverlap($roomId, $date, $startTime, $endTime, $scheduleId);

    if ($isOverlap) {
        $response['message'] = 'ช่วงเวลานี้ซ้ำซ้อนกับตารางที่มีอยู่แล้ว กรุณาเลือกเวลาอื่น';
        echo json_encode($response);
        exit;
    }

    $conn->begin_transaction();

    try {
        if ($scheduleId > 0) {
            // Update existing schedule
            $sql = "UPDATE room_schedules SET schedule_name = ?, start_time = ?, end_time = ?, interval_minutes = ? WHERE schedule_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssii", $scheduleName, $startTime, $endTime, $intervalMinutes, $scheduleId);
        } else {
            // Insert new schedule
            $sql = "INSERT INTO room_schedules (room_id, date, schedule_name, start_time, end_time, interval_minutes) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssi", $roomId, $date, $scheduleName, $startTime, $endTime, $intervalMinutes);
        }

        if (!$stmt->execute()) {
            throw new Exception("Error executing query: " . $stmt->error);
        }

        if ($scheduleId == 0) {
            $scheduleId = $conn->insert_id;
        }

        // Delete existing course associations
        $sql = "DELETE FROM room_courses WHERE schedule_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $scheduleId);
        $stmt->execute();

        // Insert new course associations
        if (!empty($courses)) {
            $sql = "INSERT INTO room_courses (schedule_id, course_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            foreach ($courses as $courseId) {
                $stmt->bind_param("ii", $scheduleId, $courseId);
                $stmt->execute();
            }
        }

        $conn->commit();
        $response['success'] = true;
        $response['message'] = 'บันทึกตารางเวลาเรียบร้อยแล้ว';
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error in save-schedule.php: " . $e->getMessage());
        $response['message'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    }
}

echo json_encode($response);
$conn->close();
?>