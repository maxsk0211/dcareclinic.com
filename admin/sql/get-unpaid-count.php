<?php
require_once '../../dbcon.php';
header('Content-Type: application/json');

try {
    // รับค่าวันที่และแปลงจาก พ.ศ. เป็น ค.ศ.
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d');
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d');

    if (strpos($startDate, '/') !== false) {
        $startDateArr = explode('/', $startDate);
        $endDateArr = explode('/', $endDate);
        
        $startDateArr[2] = intval($startDateArr[2]) - 543;
        $endDateArr[2] = intval($endDateArr[2]) - 543;
        
        $startDate = $startDateArr[2] . '-' . $startDateArr[1] . '-' . $startDateArr[0];
        $endDate = $endDateArr[2] . '-' . $endDateArr[1] . '-' . $endDateArr[0];
    }

    // Query ใหม่
    $sql = "SELECT COUNT(DISTINCT oc.oc_id) as unpaid_count
            FROM order_course oc 
            WHERE (oc.order_payment = 'ยังไม่จ่ายเงิน' OR oc.order_payment IS NULL)
            AND (oc.order_status IS NULL OR oc.order_status != 'cancelled')
            AND DATE(oc.order_datetime) BETWEEN ? AND ?";

    // เพิ่ม Query ตรวจสอบ
    $checkSql = "SELECT oc.oc_id, oc.order_payment, oc.order_status, oc.order_datetime
                 FROM order_course oc 
                 WHERE (oc.order_payment = 'ยังไม่จ่ายเงิน' OR oc.order_payment IS NULL)
                 AND (oc.order_status IS NULL OR oc.order_status != 'cancelled')
                 AND DATE(oc.order_datetime) BETWEEN ? AND ?";

    // ดึงข้อมูลจำนวนรายการ
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = intval($row['unpaid_count']);

    // ดึงข้อมูลรายละเอียดเพื่อตรวจสอบ
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param('ss', $startDate, $endDate);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $details = [];
    while ($row = $checkResult->fetch_assoc()) {
        $details[] = $row;
    }

    error_log("Unpaid count query result: " . $count);
    error_log("Date range: $startDate to $endDate");

    echo json_encode([
        'success' => true,
        'count' => $count,
        'debug' => [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'sql' => $sql,
            'originalDates' => [
                'start' => $_GET['startDate'],
                'end' => $_GET['endDate']
            ],
            'detailedResults' => $details // เพิ่มรายละเอียดเพื่อตรวจสอบ
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in get-unpaid-count.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
        'debug' => [
            'error' => $e->getMessage()
        ]
    ]);
}
?>