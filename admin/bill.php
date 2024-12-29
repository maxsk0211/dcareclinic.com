<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';
require_once 'check_permission.php';  // เพิ่มการเรียกใช้ไฟล์ check_permission.php
// เพิ่ม error reporting เพื่อ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ฟังก์ชันช่วยเหลือ
function thai_date($date) {
    $months = array(
        1=>'มกราคม', 2=>'กุมภาพันธ์', 3=>'มีนาคม', 4=>'เมษายน', 
        5=>'พฤษภาคม', 6=>'มิถุนายน', 7=>'กรกฎาคม', 8=>'สิงหาคม', 
        9=>'กันยายน', 10=>'ตุลาคม', 11=>'พฤศจิกายน', 12=>'ธันวาคม'
    );
    $timestamp = strtotime($date);
    return date('d', $timestamp).' '.$months[date('n', $timestamp)].' '.(date('Y', $timestamp) + 543);
}

function format_money($amount) {
    return number_format($amount, 2, '.', ',');
}

function formatOrderId($orderId) {
    return 'ORDER-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
}

function format_datetime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}

// ฟังก์ชันจัดการการใช้บริการ
function getUsageDetails($order_id, $course_id) {
    global $conn;
    try {
        $sql = "SELECT od.od_id, od.used_sessions, od.od_amount, c.course_amount,
                (SELECT COUNT(*) FROM course_usage cu WHERE cu.order_detail_id = od.od_id) as actual_used
                FROM order_detail od
                JOIN course c ON od.course_id = c.course_id
                WHERE od.oc_id = ? AND od.course_id = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $order_id, $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if (!$data) {
            return [
                'used_sessions' => 0,
                'total_sessions' => 0,
                'actual_used' => 0,
                'od_id' => null
            ];
        }
        
        return [
            'used_sessions' => (int)$data['used_sessions'],
            'total_sessions' => (int)$data['course_amount'] * (int)$data['od_amount'],
            'actual_used' => (int)$data['actual_used'],
            'od_id' => $data['od_id']
        ];
    } catch (Exception $e) {
        error_log("Error in getUsageDetails: " . $e->getMessage());
        return [
            'used_sessions' => 0,
            'total_sessions' => 0,
            'actual_used' => 0,
            'od_id' => null
        ];
    }
}

function getUsageHistory($order_id, $course_id) {
    global $conn;
    try {
        $sql = "SELECT cu.usage_date, cu.notes
                FROM course_usage cu
                JOIN order_detail od ON cu.order_detail_id = od.od_id
                WHERE od.oc_id = ? AND od.course_id = ?
                ORDER BY cu.usage_date DESC";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $order_id, $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $history = [];
        while ($row = $result->fetch_assoc()) {
            $history[] = [
                'date' => thai_date($row['usage_date']),
                'notes' => $row['notes']
            ];
        }
        
        return $history;
    } catch (Exception $e) {
        error_log("Error in getUsageHistory: " . $e->getMessage());
        return [];
    }
}

// รับค่า order id
$oc_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($oc_id == 0) {
    die("ไม่พบข้อมูลคำสั่งซื้อ");
}

// ดึงข้อมูลคำสั่งซื้อและลูกค้า
$sql = "SELECT oc.*, c.*, cb.booking_datetime 
        FROM order_course oc
        JOIN customer c ON oc.cus_id = c.cus_id
        JOIN course_bookings cb ON oc.course_bookings_id = cb.id
        WHERE oc.oc_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Failed to prepare statement: " . $conn->error);
}

$stmt->bind_param("i", $oc_id);
if (!$stmt->execute()) {
    die("Failed to execute statement: " . $stmt->error);
}

$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("ไม่พบข้อมูลคำสั่งซื้อ");
}

$customer_data = $result->fetch_assoc();

// ดึงข้อมูล order
$sql_order = "SELECT oc.*, 
              CONCAT(u.users_fname, ' ', u.users_lname) as seller_name,
              u.users_fname, u.users_lname
              FROM order_course oc
              LEFT JOIN users u ON oc.seller_id = u.users_id
              WHERE oc.oc_id = ?";

$stmt_order = $conn->prepare($sql_order);
if (!$stmt_order) {
    die("Failed to prepare order statement: " . $conn->error);
}

$stmt_order->bind_param("i", $oc_id);
if (!$stmt_order->execute()) {
    die("Failed to execute order statement: " . $stmt_order->error);
}

$result_order = $stmt_order->get_result();
$order_data = $result_order->fetch_assoc();


// ดึงข้อมูลรายการคอร์ส
$sql_items = "SELECT od.*, c.course_name, c.course_amount
              FROM order_detail od
              JOIN course c ON od.course_id = c.course_id
              WHERE od.oc_id = ?";

$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $oc_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();

// ดึงข้อมูล booking
if (isset($order_data['course_bookings_id'])) {
    $sql_booking = "SELECT cb.booking_datetime 
                    FROM course_bookings cb
                    WHERE cb.id = ?";
    $stmt_booking = $conn->prepare($sql_booking);
    if ($stmt_booking) {
        $stmt_booking->bind_param("i", $order_data['course_bookings_id']);
        $stmt_booking->execute();
        $booking_result = $stmt_booking->get_result();
        $booking_data = $booking_result->fetch_assoc();
        $stmt_booking->close();
    }
}

// ดึงข้อมูล queue
if (isset($order_data['course_bookings_id'])) {
    $sql_queue = "SELECT sq.queue_date, sq.queue_time, sq.queue_id
                  FROM service_queue sq
                  WHERE sq.booking_id = ?";
    $stmt_queue = $conn->prepare($sql_queue);
    if ($stmt_queue) {
        $stmt_queue->bind_param("i", $order_data['course_bookings_id']);
        if ($stmt_queue->execute()) {
            $queue_result = $stmt_queue->get_result();
            $queue_data = $queue_result->fetch_assoc();
        }
        $stmt_queue->close();
    }
}

// เพิ่มการตรวจสอบว่ามีข้อมูลคิวหรือไม่
$hasQueue = isset($queue_data) && !empty($queue_data['queue_id']);

// ดึงประวัติการใช้บริการของลูกค้า
$sql_services = "SELECT oc.oc_id, oc.order_datetime, oc.order_net_total, oc.order_payment, 
                GROUP_CONCAT(CONCAT(c.course_name, ' (', od.od_amount, ')') SEPARATOR ', ') as courses
                FROM order_course oc
                JOIN order_detail od ON oc.oc_id = od.oc_id
                JOIN course c ON od.course_id = c.course_id
                WHERE oc.cus_id = ?
                GROUP BY oc.oc_id
                ORDER BY oc.order_datetime DESC";

$stmt_services = $conn->prepare($sql_services);
$stmt_services->bind_param("i", $customer_data['cus_id']);
$stmt_services->execute();
$result_services = $stmt_services->get_result();

// คำนวณยอดรวมและสถิติ
$total_amount = 0;
$total_services = 0;
$total_used = 0;

$items_result->data_seek(0);
while ($item = $items_result->fetch_assoc()) {
    $total_amount += $item['od_amount'] * $item['od_price'];
    $usage_data = getUsageDetails($oc_id, $item['course_id']);
    $total_services += $usage_data['total_sessions'];
    $total_used += $usage_data['actual_used'];
}

// คำนวณอายุ
$birthDate = new DateTime($customer_data['cus_birthday']);
$today = new DateTime();
$age = $today->diff($birthDate);

// ปิด statements
$stmt->close();
$stmt_order->close();
$stmt_items->close();
$stmt_services->close();

// เพิ่มหลังจากดึงข้อมูลลูกค้าและก่อนปิด connection
$sql_vouchers = "SELECT gv.*, 
                 COALESCE(SUM(vuh.amount_used), 0) as total_used_amount
                 FROM gift_vouchers gv
                 LEFT JOIN voucher_usage_history vuh ON gv.voucher_id = vuh.voucher_id
                 WHERE gv.customer_id = ? 
                 AND gv.status = 'unused'  -- เพิ่มเงื่อนไขสถานะ
                 AND gv.expire_date >= CURRENT_DATE()  -- ตรวจสอบวันหมดอายุ
                 GROUP BY gv.voucher_id";

$stmt_vouchers = $conn->prepare($sql_vouchers);
$stmt_vouchers->bind_param("i", $customer_data['cus_id']);
$stmt_vouchers->execute();
$result_vouchers = $stmt_vouchers->get_result();

// เพิ่มหลังจาก query อื่นๆ
$sql_voucher_usage = "SELECT vuh.*, gv.voucher_code, gv.discount_type 
                      FROM voucher_usage_history vuh
                      JOIN gift_vouchers gv ON vuh.voucher_id = gv.voucher_id
                      WHERE vuh.order_id = ?";
$stmt_voucher_usage = $conn->prepare($sql_voucher_usage);
$stmt_voucher_usage->bind_param("i", $oc_id);
$stmt_voucher_usage->execute();
$voucher_usage_result = $stmt_voucher_usage->get_result();

// คำนวณยอดรวมส่วนลดจากบัตรกำนัล
$total_voucher_discount = 0;
while ($usage = $voucher_usage_result->fetch_assoc()) {
    $total_voucher_discount += $usage['amount_used'];
}

// ไม่ต้องปิด $conn ที่นี่เพราะอาจจะยังต้องใช้ในส่วนอื่นของหน้า
function canRecordUsage($payment_status) {
    return $payment_status !== 'ยังไม่จ่ายเงิน';
}

// อัพเดทโค้ดส่วนที่เกี่ยวข้องกับการตรวจสอบสถานะ
$isPaymentCompleted = ($order_data['order_payment'] !== 'ยังไม่จ่ายเงิน');
// $canCancelDeposit = ($_SESSION['position_id'] == 1 || $_SESSION['position_id'] == 2);
$canRecordServiceUsage = canRecordUsage($order_data['order_payment']);

// ในส่วนของการแสดงผลการใช้บริการ
$items_result->data_seek(0); // รีเซ็ตตำแหน่งของ result set


// เพิ่มที่ด้านบนของไฟล์ หลัง require dbcon.php
$deposit_add = hasSpecificPermission('deposit_add');
$deposit_cancel = hasSpecificPermission('deposit_cancel');
$payment_add = hasSpecificPermission('payment_add');
$payment_cancel = hasSpecificPermission('payment_cancel');
$service_history_add = hasSpecificPermission('service_history_add');
$service_history_cancel = hasSpecificPermission('service_history_cancel');

// Debug: แสดงค่าสิทธิ์
error_log("Permissions for user " . $_SESSION['users_id'] . ":");
error_log("deposit_add: " . var_export($deposit_add, true));
error_log("deposit_cancel: " . var_export($deposit_cancel, true));
error_log("payment_add: " . var_export($payment_add, true));
error_log("payment_cancel: " . var_export($payment_cancel, true));
error_log("service_history_add: " . var_export($service_history_add, true));
error_log("service_history_cancel: " . var_export($service_history_cancel, true));

// ตรวจสอบว่ามีการกำหนดค่าสิทธิ์ในฐานข้อมูลหรือไม่
$checkPermissionsSql = "SELECT p.action, p.permission_name 
                       FROM permissions p 
                       WHERE p.action IN ('deposit_add', 'deposit_cancel', 
                                        'payment_add', 'payment_cancel',
                                        'service_history_add', 'service_history_cancel')";
$permissionsResult = $conn->query($checkPermissionsSql);
if ($permissionsResult) {
    while ($row = $permissionsResult->fetch_assoc()) {
        error_log("Found permission in DB: " . $row['action'] . " - " . $row['permission_name']);
    }
} else {
    error_log("Error checking permissions in DB: " . $conn->error);
}

