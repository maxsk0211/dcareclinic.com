<?php
require '../../dbcon.php';

header('Content-Type: application/json');

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
    error_log("Prepare failed: " . $conn->error);
    echo json_encode(array('error' => 'Database error'));
    exit;
}

$stmt_order->bind_param("i", $order_id);
if (!$stmt_order->execute()) {
    error_log("Execute failed: " . $stmt_order->error);
    echo json_encode(array('error' => 'Database error'));
    exit;
}

$result_order = $stmt_order->get_result();
$order = $result_order->fetch_assoc();

if (!$order) {
    echo json_encode(array('error' => 'Order not found'));
    exit;
}

// ดึงข้อมูลรายละเอียดคอร์สในคำสั่งซื้อ
$sql_courses = "SELECT od.*, c.course_name
                FROM order_detail od
                JOIN course c ON od.course_id = c.course_id
                WHERE od.oc_id = ?";

$stmt_courses = $conn->prepare($sql_courses);
if (!$stmt_courses) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode(array('error' => 'Database error'));
    exit;
}

$stmt_courses->bind_param("i", $order_id);
if (!$stmt_courses->execute()) {
    error_log("Execute failed: " . $stmt_courses->error);
    echo json_encode(array('error' => 'Database error'));
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
        error_log("Prepare failed: " . $conn->error);
        continue; // Skip this course if prepare fails
    }

    $stmt_resources->bind_param("ii", $order_id, $course['course_id']);
    if (!$stmt_resources->execute()) {
        error_log("Execute failed: " . $stmt_resources->error);
        continue; // Skip this course if execute fails
    }

    $result_resources = $stmt_resources->get_result();
    
    $resources = array();
    while ($resource = $result_resources->fetch_assoc()) {
        $resources[] = array(
            'id' => $resource['id'],
            'type' => $resource['resource_type'],
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
        'resources' => $resources
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
// ก่อน echo json_encode($order_details);
error_log("Order details: " . print_r($order_details, true));
echo json_encode($order_details);

$stmt_order->close();
$stmt_courses->close();
$conn->close();

header('Content-Type: application/json');
$json_response = json_encode($order_details, JSON_PRETTY_PRINT);
if ($json_response === false) {
    // JSON encoding failed
    error_log("JSON encode error: " . json_last_error_msg());
    echo json_encode(array("error" => "Internal Server Error"));
} else {
    echo $json_response;
}
exit;