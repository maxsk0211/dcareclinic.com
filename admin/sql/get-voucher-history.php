<?php
session_start();
require_once '../../dbcon.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'history' => []];
$stmt = null;

try {
    if (!isset($_GET['cus_id'])) {
        throw new Exception('ไม่พบรหัสลูกค้า');
    }

    $cus_id = intval($_GET['cus_id']);
    if ($cus_id <= 0) {
        throw new Exception('รหัสลูกค้าไม่ถูกต้อง');
    }

    // ดึงประวัติบัตรกำนัลทั้งหมดของลูกค้า
    $sql = "SELECT 
                gv.voucher_id,
                gv.voucher_code,
                gv.amount,
                gv.max_discount,
                gv.discount_type,
                gv.expire_date,
                gv.status,
                gv.first_used_at,
                gv.remaining_amount,
                COALESCE(SUM(vuh.amount_used), 0) as total_used_amount
            FROM gift_vouchers gv
            LEFT JOIN voucher_usage_history vuh ON gv.voucher_id = vuh.voucher_id
            WHERE gv.customer_id = ? OR gv.voucher_id IN (
                SELECT DISTINCT voucher_id 
                FROM voucher_usage_history 
                WHERE customer_id = ?
            )
            GROUP BY gv.voucher_id
            ORDER BY 
                CASE 
                    WHEN gv.status = 'unused' THEN 1
                    WHEN gv.status = 'used' THEN 2
                    ELSE 3
                END,
                gv.first_used_at DESC,
                gv.expire_date DESC";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception('เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: ' . $conn->error);
    }

    if (!$stmt->bind_param("ii", $cus_id, $cus_id)) {
        throw new Exception('เกิดข้อผิดพลาดในการผูกพารามิเตอร์: ' . $stmt->error);
    }

    if (!$stmt->execute()) {
        throw new Exception('เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $history = [];
    
    while ($row = $result->fetch_assoc()) {
        // คำนวณข้อมูลเพิ่มเติม
        if ($row['discount_type'] === 'fixed') {
            $row['remaining_amount'] = $row['amount'] - $row['total_used_amount'];
        }
        
        // แปลงวันที่เป็นรูปแบบที่ต้องการ
        if ($row['first_used_at']) {
            $first_used = new DateTime($row['first_used_at']);
            $row['first_used_at'] = $first_used->format('Y-m-d H:i:s');
        }
        
        $expire = new DateTime($row['expire_date']);
        $row['expire_date'] = $expire->format('Y-m-d');

        // เพิ่มข้อมูลเข้า array
        $history[] = $row;
    }

    $response['success'] = true;
    $response['history'] = $history;

} catch (Exception $e) {
    error_log("Voucher History Error: " . $e->getMessage());
    $response['message'] = $e->getMessage();
} finally {
    if ($stmt && $stmt instanceof mysqli_stmt) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}

echo json_encode($response);