?>
<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="horizontal-menu-template-no-customizer-starter"
  data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>จัดการคำสั่งซื้อ - D Care Clinic</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/sweetalert2/sweetalert2.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
        <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
     <style>
    body {
        background-color: #f8f9fc;
        font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

/*    .container-xxl {
        padding: 20px;
    }*/

    .card {
/*        border: none;*/
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }

    .card-header {
        background-color: #4e73df;
        color: white;
        font-weight: bold;
        border-bottom: 1px solid #e3e6f0;
        padding: 0.75rem 1.25rem;
    }

    .card-body {
        padding: 1.25rem;
    }

    .customer-info {
        background-color: #ffffff;
    }

    .customer-info .avatar {
        width: 100px;
        height: 100px;
        background-color: #4e73df;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #ffffff;
        margin-bottom: 1rem;
    }

    .customer-info .info-list {
        list-style-type: none;
        padding-left: 0;
    }

    .customer-info .info-list li {
        margin-bottom: 0.5rem;
        display: flex;
    }

    .customer-info .info-list .label {
        font-weight: 600;
        width: 150px;
        color: #4e73df;
    }

    .order-details, .payment-section {
        background-color: #ffffff;
    }

    .order-details table, .payment-section table {
        width: 100%;
        margin-bottom: 1rem;
    }

    .order-details th, .payment-section th {
        background-color: #f8f9fc;
        color: #4e73df;
        font-weight: 600;
    }

    .order-details td, .order-details th,
    .payment-section td, .payment-section th {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #e3e6f0;
    }

    .badge {
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.35em 0.65em;
        border-radius: 0.35rem;
    }

    .payment-summary .summary-box {
        background-color: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        padding: 15px;
        margin-bottom: 20px;
    }

    .payment-summary .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 16px;
    }

    .payment-summary .summary-item .label {
        font-weight: 600;
        color: #4e73df;
    }

    .payment-summary .summary-item .value {
        font-weight: 700;
    }

    .payment-summary .summary-item.total {
        border-top: 2px solid #e3e6f0;
        padding-top: 10px;
        margin-top: 10px;
        font-size: 18px;
    }

    .payment-summary .summary-item.total .label,
    .payment-summary .summary-item.total .value {
        color: #e74a3b;
        font-weight: 700;
    }

    .form-label {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border: 1px solid #d1d3e2;
        border-radius: 0.35rem;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        line-height: 1.5;
    }

    .form-control:focus, .form-select:focus {
        border-color: #bac8f3;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .btn {
        font-weight: 600;
        border-radius: 0.35rem;
        padding: 0.375rem 0.75rem;
    }

    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
    }

    .side-panel {
        background-color: #ffffff;
        border-radius: 0.35rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .side-panel h5 {
        color: #4e73df;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .warning-box {
        background-color: #fff3cd;
        border: 1px solid #ffeeba;
        color: #856404;
        padding: 0.75rem;
        border-radius: 0.35rem;
        margin-top: 1rem;
    }

    @media (max-width: 768px) {
        .customer-info .info-list .label {
            width: 120px;
        }
    }
        .card {
/*        border: none;*/
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }

    .card-header {
        background-color: #4e73df;
        color: white;
        font-weight: bold;
        border-bottom: 1px solid #e3e6f0;
        padding: 0.75rem 1.25rem;
    }

    .card-body {
        padding: 1.25rem;
    }

    .avatar {
        width: 60px;
        height: 60px;
        background-color: #4e73df;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #ffffff;
    }

    .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .info-list {
        list-style-type: none;
        padding-left: 0;
        margin-bottom: 0;
    }

    .info-list li {
        margin-bottom: 0.5rem;
        display: flex;
        flex-wrap: wrap;
    }

    .info-list .label {
        font-weight: 600;
        width: 150px;
        color: #4e73df;
    }

    .info-list .value {
        flex: 1;
    }

    @media (max-width: 768px) {
        .info-list .label {
            width: 100%;
        }
        .info-list .value {
            width: 100%;
            padding-left: 1rem;
        }
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    .deposit-info .card-header {
        background-color: #4e73df;
    }
    .deposit-info .badge {
        font-size: 0.8rem;
        padding: 0.4em 0.8em;
    }
    .deposit-info .form-label {
        font-weight: 600;
        color: #4e73df;
    }
    .deposit-info .form-control:read-only,
    .deposit-info .form-select:disabled {
        background-color: #f8f9fc;
        opacity: 1;
    }
    .deposit-info .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .table-responsive {
        overflow-x: auto;
    }
    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
    }
    .table th,
    .table td {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }
    .table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
    }
    .table tbody + tbody {
        border-top: 2px solid #dee2e6;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.075);
    }
    .clickable-row {
        cursor: pointer;
    }
    .clickable-row:hover {
        background-color: #f8f9fa;
    }
    .follow-up-table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
    }
    .follow-up-table th,
    .follow-up-table td {
        padding: 0.75rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }
    .follow-up-table thead th {
        vertical-align: bottom;
        border-bottom: 2px solid #dee2e6;
    }
    .follow-up-table tbody + tbody {
        border-top: 2px solid #dee2e6;
    }
    .badge {
        display: inline-block;
        padding: 0.25em 0.4em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }
    .bg-success {
        background-color: #28a745;
    }
    .bg-danger {
        background-color: #dc3545;
    }
    .bg-info {
        background-color: #17a2b8;
    }
    .bg-secondary {
        background-color: #6c757d;
    }
    .progress {
        background-color: #e9ecef;
        border-radius: 0.5rem;
    }
    .progress-bar {
        border-radius: 0.5rem;
        transition: width 0.3s ease;
    }
    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.2rem;
    }
    .usage-history-list {
        max-height: 400px;
        overflow-y: auto;
        padding: 10px;
    }

    .usage-history-item {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
    }

    .usage-history-item:last-child {
        margin-bottom: 0;
    }

    .swal2-popup input[type="number"] {
        text-align: center;
        font-size: 1.1em;
    }

    .alert {
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 15px;
    }

    .alert-info {
        background-color: #e8f4f8;
        border-color: #bee5eb;
        color: #0c5460;
    }

    .usage-history-popup {
        max-width: 600px;
    }

    .swal2-popup .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .swal2-popup input[type="number"],
    .swal2-popup input[type="time"] {
        text-align: center;
        font-weight: bold;
    }
    /* สไตล์สำหรับ input วันที่ภาษาไทย */
    input[type="date"]:before {
        content: attr(data-date);
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: white;
        padding: 0.5rem;
        pointer-events: none;
    }
    input[type="date"]:focus:before,
    input[type="date"]:valid:before {
        display: none;
    }
    .modal-lg {
        max-width: 800px;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .btn-sm i {
        font-size: 1rem;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.075);
    }
    /* เพิ่มต่อจาก CSS เดิม */
.summary-section {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}

.section-title {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: #4e73df;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    padding: 5px 0;
}

.summary-item:last-child {
    margin-bottom: 0;
}

.summary-item .label {
    font-weight: 500;
}

.summary-item .value {
    font-weight: 600;
}

.summary-item.total {
    border-top: 2px solid #e3e6f0;
    margin-top: 15px;
    padding-top: 15px;
}

.summary-item.total .value {
    font-size: 1.2rem;
    color: #e74a3b;
}

.deposit-details {
    margin-top: 5px;
    padding-left: 15px;
}

