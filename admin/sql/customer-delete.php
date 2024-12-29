<?php
session_start();
require '../../dbcon.php';

// ตรวจสอบว่ามีการส่งรหัสผ่านมาหรือไม่
if (!isset($_POST['password']) || empty($_POST['password'])) {
    $_SESSION['msg_error'] = "กรุณากรอกรหัสผ่านเพื่อยืนยันการลบข้อมูล";
    header("Location: ../customer.php");
    exit();
}

// ตรวจสอบรหัสผ่านของผู้ใช้
$user_id = $_SESSION['users_id'];
$password = mysqli_real_escape_string($conn, $_POST['password']);
$check_password = "SELECT users_password FROM users WHERE users_id = ?";
$stmt = $conn->prepare($check_password);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['users_password'] !== $password) {
    $_SESSION['msg_error'] = "รหัสผ่านไม่ถูกต้อง";
    header("Location: ../customer.php");
    exit();
}

if (isset($_GET['id'])) {
    // เริ่ม Transaction
    mysqli_begin_transaction($conn);
    
    try {
        $cus_id = mysqli_real_escape_string($conn, $_GET['id']);
        
        // เก็บข้อมูลลูกค้าก่อนลบ
        $get_customer = "SELECT * FROM customer WHERE cus_id = ?";
        $stmt = $conn->prepare($get_customer);
        $stmt->bind_param("i", $cus_id);
        $stmt->execute();
        $customer_data = $stmt->get_result()->fetch_assoc();
        
        // ตรวจสอบการใช้งานในตารางอื่น
        $check_usage = "SELECT 
            (SELECT COUNT(*) FROM order_course WHERE cus_id = ?) as order_count,
            (SELECT COUNT(*) FROM course_bookings WHERE cus_id = ?) as booking_count,
            (SELECT COUNT(*) FROM opd WHERE cus_id = ?) as opd_count";
        
        $stmt = $conn->prepare($check_usage);
        $stmt->bind_param("iii", $cus_id, $cus_id, $cus_id);
        $stmt->execute();
        $usage = $stmt->get_result()->fetch_assoc();
        
        if ($usage['order_count'] > 0 || $usage['booking_count'] > 0 || $usage['opd_count'] > 0) {
            throw new Exception("ไม่สามารถลบข้อมูลได้เนื่องจากมีการใช้งานในระบบ");
        }

        // บันทึก log ก่อนลบข้อมูล
        $log_details = json_encode([
            'action' => 'delete',
            'customer_data' => $customer_data,
            'reason' => $_POST['delete_reason'] ?? null
        ]);

        $insert_log = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, branch_id) 
                      VALUES (?, 'delete', 'customer', ?, ?, ?)";
        $stmt = $conn->prepare($insert_log);
        $branch_id = $_SESSION['branch_id'];
        $stmt->bind_param("iisi", $user_id, $cus_id, $log_details, $branch_id);
        $stmt->execute();

        // ลบรูปภาพ (ถ้ามี)
        if ($customer_data['cus_image'] && $customer_data['cus_image'] !== 'customer.png') {
            $image_path = "../../img/customer/" . $customer_data['cus_image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // ลบข้อมูลลูกค้า
        $delete_sql = "DELETE FROM customer WHERE cus_id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $cus_id);
        
        if (!$stmt->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการลบข้อมูล: " . $stmt->error);
        }
        
        mysqli_commit($conn);
        $_SESSION['msg_ok'] = "ลบข้อมูลลูกค้าเรียบร้อยแล้ว";
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['msg_error'] = $e->getMessage();
    }
}

mysqli_close($conn);
header("Location: ../customer.php");
exit();