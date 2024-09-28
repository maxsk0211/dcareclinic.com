<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

// เพิ่ม error reporting เพื่อ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// รับค่า oc_id จาก GET parameter
$oc_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($oc_id == 0) {
    die("ไม่พบข้อมูลคำสั่งซื้อ");
}

// ดึงข้อมูลคำสั่งซื้อและข้อมูลลูกค้าจากฐานข้อมูล
$sql = "SELECT oc.*, c.*, cb.booking_datetime 
        FROM order_course oc
        JOIN customer c ON oc.cus_id = c.cus_id
        JOIN course_bookings cb ON oc.course_bookings_id = cb.id
        WHERE oc.oc_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $oc_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("ไม่พบข้อมูลคำสั่งซื้อ");
}

$customer_data = $result->fetch_assoc();

// คำนวณอายุ
$birthDate = new DateTime($customer_data['cus_birthday']);
$today = new DateTime();
$age = $today->diff($birthDate);

// ฟังก์ชันสำหรับแปลงวันที่เป็นรูปแบบไทย
function thai_date($date) {
    $months = array(
        1=>'มกราคม', 2=>'กุมภาพันธ์', 3=>'มีนาคม', 4=>'เมษายน', 5=>'พฤษภาคม', 6=>'มิถุนายน', 
        7=>'กรกฎาคม', 8=>'สิงหาคม', 9=>'กันยายน', 10=>'ตุลาคม', 11=>'พฤศจิกายน', 12=>'ธันวาคม'
    );
    $timestamp = strtotime($date);
    $thai_date = date('d', $timestamp).' '.$months[date('n', $timestamp)].' '.(date('Y', $timestamp) + 543);
    return $thai_date;
}

// ดึงข้อมูลใบเสร็จและรายการสินค้า
$sql_order = "SELECT oc.*, u.users_fname, u.users_lname,
              CONCAT(u.users_fname, ' ', u.users_lname) as seller_name
              FROM order_course oc
              LEFT JOIN users u ON oc.seller_id = u.users_id
              WHERE oc.oc_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $oc_id);
$stmt_order->execute();
$order_result = $stmt_order->get_result();
$order_data = $order_result->fetch_assoc();

$sql_items = "SELECT od.*, c.course_name
              FROM order_detail od
              JOIN course c ON od.course_id = c.course_id
              WHERE od.oc_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $oc_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();

// เพิ่มการดึงข้อมูล booking และ queue
$sql_booking = "SELECT cb.booking_datetime 
                FROM course_bookings cb
                WHERE cb.id = ?";
$stmt_booking = $conn->prepare($sql_booking);
$stmt_booking->bind_param("i", $order_data['course_bookings_id']);
$stmt_booking->execute();
$booking_result = $stmt_booking->get_result();
$booking_data = $booking_result->fetch_assoc();

$sql_queue = "SELECT sq.queue_date, sq.queue_time 
              FROM service_queue sq
              WHERE sq.booking_id = ?";
$stmt_queue = $conn->prepare($sql_queue);
$stmt_queue->bind_param("i", $order_data['course_bookings_id']);
$stmt_queue->execute();
$queue_result = $stmt_queue->get_result();
$queue_data = $queue_result->fetch_assoc();

// ฟังก์ชันสำหรับฟอร์แมตวันที่และเวลา
function format_datetime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}
function formatOrderId($orderId) {
    return 'ORDER-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
}
// คำนวณยอดรวม
$total_amount = 0;
$items_result->data_seek(0); // รีเซ็ตตำแหน่งของ result set
while ($item = $items_result->fetch_assoc()) {
    $total_amount += $item['od_amount'] * $item['od_price'];
}

// ฟังก์ชันสำหรับจัดรูปแบบตัวเลขเงิน
function format_money($amount) {
    return number_format($amount, 2, '.', ',');
}
// ตรวจสอบว่าผู้ใช้เป็นผู้ดูแลระบบหรือผู้จัดการหรือไม่
$canCancelDeposit = ($_SESSION['position_id'] == 1 || $_SESSION['position_id'] == 2);

// เพิ่มโค้ดนี้หลังจากที่คุณดึงข้อมูล $customer_data
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


?>

<!DOCTYPE html>
<html lang="en">
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

    .container-xxl {
        padding: 20px;
    }

    .card {
        border: none;
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
        border: none;
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
                                <div class="card customer-info mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0 text-white">ข้อมูลลูกค้า</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
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
                                <div class="card order-info mb-4">
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
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>รายการ</th>
                                                <th>จำนวน</th>
                                                <th>หน่วยนับ</th>
                                                <th>ราคา/หน่วย</th>
                                                <th>ยอดสุทธิ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $items_result->data_seek(0); // รีเซ็ตตำแหน่งของ result set อีกครั้ง
                                            while ($item = $items_result->fetch_assoc()): 
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['course_name']); ?></td>
                                                <td><?php echo $item['od_amount']; ?></td>
                                                <td>ครั้ง/คอร์ส</td>
                                                <td><?php echo format_money($item['od_price']); ?></td>
                                                <td><?php echo format_money($item['od_amount'] * $item['od_price']); ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-end"><strong>ยอดรวมทั้งสิ้น:</strong></td>
                                                <td><strong><?php echo format_money($total_amount); ?></strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <?php if ($order_data['order_payment'] == 'ยังไม่จ่ายเงิน'): ?>
                                    <div class="text-end m-3">
                                        <a href="edit-order.php?id=<?php echo $oc_id; ?>" class="btn btn-primary">แก้ไขคำสั่งซื้อ</a>
                                    </div>
                                    <?php endif ?>
                                </div>
                                <div class="card deposit-info mb-4">
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
                                <div class="card payment-summary mt-4">
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
                                        <button type="button" class="btn btn-danger btn-lg mt-3" onclick="cancelPayment(<?php echo $oc_id; ?>)">ยกเลิกการชำระเงิน</button>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card mb-4">
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
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title text-white">นัดหมายติดตามผล</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="followUpHistory">
                                            <!-- ข้อมูลนัดหมายติดตามผลจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                        </div>
                                    </div>
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

    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>

    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>


<script>
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

        if (receivedAmount < remainingAmount) {
            Swal.fire({
                icon: 'error',
                title: 'จำนวนเงินไม่เพียงพอ',
                text: 'จำนวนเงินที่รับมาน้อยกว่ายอดที่ต้องชำระ',
            });
            return;
        }

        var formData = new FormData(this);

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
                        text: response.message || 'บันทึกการชำระเงินสำเร็จ',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        $('#cancelDepositBtn').hide(); // ซ่อนปุ่มยกเลิกค่ามัดจำ
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message || 'ไม่สามารถบันทึกการชำระเงินได้',
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
                            text: 'ยกเลิกการชำระเงินเรียบร้อยแล้ว',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: response.message || 'ไม่สามารถยกเลิกการชำระเงินได้',
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
    console.log("Generated HTML:", historyHtml);
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


</script>

</body>
</html>