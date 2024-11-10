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

// Set default date range (current month)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// Query to get DF summary
$sql_df_summary = "
    SELECT 
        u.users_id,
        u.users_fname,
        u.users_lname,
        ssr.staff_type,
        SUM(CASE 
            WHEN ssr.staff_df_type = 'amount' THEN ssr.staff_df
            WHEN ssr.staff_df_type = 'percent' THEN (oc.order_net_total * ssr.staff_df / 100)
            ELSE 0
        END) as total_df,
        COUNT(DISTINCT oc.oc_id) as total_orders
    FROM service_staff_records ssr
    JOIN users u ON ssr.staff_id = u.users_id
    JOIN service_queue sq ON ssr.service_id = sq.queue_id
    JOIN order_course oc ON sq.booking_id = oc.course_bookings_id
    WHERE ssr.staff_type IN ('doctor', 'nurse')
    AND oc.order_payment != 'ยังไม่จ่ายเงิน'
    AND DATE(oc.order_datetime) BETWEEN ? AND ?
    GROUP BY u.users_id, ssr.staff_type
    ORDER BY ssr.staff_type, u.users_fname, u.users_lname";

$stmt = $conn->prepare($sql_df_summary);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result_df = $stmt->get_result();


$sql_seller_summary = "
    SELECT 
        u.users_id,
        u.users_fname,
        u.users_lname,
        ssr.staff_type,
        SUM(CASE 
            WHEN ssr.staff_df_type = 'amount' THEN ssr.staff_df
            WHEN ssr.staff_df_type = 'percent' THEN (oc.order_net_total * ssr.staff_df / 100)
            ELSE 0
        END) as total_commission,
        COUNT(DISTINCT oc.oc_id) as total_orders
    FROM service_staff_records ssr
    JOIN users u ON ssr.staff_id = u.users_id
    JOIN service_queue sq ON ssr.service_id = sq.queue_id
    JOIN order_course oc ON sq.booking_id = oc.course_bookings_id
    WHERE ssr.staff_type = 'seller'
    AND oc.order_payment != 'ยังไม่จ่ายเงิน'
    AND DATE(oc.order_datetime) BETWEEN ? AND ?
    GROUP BY u.users_id
    ORDER BY u.users_fname, u.users_lname";

$stmt = $conn->prepare($sql_seller_summary);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result_seller = $stmt->get_result();

