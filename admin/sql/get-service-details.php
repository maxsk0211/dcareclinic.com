<?php
session_start();
require '../../dbcon.php';

// เพิ่ม error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

// Log incoming parameters
error_log("Received parameters: " . print_r($_GET, true));

// รับพารามิเตอร์และ validate
$staff_id = isset($_GET['staff_id']) ? intval($_GET['staff_id']) : 0;
$staff_type = isset($_GET['staff_type']) ? $_GET['staff_type'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Log validated parameters
error_log("Validated parameters: staff_id=$staff_id, staff_type=$staff_type, start_date=$start_date, end_date=$end_date");

// ตรวจสอบความถูกต้องของข้อมูล
if (!$staff_id || !$staff_type || !$start_date || !$end_date) {
    $error_message = "Missing parameters: " . 
                    (!$staff_id ? "staff_id " : "") . 
                    (!$staff_type ? "staff_type " : "") . 
                    (!$start_date ? "start_date " : "") . 
                    (!$end_date ? "end_date" : "");
    error_log($error_message);
    echo json_encode([
        'error' => true,
        'message' => 'ข้อมูลไม่ครบถ้วน: ' . $error_message,
        'details' => []
    ]);
    exit;
}

try {
    // เตรียม SQL query ตามประเภทของบุคลากร
    if ($staff_type === 'seller') {
        $sql = "SELECT 
                    DATE(oc.order_datetime) as order_date,
                    CONCAT('ORDER-', LPAD(oc.oc_id, 6, '0')) as order_number,
                    CONCAT(c.cus_firstname, ' ', c.cus_lastname) as customer_name,
                    oc.order_net_total as order_total,
                    CASE 
                        WHEN ssr.staff_df_type = 'amount' THEN ssr.staff_df
                        WHEN ssr.staff_df_type = 'percent' THEN (oc.order_net_total * ssr.staff_df / 100)
                        ELSE 0
                    END as fee_amount,
                    ssr.staff_df as original_fee,
                    ssr.staff_df_type as fee_type
                FROM service_staff_records ssr
                JOIN service_queue sq ON ssr.service_id = sq.queue_id
                JOIN order_course oc ON sq.booking_id = oc.course_bookings_id
                JOIN customer c ON oc.cus_id = c.cus_id
                WHERE ssr.staff_id = ?
                AND ssr.staff_type = 'seller'
                AND oc.order_payment != 'ยังไม่จ่ายเงิน'
                AND DATE(oc.order_datetime) BETWEEN ? AND ?
                ORDER BY oc.order_datetime DESC";
        
        error_log("Using seller query");
        $params = [$staff_id, $start_date, $end_date];
        $types = "iss";
    } else {
        $sql = "SELECT 
                    DATE(oc.order_datetime) as order_date,
                    CONCAT('ORDER-', LPAD(oc.oc_id, 6, '0')) as order_number,
                    CONCAT(c.cus_firstname, ' ', c.cus_lastname) as customer_name,
                    oc.order_net_total as order_total,
                    CASE 
                        WHEN ssr.staff_df_type = 'amount' THEN ssr.staff_df
                        WHEN ssr.staff_df_type = 'percent' THEN (oc.order_net_total * ssr.staff_df / 100)
                        ELSE 0
                    END as fee_amount,
                    ssr.staff_df as original_fee,
                    ssr.staff_df_type as fee_type
                FROM service_staff_records ssr
                JOIN service_queue sq ON ssr.service_id = sq.queue_id
                JOIN order_course oc ON sq.booking_id = oc.course_bookings_id
                JOIN customer c ON oc.cus_id = c.cus_id
                WHERE ssr.staff_id = ?
                AND ssr.staff_type = ?
                AND oc.order_payment != 'ยังไม่จ่ายเงิน'
                AND DATE(oc.order_datetime) BETWEEN ? AND ?
                ORDER BY oc.order_datetime DESC";
        
        error_log("Using DF query");
        $params = [$staff_id, $staff_type, $start_date, $end_date];
        $types = "isss";
    }

    // Log the SQL and parameters
    error_log("SQL Query: " . $sql);
    error_log("Parameters: " . print_r($params, true));

    // Prepare and execute
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Get result failed: " . $stmt->error);
    }

    // Process results
    $details = [];
    $total_sales = 0;
    $total_fee = 0;

    while ($row = $result->fetch_assoc()) {
        error_log("Processing row: " . print_r($row, true));
        
        // คำนวณยอดรวม
        $total_sales += $row['order_total'];
        $total_fee += $row['fee_amount'];

        // เพิ่มข้อมูลเพิ่มเติมสำหรับการแสดงผล
        $row['fee_details'] = $row['fee_type'] === 'percent' 
            ? sprintf("%.2f%% (%.2f บาท)", $row['original_fee'], $row['fee_amount'])
            : sprintf("%.2f บาท", $row['fee_amount']);

        $details[] = $row;
    }

    $response = [
        'error' => false,
        'details' => $details,
        'summary' => [
            'total_sales' => $total_sales,
            'total_fee' => $total_fee,
            'total_records' => count($details)
        ]
    ];

    error_log("Sending response: " . print_r($response, true));
    echo json_encode($response);

} catch (Exception $e) {
    error_log("Error occurred: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    echo json_encode([
        'error' => true,
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage(),
        'details' => [],
        'debug_info' => [
            'error_message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}

// Cleanup
if (isset($stmt)) {
    $stmt->close();
}
if (isset($conn)) {
    $conn->close();
}