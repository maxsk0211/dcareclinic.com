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
        END) as total_df
    FROM service_staff_records ssr
    JOIN users u ON ssr.staff_id = u.users_id
    JOIN service_queue sq ON ssr.service_id = sq.queue_id
    JOIN order_course oc ON sq.booking_id = oc.course_bookings_id
    WHERE oc.order_payment != 'ยังไม่จ่ายเงิน'
    AND DATE(oc.order_datetime) BETWEEN ? AND ?
    GROUP BY u.users_id, ssr.staff_type
    ORDER BY u.users_fname, u.users_lname, ssr.staff_type
";

$stmt = $conn->prepare($sql_df_summary);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

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
                        <div class="card">
                            <div class="card-header">
                                <h5 class="m-0 font-weight-bold">สรุปค่า Doctor Fee (DF)</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="dfSummaryTable">
                                        <thead>
                                            <tr>
                                                <th>ชื่อ-นามสกุล</th>
                                                <th>ประเภท</th>
                                                <th>ยอดรวม DF (บาท)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr class="df-row" data-staff-id="<?php echo $row['users_id']; ?>" data-staff-type="<?php echo $row['staff_type']; ?>">
                                                <td><?php echo $row['users_fname'] . ' ' . $row['users_lname']; ?></td>
                                                <td><?php echo ($row['staff_type'] == 'doctor') ? 'แพทย์' : 'พยาบาล'; ?></td>
                                                <td><?php echo number_format($row['total_df'], 2); ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
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
    <div class="modal fade" id="dfDetailModal" tabindex="-1" aria-labelledby="dfDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dfDetailModalLabel">รายละเอียด Doctor Fee</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="dfDetailContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" onclick="printDetail()">พิมพ์</button>
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
    // Initialize DataTable
    $('#dfSummaryTable').DataTable({
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
        },
        "order": [[2, "desc"]], // Sort by total DF (third column) in descending order
        "columnDefs": [
            { "orderable": false, "targets": [0, 1] } // Disable sorting for first two columns
        ]
    });

    // Initialize Flatpickr for date inputs
    flatpickr("#start_date", {
        dateFormat: "Y-m-d",
        defaultDate: "<?php echo $start_date; ?>",
        locale: "th"
    });

    flatpickr("#end_date", {
        dateFormat: "Y-m-d",
        defaultDate: "<?php echo $end_date; ?>",
        locale: "th"
    });

    // Event listener for row click
    $('.df-row').click(function() {
        var staffId = $(this).data('staff-id');
        var staffType = $(this).data('staff-type');
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();

        // Show loading spinner
        $('#dfDetailContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

        // Show modal
        $('#dfDetailModal').modal('show');

        // AJAX call to get detailed information
        $.ajax({
            url: 'sql/get-df-details.php',
            method: 'GET',
            data: {
                staff_id: staffId,
                staff_type: staffType,
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                $('#dfDetailContent').html(response);
            },
            error: function() {
                $('#dfDetailContent').html('<div class="alert alert-danger">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>');
            }
        });
    });

    // Add hover effect to table rows
    $('.df-row').hover(
        function() {
            $(this).addClass('bg-light');
        },
        function() {
            $(this).removeClass('bg-light');
        }
    );
});

function printDetail() {
    var printContents = document.getElementById('dfDetailContent').innerHTML;
    var originalContents = document.body.innerHTML;

    var printHeader = '<div class="print-header">' +
                      '<h2>รายงานสรุป Doctor Fee</h2>' +
                      '<p>วันที่พิมพ์: ' + new Date().toLocaleDateString('th-TH') + '</p>' +
                      '</div>';

    var printFooter = '<div class="print-footer">' +
                      'พิมพ์จากระบบ D Care Clinic | หน้า 1 จาก 1' +
                      '</div>';

    var printArea = '<div id="printArea">' + printHeader + printContents + printFooter + '</div>';

    var iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    document.body.appendChild(iframe);
    iframe.contentWindow.document.open();
    iframe.contentWindow.document.write('<html><head><title>Print</title>');
    iframe.contentWindow.document.write('<style type="text/css">');
    iframe.contentWindow.document.write(`
        @page { size: A4; margin: 0; }
        body { margin: 0; color: #333; font-family: Arial, sans-serif; }
        #printArea { margin: 5mm; width: 95%; max-width: 210mm; }
        .print-header { background-color: #4e73df; color: white; padding: 10px; text-align: center; margin-bottom: 15px; }
        .print-header h2 { margin: 0; font-size: 24pt; }
        .print-header p { margin: 5px 0 0; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background-color: #4e73df; color: white; font-weight: bold; padding: 8px; font-size: 11pt; }
        td { padding: 6px; font-size: 10pt; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f8f9fc; }
        tfoot tr { background-color: #e8eaf6; font-weight: bold; }
        .summary-info { background-color: #f1f3f9; border: 1px solid #d1d3e2; padding: 10px; border-radius: 5px; }
        .print-footer { text-align: center; font-size: 8pt; color: #777; margin-top: 15px; border-top: 1px solid #ddd; padding-top: 5px; }
    `);
    iframe.contentWindow.document.write('</style></head><body>');
    iframe.contentWindow.document.write(printArea);
    iframe.contentWindow.document.write('</body></html>');
    iframe.contentWindow.document.close();

    iframe.contentWindow.focus();
    iframe.contentWindow.print();

    document.body.removeChild(iframe);
}

// Function to format numbers with commas
function formatNumber(num) {
    return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
}

// Function to show success message
function showSuccessMessage(message) {
    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ!',
        text: message,
        timer: 2000,
        showConfirmButton: false
    });
}

// Function to show error message
function showErrorMessage(message) {
    Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด!',
        text: message
    });
}
</script>
</body>
</html>