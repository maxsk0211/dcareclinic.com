<?php
session_start();
include '../../dbcon.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$voucher_id = $_POST['voucher_id'] ?? null;
$order_id = $_POST['order_id'] ?? null;
$discount_amount = $_POST['discount_amount'] ?? null;

if (!$voucher_id || !$order_id || !$discount_amount) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    $conn->begin_transaction();

    // ตรวจสอบความถูกต้องของบัตรกำนัล
    $stmt = $conn->prepare("SELECT gv.*, c.cus_id 
                           FROM gift_vouchers gv
                           JOIN customer c ON gv.customer_id = c.cus_id
                           WHERE gv.voucher_id = ? 
                           AND gv.status = 'unused'
                           AND gv.expire_date >= CURRENT_DATE()");
    $stmt->bind_param("i", $voucher_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $voucher = $result->fetch_assoc();

    if (!$voucher) {
        throw new Exception('บัตรกำนัลไม่สามารถใช้งานได้ (หมดอายุหรือถูกใช้งานไปแล้ว)');
    }

    // ตรวจสอบการผูกกับลูกค้า
    $stmt = $conn->prepare("SELECT cus_id FROM order_course WHERE oc_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if ($order['cus_id'] != $voucher['cus_id']) {
        throw new Exception('บัตรกำนัลนี้ไม่สามารถใช้กับลูกค้าท่านนี้ได้');
    }

    // ตรวจสอบการใช้งานและยอดคงเหลือ
    if ($voucher['discount_type'] === 'fixed') {
        $stmt = $conn->prepare("SELECT COALESCE(SUM(amount_used), 0) as total_used 
                               FROM voucher_usage_history 
                               WHERE voucher_id = ?");
        $stmt->bind_param("i", $voucher_id);
        $stmt->execute();
        $usage = $stmt->get_result()->fetch_assoc();
        
        $remaining = $voucher['amount'] - $usage['total_used'];
        if ($discount_amount > $remaining) {
            throw new Exception('ยอดใช้งานเกินวงเงินคงเหลือในบัตร');
        }
    }

    // บันทึกประวัติการใช้งาน
    $stmt = $conn->prepare("INSERT INTO voucher_usage_history 
                           (voucher_id, order_id, customer_id, amount_used, remaining_amount, branch_id) 
                           VALUES (?, ?, ?, ?, ?, ?)");
    
    $remaining_after = $voucher['discount_type'] === 'fixed' ? ($remaining - $discount_amount) : 0;
    $stmt->bind_param("iiiddi", 
        $voucher_id, 
        $order_id, 
        $voucher['cus_id'], 
        $discount_amount, 
        $remaining_after, 
        $_SESSION['branch_id']
    );
    $stmt->execute();

    // อัพเดทสถานะบัตรกำนัลถ้าเป็นแบบเปอร์เซ็นต์หรือใช้หมดแล้ว
    if ($voucher['discount_type'] === 'percent' || $remaining_after <= 0) {
        $stmt = $conn->prepare("UPDATE gift_vouchers 
                              SET status = 'used', 
                                  used_at = NOW() 
                              WHERE voucher_id = ?");
        $stmt->bind_param("i", $voucher_id);
        $stmt->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}