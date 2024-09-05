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
                      GROUP_CONCAT(DISTINCT c.course_name SEPARATOR ', ') as course_names,
                      SUM(od.od_price * od.od_amount) as total_price
               FROM order_course oc 
               LEFT JOIN order_detail od ON oc.oc_id = od.oc_id 
               LEFT JOIN course c ON od.course_id = c.course_id 
               WHERE oc.cus_id = '$customer_id' 
               GROUP BY oc.oc_id
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
                                        <h5 class="health-info-title">ข้อมูลสุขภาพ</h5>
                                        <div class="health-info-grid">
                                            <div class="health-info-item">
                                                <div class="health-info-label">น้ำหนัก</div>
                                                <div class="health-info-value">70 กก.</div>
                                            </div>
                                            <div class="health-info-item">
                                                <div class="health-info-label">ส่วนสูง</div>
                                                <div class="health-info-value">170 ซม.</div>
                                            </div>
                                            <div class="health-info-item">
                                                <div class="health-info-label">BMI</div>
                                                <div class="health-info-value">24.22</div>
                                            </div>
                                            <div class="health-info-item">
                                                <div class="health-info-label">ความดัน</div>
                                                <div class="health-info-value">110/70</div>
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
                                <table class="datatables-orders table border-top table-striped-columns">
                                    <thead>
                                        <tr>
                                            <th>รหัสออเดอร์</th>
                                            <th>วันที่สั่งซื้อ</th>
                                            <th>คอร์ส</th>
                                            <th>ราคารวม</th>
                                            <th>สถานะการชำระเงิน</th>
                                            <th>การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($order = $result_orders->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo formatOrderId($order['oc_id']); ?></td>
                                                <td><?php echo convertToThaiDate($order['order_datetime']); ?></td>
                                                <td><?php echo $order['course_names']; ?></td>
                                                <td><?php echo number_format($order['total_price'], 2); ?> บาท</td>
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

    // $('div.head-label').html('<h5 class="card-title mb-0">ประวัติการสั่งซื้อคอร์ส</h5>');
});
function showOrderDetails(orderId) {
    $.ajax({
        url: 'sql/get-order-details.php',
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