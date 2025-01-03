<?php
session_start();
require_once '../../dbcon.php';
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'summary' => null,
    'items' => []
];

try {
    // รับค่าพารามิเตอร์จาก request
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : null;
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : null;
    $branch_id = $_SESSION['branch_id']; // ดึง branch_id จาก session

    // สร้าง WHERE clause สำหรับการกรอง
    $whereClause = "vuh.branch_id = ?";
    $params = [$branch_id];
    $types = "i";

    if ($startDate && $endDate) {
        $whereClause .= " AND DATE(vuh.used_at) BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
        $types .= "ss";
    }

    // SQL สำหรับดึงข้อมูลสรุป
    $summarySQL = "
        SELECT 
            COUNT(*) as totalUsage,
            SUM(vuh.amount_used) as totalValue
        FROM voucher_usage_history vuh
        WHERE " . $whereClause;

    $stmtSummary = $conn->prepare($summarySQL);
    if ($stmtSummary === false) {
        throw new Exception("Error preparing summary statement: " . $conn->error);
    }

    $stmtSummary->bind_param($types, ...$params);
    if (!$stmtSummary->execute()) {
        throw new Exception("Error executing summary query: " . $stmtSummary->error);
    }

    $summaryResult = $stmtSummary->get_result();
    $summary = $summaryResult->fetch_assoc();

    // SQL สำหรับดึงข้อมูลรายการ
    $itemsSQL = "
        SELECT 
            vuh.used_at,
            gv.voucher_code,
            CONCAT(c.cus_firstname, ' ', c.cus_lastname) as customer_name,
            gv.discount_type,
            gv.amount,
            gv.max_discount,
            vuh.amount_used,
            vuh.remaining_amount,
            vuh.order_id,
            b.branch_name,
            gv.status
        FROM voucher_usage_history vuh
        LEFT JOIN gift_vouchers gv ON vuh.voucher_id = gv.voucher_id
        LEFT JOIN customer c ON vuh.customer_id = c.cus_id
        LEFT JOIN branch b ON vuh.branch_id = b.branch_id
        WHERE " . $whereClause . "
        ORDER BY vuh.used_at DESC";

    $stmtItems = $conn->prepare($itemsSQL);
    if ($stmtItems === false) {
        throw new Exception("Error preparing items statement: " . $conn->error);
    }

    $stmtItems->bind_param($types, ...$params);
    if (!$stmtItems->execute()) {
        throw new Exception("Error executing items query: " . $stmtItems->error);
    }

    $itemsResult = $stmtItems->get_result();
    $items = [];

    while ($row = $itemsResult->fetch_assoc()) {
        // จัดรูปแบบข้อมูลก่อนส่งกลับ
        $row['used_at'] = date('Y-m-d H:i:s', strtotime($row['used_at']));
        $row['amount_used'] = floatval($row['amount_used']);
        $row['remaining_amount'] = floatval($row['remaining_amount']);
        
        // แปลงค่าต่างๆ เป็นตัวเลขที่เหมาะสม
        if ($row['amount']) $row['amount'] = floatval($row['amount']);
        if ($row['max_discount']) $row['max_discount'] = floatval($row['max_discount']);

        $items[] = $row;
    }

    // ปรับข้อมูลสรุปให้เป็นตัวเลขที่เหมาะสม
    $response['summary'] = [
        'totalUsage' => intval($summary['totalUsage']),
        'totalValue' => floatval($summary['totalValue'])
    ];

    $response['items'] = $items;
    $response['success'] = true;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Voucher Usage History Error: " . $e->getMessage());
} finally {
    // ปิด statements
    if (isset($stmtSummary) && $stmtSummary instanceof mysqli_stmt) {
        $stmtSummary->close();
    }
    if (isset($stmtItems) && $stmtItems instanceof mysqli_stmt) {
        $stmtItems->close();
    }
    // ปิดการเชื่อมต่อฐานข้อมูล
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}

// ส่งข้อมูลกลับ
echo json_encode($response, JSON_UNESCAPED_UNICODE);