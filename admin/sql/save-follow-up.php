<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $opd_id = intval($_POST['opd_id']);
    $follow_up_date = mysqli_real_escape_string($conn, $_POST['follow_up_date']);
    $follow_up_time = mysqli_real_escape_string($conn, $_POST['follow_up_time']);
    $follow_up_note = mysqli_real_escape_string($conn, $_POST['follow_up_note']);
    $room_id = intval($_POST['room_id']); // เพิ่มบรรทัดนี้

    $booking_datetime = $follow_up_date . ' ' . $follow_up_time;

    try {
        $conn->begin_transaction();

        // ดึง cus_id จาก opd
        $sql_get_cus_id = "SELECT cus_id FROM opd WHERE opd_id = ?";
        $stmt = $conn->prepare($sql_get_cus_id);
        $stmt->bind_param("i", $opd_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $cus_id = $row['cus_id'];
        } else {
            throw new Exception("ไม่พบข้อมูล OPD");
        }

        // บันทึกข้อมูลการนัดหมาย
        $sql_insert_booking = "INSERT INTO course_bookings (cus_id, booking_datetime, status, is_follow_up, room_id, users_id, branch_id) 
                               VALUES (?, ?, 'confirmed', 1, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert_booking);
        $status = 'confirmed';
        $is_follow_up = 1;
        $users_id = $_SESSION['users_id'];
        $branch_id = $_SESSION['branch_id'];
        $stmt->bind_param("isiii", $cus_id, $booking_datetime, $room_id, $users_id, $branch_id);
        if (!$stmt->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error);
        }

        $booking_id = $conn->insert_id;

        // บันทึกหมายเหตุการติดตามผล
        if (!empty($follow_up_note)) {
            $sql_insert_note = "INSERT INTO follow_up_notes (booking_id, note) VALUES (?, ?)";
            $stmt = $conn->prepare($sql_insert_note);
            $stmt->bind_param("is", $booking_id, $follow_up_note);
            if (!$stmt->execute()) {
                throw new Exception("เกิดข้อผิดพลาดในการบันทึกหมายเหตุ: " . $stmt->error);
            }
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'บันทึกการนัดติดตามผลสำเร็จ']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}