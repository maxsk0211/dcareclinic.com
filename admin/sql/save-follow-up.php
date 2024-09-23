<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn->begin_transaction();

        $opd_id = intval($_POST['opd_id']);
        $follow_up_date = mysqli_real_escape_string($conn, $_POST['follow_up_date']);
        $follow_up_time = mysqli_real_escape_string($conn, $_POST['follow_up_time']);
        $follow_up_note = mysqli_real_escape_string($conn, $_POST['follow_up_note']);
        $branch_id = intval($_SESSION['branch_id']); // ใช้ branch_id จาก session

        // แปลงวันที่จากรูปแบบ d/m/Y เป็น Y-m-d
        $date_parts = explode('/', $follow_up_date);
        $year = intval($date_parts[2]) - 543; // แปลงปี พ.ศ. เป็น ค.ศ.
        $month = $date_parts[1];
        $day = $date_parts[0];
        $formatted_date = sprintf('%04d-%02d-%02d', $year, $month, $day);

        $booking_datetime = $formatted_date . ' ' . $follow_up_time;

        // สร้าง SQL query โดยรวม branch_id
        $sql = "INSERT INTO course_bookings (cus_id, branch_id, booking_datetime, status, is_follow_up, users_id) 
                SELECT cus_id, ?, ?, 'confirmed', 1, ?
                FROM opd 
                WHERE opd_id = ?";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error);
        }

        $stmt->bind_param("isii", $branch_id, $booking_datetime, $_SESSION['users_id'], $opd_id);

        if (!$stmt->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error);
        }

        $booking_id = $conn->insert_id;

        // บันทึกหมายเหตุการติดตามผล
        $note_sql = "INSERT INTO follow_up_notes (booking_id, note) VALUES (?, ?)";
        $note_stmt = $conn->prepare($note_sql);
        
        if ($note_stmt === false) {
            throw new Exception("เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL สำหรับหมายเหตุ: " . $conn->error);
        }

        $note_stmt->bind_param("is", $booking_id, $follow_up_note);
        
        if (!$note_stmt->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการบันทึกหมายเหตุ: " . $note_stmt->error);
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'บันทึกการนัดติดตามผลสำเร็จ']);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    } finally {
        if (isset($stmt) && $stmt !== false) {
            $stmt->close();
        }
        if (isset($note_stmt) && $note_stmt !== false) {
            $note_stmt->close();
        }
        if ($conn) {
            $conn->close();
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}