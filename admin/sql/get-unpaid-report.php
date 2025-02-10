<?php
require_once '../../dbcon.php';
header('Content-Type: application/json');

try {
    // รับค่าช่วงวันที่
    $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d');
    $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d');

    // Debug log
    error_log("Start Date: " . $startDate);
    error_log("End Date: " . $endDate);

    // ดึงข้อมูลรายการค้างชำระ
    $sql = "SELECT 
            oc.oc_id,
            oc.order_datetime,
            oc.order_net_total as amount,
            c.cus_firstname,
            c.cus_lastname,
            c.cus_tel,
            GROUP_CONCAT(DISTINCT co.course_name SEPARATOR ', ') as courses,
            COUNT(DISTINCT od.od_id) as total_items,
            DATEDIFF(CURRENT_DATE, oc.order_datetime) as days_overdue
        FROM order_course oc
        JOIN customer c ON oc.cus_id = c.cus_id
        JOIN order_detail od ON oc.oc_id = od.oc_id
        JOIN course co ON od.course_id = co.course_id
        WHERE oc.order_payment = 'ยังไม่จ่ายเงิน'
            AND DATE(oc.order_datetime) BETWEEN ? AND ?
        GROUP BY oc.oc_id
        ORDER BY days_overdue DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param('ss', $startDate, $endDate);
    if (!$stmt->execute()) {
        throw new Exception("Error executing statement: " . $stmt->error);
    }

    $result = $stmt->get_result();

    $details = array();
    $summary = [
        'total' => 0,
        'items' => 0,
        'customers' => 0
    ];

    $customerIds = array(); // เก็บ ID ลูกค้าที่ไม่ซ้ำกัน

    // จัดกลุ่มตามระยะเวลาค้างชำระ
    $byPeriod = [
        'under30' => ['count' => 0, 'amount' => 0],
        'days30to60' => ['count' => 0, 'amount' => 0],
        'days61to90' => ['count' => 0, 'amount' => 0],
        'over90' => ['count' => 0, 'amount' => 0]
    ];

    while ($row = $result->fetch_assoc()) {
        $summary['total'] += $row['amount'];
        $summary['items']++;

        // นับจำนวนลูกค้าที่ไม่ซ้ำกัน
        $customerName = $row['cus_firstname'] . ' ' . $row['cus_lastname'];
        if (!in_array($customerName, $customerIds)) {
            $customerIds[] = $customerName;
            $summary['customers']++;
        }

        // จัดกลุ่มตามระยะเวลาค้างชำระ
        $days = intval($row['days_overdue']);
        if ($days <= 30) {
            $byPeriod['under30']['count']++;
            $byPeriod['under30']['amount'] += $row['amount'];
        } elseif ($days <= 60) {
            $byPeriod['days30to60']['count']++;
            $byPeriod['days30to60']['amount'] += $row['amount'];
        } elseif ($days <= 90) {
            $byPeriod['days61to90']['count']++;
            $byPeriod['days61to90']['amount'] += $row['amount'];
        } else {
            $byPeriod['over90']['count']++;
            $byPeriod['over90']['amount'] += $row['amount'];
        }

        // เตรียมข้อมูลสำหรับแสดงในตาราง
        $details[] = [
            'order_id' => 'ORDER-' . str_pad($row['oc_id'], 6, '0', STR_PAD_LEFT),
            'order_date' => $row['order_datetime'],
            'customer_name' => $customerName,
            'customer_tel' => $row['cus_tel'],
            'courses' => $row['courses'],
            'amount' => floatval($row['amount']),
            'days_overdue' => $days
        ];
    }

    // คำนวณค่าเฉลี่ยต่อรายการ
    $summary['average'] = $summary['items'] > 0 ? 
        $summary['total'] / $summary['items'] : 0;

    echo json_encode([
        'success' => true,
        'summary' => $summary,
        'byPeriod' => $byPeriod,
        'details' => $details,
        'debug' => [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'query' => $sql
        ]
    ]);

} catch (Exception $e) {
    error_log("Error in get-unpaid-report.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
        'debug' => [
            'startDate' => $startDate ?? 'not set',
            'endDate' => $endDate ?? 'not set',
            'error' => $e->getMessage()
        ]
    ]);
}
?>