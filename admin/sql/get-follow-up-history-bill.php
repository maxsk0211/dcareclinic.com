<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    try {
        // ดึง course_bookings_id จาก order_course
        $sql_order = "SELECT course_bookings_id FROM order_course WHERE oc_id = ?";
        $stmt_order = $conn->prepare($sql_order);
        if (!$stmt_order) {
            throw new Exception("การเตรียมคำสั่ง SQL สำหรับ order_course ล้มเหลว: " . $conn->error);
        }
        $stmt_order->bind_param("i", $order_id);
        $stmt_order->execute();
        $result_order = $stmt_order->get_result();
        $order_data = $result_order->fetch_assoc();

        if (!$order_data) {
            throw new Exception("ไม่พบข้อมูลการสั่งซื้อ");
        }

        $course_bookings_id = $order_data['course_bookings_id'];

        // ดึงข้อมูลการนัดหมายติดตามผล
        $sql = "SELECT cb.id, cb.booking_datetime, cb.status, fn.note
                FROM course_bookings cb
                LEFT JOIN follow_up_notes fn ON cb.id = fn.booking_id
                WHERE cb.cus_id = (SELECT cus_id FROM order_course WHERE oc_id = ?)
                  AND cb.is_follow_up = 1
                ORDER BY cb.booking_datetime DESC";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("การเตรียมคำสั่ง SQL สำหรับ follow_up_notes ล้มเหลว: " . $conn->error);
        }

        $stmt->bind_param("i", $order_id);
        if (!$stmt->execute()) {
            throw new Exception("การดำเนินการคำสั่ง SQL ล้มเหลว: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $follow_ups = [];

        while ($row = $result->fetch_assoc()) {
            $follow_ups[] = [
                'id' => $row['id'],
                'booking_datetime' => date('d/m/Y H:i', strtotime($row['booking_datetime'])),
                'note' => htmlspecialchars($row['note']),
                'status' => $row['status']
            ];
        }

        echo json_encode(['success' => true, 'data' => $follow_ups]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    } finally {
        if (isset($stmt_order)) {
            $stmt_order->close();
        }
        if (isset($stmt)) {
            $stmt->close();
        }
        $conn->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่ได้ระบุ order ID']);
}