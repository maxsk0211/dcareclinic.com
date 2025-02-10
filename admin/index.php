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

// ตรวจสอบสิทธิ์การเข้าถึงข้อมูลสาขา
if(isset($_SESSION['branch_id'])){
    $userBranchId = $_SESSION['branch_id'];
}

$userPosition = $_SESSION['position_id']; // ต้องเพิ่ม position_id ใน session ตอน login

// เพิ่มการจัดการ session สำหรับสาขาที่เลือก
if (isset($_GET['branches'])) {
    if (in_array('all', $_GET['branches'])) {
        $_SESSION['selected_branches'] = ['all'];
    } else {
        $_SESSION['selected_branches'] = $_GET['branches'];
    }
} elseif (!isset($_SESSION['selected_branches'])) {
    if ($userPosition == 1) { // ถ้าเป็น admin
        $_SESSION['selected_branches'] = ['all'];
    } else {
        $_SESSION['selected_branches'] = [$userBranchId]; // default to user's branch
    }
}

// Query สำหรับดึงข้อมูลสาขาทั้งหมด
try {
    if ($userPosition == 1) {
        // Admin สามารถเห็นทุกสาขา
        $sql_branches = "SELECT branch_id, branch_name FROM branch WHERE 1=1 ORDER BY branch_name";
        $stmt_branches = $conn->prepare($sql_branches);
        if (!$stmt_branches) {
            throw new Exception('Error preparing statement: ' . $conn->error);
        }
    } else {
        // ผู้ใช้ทั่วไปเห็นเฉพาะสาขาของตัวเอง
        $sql_branches = "SELECT branch_id, branch_name FROM branch WHERE branch_id = ?";
        $stmt_branches = $conn->prepare($sql_branches);
        if (!$stmt_branches) {
            throw new Exception('Error preparing statement: ' . $conn->error);
        }
        $stmt_branches->bind_param("i", $userBranchId);
    }

    // Execute query
    if (!$stmt_branches->execute()) {
        throw new Exception('Error executing statement: ' . $stmt_branches->error);
    }

    $result_branches = $stmt_branches->get_result();
    $branches = [];
    while ($row = $result_branches->fetch_assoc()) {
        $branches[] = $row;
    }
    $stmt_branches->close();

} catch (Exception $e) {
    // Log error and show user-friendly message
    error_log($e->getMessage());
    die('เกิดข้อผิดพลาดในการดึงข้อมูลสาขา กรุณาติดต่อผู้ดูแลระบบ');
}

// สร้าง branch filter condition
$branch_filter = "";
$branch_params = [];
if (!empty($_SESSION['selected_branches']) && !in_array('all', $_SESSION['selected_branches'])) {
    $branch_placeholders = str_repeat('?,', count($_SESSION['selected_branches']) - 1) . '?';
    $branch_filter = "AND cb.branch_id IN ($branch_placeholders)";
    $branch_params = $_SESSION['selected_branches'];
    
    // ถ้าไม่ใช่ admin ให้เพิ่มเงื่อนไขจำกัดเฉพาะสาขาของตัวเอง
    if ($userPosition != 1) {
        $branch_filter = "AND cb.branch_id = ?";
        $branch_params = [$userBranchId];
    }
}

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


// ตรวจสอบรูปแบบวันที่
if (strpos($start_date, '/') !== false) {
    // ถ้าเป็นรูปแบบ DD/MM/YYYY
    $start = DateTime::createFromFormat('d/m/Y', $start_date);
    $end = DateTime::createFromFormat('d/m/Y', $end_date);
    if ($start && $end) {
        // แปลง พ.ศ. เป็น ค.ศ.
        $start->modify('-543 years');
        $end->modify('-543 years');
        $start_date = $start->format('Y-m-d');
        $end_date = $end->format('Y-m-d');
    }
} else {
    // ถ้าเป็นรูปแบบ YYYY-MM-DD ให้ใช้ค่าเดิม
    $start_date = trim($start_date);
    $end_date = trim($end_date);
}

// Debug log
error_log("Received date range: $daterange");
error_log("Parsed start date: $start_date");
error_log("Parsed end date: $end_date");


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
    JOIN course_bookings cb ON oc.course_bookings_id = cb.id
    WHERE DATE(cb.booking_datetime) BETWEEN ? AND ?
    $branch_filter";

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
    JOIN course_bookings cb ON oc.course_bookings_id = cb.id
    JOIN order_course_resources ocr ON oc.oc_id = ocr.order_id
    LEFT JOIN drug d ON ocr.resource_type = 'drug' AND ocr.resource_id = d.drug_id
    LEFT JOIN accessories a ON ocr.resource_type = 'accessory' AND ocr.resource_id = a.acc_id
    LEFT JOIN tool t ON ocr.resource_type = 'tool' AND ocr.resource_id = t.tool_id
    WHERE DATE(cb.booking_datetime) BETWEEN ? AND ?
    $branch_filter";

// คำนวณกำไร
$sql_profit = "
    SELECT 
        (SELECT SUM(order_net_total) 
         FROM order_course oc
         JOIN course_bookings cb ON oc.course_bookings_id = cb.id
         WHERE DATE(cb.booking_datetime) BETWEEN ? AND ?
         $branch_filter) -
        (SELECT COALESCE(SUM(
            CASE
                WHEN ocr.resource_type = 'drug' THEN ocr.quantity * d.drug_cost
                WHEN ocr.resource_type = 'accessory' THEN ocr.quantity * a.acc_cost
                WHEN ocr.resource_type = 'tool' THEN ocr.quantity * t.tool_cost
                ELSE 0
            END
        ), 0)
        FROM order_course oc
        JOIN course_bookings cb ON oc.course_bookings_id = cb.id
        JOIN order_course_resources ocr ON oc.oc_id = ocr.order_id
        LEFT JOIN drug d ON ocr.resource_type = 'drug' AND ocr.resource_id = d.drug_id
        LEFT JOIN accessories a ON ocr.resource_type = 'accessory' AND ocr.resource_id = a.acc_id
        LEFT JOIN tool t ON ocr.resource_type = 'tool' AND ocr.resource_id = t.tool_id
        WHERE DATE(cb.booking_datetime) BETWEEN ? AND ?
        $branch_filter) AS profit";