.text-success { color: #28a745 !important; }
.text-danger { color: #dc3545 !important; }
.text-info { color: #17a2b8 !important; }
.text-warning { color: #ffc107 !important; }

.summary-box {
    background-color: #ffffff;
    border: 1px solid #e3e6f0;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.voucher-item, .adjustment-item {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 10px;
    margin-bottom: 10px;
}

.voucher-item:last-child, .adjustment-item:last-child {
    margin-bottom: 0;
}

.discount-badge {
    display: inline-block;
    padding: 0.25em 0.5em;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 0.25rem;
    margin-left: 0.5rem;
}

.spinner-border {
    width: 2rem;
    height: 2rem;
}

.deposit-history-modal .modal-header {
    background-color: #4e73df;
    color: white;
}

.deposit-history-modal .table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.deposit-history-modal .table tr:hover {
    background-color: #f8f9fa;
}

.deposit-history-modal .modal-dialog {
    max-width: 800px;
}

.history-btn {
    transition: all 0.3s;
}

.history-btn:hover {
    transform: scale(1.05);
}

.history-empty-message {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
    font-style: italic;
}

.modal-backdrop {
    opacity: 0.5;
}

.modal-content {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: none;
    border-radius: 0.5rem;
}

.table td {
    vertical-align: middle;
}

    .custom-swal-container {
        z-index: 1060 !important;
    }
    
      .custom-swal-popup {
        max-width: 95% !important;
        width: 900px !important;
    }
    
    .custom-swal-content {
        padding: 1.5rem;
    }
    
    .custom-swal-content .table {
        margin-bottom: 0;
    }
    
    .custom-swal-content .table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        padding: 12px;
        white-space: nowrap;
    }
    
    .custom-swal-content .table td {
        vertical-align: middle;
        padding: 10px;
    }
    
    .custom-swal-content .table tbody tr:hover {
        background-color: rgba(0,0,0,.02);
    }

    .custom-swal-content .table td.text-end {
        font-family: monospace;
        font-size: 1.1em;
    }
</style>
</head>
<body>
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">
            <?php include 'navbar.php'; ?>
            <div class="layout-page">
                <div class="content-wrapper">
                    <?php include 'menu.php'; ?>
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card customer-info mb-4 border-2 border-primary">
                                    <div class="card-header">
                                        <h5 class="mb-0 text-white">ข้อมูลลูกค้า</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3 ">
                                            <div class="avatar me-3">
                                                <?php if (!empty($customer_data['line_picture_url'])): ?>
                                                    <img src="<?php echo $customer_data['line_picture_url']; ?>" alt="Avatar" class="rounded-circle">
                                                <?php else: ?>
                                                    <i class="ri-user-line"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-0"><?php echo $customer_data['cus_title'] . ' ' . $customer_data['cus_firstname'] . ' ' . $customer_data['cus_lastname']; ?></h6>
                                                <small class="text-muted"><?php echo 'HN-' . str_pad($customer_data['cus_id'], 6, '0', STR_PAD_LEFT); ?></small>
                                            </div>
                                        </div>
                                        <ul class="info-list">
                                            <li><span class="label">ชื่อเล่น:</span> <span class="value"><?php echo $customer_data['cus_nickname']; ?></span></li>
                                            <li><span class="label">เลขบัตรประชาชน:</span> <span class="value"><?php echo $customer_data['cus_id_card_number']; ?></span></li>
                                            <li><span class="label">วันเกิด:</span> <span class="value"><?php echo thai_date($customer_data['cus_birthday']); ?></span></li>
                                            <li><span class="label">อายุ:</span> <span class="value"><?php echo $age->y . ' ปี ' . $age->m . ' เดือน ' . $age->d . ' วัน'; ?></span></li>
                                            <li><span class="label">เพศ:</span> <span class="value"><?php echo $customer_data['cus_gender']; ?></span></li>
                                            <li><span class="label">กรุ๊ปเลือด:</span> <span class="value"><?php echo $customer_data['cus_blood']; ?></span></li>
                                            <li><span class="label">โทรศัพท์:</span> <span class="value"><?php echo $customer_data['cus_tel']; ?></span></li>
                                            <li><span class="label">ที่อยู่:</span> <span class="value"><?php echo $customer_data['cus_address'] . ' ' . $customer_data['cus_district'] . ' ' . $customer_data['cus_city'] . ' ' . $customer_data['cus_province'] . ' ' . $customer_data['cus_postal_code']; ?></span></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card order-info mb-4 border-2 border-primary">
                                     <div class="card-header">
                                        <h5 class="mb-0 text-white">ข้อมูลใบเสร็จ</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="mb-3">เลขที่ใบเสร็จ: <?php echo formatOrderId($order_data['oc_id']); ?></h6>
                                        <ul class="info-list">
                                            <li><span class="label">สร้างเมื่อ:</span> <span class="value"><?php echo thai_date($order_data['order_datetime']).' '.date('H:i', strtotime($order_data['order_datetime'])); ?></span></li>
                                            <li><span class="label">โดย:</span> <span class="value"><?php echo $order_data['users_fname'] . ' ' . $order_data['users_lname']; ?></span></li>
                                            <?php if ($booking_data): ?>
                                            <li><span class="label">วันที่จองคอร์ส:</span> <span class="value"><?php echo thai_date($booking_data['booking_datetime']).' '.date('H:i', strtotime($booking_data['booking_datetime'])); ?></span></li>
                                            <?php endif; ?>
                                            <?php if ($queue_data): ?>
                                            <li><span class="label">คิวที่มาใช้บริการ:</span> <span class="value"><?php echo thai_date($queue_data['queue_date']) . ' ' . date('H:i', strtotime($queue_data['queue_time'])); ?></span></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>รายการ</th>
                                                    <th class="text-center">จำนวน/คอร์ส</th>
                                                    <th width="35%">การใช้บริการ</th>
                                                    <th class="text-end">ราคา/หน่วย</th>
                                                    <th class="text-end">ยอดสุทธิ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $items_result->data_seek(0);
                                                while ($item = $items_result->fetch_assoc()): 
                                                    $usage_data = getUsageDetails($oc_id, $item['course_id']);
                                                    $used_sessions = $usage_data['used_sessions'];
                                                    $total_sessions = $usage_data['total_sessions'];
                                                    $usage_percentage = ($total_sessions > 0) ? ($used_sessions / $total_sessions) * 100 : 0;
                                                    $remaining_sessions = $total_sessions - $used_sessions;
                                                ?>
                                                <tr>
                                                    <td class="align-middle">
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($item['course_name']); ?></h6>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <?php echo $item['od_amount']; ?>
                                                    </td>
                                                    <td>
                                                        <div class="usage-section">
                                                            <!-- แสดงจำนวนครั้งที่ใช้และที่เหลือ -->
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span class="usage-count">
                                                                    <i class="ri-checkbox-circle-line text-success"></i>
                                                                    ใช้ไปแล้ว: <strong><?php echo $used_sessions; ?></strong> ครั้ง
                                                                </span>
                                                                <span class="usage-remaining">
                                                                    <i class="ri-time-line text-primary"></i>
                                                                    เหลือ: <strong><?php echo $remaining_sessions; ?></strong> ครั้ง
                                                                </span>
                                                            </div>

                                                            <!-- Progress bar แสดงความคืบหน้า -->
                                                            <div class="progress mb-2" style="height: 10px;">
                                                                <div class="progress-bar bg-<?php echo $usage_percentage >= 100 ? 'success' : 'primary'; ?>"
                                                                     role="progressbar" 
                                                                     style="width: <?php echo min(100, $usage_percentage); ?>%"
                                                                     aria-valuenow="<?php echo $used_sessions; ?>" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="<?php echo $total_sessions; ?>">
                                                                </div>
                                                            </div>

                                                            <!-- ปุ่มดูประวัติและบันทึกการใช้บริการ -->
                                                            <div class="d-flex gap-2">
                                                                <button type="button" 
                                                                        class="btn btn-outline-primary btn-sm flex-grow-1"
                                                                        onclick="showUsageDetails(<?php echo $item['course_id']; ?>)">
                                                                    <i class="ri-history-line me-1"></i>
                                                                    ประวัติการใช้บริการ
                                                                </button>
                                                                <?php if ($remaining_sessions > 0 && $service_history_add): ?>
                                                                <button type="button" 
                                                                        class="btn btn-outline-success btn-sm flex-grow-1"
                                                                        onclick="recordUsage(<?php echo $item['course_id']; ?>)">
                                                                    <i class="ri-add-circle-line me-1"></i>
                                                                    บันทึกการใช้บริการ
                                                                </button>
                                                                <?php endif; ?>
                                                            </div>
                                                            
                                                            <?php if ($used_sessions >= $total_sessions): ?>
                                                            <div class="alert alert-success mt-2 mb-0 py-2">
                                                                <i class="ri-checkbox-circle-line me-1"></i>
                                                                ใช้บริการครบตามจำนวนแล้ว
                                                            </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td class="text-end align-middle">
                                                        <?php echo number_format($item['od_price'], 2); ?>
                                                    </td>
                                                    <td class="text-end align-middle">
                                                        <?php echo number_format($item['od_amount'] * $item['od_price'], 2); ?>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>

                                    </div>
                                    <?php if ($order_data['order_payment'] == 'ยังไม่จ่ายเงิน'): ?>
                                    <div class="text-end m-3">
                                        <a href="edit-order.php?id=<?php echo $oc_id; ?>" class="btn btn-primary">แก้ไขคำสั่งซื้อ</a>
                                    </div>
                                    <?php endif ?>
                                </div>
                                <div class="card deposit-info mb-4 border-2 border-primary">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 text-white">ข้อมูลการชำระเงินมัดจำ</h5>
                                        <div>
                                            <button type="button" class="btn btn-info btn-sm me-2" onclick="showDepositHistory()">
                                                <i class="ri-history-line me-1"></i> ประวัติการยกเลิกมัดจำ
                                            </button>
                                            <?php if ($order_data['deposit_amount'] > 0): ?>
                                                <span class="badge bg-success">ชำระแล้ว</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">ยังไม่ได้ชำระ</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <form id="depositForm" enctype="multipart/form-data">
                                            <input type="hidden" name="order_id" value="<?php echo $oc_id; ?>">
                                            <div class="mb-3">
                                                <label for="deposit_amount" class="form-label">จำนวนเงินมัดจำ (บาท)</label>
                                                <input type="number" class="form-control" id="deposit_amount" name="deposit_amount" 
                                                       value="<?php echo $order_data['deposit_amount']; ?>" step="0.01" min="0" required <?php if(!$deposit_add){echo "readonly";} ?> >
                                            </div>
                                            <div class="mb-3">
                                                <label for="deposit_payment_type" class="form-label">ประเภทการชำระเงินมัดจำ</label>
                                                <select class="form-select" id="deposit_payment_type" name="deposit_payment_type" required <?php if(!$deposit_add){echo "readonly";} ?>>
                                                    <option value="">เลือกประเภท</option>
                                                    <option value="เงินสด" <?php echo $order_data['deposit_payment_type'] == 'เงินสด' ? 'selected' : ''; ?>>เงินสด</option>
                                                    <option value="บัตรเครดิต" <?php echo $order_data['deposit_payment_type'] == 'บัตรเครดิต' ? 'selected' : ''; ?>>บัตรเครดิต</option>
                                                    <option value="เงินโอน" <?php echo $order_data['deposit_payment_type'] == 'เงินโอน' ? 'selected' : ''; ?>>เงินโอน</option>
                                                </select>
                                            </div>
                                            <div id="transferSlipSection" style="display: <?php echo $order_data['deposit_payment_type'] == 'เงินโอน' ? 'block' : 'none'; ?>;">
                                                <div class="mb-3">
                                                    <label for="deposit_slip" class="form-label">แนบสลิปเงินโอน</label>
                                                    <input type="file" class="form-control" id="deposit_slip" name="deposit_slip" accept="image/*">
                                                </div>
                                            </div>
                                            <?php if (!empty($order_data['deposit_slip_image'])): ?>
                                            <div class="mb-3">
                                                <label class="form-label">สลิปการโอนเงินที่อัพโหลด</label>
                                                <div>
                                                    <button type="button" class="btn btn-sm btn-primary" onclick="showSlipModal('../img/payment-proofs/<?php echo $order_data['deposit_slip_image']; ?>')">
                                                        <i class="ri-eye-line me-1"></i> ดูสลิป
                                                    </button>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (!empty($order_data['deposit_date'])): ?>
                                            <div class="mb-3">
                                                <label class="form-label">วันที่และเวลาชำระมัดจำ</label>
                                                <input type="text" class="form-control" value="<?php echo date('d/m/Y H:i:s', strtotime($order_data['deposit_date'])); ?>" readonly>
                                            </div>
                                            <?php endif; ?>
                                            <?php if ($order_data['deposit_amount'] >= 0 && $order_data['order_payment'] == 'ยังไม่จ่ายเงิน'): ?>
                                            <?php if ($order_data['deposit_amount'] == 0 && $deposit_add): ?>

                                            <button type="submit" class="btn btn-primary" id="saveDepositBtn">บันทึกข้อมูลมัดจำ</button>

                                            <?php endif ?>
                                            
                                                <?php 

                                                if ($deposit_cancel && $order_data['deposit_amount'] > 0): ?>
                                                    <button type="button" class="btn btn-danger" id="cancelDepositBtn">ยกเลิกค่ามัดจำ</button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>
                                <?php if ($result_vouchers && $result_vouchers->num_rows > 0): ?>
                                <div class="card mb-4 border-2 border-primary">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 text-white">บัตรกำนัลที่สามารถใช้ได้</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php 
                                        // รีเซ็ต pointer ของ result set
                                        $result_vouchers->data_seek(0);
                                        while ($voucher = $result_vouchers->fetch_assoc()): 
                                            $isPercent = $voucher['discount_type'] === 'percent';
                                            $remainingAmount = $isPercent ? $voucher['amount'] : ($voucher['amount'] - $voucher['total_used_amount']);
                                            
                                            // เช็คเงื่อนไขการแสดงปุ่มใช้งาน
                                            $canUseVoucher = $order_data['order_payment'] == 'ยังไม่จ่ายเงิน' && 
                                                            $voucher['status'] == 'unused' && 
                                                            strtotime($voucher['expire_date']) >= strtotime('today');
                                        ?>
                                            <div class="voucher-item mb-3 p-3 border rounded">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0">รหัสบัตร: <?php echo $voucher['voucher_code']; ?></h6>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge <?php echo $isPercent ? 'bg-info' : 'bg-primary'; ?>">
                                                            <?php echo $isPercent ? "ส่วนลด {$voucher['amount']}%" : "ส่วนลด " . number_format($voucher['amount'], 2) . " บาท"; ?>
                                                        </span>
                                                        <button type="button" 
                                                                class="btn btn-info btn-sm" 
                                                                onclick="showVoucherHistory('<?php echo $voucher['voucher_id']; ?>')">
                                                            <i class="ri-history-line me-1"></i> ประวัติการใช้
                                                        </button>
                                                    </div>
                                                </div>
                                                
                                                <div class="voucher-details">
                                                    <p class="mb-1">
                                                        <small>วันหมดอายุ: <?php echo thai_date($voucher['expire_date']); ?></small>
                                                    </p>
                                                    <?php if (!$isPercent): ?>
                                                        <p class="mb-1">
                                                            <small>ยอดคงเหลือ: <?php echo number_format($remainingAmount, 2); ?> บาท</small>
                                                        </p>
                                                    <?php endif; ?>
                                                    <?php if ($voucher['max_discount']): ?>
                                                        <p class="mb-1">
                                                            <small>ลดสูงสุด: <?php echo number_format($voucher['max_discount'], 2); ?> บาท</small>
                                                        </p>
                                                    <?php endif; ?>
                                                </div>

                                                <?php if ($canUseVoucher): ?>
                                                    <div class="mt-2">
                                                        <button type="button" 
                                                                class="btn btn-sm btn-primary"
                                                                onclick="useVoucher(
                                                                    '<?php echo $voucher['voucher_id']; ?>', 
                                                                    '<?php echo $voucher['voucher_code']; ?>', 
                                                                    <?php echo $voucher['amount']; ?>, 
                                                                    '<?php echo $voucher['discount_type']; ?>', 
                                                                    <?php echo $remainingAmount; ?>,
                                                                    <?php echo $voucher['max_discount'] ?? 'null'; ?>
                                                                )">
                                                            ใช้บัตรกำนัล
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                                <div class="card payment-summary mt-4 border-2 border-primary">
                                   <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0 text-white">สรุปการชำระเงิน</h5>
                                        <div class="text-end">
                                            <button type="button" class="btn btn-danger" onclick="showPaymentCancellationHistory()">
                                                <i class="ri-history-line me-1"></i> ประวัติการยกเลิกชำระเงิน
                                            </button>
                                        </div>
                                    </div>
                                    

                                    <div class="card-body">
                                        <!-- Loading indicator -->
                                        <div id="paymentSummaryLoading" class="text-center d-none">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">กำลังโหลด...</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Payment summary content -->
                                        <div id="paymentSummaryContent">
                                            <div class="summary-box mb-3">
                                                <!-- Total amount -->
                                                <div class="summary-item">
                                                    <span class="label">ยอดรวมทั้งสิ้น:</span>
                                                    <span class="value text-success" id="totalAmount">0.00 บาท</span>
                                                </div>

                                                <!-- Deposit section -->
                                                <div id="depositSection" class="summary-section mb-2 d-none">
                                                    <h6 class="section-title text-primary">ข้อมูลมัดจำ</h6>
                                                    <div class="summary-item">
                                                        <span class="label">จำนวนเงินมัดจำ:</span>
                                                        <span class="value text-info" id="depositAmount">0.00 บาท</span>
                                                    </div>
                                                    <div class="deposit-details">
                                                        <small class="text-muted" id="depositDate"></small><br>
                                                        <small class="text-muted" id="depositType"></small>
                                                    </div>
                                                </div>

                                                <!-- Voucher discount section -->
                                                <div id="voucherSection" class="summary-section mb-2 d-none">
                                                    <h6 class="section-title text-primary">ส่วนลดบัตรกำนัล</h6>
                                                    <div id="voucherDiscountList">
                                                        <!-- Voucher items will be inserted here -->
                                                    </div>
                                                </div>

                                                <!-- Price adjustment section -->
                                                <div id="priceAdjustmentSection" class="summary-section mb-2 d-none">
                                                    <h6 class="section-title text-primary">ส่วนลดจากการปรับราคา</h6>
                                                    <div id="priceAdjustmentList">
                                                        <!-- Price adjustment items will be inserted here -->
                                                    </div>
                                                </div>

                                                <!-- Final amount -->
                                                <div class="summary-item total">
                                                    <span class="label">ยอดที่ต้องชำระ:</span>
                                                    <span class="value" id="remainingAmount">0.00 บาท</span>
                                                </div>
                                            </div>

                                            <!-- Payment form -->
                                            <div id="paymentFormSection">
                                                <!-- Existing payment form content -->
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-4 border-2 border-primary">
                                    <div class="card-header">
                                        <h5 class="mb-0 text-white">รายการที่ใช้บริการ</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>เลขที่บริการ</th>
                                                        <th>วันที่และเวลา</th>
                                                        <th>ราคา/บาท</th>
                                                        <th>สถานะ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($service = $result_services->fetch_assoc()): ?>
                                                    <tr class="clickable-row" data-href="bill.php?id=<?php echo $service['oc_id']; ?>">
                                                        <td><?php echo 'ORDER-' . str_pad($service['oc_id'], 6, '0', STR_PAD_LEFT); ?></td>
                                                        <td><?php echo date('d/m/Y H:i', strtotime($service['order_datetime'])); ?></td>
                                                        <td><?php echo number_format($service['order_net_total'], 2); ?></td>
                                                        <td>
                                                            <?php if ($service['order_payment'] == 'ยังไม่จ่ายเงิน'): ?>
                                                                <span class="badge bg-warning">ยังไม่ชำระ</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-success">ชำระแล้ว</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-4 border-2 border-primary">
                                    <div class="card-header">
                                        <h5 class="card-title text-white">นัดหมายติดตามผล</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="followUpHistory">
                                            <!-- ข้อมูลนัดหมายติดตามผลจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                        </div>
                                    </div>
                                </div>
                                <div class="d-grid gap-2">
                                    <?php if ($isPaymentCompleted && isset($queue_data['queue_id'])): ?>
                                        <a href="opd.php?queue_id=<?php echo $queue_data['queue_id']; ?>" class="btn btn-success btn-lg">OPD</a>
                                        <div class="mb-3">
                                            <select id="doctorSelect" class="form-select">
                                                <option value="">เลือกแพทย์</option>
                                                <!-- ตัวเลือกแพทย์จะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                            </select>
                                        </div>
                                        <button id="printMedicalCertificateBtn" class="btn btn-secondary">พิมพ์ใบรับรองแพทย์</button>
                                    <?php else: ?>
                                        <?php if (!$isPaymentCompleted): ?>
                                            <button class="btn btn-secondary btn-lg" disabled>
                                                ต้องชำระเงินก่อนจึงจะสามารถพิมพ์ใบรับรองแพทย์ได้
                                            </button>
                                        <?php elseif (!$hasQueue): ?>
                                            <button class="btn btn-secondary btn-lg" disabled>
                                                ไม่พบข้อมูลคิว ไม่สามารถพิมพ์ใบรับรองแพทย์ได้
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include 'footer.php'; ?>
                </div>
            </div>
        </div>
    </div>

<!-- Modal for displaying slip image -->
<div class="modal fade" id="slipModal" tabindex="-1" aria-labelledby="slipModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="slipModalLabel">สลิปการโอนเงิน</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <img id="slipImage" src="" alt="Slip" style="width: 100%; height: auto;">
      </div>
    </div>
  </div>
</div>


<!-- เพิ่ม Modal สำหรับบันทึกการใช้บริการ -->
<div class="modal fade" id="recordUsageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">บันทึกการใช้บริการ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="usageForm">
                    <input type="hidden" id="recordUsageCourseId">
                    <div class="mb-3">
                        <label class="form-label">วันที่ใช้บริการ</label>
                        <input type="date" class="form-control" id="usageDate" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">บันทึกเพิ่มเติม</label>
                        <textarea class="form-control" id="usageNotes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" onclick="submitUsage()">บันทึก</button>
            </div>
        </div>
    </div>
</div>

 <!-- เพิ่ม Modal HTML ไว้ที่ส่วนท้ายของ body ใน bill.php -->
<div class="modal fade" id="usageHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ประวัติการใช้บริการ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="20%">วันที่</th>
                                <th width="15%">เวลา</th>
                                <th width="15%">จำนวนครั้ง</th>
                                <th>บันทึกเพิ่มเติม</th>
                                <?php if ($_SESSION['position_id'] == 1 || $_SESSION['position_id'] == 2): ?>
                                <th width="10%">จัดการ</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="usageHistoryTableBody">
                            <!-- ข้อมูลจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- เพิ่มที่ส่วนท้ายของไฟล์ก่อน </body> -->
<div class="modal fade" id="depositHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ประวัติการยกเลิกมัดจำ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>วันที่ยกเลิก</th>
                                <th>จำนวนเงิน</th>
                                <th>เหตุผลการยกเลิก</th>
                                <th>ยกเลิกโดย</th>
                            </tr>
                        </thead>
                        <tbody id="depositHistoryTableBody">
                            <!-- ข้อมูลจะถูกเพิ่มด้วย JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- เพิ่มที่ส่วนท้ายของไฟล์ก่อน </body> -->
<div class="modal fade" id="voucherHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">ประวัติการใช้บัตรกำนัล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="voucher-info mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>รหัสบัตรกำนัล:</strong> <span id="voucherCode"></span></p>
                            <p><strong>มูลค่า:</strong> <span id="voucherAmount"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>วันที่สร้าง:</strong> <span id="voucherCreatedDate"></span></p>
                            <p><strong>วันหมดอายุ:</strong> <span id="voucherExpireDate"></span></p>
                        </div>
                    </div>
                    <div class="progress mt-2">
                        <div id="voucherUsageProgress" class="progress-bar" role="progressbar"></div>
                    </div>
                    <p class="text-center mt-2">
                        <small>ใช้ไปแล้ว <span id="voucherUsedAmount">0</span> จากทั้งหมด <span id="voucherTotalAmount">0</span> บาท</small>
                    </p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>วันที่ใช้</th>
                                <th>เลขที่ใบสั่งซื้อ</th>
                                <th>ชื่อลูกค้า</th>
                                <th>จำนวนเงินที่ใช้</th>
                            </tr>
                        </thead>
                        <tbody id="voucherHistoryTableBody">
                            <!-- ข้อมูลจะถูกเพิ่มด้วย JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>

    <!-- Vendors JS -->
    <!-- <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>


<script>
    const orderIdForJs = <?php echo json_encode($oc_id); ?>;

function formatThaiDate(dateString) {
    const months = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];
    
    const date = new Date(dateString);
    const day = date.getDate();
    const month = months[date.getMonth()];
    const year = date.getFullYear() + 543; // แปลงเป็น พ.ศ.
    
    return `${day} ${month} ${year}`;
}

// ฟังก์ชันแปลงวันที่สำหรับ input
function formatDateForInput(date) {
    const d = new Date(date);
    const year = d.getFullYear() + 543; // แปลงเป็น พ.ศ.
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// ย้ายฟังก์ชัน showSlipModal มาไว้นอก $(document).ready()
function showSlipModal(imageSrc) {
    Swal.fire({
        title: 'สลิปการโอนเงิน',
        imageUrl: imageSrc,
        imageAlt: 'Slip Image',
        width: 350,
        showCloseButton: true,
        showConfirmButton: false
    });
}

function showPaymentSlip(imageName) {
    Swal.fire({
        title: 'สลิปการชำระเงิน',
        imageUrl: '../img/payment-proofs/' + imageName,
        imageAlt: 'Payment Slip',
        width: 350,
        // imageWidth: 400,
        imageHeight: 'auto',
        showCloseButton: true,
        showConfirmButton: false
    });
}
$(document).ready(function() {
    console.log("Document ready, calling loadFollowUpAppointments");
    // loadFollowUpAppointments();
    loadFollowUpHistory();
    // จัดการการคลิกที่แถวในตารางรายการที่ใช้บริการ
    $('.clickable-row').click(function() {
        window.location = $(this).data('href');
    });

    function updateDepositButtonVisibility() {
        var hasPayment = <?php echo json_encode($order_data['order_payment'] != 'ยังไม่จ่ายเงิน'); ?>;
        if (hasPayment) {
            $('#cancelDepositBtn').hide();
        }
    }

    updateDepositButtonVisibility();

    function updateDepositFormState() {
        var depositAmount = parseFloat($('#deposit_amount').val()) || 0;
        var hasDeposit = depositAmount > 0;
        $('#deposit_amount, #deposit_payment_type, #deposit_slip').prop('disabled', hasDeposit);
        $('#saveDepositBtn').toggle(!hasDeposit);
        $('#cancelDepositBtn').toggle(hasDeposit);
    }

    updateDepositFormState();


    $('#depositForm').submit(function(e) {
        e.preventDefault();

        var depositAmount = parseFloat($('#deposit_amount').val()) || 0;
        var depositPaymentType = $('#deposit_payment_type').val();

        // ตรวจสอบการกรอกข้อมูล...

        var formData = new FormData(this);
        $.ajax({
            url: 'sql/update-deposit.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: response.message || 'บันทึกข้อมูลมัดจำสำเร็จ',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message || 'ไม่สามารถบันทึกข้อมูลได้',
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                });
            }
        });
    });

    $('#cancelDepositBtn').click(function() {
        const currentDepositAmount = parseFloat($('#deposit_amount').val()) || 0;
        
        Swal.fire({
            title: 'ยืนยันการยกเลิกค่ามัดจำ',
            html: `
                <form id="cancelDepositForm">
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">เหตุผลในการยกเลิก:</label>
                        <textarea id="cancellation_reason" class="form-control" rows="3" 
                                 placeholder="กรุณาระบุเหตุผลในการยกเลิกค่ามัดจำ" required></textarea>
                    </div>
                </form>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ยกเลิก',
            cancelButtonText: 'ไม่, ยกเลิกการดำเนินการ',
            preConfirm: () => {
                const reason = document.getElementById('cancellation_reason').value.trim();
                if (!reason) {
                    Swal.showValidationMessage('กรุณาระบุเหตุผลในการยกเลิก');
                    return false;
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'sql/cancel-deposit.php',
                    type: 'POST',
                    data: { 
                        order_id: <?php echo $oc_id; ?>,
                        reason: result.value,
                        deposit_amount: currentDepositAmount
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Server response:', response); // For debugging
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: 'ยกเลิกค่ามัดจำเรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: response.message || 'ไม่สามารถยกเลิกค่ามัดจำได้',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax error:', error);
                        console.log('Response:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                        });
                    }
                });
            }
        });
    });

  var remainingAmount = <?php echo $total_amount - $order_data['deposit_amount']; ?>;

    $('#payment_type').change(function() {
        var selectedType = $(this).val();
        $('#paymentSlipSection').toggle(selectedType === 'เงินโอน');
        
        // ซ่อน/แสดงส่วนของเงินทอน
        if (selectedType === 'เงินสด') {
            $('#changeSection').show();
            $('#received_amount').prop('required', true);
        } else {
            $('#changeSection').hide();
            $('#change_amount').val('0.00');
            $('#received_amount').prop('required', false).val(remainingAmount.toFixed(2));
        }
    });

    $('#received_amount').on('input', function() {
        var receivedAmount = parseFloat($(this).val()) || 0;
        var totalAmount = parseFloat($('input[name="total_amount"]').val()) || 0;
        var changeAmount = receivedAmount - totalAmount;
        $('#change_amount').val(changeAmount >= 0 ? changeAmount.toFixed(2) : '0.00');
    });


    // เพิ่มการประกาศตัวแปร currentOrderId ที่ส่วนบนของ JavaScript
    let currentOrderId = <?php echo $oc_id; ?>; // ดึงค่า order_id จาก PHP

    // แก้ไขส่วน submit form การชำระเงิน
    $('#paymentForm').submit(function(e) {
        e.preventDefault();
        
        var paymentType = $('#payment_type').val();
        var receivedAmount = parseFloat($('#received_amount').val()) || 0;

        if (!paymentType) {
            Swal.fire({
                icon: 'error',
                title: 'กรุณาเลือกประเภทการชำระเงิน',
                text: 'โปรดเลือกประเภทการชำระเงินก่อนบันทึกข้อมูล',
            });
            return;
        }

        if (paymentType === 'เงินโอน' && !$('#payment_slip')[0].files.length) {
            Swal.fire({
                icon: 'error',
                title: 'กรุณาแนบสลิปการชำระเงิน',
                text: 'โปรดแนบสลิปการชำระเงินก่อนบันทึกข้อมูล',
            });
            return;
        }

        // เพิ่มการตรวจสอบและตัดสต๊อก
        checkAndDeductStock(currentOrderId)
            .then(() => {
                var formData = new FormData(this);
                formData.append('order_id', currentOrderId); // เพิ่ม order_id เข้าไปใน FormData

                // ดำเนินการบันทึกการชำระเงิน
                $.ajax({
                    url: 'sql/update-payment.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        if(response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: 'บันทึกการชำระเงินและตัดสต๊อกเรียบร้อยแล้ว',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: response.message || 'ไม่สามารถบันทึกการชำระเงินได้'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        console.log('Response:', xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
                        });
                    }
                });
            })
            .catch(error => {
                console.error('Stock Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: error
                });
            });
    });
    // โหลดรายชื่อแพทย์
    loadDoctors();

    // จัดการการเลือกแพทย์
    $('#doctorSelect').change(function() {
        const hasQueue = <?php echo json_encode($hasQueue); ?>;
        const selectedDoctor = $(this).val();
        const isPaymentCompleted = <?php echo json_encode($isPaymentCompleted); ?>;
        
        $('#printMedicalCertificateBtn').prop('disabled', 
            !selectedDoctor || !isPaymentCompleted || !hasQueue
        );
    });

    // จัดการการคลิกปุ่มพิมพ์
    $('#printMedicalCertificateBtn').click(function() {
        const selectedDoctor = $('#doctorSelect').val();
        if (selectedDoctor) {
            if (!<?php echo json_encode($isPaymentCompleted); ?>) {
                Swal.fire({
                    icon: 'warning',
                    title: 'แจ้งเตือน',
                    text: 'กรุณาชำระเงินก่อนพิมพ์ใบรับรองแพทย์'
                });
                return;
            }
            printMedicalCertificate(selectedDoctor);
        } else {
            Swal.fire({
                icon: 'info',
                title: 'แจ้งเตือน',
                text: 'กรุณาเลือกแพทย์ก่อนพิมพ์ใบรับรองแพทย์'
            });
        }
    });

    updateFormState();
    $('#deposit_payment_type').change(function() {
        $('#transferSlipSection').toggle($(this).val() === 'เงินโอน');
    });

    reloadPaymentSummary();

});


// เพิ่มฟังก์ชันอัพเดทการแสดงผลข้อมูลมัดจำ
function updateDepositDisplay(amount, date, paymentType) {
    // อัพเดทค่าในฟอร์ม
    $('#deposit_amount').val(amount);
    $('#deposit_payment_type').val('');
    
    // อัพเดทการแสดงผล
    if (amount > 0) {
        $('#deposit_date').text('วันที่มัดจำ: ' + (date || '-'));
        $('#deposit_type').text('ช่องทางชำระเงิน: ' + (paymentType || '-'));
        $('.deposit-info .badge')
            .removeClass('bg-warning')
            .addClass('bg-success')
            .text('ชำระแล้ว');
    } else {
        $('#deposit_date').text('');
        $('#deposit_type').text('');
        $('.deposit-info .badge')
            .removeClass('bg-success')
            .addClass('bg-warning')
            .text('ยังไม่ได้ชำระ');
    }

    // อัพเดทสถานะฟอร์ม
    const hasDeposit = amount > 0;
    $('#deposit_amount, #deposit_payment_type, #deposit_slip').prop('disabled', hasDeposit);
    $('#saveDepositBtn').toggle(!hasDeposit);
    $('#cancelDepositBtn').toggle(hasDeposit);
}

// เพิ่มฟังก์ชันรีเซ็ตฟอร์ม
function resetDepositForm() {
    $('#deposit_amount').val('0');
    $('#deposit_payment_type').val('');
    $('#deposit_slip').val('');
    $('#transferSlipSection').hide();
}

// เพิ่มฟังก์ชันอัพเดทสถานะฟอร์ม
function updateFormState() {
    const depositAmount = parseFloat($('#deposit_amount').val()) || 0;
    const hasDeposit = depositAmount > 0;
    
    // อัพเดทการ disable/enable input fields
    $('#deposit_amount, #deposit_payment_type, #deposit_slip').prop('disabled', hasDeposit);
    
    // แสดง/ซ่อนปุ่ม
    $('#saveDepositBtn').toggle(!hasDeposit);
    $('#cancelDepositBtn').toggle(hasDeposit);
}

function cancelPayment(orderId) {
    Swal.fire({
        title: 'ยืนยันการยกเลิกการชำระเงิน',
        html: `
            <form id="cancelPaymentForm">
                <div class="mb-3">
                    <label for="password" class="form-label">กรุณายืนยันรหัสผ่าน:</label>
                    <input type="password" class="form-control" id="password" required>
                </div>
                <div class="mb-3">
                    <label for="cancellation_reason" class="form-label">เหตุผลในการยกเลิก:</label>
                    <textarea id="cancellation_reason" class="form-control" rows="3" 
                             placeholder="กรุณาระบุเหตุผลในการยกเลิกการชำระเงิน" required></textarea>
                </div>
            </form>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ยืนยันการยกเลิก',
        cancelButtonText: 'ยกเลิก',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const password = document.getElementById('password').value;
            const reason = document.getElementById('cancellation_reason').value;
            
            if (!password || !reason) {
                Swal.showValidationMessage('กรุณากรอกข้อมูลให้ครบถ้วน');
                return false;
            }

            return $.ajax({
                url: 'sql/cancel-payment.php',
                type: 'POST',
                data: {
                    order_id: orderId,
                    password: password,
                    reason: reason
                },
                dataType: 'json'
            }).catch(error => {
                console.error('Error:', error);
                Swal.showValidationMessage(
                    `เกิดข้อผิดพลาด: ${error.responseJSON?.message || 'ไม่สามารถดำเนินการได้'}`
                );
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value.success) {
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
                text: 'ยกเลิกการชำระเงินเรียบร้อยแล้ว',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                location.reload();
            });
        } else if (result.value && !result.value.success) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: result.value.message || 'ไม่สามารถยกเลิกการชำระเงินได้'
            });
        }
    });
}