// ประกาศตัวแปรรวมทั้งหมดไว้ด้านบน
$total_df = 0;
$total_df_orders = 0;
$total_commission = 0;
$total_seller_orders = 0;
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>สรุปค่า Doctor Fee (DF) - D Care Clinic</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />
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
    <link rel="stylesheet" href="../assets/vendor/libs/flatpickr/flatpickr.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
    
    <style>
 @media print {
    @page {
        size: A4;
        margin: 10mm; /* กำหนดระยะขอบของหน้า */
    }
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        font-size: 11pt; /* ปรับขนาดตัวอักษรหลัก */
    }
    #printArea {
        width: 100%;
        max-width: 190mm; /* ความกว้างสูงสุดของเนื้อหา */
    }
    .print-header {
        margin-bottom: 10mm;
    }
    .print-header h2 {
        font-size: 18pt;
        margin: 0;
    }
    .print-header p {
        font-size: 10pt;
        margin: 5px 0 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8pt; /* ลดขนาดตัวอักษรลงเล็กน้อย */
    }
    th, td {
        padding: 2px 3px; /* ลด padding เล็กน้อย */
        border: 1px solid #ddd;
        text-align: left;
    }
    th {
        background-color: #4e73df;
        color: white;
    }
    td:nth-child(3), td:nth-child(4), td:nth-child(5) {
        text-align: right;
    }
    .summary-info {
        margin-top: 10mm;
        font-size: 10pt;
    }
    .print-footer {
        margin-top: 10mm;
        font-size: 8pt;
    }
}
    .card {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
    .card-header {
        background-color: #4e73df;
        color: white;
        border-radius: 8px 8px 0 0;
    }
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    .table th {
        background-color: #f8f9fc;
        border-top: none;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,.02);
    }
    .df-row {
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .df-row:hover {
        background-color: #eaecf4 !important;
    }
    .modal-content {
        border-radius: 8px;
    }
    .modal-header {
        background-color: #4e73df;
        color: white;
        border-radius: 8px 8px 0 0;
    }
    .modal-body {
        padding: 2rem;
    }
    #dfDetailContent table {
        width: 100%;
        margin-bottom: 1rem;
    }
    #dfDetailContent th {
        background-color: #f8f9fc;
        font-weight: 600;
    }
    #dfDetailContent td, #dfDetailContent th {
        padding: 0.75rem;
        border-bottom: 1px solid #e3e6f0;
    }
    #dfDetailContent tfoot th {
        background-color: #4e73df;
        color: white;
    }
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
    }
  body {
        background-color: #f8f9fc;
        font-family: 'Inter', sans-serif;
    }
    .container-xxl {
        padding: 20px;
    }
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        margin-bottom: 1.5rem;
    }
    .card-header {
        background-color: #4e73df;
        color: white;
        border-bottom: 1px solid #e3e6f0;
        padding: 0.75rem 1.25rem;
        border-radius: 15px 15px 0 0;
    }
    .card-body {
        padding: 1.25rem;
    }
    .form-control, .form-select {
        border-radius: 10px;
    }
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
        border-radius: 10px;
        padding: 0.375rem 1.75rem;
        font-weight: 600;
    }
    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
    }
    .table {
        color: #858796;
    }
    .table th {
        background-color: #f8f9fc;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }
    .df-row {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .df-row:hover {
        background-color: #eaecf4 !important;
        transform: translateY(-2px);
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    .modal-content {
        border: none;
        border-radius: 15px;
    }
    .modal-header {
        background-color: #4e73df;
        color: white;
        border-radius: 15px 15px 0 0;
    }
    .modal-body {
        padding: 2rem;
    }
    #dfDetailContent table {
        width: 100%;
        margin-bottom: 1rem;
    }
    #dfDetailContent th {
        background-color: #f8f9fc;
        font-weight: 600;
    }
    #dfDetailContent td, #dfDetailContent th {
        padding: 0.75rem;
        border-bottom: 1px solid #e3e6f0;
    }
    #dfDetailContent tfoot th {
        background-color: #4e73df;
        color: white;
    }
       @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            .print-break-inside {
                break-inside: avoid;
            }
            .print-header {
                text-align: center;
                margin-bottom: 20px;
            }
            .print-header h2 {
                margin: 0;
                color: #000;
            }
            .print-header p {
                margin: 5px 0;
                font-size: 0.9em;
                color: #666;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f8f9fa !important;
                color: #000 !important;
            }
            .total-row {
                font-weight: bold;
                background-color: #f8f9fa !important;
            }
        }

        /* Enhanced modal styles */
        .modal-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-radius: 0.5rem 0.5rem 0 0;
        }
        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        .df-detail-header {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        .df-detail-info {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .df-detail-info p {
            margin: 0.5rem 0;
            flex: 0 0 48%;
        }
        .df-detail-table {
            margin-top: 1rem;
        }
        .df-detail-table th {
            background-color: #f8f9fa;
        }
        .summary-box {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
        }
        .summary-box h6 {
            margin-bottom: 0.5rem;
            color: #4e73df;
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

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Menu -->
                    <?php if(isset($_SESSION['branch_id'])){ include 'menu.php'; } ?>
                    <!-- / Menu -->

                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="py-3 mb-4"><span class="text-muted fw-light">รายงาน /</span> สรุปค่า Doctor Fee (DF)</h4>

                        <!-- Date Range Picker -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="m-0 font-weight-bold">เลือกช่วงเวลา</h5>
                            </div>
                            <div class="card-body">
                                <form action="" method="GET" class="row g-3">
                                    <div class="col-md-4">
                                        <label for="start_date" class="form-label">วันที่เริ่มต้น</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="end_date" class="form-label">วันที่สิ้นสุด</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">แสดงข้อมูล</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- DF Summary Table -->
                        <div class="card mb-4" 
                             data-branch-name="<?php echo $row_branch->branch_name ?? 'สาขาหลัก'; ?>"
                             data-user-name="<?php echo $_SESSION['users_fname'] . ' ' . $_SESSION['users_lname']; ?>">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="m-0">สรุปค่า Doctor Fee (DF)</h5>
                                <button onclick="printDfTable()" class="btn btn-primary no-print">
                                    <i class="ri-printer-line me-1"></i> พิมพ์รายงาน
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="dfSummaryTable">
                                        <thead>
                                            <tr>
                                                <th>ชื่อ-นามสกุล</th>
                                                <th>ประเภท</th>
                                                <th class="text-end">จำนวนรายการ</th>
                                                <th class="text-end">DF (บาท)</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $total_df = 0;
                                            while ($row = $result_df->fetch_assoc()): 
                                                $total_df_orders += $row['total_orders'];
                                                $total_df += $row['total_df'];
                                            ?>
                                            <tr class="df-row" 
                                                data-staff-id="<?php echo $row['users_id']; ?>" 
                                                data-staff-type="<?php echo $row['staff_type']; ?>">
                                                <td><?php echo $row['users_fname'] . ' ' . $row['users_lname']; ?></td>
                                                <td><?php echo ($row['staff_type'] == 'doctor') ? 'แพทย์' : 'พยาบาล'; ?></td>
                                                <td class="text-end"><?php echo number_format($row['total_orders']); ?></td>
                                                <td class="text-end"><?php echo number_format($row['total_df'], 2); ?></td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-primary view-details">
                                                        <i class="ri-eye-line"></i> ดูรายละเอียด
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-info">
                                                <td colspan="2" class="text-end"><strong>รวม DF ทั้งหมด</strong></td>
                                                <td class="text-end"><strong><?php echo number_format($total_df_orders); ?></strong></td>
                                                <td class="text-end"><strong><?php echo number_format($total_df, 2); ?></strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- ตารางแสดงค่านายหน้า -->
                        <div class="card mb-4" 
                             data-branch-name="<?php echo $row_branch->branch_name ?? 'สาขาหลัก'; ?>"
                             data-user-name="<?php echo $_SESSION['users_fname'] . ' ' . $_SESSION['users_lname']; ?>">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="m-0">สรุปค่านายหน้า</h5>
                                <button onclick="printCommissionTable()" class="btn btn-primary no-print">
                                    <i class="ri-printer-line me-1"></i> พิมพ์รายงาน
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="sellerSummaryTable">
                                        <thead>
                                            <tr>
                                                <th>ชื่อ-นามสกุล</th>
                                                <th class="text-end">จำนวนรายการ</th>
                                                <th class="text-end">ค่านายหน้า (บาท)</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            
                                            
                                            $total_commission = 0;
                                            $total_seller_orders = 0;  // เพิ่มตัวแปรนี้
                                            while ($row = $result_seller->fetch_assoc()): 
                                                $total_commission += $row['total_commission'];
                                                $total_seller_orders += $row['total_orders'];  // เพิ่มการคำนวณนี้
                                            ?>
                                            <tr class="seller-row" 
                                                data-staff-id="<?php echo $row['users_id']; ?>" 
                                                data-staff-type="seller">
                                                <td><?php echo $row['users_fname'] . ' ' . $row['users_lname']; ?></td>
                                                <td class="text-end"><?php echo number_format($row['total_orders']); ?></td>
                                                <td class="text-end"><?php echo number_format($row['total_commission'], 2); ?></td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-primary view-details">
                                                        <i class="ri-eye-line"></i> ดูรายละเอียด
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-info">
                                                <td class="text-end"><strong>รวมค่านายหน้าทั้งหมด</strong></td>
                                                <td class="text-end"><strong><?php echo number_format($total_seller_orders); ?></strong></td>
                                                <td class="text-end"><strong><?php echo number_format($total_commission, 2); ?></strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
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
            <!-- / Layout container -->
        </div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Modal -->
<!-- Modal สำหรับแสดงรายละเอียด -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">รายละเอียด</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="df-detail-header mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ชื่อ-นามสกุล:</strong> <span id="staffName"></span></p>
                            <p><strong>ประเภท:</strong> <span id="staffType"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>ช่วงวันที่:</strong> <span id="dateRange"></span></p>
                            <p><strong>จำนวนรายการ:</strong> <span id="totalOrders"></span></p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="detailTable">
                        <thead>
                            <tr>
                                <th>วันที่</th>
                                <th>เลขที่ Order</th>
                                <th>ชื่อลูกค้า</th>
                                <th class="text-end">ยอดขาย</th>
                                <th class="text-end" id="feeColumnHeader">จำนวนเงิน</th>
                            </tr>
                        </thead>
                        <tbody id="detailContent">
                            <!-- จะถูกเติมด้วย AJAX -->
                        </tbody>
                        <tfoot>
                            <tr class="table-info">
                                <td colspan="3" class="text-end"><strong>รวมทั้งหมด</strong></td>
                                <td class="text-end"><strong id="totalSales">0.00</strong></td>
                                <td class="text-end"><strong id="totalAmount">0.00</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" onclick="printDetail()">
                    <i class="ri-printer-line me-1"></i> พิมพ์
                </button>
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
    <script src="../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="../assets/vendor/libs/flatpickr/flatpickr.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
<script>
$(document).ready(function() {
    // Initialize DataTables
    const dfTable = $('#dfSummaryTable').DataTable({
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
        },
        "order": [[3, "desc"]], // Sort by DF amount
        "columnDefs": [
            { 
                "targets": [-1], // Actions column
                "orderable": false 
            },
            {
                "targets": [2, 3], // Amount columns
                "className": "text-end"
            }
        ]
    });

    const sellerTable = $('#sellerSummaryTable').DataTable({
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
        },
        "order": [[2, "desc"]], // Sort by commission amount
        "columnDefs": [
            { 
                "targets": [-1], // Actions column
                "orderable": false 
            },
            {
                "targets": [1, 2], // Amount columns
                "className": "text-end"
            }
        ]
    });

    // Handle click events for viewing details
    $('.view-details').on('click', function() {
        const row = $(this).closest('tr');
        const staffId = row.data('staff-id');
        const staffType = row.data('staff-type');
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();

        // Update modal title and header based on staff type
        if (staffType === 'seller') {
            $('#modalTitle').text('รายละเอียดค่านายหน้า');
            $('#feeColumnHeader').text('ค่านายหน้า');
        } else {
            $('#modalTitle').text('รายละเอียด Doctor Fee (DF)');
            $('#feeColumnHeader').text('DF');
        }

        // Update modal info
        $('#staffName').text(row.find('td:first').text());
        $('#staffType').text(staffType === 'seller' ? 'นายหน้า' : 
                            staffType === 'doctor' ? 'แพทย์' : 'พยาบาล');
        $('#dateRange').text(`${formatDate(startDate)} - ${formatDate(endDate)}`);
        $('#totalOrders').text(row.find('td').eq(staffType === 'seller' ? 1 : 2).text());

        // Load details
        loadDetails(staffId, staffType, startDate, endDate);
        
        // Show modal
        $('#detailModal').modal('show');
    });

    // Initialize date pickers with Thai locale
    $('.datepicker').flatpickr({
        dateFormat: "Y-m-d",
        locale: "th",
        allowInput: true
    });
});

function loadDetails(staffId, staffType, startDate, endDate) {
    console.log('Loading details with params:', { staffId, staffType, startDate, endDate });
    
    $('#detailContent').html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary"></div></td></tr>');
    
    $.ajax({
        url: 'sql/get-service-details.php',
        method: 'GET',
        data: {
            staff_id: staffId,
            staff_type: staffType,
            start_date: startDate,
            end_date: endDate
        },
        dataType: 'json',
        success: function(data) {
            console.log('Received data:', data);
            if (data.error) {
                showError(`เกิดข้อผิดพลาด: ${data.message}`);
                return;
            }
            updateDetailTable(data);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', {
                status: status,
                error: error,
                response: xhr.responseText
            });
            showError(`ไม่สามารถโหลดข้อมูลได้: ${error}`);
        }
    });
}