// Prepare parameters array for all queries
$date_params = [$start_date, $end_date];
$all_params = array_merge($date_params, $branch_params);

// Execute summary query with branch filter
$stmt_summary = $conn->prepare($sql_summary);
if (!empty($branch_params)) {
    $types = str_repeat('s', count($all_params));
    $stmt_summary->bind_param($types, ...$all_params);
} else {
    $stmt_summary->bind_param("ss", ...$date_params);
}
$stmt_summary->execute();
$result_summary = $stmt_summary->get_result();
$summary = $result_summary->fetch_assoc();
$stmt_summary->close();

// Execute cost query with branch filter
$stmt_cost = $conn->prepare($sql_cost);
if (!empty($branch_params)) {
    $types = str_repeat('s', count($all_params));
    $stmt_cost->bind_param($types, ...$all_params);
} else {
    $stmt_cost->bind_param("ss", ...$date_params);
}
$stmt_cost->execute();
$result_cost = $stmt_cost->get_result();
$cost_data = $result_cost->fetch_assoc();
$stmt_cost->close();

// Execute profit query
// Need to duplicate parameters for profit query as it uses the date range twice
$profit_params = !empty($branch_params) 
    ? array_merge($date_params, $branch_params, $date_params, $branch_params)
    : array_merge($date_params, $date_params);

$stmt_profit = $conn->prepare($sql_profit);
$types = str_repeat('s', count($profit_params));
$stmt_profit->bind_param($types, ...$profit_params);
$stmt_profit->execute();
$result_profit = $stmt_profit->get_result();
$profit_data = $result_profit->fetch_assoc();
$stmt_profit->close();

// คำนวณและเพิ่มข้อมูลลงใน $summary
$summary['total_cost'] = $cost_data['total_cost'];
$summary['total_profit'] = $profit_data['profit'];
$summary['profit_margin'] = ($summary['total_sales'] > 0) 
    ? ($summary['total_profit'] / $summary['total_sales']) * 100 
    : 0;

// Query สำหรับตารางบิล
$sql_bills = "
    SELECT 
        oc.oc_id, 
        oc.order_datetime,
        cb.booking_datetime, 
        c.cus_firstname, 
        c.cus_lastname, 
        oc.order_payment, 
        oc.order_net_total,
        b.branch_name,
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
    JOIN branch b ON cb.branch_id = b.branch_id
    WHERE DATE(cb.booking_datetime) BETWEEN ? AND ?
    $branch_filter
    ORDER BY cb.booking_datetime DESC";

// Execute bills query
$stmt_bills = $conn->prepare($sql_bills);
if (!empty($branch_params)) {
    $types = str_repeat('s', count($all_params));
    $stmt_bills->bind_param($types, ...$all_params);
} else {
    $stmt_bills->bind_param("ss", ...$date_params);
}
$stmt_bills->execute();
$result_bills = $stmt_bills->get_result();
$stmt_bills->close();

// Function to format currency
function formatCurrency($amount) {
    return number_format($amount, 2);
}

// Function to calculate percentage
function calculatePercentage($value, $total) {
    return $total > 0 ? ($value / $total) * 100 : 0;
}

// ตรวจสอบการแสดงผลสาขาที่เลือก
$selected_branches_names = [];
if (isset($_SESSION['selected_branches'])) {
    if (in_array('all', $_SESSION['selected_branches'])) {
        $selected_branches_names[] = 'ทุกสาขา';
    } else {
        foreach ($branches as $branch) {
            if (in_array($branch['branch_id'], $_SESSION['selected_branches'])) {
                $selected_branches_names[] = $branch['branch_name'];
            }
        }
    }
}
$selected_branches_display = !empty($selected_branches_names) ? implode(', ', $selected_branches_names) : 'ไม่ได้เลือกสาขา';

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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Page CSS -->

    <!-- SheetJS สำหรับ Export Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

    <!-- html2pdf สำหรับ Export PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>

        <!-- เพิ่ม CSS ของ toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- เพิ่ม CSS ของ sweetalert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
    .select2-container {
        width: 100% !important;
        z-index: 9999;
    }
    .select2-dropdown {
        z-index: 10000;
    }
    .select2-container--bootstrap-5 {
        --bs-form-select-bg-img: none;
    }

    .summary-row {
        transition: all 0.3s ease;
    }

    .summary-row:hover {
        background-color: #f8f9fa;
    }

    .bg-light-primary {
        background-color: rgba(105, 108, 255, 0.05) !important;
    }

    .detail-row table {
        background-color: #ffffff;
    }

    .toggle-details {
        padding: 0;
        background: transparent;
        border: none;
        transition: transform 0.2s;
    }

    .toggle-details i {
        font-size: 1.2rem;
    }

    .detail-row td {
        padding: 0 !important;
    }

    .toggle-details:hover {
        transform: scale(1.1);
    }

    .badge.bg-label-primary {
        background-color: rgba(105, 108, 255, 0.1) !important;
        color: #696cff !important;
    }

    .badge.bg-label-info {
        background-color: rgba(3, 195, 236, 0.1) !important;
        color: #03c3ec !important;
    }

    .progress {
        border-radius: 10px;
        background-color: #eceef1;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    /* เพิ่ม CSS สำหรับ badge */
.badge {
    transition: all 0.3s ease;
}

#unpaidCount {
    animation-duration: 1s;
    animation-iteration-count: infinite;
}

/* สีและการจัดวางของ badge */
.btn-danger .badge {
    background-color: #fff !important;
    color: #dc3545 !important;
    font-weight: 600;
    min-width: 20px;
}

/* Animation effect */
@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
    }
}

