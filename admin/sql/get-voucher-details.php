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
        CONCAT(c.cus_firstname, ' ', c.cus_lastname) as customer_name,
        oc.order_payment,
        oc.order_net_total,
        b.branch_name,
        CASE 
            WHEN v.remaining_amount IS NOT NULL THEN v.remaining_amount
            WHEN v.customer_id IS NOT NULL AND v.discount_type = 'fixed' THEN v.amount
            ELSE NULL 
        END as remaining_amount,
        vh.used_at as first_actual_usage,
        vh.amount_used,
        vh.remaining_amount as history_remaining
    FROM gift_vouchers v
    LEFT JOIN users u ON v.created_by = u.users_id
    LEFT JOIN customer c ON v.customer_id = c.cus_id
    LEFT JOIN order_course oc ON v.used_in_order = oc.oc_id
    LEFT JOIN branch b ON oc.branch_id = b.branch_id  -- แก้ไขตรงนี้
    LEFT JOIN voucher_usage_history vh ON v.voucher_id = vh.voucher_id
    WHERE v.voucher_id = ?
    ORDER BY vh.used_at ASC LIMIT 1";

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
// ดึงประวัติการใช้งานทั้งหมด
$history_sql = "SELECT 
    vh.*,
    b.branch_name
FROM voucher_usage_history vh
LEFT JOIN branch b ON vh.branch_id = b.branch_id
WHERE vh.voucher_id = ?
ORDER BY vh.used_at ASC";

$history_stmt = $conn->prepare($history_sql);
$history_stmt->bind_param("i", $voucher_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();

$voucher['usage_history'] = [];
while ($row = $history_result->fetch_assoc()) {
    $voucher['usage_history'][] = $row;
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