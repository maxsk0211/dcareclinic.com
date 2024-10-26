<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

// เพิ่ม error reporting เพื่อ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

$queue_id = isset($_GET['queue_id']) ? intval($_GET['queue_id']) : 0;

if (!$queue_id) {
    die("ไม่พบข้อมูลคิว");
}

// ดึงข้อมูลคิวและลูกค้า
$sql = "SELECT sq.*, c.*, cb.booking_datetime
        FROM service_queue sq
        LEFT JOIN customer c ON sq.cus_id = c.cus_id
        LEFT JOIN course_bookings cb ON sq.booking_id = cb.id
        WHERE sq.queue_id = $queue_id";
$result = $conn->query($sql);

if ($result === false) {
    die("เกิดข้อผิดพลาดในการค้นหาข้อมูล: " . $conn->error);
}

if ($result->num_rows == 0) {
    die("ไม่พบข้อมูลคิวที่ระบุ");
}

$queue_data = $result->fetch_assoc();

$cus_id = $queue_data['cus_id'];

// ดึงข้อมูลคอร์สที่เคยจอง
$sql_courses = "SELECT DISTINCT od.od_id, od.course_id, c.course_name, c.course_price, cb.booking_datetime
                       
                FROM course_bookings cb
                JOIN order_course oc ON cb.id = oc.course_bookings_id
                JOIN order_detail od ON oc.oc_id = od.oc_id
                JOIN course c ON od.course_id = c.course_id
                WHERE cb.cus_id = '$cus_id'
                AND cb.booking_datetime >= CURDATE()
                AND cb.status = 'confirmed'
                ORDER BY cb.booking_datetime ASC";
$result_courses = $conn->query($sql_courses);