function loadFollowUpHistory() {
    const orderId = <?php echo json_encode($oc_id); ?>;
    console.log("Loading follow-up history for order ID:", orderId);

    $.ajax({
        url: 'sql/get-follow-up-history-bill.php',
        type: 'GET',
        data: { order_id: orderId },
        dataType: 'json',
        success: function(response) {
            console.log("AJAX response:", response);
            if (response.success) {
                if (response.data.length > 0) {
                    console.log("Follow-up data received:", response.data);
                    updateFollowUpHistoryTable(response.data);
                } else {
                    console.log("No follow-up data received");
                    $('#followUpHistory').html('<p>ไม่พบข้อมูลนัดหมายติดตามผล</p>');
                }
            } else {
                console.error('Failed to load follow-up history:', response.message);
                $('#followUpHistory').html('<p>เกิดข้อผิดพลาดในการโหลดข้อมูลนัดหมายติดตามผล: ' + response.message + '</p>');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX error:', textStatus, errorThrown);
            $('#followUpHistory').html('<p>ไม่สามารถโหลดข้อมูลนัดหมายติดตามผลได้</p>');
        }
    });
}

function updateFollowUpHistoryTable(data) {
    console.log("Updating follow-up history table with data:", data);
    let historyHtml = '<table class="table">';
    historyHtml += '<thead><tr><th>วันที่และเวลานัด</th><th>หมายเหตุ</th><th>สถานะ</th></tr></thead>';
    historyHtml += '<tbody>';
    
    if (data && data.length > 0) {
        data.forEach(function(item) {
            historyHtml += `<tr>
                <td>${item.booking_datetime}</td>
                <td>${item.note || '-'}</td>
                <td>${getStatusBadge(item.status)}</td>
            </tr>`;
        });
    } else {
        historyHtml += '<tr><td colspan="3" class="text-center">ไม่พบประวัติการนัดติดตามผล</td></tr>';
    }
    
    historyHtml += '</tbody></table>';
    // console.log("Generated HTML:", historyHtml);
    $('#followUpHistory').html(historyHtml);
}

// เรียกใช้ฟังก์ชันเมื่อโหลดหน้า
// $(document).ready(function() {
//     loadFollowUpHistory();
// });



function getStatusBadge(status) {
    switch(status) {
        case 'confirmed':
            return '<span class="badge bg-success">ยืนยันแล้ว</span>';
        case 'cancelled':
            return '<span class="badge bg-danger">ยกเลิกแล้ว</span>';
        case 'completed':
            return '<span class="badge bg-info">เสร็จสิ้น</span>';
        case 'pending':
            return '<span class="badge bg-warning">รอดำเนินการ</span>';
        default:
            return '<span class="badge bg-secondary">ไม่ทราบสถานะ</span>';
    }
}

function printReceipt() {
    $.ajax({
        url: 'sql/get-receipt-data.php',
        type: 'GET',
        data: { oc_id: <?php echo $oc_id; ?> },
        dataType: 'json',
        success: function(receiptData) {
            if (receiptData && receiptData.success) {
                var currentDate = new Date();
                var printDateTime = currentDate.toLocaleString('th-TH', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric', 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit', 
                    hour12: false 
                });

                // สร้าง HTML สำหรับรายการสินค้า
                let itemsHtml = '';
                let itemNumber = 1; // ตัวแปรสำหรับลำดับรายการ

                if (receiptData.items_array && Array.isArray(receiptData.items_array)) {
                    itemsHtml = receiptData.items_array.map(item => {
                        let priceHtml = '';
                        if (item.has_price_adjustment) {
                            priceHtml = `
                                <div>
                                    <span class="original-price">${formatMoney(item.original_price)}</span>
                                    <span class="new-price">${formatMoney(item.adjusted_price)}</span>
                                </div>
                                <div class="discount-info">ส่วนลด ${calculateDiscountPercentage(item.original_price, item.adjusted_price)}%</div>
                            `;
                        } else {
                            priceHtml = formatMoney(item.price);
                        }
                        let row = `
                            <tr>
                                <td style="text-align: center;">${itemNumber}</td>
                                <td>${item.course_name}</td>
                                <td style="text-align: center;">${item.amount}</td>
                                <td style="text-align: right;">${priceHtml}</td>
                                <td style="text-align: right;">${formatMoney(item.total_price)}</td>
                            </tr>
                        `;
                        itemNumber++;
                        return row;
                    }).join('');
                }

                // สร้าง HTML สำหรับส่วนสรุปการชำระเงิน
                let summaryHtml = '';
                if (receiptData.summary) {
                    summaryHtml = `
                        <div class="summary-section" style="margin-top: 10px; text-align: right; font-size: 14px;">
                            <table style="width: 50%; margin-left: auto;">
                                <tr>
                                    <td style="border: none; text-align: right;"><strong>รวมเป็นเงิน:</strong></td>
                                    <td style="border: none; text-align: right;">${formatMoney(receiptData.summary.subtotal)} บาท</td>
                                </tr>
                    `;

                    // ตรวจสอบการมีอยู่ของส่วนลดจากการปรับราคา
                    if (receiptData.summary.price_adjustment_discount > 0) {
                        summaryHtml += `
                            <tr>
                                <td style="border: none; text-align: right;"><strong>ส่วนลดจากการปรับราคา:</strong></td>
                                <td style="border: none; text-align: right; color: #dc3545;">-${formatMoney(receiptData.summary.price_adjustment_discount)} บาท</td>
                            </tr>
                        `;
                    }

                    // ตรวจสอบการมีอยู่ของเงินมัดจำ
                    if (receiptData.order && receiptData.order.deposit_amount > 0) {
                        summaryHtml += `
                            <tr>
                                <td style="border: none; text-align: right;"><strong>มัดจำแล้ว:</strong></td>
                                <td style="border: none; text-align: right; color: #28a745;">-${formatMoney(receiptData.order.deposit_amount)} บาท</td>
                            </tr>
                        `;
                    }

                    // ตรวจสอบการมีอยู่ของส่วนลดบัตรกำนัล
                    if (receiptData.voucher_discounts && receiptData.voucher_discounts.length > 0) {
                        summaryHtml += `
                            <tr>
                                <td style="border: none; text-align: right;"><strong>ส่วนลดบัตรกำนัล:</strong></td>
                                <td style="border: none; text-align: right; color: #dc3545;">-${formatMoney(receiptData.summary.voucher_discount)} บาท</td>
                            </tr>
                        `;
                    }

                    // ยอดสุทธิ
                    summaryHtml += `
                            <tr style="border-top: 2px solid #000;">
                                <td style="border: none; text-align: right;"><strong>ยอดชำระสุทธิ:</strong></td>
                                <td style="border: none; text-align: right; font-size: 16px; font-weight: bold;"><strong>${formatMoney(receiptData.summary.net_total)} บาท</strong></td>
                            </tr>
                        </table>
                    </div>
                    `;

                    // เพิ่มส่วนแสดงวิธีการชำระเงิน
                    if (receiptData.order.order_payment && receiptData.order.order_payment_date) {
                        summaryHtml += `
                            <div class="payment-info" style="margin-top: 10px; text-align: right;">
                                <p><strong>วิธีการชำระเงิน:</strong> ${receiptData.order.order_payment}</p>
                                <p><strong>วันที่ชำระเงิน:</strong> ${formatThaiDateTime(receiptData.order.order_payment_date)}</p>
                            </div>
                        `;
                    }
                }

                var headerHtml = `
                    <div class="header" style="margin-bottom: 10px;">
                        <!-- ส่วนบนสุด แบ่ง 3 คอลัมน์ -->
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                            <!-- Logo ซ้าย -->
                            <div style="width: 15%;">
                                <img src="../img/d.png" alt="Medical Logo" style="width: 60px; height: auto;">
                                <div style="text-align: center; font-weight: bold; font-size: 16px;">MEDICAL</div>
                            </div>

                            <!-- ข้อมูลคลินิก ตรงกลาง -->
                            <div style="width: 60%; text-align: left;">
                                <div style="font-size: 20px; font-weight: bold; margin-bottom: 5px;">
                                    ${receiptData.branch_info.name || 'DEMO CLINIC คลินิก ศัลยกรรม เสริมความงาม'}
                                </div>
                                <div style="font-size: 14px; margin-bottom: 3px;">
                                    ${receiptData.branch_info.address || '100/1 ซ รื่นรมย์ 1 รัชดา จังหวัดกรุงเทพ รหัสไปรษณีย์ 10100'}
                                </div>
                                <div style="font-size: 14px; margin-bottom: 3px;">
                                    โทรศัพท์: ${receiptData.branch_info.phone || '0852225450'} 
                                    อีเมล์: ${receiptData.branch_info.email || 'demo@gmail.com'}
                                </div>
                                <div style="font-size: 14px; margin-bottom: 3px;">
                                    เลขที่ผู้เสียภาษี: ${receiptData.branch_info.tax_id || '8888888888'} 
                                </div>
                                <div style="font-size: 14px;">
                                    เลขที่ใบอนุญาต: ${receiptData.branch_info.license_no || '4221178916'}
                                </div>
                            </div>

                            <!-- ใบเสร็จ ขวา -->
                            <div style="width: 20%; text-align: right">
                                <div style="font-size: 14px; font-weight: bold;">ใบเสร็จรับเงิน</div>
                                <div style="font-size: 16px;">[ RECEIPT ]</div>
                            </div>
                        </div>

                        <!-- เส้นคั่น -->
                        <div style="border-bottom: 1px solid #000; margin: 10px 0;"></div>

                        <!-- ข้อมูลลูกค้าและเลขที่เอกสาร -->
                        <div style="display: flex; justify-content: space-between;">
                            <!-- ข้อมูลลูกค้า ซ้าย -->
                            <div style="width: 60%;">
                                <table style="width: 100%; border: none;">
                                    <tr>
                                        <td style="border: none; padding: 3px 0; font-size: 14px;">รหัสลูกค้า:</td>
                                        <td style="border: none; padding: 3px 0; font-size: 14px;">${formatCustomerId(receiptData.order.cus_id)}</td>
                                    </tr>
                                    <tr>
                                        <td style="border: none; padding: 3px 0; width: 100px; font-size: 14px;">ชื่อลูกค้า:</td>
                                        <td style="border: none; padding: 3px 0; font-size: 14px;">
                                            ${receiptData.order.cus_title} ${receiptData.order.cus_firstname} ${receiptData.order.cus_lastname}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: none; padding: 3px 0; font-size: 14px;">ที่อยู่:</td>
                                        <td style="border: none; padding: 3px 0; font-size: 14px;">${formatAddress(receiptData.order)}</td>
                                    </tr>
                                    <tr>
                                        <td style="border: none; padding: 3px 0; font-size: 14px;">โทรศัพท์:</td>
                                        <td style="border: none; padding: 3px 0; font-size: 14px;">${receiptData.order.cus_tel || '-'}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="border: none; padding: 3px 0; font-size: 14px;">เลขประจำตัวผู้เสียภาษี: ${receiptData.order.cus_id_card_number || '-'}</td>
                                    </tr>

                                </table>
                            </div>

                            <!-- ข้อมูลเอกสาร ขวา -->
                            <div style="width: 40%;">
                                <table style="width: 100%; border: none; " class="align-bottom">
                                    <tr>
                                        <td style="border: none; padding: 3px 0; text-align:right">สถานะชำระเงิน: ${receiptData.order.order_payment || 'ชำระเงินแล้ว'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: none; padding: 3px 0; text-align:right">เลขที่: ${formatOrderNumber(receiptData.order.oc_id)}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border: none; padding: 3px 0; text-align:right">วันที่: ${printDateTime}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- เส้นคั่นก่อนรายการสินค้า -->
                        <div style="border-bottom: 1px solid #000; margin: 10px 0;"></div>
                    </div>
                `;

                var printContent = `
                    <style>
                        @page {
                            size: A4;
                            margin: 15mm; // เพิ่มระยะขอบ
                        }
                        body {
                            font-family: 'Sarabun', sans-serif;
                            line-height: 1.5;
                            font-size: 14px; // ปรับจาก 8px เป็น 14px
                            padding: 0;
                            margin: 0;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin: 15px 0;
                        }
                        th {
                            font-size: 15px;
                            background-color: #f8f9fa;
                            padding: 8px;
                        }
                        td {
                            font-size: 14px;
                            padding: 6px;
                        }
                        .header {
                            margin-bottom: 10px;
                        }
                        .clinic-logo {
                            margin-bottom: 10px;
                        }
                        .contact-info p, .license-info p {
                            margin: 5px 0;
                        }
                        .receipt-info {
                            margin: 15px 0;
                            padding: 10px 0;
                            border-top: 1px solid #ddd;
                            border-bottom: 1px solid #ddd;
                        }
                        .original-price {
                            text-decoration: line-through;
                            color: #999;
                            font-size: 0.9em;
                        }
                        .new-price {
                            color: #dc3545;
                            font-weight: bold;
                        }
                        .discount-info {
                            color: #28a745;
                            font-size: 0.9em;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin: 10px 0;
                        }
                        th, td {
                            border: 1px solid #ddd;
                            padding: 8px;
                            text-align: left;
                        }
                        th {
                            background-color: #f8f9fa;
                        }
                        .footer {
                            margin-top: 20px;
                            text-align: center;
                        }
                        .signature-section {
                            display: flex;
                            justify-content: space-between;
                            margin-top: 20px;
                            font-size: 14px;
                        }
                        .signature-box {
                            text-align: center;
                            width: 180px;
                        }
                        .signature-line {
                            border-top: 1px solid #000;
                            margin-top: 40px;
                            margin-bottom: 10px;
                        }
                        .print-info {
                            font-size: 12px;
                            color: #666;
                            text-align: right;
                            margin-top: 15px;
                        }
                    </style>

                    ${headerHtml}
                    
                    <!-- ส่วนของตารางรายการสินค้า -->
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 10%;">ลำดับ</th>
                                <th style="width: 45%;">รายการ</th>
                                <th style="width: 15%; text-align: center;">จำนวน</th>
                                <th style="width: 15%; text-align: right;">ราคา/หน่วย</th>
                                <th style="width: 15%; text-align: right;">จำนวนเงิน</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${itemsHtml}
                        </tbody>
                    </table>

                    ${summaryHtml}

                    <div class="signature-section">
                        <div class="signature-box">
                            <div class="signature-line"></div>
                            <p>ผู้รับบริการ</p>
                            <p>${receiptData.order.cus_firstname} ${receiptData.order.cus_lastname}</p>
                            <p>วันที่ _____/_____/_____</p>
                        </div>
                        <div class="signature-box">
                            <div class="signature-line"></div>
                            <p>ผู้รับเงิน</p>
                            <p>${receiptData.order.seller_name || '................................'}</p>
                            <p>วันที่ _____/_____/_____</p>
                        </div>
                    </div>

                    <div class="print-info">
                        <p>พิมพ์เมื่อ: ${printDateTime}</p>
                        <p>เลขที่เอกสาร: ${formatOrderNumber(receiptData.order.oc_id)}</p>
                    </div>
                `;

                var printWindow = window.open('', '', 'height=800,width=800');
                printWindow.document.write('<html><head><title>ใบเสร็จรับเงิน</title>');
                printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">');
                printWindow.document.write('</head><body>');
                printWindow.document.write(printContent);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.focus();

               // รอให้รูปภาพโหลดเสร็จก่อนพิมพ์
               setTimeout(function() {
                   printWindow.print();
                   // printWindow.close();
               }, 1000);
           } else {
               Swal.fire({
                   icon: 'error',
                   title: 'เกิดข้อผิดพลาด',
                   text: 'ไม่สามารถดึงข้อมูลใบเสร็จได้'
               });
           }
       },
       error: function(xhr, status, error) {
           console.error('Error:', error);
           Swal.fire({
               icon: 'error',
               title: 'เกิดข้อผิดพลาด',
               text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์'
           });
       }
   });
}