function updateDetailTable(data) {
    let html = '';
    let totalSales = 0;
    let totalFee = 0;

    if (data.details && data.details.length > 0) {
        data.details.forEach(item => {
            totalSales += parseFloat(item.order_total) || 0;
            totalFee += parseFloat(item.fee_amount) || 0;

            const feeDisplay = item.fee_type === 'percent' 
                ? `${item.original_fee}% (${formatNumber(item.fee_amount)})`
                : formatNumber(item.fee_amount);

            html += `
                <tr>
                    <td>${formatDate(item.order_date)}</td>
                    <td>${item.order_number}</td>
                    <td>${item.customer_name}</td>
                    <td class="text-end">${formatNumber(item.order_total)}</td>
                    <td class="text-end">${feeDisplay}</td>
                </tr>
            `;
        });

        // Add summary row
        if (data.summary) {
            $('#totalSales').text(formatNumber(data.summary.total_sales));
            $('#totalAmount').text(formatNumber(data.summary.total_fee));
        } else {
            $('#totalSales').text(formatNumber(totalSales));
            $('#totalAmount').text(formatNumber(totalFee));
        }
    } else {
        html = `
            <tr>
                <td colspan="5" class="text-center">ไม่พบข้อมูล</td>
            </tr>
        `;
        $('#totalSales').text('0.00');
        $('#totalAmount').text('0.00');
    }

    $('#detailContent').html(html);
}

