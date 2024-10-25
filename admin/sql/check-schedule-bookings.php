<?php
session_start();
require '../../dbcon.php';

$response = ['hasBookings' => false];

try {
    if (!isset($_POST['scheduleId'])) {
        throw new Exception('ไม่พบ schedule ID');
    }

    $scheduleId = $_POST['scheduleId'];
    
    // ดึงข้อมูลตารางเวลา
    $sql_schedule = "SELECT room_id, date FROM room_schedules WHERE schedule_id = ?";
    $stmt_schedule = $conn->prepare($sql_schedule);
    
    if ($stmt_schedule === false) {
        throw new Exception("Error preparing schedule query: " . $conn->error);
    }
    
    $stmt_schedule->bind_param("i", $scheduleId);
    
    if (!$stmt_schedule->execute()) {
        throw new Exception("Error executing schedule query: " . $stmt_schedule->error);
    }
    
    $schedule_result = $stmt_schedule->get_result();
    $schedule = $schedule_result->fetch_assoc();

    if ($schedule) {
        // ค้นหาการจองที่ตรงกับวันและห้อง
        $sql_bookings = "SELECT COUNT(*) as booking_count 
                        FROM course_bookings 
                        WHERE room_id = ? 
                        AND DATE(booking_datetime) = ?
                        AND status != 'cancelled'";
                        
        $stmt_bookings = $conn->prepare($sql_bookings);
        
        if ($stmt_bookings === false) {
            throw new Exception("Error preparing bookings query: " . $conn->error);
        }
        
        $stmt_bookings->bind_param("is", $schedule['room_id'], $schedule['date']);
        
        if (!$stmt_bookings->execute()) {
            throw new Exception("Error executing bookings query: " . $stmt_bookings->error);
        }
        
        $result = $stmt_bookings->get_result();
        $booking_data = $result->fetch_assoc();

        if ($booking_data['booking_count'] > 0) {
            $response['hasBookings'] = true;
            $response['message'] = 'มีการจองในช่วงเวลานี้แล้ว ไม่สามารถลบได้';
        }

        $response['debug'] = [
            'scheduleId' => $scheduleId,
            'schedule' => $schedule,
            'booking_count' => $booking_data['booking_count']
        ];
    } else {
        $response['error'] = 'ไม่พบข้อมูลตารางเวลา';
    }

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    $response['debug_error'] = true;
}

header('Content-Type: application/json');
echo json_encode($response);
?>