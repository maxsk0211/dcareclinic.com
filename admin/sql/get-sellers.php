<?php
session_start();
require '../../dbcon.php';

// ตรวจสอบว่ามีการส่ง service_id มาหรือไม่
if (!isset($_GET['service_id'])) {
    echo json_encode(['error' => 'Missing service_id']);
    exit;
}

$service_id = intval($_GET['service_id']);

// ดึงข้อมูล order_course เพื่อใช้ในการคำนวณ
$sql_order = "SELECT oc.order_net_total 
              FROM service_queue sq 
              JOIN course_bookings cb ON sq.booking_id = cb.id 
              JOIN order_course oc ON cb.id = oc.course_bookings_id 
              WHERE sq.queue_id = ?";

$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $service_id);
$stmt_order->execute();
$order_result = $stmt_order->get_result();
$order_data = $order_result->fetch_assoc();

// ดึงข้อมูลผู้ขายคอร์ส
$sql = "SELECT ssr.staff_record_id, 
               ssr.staff_df,
               ssr.staff_df_type,
               u.users_fname,
               u.users_lname,
               CONCAT(u.users_fname, ' ', u.users_lname) as staff_name
        FROM service_staff_records ssr
        JOIN users u ON ssr.staff_id = u.users_id
        WHERE ssr.service_id = ? 
        AND ssr.staff_type = 'seller'  /* เฉพาะผู้ขาย */
        ORDER BY ssr.staff_record_id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

$sellers = array();
while ($row = $result->fetch_assoc()) {
    // คำนวณค่าคอมมิชชั่น
    if ($row['staff_df_type'] === 'percent' && isset($order_data['order_net_total'])) {
        $commission = ($order_data['order_net_total'] * $row['staff_df']) / 100;
    } else {
        $commission = $row['staff_df'];
    }
    
    $row['commission_amount'] = $commission;
    $sellers[] = $row;
}

echo json_encode($sellers);

$stmt->close();
$stmt_order->close();
$conn->close();