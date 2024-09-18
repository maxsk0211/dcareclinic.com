<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

// เพิ่ม error reporting เพื่อ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// รับค่า oc_id จาก GET parameter
$oc_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($oc_id == 0) {
    die("ไม่พบข้อมูลคำสั่งซื้อ");
}

// ดึงข้อมูลคำสั่งซื้อและข้อมูลลูกค้าจากฐานข้อมูล
$sql = "SELECT oc.*, c.*, cb.booking_datetime 
        FROM order_course oc
        JOIN customer c ON oc.cus_id = c.cus_id
        JOIN course_bookings cb ON oc.course_bookings_id = cb.id
        WHERE oc.oc_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $oc_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("ไม่พบข้อมูลคำสั่งซื้อ");
}

$customer_data = $result->fetch_assoc();

// คำนวณอายุ
$birthDate = new DateTime($customer_data['cus_birthday']);
$today = new DateTime();
$age = $today->diff($birthDate);

// ฟังก์ชันสำหรับแปลงวันที่เป็นรูปแบบไทย
function thai_date($date) {
    $months = array(
        1=>'มกราคม', 2=>'กุมภาพันธ์', 3=>'มีนาคม', 4=>'เมษายน', 5=>'พฤษภาคม', 6=>'มิถุนายน', 
        7=>'กรกฎาคม', 8=>'สิงหาคม', 9=>'กันยายน', 10=>'ตุลาคม', 11=>'พฤศจิกายน', 12=>'ธันวาคม'
    );
    $timestamp = strtotime($date);
    $thai_date = date('d', $timestamp).' '.$months[date('n', $timestamp)].' '.(date('Y', $timestamp) + 543);
    return $thai_date;
}

// ดึงข้อมูลใบเสร็จและรายการสินค้า
$sql_order = "SELECT oc.*, u.users_fname, u.users_lname 
              FROM order_course oc
              LEFT JOIN users u ON oc.users_id = u.users_id
              WHERE oc.oc_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $oc_id);
$stmt_order->execute();
$order_result = $stmt_order->get_result();
$order_data = $order_result->fetch_assoc();

$sql_items = "SELECT od.*, c.course_name
              FROM order_detail od
              JOIN course c ON od.course_id = c.course_id
              WHERE od.oc_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $oc_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();

// ฟังก์ชันสำหรับฟอร์แมตวันที่และเวลา
function format_datetime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}
function formatOrderId($orderId) {
    return 'ORDER-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
}
// คำนวณยอดรวม
$total_amount = 0;
$items_result->data_seek(0); // รีเซ็ตตำแหน่งของ result set
while ($item = $items_result->fetch_assoc()) {
    $total_amount += $item['od_amount'] * $item['od_price'];
}