function formatOrderNumber(number) {
   return 'ORDER-' + String(number).padStart(6, '0');
}

function formatCustomerId(id) {
   return 'HN-' + String(id).padStart(6, '0');
}

function calculateDiscountPercentage(originalPrice, newPrice) {
   if (!originalPrice || !newPrice) return '0.00';
   const discount = ((originalPrice - newPrice) / originalPrice) * 100;
   return discount.toFixed(2);
}
// เพิ่ม Event Listener สำหรับปุ่มพิมพ์ใบเสร็จ
// document.getElementById('printReceiptBtn').addEventListener('click', printReceipt);

$(document).ready(function() {

});

function loadDoctors() {
    $.ajax({
        url: 'sql/get-doctors.php', // สร้างไฟล์นี้เพื่อดึงรายชื่อแพทย์จากฐานข้อมูล
        type: 'GET',
        dataType: 'json',
        success: function(doctors) {
            var select = $('#doctorSelect');
            doctors.forEach(function(doctor) {
                select.append($('<option>', {
                    value: doctor.id,
                    text: doctor.name
                }));
            });
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถโหลดรายชื่อแพทย์ได้'
            });
        }
    });
}

function printMedicalCertificate(doctorId) {
    <?php
    $queueId = isset($queue_data['queue_id']) ? $queue_data['queue_id'] : 'null';
    ?>
    var queueId = <?php echo $queueId; ?>;
    if (!queueId || queueId === 'null') {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่พบข้อมูลคิว ไม่สามารถพิมพ์ใบรับรองแพทย์ได้',
        });
        return;
    }
    // เพิ่มการตรวจสอบการชำระเงิน
    if (!<?php echo json_encode($isPaymentCompleted); ?>) {
        Swal.fire({
            icon: 'warning',
            title: 'แจ้งเตือน',
            text: 'กรุณาชำระเงินก่อนพิมพ์ใบรับรองแพทย์'
        });
        return;
    }
    $.ajax({
        url: 'sql/get-medical-certificate-data.php',
        type: 'GET',
        data: { 
            doctor_id: doctorId,
            oc_id: <?php echo $oc_id; ?>,
            queue_id: queueId
        },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                var currentDate = new Date();
                var thaiDate = currentDate.toLocaleDateString('th-TH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                var printContent = `
                <style>
                    @page {
                        size: A4;
                        margin: 0;
                    }
                    body {
                        font-family: 'Sarabun', sans-serif;
                        font-size: 16px;
                        line-height: 1.5;
                        padding: 40px;
                    }
                    h1 {
                        text-align: center;
                        font-size: 24px;
                        margin-bottom: 20px;
                    }
                    .header, .content, .footer {
                        margin-bottom: 20px;
                    }
                    .header {
                        text-align: center;
                    }
                    .signature {
                        text-align: right;
                        margin-top: 50px;
                    }
                    .footer {
                        font-size: 14px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    td {
                        padding: 5px;
                        border: 1px solid #000;
                    }
                </style>
                <h1>ใบรับรองแพทย์</h1>
                <div class="header">
                    <p>สถานที่ตรวจ DEMO CLINIC</p>
                    <p>วันที่ ${thaiDate}</p>
                </div>
                <div class="content">
                    <p>ข้าพเจ้า ${data.doctor.name}</p>
                    <p>ใบอนุญาตประกอบวิชาชีพเวชกรรมเลขที่ ${data.doctor.license_number} สถานที่ประกอบวิชาชีพเวชกรรมอยู่ที่ 100/1 ซ รื่นรมส์ 1 รัชดา จังหวัดกรุงเทพ รหัสไปรษณีย์ 10100</p>
                    <p>ได้ตรวจร่างกาย: ${data.customer.cus_title} ${data.customer.cus_firstname} ${data.customer.cus_lastname} หมายเลขบัตรประชาชน: ${data.customer.cus_id_card_number}</p>
                    
                    
                    <p>แล้วเมื่อวันที่ ${new Date(data.queue.queue_date).toLocaleDateString('th-TH')} มีรายละเอียดดังนี้</p>
                    <table>
                        <tr>
                            <td>น้ำหนักตัว ${data.opd.Weight} กก.</td>
                            <td>ส่วนสูง ${data.opd.Height} เซนติเมตร</td>
                            <td>ความดันโลหิต ${data.opd.Systolic} มม.ปรอท</td>
                            <td>ชีพจร ${data.opd.Pulsation} ครั้ง/นาที</td>
                        </tr>
                    </table>
                    <p>อาการ/วินิจฉัย ขอรับรองว่า: ${data.opd.opd_diagnose || '-'}</p>
                    <p>สรุปความเห็นและข้อแนะนำของแพทย์: ${data.opd.opd_note || '-'}</p>
                </div>
                <div class="signature">
                    <p>ลงชื่อ ............................................</p>
                    <p>${data.doctor.name}</p>
                    <p>แพทย์ผู้ตรวจร่างกาย</p>
                </div>
                <div class="footer">
                    <p>หมายเหตุ</p>
                    <p>(1) ต้องเป็นแพทย์ซึ่งได้ขึ้นทะเบียนรับใบอนุญาตประกอบวิชาชีพเวชกรรม</p>
                    <p>(2) ให้แสดงว่าเป็นผู้มีร่างกายสมบรูณ์เพียงใด ใบรับรองแพทย์ฉบับนี้ให้ใช้ได้ 1 เดือนนับแต่่วันที่ตรวจร่างกาย</p>
                </div>
                `;

                var printWindow = window.open('', '', 'height=800,width=800');
                printWindow.document.write('<html><head><title>ใบรับรองแพทย์</title>');
                printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">');
                printWindow.document.write('</head><body>');
                printWindow.document.write(printContent);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถดึงข้อมูลได้: ' + data.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'เกิดข้อผิดพลาดในการดึงข้อมูล: '
            });
        }
    });
}

// เพิ่ม Event Listener สำหรับปุ่มพิมพ์ใบรับรองแพทย์
document.getElementById('printMedicalCertificateBtn').addEventListener('click', printMedicalCertificate);
// เพิ่ม JavaScript functions สำหรับจัดการการใช้บริการ
function showUsageDetails(courseId) {
    $.ajax({
        url: 'sql/get-usage-details.php',
        type: 'GET',
        data: { 
            course_id: courseId,
            order_id: orderIdForJs
        },
        dataType: 'json',
        success: function(response) {
            const tableBody = $('#usageHistoryTableBody');
            tableBody.empty();

            if (!Array.isArray(response) || response.length === 0) {
                tableBody.html(`<tr><td colspan="5" class="text-center">ยังไม่มีประวัติการใช้บริการ</td></tr>`);
                $('#usageHistoryModal').modal('show');
                return;
            }

            response.forEach(usage => {
                const date = new Date(usage.usage_date);
                const thaiDate = date.toLocaleDateString('th-TH', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                const time = date.toLocaleTimeString('th-TH', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });

                let row = `
                    <tr data-id="${usage.id}">
                        <td>${thaiDate}</td>
                        <td>${time} น.</td>
                        <td>${usage.usage_count || 1} ครั้ง</td>
                        <td>${usage.notes || '-'}</td>`;
                
                // เพิ่มปุ่มลบสำหรับผู้จัดการและแอดมินเท่านั้น
                <?php if ($service_history_cancel): ?>
                row += `
                        <td>
                            <button type="button" 
                                    class="btn btn-danger btn-sm" 
                                    onclick="deleteUsage(
                                        ${usage.id}, 
                                        ${usage.course_id}, 
                                        ${usage.usage_count || 1}, 
                                        ${usage.order_detail_id}
                                    )">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </td>`;
                <?php endif; ?>
                
                row += `</tr>`;
                tableBody.append(row);
            });

            $('#usageHistoryModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            console.log('Response:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถดึงข้อมูลประวัติการใช้บริการได้'
            });
        }
    });
}


