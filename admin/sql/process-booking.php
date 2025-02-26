<?php
session_start();
require '../../dbcon.php';
header('Content-Type: application/json');

// ฟังก์ชันสำหรับ close statement อย่างปลอดภัย
function safeCloseStmt($stmt) {
    if ($stmt && !is_bool($stmt)) {
        $stmt->close();
    }
}

if (!isset($_POST['action']) || !isset($_POST['booking_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ข้อมูลไม่ครบถ้วน'
    ]);
    exit;
}

$action = $_POST['action'];
$booking_id = intval($_POST['booking_id']);

try {
    // เริ่ม transaction
    $conn->begin_transaction();

    // ดึงข้อมูลการจองปัจจุบัน
    $sql = "SELECT cb.*, oc.oc_id 
            FROM course_bookings cb
            LEFT JOIN order_course oc ON cb.id = oc.course_bookings_id
            WHERE cb.id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param('i', $booking_id);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    if (!$booking) {
        throw new Exception('ไม่พบข้อมูลการจอง');
    }

    switch ($action) {
        case 'approve':
            if ($booking['status'] === 'confirmed') {
                throw new Exception('การจองนี้ได้รับการอนุมัติแล้ว');
            }
            if ($booking['status'] === 'cancelled') {
                throw new Exception('ไม่สามารถอนุมัติการจองที่ถูกยกเลิกแล้ว');
            }

            // อัพเดทสถานะการจอง
            $update_sql = "UPDATE course_bookings SET status = 'confirmed' WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            if (!$update_stmt) {
                throw new Exception('Prepare update failed: ' . $conn->error);
            }
            
            $update_stmt->bind_param('i', $booking_id);
            if (!$update_stmt->execute()) {
                throw new Exception('Update failed: ' . $update_stmt->error);
            }
            $update_stmt->close();

            // บันทึกประวัติการดำเนินการ
            $log_sql = "INSERT INTO activity_logs 
                       (user_id, action, entity_type, entity_id, details, branch_id) 
                       VALUES (?, 'approve', 'booking', ?, '0', ?)";
            $log_stmt = $conn->prepare($log_sql);
            if (!$log_stmt) {
                throw new Exception('Prepare log failed: ' . $conn->error);
            }
            
            $log_stmt->bind_param('iii', $_SESSION['users_id'], $booking_id, $_SESSION['branch_id']);
            if (!$log_stmt->execute()) {
                throw new Exception('Log insert failed: ' . $log_stmt->error);
            }
            $log_stmt->close();

            $message = 'อนุมัติการจองเรียบร้อยแล้ว';
            break;

        case 'cancel':
            if ($booking['status'] === 'cancelled') {
                throw new Exception('การจองนี้ถูกยกเลิกแล้ว');
            }

            $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
            if (empty($reason)) {
                throw new Exception('กรุณาระบุเหตุผลการยกเลิก');
            }

            // อัพเดทสถานะการจอง
            $cancel_sql = "UPDATE course_bookings SET status = 'cancelled' WHERE id = ?";
            $cancel_stmt = $conn->prepare($cancel_sql);
            if (!$cancel_stmt) {
                throw new Exception('Prepare cancel failed: ' . $conn->error);
            }
            
            $cancel_stmt->bind_param('i', $booking_id);
            if (!$cancel_stmt->execute()) {
                throw new Exception('Cancel update failed: ' . $cancel_stmt->error);
            }
            $cancel_stmt->close();

            // สร้างข้อมูล details
            $booking_sql = "SELECT cb.*, c.cus_firstname, c.cus_lastname 
                           FROM course_bookings cb
                           LEFT JOIN customer c ON cb.cus_id = c.cus_id
                           WHERE cb.id = ?";
            $booking_stmt = $conn->prepare($booking_sql);
            $booking_stmt->bind_param('i', $booking_id);
            $booking_stmt->execute();
            $booking_result = $booking_stmt->get_result();
            $booking_data = $booking_result->fetch_assoc();
            $booking_stmt->close();

            // สร้างข้อมูล details
            $details = json_encode([
                'reason' => $reason,
                'booking_info' => [
                    'date' => date('Y-m-d H:i:s', strtotime($booking_data['booking_datetime'])),
                    'customer' => $booking_data['cus_firstname'] . ' ' . $booking_data['cus_lastname']
                ],
                'changes' => [
                    'status' => [
                        'from' => $booking_data['status'],
                        'to' => 'cancelled'
                    ]
                ]
            ], JSON_UNESCAPED_UNICODE);

            // บันทึกประวัติการดำเนินการ - แก้ไขส่วนนี้
            $user_id = $_SESSION['users_id'];  // กำหนดตัวแปรก่อน
            $branch_id = $_SESSION['branch_id']; // กำหนดตัวแปรก่อน
            $action_type = 'cancel'; // กำหนดตัวแปรก่อน

            $log_sql = "INSERT INTO activity_logs 
                        (user_id, action, entity_type, entity_id, details, branch_id) 
                        VALUES (?, ?, 'booking', ?, ?, ?)";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param('isiss', 
                $user_id,     // ใช้ตัวแปรที่กำหนดไว้
                $action_type, // ใช้ตัวแปรที่กำหนดไว้
                $booking_id,
                $details,
                $branch_id    // ใช้ตัวแปรที่กำหนดไว้
            );
            $log_stmt->execute();
            $log_stmt->close();

            $message = 'ยกเลิกการจองเรียบร้อยแล้ว';
        break;

        default:
            throw new Exception('ไม่พบการดำเนินการที่ระบุ');
    }

    // ยืนยัน transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => $message
    ]);

} catch (Exception $e) {
    // ยกเลิก transaction ถ้าเกิดข้อผิดพลาด
    if ($conn->inTransaction()) {
        $conn->rollback();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>