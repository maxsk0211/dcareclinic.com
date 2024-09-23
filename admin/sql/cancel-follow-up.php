<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn->begin_transaction();

        $follow_up_id = intval($_POST['follow_up_id']);

        // ตรวจสอบว่าวันที่นัดยังไม่ผ่านไป และเป็นการนัดติดตามผล
        $check_sql = "SELECT booking_datetime, is_follow_up 
                      FROM course_bookings 
                      WHERE id = ? AND booking_datetime > NOW() AND status != 'cancelled'";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $follow_up_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows == 0) {
            throw new Exception("ไม่พบข้อมูลการนัดหรือไม่สามารถยกเลิกได้");
        }

        $booking_data = $check_result->fetch_assoc();
        
        if ($booking_data['is_follow_up'] != 1) {
            throw new Exception("รายการนี้ไม่ใช่การนัดติดตามผล");
        }

        // อัปเดตสถานะเป็น 'cancelled'
        $update_sql = "UPDATE course_bookings SET status = 'cancelled' WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $follow_up_id);
        if (!$update_stmt->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการยกเลิกการนัดติดตามผล: " . $update_stmt->error);
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'ยกเลิกการนัดติดตามผลสำเร็จ']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    } finally {
        if (isset($check_stmt)) $check_stmt->close();
        if (isset($update_stmt)) $update_stmt->close();
        $conn->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}