.badge.animate {
    animation: pulse 1s infinite;
}

.courses-container {
    overflow: hidden;
    text-overflow: ellipsis;
}

td {
    vertical-align: middle !important;
}

.text-nowrap {
    white-space: nowrap !important;
}

.badge {
    line-height: 1.2;
    padding: 0.4em 0.6em;
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
                                    <!-- ส่วนเลือกสาขา -->
                                    <div class="col-md-6">
                                        <label for="branches" class="form-label">เลือกสาขา</label>
                                        <select class="form-select select2" id="branches" name="branches[]" multiple="multiple">
                                            <option value="all">ทุกสาขา</option>
                                            <?php foreach ($branches as $branch): ?>
                                                <option value="<?php echo htmlspecialchars($branch['branch_id']); ?>"
                                                        <?php echo (isset($_SESSION['selected_branches']) && 
                                                                  in_array($branch['branch_id'], $_SESSION['selected_branches'])) 
                                                                  ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($branch['branch_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <!-- ส่วนเลือกวันที่ -->
                                    <div class="col-md-6">
                                        <label for="daterange" class="form-label">เลือกช่วงวันที่</label>
                                        <input type="text" class="form-control" id="daterange" name="daterange" value="<?php echo $daterange; ?>">
                                    </div>

                                    <!-- ปุ่มค้นหา -->
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                                        <button type="button" class="btn btn-secondary" onclick="resetFilters()">รีเซ็ต</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- เพิ่มปุ่มรายงานต่างๆ ไว้ด้านบนของ content ในหน้า index.php -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-end gap-2">
                                            <!-- ปุ่มรายงานสรุปรายได้ -->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#incomeReportModal">
                                                <i class="ri-money-dollar-circle-line me-1"></i>
                                                สรุปรายได้
                                            </button>
                                            
                                            
                                            <!-- ปุ่มรายงานยอดค้างชำระ -->
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#unpaidReportModal">
                                                <i class="ri-time-line me-1"></i>
                                                ยอดค้างชำระ
                                                <span id="unpaidCount" class="badge bg-white text-danger ms-1" style="display: none;">0</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
                                <h5 class="card-title">สรุปการให้บริการระหว่างวันที่ 
                                    <?php 
                                        $display_start = DateTime::createFromFormat('Y-m-d', $start_date);
                                        $display_end = DateTime::createFromFormat('Y-m-d', $end_date);
                                        echo $display_start->format('d/m/') . ($display_start->format('Y') + 543);
                                        echo ' ถึง ';
                                        echo $display_end->format('d/m/') . ($display_end->format('Y') + 543);
                                    ?>
                                </h5>
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


<div class="modal fade" id="incomeReportModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">
                    <i class="ri-money-dollar-circle-line me-2"></i>รายงานสรุปรายได้แยกตามประเภทการชำระเงิน
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- ส่วนสรุป -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white mb-2">ยอดรวมทั้งหมด</h6>
                                        <h3 class="text-white mb-0" id="totalIncome">฿0.00</h3>
                                    </div>
                                    <div class="bg-primary bg-opacity-25 p-3 rounded">
                                        <i class="ri-money-dollar-circle-line text-white" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white mb-2">จำนวนรายการทั้งหมด</h6>
                                        <h3 class="text-white mb-0" id="totalTransactions">0</h3>
                                    </div>
                                    <div class="bg-info bg-opacity-25 p-3 rounded">
                                        <i class="ri-file-list-3-line text-white" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white mb-2">ยอดเฉลี่ยต่อรายการ</h6>
                                        <h3 class="text-white mb-0" id="averageIncome">฿0.00</h3>
                                    </div>
                                    <div class="bg-success bg-opacity-25 p-3 rounded">
                                        <i class="ri-calculator-line text-white" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ตารางแสดงข้อมูล -->
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">รายละเอียดรายได้ตามประเภทการชำระเงิน</h6>
                        <div>
                            <button type="button" class="btn btn-success btn-sm me-2" onclick="exportIncomeToExcel()">
                                <i class="ri-file-excel-2-line me-1"></i> Excel
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="exportIncomeToPDF()">
                                <i class="ri-file-pdf-line me-1"></i> PDF
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="incomeTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>ประเภทการชำระเงิน</th>
                                        <th class="text-end">ยอดรวม</th>
                                        <th class="text-end">เปอร์เซ็นต์</th>
                                    </tr>
                                </thead>
                                <tbody id="incomeTableBody">
                                    <!-- ข้อมูลจะถูกเพิ่มด้วย JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="unpaidReportModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">
                    <i class="ri-time-line me-2"></i>รายงานยอดค้างชำระ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Cards สรุป -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-danger">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="text-white">
                                        <h6 class="mb-0">ยอดค้างชำระรวม</h6>
                                    </div>
                                    <div class="avatar bg-white bg-opacity-10">
                                        <span class="avatar-content">
                                            <i class="ri-money-dollar-circle-line text-white fs-4"></i>
                                        </span>
                                    </div>
                                </div>
                                <h3 class="text-white mb-0" id="totalUnpaid">฿0.00</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="text-white">
                                        <h6 class="mb-0">จำนวนรายการค้างชำระ</h6>
                                    </div>
                                    <div class="avatar bg-white bg-opacity-10">
                                        <span class="avatar-content">
                                            <i class="ri-file-list-3-line text-white fs-4"></i>
                                        </span>
                                    </div>
                                </div>
                                <h3 class="text-white mb-0"><span id="totalUnpaidItems">0</span> รายการ</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="text-white">
                                        <h6 class="mb-0">จำนวนลูกค้าที่ค้างชำระ</h6>
                                    </div>
                                    <div class="avatar bg-white bg-opacity-10">
                                        <span class="avatar-content">
                                            <i class="ri-user-line text-white fs-4"></i>
                                        </span>
                                    </div>
                                </div>
                                <h3 class="text-white mb-0"><span id="totalUnpaidCustomers">0</span> คน</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-3">
                        <div class="card bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="text-white">
                                        <h6 class="mb-0">ยอดเฉลี่ยต่อรายการ</h6>
                                    </div>
                                    <div class="avatar bg-white bg-opacity-10">
                                        <span class="avatar-content">
                                            <i class="ri-calculator-line text-white fs-4"></i>
                                        </span>
                                    </div>
                                </div>
                                <h3 class="text-white mb-0" id="averageUnpaid">฿0.00</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- แยกตามระยะเวลาค้างชำระ -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">ระยะเวลาค้างชำระ</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <div class="border rounded p-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>น้อยกว่า 30 วัน</span>
                                                <span class="badge bg-label-success" id="under30Count">0</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">ยอดรวม</small>
                                                <span class="fw-semibold" id="under30Amount">฿0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="border rounded p-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>30-60 วัน</span>
                                                <span class="badge bg-label-warning" id="30to60Count">0</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">ยอดรวม</small>
                                                <span class="fw-semibold" id="30to60Amount">฿0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="border rounded p-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>61-90 วัน</span>
                                                <span class="badge bg-label-danger" id="61to90Count">0</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">ยอดรวม</small>
                                                <span class="fw-semibold" id="61to90Amount">฿0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <div class="border rounded p-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>มากกว่า 90 วัน</span>
                                                <span class="badge bg-label-dark" id="over90Count">0</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">ยอดรวม</small>
                                                <span class="fw-semibold" id="over90Amount">฿0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ตารางรายการค้างชำระ -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">รายการค้างชำระ</h5>
                        <div>
                            <button class="btn btn-success btn-sm me-2" onclick="exportUnpaidToExcel()">
                                <i class="ri-file-excel-2-line me-1"></i> Excel
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="exportUnpaidToPDF()">
                                <i class="ri-file-pdf-line me-1"></i> PDF
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="unpaidTable">
                                <thead>
                                    <tr>
                                        <th>เลขที่คำสั่งซื้อ</th>
                                        <th>วันที่สั่งซื้อ</th>
                                        <th>ลูกค้า</th>
                                        <th>เบอร์โทร</th>
                                        <th>รายการ</th>
                                        <th class="text-end">จำนวนเงิน</th>
                                        <th class="text-end">ระยะเวลา</th>
                                        <th class="text-center">สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody id="unpaidTableBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Page JS -->
<script>
$(document).ready(function() {


        // กำหนดค่าเริ่มต้นของ toastr
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    // Initialize Select2 with proper options
    $('.select2').select2({
        theme: 'bootstrap-5',
        placeholder: "เลือกสาขา",
        allowClear: true,
        escapeMarkup: function(markup) {
            return markup;
        }
    });

    // Handle 'Select All' option with simplified logic
    $('#branches').on('change', function() {
        var selected = $(this).val();
        if (selected && selected.includes('all')) {
            // Deselect other options if 'all' is selected
            $(this).find('option:not([value="all"])').prop('selected', false);
            $(this).find('option[value="all"]').prop('selected', true);
            $(this).trigger('change.select2');
        } else if (selected && selected.length > 0) {
            // Deselect 'all' if other options are selected
            $(this).find('option[value="all"]').prop('selected', false);
            $(this).trigger('change.select2');
        }
    });

    // Initialize DataTable
    $('#billTable').DataTable({
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
        },
        "order": [[1, "desc"]] // Sort by order date (second column) in descending order
    });

    // เก็บ format และ state ของวันที่ไว้ใช้ร่วมกัน
    const DATE_FORMATS = {
        display: 'DD/MM/YYYY',
        server: 'YYYY-MM-DD'
    };

    // แปลงปี ค.ศ. เป็น พ.ศ. สำหรับการแสดงผล
    function convertToBuddhistEra(date) {
        console.log('Converting to Buddhist Era:', date);
        return moment(date).add(543, 'years').format(DATE_FORMATS.display);
    }

    // แปลงปี พ.ศ. เป็น ค.ศ.
    function convertToChristianEra(date) {
        console.log('Converting to Christian Era:', date);
        return moment(date).subtract(543, 'years').format(DATE_FORMATS.server);
    }

    // Function to reset filters
    function resetFilters() {
        // Reset Select2
        $('#branches').val(null).trigger('change');
        
        // Reset daterange to current date
        var today = moment().format(DATE_FORMATS.server);
        $('#daterange').val(today + ' - ' + today);
        
        // Submit form
        $('form').submit();
    }

    // Initialize Date Range Picker
    $('#daterange').daterangepicker({
        autoUpdateInput: false,
        opens: 'left',
        locale: {
            format: DATE_FORMATS.display,
            applyLabel: 'ตกลง',
            cancelLabel: 'ยกเลิก',
            fromLabel: 'จาก',
            toLabel: 'ถึง',
            customRangeLabel: 'กำหนดเอง',
            daysOfWeek: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
            monthNames: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
                        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'],
            firstDay: 0
        },
        ranges: {
           'วันนี้': [moment(), moment()],
           'เมื่อวาน': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           '7 วันที่ผ่านมา': [moment().subtract(6, 'days'), moment()],
           '30 วันที่ผ่านมา': [moment().subtract(29, 'days'), moment()],
           'เดือนนี้': [moment().startOf('month'), moment().endOf('month')],
           'เดือนที่แล้ว': [moment().subtract(1, 'month').startOf('month'), 
                           moment().subtract(1, 'month').endOf('month')]
        }
    });

    // อัพเดทค่าใน input เมื่อเลือกช่วงวันที่
    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        console.log('DateRangePicker Selection:', 
            'Start:', picker.startDate.format(DATE_FORMATS.server),
            'End:', picker.endDate.format(DATE_FORMATS.server)
        );
        
        var displayRange = convertToBuddhistEra(picker.startDate) + ' - ' + 
                          convertToBuddhistEra(picker.endDate);
        $(this).val(displayRange);

        // ส่งค่า ค.ศ. ไปยังเซิร์ฟเวอร์
        window.location.href = '?daterange=' + 
            picker.startDate.format(DATE_FORMATS.server) + ' - ' + 
            picker.endDate.format(DATE_FORMATS.server);
    });

    // ล้างค่าใน input เมื่อกดยกเลิก
    $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    // จัดการค่าเริ่มต้นของ date range
    var initialDateRange = $('#daterange').val();
    console.log('Initial date range value:', initialDateRange);

    if (initialDateRange) {
        var dates = initialDateRange.split(' - ');
        console.log('Split dates:', dates);
        
        // ตรวจสอบรูปแบบวันที่ที่ได้รับ
        if (dates[0].includes('/')) {
            // ถ้าเป็นรูปแบบ DD/MM/YYYY (พ.ศ.)
            console.log('Date format is DD/MM/YYYY');
            var start = moment(dates[0], DATE_FORMATS.display).subtract(543, 'years');
            var end = moment(dates[1], DATE_FORMATS.display).subtract(543, 'years');
        } else {
            // ถ้าเป็นรูปแบบ YYYY-MM-DD (ค.ศ.)
            console.log('Date format is YYYY-MM-DD');
            var start = moment(dates[0], DATE_FORMATS.server);
            var end = moment(dates[1], DATE_FORMATS.server);
        }
        
        console.log('Parsed dates (Christian Era):', 
            'Start:', start.format(DATE_FORMATS.server),
            'End:', end.format(DATE_FORMATS.server)
        );

        if (start.isValid() && end.isValid()) {
            // แสดงผลในรูปแบบ พ.ศ.
            var displayRange = convertToBuddhistEra(start) + ' - ' + convertToBuddhistEra(end);
            console.log('Setting display range to:', displayRange);
            $('#daterange').val(displayRange);

            // Set daterangepicker dates
            var picker = $('#daterange').data('daterangepicker');
            if (picker) {
                picker.setStartDate(start);
                picker.setEndDate(end);
            }
        } else {
            console.error('Invalid dates:', dates);
            var today = moment();
            var displayRange = convertToBuddhistEra(today) + ' - ' + convertToBuddhistEra(today);
            console.log('Using today:', displayRange);
            $('#daterange').val(displayRange);

            // Set daterangepicker to today
            var picker = $('#daterange').data('daterangepicker');
            if (picker) {
                picker.setStartDate(today);
                picker.setEndDate(today);
            }
        }
    }
});


