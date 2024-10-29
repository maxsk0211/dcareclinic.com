<?php
session_start();
require_once '../../dbcon.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'voucher' => null, 'history' => []];
$stmt_voucher = null;
$stmt_history = null;

try {
    if (!isset($_GET['voucher_id'])) {
        throw new Exception('ไม่พบรหัสบัตรกำนัล');
    }

    $voucher_id = intval($_GET['voucher_id']);

    if ($voucher_id <= 0) {
        throw new Exception('รหัสบัตรกำนัลไม่ถูกต้อง');
    }

    // ดึงข้อมูลบัตรกำนัล
    $sql_voucher = "SELECT gv.*,
                    COALESCE(SUM(vuh.amount_used), 0) as total_used
                    FROM gift_vouchers gv
                    LEFT JOIN voucher_usage_history vuh ON gv.voucher_id = vuh.voucher_id
                    WHERE gv.voucher_id = ?
                    GROUP BY gv.voucher_id";

    $stmt_voucher = $conn->prepare($sql_voucher);
    if ($stmt_voucher === false) {
        throw new Exception('เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL สำหรับบัตรกำนัล: ' . $conn->error);
    }

    if (!$stmt_voucher->bind_param("i", $voucher_id)) {
        throw new Exception('เกิดข้อผิดพลาดในการผูกพารามิเตอร์: ' . $stmt_voucher->error);
    }

    if (!$stmt_voucher->execute()) {
        throw new Exception('เกิดข้อผิดพลาดในการดึงข้อมูลบัตรกำนัล: ' . $stmt_voucher->error);
    }

    $result_voucher = $stmt_voucher->get_result();
    $voucher = $result_voucher->fetch_assoc();

    if (!$voucher) {
        throw new Exception('ไม่พบข้อมูลบัตรกำนัล');
    }

    // ดึงประวัติการใช้งาน
    $sql_history = "SELECT vuh.used_at, vuh.amount_used, 
                    oc.oc_id as order_id,
                    CONCAT(c.cus_firstname, ' ', c.cus_lastname) as customer_name
                    FROM voucher_usage_history vuh
                    LEFT JOIN order_course oc ON vuh.order_id = oc.oc_id
                    LEFT JOIN customer c ON oc.cus_id = c.cus_id
                    WHERE vuh.voucher_id = ?
                    ORDER BY vuh.used_at DESC";

    $stmt_history = $conn->prepare($sql_history);
    if ($stmt_history === false) {
        throw new Exception('เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL สำหรับประวัติ: ' . $conn->error);
    }

    if (!$stmt_history->bind_param("i", $voucher_id)) {
        throw new Exception('เกิดข้อผิดพลาดในการผูกพารามิเตอร์ประวัติ: ' . $stmt_history->error);
    }

    if (!$stmt_history->execute()) {
        throw new Exception('เกิดข้อผิดพลาดในการดึงประวัติ: ' . $stmt_history->error);
    }

    $result_history = $stmt_history->get_result();

    $history = [];
    while ($row = $result_history->fetch_assoc()) {
        $history[] = [
            'used_at' => $row['used_at'],
            'order_id' => $row['order_id'],
            'customer_name' => $row['customer_name'],
            'amount_used' => $row['amount_used']
        ];
    }

    $response['success'] = true;
    $response['voucher'] = $voucher;
    $response['history'] = $history;

} catch (Exception $e) {
    error_log("Voucher History Error: " . $e->getMessage());
    $response['message'] = $e->getMessage();
} finally {
    // ปิด statements
    if ($stmt_voucher && $stmt_voucher instanceof mysqli_stmt) {
        $stmt_voucher->close();
    }
    if ($stmt_history && $stmt_history instanceof mysqli_stmt) {
        $stmt_history->close();
    }
}

echo json_encode($response);
if (isset($conn)) {
    $conn->close();
}