<?php
require_once '../../dbcon.php';
header('Content-Type: application/json');

$dates = explode(' - ', $_GET['daterange']);
$startDate = date('Y-m-d', strtotime($dates[0]));
$endDate = date('Y-m-d', strtotime($dates[1]));

try {
    // ดึงข้อมูลรายการค้างชำระ
    $sql = "SELECT 
            oc.oc_id,
            oc.order_datetime,
            oc.order_net_total,
            c.cus_firstname,
            c.cus_lastname,
            c.cus_tel,
            GROUP_CONCAT(co.course_name SEPARATOR ', ') as items,
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
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $details = array();
    $summary = [
        'total' => 0,
        'items' => 0,
        'customers' => 0
    ];
    
    $customerIds = array(); // เก็บ ID ลูกค้าที่ไม่ซ้ำกัน
    
    while ($row = $result->fetch_assoc()) {
        $summary['total'] += $row['order_net_total'];
        $summary['items']++;
        
        if (!in_array($row['cus_id'], $customerIds)) {
            $customerIds[] = $row['cus_id'];
            $summary['customers']++;
        }
        
        $details[] = [
            'order_id' => 'ORDER-' . str_pad($row['oc_id'], 6, '0', STR_PAD_LEFT),
            'customer_name' => $row['cus_firstname'] . ' ' . $row['cus_lastname'],
            'phone' => $row['cus_tel'],
            'order_date' => $row['order_datetime'],
            'items' => $row['items'],
            'unpaid_amount' => floatval($row['order_net_total']),
            'days_overdue' => intval($row['days_overdue'])
        ];
    }
    
    echo json_encode([
        'success' => true,
        'summary' => $summary,
        'details' => $details
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
?>