///////////////////////////////////////////////////////////////////////////////

// ฟังก์ชันสำหรับรายงานสรุปรายได้
function loadIncomeReport() {
    // ดึงค่าวันที่จาก daterange
    let daterange = $('#daterange').val();
    console.log("Daterange value:", daterange); // debug

    $.ajax({
        url: 'sql/get-income-report.php',
        type: 'GET',
        data: { daterange: daterange }, // ส่งค่า daterange ไปทั้งสตริง
        success: function(response) {
            if (response.success) {
                updateIncomeSummary(response.summary);
                updateIncomeTable(response.details);
            } else {
                showErrorAlert(response.message || 'ไม่สามารถโหลดข้อมูลได้');
            }
        },
        error: function() {
            showErrorAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
        }
    });
}

function updateIncomeSummary(summary) {
    $('#totalIncome').text(formatCurrency(summary.total));
    $('#totalTransactions').text(formatNumber(summary.transactions));
    $('#averageIncome').text(formatCurrency(summary.average));
}

function updateIncomeTable(details) {
    const tbody = $('#incomeTableBody');
    tbody.empty();
    
    details.forEach(item => {
        // แถวสรุปของแต่ละประเภทการชำระเงิน
        const mainRow = $(`
            <tr class="summary-row">
                <td style="width: 30%">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-icon me-2 toggle-details">
                            <i class="ri-add-circle-fill text-primary fs-5"></i>
                        </button>
                        <div>
                            <span class="fw-semibold">${item.payment_type}</span>
                            <div class="small text-muted">${item.count} รายการ</div>
                        </div>
                    </div>
                </td>
                <td class="text-end" style="width: 25%">
                    <span class="fw-semibold">${formatCurrency(item.amount)}</span>
                </td>
                <td class="text-end" style="width: 20%">
                    <div class="d-flex align-items-center justify-content-end">
                        <div class="progress flex-grow-1 me-2" style="height: 8px; max-width: 100px">
                            <div class="progress-bar bg-primary" style="width: ${item.percentage}%"></div>
                        </div>
                        <span class="text-muted">${formatNumber(item.percentage)}%</span>
                    </div>
                </td>
            </tr>
        `);

        // แถวรายละเอียดรายการ
        const detailRow = $(`
            <tr class="detail-row" style="display: none;">
                <td colspan="4" class="p-0">
                    <div class="border-start border-4 border-primary ms-4 bg-light-primary">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead>
                                    <tr class="table-primary">
                                        <th>เลขที่คำสั่งซื้อ</th>
                                        <th>วันที่สั่งซื้อ</th>
                                        <th>วันที่ชำระเงิน</th>
                                        <th>ลูกค้า</th>
                                        <th>เบอร์โทร</th>
                                        <th>รายการ</th>
                                        <th class="text-end">จำนวนเงิน</th>
                                    </tr>
                                </thead>
                                <tbody>
                                ${item.orders.map(order => `
                                    <tr>
                                        <td>
                                            <span class="badge bg-label-primary">${order.order_id}</span>
                                        </td>
                                        <td class="text-nowrap">${formatDateTime(order.order_date)}</td>
                                        <td class="text-nowrap">${formatDateTime(order.payment_date)}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ri-user-3-line me-2 text-danger"></i>
                                                <span>${order.customer_name}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ri-phone-line me-2 text-danger"></i>
                                                <span class="text-nowrap">${order.customer_tel}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="courses-container" style="max-width: 200px;">
                                                ${order.courses.split(',').map(course => 
                                                    `<span class="badge bg-label-danger me-1 mb-1">${course.trim()}</span>`
                                                ).join('')}
                                            </div>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            ${formatCurrency(order.amount)}
                                        </td>
                                    </tr>
                                `).join('')}
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <td colspan="6" class="text-end fw-bold">รวมทั้งสิ้น:</td>
                                        <td class="text-end fw-bold">${formatCurrency(item.amount)}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        `);

        tbody.append(mainRow);
        tbody.append(detailRow);

        // Event listener สำหรับการแสดง/ซ่อนรายละเอียด
        mainRow.find('.toggle-details').click(function(e) {
            e.preventDefault();
            const icon = $(this).find('i');
            const detailRow = mainRow.next('.detail-row');
            
            detailRow.toggle();
            
            if (detailRow.is(':visible')) {
                icon.removeClass('ri-add-circle-fill').addClass('ri-subtract-circle-fill');
            } else {
                icon.removeClass('ri-subtract-circle-fill').addClass('ri-add-circle-fill');
            }
        });
    });
}