// ฟังก์ชันสำหรับจัดรูปแบบตัวเลขเงิน
function format_money($amount) {
    return number_format($amount, 2, '.', ',');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>จัดการคำสั่งซื้อ - D Care Clinic</title>

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
        .card {
            margin-bottom: 1.5rem;
        }
        .customer-info {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
        }
        .customer-info .avatar {
            width: 80px;
            height: 80px;
            background-color: #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #757575;
        }
        .customer-info .info-list {
            list-style-type: none;
            padding-left: 0;
        }
        .customer-info .info-list li {
            margin-bottom: 0.5rem;
        }
.order-details {
    background-color: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
}
.order-details h5 {
    margin-bottom: 1rem;
}
.order-details table {
    width: 100%;
    margin-bottom: 1rem;
}
.order-details th, .order-details td {
    padding: 0.5rem;
    vertical-align: middle;
}
.order-details .badge {
    font-size: 0.8rem;
    padding: 0.3rem 0.5rem;
}
.order-details tfoot {
    font-weight: bold;
    background-color: #f8f9fa;
}
.order-details tfoot td {
    border-top: 2px solid #dee2e6;
}
        .payment-section {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1rem;
        }
        .payment-section input, .payment-section select {
            margin-bottom: 1rem;
        }
        .action-buttons {
            margin-top: 1rem;
        }
        .action-buttons button {
            margin-right: 0.5rem;
        }
        .side-panel {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        .side-panel h5 {
            margin-bottom: 1rem;
        }
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 0.75rem;
            border-radius: 0.25rem;
            margin-top: 1rem;
        }
        .avatar {
            width: 80px;
            height: 80px;
            background-color: #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #757575;
            overflow: hidden;
        }
        .avatar-initial {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #fff;
            text-transform: uppercase;
        }
        
    </style>
</head>
<body>
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">
            <?php include 'navbar.php'; ?>
            <div class="layout-page">
                <div class="content-wrapper">
                    <?php include 'menu.php'; ?>
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card customer-info">
                                    <h5>ข้อมูลลูกค้า</h5>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="avatar">
                                                <?php
                                                if (!empty($customer_data['line_picture_url'])) {
                                                    // $firstChar = mb_substr($customer_data['line_picture_url'], 0, 1, 'UTF-8');
                                                    echo '<img src="'.$customer_data['line_picture_url'].'" class="rounded-circle" alt="">';
                                                } else {
                                                    echo '<i class="ri-user-line"></i>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-10">
                                            <ul class="info-list">
                                                <li><strong>รหัส:</strong> <?php echo 'HN-' . str_pad($customer_data['cus_id'], 6, '0', STR_PAD_LEFT); ?></li>
                                                <li><strong>ชื่อ - นามสกุล:</strong> <?php echo $customer_data['cus_title'] . ' ' . $customer_data['cus_firstname'] . ' ' . $customer_data['cus_lastname'] . ' (' . $customer_data['cus_nickname'] . ')'; ?></li>
                                                <li><strong>เลขบัตรประชาชน:</strong> <?php echo $customer_data['cus_id_card_number']; ?></li>
                                                <li><strong>เพศ:</strong> <?php echo $customer_data['cus_gender']; ?> | <strong>วันเกิด:</strong> <?php echo thai_date($customer_data['cus_birthday']) . ' (' . $age->y . ' ปี ' . $age->m . ' เดือน ' . $age->d . ' วัน)'; ?></li>
                                                <li><strong>กรุ๊ปเลือด:</strong> <?php echo $customer_data['cus_blood']; ?></li>
                                                <li><strong>แพ้ยา:</strong> <?php echo $customer_data['cus_drugallergy'] ?: 'ไม่มี'; ?> | <strong>โรคประจำตัว:</strong> <?php echo $customer_data['cus_congenital'] ?: 'ไม่มี'; ?></li>
                                                <li><strong>ที่อยู่:</strong> <i class="ri-home-4-line"></i> <?php echo $customer_data['cus_address'] . ' ' . $customer_data['cus_district'] . ' ' . $customer_data['cus_city'] . ' ' . $customer_data['cus_province'] . ' ' . $customer_data['cus_postal_code']; ?></li>
                                                <li><strong>โทร:</strong> <i class="ri-phone-line"></i> <?php echo $customer_data['cus_tel']; ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card order-details">
                                    <h5>เลขที่ใบเสร็จ <?php echo formatOrderId($order_data['oc_id']); ?> 
                                        <!-- <span class="badge bg-success">ส่วนลดทั้งใบเสร็จ</span> -->
                                    </h5>
                                    <p>สร้างเมื่อ: <?php echo format_datetime($order_data['order_datetime']); ?> / 
                                       โดย: <?php echo $order_data['users_fname'] . ' ' . $order_data['users_lname']; ?></p>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>รายการ</th>
                                                <th>จำนวน</th>
                                                <th>หน่วยนับ</th>
                                                <th>ราคา/หน่วย</th>
                                                <th>ยอดสุทธิ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $items_result->data_seek(0); // รีเซ็ตตำแหน่งของ result set อีกครั้ง
                                            while ($item = $items_result->fetch_assoc()): 
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['course_name']); ?></td>
                                                <td><?php echo $item['od_amount']; ?></td>
                                                <td>ครั้ง/คอร์ส</td>
                                                <td><?php echo format_money($item['od_price']); ?></td>
                                                <td><?php echo format_money($item['od_amount'] * $item['od_price']); ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-end"><strong>ยอดรวมทั้งสิ้น:</strong></td>
                                                <td><strong><?php echo format_money($total_amount); ?></strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div class="text-end mt-3">
                                        <a href="edit-order.php?id=<?php echo $oc_id; ?>" class="btn btn-primary">แก้ไขคำสั่งซื้อ</a>
                                    </div>
                                </div>
                                <div class="card payment-section">
                                    <h5>การชำระเงินมัดจำ</h5>
                                    <form id="depositForm" enctype="multipart/form-data">
                                        <input type="hidden" name="order_id" value="<?php echo $oc_id; ?>">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>จำนวนเงินมัดจำ (บาท)</label>
                                                <input type="number" class="form-control" name="deposit_amount" id="deposit_amount" value="<?php echo $order_data['deposit_amount']; ?>" step="0.01" min="0">
                                            </div>
                                            <div class="col-md-6">
                                                <label>ประเภทการชำระเงินมัดจำ</label>
                                                <select class="form-select" name="deposit_payment_type" id="deposit_payment_type">
                                                    <option value="">เลือกประเภท</option>
                                                    <option value="เงินสด" <?php echo $order_data['deposit_payment_type'] == 'เงินสด' ? 'selected' : ''; ?>>เงินสด</option>
                                                    <option value="บัตรเครดิต" <?php echo $order_data['deposit_payment_type'] == 'บัตรเครดิต' ? 'selected' : ''; ?>>บัตรเครดิต</option>
                                                    <option value="เงินโอน" <?php echo $order_data['deposit_payment_type'] == 'เงินโอน' ? 'selected' : ''; ?>>เงินโอน</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mt-3" id="slipUploadSection" style="display: none;">
                                            <div class="col-md-12">
                                                <label>อัพโหลดสลิปการโอนเงิน</label>
                                                <input type="file" class="form-control" name="deposit_slip" id="deposit_slip" accept="image/*">
                                            </div>
                                        </div>
                                        <?php if (!empty($order_data['deposit_slip_image'])): ?>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <label>สลิปการโอนเงิน:</label>
                                                <button type="button" class="btn btn-primary btn-sm" onclick="showSlipModal('<?php echo '../img/payment-proofs/' . $order_data['deposit_slip_image']; ?>')">ดูสลิป</button>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (!empty($order_data['deposit_date'])): ?>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <label>วันที่และเวลาชำระมัดจำ:</label>
                                                <span><?php echo date('d/m/Y H:i:s', strtotime($order_data['deposit_date'])); ?></span>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <button type="submit" class="btn btn-primary">บันทึกข้อมูลมัดจำ</button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="card payment-summary mt-4">
                                        <h5>สรุปการชำระเงิน</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p>ยอดรวมทั้งสิ้น: <?php echo format_money($total_amount); ?> บาท</p>
                                                <p>หักเงินมัดจำ: <?php echo format_money($order_data['deposit_amount']); ?> บาท</p>
                                                <p><strong>ยอดที่ต้องชำระเพิ่ม: <?php echo format_money($total_amount - $order_data['deposit_amount']); ?> บาท</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="side-panel">
                                    <h5>รายการที่ใช้บริการ</h5>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>เลขที่บริการ</th>
                                                <th>รายการที่ให้บริการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- เพิ่มข้อมูลตามต้องการ -->
                                        </tbody>
                                    </table>
                                    <p>รวมค่ามือ: 0.00 บาท</p>
                                </div>
                                <div class="side-panel">
                                    <h5>นัดหมายติดตามผล</h5>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>เลขที่บริการ</th>
                                                <th>รายการที่ให้บริการ</th>
                                                <th>วันที่นัดหมาย/ห้วงเวลา</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- เพิ่มข้อมูลตามต้องการ -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="side-panel">
                                    <h5>ชำระเงินมัดจำ</h5>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>ช่องทาง</th>
                                                <th>จำนวนเงิน</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>รวมทั้งหมด</td>
                                                <td>0.00 บาท</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button class="btn btn-primary w-100">ชำระเงินมัดจำ</button>
                                </div>
                                <div class="warning-box">
                                    Warning - โปรดเลือกประเภทการชำระเงิน!
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include 'footer.php'; ?>
                </div>
            </div>
        </div>
    </div>

<!-- Modal for displaying slip image -->
<div class="modal fade" id="slipModal" tabindex="-1" aria-labelledby="slipModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="slipModalLabel">สลิปการโอนเงิน</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <img id="slipImage" src="" alt="Slip" style="width: 100%; height: auto;">
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
    <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>

    <script>
function showSlipModal(imageSrc) {
    document.getElementById('slipImage').src = imageSrc;
    var modal = new bootstrap.Modal(document.getElementById('slipModal'));
    modal.show();
}

$(document).ready(function() {
    function toggleSlipUpload() {
        if ($('#deposit_payment_type').val() == 'เงินโอน') {
            $('#slipUploadSection').show();
        } else {
            $('#slipUploadSection').hide();
        }
    }

    // เรียกใช้ฟังก์ชันเมื่อโหลดหน้าเพื่อตั้งค่าเริ่มต้น
    toggleSlipUpload();

    // เรียกใช้ฟังก์ชันเมื่อมีการเปลี่ยนแปลงค่า
    $('#deposit_payment_type').change(toggleSlipUpload);

    $('#depositForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        // ตรวจสอบว่ามีไฟล์ถูกเลือกหรือไม่
        var fileInput = $('#deposit_slip')[0];
        if(fileInput.files.length > 0) {
            console.log('File selected:', fileInput.files[0]);
        } else {
            console.log('No file selected');
        }

        $.ajax({
            url: 'sql/update-deposit.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                console.log('Server response:', response);
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: response.message || 'บันทึกข้อมูลมัดจำสำเร็จ',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: response.message || 'ไม่สามารถบันทึกข้อมูลได้',
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
                console.log('Response Text:', jqXHR.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                });
            }
        });
    });

    // เพิ่มฟังก์ชันอื่นๆ ที่จำเป็นสำหรับหน้านี้ (ถ้ามี)
});
</script>
</body>
</html>