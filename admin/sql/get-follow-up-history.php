<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['opd_id'])) {
        throw new Exception("ไม่ได้ระบุ OPD ID");
    }

    $opd_id = intval($_GET['opd_id']);

    $sql = "SELECT cb.id, cb.booking_datetime, cb.status, fn.note
            FROM opd o
            JOIN course_bookings cb ON o.cus_id = cb.cus_id
            LEFT JOIN follow_up_notes fn ON cb.id = fn.booking_id
            WHERE o.opd_id = ? AND cb.is_follow_up = 1
            ORDER BY cb.booking_datetime DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("การเตรียมคำสั่ง SQL ล้มเหลว: " . $conn->error);
    }

    $stmt->bind_param("i", $opd_id);
    if (!$stmt->execute()) {
        throw new Exception("การดำเนินการคำสั่ง SQL ล้มเหลว: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $follow_ups = [];

    while ($row = $result->fetch_assoc()) {
        $follow_ups[] = [
            'id' => $row['id'],
            'booking_datetime' => date('d/m/Y H:i', strtotime($row['booking_datetime'])),
            'booking_datetime_raw' => $row['booking_datetime'],
            'note' => htmlspecialchars($row['note']),
            'status' => $row['status']
        ];
    }

    echo json_encode(['success' => true, 'data' => $follow_ups]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}