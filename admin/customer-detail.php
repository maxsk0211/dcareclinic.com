<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

// ตรวจสอบว่ามีการส่ง ID มาหรือไม่
if (!isset($_GET['id'])) {
    die('ไม่พบข้อมูลลูกค้า');
}

$customer_id = mysqli_real_escape_string($conn, $_GET['id']);

// ดึงข้อมูลลูกค้า
$sql = "SELECT * FROM customer WHERE cus_id = '$customer_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die('ไม่พบข้อมูลลูกค้า');
}

$customer = $result->fetch_assoc();

// ดึงข้อมูลการสั่งซื้อคอร์ส
$sql_orders = "SELECT oc.*, 
                      GROUP_CONCAT(
                          CONCAT(
                              c.course_name, '||',
                              c.course_amount, '||',  
                              COALESCE(od.used_sessions, 0)
                          ) 
                          ORDER BY od.od_id 
                          SEPARATOR '##'
                      ) as course_details,
                      SUM(od.od_price * od.od_amount) as total_price
               FROM order_course oc 
               LEFT JOIN order_detail od ON oc.oc_id = od.oc_id 
               LEFT JOIN course c ON od.course_id = c.course_id 
               WHERE oc.cus_id = ?
               GROUP BY oc.oc_id  
               ORDER BY oc.oc_id DESC";

$stmt = $conn->prepare($sql_orders);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result_orders = $stmt->get_result();

$stmt = $conn->prepare($sql_orders);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result_orders = $stmt->get_result();

// ฟังก์ชันแปลงวันที่เป็น พ.ศ.
function convertToThaiDate($date) {
    if(empty($date)) return '-';
    
    $thai_months = [
        1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.', 5 => 'พ.ค.', 6 => 'มิ.ย.',
        7 => 'ก.ค.', 8 => 'ส.ค.', 9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
    ];

    try {
        $date_parts = explode(' ', $date);
        $time = isset($date_parts[1]) ? $date_parts[1] : '';
        $date_parts = explode('-', $date_parts[0]);
        
        if(count($date_parts) !== 3) return '-';
        
        $day = intval($date_parts[2]);
        $month = isset($thai_months[intval($date_parts[1])]) ? $thai_months[intval($date_parts[1])] : '';
        $year = intval($date_parts[0]) + 543;

        if($day && $month && $year) {
            return "$day $month $year" . ($time ? " $time" : "");
        }
        return '-';
    } catch (Exception $e) {
        return '-';
    }
}

// 1. ดึงข้อมูลสุขภาพล่าสุด
$sql_health = "SELECT Weight, Height, BMI, Systolic, Pulsation, created_at 
               FROM opd 
               WHERE cus_id = ? 
               ORDER BY created_at DESC 
               LIMIT 1";
$stmt = $conn->prepare($sql_health);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$health_result = $stmt->get_result();
$health_data = $health_result->fetch_assoc();

// 3. ปรับปรุงการดึงข้อมูลประวัติการสั่งซื้อคอร์ส
$sql_orders = "SELECT oc.*, 
    GROUP_CONCAT(DISTINCT c.course_name SEPARATOR ', ') as course_names,
    GROUP_CONCAT(DISTINCT od.od_amount) as course_amounts,
    GROUP_CONCAT(DISTINCT od.used_sessions) as used_sessions,
    SUM(od.od_price * od.od_amount) as total_price
FROM order_course oc 
LEFT JOIN order_detail od ON oc.oc_id = od.oc_id 
LEFT JOIN course c ON od.course_id = c.course_id 
WHERE oc.cus_id = '$customer_id' 
GROUP BY oc.oc_id
ORDER BY oc.order_datetime DESC";

// 4. ดึงข้อมูลการนัดหมาย
$sql_bookings = "SELECT cb.*, r.room_name,
    GROUP_CONCAT(DISTINCT c.course_name SEPARATOR '|') as booked_courses
FROM course_bookings cb
LEFT JOIN rooms r ON cb.room_id = r.room_id
LEFT JOIN order_course oc ON cb.id = oc.course_bookings_id
LEFT JOIN order_detail od ON oc.oc_id = od.oc_id
LEFT JOIN course c ON od.course_id = c.course_id
WHERE cb.cus_id = ?
GROUP BY cb.id 
ORDER BY cb.booking_datetime DESC";

$stmt = $conn->prepare($sql_bookings);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$bookings_result = $stmt->get_result();

