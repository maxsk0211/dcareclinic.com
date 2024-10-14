<?php
session_start();
require '../../dbcon.php';

// เพิ่มการ log ข้อมูลที่ได้รับ
error_log("Received POST data: " . print_r($_POST, true));
error_log("Session data: " . print_r($_SESSION, true));

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถเชื่อมต่อฐานข้อมูลได้']);
    exit;
}

// ตรวจสอบว่ามีการ POST ข้อมูลมาจริง
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจาก POST
    $customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : 0;
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $booking_datetime = isset($_POST['booking_datetime']) ? $_POST['booking_datetime'] : '';
    $course_price = isset($_POST['course_price']) ? floatval($_POST['course_price']) : 0;
    $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;
    $branch_id = isset($_SESSION['branch_id']) ? intval($_SESSION['branch_id']) : 0;
    $users_id = isset($_SESSION['users_id']) ? intval($_SESSION['users_id']) : 0;

    // ตรวจสอบความถูกต้องของข้อมูล
    if ($customer_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'รหัสลูกค้าไม่ถูกต้อง']);
        exit;
    }
    if ($course_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'รหัสคอร์สไม่ถูกต้อง']);
        exit;
    }
    if (empty($booking_datetime)) {
        echo json_encode(['success' => false, 'message' => 'ไม่ได้ระบุวันและเวลาจอง']);
        exit;
    }
    if ($course_price <= 0) {
        echo json_encode(['success' => false, 'message' => 'ราคาคอร์สไม่ถูกต้อง']);
        exit;
    }
    if ($room_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ไม่ได้ระบุห้อง']);
        exit;
    }
    if ($branch_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ไม่ได้ระบุสาขา']);
        exit;
    }
    if ($users_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ไม่ได้ระบุผู้ใช้']);
        exit;
    }

    // ตรวจสอบรูปแบบของ $booking_datetime
    if (!preg_match("/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/", $booking_datetime)) {
        echo json_encode(['success' => false, 'message' => 'รูปแบบวันที่และเวลาไม่ถูกต้อง']);
        exit;
    }

    // เริ่ม transaction
    $conn->begin_transaction();

    try {
        // ตรวจสอบว่าช่วงเวลาที่เลือกยังว่างอยู่
        $check_availability_sql = "SELECT COUNT(*) as booking_count FROM course_bookings 
                                   WHERE booking_datetime = ? AND room_id = ? AND status != 'cancelled'";
        $check_stmt = $conn->prepare($check_availability_sql);
        $check_stmt->bind_param("si", $booking_datetime, $room_id);
        $check_stmt->execute();
        $availability_result = $check_stmt->get_result()->fetch_assoc();

        if ($availability_result['booking_count'] > 0) {
            throw new Exception("ช่วงเวลาที่เลือกไม่ว่าง กรุณาเลือกเวลาอื่น");
        }

        // บันทึกข้อมูลลงตาราง course_bookings
        $sql_booking = "INSERT INTO course_bookings (branch_id, cus_id, booking_datetime, users_id, status, room_id) 
                        VALUES (?, ?, ?, ?, 'confirmed', ?)";
        $stmt_booking = $conn->prepare($sql_booking);
        $stmt_booking->bind_param("iisis", $branch_id, $customer_id, $booking_datetime, $users_id, $room_id);
        $stmt_booking->execute();
        $booking_id = $stmt_booking->insert_id;

        // บันทึกข้อมูลลงตาราง order_course
        $sql_order = "INSERT INTO order_course (cus_id, users_id, course_bookings_id, order_datetime, order_payment, order_net_total, branch_id) 
                      VALUES (?, ?, ?, NOW(), 'ยังไม่จ่ายเงิน', ?, ?)";
        $stmt_order = $conn->prepare($sql_order);
        $stmt_order->bind_param("iiidi", $customer_id, $users_id, $booking_id, $course_price, $branch_id);
        $stmt_order->execute();
        $order_id = $stmt_order->insert_id;

        // บันทึกข้อมูลลงตาราง order_detail
        $sql_detail = "INSERT INTO order_detail (oc_id, course_id, od_amount, od_price) 
                       VALUES (?, ?, 1, ?)";
        $stmt_detail = $conn->prepare($sql_detail);
        $stmt_detail->bind_param("iid", $order_id, $course_id, $course_price);
        $stmt_detail->execute();

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'บันทึกการจองเรียบร้อยแล้ว']);
    } catch (Exception $e) {
        // Rollback ในกรณีที่เกิดข้อผิดพลาด
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>