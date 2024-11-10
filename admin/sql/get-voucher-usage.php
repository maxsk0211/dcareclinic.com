<?php
session_start();
require_once '../../dbcon.php';
header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_GET['voucher_id'])) {
        throw new Exception('ไม่พบรหัสบัตรกำนัล');
    }

    $voucher_id = intval($_GET['voucher_id']);

    // ดึงข้อมูลบัตรกำนัลและข้อมูลลูกค้า
    $sql = "SELECT 
        v.*,
        CONCAT(u.users_fname, ' ', u.users_lname) as creator_name,
        CONCAT(c.cus_firstname, ' ', c.cus_lastname) as customer_name,
        c.cus_nickname
    FROM gift_vouchers v
    LEFT JOIN users u ON v.created_by = u.users_id
    LEFT JOIN customer c ON v.customer_id = c.cus_id
    WHERE v.voucher_id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparing voucher statement: " . $conn->error);
    }

    $stmt->bind_param("i", $voucher_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Error executing voucher statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $voucher = $result->fetch_assoc();

    if (!$voucher) {
        throw new Exception('ไม่พบข้อมูลบัตรกำนัล');
    }

    // ดึงประวัติการใช้งาน
    $history_sql = "SELECT 
        vh.*,
        CONCAT(c.cus_firstname, ' ', c.cus_lastname) as customer_name,
        c.cus_nickname,
        b.branch_name
    FROM voucher_usage_history vh
    LEFT JOIN customer c ON vh.customer_id = c.cus_id
    LEFT JOIN branch b ON vh.branch_id = b.branch_id
    WHERE vh.voucher_id = ?
    ORDER BY vh.used_at ASC";

    $history_stmt = $conn->prepare($history_sql);
    if (!$history_stmt) {
        throw new Exception("Error preparing history statement: " . $conn->error);
    }

    $history_stmt->bind_param("i", $voucher_id);
    
    if (!$history_stmt->execute()) {
        throw new Exception("Error executing history statement: " . $history_stmt->error);
    }

    $history_result = $history_stmt->get_result();
    $usage_history = [];

    while ($row = $history_result->fetch_assoc()) {
        // เพิ่มชื่อเล่นถ้ามี
        if (!empty($row['cus_nickname'])) {
            $row['customer_name'] .= ' (' . $row['cus_nickname'] . ')';
        }
        unset($row['cus_nickname']);  // ลบฟิลด์ที่ไม่จำเป็น
        $usage_history[] = $row;
    }

    // คำนวณข้อมูลสรุป
    if ($voucher['discount_type'] === 'fixed') {
        $total_used = array_sum(array_column($usage_history, 'amount_used'));
        $voucher['total_used'] = $total_used;
        
        // ตรวจสอบว่ามีการใช้งานหรือไม่
        if (count($usage_history) > 0) {
            if ($voucher['remaining_amount'] <= 0) {
                $voucher['status'] = 'used';
            } else {
                $voucher['status'] = 'partially_used';
            }
        }
    }

    // เพิ่มชื่อเล่นให้ชื่อลูกค้าในข้อมูลบัตรกำนัล
    if (!empty($voucher['cus_nickname'])) {
        $voucher['customer_name'] .= ' (' . $voucher['cus_nickname'] . ')';
    }
    unset($voucher['cus_nickname']);  // ลบฟิลด์ที่ไม่จำเป็น

    // ส่งข้อมูลกลับ
    echo json_encode([
        'success' => true,
        'data' => [
            'voucher' => $voucher,
            'usage_history' => $usage_history
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'voucher_id' => $voucher_id ?? null
        ]
    ]);
}

// ปิดการเชื่อมต่อ
if (isset($stmt)) $stmt->close();
if (isset($history_stmt)) $history_stmt->close();
if ($conn) $conn->close();
?>