function formatDateTime(dateStr) {
    if (!dateStr) return '-';

    try {
        // แยกวันที่และเวลา
        const [date, time] = dateStr.split(' ');
        
        // แยกวัน เดือน ปี
        const [day, month, year] = date.split('/');
        
        // แปลงเป็น Date object - ต้องแปลงปีให้เป็น ค.ศ.
        const dateObj = new Date(`${year}-${month}-${day} ${time}`);
        
        // ตรวจสอบว่าวันที่ถูกต้องหรือไม่
        if (isNaN(dateObj.getTime())) {
            return '-';
        }

        // จัดรูปแบบวันที่และเวลา
        const thaiDate = new Date(dateObj).toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });

        // จัดรูปแบบเวลา
        const timeStr = time ? time : '00:00';
        
        return `${thaiDate} ${timeStr}`;

    } catch (e) {
        console.error('Error formatting date:', dateStr, e);
        return '-';
    }
}

// ฟังก์ชันช่วยจัดรูปแบบตัวเลข
function formatNumber(num) {
    return new Intl.NumberFormat('th-TH').format(num);
}

// ฟังก์ชันสำหรับรายงานสรุปยอดขาย
function loadSalesReport() {
    const dateRange = $('#daterange').val().split(' - ');
    const startDate = dateRange[0];
    const endDate = dateRange[1];

    $.ajax({
        url: 'sql/get-sales-report.php',
        type: 'GET',
        data: {
            startDate: startDate,
            endDate: endDate
        },
        success: function(response) {
            if (response.success) {
                updateSalesSummary(response.summary);
                updateSalesRankingTable(response.rankings);
                updateTopSalesTable(response.topItems);
            } else {
                showErrorAlert(response.message || 'ไม่สามารถโหลดข้อมูลได้');
            }
        },
        error: function() {
            showErrorAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
        }
    });
}

