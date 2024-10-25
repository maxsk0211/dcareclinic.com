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

// รับค่าช่วงวันที่จาก input หรือ session
if (isset($_GET['daterange'])) {
    $daterange = $_GET['daterange'];
    $_SESSION['daterange'] = $daterange;
} elseif (isset($_SESSION['daterange'])) {
    $daterange = $_SESSION['daterange'];
} else {
    $daterange = date('Y-m-d') . ' - ' . date('Y-m-d');
}

list($start_date, $end_date) = explode(' - ', $daterange);

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
    WHERE DATE(oc.order_datetime) BETWEEN ? AND ?
";

// คำนวณต้นทุนรวม
$sql_cost = "
    SELECT COALESCE(SUM(
        CASE
            WHEN ocr.resource_type = 'drug' THEN ocr.quantity * d.drug_cost
            WHEN ocr.resource_type = 'accessory' THEN ocr.quantity * a.acc_cost
            WHEN ocr.resource_type = 'tool' THEN ocr.quantity * t.tool_cost
            ELSE 0
        END
    ), 0) AS total_cost
    FROM order_course oc
    JOIN order_course_resources ocr ON oc.oc_id = ocr.order_id
    LEFT JOIN drug d ON ocr.resource_type = 'drug' AND ocr.resource_id = d.drug_id
    LEFT JOIN accessories a ON ocr.resource_type = 'accessory' AND ocr.resource_id = a.acc_id
    LEFT JOIN tool t ON ocr.resource_type = 'tool' AND ocr.resource_id = t.tool_id
    WHERE DATE(oc.order_datetime) BETWEEN ? AND ?";

// คำนวณกำไร
$sql_profit = "
    SELECT 
        (SELECT SUM(order_net_total) FROM order_course WHERE DATE(order_datetime) BETWEEN ? AND ?) -
        (SELECT COALESCE(SUM(
            CASE
                WHEN ocr.resource_type = 'drug' THEN ocr.quantity * d.drug_cost
                WHEN ocr.resource_type = 'accessory' THEN ocr.quantity * a.acc_cost
                WHEN ocr.resource_type = 'tool' THEN ocr.quantity * t.tool_cost
                ELSE 0
            END
        ), 0)
        FROM order_course oc
        JOIN order_course_resources ocr ON oc.oc_id = ocr.order_id
        LEFT JOIN drug d ON ocr.resource_type = 'drug' AND ocr.resource_id = d.drug_id
        LEFT JOIN accessories a ON ocr.resource_type = 'accessory' AND ocr.resource_id = a.acc_id
        LEFT JOIN tool t ON ocr.resource_type = 'tool' AND ocr.resource_id = t.tool_id
        WHERE DATE(oc.order_datetime) BETWEEN ? AND ?) AS profit";

// Execute summary query
$stmt_summary = $conn->prepare($sql_summary);
$stmt_summary->bind_param("ss", $start_date, $end_date);
$stmt_summary->execute();
$result_summary = $stmt_summary->get_result();
$summary = $result_summary->fetch_assoc();
$stmt_summary->close();

// Execute cost query
$stmt_cost = $conn->prepare($sql_cost);
$stmt_cost->bind_param("ss", $start_date, $end_date);
$stmt_cost->execute();
$result_cost = $stmt_cost->get_result();
$cost_data = $result_cost->fetch_assoc();
$stmt_cost->close();

// Execute profit query
$stmt_profit = $conn->prepare($sql_profit);
$stmt_profit->bind_param("ssss", $start_date, $end_date, $start_date, $end_date);
$stmt_profit->execute();
$result_profit = $stmt_profit->get_result();
$profit_data = $result_profit->fetch_assoc();
$stmt_profit->close();

// คำนวณและเพิ่มข้อมูลลงใน $summary
$summary['total_cost'] = $cost_data['total_cost'];
$summary['total_profit'] = $profit_data['profit'];
$summary['profit_margin'] = ($summary['total_sales'] > 0) ? ($summary['total_profit'] / $summary['total_sales']) * 100 : 0;

// คิวรี่ข้อมูลรายการบิลประจำวัน
$sql_bills = "SELECT oc.oc_id, oc.order_datetime, c.cus_firstname, c.cus_lastname, 
                     oc.order_payment, oc.order_net_total, cb.booking_datetime,
                     (SELECT COALESCE(SUM(
                         CASE
                             WHEN ocr.resource_type = 'drug' THEN ocr.quantity * d.drug_cost
                             WHEN ocr.resource_type = 'accessory' THEN ocr.quantity * a.acc_cost
                             WHEN ocr.resource_type = 'tool' THEN ocr.quantity * t.tool_cost
                             ELSE 0
                         END
                     ), 0)
                      FROM order_course_resources ocr
                      LEFT JOIN drug d ON ocr.resource_type = 'drug' AND ocr.resource_id = d.drug_id
                      LEFT JOIN accessories a ON ocr.resource_type = 'accessory' AND ocr.resource_id = a.acc_id
                      LEFT JOIN tool t ON ocr.resource_type = 'tool' AND ocr.resource_id = t.tool_id
                      WHERE ocr.order_id = oc.oc_id) AS total_cost
              FROM order_course oc
              JOIN customer c ON oc.cus_id = c.cus_id
              JOIN course_bookings cb ON oc.course_bookings_id = cb.id
              WHERE DATE(oc.order_datetime) BETWEEN ? AND ?
              ORDER BY oc.order_datetime DESC";