function deleteUsage(usageId, courseId, usageCount, orderDetailId) {
     console.log('Delete params:', { usageId, courseId, usageCount, orderDetailId }); // เพิ่ม debug log

    if (!usageId || !courseId || !usageCount || !orderDetailId) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ข้อมูลสำหรับการลบไม่ครบถ้วน'
        });
        return;
    }

    Swal.fire({
        title: 'ยืนยันการลบ',
        text: 'คุณต้องการลบประวัติการใช้บริการนี้ใช่หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ลบเลย',
        cancelButtonText: 'ยกเลิก',
        customClass: {
            confirmButton: 'btn btn-danger me-1',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'sql/delete-usage.php',
                type: 'POST',
                data: {
                    usage_id: usageId,
                    course_id: courseId,
                    usage_count: usageCount,
                    order_id: orderIdForJs,
                    order_detail_id: orderDetailId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'ลบสำเร็จ',
                            text: 'ลบประวัติการใช้บริการเรียบร้อยแล้ว',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            showUsageDetails(courseId);
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: response.message || 'ไม่สามารถลบข้อมูลได้'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                    console.log("Response:", xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์'
                    });
                }
            });
        }
    });
}

function recordUsage(courseId) {
    // สร้างวันที่และเวลาปัจจุบัน
    const now = new Date();
    const currentDate = now.toISOString().slice(0, 10);
    const currentTime = now.toTimeString().slice(0, 5);

    // สร้างวันที่ภาษาไทย
    const thaiMonths = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];
    const thaiDay = now.getDate();
    const thaiMonth = thaiMonths[now.getMonth()];
    const thaiYear = now.getFullYear() + 543;
    const thaiDateString = `${thaiDay} ${thaiMonth} ${thaiYear}`;

    $.ajax({
        url: 'sql/get-course-usage.php',
        type: 'GET',
        data: {
            course_id: courseId,
            order_id: orderIdForJs
        },
        success: function(usageInfo) {
            const remainingSessions = usageInfo.total_sessions - usageInfo.used_sessions;

            Swal.fire({
                title: 'บันทึกการใช้บริการ',
                html: `
                    <div class="mb-3">
                        <div class="alert alert-info">
                            <strong>จำนวนครั้งที่เหลือ:</strong> ${remainingSessions} ครั้ง
                        </div>
                    </div>
                    <form id="usageForm" class="text-start">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">วันที่ใช้บริการ:</label>
                                <input type="date" id="usage_date" class="form-control" 
                                       value="${currentDate}" 
                                       data-thai-date="${thaiDateString}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">เวลา:</label>
                                <input type="time" id="usage_time" class="form-control" 
                                       value="${currentTime}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">จำนวนครั้งที่ใช้:</label>
                            <input type="number" id="usage_count" class="form-control" 
                                   min="1" max="${remainingSessions}" value="1" required>
                            <small class="text-muted">ระบุจำนวนครั้งที่ต้องการใช้ (1-${remainingSessions} ครั้ง)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">บันทึกเพิ่มเติม:</label>
                            <textarea id="usage_notes" class="form-control" rows="3" 
                                      placeholder="บันทึกรายละเอียดการใช้บริการ (ถ้ามี)"></textarea>
                        </div>
                    </form>
                    <style>
                        input[type="date"]:before {
                            content: attr(data-thai-date);
                            position: absolute;
                            top: 0;
                            left: 0;
                            right: 0;
                            bottom: 0;
                            background: white;
                            padding: 0.5rem;
                            pointer-events: none;
                            white-space: nowrap;
                            overflow: hidden;
                        }
                        input[type="date"]:focus:before,
                        input[type="date"]:valid:before {
                            display: none;
                        }
                    </style>
                `,
                showCancelButton: true,
                confirmButtonText: 'บันทึก',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                didOpen: () => {
                    // จัดการการเปลี่ยนแปลงวันที่
                    const dateInput = document.getElementById('usage_date');
                    dateInput.addEventListener('change', function() {
                        const selectedDate = new Date(this.value);
                        const day = selectedDate.getDate();
                        const month = thaiMonths[selectedDate.getMonth()];
                        const year = selectedDate.getFullYear() + 543;
                        const thaiDate = `${day} ${month} ${year}`;
                        this.setAttribute('data-thai-date', thaiDate);
                    });
                },
                preConfirm: () => {
                    const date = document.getElementById('usage_date').value;
                    const time = document.getElementById('usage_time').value;
                    const count = parseInt(document.getElementById('usage_count').value);
                    const notes = document.getElementById('usage_notes').value;
                    
                    if (!date || !time) {
                        Swal.showValidationMessage('กรุณาระบุวันที่และเวลา');
                        return false;
                    }
                    
                    if (!count || count < 1 || count > remainingSessions) {
                        Swal.showValidationMessage(`กรุณาระบุจำนวนครั้งที่ถูกต้อง (1-${remainingSessions})`);
                        return false;
                    }
                    
                    const datetime = `${date} ${time}:00`;
                    return { datetime, count, notes };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'sql/record-usage.php',
                        type: 'POST',
                        data: {
                            course_id: courseId,
                            order_id: orderIdForJs,
                            usage_date: result.value.datetime,
                            usage_count: result.value.count,
                            notes: result.value.notes
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'บันทึกสำเร็จ',
                                    html: `บันทึกการใช้บริการ ${result.value.count} ครั้ง เรียบร้อยแล้ว<br>
                                          <small class="text-muted">เหลือการใช้บริการอีก ${remainingSessions - result.value.count} ครั้ง</small>`,
                                    showConfirmButton: false,
                                    timer: 2000
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: response.message || 'ไม่สามารถบันทึกข้อมูลได้'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์'
                            });
                        }
                    });
                }
            });
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์'
            });
        }
    });
}
function submitUsage() {
    const courseId = document.getElementById('recordUsageCourseId').value;
    const usageDate = document.getElementById('usageDate').value;
    const notes = document.getElementById('usageNotes').value;
    
    $.ajax({
        url: 'sql/record-usage.php',
        type: 'POST',
        data: {
            order_id: <?php echo $oc_id; ?>,
            course_id: courseId,
            usage_date: usageDate,
            notes: notes
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกสำเร็จ',
                    text: 'บันทึกการใช้บริการเรียบร้อยแล้ว',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('เกิดข้อผิดพลาด', response.message || 'ไม่สามารถบันทึกข้อมูลได้', 'error');
            }
        },
        error: function() {
            Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
        }
    });
}

