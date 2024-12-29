<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

// ฟังก์ชันอนุมัติการจอง
if (isset($_GET['approve']) && $_GET['approve'] == 1) {
    $booking_id = mysqli_real_escape_string($conn, $_GET['booking_id']);
    $approve_sql = "UPDATE course_bookings SET status = 'confirmed' WHERE id = '$booking_id'";
    if (mysqli_query($conn, $approve_sql)) {
        $_SESSION['msg_ok'] = "อนุมัติการจองเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการอนุมัติการจอง: " . mysqli_error($conn);
    }
    header("Location: booking-show.php");
    exit();
}

// ฟังก์ชันยกเลิกการจอง
if (isset($_GET['cancel']) && $_GET['cancel'] == 1) {
    $booking_id = mysqli_real_escape_string($conn, $_GET['booking_id']);
    $cancel_sql = "UPDATE course_bookings SET status = 'cancelled' WHERE id = '$booking_id'";
    if (mysqli_query($conn, $cancel_sql)) {
        $_SESSION['msg_ok'] = "ยกเลิกการจองเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการยกเลิกการจอง: " . mysqli_error($conn);
    }
    header("Location: booking-show.php");
    exit();
}

// ดึงข้อมูลการจองทั้งหมด
// ดึงข้อมูลการจองทั้งหมด
$sql = "SELECT 
    cb.*,
    c.cus_firstname, 
    c.cus_lastname,
    c.cus_tel,
    c.cus_email,
    c.cus_address,
    c.cus_district,
    c.cus_city,
    c.cus_province,
    c.cus_postal_code,
    r.room_name,
    u.users_fname,
    u.users_lname,
    GROUP_CONCAT(DISTINCT CONCAT(co.course_name, ' (', co.course_amount, ' ครั้ง)') SEPARATOR ', ') as booked_courses,
    oc.oc_id,
    oc.order_net_total,
    oc.order_payment,
    fn.note as follow_up_note
FROM course_bookings cb
LEFT JOIN customer c ON cb.cus_id = c.cus_id
LEFT JOIN rooms r ON cb.room_id = r.room_id
LEFT JOIN users u ON cb.users_id = u.users_id
LEFT JOIN order_course oc ON cb.id = oc.course_bookings_id
LEFT JOIN order_detail od ON oc.oc_id = od.oc_id
LEFT JOIN course co ON od.course_id = co.course_id
LEFT JOIN follow_up_notes fn ON cb.id = fn.booking_id
WHERE cb.branch_id = {$_SESSION['branch_id']}
GROUP BY cb.id
ORDER BY cb.created_at DESC";

$result_booking = $conn->query($sql);


// ดึงข้อมูลประวัติการแก้ไข
$sql_logs = "SELECT al.*, u.users_fname, u.users_lname, b.id as booking_id 
             FROM activity_logs al
             LEFT JOIN users u ON al.user_id = u.users_id 
             LEFT JOIN course_bookings b ON al.entity_id = b.id
             WHERE al.entity_type = 'booking'
             ORDER BY al.created_at DESC";
$result_logs = $conn->query($sql_logs);

if (!$result_logs) {
    die("Error fetching logs: " . $conn->error);
}

if (!$result_booking) {
    die("Error fetching bookings: " . $conn->error);
}

