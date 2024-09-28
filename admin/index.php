<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

$editFormAction = htmlspecialchars($_SERVER['PHP_SELF']);

if (isset($_GET['branch_id'])) {
    $branch_id = filter_input(INPUT_GET, 'branch_id', FILTER_SANITIZE_NUMBER_INT);
    $_SESSION['branch_id'] = $branch_id;
}

if (isset($_GET['branch_out'])) {
    unset($_SESSION['branch_id']);
}

// เพิ่ม error reporting เพื่อ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// รับค่าวันที่จาก input (ถ้ามี)
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// คิวรี่ข้อมูลสรุปยอดขาย และจำนวนบิลประจำวัน
$sql_summary = "
    SELECT 
        COUNT(DISTINCT oc.oc_id) as total_bills,
        SUM(oc.order_net_total) as total_sales,
        SUM(CASE WHEN oc.order_payment != 'ยังไม่จ่ายเงิน' THEN oc.order_net_total ELSE 0 END) as paid_sales,
        SUM(CASE WHEN oc.order_payment = 'ยังไม่จ่ายเงิน' THEN oc.order_net_total ELSE 0 END) as unpaid_sales,
        SUM(CASE WHEN oc.order_payment = 'เงินสด' THEN oc.order_net_total ELSE 0 END) as cash_payment,
        SUM(CASE WHEN oc.order_payment = 'เงินโอน' THEN oc.order_net_total ELSE 0 END) as transfer_payment,
        SUM(CASE WHEN oc.order_payment = 'บัตรเครดิต' THEN oc.order_net_total ELSE 0 END) as credit_card_payment
    FROM order_course oc
    WHERE DATE(oc.order_datetime) = ?
";

// คิวรี่ข้อมูลต้นทุน
$sql_cost = "
    SELECT COALESCE(SUM(resource_cost), 0) as total_cost
    FROM (
        SELECT 
            ocr.order_id,
            ocr.quantity * COALESCE(
                (SELECT cost_per_unit 
                 FROM stock_transactions 
                 WHERE stock_type = ocr.resource_type 
                 AND related_id = ocr.resource_id 
                 AND transaction_date <= oc.order_datetime
                 ORDER BY transaction_date DESC, transaction_id DESC 
                 LIMIT 1), 
                0
            ) as resource_cost
        FROM order_course_resources ocr
        JOIN order_course oc ON ocr.order_id = oc.oc_id
        WHERE DATE(oc.order_datetime) = ?
    ) as costs
";

// ตรวจสอบจำนวนทรัพยากรสำหรับวันที่เลือก
$sql_check_resources = "
    SELECT COUNT(*) as count
    FROM order_course_resources ocr
    JOIN order_course oc ON ocr.order_id = oc.oc_id
    WHERE DATE(oc.order_datetime) = ?
";

// Execute summary query
$stmt_summary = $conn->prepare($sql_summary);
if ($stmt_summary === false) {
    die("เกิดข้อผิดพลาดในการเตรียม SQL summary: " . $conn->error);
}
$stmt_summary->bind_param("s", $selected_date);
if (!$stmt_summary->execute()) {
    die("เกิดข้อผิดพลาดในการ execute SQL summary: " . $stmt_summary->error);
}
$result_summary = $stmt_summary->get_result();
$summary = $result_summary->fetch_assoc();
$stmt_summary->close();

// Execute cost query
$stmt_cost = $conn->prepare($sql_cost);
if ($stmt_cost === false) {
    die("เกิดข้อผิดพลาดในการเตรียม SQL cost: " . $conn->error);
}
$stmt_cost->bind_param("s", $selected_date);
if (!$stmt_cost->execute()) {
    die("เกิดข้อผิดพลาดในการ execute SQL cost: " . $stmt_cost->error);
}
$result_cost = $stmt_cost->get_result();
$cost_data = $result_cost->fetch_assoc();
$stmt_cost->close();

