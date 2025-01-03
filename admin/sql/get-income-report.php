<?php
require_once '../../dbcon.php';
header('Content-Type: application/json');

try {
    // รับค่า daterange
    $daterange = isset($_GET['daterange']) ? $_GET['daterange'] : '';
    
    // แยกวันที่เริ่มและวันที่สิ้นสุด
    $dates = explode(' - ', $daterange);
    
    // แปลงวันที่เริ่มต้น
    $startDateParts = explode('/', trim($dates[0]));
    $startDateParts[2] = (int)$startDateParts[2] - 543; // แปลง พ.ศ. เป็น ค.ศ.
    $startDate = $startDateParts[2] . '-' . $startDateParts[1] . '-' . $startDateParts[0];
    
    // แปลงวันที่สิ้นสุด
    $endDateParts = explode('/', trim($dates[1]));
    $endDateParts[2] = (int)$endDateParts[2] - 543; // แปลง พ.ศ. เป็น ค.ศ.
    $endDate = $endDateParts[2] . '-' . $endDateParts[1] . '-' . $endDateParts[0];

    // Debug log
    error_log("Converting dates:");
    error_log("Start Date: " . $dates[0] . " -> " . $startDate);
    error_log("End Date: " . $dates[1] . " -> " . $endDate);

    // 1. ดึงข้อมูลสรุปตามประเภทการชำระเงิน
    $sql_summary = "SELECT 
            COUNT(oc.oc_id) as total_transactions,
            SUM(oc.order_net_total) as total_amount,
            oc.order_payment as payment_type
        FROM order_course oc 
        WHERE DATE(oc.order_datetime) BETWEEN ? AND ?
            AND oc.order_payment != 'ยังไม่จ่ายเงิน'
            AND oc.order_payment IS NOT NULL
        GROUP BY oc.order_payment";

    // 2. ดึงรายละเอียด orders ตามประเภทการชำระเงิน
    $sql_details = "SELECT 
            oc.oc_id,
            oc.order_datetime,
            oc.order_payment,
            oc.order_net_total,
            oc.order_payment_date,
            c.cus_firstname,
            c.cus_lastname,
            c.cus_tel,
            GROUP_CONCAT(DISTINCT co.course_name SEPARATOR ', ') as courses
        FROM order_course oc
        JOIN customer c ON oc.cus_id = c.cus_id
        JOIN order_detail od ON oc.oc_id = od.oc_id
        JOIN course co ON od.course_id = co.course_id
        WHERE DATE(oc.order_datetime) BETWEEN ? AND ?
            AND oc.order_payment != 'ยังไม่จ่ายเงิน'
            AND oc.order_payment IS NOT NULL
        GROUP BY oc.oc_id
        ORDER BY oc.order_payment, oc.order_datetime DESC";

    // ดึงข้อมูลสรุป
    $stmt = $conn->prepare($sql_summary);
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $result_summary = $stmt->get_result();
    
    $details = array();
    $summary = [
        'total' => 0,
        'transactions' => 0,
        'average' => 0
    ];
    
    while ($row = $result_summary->fetch_assoc()) {
        $amount = floatval($row['total_amount']);
        $count = intval($row['total_transactions']);
        
        $summary['total'] += $amount;
        $summary['transactions'] += $count;
        
        $details[$row['payment_type']] = [
            'payment_type' => $row['payment_type'],
            'count' => $count,
            'amount' => $amount,
            'percentage' => 0,
            'orders' => [] // เพิ่มอาร์เรย์สำหรับเก็บรายการ orders
        ];
    }
    // ดึงรายละเอียด orders
    $stmt = $conn->prepare($sql_details);
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $result_details = $stmt->get_result();

    while ($row = $result_details->fetch_assoc()) {
        $payment_type = $row['order_payment'];
        if (isset($details[$payment_type])) {
            $details[$payment_type]['orders'][] = [
                'order_id' => 'ORDER-' . str_pad($row['oc_id'], 6, '0', STR_PAD_LEFT),
                'order_date' => date('d/m/Y H:i', strtotime($row['order_datetime'])),
                'payment_date' => date('d/m/Y H:i', strtotime($row['order_payment_date'])),
                'customer_name' => $row['cus_firstname'] . ' ' . $row['cus_lastname'],
                'customer_tel' => $row['cus_tel'],
                'courses' => $row['courses'],
                'amount' => floatval($row['order_net_total'])
            ];
        }
    }
    
    // คำนวณค่าเฉลี่ยและเปอร์เซ็นต์
    $summary['average'] = $summary['transactions'] > 0 ? 
        $summary['total'] / $summary['transactions'] : 0;
        
    foreach ($details as &$detail) {
        $detail['percentage'] = $summary['total'] > 0 ? 
            round(($detail['amount'] / $summary['total']) * 100, 2) : 0;
    }
    
    echo json_encode([
        'success' => true,
        'summary' => $summary,
        'details' => array_values($details)
    ]);
    
} catch (Exception $e) {
    error_log("Error in get-income-report.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
        'debug' => [
            'daterange' => $daterange ?? 'not set',
            'error' => $e->getMessage()
        ]
    ]);
}
?>