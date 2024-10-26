<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../dbcon.php';

header('Content-Type: application/json');
function translateResourceType($type) {
    switch($type) {
        case 'drug':
            return 'ยา';
        case 'tool':
            return 'เครื่องมือ';
        case 'accessory':
            return 'อุปกรณ์';
        default:
            return $type;
    }
}
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id == 0) {
    echo json_encode(array('error' => 'Invalid order ID'));
    exit;
}

// ดึงข้อมูลหลักของคำสั่งซื้อ
$sql_order = "SELECT oc.*, c.cus_firstname, c.cus_lastname, cb.booking_datetime
              FROM order_course oc
              JOIN customer c ON oc.cus_id = c.cus_id
              JOIN course_bookings cb ON oc.course_bookings_id = cb.id
              WHERE oc.oc_id = ?";

$stmt_order = $conn->prepare($sql_order);
if (!$stmt_order) {
    echo json_encode(array('error' => 'Prepare failed: ' . $conn->error));
    exit;
}

$stmt_order->bind_param("i", $order_id);
if (!$stmt_order->execute()) {
    echo json_encode(array('error' => 'Execute failed: ' . $stmt_order->error));
    exit;
}

$result_order = $stmt_order->get_result();
$order = $result_order->fetch_assoc();

if (!$order) {
    echo json_encode(array('error' => 'Order not found'));
    exit;
}

// ดึงข้อมูลรายละเอียดคอร์สในคำสั่งซื้อ
$sql_courses = "SELECT od.*, c.course_name, c.course_amount, od.detail as course_detail,
                COALESCE(cu.used_sessions, 0) as used_sessions
                FROM order_detail od
                JOIN course c ON od.course_id = c.course_id
                LEFT JOIN (
                    SELECT order_detail_id, COUNT(*) as used_sessions
                    FROM course_usage
                    GROUP BY order_detail_id
                ) cu ON od.od_id = cu.order_detail_id
                WHERE od.oc_id = ?";

$stmt_courses = $conn->prepare($sql_courses);
if (!$stmt_courses) {
    echo json_encode(array('error' => 'Prepare failed: ' . $conn->error));
    exit;
}

$stmt_courses->bind_param("i", $order_id);
if (!$stmt_courses->execute()) {
    echo json_encode(array('error' => 'Execute failed: ' . $stmt_courses->error));
    exit;
}

$result_courses = $stmt_courses->get_result();

$courses = array();
while ($course = $result_courses->fetch_assoc()) {
    // ดึงข้อมูลทรัพยากรสำหรับแต่ละคอร์ส
    $sql_resources = "SELECT ocr.*, 
                      CASE 
                        WHEN ocr.resource_type = 'drug' THEN d.drug_name
                        WHEN ocr.resource_type = 'tool' THEN t.tool_name
                        WHEN ocr.resource_type = 'accessory' THEN a.acc_name
                      END AS resource_name,
                      CASE 
                        WHEN ocr.resource_type = 'drug' THEN u1.unit_name
                        WHEN ocr.resource_type = 'tool' THEN u2.unit_name
                        WHEN ocr.resource_type = 'accessory' THEN u3.unit_name
                      END AS unit_name
                      FROM order_course_resources ocr
                      LEFT JOIN drug d ON ocr.resource_type = 'drug' AND ocr.resource_id = d.drug_id
                      LEFT JOIN tool t ON ocr.resource_type = 'tool' AND ocr.resource_id = t.tool_id
                      LEFT JOIN accessories a ON ocr.resource_type = 'accessory' AND ocr.resource_id = a.acc_id
                      LEFT JOIN unit u1 ON d.drug_unit_id = u1.unit_id
                      LEFT JOIN unit u2 ON t.tool_unit_id = u2.unit_id
                      LEFT JOIN unit u3 ON a.acc_unit_id = u3.unit_id
                      WHERE ocr.order_id = ? AND ocr.course_id = ?";
    
    $stmt_resources = $conn->prepare($sql_resources);
    if (!$stmt_resources) {
        echo json_encode(array('error' => 'Prepare failed: ' . $conn->error));
        exit;
    }

    $stmt_resources->bind_param("ii", $order_id, $course['course_id']);
    if (!$stmt_resources->execute()) {
        echo json_encode(array('error' => 'Execute failed: ' . $stmt_resources->error));
        exit;
    }

    $result_resources = $stmt_resources->get_result();
    
    $resources = array();
    while ($resource = $result_resources->fetch_assoc()) {
        $resources[] = array(
            'id' => $resource['id'],
            'type' => translateResourceType($resource['resource_type']),
            'name' => $resource['resource_name'],
            'quantity' => $resource['quantity'],
            'unit' => $resource['unit_name']
        );
    }
    
    $courses[] = array(
        'id' => $course['course_id'],
        'name' => $course['course_name'],
        'amount' => $course['od_amount'],
        'price' => $course['od_price'],
        'resources' => $resources,
        'used_sessions' => intval($course['used_sessions']),
        'course_amount' => intval($course['course_amount']),
        'detail' => $course['course_detail'] // เพิ่มบรรทัดนี้
    );

    $stmt_resources->close();
}

$order_details = array(
    'order_id' => $order['oc_id'],
    'customer_name' => $order['cus_firstname'] . ' ' . $order['cus_lastname'],
    'booking_datetime' => $order['booking_datetime'],
    'payment_status' => $order['order_payment'],
    'total_price' => $order['order_net_total'],
    'courses' => $courses
);

ob_clean();
echo json_encode($order_details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$stmt_order->close();
$stmt_courses->close();
$conn->close();