<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    die("ไม่พบข้อมูลคำสั่งซื้อ");
}
$branch_id=$_SESSION['branch_id'];

// ดึงข้อมูลคำสั่งซื้อ
$sql = "SELECT oc.*, c.cus_firstname, c.cus_lastname, cb.booking_datetime
        FROM order_course oc
        JOIN course_bookings cb ON oc.course_bookings_id = cb.id
        JOIN customer c ON cb.cus_id = c.cus_id
        WHERE oc.oc_id = $order_id and oc.branch_id='$branch_id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("ไม่พบข้อมูลคำสั่งซื้อที่ระบุ");
}

$order = $result->fetch_assoc();
// ตรวจสอบสถานะการชำระเงิน
$isPaymentCompleted = in_array($order['order_payment'], ['เงินสด', 'บัตรเครดิต', 'เงินโอน']);

// ดึงรายละเอียดคอร์ส
$sql_details = "SELECT od.*, c.course_name, c.course_price
                FROM order_detail od
                JOIN course c ON od.course_id = c.course_id
                WHERE od.oc_id = $order_id";
$result_details = $conn->query($sql_details);


// ตรวจสอบว่าผู้ใช้เป็นผู้ดูแลระบบหรือผู้จัดการหรือไม่
$canCancelPayment = ($_SESSION['position_id'] == 1 || $_SESSION['position_id'] == 2);