function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('th-TH', options);
}

// เพิ่มฟังก์ชันใหม่
function checkAndDeductStock(orderId) {
    return new Promise((resolve, reject) => {
        // ตรวจสอบสถานะสต๊อก
        $.ajax({
            url: 'sql/check-stock-status.php',
            type: 'POST',
            data: { order_id: orderId },
            dataType: 'json',
            success: function(response) {
                if (response.hasInsufficientStock) {
                    // สร้างข้อความแจ้งเตือน
                    let warningMessage = 'พบรายการที่สต๊อกไม่พอ:\n\n';
                    response.insufficientItems.forEach(item => {
                        warningMessage += `${item.name}\n`;
                        warningMessage += `- ต้องใช้: ${item.required}\n`;
                        warningMessage += `- คงเหลือ: ${item.current}\n`;
                        warningMessage += `- จะติดลบ: ${Math.abs(item.willBeNegative)}\n\n`;
                    });
                    
                    // แสดง SweetAlert2
                    Swal.fire({
                        title: 'แจ้งเตือนสต๊อกไม่พอ',
                        html: warningMessage.replace(/\n/g, '<br>'),
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'ดำเนินการต่อ',
                        cancelButtonText: 'ยกเลิก',
                        customClass: {
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-danger'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // ดำเนินการตัดสต๊อก
                            deductStockAndPay(orderId).then(resolve).catch(reject);
                        } else {
                            reject('ยกเลิกโดยผู้ใช้');
                        }
                    });
                } else {
                    // ถ้าสต๊อกพอ ดำเนินการต่อได้เลย
                    deductStockAndPay(orderId).then(resolve).catch(reject);
                }
            },
            error: function(xhr, status, error) {
                reject(error);
            }
        });
    });
}

function deductStockAndPay(orderId) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'sql/deduct-stock.php',
            type: 'POST',
            data: { order_id: orderId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    resolve();
                } else {
                    reject(response.error || 'เกิดข้อผิดพลาดในการตัดสต๊อก');
                }
            },
            error: function(xhr, status, error) {
                reject(error);
            }
        });
    });
}