// Execute check resources query
$stmt_check = $conn->prepare($sql_check_resources);
if ($stmt_check === false) {
    die("เกิดข้อผิดพลาดในการเตรียม SQL check resources: " . $conn->error);
}
$stmt_check->bind_param("s", $selected_date);
if (!$stmt_check->execute()) {
    die("เกิดข้อผิดพลาดในการ execute SQL check resources: " . $stmt_check->error);
}
$result_check = $stmt_check->get_result();
$resource_count = $result_check->fetch_assoc()['count'];
$stmt_check->close();

// เพิ่มการตรวจสอบและแสดงผล
// echo "<pre>";
// echo "Summary Data:\n";
// print_r($summary);
// echo "\nCost Data:\n";
// print_r($cost_data);
// echo "\nNumber of resources for selected date: " . $resource_count . "\n";
// echo "</pre>";

// ตรวจสอบก่อนการกำหนดค่า
if (isset($cost_data['total_cost'])) {
    $summary['total_cost'] = $cost_data['total_cost'];
} else {
    $summary['total_cost'] = 0; // หรือค่าเริ่มต้นที่คุณต้องการ
}

// คำนวณกำไรและอัตรากำไร
$summary['total_profit'] = $summary['total_sales'] - $summary['total_cost'];
$summary['profit_margin'] = ($summary['total_sales'] > 0) ? ($summary['total_profit'] / $summary['total_sales']) * 100 : 0;

// แสดงผลข้อมูลสรุปอีกครั้ง
// echo "<pre>";
// echo "Final Summary Data:\n";
// print_r($summary);
// echo "</pre>";

// คิวรี่ข้อมูลรายการบิลประจำวัน
$sql_bills = "SELECT oc.oc_id, oc.order_datetime, c.cus_firstname, c.cus_lastname, 
                     oc.order_payment, oc.order_net_total, cb.booking_datetime
              FROM order_course oc
              JOIN customer c ON oc.cus_id = c.cus_id
              JOIN course_bookings cb ON oc.course_bookings_id = cb.id
              WHERE DATE(oc.order_datetime) = ?
              ORDER BY oc.order_datetime DESC";

$stmt_bills = $conn->prepare($sql_bills);
if ($stmt_bills === false) {
    die("เกิดข้อผิดพลาดในการเตรียม SQL สำหรับรายการบิล: " . $conn->error);
}
$stmt_bills->bind_param("s", $selected_date);
if (!$stmt_bills->execute()) {
    die("เกิดข้อผิดพลาดในการ execute SQL สำหรับรายการบิล: " . $stmt_bills->error);
}
$result_bills = $stmt_bills->get_result();
$stmt_bills->close();

?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>รายการคำสั่งซื้อประจำวัน - D Care Clinic</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="../assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
    <style>
    .card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }
    .card-body {
        padding: 1.5rem;
    }
    .card-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
    }
    .card-info h5 {
        font-size: 1rem;
        font-weight: 600;
        color: #555;
    }
    .card-icon {
        font-size: 1.5rem;
    }
    .progress {
        height: 8px;
        margin-bottom: 0.5rem;
    }
    .text-muted {
        font-size: 0.85rem;
    }
    .badge {
        padding: 0.5rem;
    }