function formatOrderId($orderId) {
    return 'ORDER-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>แก้ไขคำสั่งซื้อ - D Care Clinic</title>

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
        .course-item {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .course-resources {
            background-color: #e9ecef;
            border-radius: 8px;
            padding: 10px;
            margin-top: 10px;
        }
        .resource-item {
            background-color: #ffffff;
            border-radius: 5px;
            padding: 8px;
            margin-bottom: 5px;
        }
        .resource-item {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .resource-item .row {
            align-items: center;
        }
        .resource-details {
            margin-bottom: 5px;
            line-height: 1.4;
        }
        .resource-total {
            margin-top: 5px;
            text-align: right;
            color: #6c757d;
        }

        .course-resources {
            margin-top: 20px;
        }
        .course-resources h6 {
            margin-bottom: 10px;
        }
        .table {
            font-size: 0.9em;
        }
        .table th {
            background-color: #f8f9fa;
        }
    .order-info-section {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .order-info-section .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    .order-info-section .form-control,
    .order-info-section .form-select {
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 10px 15px;
        font-size: 1rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .order-info-section .form-control:focus,
    .order-info-section .form-select:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .order-info-section .form-control[readonly] {
        background-color: #e9ecef;
        opacity: 1;
    }
    .order-info-section .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 16px 12px;
    }
    .payment-pending {
        color: #ffc107;
    }
    .payment-completed {
        color: #28a745;
    }
        .order-info-section {
        background-color: #ffffff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    .order-info-section .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }
    .order-info-section .form-control,
    .order-info-section .form-select {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }
    .order-info-section .form-control:focus,
    .order-info-section .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    .order-info-section .form-control[readonly] {
        background-color: #f8f9fa;
        opacity: 1;
    }
    .payment-date {
        background-color: #e8f5e9;
        color: #2e7d32;
        padding: 10px 15px;
        border-radius: 8px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .slip-preview {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px;
        text-align: center;
    }
    .slip-preview img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }
    .btn-primary, .btn-secondary {
        padding: 10px 20px;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    .btn-primary:hover, .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .total-price {
        font-size: 1.5rem;
        font-weight: 700;
        color: #4e73df;
    }
    .slip-preview img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.3s ease;
    }
    .slip-preview img:hover {
        transform: scale(1.05);
    }
    .modal-body img {
        max-width: 100%;
        height: auto;
    }
    .payment-date {
        background-color: #e8f5e9;
        color: #2e7d32;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .payment-date p {
        margin-bottom: 5px;
    }
    .payment-date p:last-child {
        margin-bottom: 0;
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

                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="fw-bold py-3 mb-4">แก้ไขคำสั่งซื้อ #<?php echo formatOrderId($order_id); ?></h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <h5 class="card-header">รายละเอียดคำสั่งซื้อ</h5>
                                    <div class="card-body">
                                     <form id="editOrderForm" method="post" action="sql/update-order.php" enctype="multipart/form-data">
                                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                        <div class="order-info-section">
                                            <h3 class="mb-4">รายละเอียดคำสั่งซื้อ</h3>
                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <label class="form-label">ชื่อลูกค้า</label>
                                                    <input type="text" class="form-control" value="<?php echo $order['cus_firstname'] . ' ' . $order['cus_lastname']; ?>" readonly>
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <label class="form-label">วันที่นัดรับบริการ</label>
                                                    <input type="datetime-local" class="form-control" name="booking_datetime" id="booking_datetime" value="<?php echo date('Y-m-d\TH:i', strtotime($order['booking_datetime'])); ?>" readonly>
                                                </div>
                                            </div>
                                            <?php
                                            $paymentStatusClass = ($order['order_payment'] == 'ยังไม่จ่ายเงิน') ? 'text-warning' : 'text-success';
                                            ?>
                                            <div class="mb-4">
                                                <label class="form-label">สถานะการชำระเงิน</label>
                                                <input type="text" class="form-control <?php echo ($order['order_payment'] == 'ยังไม่จ่ายเงิน') ? 'text-warning' : 'text-success'; ?>" 
                                                       value="<?php echo $order['order_payment']; ?>" readonly>
                                            </div>
                                            <input type="hidden" name="order_payment" value="<?php echo $order['order_payment']; ?>">
<?php if (!empty($order['order_payment_date'])): ?>
<div class="payment-date mb-4">
    <p>จ่ายเงินแล้วเมื่อวันที่: <?php echo date('d/m/Y H:i', strtotime($order['order_payment_date'])); ?></p>
    <?php
    if (!empty($order['seller_id'])) {
        $seller_sql = "SELECT users_fname, users_lname FROM users WHERE users_id = ?";
        $seller_stmt = $conn->prepare($seller_sql);
        if ($seller_stmt) {
            $seller_stmt->bind_param("i", $order['seller_id']);
            $seller_stmt->execute();
            $seller_result = $seller_stmt->get_result();
            if ($seller_data = $seller_result->fetch_assoc()) {
                echo "<p>ผู้ขาย: " . htmlspecialchars($seller_data['users_fname'] . ' ' . $seller_data['users_lname']) . "</p>";
            }
            $seller_stmt->close();
        } else {
            echo "<p>Error preparing statement: " . $conn->error . "</p>";
        }
    }
    ?>
</div>
<?php endif; ?>
                                            
                                            <div id="slipUpload" class="mb-4" style="display: none;">
                                                <label class="form-label">อัพโหลดสลิปการโอนเงิน</label>
                                                <input type="file" class="form-control" name="payment_slip" accept="image/*">
                                            </div>
                                            <?php if (!empty($order['payment_proofs'])): ?>
                                            <div class="mb-4 slip-preview">
                                                <label class="form-label">สลิปการโอนเงินที่อัพโหลดแล้ว</label><br>
                                                <img src="../img/payment-proofs/<?php echo $order['payment_proofs']; ?>" alt="Payment Slip" class="img-fluid" onclick="showSlipModal(this.src)" style="height: 300px;">
                                            </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="text-end mt-4">
                                         <!-- เพิ่มปุ่มยกเลิกการชำระเงินถ้าผู้ใช้มีสิทธิ์และการชำระเงินเสร็จสิ้นแล้ว -->
                                        <?php if ($canCancelPayment && $isPaymentCompleted): ?>
                                            <button type="button" class="btn btn-warning" id="cancelPaymentButton">ยกเลิกการชำระเงิน</button>
                                        <?php endif; ?>
                                            <!-- <button type="button" class="btn btn-primary" id="saveChangesButton"  <?php echo $isPaymentCompleted ? 'disabled' : '';?>>บันทึกการแก้ไข</button> -->
                                        <?php if($order['order_payment_date']==null): ?>
                                            <!-- <a href="queue-management.php" class="btn btn-secondary" >ยกเลิก</a> -->
                                        <?php endif ?>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h3 class="mb-0">รายการคอร์ส</h3>
                                            <h3 class="mb-0 total-price">ราคารวม: <span id="totalPrice">0.00</span> บาท</h3>
                                        </div>
                                    </form>

                                    <!-- Modal สำหรับแสดงรูปภาพ -->
                                    <div class="modal fade" id="slipModal" tabindex="-1" aria-labelledby="slipModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="slipModalLabel">สลิปการโอนเงิน</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center">
                                                    <img src="" id="slipModalImage" alt="Payment Slip" class="img-fluid">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                            <div id="courseList">
                                                <!-- รายการคอร์สจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                            </div>
                                            <button type="button" class="btn btn-primary mb-3" onclick="addNewCourse()" <?php echo $isPaymentCompleted ? 'disabled' : '';?>>เพิ่มคอร์ส</button>
                                            

                                            <script>
                                                document.getElementById('saveChangesButton').addEventListener('click', function() {
                                                    document.getElementById('editOrderForm').submit();
                                                });
                                            </script>
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

 <!-- Template สำหรับรายการคอร์ส -->
    <template id="courseTemplate">
        <div class="course-item mb-3" data-course-id="">
            <div class="row">
                <div class="col-md-4">
                    <select class="form-select course-select" onchange="updateCourseDetails(this)">
                        <option value="">เลือกคอร์ส</option>
                        <!-- ตัวเลือกคอร์สจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control course-amount" value="1" min="1" onchange="updateTotalPrice()">
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control course-price" readonly>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger" onclick="removeCourse(this)">ลบคอร์ส</button>
                    <button type="button" class="btn btn-info" onclick="showResourceModal(this)">จัดการทรัพยากร</button>
                </div>
            </div>
            <div class="resources-table mt-2">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ประเภท</th>
                            <th>ชื่อทรัพยากร</th>
                            <th>จำนวน</th>
                            <th>หน่วย</th>
                            <th>การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- รายการทรัพยากรจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </template>

    <!-- Modal สำหรับจัดการทรัพยากร -->
    <div class="modal fade" id="resourceModal" tabindex="-1" aria-labelledby="resourceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resourceModalLabel">จัดการทรัพยากร</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addResourceForm">
                        <div class="mb-3">
                            <label for="resourceType" class="form-label">ประเภททรัพยากร</label>
                            <select class="form-select" id="resourceType" required>
                                <option value="">เลือกประเภท</option>
                                <option value="drug">ยา</option>
                                <option value="tool">เครื่องมือ</option>
                                <option value="accessory">อุปกรณ์เสริม</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="resourceId" class="form-label">ทรัพยากร</label>
                            <select class="form-select" id="resourceId" required>
                                <option value="">เลือกทรัพยากร</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="resourceQuantity" class="form-label">จำนวน</label>
                            <input type="number" class="form-control" id="resourceQuantity" required min="0.01" step="0.01">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" onclick="addResource()">เพิ่มทรัพยากร</button>
                </div>
            </div>
        </div>
    </div>

<!-- Modal สำหรับเพิ่มคอร์ส -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCourseModalLabel">เพิ่มคอร์ส</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="courseSelect" class="form-label">เลือกคอร์ส</label>
                    <select class="form-select" id="courseSelect">
                        <option value="">เลือกคอร์ส</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" onclick="confirmAddCourse()">เพิ่มคอร์ส</button>
            </div>
        </div>
    </div>
</div>


    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>


    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <!-- <script src="../assets/js/tables-datatables-basic.js"></script> -->
    <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- Core JS -->

    <!-- Page JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
    <script>
document.getElementById('addResourceForm').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        return false;
    }
});

function showSlipModal(imageSrc) {
    document.getElementById('slipModalImage').src = imageSrc;
    var slipModal = new bootstrap.Modal(document.getElementById('slipModal'));
    slipModal.show();
}

// document.getElementById('payment_method').addEventListener('change', function() {
//     const slipUpload = document.getElementById('slipUpload');
//     if (this.value === 'เงินโอน') {
//         slipUpload.style.display = 'block';
//     } else {
//         slipUpload.style.display = 'none';
//     }
// });
var isPaymentCompleted = <?php echo json_encode($isPaymentCompleted); ?>;
let currentOrderId;
let currentCourseItem;
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
function formatNumber(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function translateResourceType(type) {
    switch(type) {
        case 'drug':
            return 'ยา';
        case 'tool':
            return 'เครื่องมือ';
        case 'accessory':
            return 'อุปกรณ์';
        default:
            return type;
    }
}
function loadExistingOrder() {
    currentOrderId = document.querySelector('input[name="order_id"]').value;
    $.ajax({
        url: 'sql/get-order-details.php',
        type: 'GET',
        data: { id: currentOrderId },
        dataType: 'json',
        success: function(orderDetails) {
            console.log('Parsed order details:', orderDetails);
            $('#booking_datetime').val(orderDetails.booking_datetime);
            $('#payment_method').val(orderDetails.payment_status);
            
            orderDetails.courses.forEach(course => {
                addCourseToList(course);
                checkAndLoadDefaultResources(course.id);
            });
            
            updateTotalPrice();
        },
        error: function(xhr, status, error) {
            console.error('Error loading order details:', error);
            console.error('Response:', xhr.responseText);
            alert('เกิดข้อผิดพลาดในการโหลดข้อมูล กรุณาลองใหม่อีกครั้ง');
        }
    });
}
function checkAndLoadDefaultResources(courseId) {
    $.ajax({
        url: 'sql/check-and-load-resources.php',
        type: 'POST',
        data: {
            order_id: currentOrderId,
            course_id: courseId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (response.resourcesAdded) {
                    console.log('Default resources added for course:', courseId);
                    loadCourseResources(courseId);
                } else {
                    console.log('Resources already exist for course:', courseId);
                }
            } else {
                console.error('Error checking/loading default resources:', response.error);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', status, error);
        }
    });
}
function addCourseToList(course) {
    const courseList = document.getElementById('courseList');
    const courseItem = document.createElement('div');
    courseItem.className = 'course-item mb-3';
    courseItem.dataset.courseId = course.id;
    
    courseItem.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <label  class="form-label">ชื่อคอร์ส</label> 
                <input type="text" class="form-control course-name" value="${course.name}" readonly>
            </div>
            <div class="col-md-2">
                <label  class="form-label">จำนวน</label> 
                <input type="number" class="form-control course-amount" value="${course.amount}" min="1" onchange="updateCourse(this)" readonly>
            </div>
            <div class="col-md-3">
                <label  class="form-label">ราคา/คอร์ส</label> 
                <input type="text" class="form-control course-price" value="${formatNumber(parseFloat(course.price).toFixed(2))}" onchange="updateCourse(this)" ${isPaymentCompleted ? 'readonly' : ''}>
            </div>
            <div class="col-md-3">
                ${isPaymentCompleted ? '' : '<button type="button" class="btn btn-danger" onclick="removeCourse(this)">ลบคอร์ส</button>'}
            </div>
        </div>
        <div class="resources-table mt-2">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ประเภท</th>
                        <th>ชื่อทรัพยากร</th>
                        <th>จำนวน</th>
                        <th>หน่วย</th>
                        <th>การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- ทรัพยากรจะถูกเพิ่มที่นี่ -->
                </tbody>
            </table>
            ${isPaymentCompleted ? '' : '<button type="button" class="btn btn-info" onclick="showResourceModal(this)">เพิ่มทรัพยากร</button>'}
        </div>
    `;
    
    courseList.appendChild(courseItem);

    console.log('Course resources:', course.resources);
    
    const resourcesTable = courseItem.querySelector('.resources-table tbody');
    if (course.resources && course.resources.length > 0) {
        course.resources.forEach(resource => {
            const resourceRow = `
                <tr data-resource-id="${resource.id}">
                    <td>${translateResourceType(resource.type)}</td>
                    <td>${resource.name}</td>
                    <td><input type="number" value="${resource.quantity}" class="form-control"  min="0.01" step="0.01" onchange="updateResource(this)" ${isPaymentCompleted ? 'readonly' : ''}></td>
                    <td>${resource.unit}</td>
                    <td>${isPaymentCompleted ? '' : '<button onclick="removeResource(this)" class="btn btn-sm btn-danger">ลบ</button>'}</td>
                </tr>
            `;
            resourcesTable.insertAdjacentHTML('beforeend', resourceRow);
        });
    } else {
        resourcesTable.innerHTML = '<tr><td colspan="5">ไม่พบข้อมูลทรัพยากร</td></tr>';
    }
}

function updateCourse(input) {
    const courseItem = input.closest('.course-item');
    const courseId = courseItem.dataset.courseId;
    const amount = courseItem.querySelector('.course-amount').value;
     let price = parseFloat(courseItem.querySelector('.course-price').value.replace(/,/g, ''));
    
    // ตรวจสอบว่าราคาเป็นตัวเลขที่ถูกต้อง
    if (isNaN(price)) {
        alert('กรุณาใส่ราคาที่ถูกต้อง');
        return;
    }
    // ปัดเศษทศนิยมให้เหลือ 2 ตำแหน่ง
    price = parseFloat(price.toFixed(2));

    $.ajax({
        url: 'sql/update-order-course.php',
        type: 'POST',
        data: {
            order_id: currentOrderId,
            course_id: courseId,
            amount: amount,
            price: price
        },
        success: function(response) {
            console.log('Course updated successfully');
            courseItem.querySelector('.course-price').value = formatNumber(price.toFixed(2));
            updateTotalPrice();
            // แสดงการแจ้งเตือนด้วย SweetAlert2
            msg_ok('แก้ไขคอร์สสำเร็จ','ข้อมูลคอร์สถูกอัปเดตเรียบร้อยแล้ว');
        },
        error: function(xhr, status, error) {
            console.error('Error updating course:', error);
        }
    });
}

function removeCourse(button) {
    const courseItem = button.closest('.course-item');
    const courseId = courseItem.dataset.courseId;
    
    $.ajax({
        url: 'sql/remove-course.php',
        type: 'POST',
        data: {
            order_id: currentOrderId,
            course_id: courseId
        },
        success: function(response) {
            console.log('Course removed successfully');
            msg_ok('ลบคอร์สสำเร็จ','คอร์สถูกลบเรียบร้อยแล้ว');
            courseItem.remove();
            updateTotalPrice();
        },
        error: function(xhr, status, error) {
            console.error('Error removing course:', error);
        }
    });
}

function updateResource(input) {
    const row = input.closest('tr');
    const resourceId = row.dataset.resourceId;
    const quantity = input.value;
    const orderId = currentOrderId;
    const courseId = row.closest('.course-item').dataset.courseId;
    
    $.ajax({
        url: 'sql/update-order-resource.php',
        type: 'POST',
        data: {
            resource_id: resourceId,
            quantity: quantity,
            order_id: orderId,
            course_id: courseId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                console.log('Resource updated successfully');
                msg_ok('แก้ไขทรัพยากรสำเร็จ','ข้อมูลทรัพยากรถูกอัปเดตเรียบร้อยแล้ว');

                // อาจจะเพิ่มการแสดงผลให้ผู้ใช้ทราบว่าอัปเดตสำเร็จ
            } else {
                console.error('Error updating resource:', response.error);
                alert('เกิดข้อผิดพลาดในการอัปเดตทรัพยากร: ' + response.error);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            alert('เกิดข้อผิดพลาดในการอัปเดตทรัพยากร');
        }
    });
}

function removeResource(button) {
    const row = button.closest('tr');
    const resourceId = row.dataset.resourceId;
    const orderId = currentOrderId;
    const courseId = row.closest('.course-item').dataset.courseId;
    
    console.log('Removing resource:', { resourceId, orderId, courseId }); // เพิ่ม log นี้

    $.ajax({
        url: 'sql/remove-resource.php',
        type: 'POST',
        data: {
            resource_id: resourceId,
            order_id: orderId,
            course_id: courseId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                console.log('Resource removed successfully');
                msg_ok('ลบทรัพยากรสำเร็จ','ข้อมูลทรัพยากรถูกลบเรียบร้อยแล้ว');
                row.remove();
            } else {
                console.error('Error removing resource:', response.message);
                alert('เกิดข้อผิดพลาดในการลบทรัพยากร: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            console.log('Response Text:', xhr.responseText);
            alert('เกิดข้อผิดพลาดในการลบทรัพยากร');
        }
    });
}

function showResourceModal(button) {
    currentCourseItem = button.closest('.course-item');
    resetAddResourceModal(); // รีเซ็ต Modal ก่อนแสดง
    $('#resourceModal').modal('show');
}

function addResource() {
    const type = $('#resourceType').val();
    const id = $('#resourceId').val();
    const quantity = $('#resourceQuantity').val();
    const name = $('#resourceId option:selected').text();
    const unit = $('#resourceId option:selected').data('unit');

    if (!quantity) {
        alert('กรุณาใส่จำนวน');
        return;
    }
    $.ajax({
        url: 'sql/add-resource.php',
        type: 'POST',
        data: {
            order_id: currentOrderId,
            course_id: currentCourseItem.dataset.courseId,
            resource_type: type,
            resource_id: id,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                console.log('Resource added successfully');
                msg_ok('เพิ่มทรัพยากรสำเร็จ','ข้อมูลทรัพยากรถูกเพิ่มเรียบร้อยแล้ว');

                const newRow = `
                    <tr data-resource-id="${response.id}">
                        <td>${translateResourceType(type)}</td>
                        <td>${name}</td>
                        <td><input type="number" class="form-control" value="${quantity}" min="0.01" step="0.01" onchange="updateResource(this)" ${isPaymentCompleted ? 'readonly' : ''}></td>
                        <td>${unit}</td>
                        <td>${isPaymentCompleted ? '' : '<button onclick="removeResource(this)" class="btn btn-sm btn-danger">ลบ</button>'}</td>
                    </tr>
                `;
                currentCourseItem.querySelector('.resources-table tbody').insertAdjacentHTML('beforeend', newRow);
                // รีเซ็ต Modal
                $('#resourceType').val('').trigger('change');
                $('#resourceId').val('').trigger('change');
                $('#resourceQuantity').val('');
                $('#resourceModal').modal('hide');
            } else {
                console.error('Error adding resource:', response.message);
                alert('เกิดข้อผิดพลาดในการเพิ่มทรัพยากร: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error adding resource:', error);
            alert('เกิดข้อผิดพลาดในการเพิ่มทรัพยากร');
        }
    });
}

function updateTotalPrice() {
    let total = 0;
    document.querySelectorAll('.course-item').forEach(item => {
        const price = parseFloat(item.querySelector('.course-price').value.replace(/,/g, ''));
        const amount = parseInt(item.querySelector('.course-amount').value);
        total += price * amount;
    });
    document.getElementById('totalPrice').textContent = formatNumber(total.toFixed(2));
    
    // Update total price in database
    $.ajax({
        url: 'sql/update-total-price.php',
        type: 'POST',
        data: {
            order_id: currentOrderId,
            total_price: total
        },
        success: function(response) {
            console.log('Total price updated successfully');
        },
        error: function(xhr, status, error) {
            console.error('Error updating total price:', error);
        }
    });
}

// Load existing order when page loads
$(document).ready(function() {
    loadExistingOrder();
    loadCourseOptions();

    $('#cancelPaymentButton').on('click', function() {
        Swal.fire({
            title: 'ยืนยันการยกเลิกการชำระเงิน?',
            text: "คุณแน่ใจหรือไม่ที่จะยกเลิกการชำระเงินนี้?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ยกเลิกการชำระเงิน',
            cancelButtonText: 'ยกเลิก',
            customClass: {
                confirmButton: 'btn btn-danger me-1 waves-effect waves-light',
                cancelButton: 'btn btn-outline-secondary waves-effect'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'sql/cancel-payment.php',
                    type: 'POST',
                    data: {
                        order_id: currentOrderId
                    },
                    dataType: 'json', // ระบุ dataType เป็น json
                    success: function(response) {
                        console.log('Server response:', response);
                        
                        // ตรวจสอบว่า response เป็น string หรือไม่
                        if (typeof response === 'string') {
                            try {
                                response = JSON.parse(response);
                            } catch (e) {
                                console.error('Error parsing response:', e);
                            }
                        }

                        if (response && response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'ยกเลิกสำเร็จ!',
                                text: 'การชำระเงินได้ถูกยกเลิกแล้ว',
                                customClass: {
                                    confirmButton: 'btn btn-danger waves-effect waves-light'
                                },
                                buttonsStyling: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด!',
                                text: response.message || 'ไม่สามารถยกเลิกการชำระเงินได้',
                                customClass: {
                                    confirmButton: 'btn btn-danger waves-effect waves-light'
                                },
                                buttonsStyling: false
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error);
                        console.log('Response Text:', xhr.responseText);
                        
                        // พยายามแปลง response เป็น JSON ถ้าเป็นไปได้
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.message) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด!',
                                    text: errorResponse.message,
                                    customClass: {
                                        confirmButton: 'btn btn-danger waves-effect waves-light'
                                    },
                                    buttonsStyling: false
                                });
                                return;
                            }
                        } catch(e) {
                            console.error('Error parsing error response:', e);
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด!',
                            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                            customClass: {
                                confirmButton: 'btn btn-danger waves-effect waves-light'
                            },
                            buttonsStyling: false
                        });
                    }
                });
            }
        });
    });


});

// Auto-save when changing booking datetime or payment status
$('#booking_datetime, #payment_method').on('change', function() {
    $.ajax({
        url: 'sql/update-order-details.php',
        type: 'POST',
        data: {
            order_id: currentOrderId,
            booking_datetime: $('#booking_datetime').val(),
            payment_status: $('#payment_method').val()
        },
        success: function(response) {
            console.log('Order details updated successfully');
        },
        error: function(xhr, status, error) {
            console.error('Error updating order details:', error);
        }
    });
});

// Function to load resources for the modal
function loadResources(type) {
    $.ajax({
        url: 'sql/get-resources.php',
        type: 'GET',
        data: { type: type },
        success: function(response) {
            const resources = JSON.parse(response);
            const select = $('#resourceId');
            select.empty();
            select.append('<option value="">เลือกทรัพยากร</option>');
            resources.forEach(resource => {
                select.append(`<option value="${resource.id}" data-unit="${resource.unit_name}">${resource.name}</option>`);
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading resources:', error);
        }
    });
}

// Event listener for resource type change
$('#resourceType').on('change', function() {
    loadResources($(this).val());
});

function addNewCourse() {
    $('#addCourseModal').modal('show');
}

function confirmAddCourse() {
    const courseId = $('#courseSelect').val();
    const courseName = $('#courseSelect option:selected').text();
    const coursePrice = parseFloat($('#courseSelect option:selected').data('price'));

    if (!courseId) {
        alert('กรุณาเลือกคอร์ส');
        return;
    }

    $.ajax({
        url: 'sql/add-course.php',
        type: 'POST',
        data: {
            order_id: currentOrderId,
            course_id: courseId,
            amount: 1,
            price: coursePrice
        },
        success: function(response) {
            console.log('Raw response:', response);  // เพิ่มบรรทัดนี้เพื่อ debug
            try {
                const result = typeof response === 'string' ? JSON.parse(response) : response;
                if (result.success) {
                    const newCourse = {
                        id: courseId,
                        name: courseName,
                        amount: 1,
                        price: coursePrice,
                        resources: []
                    };
                    addCourseToList(newCourse);
                    updateTotalPrice();
                    $('#addCourseModal').modal('hide');

                    // รีเซ็ต select ใน Modal
                    $('#courseSelect').val('').trigger('change');

                    checkAndLoadDefaultResources(courseId);
                    loadCourseResources(courseId);
                } else {
                    alert('เกิดข้อผิดพลาดในการเพิ่มคอร์ส: ' + (result.error || 'Unknown error'));
                }
            } catch (e) {
                console.error('Error parsing JSON:', e);
                console.error('Raw response:', response);
                alert('เกิดข้อผิดพลาดในการประมวลผลการตอบกลับจากเซิร์ฟเวอร์');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', status, error);
            console.log('Response Text:', xhr.responseText);
            alert('เกิดข้อผิดพลาดในการเพิ่มคอร์ส: ' + error);
        }
    });
}

function loadCourseResources(courseId) {
    $.ajax({
        url: 'sql/get-course-resources.php',
        type: 'GET',
        data: { 
            order_id: currentOrderId,
            course_id: courseId 
        },
        dataType: 'json',
        success: function(data) {  // เปลี่ยนจาก resources เป็น data
            console.log("Loaded resources:", data);
            
            // ส่วนที่เหลือของโค้ดสำหรับการแสดงผลทรัพยากร
            const resourcesTableBody = document.querySelector(`.course-item[data-course-id="${courseId}"] .resources-table tbody`);
            resourcesTableBody.innerHTML = ''; // Clear existing resources
            
            data.forEach(resource => {  // ใช้ data แทน resources
                const newRow = `
                    <tr data-resource-id="${resource.id}">
                        <td>${resource.type}</td>
                        <td>${resource.name}</td>
                        <td><input type="number" value="${resource.quantity}" class="form-control" min="0.01" step="0.01" onchange="updateResource(this)" ${isPaymentCompleted ? 'readonly' : ''}></td>
                        <td>${resource.unit}</td>
                        <td>${isPaymentCompleted ? '' : '<button onclick="removeResource(this)" class="btn btn-sm btn-danger">ลบ</button>'}</td>
                    </tr>
                `;
                resourcesTableBody.insertAdjacentHTML('beforeend', newRow);
            });

            if (data.length === 0) {  // ใช้ data แทน resources
                resourcesTableBody.innerHTML = '<tr><td colspan="5">ไม่พบข้อมูลทรัพยากร</td></tr>';
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading course resources:', error);
            console.log('Response Text:', xhr.responseText);
        }
    });
}

// โหลดรายการคอร์สสำหรับ modal
function loadCourseOptions() {
    $.ajax({
        url: 'sql/get-courses.php',
        type: 'GET',
        success: function(response) {
            const courses = JSON.parse(response);
            const select = $('#courseSelect');
            select.empty();
            select.append('<option value="">เลือกคอร์ส</option>');
            courses.forEach(course => {
                select.append(`<option value="${course.id}" data-price="${course.price}">${course.name}</option>`);
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading courses:', error);
        }
    });
}

function resetAddCourseModal() {
    $('#courseSelect').val('').trigger('change');
}

function resetAddResourceModal() {
    $('#resourceType').val('').trigger('change');
    $('#resourceId').val('').trigger('change');
    $('#resourceQuantity').val('');
}

// เพิ่ม event listeners สำหรับการปิด Modal
$('#addCourseModal').on('hidden.bs.modal', function () {
    resetAddCourseModal();
});

$('#resourceModal').on('hidden.bs.modal', function () {
    resetAddResourceModal();
});




    // msg error
     <?php if(isset($_SESSION['msg_error'])){ ?>

      Swal.fire({
         icon: 'error',
         title: 'แจ้งเตือน!!',
         text: '<?php echo $_SESSION['msg_error']; ?>',
         customClass: {
              confirmButton: 'btn btn-danger waves-effect waves-light'
            },
         buttonsStyling: false

      })
    <?php unset($_SESSION['msg_error']); } ?>


    // msg ok 
    <?php if(isset($_SESSION['msg_ok'])){ ?>
      Swal.fire({
         icon: 'success',
         title: 'แจ้งเตือน!!',
         text: '<?php echo $_SESSION['msg_ok']; ?>',
         customClass: {
              confirmButton: 'btn btn-primary waves-effect waves-light'
            },
         buttonsStyling: false

      })
    <?php unset($_SESSION['msg_ok']); } ?>
</script>
</body>
</html>