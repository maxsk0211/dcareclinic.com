<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

$cusId = $_GET['cus_id'] ?? '';

if (empty($cusId)) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสลูกค้า']);
    exit;
}

try {
    // ดึงข้อมูลบัตรกำนัลที่ active
    $query = "SELECT gv.*, vh.amount_used 
             FROM gift_vouchers gv
             LEFT JOIN (
                 SELECT voucher_id, SUM(amount_used) as amount_used 
                 FROM voucher_usage_history 
                 GROUP BY voucher_id
             ) vh ON gv.voucher_id = vh.voucher_id
             WHERE gv.customer_id = ?
             AND gv.status IN ('unused', 'used')
             AND gv.expire_date >= CURDATE()
             LIMIT 1";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Error preparing query: " . $conn->error);
    }

    $stmt->bind_param("i", $cusId);
    if (!$stmt->execute()) {
        throw new Exception("Error executing query: " . $stmt->error);
    }

    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => true, 'voucher' => null]);
        exit;
    }

    $voucher = $result->fetch_assoc();
    
    // คำนวณยอดเงินคงเหลือ (สำหรับ fixed amount vouchers)
    if ($voucher['discount_type'] === 'fixed') {
        $voucher['remaining_amount'] = $voucher['amount'] - ($voucher['amount_used'] ?? 0);
    } else {
        $voucher['remaining_amount'] = null;
    }

    // แปลงวันที่เป็น timestamp เพื่อให้ JavaScript จัดการได้ง่ายขึ้น
    if ($voucher['expire_date']) {
        $expire_date = new DateTime($voucher['expire_date']);
        $voucher['expire_date_timestamp'] = $expire_date->getTimestamp() * 1000;
    }

    if ($voucher['first_used_at']) {
        $first_used = new DateTime($voucher['first_used_at']);
        $voucher['first_used_timestamp'] = $first_used->getTimestamp() * 1000;
    }

    echo json_encode([
        'success' => true, 
        'voucher' => $voucher
    ]);

} catch (Exception $e) {
    error_log("Error in get-active-voucher.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage()
    ]);
}
?>