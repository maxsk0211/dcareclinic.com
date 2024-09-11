<?php
require '../../dbcon.php';

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
$stmt_order->bind_param("i", $order_id);
$stmt_order->execute();
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
$stmt_courses->bind_param("i", $order_id);
$stmt_courses->execute();
$result_courses = $stmt_courses->get_result();

$courses = array();
while ($course = $result_courses->fetch_assoc()) {
    // ดึงข้อมูลทรัพยากรสำหรับแต่ละคอร์ส
    $sql_resources = "SELECT cr.*, 
                      CASE 
                        WHEN cr.resource_type = 'drug' THEN d.drug_name
                        WHEN cr.resource_type = 'tool' THEN t.tool_name
                        WHEN cr.resource_type = 'accessory' THEN a.acc_name
                      END AS resource_name,
                      CASE 
                        WHEN cr.resource_type = 'drug' THEN u1.unit_name
                        WHEN cr.resource_type = 'tool' THEN u2.unit_name
                        WHEN cr.resource_type = 'accessory' THEN u3.unit_name
                      END AS unit_name
                      FROM course_resources cr
                      LEFT JOIN drug d ON cr.resource_type = 'drug' AND cr.resource_id = d.drug_id
                      LEFT JOIN tool t ON cr.resource_type = 'tool' AND cr.resource_id = t.tool_id
                      LEFT JOIN accessories a ON cr.resource_type = 'accessory' AND cr.resource_id = a.acc_id
                      LEFT JOIN unit u1 ON d.drug_unit_id = u1.unit_id
                      LEFT JOIN unit u2 ON t.tool_unit_id = u2.unit_id
                      LEFT JOIN unit u3 ON a.acc_unit_id = u3.unit_id
                      WHERE cr.course_id = ?";
    
    $stmt_resources = $conn->prepare($sql_resources);
    $stmt_resources->bind_param("i", $course['course_id']);
    $stmt_resources->execute();
    $result_resources = $stmt_resources->get_result();
    
    $resources = array();
    while ($resource = $result_resources->fetch_assoc()) {
        // ตรวจสอบว่ามีข้อมูลในตาราง order_course_resources หรือไม่
        $sql_check = "SELECT * FROM order_course_resources 
                      WHERE order_id = ? AND course_id = ? AND resource_type = ? AND resource_id = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("iisi", $order_id, $course['course_id'], $resource['resource_type'], $resource['resource_id']);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows == 0) {
            // ถ้าไม่มีข้อมูล ให้เพิ่มข้อมูลลงในตาราง order_course_resources
            $sql_insert = "INSERT INTO order_course_resources (order_id, course_id, resource_type, resource_id, quantity) 
                           VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("iisid", $order_id, $course['course_id'], $resource['resource_type'], $resource['resource_id'], $resource['quantity']);
            $stmt_insert->execute();
            
            $resource['id'] = $conn->insert_id;
        } else {
            $existing_resource = $result_check->fetch_assoc();
            $resource['id'] = $existing_resource['id'];
            $resource['quantity'] = $existing_resource['quantity'];
        }
        
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
}

$order_details = array(
    'order_id' => $order['oc_id'],
    'customer_name' => $order['cus_firstname'] . ' ' . $order['cus_lastname'],
    'booking_datetime' => $order['booking_datetime'],
    'payment_status' => $order['order_payment'],
    'total_price' => $order['order_net_total'],
    'courses' => $courses
);

echo json_encode($order_details);

$stmt_order->close();
$stmt_courses->close();
$stmt_resources->close();
$conn->close();