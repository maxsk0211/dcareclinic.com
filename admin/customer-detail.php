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
$sql_orders = "SELECT oc.*, c.course_name 
               FROM order_course oc 
               JOIN order_detail od ON oc.oc_id = od.oc_id 
               JOIN course c ON od.course_id = c.course_id 
               WHERE oc.cus_id = '$customer_id' 
               ORDER BY oc.order_datetime DESC";
$result_orders = $conn->query($sql_orders);

// ฟังก์ชันแปลงวันที่เป็น พ.ศ.
function convertToThaiDate($date) {
    $thai_months = [
        1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.', 5 => 'พ.ค.', 6 => 'มิ.ย.',
        7 => 'ก.ค.', 8 => 'ส.ค.', 9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
    ];

    $date_parts = explode(' ', $date);
    $time = isset($date_parts[1]) ? $date_parts[1] : '';
    $date_parts = explode('-', $date_parts[0]);
    
    $day = intval($date_parts[2]);
    $month = $thai_months[intval($date_parts[1])];
    $year = intval($date_parts[0]) + 543;

    return "$day $month $year" . ($time ? " $time" : "");
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
    .customer-card {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 15px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .customer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }
    .customer-image-container {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 20px auto;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .customer-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.3s ease;
    }
    .customer-image:hover {
        transform: scale(1.1);
    }
    .customer-info {
        background-color: rgba(255,255,255,0.9);
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
    }
    .customer-name {
        font-size: 1.5rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
    }
    .customer-id {
        font-size: 1rem;
        color: #666;
        margin-bottom: 20px;
    }
    .info-label {
        font-weight: bold;
        color: #4a4a4a;
    }
    .info-value {
        color: #0056b3;
    }
    .vital-signs {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .vital-title {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 15px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }
    .vital-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .vital-label {
        font-weight: 500;
    }
    .vital-value {
        font-weight: bold;
    }
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
        background-color: #f8f9fa;
        border: none;
        padding: 15px;
        font-weight: bold;
        color: #333;
    }
    .datatables-orders tbody td {
        background-color: white;
        border: none;
        padding: 15px;
        vertical-align: middle;
    }
    .datatables-orders tbody tr {
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .datatables-orders tbody tr:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
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
    /* สไตล์สำหรับ Modal */
    .modal-content {
        border-radius: 15px;
    }
    .modal-header {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }
    .modal-body {
        padding: 20px;
    }
    .modal-footer {
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px;
    }
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
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 text-center">
                                                <div class="customer-image-container">
                                                    <img src="<?php echo $customer['line_picture_url']; ?>" alt="รูปลูกค้า" class="customer-image">
                                                </div>
                                                <h5 class="customer-name"><?php echo $customer['cus_firstname'] . ' ' . $customer['cus_lastname']; ?></h5>
                                                <p class="customer-id"><?php echo $customer['cus_id_card_number']; ?></p>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="customer-info">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><span class="info-label">รหัส:</span> <span class="info-value"><?php echo $customer['cus_id']; ?></span></p>
                                                            <p><span class="info-label">เพศ:</span> <span class="info-value"><?php echo $customer['cus_gender']; ?></span></p>
                                                            <p><span class="info-label">วันเกิด:</span> <span class="info-value"><?php echo convertToThaiDate($customer['cus_birthday']); ?></span></p>
                                                            <p><span class="info-label">กรุ๊ปเลือด:</span> <span class="info-value"><?php echo $customer['cus_blood']; ?></span></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><span class="info-label">อีเมล:</span> <span class="info-value"><?php echo $customer['cus_email']; ?></span></p>
                                                            <p><span class="info-label">เบอร์โทร:</span> <span class="info-value"><?php echo $customer['cus_tel']; ?></span></p>
                                                            <p><span class="info-label">ที่อยู่:</span> <span class="info-value"><?php echo $customer['cus_address'] . ' ' . $customer['cus_district'] . ' ' . $customer['cus_city'] . ' ' . $customer['cus_province'] . ' ' . $customer['cus_postal_code']; ?></span></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="vital-signs">
                                                    <h5 class="vital-title">ข้อมูลสุขภาพ</h5>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="vital-item">
                                                                <span class="vital-label">น้ำหนัก:</span>
                                                                <span class="vital-value">70 กก.</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="vital-item">
                                                                <span class="vital-label">ส่วนสูง:</span>
                                                                <span class="vital-value">170 ซม.</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="vital-item">
                                                                <span class="vital-label">BMI:</span>
                                                                <span class="vital-value">24.22</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="vital-item">
                                                                <span class="vital-label">ความดัน:</span>
                                                                <span class="vital-value">110/70</span>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                            <th>วันที่สั่งซื้อ</th>
                                            <th>คอร์ส</th>
                                            <th>ราคา</th>
                                            <th>สถานะการชำระเงิน</th>
                                            <th>การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($order = $result_orders->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo convertToThaiDate($order['order_datetime']); ?></td>
                                                <td><?php echo $order['course_name']; ?></td>
                                                <td><?php echo number_format($order['order_net_total'], 2); ?> บาท</td>
                                                <td>
                                                    <?php
                                                    $status_class = '';
                                                    $icon = '';
                                                    switch ($order['order_payment']) {
                                                        case 'เงินสด':
                                                            $status_class = 'cash';
                                                            $icon = 'ti-money-bill';
                                                            break;
                                                        case 'บัตรเครดิต':
                                                            $status_class = 'credit-card';
                                                            $icon = 'ti-credit-card';
                                                            break;
                                                        case 'โอนเงิน':
                                                            $status_class = 'transfer';
                                                            $icon = 'ti-transfer';
                                                            break;
                                                        default:
                                                            $status_class = 'unpaid';
                                                            $icon = 'ti-alert-triangle';
                                                    }
                                                    ?>
                                                    <span class="payment-status <?php echo $status_class; ?>">
                                                        <i class="ti <?php echo $icon; ?>"></i>
                                                        <?php echo $order['order_payment']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn-details" onclick="showOrderDetails(<?php echo $order['oc_id']; ?>)">รายละเอียด</button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
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
    $('.datatables-orders').DataTable({
        dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        displayLength: 7,
        lengthMenu: [7, 10, 25, 50, 75, 100],
        buttons: [
            {
                extend: 'collection',
                className: 'btn btn-label-primary dropdown-toggle me-2',
                text: '<i class="ti ti-file-export me-sm-1"></i> <span class="d-none d-sm-inline-block">Export</span>',
                buttons: [
                    {
                        extend: 'print',
                        text: '<i class="ti ti-printer me-1"></i>Print',
                        className: 'dropdown-item',
                        exportOptions: { columns: [0, 1, 2, 3] }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="ti ti-file-text me-1"></i>Csv',
                        className: 'dropdown-item',
                        exportOptions: { columns: [0, 1, 2, 3] }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="ti ti-file-spreadsheet me-1"></i>Excel',
                        className: 'dropdown-item',
                        exportOptions: { columns: [0, 1, 2, 3] }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="ti ti-file-description me-1"></i>Pdf',
                        className: 'dropdown-item',
                        exportOptions: { columns: [0, 1, 2, 3] }
                    }
                ]
            }
        ],
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

    $('div.head-label').html('<h5 class="card-title mb-0">ประวัติการสั่งซื้อคอร์ส</h5>');
});
function showOrderDetails(orderId) {
    $.ajax({
        url: 'sql/get-order-details.php',  // สร้างไฟล์นี้เพื่อดึงข้อมูลรายละเอียดการสั่งซื้อ
        type: 'GET',
        data: { order_id: orderId },
        success: function(response) {
            $('#orderDetailsContent').html(response);
            $('#orderDetailsModal').modal('show');
        },
        error: function() {
            alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
        }
    });
}
    </script>
</body>
</html>