<?php
session_start();
require_once '../../dbcon.php';
header('Content-Type: application/json; charset=utf-8');

try {
    // ตรวจสอบ session
    if (!isset($_SESSION['branch_id'])) {
        throw new Exception('กรุณาเข้าสู่ระบบใหม่');
    }

    // ตรวจสอบ parameter
    if (!isset($_GET['voucher_id'])) {
        throw new Exception('ไม่พบรหัสบัตรกำนัล');
    }

    $voucher_id = intval($_GET['voucher_id']);

    // ดึงข้อมูลบัตรกำนัล
    $sql = "SELECT 
        v.*,
        CONCAT(u.users_fname, ' ', u.users_lname) as creator_name,
        oc.order_payment,
        oc.order_net_total,
        CONCAT(c.cus_firstname, ' ', c.cus_lastname) as customer_name
    FROM gift_vouchers v
    LEFT JOIN users u ON v.created_by = u.users_id
    LEFT JOIN order_course oc ON v.used_in_order = oc.oc_id
    LEFT JOIN customer c ON oc.cus_id = c.cus_id
    WHERE v.voucher_id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("i", $voucher_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error executing statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $voucher = $result->fetch_assoc();

    if (!$voucher) {
        throw new Exception('ไม่พบข้อมูลบัตรกำนัล');
    }

    // เช็คสถานะหมดอายุ
    if ($voucher['status'] === 'unused' && strtotime($voucher['expire_date']) < strtotime('today')) {
        $voucher['status'] = 'expired';
        
        // อัพเดทสถานะในฐานข้อมูล
        $update_sql = "UPDATE gift_vouchers SET 
            status = 'expired'
        WHERE voucher_id = ?";
        
        $update_stmt = $conn->prepare($update_sql);
        if ($update_stmt) {
            $update_stmt->bind_param("i", $voucher_id);
            $update_stmt->execute();
            $update_stmt->close();
        }
    }

    // ส่งข้อมูลกลับ
    echo json_encode([
        'success' => true,
        'data' => $voucher
    ]);

    // ปิด statement
    $stmt->close();

} catch (Exception $e) {
    // ส่งข้อผิดพลาดกลับ
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'voucher_id' => $voucher_id ?? null,
            'branch_id' => $_SESSION['branch_id'] ?? null
        ]
    ]);
} finally {
    // ปิดการเชื่อมต่อ
    if (isset($conn)) {
        $conn->close();
    }
}
?>