function updateSalesSummary(summary) {
    $('#totalSales').text(formatCurrency(summary.total));
    $('#totalItems').text(formatNumber(summary.items));
    $('#totalCategories').text(formatNumber(summary.categories));
}

function updateSalesRankingTable(rankings) {
    const tbody = $('#salesRankingTableBody');
    tbody.empty();
    
    rankings.forEach((item, index) => {
        const row = `
            <tr>
                <td>${index + 1}</td>
                <td>${item.category}</td>
                <td class="text-end">${formatNumber(item.quantity)}</td>
                <td class="text-end">${formatCurrency(item.amount)}</td>
                <td class="text-end">${formatNumber(item.percentage)}%</td>
            </tr>
        `;
        tbody.append(row);
    });
}

function updateTopSalesTable(topItems) {
    const tbody = $('#topSalesTableBody');
    tbody.empty();
    
    topItems.forEach((item, index) => {
        const row = `
            <tr>
                <td>${index + 1}</td>
                <td>${item.name}</td>
                <td>${item.category}</td>
                <td class="text-end">${formatNumber(item.quantity)}</td>
                <td class="text-end">${formatCurrency(item.amount)}</td>
            </tr>
        `;
        tbody.append(row);
    });
}

// ฟังก์ชันสำหรับรายงานยอดค้างชำระ
function loadUnpaidReport() {
    $.ajax({
        url: 'sql/get-unpaid-report.php',
        type: 'GET',
        data: {
            startDate: $('#daterange').data('daterangepicker').startDate.format('YYYY-MM-DD'),
            endDate: $('#daterange').data('daterangepicker').endDate.format('YYYY-MM-DD')
        },
        success: function(response) {
            if (response.success) {
                updateUnpaidSummary(response.summary);
                updateUnpaidByPeriod(response.byPeriod);
                updateUnpaidTable(response.details);
            } else {
                toastr.error(response.message || 'ไม่สามารถโหลดข้อมูลได้');
            }
        },
        error: function() {
            toastr.error('เกิดข้อผิดพลาดในการเชื่อมต่อ');
        }
    });
}

function updateUnpaidSummary(summary) {
    $('#totalUnpaid').text(formatCurrency(summary.total));
    $('#totalUnpaidItems').text(formatNumber(summary.items));
    $('#totalUnpaidCustomers').text(formatNumber(summary.customers));
    $('#averageUnpaid').text(formatCurrency(summary.average));
}

function updateUnpaidByPeriod(data) {
    // น้อยกว่า 30 วัน
    $('#under30Count').text(formatNumber(data.under30.count));
    $('#under30Amount').text(formatCurrency(data.under30.amount));

    // 30-60 วัน
    $('#30to60Count').text(formatNumber(data.days30to60.count));
    $('#30to60Amount').text(formatCurrency(data.days30to60.amount));

    // 61-90 วัน
    $('#61to90Count').text(formatNumber(data.days61to90.count));
    $('#61to90Amount').text(formatCurrency(data.days61to90.amount));

    // มากกว่า 90 วัน
    $('#over90Count').text(formatNumber(data.over90.count));
    $('#over90Amount').text(formatCurrency(data.over90.amount));
}

