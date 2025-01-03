<?php
require_once '../../dbcon.php';
header('Content-Type: application/json');

$dates = explode(' - ', $_GET['daterange']);
$startDate = date('Y-m-d', strtotime($dates[0]));
$endDate = date('Y-m-d', strtotime($dates[1]));

try {
    // 1. ดึงข้อมูลสรุปตามประเภทสินค้า/บริการ
    $sql = "SELECT 
            ct.course_type_name,
            COUNT(od.od_id) as total_items,
            SUM(od.od_price * od.od_amount) as total_amount
        FROM order_detail od
        JOIN order_course oc ON od.oc_id = oc.oc_id
        JOIN course c ON od.course_id = c.course_id
        JOIN course_type ct ON c.course_type_id = ct.course_type_id
        WHERE DATE(oc.order_datetime) BETWEEN ? AND ?
        GROUP BY ct.course_type_id
        ORDER BY total_amount DESC";
        
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $rankings = array();
    $summary = [
        'total' => 0,
        'items' => 0,
        'categories' => 0
    ];
    
    while ($row = $result->fetch_assoc()) {
        $summary['total'] += $row['total_amount'];
        $summary['items'] += $row['total_items'];
        $summary['categories']++;
        
        $rankings[] = [
            'category' => $row['course_type_name'],
            'quantity' => intval($row['total_items']),
            'amount' => floatval($row['total_amount']),
            'percentage' => 0 // จะคำนวณในภายหลัง
        ];
    }
    
    // 2. ดึง Top 5 สินค้า/บริการขายดี
    $sql = "SELECT 
            c.course_name,
            ct.course_type_name,
            COUNT(od.od_id) as total_sold,
            SUM(od.od_price * od.od_amount) as total_amount
        FROM order_detail od
        JOIN order_course oc ON od.oc_id = oc.oc_id
        JOIN course c ON od.course_id = c.course_id
        JOIN course_type ct ON c.course_type_id = ct.course_type_id
        WHERE DATE(oc.order_datetime) BETWEEN ? AND ?
        GROUP BY c.course_id
        ORDER BY total_amount DESC
        LIMIT 5";
        
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $topItems = array();
    while ($row = $result->fetch_assoc()) {
        $topItems[] = [
            'name' => $row['course_name'],
            'category' => $row['course_type_name'],
            'quantity' => intval($row['total_sold']),
            'amount' => floatval($row['total_amount'])
        ];
    }
    
    // คำนวณเปอร์เซ็นต์สำหรับการจัดอันดับ
    foreach ($rankings as &$rank) {
        $rank['percentage'] = $summary['total'] > 0 ? 
            ($rank['amount'] / $summary['total']) * 100 : 0;
    }
    
    echo json_encode([
        'success' => true,
        'summary' => $summary,
        'rankings' => $rankings,
        'topItems' => $topItems
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}
?>