// รีเซ็ตตัวชี้ข้อมูลกลับไปที่จุดเริ่มต้น
$result_courses->data_seek(0);
?>
<?php
    if (!isset($order) || !isset($order['order_net_total'])) {
        $order['order_net_total'] = 0;
    }
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
    <!-- เพิ่ม head content เหมือนกับหน้าอื่นๆ -->
    <title>บริการ - D Care Clinic</title>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/sweetalert2/sweetalert2.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
    <style>
    body {
        background-color: #f8f9fa;
    }
    
    .card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 15px;
        margin-bottom: 30px;
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border-bottom: none;
        padding: 20px;
    }
    
    .card-title {
        margin-bottom: 0;
        font-weight: 600;
        font-size: 1.25rem;
    }
    
    .card-body {
        padding: 30px;
    }
    
    .order-item, .customer-info {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .order-item:hover, .customer-info:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .order-item h6, .customer-info h6 {
        color: #4e73df;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 10px;
        margin-bottom: 15px;
        font-weight: 600;
    }
    
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .order-id {
        font-size: 1.2em;
        color: #4e73df;
        margin: 0;
    }
    
    .order-info p {
        margin-bottom: 5px;
    }
    
    .course-list-title {
        margin-top: 20px;
        margin-bottom: 10px;
        color: #4e73df;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 5px;
    }
    
    .course-list {
        list-style-type: none;
        padding: 0;
        margin-bottom: 0;
    }
    
    .course-item {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 10px 15px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .course-info {
        display: flex;
        justify-content: space-between;
        width: 100%;
        margin-right: 10px;
    }
    
    .course-name {
        font-weight: 500;
        color: #333;
    }
    
    .course-price {
        font-weight: 600;
        color: #28a745;
    }
    
    .total-price {
        font-size: 1.2em;
        font-weight: 700;
        color: #4e73df;
        text-align: right;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 2px solid #e9ecef;
    }
    
    .badge {
        font-size: 0.85em;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .bg-warning {
        background-color: #ffc107 !important;
        color: #000;
    }
    
    .bg-success {
        background-color: #28a745 !important;
    }
    
    .bg-info {
        background-color: #17a2b8 !important;
    }
    
    .text-muted {
        color: #6c757d !important;
    }
    
    .customer-info strong {
        font-weight: 600;
        color: #495057;
    }
    
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
    }
    
    .btn-secondary {
        background-color: #858796;
        border-color: #858796;
    }
    
    .btn-secondary:hover {
        background-color: #717384;
        border-color: #717384;
    }
    
    .ri-calendar-2-line,
    .ri-calendar-check-line {
        margin-right: 5px;
    }
    .df-container {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .df-record {
        background-color: #ffffff;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .df-record:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .df-record p {
        margin-bottom: 5px;
        font-size: 16px;
    }
    .df-record .staff-name {
        font-weight: bold;
        color: #3366cc;
    }
    .df-record .df-amount {
        font-weight: bold;
        color: #28a745;
    }
    .df-record .remove-btn {
        margin-top: 10px;
    }
    .df-total {
        font-size: 18px;
        font-weight: bold;
        text-align: right;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 2px solid #e9ecef;
    }
    .add-df-btn {
        margin-top: 20px;
    }
    .btn-success.btn-lg {
        padding: 15px 30px;
        font-size: 18px;
        border-radius: 30px;
        transition: all 0.3s ease;
    }

    .btn-success.btn-lg:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .card-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
        padding: 20px;
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
                        <h4 class="py-3 mb-4"><span class="text-muted fw-light">บริการ /</span> รายละเอียดบริการ</h4>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title text-white"><i class="ri-user-fill mr-2"></i> ข้อมูลลูกค้า</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="customer-info">
                                            <h6>ข้อมูลส่วนตัว</h6>
                                            <p><strong>รหัสลูกค้า (HN):</strong> <?php echo 'HN-' . str_pad($queue_data['cus_id'], 6, '0', STR_PAD_LEFT); ?></p>
                                            <p><strong>ชื่อ-นามสกุล:</strong> <?php echo $queue_data['cus_firstname'] . ' ' . $queue_data['cus_lastname']; ?></p>
                                            <p><strong>ชื่อเล่น:</strong> <?php echo $queue_data['cus_nickname']; ?></p>
                                            <p><strong>เพศ:</strong> <?php echo $queue_data['cus_gender']; ?></p>
                                            <p><strong>วันเกิด:</strong> <?php echo date('d/m/Y', strtotime($queue_data['cus_birthday'])); ?></p>
                                            <p><strong>เลขบัตรประชาชน:</strong> <?php echo $queue_data['cus_id_card_number']; ?></p>
                                            <p><strong>กรุ๊ปเลือด:</strong> <?php echo $queue_data['cus_blood']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0 text-white"><i class="ri-shopping-cart-fill mr-2"></i> รายละเอียดคำสั่งซื้อสำหรับการบริการวันนี้</h5>

                                        <?php 
                                        // ดึงข้อมูลคำสั่งซื้อที่เกี่ยวข้องกับคิวปัจจุบัน
                                        $sql_order = "SELECT oc.oc_id, oc.order_datetime, oc.order_payment, oc.order_net_total, oc.order_status,
                                                             cb.booking_datetime
                                                      FROM service_queue sq
                                                      JOIN course_bookings cb ON sq.booking_id = cb.id
                                                      JOIN order_course oc ON cb.id = oc.course_bookings_id
                                                      WHERE sq.queue_id = '$queue_id'";
                                        $result_order = $conn->query($sql_order);

                                        if ($result_order->num_rows > 0): 
                                            $order = $result_order->fetch_assoc();

                                        ?>
                                        <a href="edit-order.php?id=<?php echo $order['oc_id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="ri-edit-2-line"></i> แก้ไขคำสั่งซื้อ
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($result_order->num_rows > 0): ?>
                                            <div class="order-item">
                                                <div class="order-header">

                                                    <h6 class="order-id">คำสั่งซื้อ #<?php echo 'ORDER-' . str_pad($order['oc_id'], 6, '0', STR_PAD_LEFT); ?></h6>
                                                    <span class="badge <?php echo ($order['order_payment'] == 'ยังไม่จ่ายเงิน') ? 'bg-warning' : 'bg-success'; ?>">
                                                        <?php echo $order['order_payment']; ?>
                                                    </span>
                                                </div>
                                                <div class="order-info">
                                                    <p><i class="ri-calendar-2-line"></i> <strong>วันที่สั่งซื้อ:</strong> <?php echo date('d/m/Y H:i', strtotime($order['order_datetime'])); ?></p>
                                                    <p><i class="ri-calendar-check-line"></i> <strong>วันที่นัดรับบริการ:</strong> <?php echo date('d/m/Y H:i', strtotime($order['booking_datetime'])); ?></p>
                                                </div>
                                                
                                                <h6 class="course-list-title">รายการคอร์ส:</h6>
                                                <ul class="course-list">
                                                <?php
                                                $sql_details = "SELECT od.od_id, c.course_name, od.od_amount, od.od_price
                                                FROM order_detail od
                                                JOIN course c ON od.course_id = c.course_id
                                                
                                                WHERE od.oc_id = '{$order['oc_id']}'";
                                                $result_details = $conn->query($sql_details);
                                                $result_details->data_seek(0);
                                                while($detail = $result_details->fetch_assoc()):
                                                ?>
                                                    <li class="course-item">
                                                        <div class="course-info">
                                                            <span class="course-name"><?php echo $detail['course_name']; ?></span>
                                                            <span class="course-price"><?php echo number_format($detail['od_price'], 2); ?> บาท</span>
                                                        </div>
                                                    </li>
                                                <?php endwhile; ?>
                                                </ul>
                                                <div class="total-price">
                                                    <strong>ราคารวม:</strong> <span><?php echo number_format($order['order_net_total'], 2); ?> บาท</span>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted text-center">ไม่พบข้อมูลคำสั่งซื้อสำหรับการบริการนี้</p>
                                            <div class="text-center mt-3">
                                                <button type="button" class="btn btn-primary" onclick="createOrder()">สร้างคำสั่งซื้อ</button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                    <!-- / Content -->
                    <!-- df -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title text-white">ค่าธรรมเนียมแพทย์และพยาบาล (DF)</h5>
    </div>
    <div class="card-body">
        <div class="df-container">
            <div id="dfList">
                <!-- DF records will be displayed here -->
            </div>
            <div class="df-total">รวม DF: <span id="totalDF">0</span> บาท</div>
        </div>
        <button type="button" class="btn btn-primary add-df-btn" data-bs-toggle="modal" data-bs-target="#addDFModal">
            <i class="fas fa-plus-circle"></i> เพิ่ม DF
        </button>
    </div>
    <div class="card-footer text-center">
        <button type="button" class="btn btn-success btn-lg" onclick="completeService()">
            <i class="fas fa-check-circle"></i> เสร็จสิ้น
        </button>
    </div>
</div>

<!-- Modal for adding DF -->
<div class="modal fade" id="addDFModal" tabindex="-1" aria-labelledby="addDFModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addDFModalLabel"><i class="fas fa-plus-circle"></i> เพิ่มค่าธรรมเนียม (DF)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addDFForm">
                    <div class="mb-3">
                        <label for="staffType" class="form-label">ประเภทบุคลากร</label>
                        <select class="form-select" id="staffType" required>
                            <option value="">เลือกประเภท</option>
                            <option value="doctor">แพทย์</option>
                            <option value="nurse">พยาบาล</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="staffId" class="form-label">เลือกบุคลากร</label>
                        <select class="form-select" id="staffId" required>
                            <option value="">เลือกบุคลากร</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="dfAmount" class="form-label">จำนวน DF</label>
                        <input type="number" class="form-control" id="dfAmount" required>
                    </div>
                    <div class="mb-3">
                        <label for="dfType" class="form-label">ประเภท DF</label>
                        <select class="form-select" id="dfType" required>
                            <option value="amount">บาท</option>
                            <option value="percent">เปอร์เซ็นต์</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" onclick="addDF()">เพิ่ม DF</button>
            </div>
        </div>
    </div>
</div>                    <!-- df -->
                    <!-- Footer -->
                    <?php include 'footer.php'; ?>
                    <!-- / Footer -->
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>
    <!-- Page JS -->
<script>

function msg_ok(title,text){
        Swal.fire({
        icon: 'success',
        title: title,
        text: text,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500,
        toast: true,

    });
}

function msg_error(message){
        Swal.fire({
        icon: 'error',
        title: 'ข้อมูลผิดพลาด!',
        text: message,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1500,
        toast: true,

    });
}

function createOrder() {
    $.ajax({
        url: 'sql/create-order.php',
        type: 'POST',
        data: {
            queue_id: <?php echo $queue_id; ?>,
            cus_id: <?php echo $cus_id; ?>
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                window.location.href = 'edit-order.php?id=' + response.order_id;
            } else {
                console.error('Server response:', response);
                alert('เกิดข้อผิดพลาดในการสร้างคำสั่งซื้อ: ' + response.message);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX error:', textStatus, errorThrown);
            console.log('Response Text:', jqXHR.responseText);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
        }
    });
}

function showAddDFModal() {
    $('#addDFModal').modal('show');
}

function loadStaff(type) {
    $.ajax({
        url: 'sql/get-staff.php',
        type: 'GET',
        data: { type: type },
        success: function(response) {
            const staff = JSON.parse(response);
            const select = $('#staffId');
            select.empty();
            select.append('<option value="">เลือกบุคลากร</option>');
            staff.forEach(s => {
                select.append(`<option value="${s.id}">${s.name}</option>`);
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading staff:', error);
        }
    });
}

$('#staffType').on('change', function() {
    loadStaff($(this).val());
});

function addDF() {
    const staffType = $('#staffType').val();
    const staffId = $('#staffId').val();
    const dfAmount = $('#dfAmount').val();
    const dfType = $('#dfType').val();

    $.ajax({
        url: 'sql/add-df.php',
        type: 'POST',
        data: {
            service_id: <?php echo $queue_id; ?>, // ใช้ queue_id แทน service_id
            staff_type: staffType,
            staff_id: staffId,
            df_amount: dfAmount,
            df_type: dfType
        },
        success: function(response) {
            const result = JSON.parse(response);
            if (result.success) {
                loadDFRecords();
                $('#addDFModal').modal('hide');
                msg_ok('เพิ่ม DF สำเร็จ', 'ค่าธรรมเนียมถูกเพิ่มเรียบร้อยแล้ว');
            } else {
                msg_error('เกิดข้อผิดพลาดในการเพิ่ม DF: ' + result.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error adding DF:', error);
            msg_error('เกิดข้อผิดพลาดในการเพิ่ม DF');
        }
    });
}

function loadDFRecords() {
    $.ajax({
        url: 'sql/get-df-records.php',
        type: 'GET',
        data: { service_id: <?php echo $queue_id; ?> },
        success: function(response) {
            const records = JSON.parse(response);
            const dfList = $('#dfList');
            dfList.empty();
            let totalDF = 0;

            records.forEach(record => {
            const dfAmount = record.staff_df_type === 'amount' 
                ? parseFloat(record.staff_df) 
                : (parseFloat(record.staff_df) / 100) * <?php echo json_encode($order['order_net_total']); ?>;
                
                totalDF += dfAmount;

                dfList.append(`
                    <div class="df-record">
                        <p class="staff-name">${record.staff_type === 'doctor' ? '<i class="fas fa-user-md"></i> แพทย์' : '<i class="fas fa-user-nurse"></i> พยาบาล'}: ${record.staff_name}</p>
                        <p class="df-amount">DF: ${record.staff_df_type === 'amount' 
                            ? record.staff_df + ' บาท' 
                            : record.staff_df + '% (' + (dfAmount ? dfAmount.toFixed(2) : '0.00') + ' บาท)'}</p>
                        <button class="btn btn-danger btn-sm remove-btn" onclick="removeDF(${record.staff_record_id})">
                            <i class="fas fa-trash-alt"></i> ลบ
                        </button>
                    </div>
                `);
            });

            $('#totalDF').text(totalDF.toFixed(2));
        },
        error: function(xhr, status, error) {
            console.error('Error loading DF records:', error);
        }
    });
}

function removeDF(recordId) {

    Swal.fire({
      title: 'คุณแน่ใจหรือไม่ที่จะลบข้อมูล?',
      text: "การลบจะทำให้ข้อมูลหาย ไม่สามารถกู้คืนมาได้!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'ใช่ ฉันต้องการลบข้อมูล!',
      customClass: {
        confirmButton: 'btn btn-danger me-1 waves-effect waves-light',
        cancelButton: 'btn btn-outline-secondary waves-effect'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
            url: 'sql/remove-df.php',
            type: 'POST',
            data: { record_id: recordId },
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    loadDFRecords();
                    msg_ok('ลบ DF สำเร็จ', 'ค่าธรรมเนียมถูกลบเรียบร้อยแล้ว');
                } else {
                    msg_error('เกิดข้อผิดพลาดในการลบ DF: ' + result.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error removing DF:', error);
                msg_error('เกิดข้อผิดพลาดในการลบ DF');
            }
        });

      }
    });
}

$(document).ready(function() {
    loadDFRecords();
});

function completeService() {
    Swal.fire({
        title: 'ยืนยันการเสร็จสิ้น?',
        text: "คุณแน่ใจหรือไม่ว่าต้องการเสร็จสิ้นการให้บริการนี้?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, เสร็จสิ้น!!',
        customClass: {
        confirmButton: 'btn btn-danger me-1 waves-effect waves-light',
        cancelButton: 'btn btn-outline-secondary waves-effect'
      }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'sql/complete-service.php',
                type: 'POST',
                data: { queue_id: <?php echo $queue_id; ?> },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'เสร็จสิ้น!',
                            text: 'การให้บริการเสร็จสิ้นเรียบร้อยแล้ว',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = 'queue-management.php';
                        });
                    } else {
                        Swal.fire(
                            'เกิดข้อผิดพลาด!',
                            'ไม่สามารถอัปเดตสถานะได้: ' + response.message,
                            'error'
                        );
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error completing service:', error);
                    Swal.fire(
                        'เกิดข้อผิดพลาด!',
                        'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                        'error'
                    );
                }
            });
        }
    });
}
</script>
</body>
</html>