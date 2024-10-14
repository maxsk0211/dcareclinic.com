<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $opd_id = intval($_POST['opd_id']);
    $follow_up_date = mysqli_real_escape_string($conn, $_POST['follow_up_date']);
    $follow_up_time = mysqli_real_escape_string($conn, $_POST['follow_up_time']);
    $follow_up_note = mysqli_real_escape_string($conn, $_POST['follow_up_note']);
    $branch_id = intval($_SESSION['branch_id']); // ใช้ branch_id จาก session

    $booking_datetime = $follow_up_date . ' ' . $follow_up_time;

    // สร้าง SQL query โดยรวม branch_id
    $sql = "INSERT INTO course_bookings (cus_id, branch_id, booking_datetime, status, is_follow_up, users_id) 
            SELECT cus_id, ?, ?, 'confirmed', 1, ?
            FROM opd 
            WHERE opd_id = ?";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("isii", $branch_id, $booking_datetime, $_SESSION['users_id'], $opd_id);

    if ($stmt->execute()) {
        $booking_id = $conn->insert_id;

        // บันทึกหมายเหตุการติดตามผล
        $note_sql = "INSERT INTO follow_up_notes (booking_id, note) VALUES (?, ?)";
        $note_stmt = $conn->prepare($note_sql);
        
        if ($note_stmt === false) {
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL สำหรับหมายเหตุ: ' . $conn->error]);
            exit;
        }

        $note_stmt->bind_param("is", $booking_id, $follow_up_note);
        
        if ($note_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'บันทึกการนัดติดตามผลสำเร็จ']);
        } else {
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกหมายเหตุ: ' . $note_stmt->error]);
        }

        $note_stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();