function updateUnpaidTable(details) {
    const tbody = $('#unpaidTableBody');
    tbody.empty();

    details.forEach(item => {
        const statusBadge = getUnpaidStatusBadge(item.days_overdue);
        const row = `
            <tr>
                <td>
                    <span class="badge bg-label-danger">${item.order_id}</span>
                </td>
                <td>${formatDateTime(item.order_date)}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="ri-user-3-line me-2 text-danger"></i>
                        ${item.customer_name}
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="ri-phone-line me-2 text-danger"></i>
                        ${item.customer_tel}
                    </div>
                </td>
                <td>
                    <div style="max-width: 250px;">
                        ${item.courses.split(',').map(course => 
                            `<span class="badge bg-label-danger me-1 mb-1">${course.trim()}</span>`
                        ).join('')}
                    </div>
                </td>
                <td class="text-end fw-semibold">
                    ${formatCurrency(item.amount)}
                </td>
                <td class="text-end">
                    ${item.days_overdue} วัน
                </td>
                <td class="text-center">
                    ${statusBadge}
                </td>

            </tr>
        `;
        tbody.append(row);
    });
}

function getUnpaidStatusBadge(days) {
    if (days <= 30) {
        return '<span class="badge bg-success">ปกติ</span>';
    } else if (days <= 60) {
        return '<span class="badge bg-warning">เกินกำหนด</span>';
    } else if (days <= 90) {
        return '<span class="badge bg-danger">ค้างนาน</span>';
    } else {
        return '<span class="badge bg-dark">ค้างนานมาก</span>';
    }
}

function recordPayment(orderId) {
    // แสดง Modal สำหรับบันทึกการชำระเงิน
    $('#paymentModal').modal('show');
    // เก็บ orderId ไว้ใน input hidden
    $('#paymentOrderId').val(orderId);
}

function sendReminder(orderId) {
    Swal.fire({
        title: 'ยืนยันการส่งการแจ้งเตือน',
        text: 'ระบบจะส่งข้อความแจ้งเตือนไปยังลูกค้า',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ส่งการแจ้งเตือน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            // ส่ง API request เพื่อส่งการแจ้งเตือน
            $.ajax({
                url: 'sql/send-payment-reminder.php',
                type: 'POST',
                data: { orderId: orderId },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('สำเร็จ', 'ส่งการแจ้งเตือนเรียบร้อยแล้ว', 'success');
                    } else {
                        Swal.fire('ผิดพลาด', response.message, 'error');
                    }
                }
            });
        }
    });
}

function updateUnpaidCount(count) {
    $('#unpaidCount').text(count);
}

// ฟังก์ชันช่วยจัดรูปแบบ
function formatCurrency(amount) {
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB'
    }).format(amount);
}

function formatNumber(number) {
    return new Intl.NumberFormat('th-TH').format(number);
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    const options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return date.toLocaleDateString('th-TH', options);
}

function getOverdueBadge(days) {
    if (days > 30) {
        return '<span class="badge bg-danger">เกินกำหนดมาก</span>';
    } else if (days > 15) {
        return '<span class="badge bg-warning">เกินกำหนด</span>';
    } else {
        return '<span class="badge bg-info">ปกติ</span>';
    }
}

function showErrorAlert(message) {
    toastr.error(message, 'ผิดพลาด', {
        timeOut: 3000,
        positionClass: 'toast-top-right',
        closeButton: true
    });
}

// Event Listeners
$(document).ready(function() {
    updateUnpaidCount();
    
    // อัพเดททุก 5 นาที
    setInterval(updateUnpaidCount, 300000);

    // เมื่อเปิด Modal
    $('#incomeReportModal').on('show.bs.modal', function() {
        loadIncomeReport();
    });

    // $('#salesReportModal').on('show.bs.modal', function() {
    //     loadSalesReport();
    // });

    $('#unpaidReportModal').on('show.bs.modal', function() {
        loadUnpaidReport();
    });

    // เมื่อ daterange มีการเปลี่ยนแปลง
    $('#daterange').on('change', function() {
        if ($('#incomeReportModal').is(':visible')) {
            loadIncomeReport();
        }
        // if ($('#salesReportModal').is(':visible')) {
        //     loadSalesReport();
        // }
        if ($('#unpaidReportModal').is(':visible')) {
            loadUnpaidReport();
        }
        updateUnpaidCount();

    });
});

$('#unpaidReportModal').on('hidden.bs.modal', function () {
    updateUnpaidCount();
});

// Export Functions
function exportToExcel(tableId, filename) {
    const table = document.getElementById(tableId);
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.table_to_sheet(table);
    XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
    XLSX.writeFile(wb, `${filename}_${formatDateFilename(new Date())}.xlsx`);
}