</style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">
            <!-- Navbar -->
            <?php if(isset($_SESSION['branch_id'])){ include 'navbar.php'; } ?>
            <!-- / Navbar -->
            <?php if (isset($_SESSION['branch_id'])): ?>
              
            
            <!-- Layout container -->
            <div class="layout-page">
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Menu -->
                    <?php if(isset($_SESSION['branch_id'])){ include 'menu.php'; } ?>
                    <!-- / Menu -->

                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">

                        <h4 class="py-3 mb-4"><span class="text-muted fw-light">คำสั่งซื้อ /</span> รายการคำสั่งซื้อประจำวัน</h4>
                        
                        <!-- Date Picker -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <form action="" method="GET" class="row g-3">
                                    <div class="col-md-4">
                                        <label for="date" class="form-label">เลือกวันที่</label>
                                        <input type="date" class="form-control" id="date" name="date" value="<?php echo $selected_date; ?>">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary">แสดงข้อมูล</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Dashboard Summary -->
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="card-info">
                                                <h5 class="mb-0">ยอดขายทั้งหมด</h5>
                                                <small class="text-muted">รวมทุกช่องทางการชำระเงิน</small>
                                            </div>
                                            <div class="card-icon">
                                                <span class="badge bg-label-primary rounded p-2">
                                                    <i class="ti ti-currency-baht ti-sm"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <h3 class="card-title mb-1 mt-4">
                                            <?php echo number_format($summary['total_sales'], 2); ?> บาท
                                        </h3>
                                        <small class="text-success fw-semibold">
                                            <?php 
                                            $percentPaid = ($summary['total_sales'] > 0) ? ($summary['paid_sales'] / $summary['total_sales']) * 100 : 0;
                                            echo number_format($percentPaid, 2) . '% ชำระแล้ว'; 
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="card-info">
                                                <h5 class="mb-0">ยอดชำระแล้ว</h5>
                                                <small class="text-muted">เฉพาะรายการที่ชำระเงินแล้ว</small>
                                            </div>
                                            <div class="card-icon">
                                                <span class="badge bg-label-success rounded p-2">
                                                    <i class="ti ti-check ti-sm"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <h3 class="card-title mb-1 mt-4"><?php echo number_format($summary['paid_sales'], 2); ?> บาท</h3>
                                        <small class="text-success fw-semibold">
                                            <?php
                                            $percentOfTotal = ($summary['total_sales'] > 0) ? ($summary['paid_sales'] / $summary['total_sales']) * 100 : 0;
                                            echo number_format($percentOfTotal, 2) . '% ของยอดขายทั้งหมด';
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="card-info">
                                                <h5 class="mb-0">ยอดค้างชำระ</h5>
                                                <small class="text-muted">รายการที่ยังไม่ได้ชำระเงิน</small>
                                            </div>
                                            <div class="card-icon">
                                                <span class="badge bg-label-warning rounded p-2">
                                                    <i class="ti ti-alert-triangle ti-sm"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <h3 class="card-title mb-1 mt-4"><?php echo number_format($summary['unpaid_sales'], 2); ?> บาท</h3>
                                        <small class="text-danger fw-semibold">
                                            <?php
                                            $percentUnpaid = ($summary['total_sales'] > 0) ? ($summary['unpaid_sales'] / $summary['total_sales']) * 100 : 0;
                                            echo number_format($percentUnpaid, 2) . '% ของยอดขายทั้งหมด';
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="card-info">
                                                <h5 class="mb-0">จำนวนบิล</h5>
                                                <small class="text-muted">รายการคำสั่งซื้อทั้งหมด</small>
                                            </div>
                                            <div class="card-icon">
                                                <span class="badge bg-label-info rounded p-2">
                                                    <i class="ti ti-file-invoice ti-sm"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <h3 class="card-title mb-1 mt-4"><?php echo $summary['total_bills']; ?> รายการ</h3>
                                        <small class="text-muted fw-semibold"><i class="ti ti-calendar"></i> อัพเดทล่าสุด: <?php echo date('d/m/Y H:i'); ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="card-info">
                                                <h5 class="mb-0">ต้นทุนรวม</h5>
                                                <small class="text-muted">ค่าใช้จ่ายทั้งหมดในการดำเนินงาน</small>
                                            </div>
                                            <div class="card-icon">
                                                <span class="badge bg-label-danger rounded p-2">
                                                    <i class="ti ti-receipt ti-sm"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <h3 class="card-title mb-1"><?php echo number_format($summary['total_cost'], 2); ?> บาท</h3>
                                        <div class="progress mb-1" style="height: 8px;">
                                            <div class="progress-bar bg-danger" style="width: <?php echo ($summary['total_sales'] > 0) ? ($summary['total_cost'] / $summary['total_sales']) * 100 : 0; ?>%" role="progressbar" aria-valuenow="<?php echo ($summary['total_sales'] > 0) ? ($summary['total_cost'] / $summary['total_sales']) * 100 : 0; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">
                                            <?php
                                            $percentOfSales = ($summary['total_sales'] > 0) ? ($summary['total_cost'] / $summary['total_sales']) * 100 : 0;
                                            echo number_format($percentOfSales, 2) . '% ของยอดขาย';
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="card-info">
                                                <h5 class="mb-0">กำไรรวม</h5>
                                                <small class="text-muted">ผลต่างระหว่างยอดขายและต้นทุน</small>
                                            </div>
                                            <div class="card-icon">
                                                <span class="badge bg-label-success rounded p-2">
                                                    <i class="ti ti-chart-bar ti-sm"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <h3 class="card-title mb-1"><?php echo number_format($summary['total_profit'], 2); ?> บาท</h3>
                                        <div class="progress mb-1" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: <?php echo ($summary['total_sales'] > 0) ? ($summary['total_profit'] / $summary['total_sales']) * 100 : 0; ?>%" role="progressbar" aria-valuenow="<?php echo ($summary['total_sales'] > 0) ? ($summary['total_profit'] / $summary['total_sales']) * 100 : 0; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">
                                            <?php
                                            $percentProfit = ($summary['total_sales'] > 0) ? ($summary['total_profit'] / $summary['total_sales']) * 100 : 0;
                                            echo number_format($percentProfit, 2) . '% ของยอดขาย';
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="card-info">
                                                <h5 class="mb-0">อัตรากำไร</h5>
                                                <small class="text-muted">เปอร์เซ็นต์กำไรต่อยอดขาย</small>
                                            </div>
                                            <div class="card-icon">
                                                <span class="badge bg-label-info rounded p-2">
                                                    <i class="ti ti-chart-pie ti-sm"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <h3 class="card-title mb-1"><?php echo number_format($summary['profit_margin'], 2); ?>%</h3>
                                        <div id="profitMarginChart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="card-info">
                                                <h5 class="mb-0">ชำระเงินสด</h5>
                                                <small class="text-muted">ยอดชำระด้วยเงินสด</small>
                                            </div>
                                            <div class="card-icon">
                                                <span class="badge bg-label-success rounded p-2">
                                                    <i class="ti ti-cash ti-sm"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <h3 class="card-title mb-1"><?php echo number_format($summary['cash_payment'], 2); ?> บาท</h3>
                                        <small class="text-muted">
                                            <?php
                                            $percentCash = ($summary['total_sales'] > 0) ? ($summary['cash_payment'] / $summary['total_sales']) * 100 : 0;
                                            echo number_format($percentCash, 2) . '% ของยอดขายทั้งหมด';
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="card-info">
                                                <h5 class="mb-0">ชำระเงินโอน</h5>
                                                <small class="text-muted">ยอดชำระผ่านการโอนเงิน</small>
                                            </div>
                                            <div class="card-icon">
                                                <span class="badge bg-label-primary rounded p-2">
                                                    <i class="ti ti-brand-transfer ti-sm"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <h3 class="card-title mb-1"><?php echo number_format($summary['transfer_payment'], 2); ?> บาท</h3>
                                        <small class="text-muted">
                                            <?php
                                            $percentTransfer = ($summary['total_sales'] > 0) ? ($summary['transfer_payment'] / $summary['total_sales']) * 100 : 0;
                                            echo number_format($percentTransfer, 2) . '% ของยอดขายทั้งหมด';
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <div class="card-info">
                                                <h5 class="mb-0">ชำระบัตรเครดิต</h5>
                                                <small class="text-muted">ยอดชำระผ่านบัตรเครดิต</small>
                                            </div>
                                            <div class="card-icon">
                                                <span class="badge bg-label-info rounded p-2">
                                                    <i class="ti ti-credit-card ti-sm"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <h3 class="card-title mb-1"><?php echo number_format($summary['credit_card_payment'], 2); ?> บาท</h3>
                                        <small class="text-muted">
                                            <?php
                                            $percentCredit = ($summary['total_sales'] > 0) ? ($summary['credit_card_payment'] / $summary['total_sales']) * 100 : 0;
                                            echo number_format($percentCredit, 2) . '% ของยอดขายทั้งหมด';
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Table -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">รายการคำสั่งซื้อประจำวันที่ <?php echo date('d/m/Y', strtotime($selected_date)); ?></h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped" id="billTable">
                                    <thead>
                                        <tr>
                                            <th>เลขที่คำสั่งซื้อ</th>
                                            <th>วันที่สั่งซื้อ</th>
                                            <th>ชื่อลูกค้า</th>
                                            <th>วันที่นัดรับบริการ</th>
                                            <th>สถานะการชำระเงิน</th>
                                            <th>ยอดรวม</th>
                                            <th>ดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $result_bills->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo 'ORDER-' . str_pad($row['oc_id'], 6, '0', STR_PAD_LEFT); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($row['order_datetime'])); ?></td>
                                            <td><?php echo $row['cus_firstname'] . ' ' . $row['cus_lastname']; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($row['booking_datetime'])); ?></td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                switch ($row['order_payment']) {
                                                    case 'ยังไม่จ่ายเงิน':
                                                        $status_class = 'bg-label-warning';
                                                        break;
                                                    case 'เงินสด':
                                                    case 'เงินโอน':
                                                    case 'บัตรเครดิต':
                                                        $status_class = 'bg-label-success';
                                                        break;
                                                    default:
                                                        $status_class = 'bg-label-secondary';
                                                }
                                                ?>
                                                <span class="badge <?php echo $status_class; ?>"><?php echo $row['order_payment']; ?></span>
                                            </td>
                                            <td><?php echo number_format($row['order_net_total'], 2); ?> บาท</td>
                                            <td>
                                                <a href="edit-order.php?id=<?php echo $row['oc_id']; ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                                <a href="bill.php?id=<?php echo $row['oc_id']; ?>" class="btn btn-primary btn-sm">บิล</a>
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
                    <?php if(isset($_SESSION['branch_id'])){ include 'footer.php'; } ?>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- / Content wrapper -->
            </div>
            <?php else: ?>
            <?php include 'main.php'; ?>
            <?php endif ?>
            <!-- / Layout container -->
        </div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="../assets/vendor/libs/flatpickr/flatpickr.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var profitMargin = <?php echo ($summary['total_sales'] > 0) ? $summary['profit_margin'] : 0; ?>;
        var options = {
            series: [profitMargin],
            chart: {
                height: 150,
                type: 'radialBar',
            },
            plotOptions: {
                radialBar: {
                    hollow: {
                        size: '70%',
                    },
                    dataLabels: {
                        show: true,
                        name: {
                            show: false,
                        },
                        value: {
                            fontSize: '1.5rem',
                            fontWeight: 600,
                            offsetY: 8,
                            formatter: function (val) {
                                return val.toFixed(2) + '%';
                            }
                        }
                    }
                },
            },
            colors: ['#20c997'],
            stroke: {
                lineCap: 'round'
            },
        };

        var chart = new ApexCharts(document.querySelector("#profitMarginChart"), options);
        chart.render();
    });

    $(document).ready(function() {
        // Initialize DataTable
        $('#billTable').DataTable({
            "pageLength": 25,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
            },
            "order": [[1, "desc"]] // Sort by order date (second column) in descending order
        });

        // Initialize Flatpickr for date input
        flatpickr("#date", {
            dateFormat: "Y-m-d",
            defaultDate: "<?php echo $selected_date; ?>",
            locale: "th"
        });
    });

    // Display success message
    <?php if(isset($_SESSION['msg_ok'])){ ?>
        Swal.fire({
            icon: 'success',
            title: 'แจ้งเตือน!',
            text: '<?php echo $_SESSION['msg_ok']; ?>',
            customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
            },
            buttonsStyling: false
        });
    <?php unset($_SESSION['msg_ok']); } ?>

    // Display error message
    <?php if(isset($_SESSION['msg_error'])){ ?>
        Swal.fire({
            icon: 'error',
            title: 'แจ้งเตือน!',
            text: '<?php echo $_SESSION['msg_error']; ?>',
            customClass: {
                confirmButton: 'btn btn-danger waves-effect waves-light'
            },
            buttonsStyling: false
        });
    <?php unset($_SESSION['msg_error']); } ?>
    </script>
</body>
</html>