function formatCustomerId($cusId) {
    $paddedId = str_pad($cusId, 6, '0', STR_PAD_LEFT);
    return "HN-" . $paddedId;
}
function formatOrderId($orderId) {
    return 'ORDER-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
}
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>รายละเอียดลูกค้า - D Care Clinic</title>

    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet" />
    <!-- Icons -->
    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />
    <!-- Page CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
        <link rel="stylesheet" href="../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/sweetalert2/sweetalert2.css" />
    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
    
    <style>
        /* Customer Card Styles */
        .customer-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .customer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .customer-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
        }

        .customer-image-container {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            margin-right: 20px;
        }

        .customer-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .customer-header-info {
            flex-grow: 1;
        }

        .customer-name {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .customer-nickname {
            font-size: 1.2rem;
            opacity: 0.8;
            margin-bottom: 5px;
        }

        .customer-id {
            font-size: 1.1rem;
            color: #fda085;
        }

        .customer-details {
            padding: 20px;
        }

        /* Info Grid Styles */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .info-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: bold;
            color: #333;
        }

        /* Additional Info Styles */
        .additional-info {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .info-section {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .info-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .info-section-title {
            color: #333;
            font-size: 1.1rem;
            margin-bottom: 10px;
            border-bottom: 2px solid #fda085;
            padding-bottom: 5px;
        }

        .info-section-content {
            color: #555;
            font-size: 1rem;
            line-height: 1.6;
        }

        /* Address Styles */
        .customer-address {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
        }

        .address-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .address-value {
            color: #555;
            line-height: 1.6;
        }

        /* Health Info Styles */
        .health-info {
            background: linear-gradient(135deg, #6dd5fa, #2980b9);
            border-radius: 15px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .health-info-title {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .health-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .health-info-item {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .health-info-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .health-info-label {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 5px;
        }

        .health-info-value {
            font-size: 1.4rem;
            font-weight: bold;
            color: #2980b9;
        }

        /* Order History Styles */
        .order-history-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .order-history-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 20px;
            font-size: 1.3rem;
            font-weight: bold;
        }

        .datatables-orders {
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .datatables-orders thead th {
            background-color: #f0f4f8;
            color: #333;
            font-weight: bold;
            padding: 15px;
            border: none;
        }

        .datatables-orders tbody tr {
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .datatables-orders tbody tr:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .datatables-orders tbody td {
            padding: 15px;
            border: none;
            vertical-align: middle;
        }

        .datatables-orders .btn-details {
            padding: 5px 10px;
            border-radius: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .datatables-orders .btn-details:hover {
            background-color: #0056b3;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 15px;
        }

    .modal-header {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
    }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        /* Payment Status Styles */
        .payment-status {
            display: inline-flex;
            align-items: center;
            padding: 4px 6px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .payment-status:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        }

        .payment-status i {
            margin-right: 5px;
            font-size: 1.1rem;
        }

        .payment-status.cash {
            background-color: #28a745;
            color: white;
        }

        .payment-status.credit-card {
            background-color: #007bff;
            color: white;
        }

        .payment-status.transfer {
            background-color: #17a2b8;
            color: white;
        }

        .payment-status.unpaid {
            background-color: #dc3545;
            color: white;
        }
    .order-summary .card {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .order-summary .card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Progress Bar Styles */
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 600;
        color: white;
        transition: width 0.6s ease;
    }

    /* Resource Table Styles */
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .table tbody tr:hover {
        background-color: rgba(0,0,0,0.02);
    }

    /* Badge Styles */
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
        border-radius: 6px;
    }

    /* Card Styles */
    .courses-details .card {
        border: 1px solid rgba(0,0,0,0.1);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .courses-details .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .card-title {
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .resources-section {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
    }
    .payment-summary .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .payment-summary .table-borderless td {
        padding: 0.3rem 0;
    }

    .payment-summary .payment-details {
        background-color: rgba(0,0,0,0.02);
        border-radius: 8px;
    }

    .payment-details p {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .payment-details .badge {
        font-size: 0.85rem;
        padding: 0.4em 0.6em;
    }

    .text-success {
        color: #28a745 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .border-top td {
        border-top: 1px solid #dee2e6;
        padding-top: 0.7rem;
        margin-top: 0.7rem;
    }

    .fw-bold {
        font-weight: 600 !important;
    }

    .ps-3 {
        padding-left: 1rem !important;
    }
    .payment-summary .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,0.125);
    }

    .payment-details-table {
        margin-bottom: 0;
    }

    .payment-details-table td {
        padding: 0.5rem;
        vertical-align: middle;
    }

    .total-row {
        font-size: 1.1rem;
    }

    .deposit-section, .voucher-section {
        background-color: rgba(0,0,0,0.02);
        border-radius: 8px;
        margin: 0.5rem 0;
    }

    .deposit-section h6, .voucher-section h6 {
        color: #2196F3;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .border-top td {
        border-top: 2px solid #dee2e6 !important;
        padding-top: 1rem;
    }

    .payment-info {
        background-color: #f8f9fa;
    }

    .payment-info h6 {
        color: #495057;
    }

    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
    }

    .text-muted {
        color: #6c757d !important;
    }

    .ps-3 {
        padding-left: 1rem !important;
    }

    .fw-bold {
        font-weight: 600 !important;
    }

    /* Additional hover effects */
    .deposit-section:hover, .voucher-section:hover {
        background-color: rgba(0,0,0,0.04);
        transition: background-color 0.3s ease;
    }
    .course-status {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0;
    }

    .course-status:not(:last-child) {
        border-bottom: 1px dashed rgba(0,0,0,0.1);
    }

    .course-name {
        font-weight: 500;
        color: #333;
        flex: 1;
    }

    .course-status .badge {
        font-size: 0.85rem;
        padding: 0.4em 0.6em;
        white-space: nowrap;
    }

    .course-status:hover {
        background-color: rgba(0,0,0,0.02);
        border-radius: 4px;
    }

    /* สีสำหรับสถานะต่างๆ */
    .badge.bg-success {
        background-color: #28a745 !important;
    }

    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000;
    }

    .badge.bg-danger {
        background-color: #dc3545 !important;
    }

    .badge.bg-secondary {
        background-color: #6c757d !important;
    }

    /* Tooltip customization */
    .tooltip .tooltip-inner {
        background-color: #333;
        color: #fff;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 0.9rem;
    }
</style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">
            <!-- Navbar -->
            <?php include 'navbar.php'; ?>
            <!-- / Navbar -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Menu -->
                    <?php include 'menu.php'; ?>
                    <!-- / Menu -->

                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="py-3 mb-4"><span class="text-muted fw-light">ลูกค้า /</span> รายละเอียดลูกค้า</h4>

                        <!-- Customer Info -->
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card customer-card mb-4">
                                <div class="customer-header">
                                    <div class="customer-image-container">
                                        <img src="<?php echo $customer['line_picture_url']; ?>" alt="รูปลูกค้า" class="customer-image">
                                    </div>
                                    <div class="customer-header-info">
                                        <h2 class="customer-name text-white"><?php echo $customer['cus_firstname'] . ' ' . $customer['cus_lastname']; ?></h2>
                                        <p class="customer-nickname"><?php echo $customer['cus_nickname']; ?></p>
                                        <p class="customer-id">รหัสลูกค้า: <?php echo formatCustomerId($customer['cus_id']); ?></p>
                                    </div>
                                </div>
                                <div class="customer-details">
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <div class="info-label">เลขบัตรประชาชน</div>
                                            <div class="info-value"><?php echo $customer['cus_id_card_number']; ?></div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">เพศ</div>
                                            <div class="info-value"><?php echo $customer['cus_gender']; ?></div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">วันเกิด</div>
                                            <div class="info-value"><?php echo convertToThaiDate($customer['cus_birthday']); ?></div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">กรุ๊ปเลือด</div>
                                            <div class="info-value"><?php echo $customer['cus_blood']; ?></div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">อีเมล</div>
                                            <div class="info-value"><?php echo $customer['cus_email']; ?></div>
                                        </div>
                                        <div class="info-item">
                                            <div class="info-label">เบอร์โทร</div>
                                            <div class="info-value"><?php echo $customer['cus_tel']; ?></div>
                                        </div>
                                    </div>
                                    <div class="customer-address">
                                        <div class="address-title">ที่อยู่</div>
                                        <div class="address-value">
                                            <?php echo $customer['cus_address'] . ' ' . $customer['cus_district'] . ' ' . $customer['cus_city'] . ' ' . $customer['cus_province'] . ' ' . $customer['cus_postal_code']; ?>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="additional-info">
                                        <div class="info-section">
                                            <h6 class="info-section-title">ประวัติการแพ้ยา</h6>
                                            <p class="info-section-content"><?php echo $customer['cus_drugallergy'] ? $customer['cus_drugallergy'] : 'ไม่มี'; ?></p>
                                        </div>
                                        <div class="info-section">
                                            <h6 class="info-section-title">โรคประจำตัว</h6>
                                            <p class="info-section-content"><?php echo $customer['cus_congenital'] ? $customer['cus_congenital'] : 'ไม่มี'; ?></p>
                                        </div>
                                        <div class="info-section">
                                            <h6 class="info-section-title">หมายเหตุ</h6>
                                            <p class="info-section-content"><?php echo $customer['cus_remark'] ? $customer['cus_remark'] : 'ไม่มี'; ?></p>
                                        </div>
                                    </div>
                                </div>


                                    <div class="health-info">
                                        <h5 class="health-info-title">ข้อมูลสุขภาพล่าสุด</h5>
                                        <p class="text-white mb-2">
                                            วันที่ตรวจ: <?php echo isset($health_data['created_at']) ? convertToThaiDate($health_data['created_at']) : '-'; ?>
                                        </p>
                                        <div class="health-info-grid">
                                            <div class="health-info-item">
                                                <div class="health-info-label">น้ำหนัก</div>
                                                <div class="health-info-value">
                                                    <?php echo isset($health_data['Weight']) && $health_data['Weight'] ? $health_data['Weight'] . ' กก.' : '-'; ?>
                                                </div>
                                            </div>
                                            <div class="health-info-item">
                                                <div class="health-info-label">ส่วนสูง</div>
                                                <div class="health-info-value">
                                                    <?php echo isset($health_data['Height']) && $health_data['Height'] ? $health_data['Height'] . ' ซม.' : '-'; ?>
                                                </div>
                                            </div>
                                            <div class="health-info-item">
                                                <div class="health-info-label">BMI</div>
                                                <div class="health-info-value">
                                                    <?php echo isset($health_data['BMI']) && $health_data['BMI'] ? number_format($health_data['BMI'], 2) : '-'; ?>
                                                </div>
                                            </div>
                                            <div class="health-info-item">
                                                <div class="health-info-label">ความดัน</div>
                                                <div class="health-info-value">
                                                    <?php 
                                                    $systolic = isset($health_data['Systolic']) && $health_data['Systolic'] ? $health_data['Systolic'] : '-';
                                                    $pulsation = isset($health_data['Pulsation']) && $health_data['Pulsation'] ? $health_data['Pulsation'] : '-';
                                                    echo $systolic . '/' . $pulsation;
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- / Customer Info -->

                        <!-- Order History -->
                        <div class="card order-history-card">
                            <h5 class="order-history-header">ประวัติการสั่งซื้อคอร์ส</h5>
                            <div class="card-datatable table-responsive">
                                <table class="datatables-orders table border-top">
                                    <thead>
                                        <tr>
                                            <th>รหัสออเดอร์</th>
                                            <th>วันที่สั่งซื้อ</th>
                                            <th>คอร์ส (จำนวนครั้ง/ใช้ไปแล้ว)</th>
                                            <th>ราคารวม</th>
                                            <th>สถานะการชำระเงิน</th>
                                            <th>การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        while($order = $result_orders->fetch_assoc()): 
                                            $course_details = explode('##', $order['course_details']);
                                        ?>
                                            <tr>
                                                <td>
                                                    <a href="bill.php?id=<?php echo htmlspecialchars($order['oc_id']); ?>" class="text-primary">
                                                        <?php echo formatOrderId($order['oc_id']); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo convertToThaiDate($order['order_datetime']); ?></td>
                                                <td>
                                                    <?php
                                                    if (!empty($order['course_details'])) {
                                                        foreach($course_details as $detail) {
                                                            list($course_name, $total_sessions, $used) = explode('||', $detail);
                                                            if ($total_sessions > 0) { // เพิ่มการตรวจสอบเพื่อป้องกันการหารด้วยศูนย์
                                                                $remaining = $total_sessions - $used;
                                                                $percentage = ($remaining / $total_sessions) * 100;
                                                                
                                                                // กำหนดสีและไอคอนตามจำนวนที่เหลือ
                                                                if ($remaining === 0) {
                                                                    $color = 'secondary';
                                                                    $icon = '✔️';
                                                                } elseif ($remaining === $total_sessions) {
                                                                    $color = 'success';
                                                                    $icon = '✅';
                                                                } elseif ($percentage >= 50) {
                                                                    $color = 'success';
                                                                    $icon = '✅';
                                                                } elseif ($percentage >= 25) {
                                                                    $color = 'warning';
                                                                    $icon = '⚠️';
                                                                } else {
                                                                    $color = 'danger';
                                                                    $icon = '⚠️';
                                                                }

                                                                echo "<div class='course-status mb-2' data-bs-toggle='tooltip' 
                                                                           title='ใช้ไปแล้ว {$used} ครั้ง จากทั้งหมด {$total_sessions} ครั้ง คงเหลือ {$remaining} ครั้ง'>
                                                                        <span class='course-name'>" . htmlspecialchars($course_name) . "</span>
                                                                        <span class='badge bg-{$color} ms-2'>
                                                                            {$icon} ({$used}/{$total_sessions}) [คงเหลือ {$remaining} ครั้ง]
                                                                        </span>
                                                                      </div>";
                                                            }
                                                        }
                                                    } else {
                                                        echo "<span class='text-muted'>ไม่มีข้อมูลคอร์ส</span>";
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo number_format($order['total_price'], 2); ?> บาท</td>
                                                <td>
                                                    <?php
                                                    $status_class = 'unpaid';
                                                    $icon = 'ri-error-warning-line';
                                                    
                                                    if(isset($order['order_payment'])) {
                                                        switch ($order['order_payment']) {
                                                            case 'เงินสด':
                                                                $status_class = 'cash';
                                                                $icon = 'ri-money-dollar-box-line';
                                                                break;
                                                            case 'บัตรเครดิต':
                                                                $status_class = 'credit-card';
                                                                $icon = 'ri-bank-card-line';
                                                                break;
                                                            case 'โอนเงิน':
                                                                $status_class = 'transfer';
                                                                $icon = 'ri-exchange-funds-line';
                                                                break;
                                                        }
                                                    }
                                                    ?>
                                                    <span class="payment-status <?php echo $status_class; ?>">
                                                        <i class="<?php echo $icon; ?>"></i>
                                                        <?php echo htmlspecialchars($order['order_payment'] ?? 'ยังไม่จ่ายเงิน'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn-details" onclick="showOrderDetails(<?php echo $order['oc_id']; ?>)">
                                                        <i class="ri-file-list-3-line"></i> รายละเอียด
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card mt-4">
                            <h5 class="card-header bg-primary text-white">ประวัติการนัดหมาย</h5>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>วันที่นัด</th>
                                                <th>เวลา</th>
                                                <th>ห้อง</th>
                                                <th>คอร์สที่นัด</th>
                                                <th>สถานะ</th>
                                                <th>หมายเหตุ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($booking = $bookings_result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo convertToThaiDate(date('Y-m-d', strtotime($booking['booking_datetime']))); ?></td>
                                                    <td><?php echo date('H:i', strtotime($booking['booking_datetime'])); ?></td>
                                                    <td><?php echo htmlspecialchars($booking['room_name'] ?? '-'); ?></td>
                                                    <td><?php 
                                                        $booked_courses = $booking['booked_courses'] ? 
                                                            explode('|', $booking['booked_courses']) : [];
                                                        echo implode('<br>', array_map('htmlspecialchars', $booked_courses));
                                                    ?></td>
                                                    <td>
                                                        <?php
                                                        $status_class = 'warning';
                                                        $status_text = 'รอยืนยัน';
                                                        
                                                        if(isset($booking['status'])) {
                                                            switch($booking['status']) {
                                                                case 'confirmed':
                                                                    $status_class = 'success';
                                                                    $status_text = 'ยืนยันแล้ว';
                                                                    break;
                                                                case 'cancelled':
                                                                    $status_class = 'danger';
                                                                    $status_text = 'ยกเลิก';
                                                                    break;
                                                            }
                                                        }
                                                        ?>
                                                        <span class="badge bg-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                                    </td>
                                                    <td>
                                                        <?php if(isset($booking['is_follow_up']) && $booking['is_follow_up']): ?>
                                                            <span class="badge bg-info">นัดติดตามผล</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <?php include 'footer.php'; ?>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="orderDetailsModalLabel">รายละเอียดการสั่งซื้อ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- รายละเอียดการสั่งซื้อจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/@tabler/icons@latest/iconfont/tabler-icons.min.js"></script>
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>

    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <!-- <script src="../assets/js/tables-datatables-basic.js"></script> -->
    <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script>
$(document).ready(function() {
    // Initialize DataTable
    $('.datatables-orders').DataTable({
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        responsive: true,
        language: {
            search: 'ค้นหา:',
            lengthMenu: 'แสดง _MENU_ รายการ',
            info: 'แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ',
            paginate: {
                first: 'หน้าแรก',
                last: 'หน้าสุดท้าย',
                next: 'ถัดไป',
                previous: 'ก่อนหน้า'
            }
        }
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover'
        });
    });
});
function showOrderDetails(orderId) {
    // Show loading state
    Swal.fire({
        title: 'กำลังโหลดข้อมูล...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // ดึงข้อมูลการใช้บัตรกำนัล
    $.ajax({
        url: 'sql/get-payment-details.php',
        type: 'GET',
        data: { order_id: orderId },
        success: function(paymentData) {
            // ดึงข้อมูลรายละเอียดออเดอร์
            $.ajax({
                url: 'sql/get-order-details.php',
                type: 'GET',
                data: { id: orderId },
                success: function(response) {
                    Swal.close();
                    
                    // Parse response if it's a string
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    const payment = typeof paymentData === 'string' ? JSON.parse(paymentData) : paymentData;
                    
                    // Format the date
                    const bookingDate = new Date(data.booking_datetime);
                    const formattedDate = bookingDate.toLocaleDateString('th-TH', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    // สร้าง Payment Summary Section
                    const paymentSummary = `
                        <div class="payment-summary mb-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">สรุปการชำระเงิน</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <!-- ยอดรวมและการคำนวณ -->
                                            <table class="table table-borderless table-sm payment-details-table">
                                                <tr class="total-row">
                                                    <td class="fw-bold">ยอดรวมทั้งสิ้น:</td>
                                                    <td class="text-end">${Number(payment.total_amount).toLocaleString('th-TH')}.00 บาท</td>
                                                </tr>

                                                <!-- ส่วนแสดงข้อมูลมัดจำ -->
                                                ${payment.deposit && payment.deposit.amount > 0 ? `
                                                    <tr>
                                                        <td colspan="2">
                                                            <div class="deposit-section bg-light p-2 rounded mb-2">
                                                                <h6 class="mb-2 text-primary">ข้อมูลมัดจำ</h6>
                                                                <div class="ps-3">
                                                                    <div class="row mb-1">
                                                                        <div class="col-6">จำนวนเงินมัดจำ:</div>
                                                                        <div class="col-6 text-end text-danger">
                                                                            - ${Number(payment.deposit.amount).toLocaleString('th-TH')}.00 บาท
                                                                        </div>
                                                                    </div>
                                                                    <div class="small text-muted">
                                                                        วันที่มัดจำ: ${payment.deposit.date || '-'}<br>
                                                                        ช่องทางชำระเงิน: ${payment.deposit.payment_type || '-'}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                ` : ''}

                                                <!-- ส่วนแสดงส่วนลดบัตรกำนัล -->
                                                ${payment.vouchers && payment.vouchers.length > 0 ? `
                                                    <tr>
                                                        <td colspan="2">
                                                            <div class="voucher-section bg-light p-2 rounded mb-2">
                                                                <h6 class="mb-2 text-primary">ส่วนลดบัตรกำนัล</h6>
                                                                ${payment.vouchers.map(v => `
                                                                    <div class="ps-3">
                                                                        <div class="row">
                                                                            <div class="col-7">
                                                                                บัตรกำนัล ${v.code}<br>
                                                                                <small class="text-muted">
                                                                                    (${v.type === 'fixed' ? 'ส่วนลดเงินสด' : 'ส่วนลดเปอร์เซ็นต์'})
                                                                                </small>
                                                                            </div>
                                                                            <div class="col-5 text-end text-danger">
                                                                                - ${Number(v.amount).toLocaleString('th-TH')}.00 บาท
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                `).join('')}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                ` : ''}

                                                <!-- ยอดที่ต้องชำระ -->
                                                <tr class="border-top">
                                                    <td class="fw-bold">ยอดที่ต้องชำระ:</td>
                                                    <td class="text-end fw-bold">${Number(payment.net_amount).toLocaleString('th-TH')}.00 บาท</td>
                                                </tr>
                                            </table>

                                            <!-- ข้อมูลการชำระเงิน -->
                                            <div class="payment-info bg-light p-3 rounded mt-3">
                                                <h6 class="mb-3">ข้อมูลการชำระเงิน</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p class="mb-2">
                                                            <span class="fw-bold">สถานะการชำระเงิน:</span>
                                                            <span class="badge bg-${payment.payment_status === 'ยังไม่จ่ายเงิน' ? 'danger' : 'success'} ms-2">
                                                                ${payment.payment_status}
                                                            </span>
                                                        </p>
                                                        ${payment.payment_amount ? `
                                                            <p class="mb-2">
                                                                <span class="fw-bold">จำนวนเงินที่ชำระ:</span>
                                                                <span class="text-success ms-2">${Number(payment.payment_amount).toLocaleString('th-TH')}.00 บาท</span>
                                                            </p>
                                                        ` : ''}
                                                    </div>
                                                    <div class="col-md-6">
                                                        ${payment.payment_date ? `
                                                            <p class="mb-2">
                                                                <span class="fw-bold">วันที่ชำระเงิน:</span>
                                                                <span class="ms-2">${payment.payment_date}</span>
                                                            </p>
                                                        ` : ''}
                                                        ${payment.payment_by ? `
                                                            <p class="mb-2">
                                                                <span class="fw-bold">ผู้รับชำระเงิน:</span>
                                                                <span class="ms-2">${payment.payment_by}</span>
                                                            </p>
                                                        ` : ''}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    // สร้าง Order Info Section
                    const orderInfo = `
                        <div class="order-summary mb-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="mb-2">รหัสออเดอร์: <span class="text-primary">ORDER-${String(data.order_id).padStart(6, '0')}</span></h6>
                                            <p class="mb-2">ลูกค้า: ${data.customer_name}</p>
                                            <p class="mb-2">วันที่นัด: ${formattedDate}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    // Generate resources list HTML
                    const generateResourcesList = (resources) => {
                        if (!resources || resources.length === 0) return '<p class="text-muted">ไม่มีการใช้ทรัพยากร</p>';
                        
                        return `
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ประเภท</th>
                                            <th>รายการ</th>
                                            <th>จำนวน</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${resources.map(resource => `
                                            <tr>
                                                <td>${resource.type}</td>
                                                <td>${resource.name}</td>
                                                <td>${resource.quantity} ${resource.unit || ''}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    };

                    // Generate progress bars for course usage
                    const generateProgressBar = (used, total) => {
                        const percentage = (used / total) * 100;
                        const bgClass = percentage < 50 ? 'success' : percentage < 75 ? 'warning' : 'danger';
                        
                        return `
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress w-100" style="height: 20px;">
                                    <div class="progress-bar bg-${bgClass}" 
                                         role="progressbar" 
                                         style="width: ${percentage}%"
                                         aria-valuenow="${used}"
                                         aria-valuemin="0"
                                         aria-valuemax="${total}">
                                        ${used}/${total}
                                    </div>
                                </div>
                            </div>
                        `;
                    };

                    // Build Courses Section
                    const coursesSection = `
                        <div class="courses-details">
                            <h6 class="fw-bold mb-3">รายละเอียดคอร์ส</h6>
                            ${data.courses.map((course, index) => `
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h6 class="card-title">
                                                ${course.name}
                                                <span class="badge bg-primary ms-2">
                                                    ${Number(course.price).toLocaleString('th-TH')} บาท
                                                </span>
                                            </h6>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label class="form-label">ความคืบหน้าการใช้คอร์ส:</label>
                                                ${generateProgressBar(course.used_sessions, course.course_amount)}
                                            </div>
                                        </div>

                                        ${course.detail ? `
                                            <div class="mb-3">
                                                <label class="form-label">รายละเอียดเพิ่มเติม:</label>
                                                <p class="card-text">${course.detail}</p>
                                            </div>
                                        ` : ''}

                                        <div class="resources-section">
                                            <label class="form-label">ทรัพยากรที่ใช้:</label>
                                            ${generateResourcesList(course.resources)}
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;

                    // Combine all sections
                    const modalContent = paymentSummary + orderInfo + coursesSection;

                    // Update and show the modal
                    $('#orderDetailsContent').html(modalContent);
                    $('#orderDetailsModal').modal('show');
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถโหลดข้อมูลได้: ' + error
                    });
                }
            });
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถโหลดข้อมูลการชำระเงิน: ' + error
            });
        }
    });
}
    </script>
</body>
</html>