function exportToPDF(elementId, filename) {
    const element = document.getElementById(elementId);
    const opt = {
        margin: 1,
        filename: `${filename}_${formatDateFilename(new Date())}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
    };
    
    html2pdf().set(opt).from(element).save();
}


// ฟังก์ชันสำหรับ Export Excel
function exportIncomeToExcel() {
    // สร้าง workbook ใหม่
    const wb = XLSX.utils.book_new();
    
    // สร้างข้อมูลสำหรับ Excel
    const summaryData = [
        ['รายงานสรุปรายได้แยกตามประเภทการชำระเงิน'],
        ['วันที่: ' + $('#daterange').val()],
        [''],
        ['สรุปภาพรวม'],
        ['ยอดรวมทั้งหมด:', $('#totalIncome').text()],
        ['จำนวนรายการทั้งหมด:', $('#totalTransactions').text()],
        ['ยอดเฉลี่ยต่อรายการ:', $('#averageIncome').text()],
        [''],
        ['รายละเอียดตามประเภทการชำระเงิน'],
        ['ประเภทการชำระเงิน', 'จำนวนรายการ', 'ยอดรวม', 'เปอร์เซ็นต์']
    ];

    // เพิ่มข้อมูลจากตาราง
    $('#incomeTableBody tr').each(function() {
        const row = [];
        $(this).find('td').each(function() {
            row.push($(this).text().trim());
        });
        summaryData.push(row);
    });

    // สร้าง worksheet จากข้อมูล
    const ws = XLSX.utils.aoa_to_sheet(summaryData);

    // กำหนดความกว้างคอลัมน์
    const wscols = [
        {wch: 25}, // ประเภทการชำระเงิน
        {wch: 15}, // จำนวนรายการ
        {wch: 15}, // ยอดรวม
        {wch: 12}  // เปอร์เซ็นต์
    ];
    ws['!cols'] = wscols;

    // เพิ่ม worksheet ลงใน workbook
    XLSX.utils.book_append_sheet(wb, ws, "รายงานรายได้");

    // สร้างไฟล์ Excel และดาวน์โหลด
    const filename = `income_report_${formatDateFilename(new Date())}.xlsx`;
    XLSX.writeFile(wb, filename);
}

// ฟังก์ชันสำหรับ Export PDF
function exportIncomeToPDF() {
    // สร้าง HTML content สำหรับ PDF
    const content = `
        <div style="font-family: 'Sarabun', sans-serif;">
            <!-- ส่วนหัวรายงาน -->
            <div style="text-align: center; margin-bottom: 30px;">
                <h2 style="color: #333; margin: 0;">รายงานสรุปรายได้แยกตามประเภทการชำระเงิน</h2>
                <p style="color: #666; margin: 10px 0;">
                    วันที่: ${$('#daterange').val()}
                </p>
                <p style="color: #666; margin: 5px 0;">
                    วันที่ออกรายงาน: ${new Date().toLocaleDateString('th-TH', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })}
                </p>
            </div>

            <!-- สรุปภาพรวม -->
            <div style="margin-bottom: 30px;">
                <h3 style="color: #333; border-bottom: 2px solid #007bff; padding-bottom: 8px; margin-bottom: 15px;">
                    สรุปภาพรวม
                </h3>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #dee2e6;">
                        <p style="color: #666; margin: 0 0 5px 0;">ยอดรวมทั้งหมด</p>
                        <h4 style="color: #007bff; margin: 0;">${$('#totalIncome').text()}</h4>
                    </div>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #dee2e6;">
                        <p style="color: #666; margin: 0 0 5px 0;">จำนวนรายการทั้งหมด</p>
                        <h4 style="color: #28a745; margin: 0;">${$('#totalTransactions').text()} รายการ</h4>
                    </div>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #dee2e6;">
                        <p style="color: #666; margin: 0 0 5px 0;">ยอดเฉลี่ยต่อรายการ</p>
                        <h4 style="color: #17a2b8; margin: 0;">${$('#averageIncome').text()}</h4>
                    </div>
                </div>
            </div>

            <!-- ตารางรายละเอียด -->
            <div>
                <h3 style="color: #333; border-bottom: 2px solid #007bff; padding-bottom: 8px; margin-bottom: 15px;">
                    รายละเอียดตามประเภทการชำระเงิน
                </h3>
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <thead>
                        <tr style="background-color: #007bff; color: white;">
                            <th style="padding: 12px; border: 1px solid #dee2e6; text-align: left;">ประเภทการชำระเงิน</th>
                            <th style="padding: 12px; border: 1px solid #dee2e6; text-align: right;">จำนวนรายการ</th>
                            <th style="padding: 12px; border: 1px solid #dee2e6; text-align: right;">ยอดรวม</th>
                            <th style="padding: 12px; border: 1px solid #dee2e6; text-align: right;">เปอร์เซ็นต์</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${Array.from($('#incomeTableBody tr')).map((row, index) => `
                            <tr style="background-color: ${index % 2 === 0 ? '#f8f9fa' : '#ffffff'};">
                                <td style="padding: 12px; border: 1px solid #dee2e6;">
                                    ${$(row).find('td').eq(0).text()}
                                </td>
                                <td style="padding: 12px; border: 1px solid #dee2e6; text-align: right;">
                                    ${$(row).find('td').eq(1).text()}
                                </td>
                                <td style="padding: 12px; border: 1px solid #dee2e6; text-align: right;">
                                    ${$(row).find('td').eq(2).text()}
                                </td>
                                <td style="padding: 12px; border: 1px solid #dee2e6; text-align: right;">
                                    ${$(row).find('td').eq(3).text()}
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>

            <!-- ส่วนท้าย -->
            <div style="margin-top: 40px; text-align: right; font-size: 12px; color: #666;">
                <p style="margin: 0;">* รายงานนี้ออกโดยระบบอัตโนมัติ</p>
            </div>
        </div>
    `;

    // กำหนดค่า options สำหรับ html2pdf
    const opt = {
        margin: 1,
        filename: `income_report_${formatDateFilename(new Date())}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2,
            useCORS: true,
            letterRendering: true
        },
        jsPDF: {
            unit: 'cm',
            format: 'a4',
            orientation: 'portrait'
        },
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
    };

    // สร้าง PDF
    html2pdf().set(opt).from(content).save();
}

// ฟังก์ชันช่วยจัดรูปแบบวันที่สำหรับชื่อไฟล์
function formatDateFilename(date) {
    return date.toISOString().slice(0, 10);
}

function updateUnpaidCount() {
    const dateRange = $('#daterange').val();  // ดึงค่าช่วงวันที่ที่เลือก
    const dates = dateRange.split(' - ');
    
    $.ajax({
        url: 'sql/get-unpaid-count.php',
        type: 'GET',
        data: {
            startDate: dates[0],
            endDate: dates[1]
        },
        success: function(response) {
            if (response.success) {
                const count = response.count;
                const badge = $('#unpaidCount');
                
                if (count > 0) {
                    badge.text(count);
                    badge.show();
                } else {
                    badge.hide();
                }
            }
        }
    });
}
</script>
</body>
</html>