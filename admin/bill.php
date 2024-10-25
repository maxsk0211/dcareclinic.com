<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

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

// ไม่ต้องปิด $conn ที่นี่เพราะอาจจะยังต้องใช้ในส่วนอื่นของหน้า
function canRecordUsage($payment_status) {
    return $payment_status !== 'ยังไม่จ่ายเงิน';
}

// อัพเดทโค้ดส่วนที่เกี่ยวข้องกับการตรวจสอบสถานะ
$isPaymentCompleted = ($order_data['order_payment'] !== 'ยังไม่จ่ายเงิน');
$canCancelDeposit = ($_SESSION['position_id'] == 1 || $_SESSION['position_id'] == 2);
$canRecordServiceUsage = canRecordUsage($order_data['order_payment']);

// ในส่วนของการแสดงผลการใช้บริการ
$items_result->data_seek(0); // รีเซ็ตตำแหน่งของ result set
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
                                                                <?php if ($remaining_sessions > 0 ): ?>
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
                                        <?php if ($order_data['deposit_amount'] > 0): ?>
                                            <span class="badge bg-success">ชำระแล้ว</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">ยังไม่ได้ชำระ</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <form id="depositForm" enctype="multipart/form-data">
                                            <input type="hidden" name="order_id" value="<?php echo $oc_id; ?>">
                                            <div class="mb-3">
                                                <?=$order_data['deposit_amount']; ?>
                                                <label for="deposit_amount" class="form-label">จำนวนเงินมัดจำ (บาท)</label>
                                                <input type="number" class="form-control" id="deposit_amount" name="deposit_amount" 
                                                       value="<?php echo $order_data['deposit_amount']; ?>" step="0.01" min="0" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="deposit_payment_type" class="form-label">ประเภทการชำระเงินมัดจำ</label>
                                                <select class="form-select" id="deposit_payment_type" name="deposit_payment_type" required>
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
                                            <?php if ($order_data['deposit_amount'] == 0): ?>
                                            <button type="submit" class="btn btn-primary" id="saveDepositBtn">บันทึกข้อมูลมัดจำ</button>
                                                
                                            <?php endif ?>
                                            
                                                <?php if ($canCancelDeposit and $order_data['deposit_amount'] > 0): ?>
                                                    <button type="button" class="btn btn-danger" id="cancelDepositBtn">ยกเลิกค่ามัดจำ</button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>
                                <div class="card payment-summary mt-4 border-2 border-primary">
                                    <h5 class="card-header">สรุปการชำระเงิน</h5>
                                    <div class="card-body">
                                        <div class="summary-box mb-3">
                                            <div class="summary-item">
                                                <span class="label">ยอดรวมทั้งสิ้น:</span>
                                                <span class="value"><?php echo format_money($total_amount); ?> บาท</span>
                                            </div>
                                            <div class="summary-item">
                                                <span class="label">หักเงินมัดจำ:</span>
                                                <span class="value"><?php echo format_money($order_data['deposit_amount']); ?> บาท</span>
                                            </div>
                                            <div class="summary-item total">
                                                <span class="label">ยอดที่ต้องชำระเพิ่ม:</span>
                                                <span class="value" id="remainingAmount"><?php echo format_money($total_amount - $order_data['deposit_amount']); ?> บาท</span>
                                            </div>
                                        </div>

                                        <?php if ($order_data['order_payment'] == 'ยังไม่จ่ายเงิน'): ?>
                                        <form id="paymentForm" enctype="multipart/form-data">
                                            <input type="hidden" name="order_id" value="<?php echo $oc_id; ?>">
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
                                                <input type="number" class="form-control" id="received_amount" name="received_amount" step="0.01" required>
                                            </div>
                                            <div class="mb-3" id="changeSection">
                                                <label for="change_amount" class="form-label">เงินทอน</label>
                                                <input type="text" class="form-control" id="change_amount" readonly>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-lg w-100">บันทึกการชำระเงิน</button>
                                        </form>
                                        <?php else: ?>
                                        <div class="summary-box">
                                            <div class="summary-item">
                                                <span class="label">สถานะการชำระเงิน:</span>
                                                <span class="value"><?php echo $order_data['order_payment']; ?></span>
                                            </div>
                                            <div class="summary-item">
                                                <span class="label">จำนวนเงินที่ชำระ:</span>
                                                <span class="value"><?php echo format_money($order_data['order_net_total']); ?> บาท</span>
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
                                                <button type="button" class="btn btn-info btn-sm" onclick="showPaymentSlip('<?php echo $order_data['payment_proofs']; ?>')">
                                                    ดูสลิปการชำระเงิน
                                                </button>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($_SESSION['position_id'] == 1 || $_SESSION['position_id'] == 2): ?>
                                            <div class="d-flex justify-content-between mt-3">
                                                <button id="printReceiptBtn" class="btn btn-primary">พิมพ์ใบเสร็จ</button>
                                                <button type="button" class="btn btn-danger btn-lg " onclick="cancelPayment(<?php echo $oc_id; ?>)">ยกเลิกการชำระเงิน</button>
                                            </div>
                                        <?php endif; ?>
                                        <?php endif; ?>
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

    $('#deposit_payment_type').change(function() {
        $('#transferSlipSection').toggle($(this).val() === 'เงินโอน');
    });

    $('#depositForm').submit(function(e) {
        e.preventDefault();

        var depositAmount = parseFloat($('#deposit_amount').val()) || 0;
        var depositPaymentType = $('#deposit_payment_type').val();

        // ตรวจสอบว่าได้เลือกประเภทการชำระเงินมัดจำหรือไม่
        if (!depositPaymentType) {
            Swal.fire({
                icon: 'error',
                title: 'กรุณาเลือกประเภทการชำระเงินมัดจำ',
                text: 'โปรดเลือกประเภทการชำระเงินมัดจำก่อนบันทึกข้อมูล',
            });
            return;
        }
        // ตรวจสอบว่าได้แนบสลิปหรือไม่ (กรณีเลือกเงินโอน)
        if (depositPaymentType === 'เงินโอน' && !$('#deposit_slip')[0].files.length) {
            Swal.fire({
                icon: 'error',
                title: 'กรุณาแนบสลิปการโอนเงิน',
                text: 'โปรดแนบสลิปการโอนเงินก่อนบันทึกข้อมูล',
            });
            return;
        }

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
                        location.reload();
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
        Swal.fire({
            title: 'ยืนยันการยกเลิกค่ามัดจำ',
            text: "คุณแน่ใจหรือไม่ที่จะยกเลิกค่ามัดจำนี้?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'ใช่, ยกเลิก',
            cancelButtonText: 'ไม่, ยกเลิกการดำเนินการ'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'sql/cancel-deposit.php',
                    type: 'POST',
                    data: { order_id: <?php echo $oc_id; ?> },
                    dataType: 'json',
                    success: function(response) {
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
                    error: function() {
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
        var changeAmount = receivedAmount - remainingAmount;
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





});
function cancelPayment(orderId) {
    Swal.fire({
        title: 'ยืนยันการยกเลิกการชำระเงิน',
        text: "คุณแน่ใจหรือไม่ที่จะยกเลิกการชำระเงินนี้?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ยกเลิก',
        cancelButtonText: 'ไม่, ยกเลิกการดำเนินการ'
    }).then((result) => {
        if (result.isConfirmed) {
            // เพิ่มการคืนสต๊อกก่อนยกเลิกการชำระเงิน
            $.ajax({
                url: 'sql/return-stock.php',
                type: 'POST',
                data: { order_id: orderId },
                dataType: 'json',
                success: function(stockResponse) {
                    if (stockResponse.success) {
                        // หลังจากคืนสต๊อกสำเร็จ ทำการยกเลิกการชำระเงิน
                        $.ajax({
                            url: 'sql/cancel-payment.php',
                            type: 'POST',
                            data: { order_id: orderId },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'สำเร็จ',
                                        text: 'ยกเลิกการชำระเงินและคืนสต๊อกเรียบร้อยแล้ว',
                                        showConfirmButton: false,
                                        timer: 1500
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'เกิดข้อผิดพลาด',
                                        text: response.message || 'ไม่สามารถยกเลิกการชำระเงินได้'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถคืนสต๊อกได้: ' + stockResponse.error
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
                    });
                }
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
            if (receiptData) {
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
                var printContent = `
<style>
            @page {
                size: A4;
                margin: 0;
            }
            body {
                font-family: 'Sarabun', sans-serif;
                padding: 10mm;
                margin: 0;
                font-size: 12px;
                line-height: 1.3;
            }
            .header {
                text-align: center;
                margin-bottom: 3mm;
            }
            .logo {
                width: 40px;
                height: 40px;
            }
            h3 {
                margin: 5px 0;
            }
            p {
                margin: 3px 0;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 5mm 0;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 4px;
                text-align: left;
                font-size: 11px;
            }
            .footer {
                margin-top: 5mm;
                font-size: 11px;
            }
            .text-end {
                text-align: right;
            }
            .print-datetime {
                position: fixed;
                bottom: 10mm;
                right: 10mm;
                font-size: 10px;
                color: #888;
            }
        </style>
                    <div class="header">
                        <img src="../img/d.png" alt="Logo" class="logo">
                        <h3>DEMO CLINIC คลินิก ศัลยกรรม เสริมความงาม</h3>
                        <p>100/1 ซ วิภาวดี 1 รัชดา จังหวัดกรุงเทพ รหัสไปรษณีย์ 10100</p>
                        <p>โทรศัพท์: 0852225450 อีเมล์: demo@gmail.com</p>
                        <p>เลขที่ผู้เสียภาษี: 8888888888 เลขที่ใบอนุญาต: 4221178916</p>
                    </div>
                    <h3 style="text-align: center; margin: 5px 0;">ใบเสร็จรับเงิน [ RECEIPT ]</h3>
                    <p><strong>ชื่อลูกค้า:</strong> ${receiptData.cus_title} ${receiptData.cus_firstname} ${receiptData.cus_lastname}</p>
                    <p><strong>ที่อยู่:</strong> ${receiptData.full_address}</p>
                    <p><strong>เลขประจำตัวผู้เสียภาษี:</strong> ${receiptData.cus_id_card_number}</p>
                    <p><strong>รหัสลูกค้า:</strong> HN-${String(receiptData.cus_id).padStart(6, '0')} </p>
                    <div class="text-end">
                        <p><strong>สถานะชำระเงิน:</strong> ${receiptData.order_payment}</p>
                        <p><strong>เลขที่คำสั่งซื้อ:</strong> ORDER-${String(receiptData.oc_id).padStart(6, '0')} <strong>วันที่:</strong> ${new Date(receiptData.order_datetime).toLocaleDateString('th-TH')}</p>
                    </div>

                    <table>
                        <tr>
                            <th>รหัสคอร์ส</th>
                            <th>รายการ</th>
                            <th style="text-align: center;">จำนวน</th>
                            <th style="text-align: center;">หน่วย</th>
                            <th style="text-align: center;">จำนวนเงิน</th>
                        </tr>
                    ${receiptData.items_array.map(item => `
                        <tr>
                            <td>${item.course_id}</td>
                            <td>${item.course_name}</td>
                            <td style="text-align: center;">${item.amount}</td>
                            <td style="text-align: center;">ครั้ง/หน่วย</td>
                            <td class="text-end">${parseFloat(item.price).toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        </tr>
                    `).join('')}
                    </table>

                    <div class="text-end">
                        <p><strong>รวมเป็นเงิน:</strong> ${parseFloat(receiptData.order_net_total).toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2})} บาท</p>
                        <p>มัดจำแล้ว : ${parseFloat(receiptData.deposit_amount).toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2})} บาท</p>
                        <p>วันที่มัดจำ : ${receiptData.deposit_date ? new Date(receiptData.deposit_date).toLocaleString('th-TH') : '-'}</p>
                        <p><strong>จำนวนเงินชำระสุทธิ:</strong> ${(parseFloat(receiptData.order_net_total) - parseFloat(receiptData.deposit_amount)).toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2})} บาท</p>
                        <p><strong>วิธีการชำระเงิน:</strong> ${receiptData.order_payment} <strong>จำนวนเงิน:</strong> ${parseFloat(receiptData.order_net_total).toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2})} บาท</p>
                        <p>วันที่ชำระเงิน : ${receiptData.order_payment_date ? new Date(receiptData.order_payment_date).toLocaleString('th-TH') : '-'}</p>
                    </div>
                    <br><br><br>
                    <div class="footer">
                        <p>ลูกค้า Customer _________________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ผู้ตรวจ Auditor _________________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ผู้รับเงิน Collector _________________________</p>
                        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; วันที่ ${new Date().toLocaleDateString('th-TH')} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; วันที่ ${new Date().toLocaleDateString('th-TH')} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; วันที่ ${new Date().toLocaleDateString('th-TH')}</p>
                    </div>
                    <div class="print-datetime">
                        พิมพ์เมื่อ: ${printDateTime}
                    </div>
                `;

                var printWindow = window.open('', '', 'height=600,width=800');
                printWindow.document.write('<html><head><title>Print Receipt</title>');
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
                    text: 'ไม่พบข้อมูลใบเสร็จ'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'เกิดข้อผิดพลาดในการดึงข้อมูลใบเสร็จ'
            });
        }
    });
}

// เพิ่ม Event Listener สำหรับปุ่มพิมพ์ใบเสร็จ
document.getElementById('printReceiptBtn').addEventListener('click', printReceipt);

$(document).ready(function() {
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
                <?php if ($_SESSION['position_id'] == 1 || $_SESSION['position_id'] == 2): ?>
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
</script>

</body>
</html>