function printDfTable() {
    printTable('dfSummaryTable', 'รายงานสรุป Doctor Fee (DF)');
}

function printCommissionTable() {
    printTable('sellerSummaryTable', 'รายงานสรุปค่านายหน้า');
}

function printTable(tableId, title) {
    const startDate = $('#start_date').val();
    const endDate = $('#end_date').val();
    const today = new Date().toLocaleString('th-TH', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    // คำนวณสรุปข้อมูล
    let totalStaff = 0;
    let totalDoctors = 0;
    let totalNurses = 0;
    let totalSellers = 0;
    let totalOrders = 0;
    let totalAmount = 0;

    // Clone table and remove Actions column
    const tableClone = $(`#${tableId}`).clone();
    tableClone.find('tr').each(function() {
        $(this).find('th:last, td:last').remove(); // Remove Actions column
    });

    // คำนวณสรุปข้อมูลจาก table clone
    if (tableId === 'dfSummaryTable') {
        tableClone.find('tbody tr').each(function() {
            totalStaff++;
            if ($(this).find('td:eq(1)').text().trim() === 'แพทย์') {
                totalDoctors++;
            } else {
                totalNurses++;
            }
            totalOrders += parseInt($(this).find('td:eq(2)').text().replace(/,/g, '')) || 0;
            const feeText = $(this).find('td:eq(3)').text();
            totalAmount += parseFloat(feeText.match(/[\d,]+\.?\d*/g)[0].replace(/,/g, '')) || 0;
        });
    } else {
        tableClone.find('tbody tr').each(function() {
            totalSellers++;
            totalOrders += parseInt($(this).find('td:eq(1)').text().replace(/,/g, '')) || 0;
            const feeText = $(this).find('td:eq(2)').text();
            totalAmount += parseFloat(feeText.match(/[\d,]+\.?\d*/g)[0].replace(/,/g, '')) || 0;
        });
    }

    // รับข้อมูล session จาก PHP หรือใช้ค่า default
    const branchName = $(`#${tableId}`).closest('.card').data('branch-name') || 'สาขาหลัก';
    const userName = $(`#${tableId}`).closest('.card').data('user-name') || 'เจ้าหน้าที่ระบบ';
    
    let printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write(`
        <html>
            <head>
                <title>${title}</title>
                <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
                <style>
                    @media print {
                        body { 
                            font-family: 'Sarabun', sans-serif; 
                            font-size: 14px;
                            line-height: 1.5;
                            margin: 20px;
                        }
                        .print-header {
                            text-align: center;
                            margin-bottom: 30px;
                        }
                        .clinic-name {
                            font-size: 24px;
                            font-weight: bold;
                            margin-bottom: 5px;
                        }
                        .report-title {
                            font-size: 20px;
                            margin-bottom: 15px;
                        }
                        .report-info {
                            text-align: left;
                            margin-bottom: 20px;
                            padding: 15px;
                            border: 1px solid #ddd;
                            border-radius: 5px;
                            background-color: #f9f9f9;
                        }
                        .report-info p {
                            margin: 5px 0;
                        }
                        /* Table styles */
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 20px;
                            font-size: 12px;
                        }
                        th, td {
                            border: 1px solid #ddd;
                            padding: 8px;
                            text-align: left;
                        }
                        th {
                            background-color: #f8f9fa !important;
                            font-weight: bold;
                            white-space: nowrap;
                        }
                        td.text-end, th.text-end {
                            text-align: right;
                        }
                        .fee-percentage {
                            color: #666;
                            font-size: 0.9em;
                        }
                        tfoot tr {
                            background-color: #f8f9fa !important;
                            font-weight: bold;
                        }
                        /* Hide no-print elements */
                        .no-print { 
                            display: none !important; 
                        }
                        /* Footer styles */
                        .footer {
                            margin-top: 30px;
                            text-align: left;
                            font-size: 12px;
                            page-break-inside: avoid;
                        }
                        .footer ul {
                            padding-left: 20px;
                            margin: 10px 0;
                        }
                        .footer li {
                            margin-bottom: 5px;
                        }
                        /* Signature section */
                        .signature-section {
                            margin-top: 50px;
                            display: flex;
                            justify-content: space-between;
                            page-break-inside: avoid;
                        }
                        .signature-box {
                            text-align: center;
                            flex: 0 0 45%;
                        }
                        .signature-line {
                            border-top: 1px solid #000;
                            margin-top: 40px;
                            padding-top: 5px;
                            font-size: 14px;
                        }
                        /* Page break control */
                        .page-break-before {
                            page-break-before: always;
                        }
                        .avoid-break {
                            page-break-inside: avoid;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <div class="clinic-name"><?php echo $row_branch->branch_name; ?></div>
                    <div class="report-title">${title}</div>
                </div>
                
                <div class="report-info avoid-break">
                    <p><strong>ประจำวันที่:</strong> ${formatDate(startDate)} ถึง ${formatDate(endDate)}</p>
                    <p><strong>สาขา:</strong> ${branchName}</p>
                    ${tableId === 'dfSummaryTable' ? `
                        <p><strong>จำนวนผู้ให้บริการทั้งหมด:</strong> ${totalStaff} คน (แพทย์: ${totalDoctors} คน, พยาบาล: ${totalNurses} คน)</p>
                    ` : `
                        <p><strong>จำนวนผู้ขายทั้งหมด:</strong> ${totalSellers} คน</p>
                    `}
                    <p><strong>จำนวนรายการทั้งหมด:</strong> ${totalOrders.toLocaleString()} รายการ</p>
                    <p><strong>มูลค่ารวม:</strong> ${totalAmount.toLocaleString('th-TH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} บาท</p>
                    <p><strong>วันที่พิมพ์:</strong> ${today}</p>
                    <p><strong>พิมพ์โดย:</strong> ${userName}</p>
                </div>

                <div class="avoid-break">
                    ${tableClone[0].outerHTML}
                </div>
                
                <div class="footer">
                    <p><strong>หมายเหตุ:</strong></p>
                    <ul>
                        <li>คำนวณเฉพาะรายการที่ชำระเงินแล้วเท่านั้น</li>
                        <li>DF แบบเปอร์เซ็นต์คำนวณจากยอดขายทั้งหมด</li>
                        <li>รายงานนี้เป็นการสรุปข้อมูลการให้บริการเท่านั้น</li>
                    </ul>
                </div>

                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-line">
                            ลงชื่อ ................................................ ผู้จัดทำ<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(${userName})<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;วันที่ ${formatDate(new Date())}
                        </div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-line">
                            ลงชื่อ ................................................ ผู้ตรวจสอบ<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(............................................)<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;วันที่ ............/............/............
                        </div>
                    </div>
                </div>
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
}

function printDetail() {
    const modalTitle = $('#modalTitle').text();
    const staffName = $('#staffName').text();
    const staffType = $('#staffType').text();
    const dateRange = $('#dateRange').text();

    let printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write(`
        <html>
            <head>
                <title>${modalTitle}</title>
                <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css">
                <style>
                    @media print {
                        .print-header { margin-bottom: 20px; }
                        .staff-info { margin-bottom: 15px; }
                        .staff-info p { margin: 5px 0; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                        th, td { border: 1px solid #ddd; padding: 8px; }
                        th { background-color: #f8f9fa !important; }
                        .text-end { text-align: right; }
                        .table-info { background-color: #e3f2fd !important; }
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <h2>${modalTitle}</h2>
                    <div class="staff-info">
                        <p><strong>ชื่อ-นามสกุล:</strong> ${staffName}</p>
                        <p><strong>ประเภท:</strong> ${staffType}</p>
                        <p><strong>ช่วงวันที่:</strong> ${dateRange}</p>
                    </div>
                </div>
                ${document.getElementById('detailTable').outerHTML}
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
}

// Utility functions
function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        timeZone: 'Asia/Bangkok'
    };
    return new Date(dateString).toLocaleDateString('th-TH', options);
}

function formatNumber(number) {
    if (typeof number === 'string') {
        number = parseFloat(number);
    }
    return new Intl.NumberFormat('th-TH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(number);
}

function showError(message) {
    console.error('Showing error:', message);
    $('#detailContent').html(`
        <tr>
            <td colspan="5">
                <div class="alert alert-danger m-0">
                    <i class="ri-error-warning-line me-2"></i>
                    ${message}
                </div>
            </td>
        </tr>
    `);
}
</script>
</body>
</html>