function formatOrderId($orderId) {
    return 'ORDER-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
}

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
    <title>แสดงการจองคอร์ส - D Care Clinic</title>
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
        .payment-status {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
        }
        .payment-cash {
            background-color: #d4edda;
            color: #155724;
        }
        .payment-credit {
            background-color: #cce5ff;
            color: #004085;
        }
        .payment-transfer {
            background-color: #e2e3e5;
            color: #383d41;
        }
        .payment-unpaid {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
        }
        .status-confirmed {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
    .dropdown-toggle-z-index {
        position: relative;
        z-index: 1000; /* ปรับค่านี้ตามความเหมาะสม */
    }
    .dropdown-menu-z-index {
        z-index: 1001; /* ต้องมากกว่า z-index ของ .dropdown-toggle-z-index */
    }
    .dropdown-item-z-index {
        position: relative;
        z-index: 1002; /* ต้องมากกว่า z-index ของ .dropdown-menu-z-index */
    }
    .clickable-row {
        cursor: pointer;
    }
    .clickable-row:hover {
        background-color: #f5f5f5;
    }
     .datatables-bookings tbody tr {
        cursor: pointer;
        transition: all 0.2s;
    }

    .datatables-bookings tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
        transform: translateY(-1px);
    }

    /* สถานะการจอง */
    .booking-status {
        padding: 0.4em 0.8em;
        border-radius: 30px;
        font-weight: 500;
        font-size: 0.85em;
        display: inline-flex;
        align-items: center;
        gap: 0.3em;
    }

    .booking-status i {
        font-size: 1.1em;
    }

    /* Modal Styles */
    .booking-detail-card {
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        transition: all 0.3s;
    }

    .booking-detail-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .card-header {
        background: linear-gradient(45deg, #6b21a8, #3730a3);
        color: white;
        border-radius: 10px 10px 0 0 !important;
    }

    /* Course Progress */
    .course-progress {
        height: 10px;
        border-radius: 5px;
        background-color: #e9ecef;
    }

    .course-progress-bar {
        height: 100%;
        border-radius: 5px;
        transition: width 0.3s ease;
    }

    /* Resource List */
    .resource-list {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 0.8rem;
    }

    .resource-item {
        display: flex;
        justify-content: space-between;
        padding: 0.4rem 0;
        border-bottom: 1px dashed #dee2e6;
    }

    .resource-item:last-child {
        border-bottom: none;
    }

    /* Payment Info */
    .payment-info {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
    }

    .payment-status {
        display: inline-flex;
        align-items: center;
        gap: 0.5em;
        padding: 0.4em 1em;
        border-radius: 30px;
        font-weight: 500;
    }

    /* ป้ายกำกับสถานะ */
    .status-badge {
        padding: 0.5em 0.8em;
        border-radius: 6px;
        font-size: 0.85em;
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    /* Follow-up Badge */
    .follow-up-badge {
        background-color: #e8f5e9;
        color: #2e7d32;
        padding: 0.4em 0.8em;
        border-radius: 4px;
        font-size: 0.85em;
    }

    /* Action Buttons */
    .action-btn {
        padding: 0.4em 0.8em;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .action-btn:hover {
        transform: translateY(-1px);
    }

    /* Modal Animation */
    .modal.fade .modal-dialog {
        transform: scale(0.95);
        transition: transform 0.2s ease-out;
    }

    .modal.show .modal-dialog {
        transform: scale(1);
    }
    #historyTable thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    #historyTable tbody tr:hover {
        background-color: rgba(0,0,0,.02);
        cursor: pointer;
    }
    #historyModal .modal-body {
        padding: 1.5rem;
    }
    #historyTable .badge {
        font-size: 0.85em;
        padding: 0.4em 0.8em;
    }

        /* ปรับ z-index ของ Modal */
    .modal {
        z-index: 1060 !important;
    }
    
    /* ปรับ z-index ของ Dropdown */
    .dropdown-menu {
        z-index: 1050 !important;
    }
    
    /* ปรับ z-index ของ Modal Backdrop */
    .modal-backdrop {
        z-index: 1050 !important;
    }
    
    /* ทำให้ปุ่ม dropdown อยู่ด้านบนเสมอ */
    .dropdown .dropdown-toggle {
        position: relative;
        z-index: 1051 !important;
    }
    
    /* ทำให้ dropdown menu อยู่ด้านบน modal */
    .dropdown-menu.show {
        z-index: 1052 !important;
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
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="py-3 mb-4"><span class="text-muted fw-light">การจองคอร์ส /</span> รายละเอียดการจอง</h4>

                        <!-- Booking List Table -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 text-white">รายการจองคอร์สทั้งหมด</h5>
                                <a href="booking.php" class="btn btn-primary">
                                    <i class="ri-add-line me-1"></i> จองคอร์สใหม่
                                </a>
                            </div>
                            <div class="text-end my-2">
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#historyModal">
                                    <i class="ri-history-line me-2"></i> ประวัติการแก้ไข
                                </button>
                            </div>
                            <div class="card-datatable table-responsive">
                                <div class="card-datatable table-responsive">
                                    <table class="datatables-bookings table border-top">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>วันที่นัด</th>
                                                <th>เวลา</th>
                                                <th>ลูกค้า</th>
                                                <th>ห้อง</th>
                                                <th>คอร์สที่จอง</th>
                                                <th>ติดตามผล</th>
                                                <th>สถานะ</th>
                                                <th>การชำระเงิน</th>
                                                <th>ผู้ทำรายการ</th>
                                                <th>การดำเนินการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($booking = mysqli_fetch_object($result_booking)): ?>
                                            <tr class="clickable-row" data-customer-id="<?php echo $booking->cus_id; ?>">
                                                <td></td>
                                                <td><?php echo date('d/m/Y', strtotime($booking->booking_datetime)); ?></td>
                                                <td><?php echo date('H:i', strtotime($booking->booking_datetime)); ?></td>
                                                <td>
                                                    <a href="customer-detail.php?id=<?php echo $booking->cus_id; ?>">
                                                        <?php echo htmlspecialchars($booking->cus_firstname . ' ' . $booking->cus_lastname); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo htmlspecialchars($booking->room_name); ?></td>
                                                <td><?php echo htmlspecialchars($booking->booked_courses); ?></td>
                                                <td>
                                                    <?php if ($booking->is_follow_up): ?>
                                                        <span class="badge bg-info">นัดติดตาม</span>
                                                        <i class="ri-information-line" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($booking->follow_up_note); ?>"></i>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">ปกติ</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status_class = '';
                                                    $status_text = '';
                                                    switch ($booking->status) {
                                                        case 'confirmed':
                                                            $status_class = 'success';
                                                            $status_text = 'ยืนยันแล้ว';
                                                            break;
                                                        case 'cancelled':
                                                            $status_class = 'danger';
                                                            $status_text = 'ยกเลิกแล้ว';
                                                            break;
                                                        default:
                                                            $status_class = 'warning';
                                                            $status_text = 'รอยืนยัน';
                                                    }
                                                    ?>
                                                    <span class="badge bg-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($booking->order_payment) {
                                                        $payment_class = match($booking->order_payment) {
                                                            'เงินสด' => 'success',
                                                            'บัตรเครดิต' => 'info',
                                                            'โอนเงิน' => 'primary',
                                                            default => 'secondary'
                                                        };
                                                        echo "<span class='badge bg-{$payment_class}'>{$booking->order_payment}</span>";
                                                    } else {
                                                        echo "<span class='badge bg-danger'>ยังไม่ชำระ</span>";
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($booking->users_fname . ' ' . $booking->users_lname); ?></td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" 
                                                                data-bs-toggle="dropdown" 
                                                                style="position: relative; z-index: 1051;">
                                                            <i class="ri-more-fill"></i>
                                                        </button>
                                                        <div class="dropdown-menu" style="z-index: 1052;">
                                                            <a class="dropdown-item" href="javascript:void(0);" 
                                                               onclick="showBookingDetails(<?php echo $booking->id; ?>)">
                                                                <i class="ri-eye-line me-1"></i> รายละเอียด
                                                            </a>
                                                            <?php if ($booking->status === 'pending'): ?>
                                                            <a class="dropdown-item" href="javascript:void(0);" 
                                                               onclick="confirmApprove(<?php echo $booking->id; ?>)">
                                                                <i class="ri-check-line me-1"></i> อนุมัติ
                                                            </a>
                                                            <?php endif; ?>
                                                            <?php if ($booking->status !== 'cancelled'): ?>
                                                            <a class="dropdown-item text-danger" href="javascript:void(0);" 
                                                               onclick="confirmCancel(<?php echo $booking->id; ?>)">
                                                                <i class="ri-close-line me-1"></i> ยกเลิก
                                                            </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!--/ Booking List Table -->
                    </div>
                    <!-- / Content -->
                    <?php include 'footer.php'; ?>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
    </div>

   <!-- Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white">รายละเอียดการจอง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- ข้อมูลลูกค้า -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0 text-white">ข้อมูลลูกค้า</h6>
                            </div>
                            <div class="card-body" id="customerDetails">
                                <!-- จะถูกเติมด้วย JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- ข้อมูลการจอง -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0 text-white">ข้อมูลการจอง</h6>
                            </div>
                            <div class="card-body" id="bookingDetails">
                                <!-- จะถูกเติมด้วย JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- ข้อมูลคอร์ส -->
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0 text-white">รายละเอียดคอร์ส</h6>
                            </div>
                            <div class="card-body" id="courseDetails">
                                <!-- จะถูกเติมด้วย JavaScript -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- ข้อมูลการชำระเงิน -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="card-title mb-0 text-white">ข้อมูลการชำระเงิน</h6>
                            </div>
                            <div class="card-body" id="paymentDetails">
                                <!-- จะถูกเติมด้วย JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal แสดงประวัติ -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">ประวัติการแก้ไขข้อมูลการจอง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="historyTable">
                        <thead>
                            <tr>
                                <th>วันที่-เวลา</th>
                                <th>การดำเนินการ</th>
                                <th>ผู้ดำเนินการ</th>
                                <th>รายละเอียด</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($log = $result_logs->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo convertToThaiDate($log['created_at']); ?></td>
                                    <td>
                                        <?php
                                        $action_class = '';
                                        $action_text = '';
                                        switch($log['action']) {
                                            case 'approve':
                                                $action_class = 'success';
                                                $action_text = 'อนุมัติการจอง';
                                                break;
                                            case 'cancel':
                                                $action_class = 'danger';
                                                $action_text = 'ยกเลิกการจอง';
                                                break;
                                            default:
                                                $action_class = 'primary';
                                                $action_text = $log['action'];
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $action_class; ?>"><?php echo $action_text; ?></span>
                                    </td>
                                    <td><?php echo $log['users_fname'] . ' ' . $log['users_lname']; ?></td>
                                    <td>
                                        <?php
                                        // แปลงข้อมูล details เป็น array (ถ้าอยู่ในรูป JSON)
                                        $details = json_decode($log['details'], true);
if ($details && is_array($details)) {
    if (isset($details['reason'])) {
        echo "เหตุผล: " . htmlspecialchars($details['reason']) . "<br>";
    }
    if (isset($details['booking_info'])) {
        echo "วันที่จอง: " . date('d/m/Y H:i', strtotime($details['booking_info']['date'])) . "<br>";
        echo "ลูกค้า: " . htmlspecialchars($details['booking_info']['customer']) . "<br>";
    }
} else {
                                            // ถ้าไม่ใช่ JSON หรือไม่สามารถ decode ได้ ให้แสดงข้อมูลดิบ
                                            echo htmlspecialchars($log['details'] ?: '-');
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

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
    <script src="../assets/js/tables-datatables-basic.js"></script>
    <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>


    <script>


$(document).ready(function() {
    // Initialize history table
    $('#historyTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
        },
        "order": [[0, "desc"]], // เรียงตามวันที่ล่าสุด
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "ทั้งหมด"]],
    });

    // เพิ่ม event listener สำหรับ historyModal
    $('#historyModal').on('shown.bs.modal', function () {
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust()
            .responsive.recalc();
    });

    const bookingTable = $('.datatables-bookings').DataTable({
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        displayLength: 10,
        lengthMenu: [10, 25, 50, 75, 100],
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function(row) {
                        const data = row.data();
                        return 'รายละเอียดการจอง ' + data[3];
                    }
                }),
                type: 'column',
                renderer: function(api, rowIdx, columns) {
                    const data = $.map(columns, function(col, i) {
                        return col.hidden ?
                            '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' + col.columnIndex + '">' +
                            '<td>' + col.title + ':</td> ' +
                            '<td>' + col.data + '</td>' +
                            '</tr>' :
                            '';
                    }).join('');

                    return data ? 
                        $('<table class="table"/><tbody />').append(data) :
                        false;
                }
            }
        },
        language: {
            search: 'ค้นหา:',
            lengthMenu: 'แสดง _MENU_ รายการ',
            info: 'แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ',
            infoEmpty: 'ไม่พบรายการ',
            infoFiltered: '(กรองจากทั้งหมด _MAX_ รายการ)',
            zeroRecords: 'ไม่พบรายการที่ตรงกับการค้นหา',
            paginate: {
                first: 'หน้าแรก',
                previous: 'ก่อนหน้า',
                next: 'ถัดไป',
                last: 'หน้าสุดท้าย'
            }
        },
        //order: [[1, 'desc']],  เรียงตามวันที่จากใหม่ไปเก่า
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false,
                className: 'control'
            },
            {
                targets: -1,
                orderable: false,
                searchable: false
            }
        ]
    });

    // Event Handlers
    $('.datatables-bookings tbody').on('click', 'tr', function(e) {
        // ตรวจสอบว่าคลิกที่ปุ่ม dropdown หรือรายการเมนูหรือไม่
        if (!$(e.target).closest('.dropdown-toggle, .dropdown-item').length) {
            var customerId = $(this).data('customer-id');
            if (customerId) {
                window.location.href = 'customer-detail.php?id=' + customerId;
            }
        }
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function showBookingDetails(bookingId) {
    Swal.fire({
        title: 'กำลังโหลดข้อมูล...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: 'sql/get-booking-details.php',
        type: 'GET',
        data: { id: bookingId },
        success: function(response) {
            Swal.close();
            
            // เติมข้อมูลลูกค้า
            const customerHtml = `
                <p><strong>ชื่อ-นามสกุล:</strong> ${response.customer?.fullname || '-'}</p>
                <p><strong>ชื่อเล่น:</strong> ${response.customer?.nickname || '-'}</p>
                <p><strong>เบอร์โทร:</strong> ${response.customer?.tel || '-'}</p>
                <p><strong>อีเมล:</strong> ${response.customer?.email || '-'}</p>
                <p><strong>ที่อยู่:</strong> ${response.customer?.address || '-'}</p>
            `;
            $('#customerDetails').html(customerHtml);

            // เติมข้อมูลการจอง
            const bookingHtml = `
                <p><strong>วันที่นัด:</strong> ${response.booking?.datetime || '-'}</p>
                <p><strong>ห้อง:</strong> ${response.booking?.room || '-'}</p>
                <p><strong>สถานะ:</strong> 
                    <span class="badge bg-${response.booking?.status_class || 'secondary'}">
                        ${response.booking?.status_text || '-'}
                    </span>
                </p>
                ${response.booking?.follow_up?.is_follow_up ? `
                    <p><strong>หมายเหตุการติดตาม:</strong> ${response.booking.follow_up.note || '-'}</p>
                ` : ''}
            `;
            $('#bookingDetails').html(bookingHtml);

            // เติมข้อมูลคอร์ส
            if (response.courses && response.courses.length > 0) {
                const coursesHtml = response.courses.map(course => `
                    <div class="course-item mb-3">
                        <h6>${course.name || '-'}</h6>
                        <div class="progress mb-2">
                            <div class="progress-bar" role="progressbar" 
                                style="width: ${course.total_sessions ? (course.used_sessions/course.total_sessions*100) : 0}%">
                                ${course.used_sessions || 0}/${course.total_sessions || 0}
                            </div>
                        </div>
                        <p class="mb-2">ราคา: ${(course.price || 0).toLocaleString()} บาท</p>
                        ${course.resources && course.resources.length > 0 ? `
                            <div class="resources-used">
                                <small>ทรัพยากรที่ใช้:</small>
                                <ul class="list-unstyled">
                                    ${course.resources.map(resource => `
                                        <li>${resource.name || '-'} (${resource.quantity || 0} ${resource.unit || '-'})</li>
                                    `).join('')}
                                </ul>
                            </div>
                        ` : '<p class="mb-0"><small>ไม่มีการใช้ทรัพยากร</small></p>'}
                    </div>
                `).join('');
                $('#courseDetails').html(coursesHtml);
            } else {
                $('#courseDetails').html('<p class="text-muted">ไม่พบข้อมูลคอร์ส</p>');
            }

            // เติมข้อมูลการชำระเงิน
            const payment = response.payment || {};
            const paymentHtml = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ยอดรวม:</strong> ${(payment.total || 0).toLocaleString()} บาท</p>
                        ${payment.deposit?.amount ? `
                            <p><strong>เงินมัดจำ:</strong> ${payment.deposit.amount.toLocaleString()} บาท</p>
                        ` : ''}
                        ${payment.vouchers?.length > 0 ? `
                            <div class="mb-2">
                                <strong>บัตรกำนัลที่ใช้:</strong>
                                ${payment.vouchers.map(v => `
                                    <div class="ps-3">
                                        <small>
                                            ${v.code} - ${v.type === 'fixed' ? 
                                                `${(v.used_amount || 0).toLocaleString()} บาท` : 
                                                `${v.amount}%`}
                                        </small>
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}
                        <p><strong>ยอดสุทธิ:</strong> ${((payment.total || 0) - (payment.deposit?.amount || 0)).toLocaleString()} บาท</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>สถานะ:</strong> 
                            <span class="badge bg-${payment.status_class || 'secondary'}">
                                ${payment.status || 'รอชำระเงิน'}
                            </span>
                        </p>
                        ${payment.payment_date ? `
                            <p><strong>วันที่ชำระ:</strong> ${payment.payment_date}</p>
                        ` : ''}
                        ${payment.deposit?.date ? `
                            <p><strong>วันที่มัดจำ:</strong> ${payment.deposit.date}</p>
                        ` : ''}
                    </div>
                </div>
            `;
            $('#paymentDetails').html(paymentHtml);

            // แสดง modal
            $('#bookingDetailsModal').modal('show');
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถโหลดข้อมูลได้: ' + error
            });
        }
    });
}



// สำหรับการจัดการการอนุมัติและยกเลิก
function confirmApprove(bookingId) {
    Swal.fire({
        title: 'ยืนยันการอนุมัติ',
        text: 'คุณต้องการอนุมัติการจองนี้ใช่หรือไม่?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ใช่, อนุมัติ',
        cancelButtonText: 'ยกเลิก',
        customClass: {
            confirmButton: 'btn btn-primary me-3',
            cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false,
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'sql/process-booking.php',
                type: 'POST',
                data: {
                    action: 'approve',
                    booking_id: bookingId
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'อนุมัติสำเร็จ',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: response.message
                        });
                    }
                }
            });
        }
    });
}

function confirmCancel(bookingId) {
    Swal.fire({
        title: 'ยืนยันการยกเลิก',
        text: 'คุณต้องการยกเลิกการจองนี้ใช่หรือไม่?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, ยกเลิก',
        cancelButtonText: 'ไม่',
        customClass: {
            confirmButton: 'btn btn-danger me-3',
            cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'ระบุเหตุผลการยกเลิก',
                input: 'textarea',
                inputPlaceholder: 'กรุณาระบุเหตุผล...',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                customClass: {
                    confirmButton: 'btn btn-primary me-3',
                    cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false,
                inputValidator: (value) => {
                    if (!value) {
                        return 'กรุณาระบุเหตุผลการยกเลิก';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'sql/process-booking.php',
                        type: 'POST',
                        data: {
                            action: 'cancel',
                            booking_id: bookingId,
                            reason: result.value
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'ยกเลิกสำเร็จ',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: response.message
                                });
                            }
                        }
                    });
                }
            });
        }
    });
}

</script>
</body>
</html>