function useVoucher(voucherId, voucherCode, amount, discountType, remainingAmount, maxDiscount) {
    const orderTotal = parseFloat(<?php echo $total_amount - $order_data['deposit_amount']; ?>);
    
    if (discountType === 'percent') {
        const discountAmount = (orderTotal * amount) / 100;
        const finalDiscount = maxDiscount ? Math.min(discountAmount, maxDiscount) : discountAmount;
        
        Swal.fire({
            title: 'ยืนยันการใช้บัตรกำนัล',
            html: `
                <p>ส่วนลด ${amount}% คิดเป็นเงิน ${finalDiscount.toFixed(2)} บาท</p>
                <p>จากยอดชำระ ${orderTotal.toFixed(2)} บาท</p>
                <p>ยอดชำระสุทธิ ${(orderTotal - finalDiscount).toFixed(2)} บาท</p>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'ใช้บัตรกำนัล',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                applyVoucher(voucherId, finalDiscount);
            }
        });
    } else {
        Swal.fire({
            title: 'ระบุจำนวนเงินที่ต้องการใช้',
            input: 'number',
            inputAttributes: {
                min: 1,
                max: Math.min(remainingAmount, orderTotal),
                step: '0.01'
            },
            inputValue: Math.min(remainingAmount, orderTotal),
            showCancelButton: true,
            confirmButtonText: 'ใช้บัตรกำนัล',
            cancelButtonText: 'ยกเลิก',
            inputValidator: (value) => {
                const numValue = parseFloat(value);
                if (!numValue) return 'กรุณาระบุจำนวนเงิน';
                if (numValue > remainingAmount) return 'จำนวนเงินเกินยอดคงเหลือในบัตร';
                if (numValue > orderTotal) return 'จำนวนเงินเกินยอดที่ต้องชำระ';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                applyVoucher(voucherId, parseFloat(result.value));
            }
        });
    }
}

function applyVoucher(voucherId, discountAmount) {
    $.ajax({
        url: 'sql/apply-voucher.php',
        type: 'POST',
        data: {
            voucher_id: voucherId,
            order_id: <?php echo $oc_id; ?>,
            discount_amount: discountAmount
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'ใช้บัตรกำนัลสำเร็จ',
                    text: 'บันทึกการใช้บัตรกำนัลเรียบร้อยแล้ว',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    reloadPaymentSummary(); // เพิ่มการรีโหลดข้อมูล
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถใช้บัตรกำนัลได้'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์'
            });
        }
    });
}

// เพิ่มฟังก์ชันสำหรับโหลดและแสดงข้อมูลสรุปการชำระเงิน
function loadPaymentSummary() {
    const orderId = <?php echo $oc_id; ?>;
    console.log('Loading payment summary for order:', orderId); // เพิ่ม debug log
    
    $('#paymentSummaryLoading').removeClass('d-none');
    $('#paymentSummaryContent').addClass('d-none');

    $.ajax({
        url: 'sql/get-payment-summary.php',
        type: 'GET',
        data: { order_id: orderId },
        dataType: 'json',
        success: function(response) {
            console.log('Payment summary response:', response); // เพิ่ม debug log
            if (response.success) {
                updatePaymentSummary(response.data);
            } else {
                console.error('Error loading payment summary:', response.message);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถโหลดข้อมูลสรุปการชำระเงินได้'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', error);
            console.log('Response:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์'
            });
        },
        complete: function() {
            $('#paymentSummaryLoading').addClass('d-none');
            $('#paymentSummaryContent').removeClass('d-none');
        }
    });
}

function updatePaymentSummary(data) {
    console.log('Updating payment summary with data:', data); // เพิ่ม debug log

    // แสดงยอดรวม
    $('#totalAmount').text(formatMoney(data.summary.total) + ' บาท');

    // แสดงข้อมูลมัดจำ
    if (parseFloat(data.deposit.amount) > 0) {
        $('#depositSection').removeClass('d-none');
        $('#depositAmount').text(formatMoney(data.deposit.amount) + ' บาท');
        if (data.deposit.date) {
            $('#depositDate').text('วันที่มัดจำ: ' + formatThaiDateTime(data.deposit.date));
        }
        if (data.deposit.payment_type) {
            $('#depositType').text('ช่องทางชำระเงิน: ' + data.deposit.payment_type);
        }
    } else {
        $('#depositSection').addClass('d-none');
    }

    // แสดงส่วนลดบัตรกำนัล
    if (data.voucher_usage && data.voucher_usage.length > 0) {
        $('#voucherSection').removeClass('d-none');
        let voucherHtml = '';
        data.voucher_usage.forEach(voucher => {
            voucherHtml += `
                <div class="summary-item">
                    <span class="label">
                        บัตรกำนัล ${voucher.voucher_code}
                        <small class="text-muted">
                            (${voucher.discount_type === 'percent' ? 'ส่วนลด %' : 'ส่วนลดเงินสด'})
                        </small>
                    </span>
                    <span class="value text-danger">
                        - ${formatMoney(voucher.amount_used)} บาท
                    </span>
                </div>`;
        });
        $('#voucherDiscountList').html(voucherHtml);
    } else {
        $('#voucherSection').addClass('d-none');
    }

    // แสดงยอดที่ต้องชำระ
    $('#remainingAmount').text(formatMoney(data.summary.remaining) + ' บาท');

    // อัพเดทส่วนฟอร์มการชำระเงิน
    updatePaymentForm(data.summary.remaining);
}

// Utility functions
function formatMoney(amount) {
   return parseFloat(amount).toLocaleString('th-TH', {
       minimumFractionDigits: 2,
       maximumFractionDigits: 2
   });
}

// เพิ่มฟังก์ชัน number_format ถ้ายังไม่มี
function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function formatThaiDateTime(datetime) {
   if (!datetime) return '';
   const date = new Date(datetime);
   const options = {
       year: 'numeric',
       month: 'long',
       day: 'numeric',
       hour: '2-digit',
       minute: '2-digit',
       hour12: false
   };
   
   // แปลงปีเป็น พ.ศ.
   let thaiDate = date.toLocaleDateString('th-TH', options);
   return thaiDate;
}

function formatAddress(orderData) {
   const addressParts = [
       orderData.cus_address,
       orderData.cus_district,
       orderData.cus_city,
       orderData.cus_province,
       orderData.cus_postal_code
   ].filter(Boolean); // กรองค่า null หรือ empty string ออก
   return addressParts.join(' ');
}
// โหลดข้อมูลเมื่อโหลดหน้า
// $(document).ready(function() {
//     loadPaymentSummary();
// });

// เพิ่ม event listener สำหรับรีโหลดข้อมูลเมื่อมีการเปลี่ยนแปลง
function reloadPaymentSummary() {
    loadPaymentSummary();
}

// เรียกใช้ reloadPaymentSummary() หลังจากการทำรายการต่างๆ เช่น
// - หลังใช้บัตรกำนัล
// - หลังปรับราคา
// - หลังชำระมัดจำ
// - หลังยกเลิกการใช้บัตรกำนัล
// - หลังยกเลิกการมัดจำ

function showVoucherDetails(voucherId) {
    $.ajax({
        url: 'sql/get-voucher-details.php',
        type: 'GET',
        data: { voucher_id: voucherId },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'รายละเอียดบัตรกำนัล',
                    html: `
                        <div class="text-start">
                            <p><strong>รหัสบัตร:</strong> ${response.data.voucher_code}</p>
                            <p><strong>ประเภท:</strong> ${response.data.discount_type === 'percent' ? 'ส่วนลด %' : 'ส่วนลดเงินสด'}</p>
                            <p><strong>มูลค่า:</strong> ${formatMoney(response.data.amount)} ${response.data.discount_type === 'percent' ? '%' : 'บาท'}</p>
                            <p><strong>วันที่ใช้งาน:</strong> ${formatThaiDateTime(response.data.used_at)}</p>
                        </div>
                    `,
                    confirmButtonText: 'ปิด'
                });
            }
        }
    });
}

function showPriceAdjustmentDetails(adjustmentId) {
    $.ajax({
        url: 'sql/get-price-adjustment-details.php',
        type: 'GET',
        data: { adjustment_id: adjustmentId },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    title: 'รายละเอียดการปรับราคา',
                    html: `
                        <div class="text-start">
                            <p><strong>คอร์ส:</strong> ${response.data.course_name}</p>
                            <p><strong>ราคาเดิม:</strong> ${formatMoney(response.data.old_price)} บาท</p>
                            <p><strong>ราคาใหม่:</strong> ${formatMoney(response.data.new_price)} บาท</p>
                            <p><strong>ส่วนต่าง:</strong> ${formatMoney(response.data.old_price - response.data.new_price)} บาท</p>
                            <p><strong>เหตุผล:</strong> ${response.data.reason}</p>
                            <p><strong>ปรับราคาเมื่อ:</strong> ${formatThaiDateTime(response.data.adjusted_at)}</p>
                        </div>
                    `,
                    confirmButtonText: 'ปิด'
                });
            }
        }
    });
}

function updatePaymentForm(remainingAmount) {
    // เช็คว่ายังไม่ได้ชำระเงิน
    if (<?php echo json_encode($order_data['order_payment'] == 'ยังไม่จ่ายเงิน'); ?>) {
        const formHtml = `
            <form id="paymentForm" enctype="multipart/form-data">
                <input type="hidden" name="order_id" value="<?php echo $oc_id; ?>">
                <input type="hidden" name="total_amount" value="${remainingAmount}">
                
                <div class="mb-3">
                    <label for="payment_type" class="form-label">ประเภทการชำระเงิน</label>
                    <select class="form-select" id="payment_type" name="payment_type" required>
                        <option value="">เลือกประเภท</option>
                        <option value="เงินสด">เงินสด</option>
                        <option value="บัตรเครดิต">บัตรเครดิต</option>
                        <option value="เงินโอน">เงินโอน</option>
                    </select>
                </div>

                <div id="paymentSlipSection" style="display: none;">
                    <div class="mb-3">
                        <label for="payment_slip" class="form-label">แนบสลิปการชำระเงิน</label>
                        <input type="file" class="form-control" id="payment_slip" name="payment_slip" accept="image/*">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="received_amount" class="form-label">จำนวนเงินที่รับมา</label>
                    <input type="number" class="form-control" id="received_amount" name="received_amount" 
                           step="0.01" required min="${remainingAmount}"
                           value="${remainingAmount}">
                </div>

                <div class="mb-3" id="changeSection" style="display: none;">
                    <label for="change_amount" class="form-label">เงินทอน</label>
                    <input type="text" class="form-control" id="change_amount" readonly>
                </div>


                <button type="submit" class="btn btn-primary btn-lg w-100">บันทึกการชำระเงิน</button>

            </form>
        `;

        $('#paymentFormSection').html(formHtml);

        // เพิ่ม event listeners
        $('#payment_type').change(function() {
            const selectedType = $(this).val();
            $('#paymentSlipSection').toggle(selectedType === 'เงินโอน');
            
            if (selectedType === 'เงินสด') {
                $('#changeSection').show();
                $('#received_amount').prop('required', true);
            } else {
                $('#changeSection').hide();
                $('#change_amount').val('0.00');
                $('#received_amount')
                    .prop('required', false)
                    .val(remainingAmount.toFixed(2));
            }
        });

        $('#received_amount').on('input', function() {
            const receivedAmount = parseFloat($(this).val()) || 0;
            const changeAmount = receivedAmount - remainingAmount;
            $('#change_amount').val(changeAmount >= 0 ? changeAmount.toFixed(2) : '0.00');
        });

        // จัดการการ submit form
        $('#paymentForm').submit(function(e) {
            e.preventDefault();
            
            const paymentType = $('#payment_type').val();
            if (!paymentType) {
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาเลือกประเภทการชำระเงิน',
                    text: 'โปรดเลือกประเภทการชำระเงินก่อนบันทึกข้อมูล'
                });
                return;
            }

            if (paymentType === 'เงินโอน' && !$('#payment_slip')[0].files.length) {
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณาแนบสลิปการชำระเงิน',
                    text: 'โปรดแนบสลิปการชำระเงินก่อนบันทึกข้อมูล'
                });
                return;
            }

            // ตรวจสอบและตัดสต๊อก
            checkAndDeductStock(<?php echo $oc_id; ?>)
                .then(() => {
                    const formData = new FormData(this);
                    
                    $.ajax({
                        url: 'sql/update-payment.php',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(response) {
                            if(response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'สำเร็จ',
                                    text: 'บันทึกการชำระเงินและตัดสต๊อกเรียบร้อยแล้ว',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: response.message || 'ไม่สามารถบันทึกการชำระเงินได้'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                            console.log('Response:', xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
                            });
                        }
                    });
                })
                .catch(error => {
                    console.error('Stock Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: error
                    });
                });
        });
    } else {
        // กรณีชำระเงินแล้ว แสดงข้อมูลการชำระเงิน
        const paymentInfo = `
            <div class="summary-box">
                <div class="summary-item">
                    <span class="label">สถานะการชำระเงิน:</span>
                    <span class="value"><?php echo $order_data['order_payment']; ?></span>
                </div>
                <div class="summary-item">
                    <span class="label">จำนวนเงินที่ชำระ:</span>
                    <span class="value"><?php echo number_format($order_data['order_net_total'], 2); ?> บาท</span>
                </div>
                <div class="summary-item">
                    <span class="label">วันที่ชำระเงิน:</span>
                    <span class="value"><?php echo date('d/m/Y H:i:s', strtotime($order_data['order_payment_date'])); ?></span>
                </div>
                <div class="summary-item">
                    <span class="label">ผู้รับชำระเงิน:</span>
                    <span class="value"><?php echo $order_data['seller_name']; ?></span>
                </div>
                <?php if ($order_data['payment_proofs']): ?>
                <div class="mt-3">
                    <button type="button" class="btn btn-info btn-sm" 
                            onclick="showPaymentSlip('<?php echo $order_data['payment_proofs']; ?>')">
                        ดูสลิปการชำระเงิน
                    </button>
                </div>
                <?php endif; ?>
            </div>
            
            
            <div class="d-flex justify-content-between mt-3">
                <button id="printReceiptBtn" class="btn btn-primary">พิมพ์ใบเสร็จ</button>
                <?php if ($order_data['order_payment'] !== 'ยังไม่จ่ายเงิน' && $payment_cancel): ?>
                    <button type="button" class="btn btn-danger btn-lg" 
                            onclick="cancelPayment(<?php echo $oc_id; ?>)">
                        ยกเลิกการชำระเงิน
                    </button>
                <?php endif; ?>   
            </div>
            
        `;

        $('#paymentFormSection').html(paymentInfo);
        // เพิ่ม event listener หลังจากสร้างปุ่ม
        const printReceiptBtn = document.getElementById('printReceiptBtn');
        if (printReceiptBtn) {
            printReceiptBtn.addEventListener('click', printReceipt);
        }

    }
}

function showDepositHistory() {
    $.ajax({
        url: 'sql/get-deposit-history.php',
        type: 'GET',
        data: { order_id: <?php echo $oc_id; ?> },
        success: function(response) {
            if (response.success) {
                const tableBody = $('#depositHistoryTableBody');
                tableBody.empty();

                if (response.data.length === 0) {
                    tableBody.html('<tr><td colspan="4" class="text-center">ไม่พบประวัติการยกเลิกมัดจำ</td></tr>');
                } else {
                    response.data.forEach(item => {
                        const date = new Date(item.cancelled_at);
                        const formattedDate = date.toLocaleDateString('th-TH', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });

                        const row = `
                            <tr>
                                <td>${formattedDate}</td>
                                <td>${formatMoney(item.deposit_amount)} บาท</td>
                                <td>${escapeHtml(item.cancellation_reason)}</td>
                                <td>${escapeHtml(item.cancelled_by_name)}</td>
                            </tr>
                        `;
                        tableBody.append(row);
                    });
                }

                $('#depositHistoryModal').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถดึงข้อมูลประวัติได้'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์'
            });
        }
    });
}

// Utility function for escaping HTML
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function showVoucherHistory(voucherId) {
    console.log('Voucher ID:', voucherId); // เพิ่ม debug log
    
    if (!voucherId) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่พบรหัสบัตรกำนัล'
        });
        return;
    }

    $.ajax({
        url: 'sql/get-voucher-history.php',
        type: 'GET',
        data: { voucher_id: voucherId },
        dataType: 'json',
        success: function(response) {
            console.log('Response:', response); // เพิ่ม debug log
            
            if (response.success) {
                updateVoucherHistoryModal(response.voucher, response.history);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถดึงข้อมูลประวัติได้'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', error);
            console.log('Response:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์'
            });
        }
    });
}

function updateVoucherHistoryModal(voucher, history) {
    // อัพเดทข้อมูลบัตรกำนัล
    $('#voucherCode').text(voucher.voucher_code);
    $('#voucherAmount').text(formatMoney(voucher.amount) + 
        (voucher.discount_type === 'percent' ? '%' : ' บาท'));
    $('#voucherCreatedDate').text(formatThaiDate(voucher.created_at));
    $('#voucherExpireDate').text(formatThaiDate(voucher.expire_date));

    // คำนวณและแสดงความคืบหน้าการใช้งาน
    const totalAmount = parseFloat(voucher.amount);
    const usedAmount = parseFloat(voucher.total_used) || 0;
    const usagePercentage = (usedAmount / totalAmount) * 100;

    $('#voucherUsageProgress')
        .css('width', `${Math.min(usagePercentage, 100)}%`)
        .attr('aria-valuenow', usagePercentage)
        .addClass(usagePercentage >= 100 ? 'bg-success' : 'bg-primary');

    $('#voucherUsedAmount').text(formatMoney(usedAmount));
    $('#voucherTotalAmount').text(formatMoney(totalAmount));

    // แสดงประวัติการใช้งาน
    const tableBody = $('#voucherHistoryTableBody');
    tableBody.empty();

    if (!history || history.length === 0) {
        tableBody.html(`
            <tr>
                <td colspan="4" class="text-center">
                    <span class="text-muted">ยังไม่มีประวัติการใช้งาน</span>
                </td>
            </tr>
        `);
    } else {
        history.forEach(item => {
            const row = `
                <tr>
                    <td>${formatThaiDateTime(item.used_at)}</td>
                    <td>
                        <a href="bill.php?id=${item.order_id}" target="_blank">
                            ORDER-${String(item.order_id).padStart(6, '0')}
                        </a>
                    </td>
                    <td>${escapeHtml(item.customer_name)}</td>
                    <td>${formatMoney(item.amount_used)} บาท</td>
                </tr>
            `;
            tableBody.append(row);
        });
    }

    $('#voucherHistoryModal').modal('show');
}

// Utility functions
function formatThaiDate(dateString) {
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        timeZone: 'Asia/Bangkok'
    };
    return date.toLocaleDateString('th-TH', options);
}

function formatThaiDateTime(dateString) {
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'Asia/Bangkok'
    };
    return date.toLocaleDateString('th-TH', options);
}

function showPaymentCancellationHistory() {
    $.ajax({
        url: 'sql/get-payment-cancellation-history.php',
        type: 'GET',
        data: { order_id: <?php echo $oc_id; ?> },
        success: function(response) {
            if (response.success) {
                if (!response.data || response.data.length === 0) {
                    Swal.fire({
                        title: 'ประวัติการยกเลิกการชำระเงิน',
                        text: 'ไม่พบประวัติการยกเลิกการชำระเงิน',
                        icon: 'info',
                        confirmButtonText: 'ปิด'
                    });
                    return;
                }

                let historyHtml = `
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>วันที่ยกเลิก</th>
                                    <th>ผู้ยกเลิก</th>
                                    <th>ลูกค้า</th>
                                    <th>จำนวนเงิน</th>
                                    <th>วิธีชำระเงินเดิม</th>
                                    <th>เหตุผล</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                response.data.forEach(item => {
                    historyHtml += `
                        <tr>
                            <td>${item.created_at}</td>
                            <td>${item.users_fname} ${item.users_lname}</td>
                            <td>${item.details.customer_info.name}</td>
                            <td class="text-end">${parseFloat(item.details.payment_info.amount).toLocaleString('th-TH', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            })} บาท</td>
                            <td>${item.details.payment_info.payment_type}</td>
                            <td>${item.details.reason}</td>
                        </tr>
                    `;
                });

                historyHtml += `
                            </tbody>
                        </table>
                    </div>
                `;

                Swal.fire({
                    title: 'ประวัติการยกเลิกการชำระเงิน',
                    html: historyHtml,
                    width: '900px',
                    customClass: {
                        container: 'custom-swal-container',
                        popup: 'custom-swal-popup',
                        content: 'custom-swal-content'
                    },
                    confirmButtonText: 'ปิด',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถดึงข้อมูลประวัติได้',
                    confirmButtonText: 'ปิด'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', error);
            console.log('Response:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์',
                confirmButtonText: 'ปิด'
            });
        }
    });
}
</script>

</body>
</html>