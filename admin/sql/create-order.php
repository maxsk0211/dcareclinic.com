<?php
session_start();
require '../../dbcon.php';
$branch_id=$_SESSION['branch_id'];
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $queue_id = isset($_POST['queue_id']) ? intval($_POST['queue_id']) : 0;
    $cus_id = isset($_POST['cus_id']) ? intval($_POST['cus_id']) : 0;

    if ($queue_id == 0 || $cus_id == 0) {
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
        exit;
    }

    // ดึงข้อมูล booking_id จาก service_queue
    $sql_queue = "SELECT booking_id, queue_date, queue_time FROM service_queue WHERE queue_id = ?";
    $stmt_queue = $conn->prepare($sql_queue);
    if (!$stmt_queue) {
        echo json_encode(['success' => false, 'message' => 'การเตรียมคำสั่ง SQL ผิดพลาด: ' . $conn->error]);
        exit;
    }
    $stmt_queue->bind_param("i", $queue_id);
    if (!$stmt_queue->execute()) {
        echo json_encode(['success' => false, 'message' => 'การดึงข้อมูล queue ผิดพลาด: ' . $stmt_queue->error]);
        exit;
    }
    $result_queue = $stmt_queue->get_result();
    $queue_data = $result_queue->fetch_assoc();
    if (!$queue_data) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูล queue']);
        exit;
    }

    $booking_id = $queue_data['booking_id'];
    $queue_datetime = $queue_data['queue_date'] . ' ' . $queue_data['queue_time'];

    // ถ้าไม่มี booking_id ให้สร้าง booking ใหม่
    if ($booking_id === null) {
        $sql_create_booking = "INSERT INTO course_bookings (branch_id, cus_id, booking_datetime, created_at, users_id, status) 
                               VALUES (?, ?, ?, NOW(), ?, 'confirmed')";
        $stmt_create_booking = $conn->prepare($sql_create_booking);
        if (!$stmt_create_booking) {
            echo json_encode(['success' => false, 'message' => 'การเตรียมคำสั่ง SQL สร้าง booking ผิดพลาด: ' . $conn->error]);
            exit;
        }
        $branch_id = $_SESSION['branch_id']; // ต้องแน่ใจว่ามีค่านี้ใน session
        $stmt_create_booking->bind_param("iisi", $branch_id, $cus_id, $queue_datetime, $_SESSION['users_id']);
        if (!$stmt_create_booking->execute()) {
            echo json_encode(['success' => false, 'message' => 'การสร้าง booking ผิดพลาด: ' . $stmt_create_booking->error]);
            exit;
        }
        $booking_id = $conn->insert_id;

        // อัพเดต booking_id ใน service_queue
        $sql_update_queue = "UPDATE service_queue SET booking_id = ? WHERE queue_id = ?";
        $stmt_update_queue = $conn->prepare($sql_update_queue);
        $stmt_update_queue->bind_param("ii", $booking_id, $queue_id);
        $stmt_update_queue->execute();
    }

    // สร้างคำสั่งซื้อใหม่
    $sql_create_order = "INSERT INTO order_course (branch_id, cus_id, users_id, course_bookings_id, order_datetime, order_payment, order_net_total, order_status) 
                         VALUES (?, ?, ?, ?, NOW(), 'ยังไม่จ่ายเงิน', 0, 1)";
    $stmt_create_order = $conn->prepare($sql_create_order);
    if (!$stmt_create_order) {
        echo json_encode(['success' => false, 'message' => 'การเตรียมคำสั่ง SQL สร้างคำสั่งซื้อผิดพลาด: ' . $conn->error]);
        exit;
    }
    $stmt_create_order->bind_param("iiii",$branch_id , $cus_id, $_SESSION['users_id'], $booking_id);
    if (!$stmt_create_order->execute()) {
        echo json_encode(['success' => false, 'message' => 'การสร้างคำสั่งซื้อผิดพลาด: ' . $stmt_create_order->error]);
        exit;
    }

    $new_order_id = $conn->insert_id;
    echo json_encode(['success' => true, 'order_id' => $new_order_id]);

    $stmt_queue->close();
    if (isset($stmt_create_booking)) $stmt_create_booking->close();
    if (isset($stmt_update_queue)) $stmt_update_queue->close();
    $stmt_create_order->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();