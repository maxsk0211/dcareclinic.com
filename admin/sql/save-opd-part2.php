<?php
session_start();
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $opd_id = $_POST['opd_id'];
    $opd_diagnose = $_POST['opd_diagnose'];
    $opd_note = $_POST['opd_note'];
    // $saved_drawings = json_decode($_POST['saved_drawings'], true);

    // อัพเดตข้อมูล OPD
    $sql = "UPDATE opd SET opd_diagnose = ?, opd_note = ?, opd_status = 1 WHERE opd_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $opd_diagnose, $opd_note, $opd_id);

    if ($stmt->execute()) {
        // บันทึกรูปภาพที่วาด (ถ้ามี)
        // if (!empty($saved_drawings)) {
        //     $drawing_sql = "INSERT INTO opd_drawings (opd_id, image_path) VALUES (?, ?)";
        //     $drawing_stmt = $conn->prepare($drawing_sql);
        //     foreach ($saved_drawings as $drawing) {
        //         $drawing_stmt->bind_param("is", $opd_id, $drawing);
        //         $drawing_stmt->execute();
        //     }
        //     $drawing_stmt->close();
        // }

        // ดึง queue_id จาก opd
        $queue_sql = "SELECT queue_id FROM opd WHERE opd_id = ?";
        $queue_stmt = $conn->prepare($queue_sql);
        $queue_stmt->bind_param("i", $opd_id);
        $queue_stmt->execute();
        $queue_result = $queue_stmt->get_result();
        $queue_row = $queue_result->fetch_assoc();
        $queue_id = $queue_row['queue_id'];

        // อัพเดตสถานะคิวเป็น 'in_progress' (ไม่เปลี่ยนเป็น 'completed')
        $update_queue_sql = "UPDATE service_queue SET service_status = 'in_progress' WHERE queue_id = ?";
        $update_queue_stmt = $conn->prepare($update_queue_sql);
        $update_queue_stmt->bind_param("i", $queue_id);
        $update_queue_stmt->execute();
        $update_queue_stmt->close();

        echo json_encode(['success' => true, 'queue_id' => $queue_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();