$stmt_bills = $conn->prepare($sql_bills);
$stmt_bills->bind_param("ss", $start_date, $end_date);
$stmt_bills->execute();
$result_bills = $stmt_bills->get_result();
$stmt_bills->close();

?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>สรุปการให้บริการประจำวัน - D Care Clinic</title>

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
    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />
    
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
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
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

                        <h4 class="py-3 mb-4"> สรุปการให้บริการประจำวัน</h4>
                        
                        <!-- Date Picker -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <form action="" method="GET" class="row g-3">
                                    <div class="col-md-4">
                                        <label for="daterange" class="form-label">เลือกช่วงวันที่</label>
                                        <input type="text" class="form-control" id="daterange" name="daterange" value="<?php echo $daterange; ?>">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <!-- <button type="submit" class="btn btn-primary">แสดงข้อมูล</button> -->
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
                                <h5 class="card-title">สรุปการให้บริการระหว่างวันที่ <?php echo date('d/m/Y', strtotime($start_date) + 543 * 365 * 24 * 60 * 60); ?> ถึง <?php echo date('d/m/Y', strtotime($end_date) + 543 * 365 * 24 * 60 * 60); ?></h5>
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
                                            <th>ต้นทุน</th>
                                            <th>กำไร</th>
                                            <th>ดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $result_bills->fetch_assoc()): 
                                            $profit = $row['order_net_total'] - $row['total_cost'];
                                        ?>
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
                                            <td><?php echo number_format($row['total_cost'], 2); ?> บาท</td>
                                            <td><?php echo number_format($profit, 2); ?> บาท</td>
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
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#billTable').DataTable({
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
        },
        "order": [[1, "desc"]] // Sort by order date (second column) in descending order
    });

    // แปลงปี ค.ศ. เป็น พ.ศ. สำหรับการแสดงผล
    function convertToBuddhistEra(date) {
        return moment(date).add(543, 'years').format('DD/MM/YYYY');
    }

    // แปลงปี พ.ศ. เป็น ค.ศ. สำหรับการส่งค่าไปยังเซิร์ฟเวอร์
    function convertToChristianEra(date) {
        return moment(date, 'DD/MM/YYYY').subtract(543, 'years').format('YYYY-MM-DD');
    }

    // Initialize Date Range Picker
    $('#daterange').daterangepicker({
        autoUpdateInput: false,
        opens: 'left',
        locale: {
            format: 'DD/MM/YYYY',
            applyLabel: 'ตกลง',
            cancelLabel: 'ยกเลิก',
            fromLabel: 'จาก',
            toLabel: 'ถึง',
            customRangeLabel: 'กำหนดเอง',
            daysOfWeek: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
            monthNames: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'],
            firstDay: 0
        },
        ranges: {
           'วันนี้': [moment(), moment()],
           'เมื่อวาน': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           '7 วันที่ผ่านมา': [moment().subtract(6, 'days'), moment()],
           '30 วันที่ผ่านมา': [moment().subtract(29, 'days'), moment()],
           'เดือนนี้': [moment().startOf('month'), moment().endOf('month')],
           'เดือนที่แล้ว': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    // อัพเดทค่าใน input เมื่อเลือกช่วงวันที่
    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        var startDate = convertToBuddhistEra(picker.startDate);
        var endDate = convertToBuddhistEra(picker.endDate);
        $(this).val(startDate + ' - ' + endDate);

        // ส่งค่า ค.ศ. ไปยังเซิร์ฟเวอร์
        var startChristian = picker.startDate.format('YYYY-MM-DD');
        var endChristian = picker.endDate.format('YYYY-MM-DD');
        window.location.href = '?daterange=' + startChristian + ' - ' + endChristian;
    });

    // ล้างค่าใน input เมื่อกดยกเลิก
    $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    // ถ้ามีค่าเริ่มต้นใน input ให้แสดงค่านั้น
    var initialDateRange = $('#daterange').val();
    if (initialDateRange) {
        var dates = initialDateRange.split(' - ');
        var start = moment(dates[0], 'YYYY-MM-DD');
        var end = moment(dates[1], 'YYYY-MM-DD');
        if (start.isValid() && end.isValid()) {
            $('#daterange').val(convertToBuddhistEra(start) + ' - ' + convertToBuddhistEra(end));
        } else {
            // ถ้าวันที่ไม่ถูกต้อง ให้ใช้วันที่ปัจจุบัน
            var today = moment();
            $('#daterange').val(convertToBuddhistEra(today) + ' - ' + convertToBuddhistEra(today));
        }
    } else {
        // ถ้าไม่มีค่าเริ่มต้น ให้ใช้วันที่ปัจจุบัน
        var today = moment();
        $('#daterange').val(convertToBuddhistEra(today) + ' - ' + convertToBuddhistEra(today));
    }

    // ปรับปรุง daterangepicker หลังจากตั้งค่าเริ่มต้น
    var picker = $('#daterange').data('daterangepicker');
    if (picker) {
        var dates = $('#daterange').val().split(' - ');
        picker.setStartDate(moment(dates[0], 'DD/MM/YYYY').subtract(543, 'years'));
        picker.setEndDate(moment(dates[1], 'DD/MM/YYYY').subtract(543, 'years'));
    }
});

// ... (ส่วนอื่นๆ ของ JavaScript ยังคงเหมือนเดิม)
</script>
</body>
</html>