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
$sql = "SELECT cb.*, cu.cus_id AS customer_id, cu.cus_firstname, cu.cus_lastname, u.users_fname, oc.oc_id, oc.order_net_total, oc.order_payment
        FROM course_bookings cb
        JOIN customer cu ON cb.cus_id = cu.cus_id
        LEFT JOIN users u ON cb.users_id = u.users_id
        LEFT JOIN order_course oc ON cb.id = oc.course_bookings_id
        WHERE cb.branch_id = {$_SESSION['branch_id']}
        ORDER BY cb.booking_datetime DESC";

$result_booking = $conn->query($sql);

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
                                <h5 class="mb-0">รายการจองคอร์สทั้งหมด</h5>
                                <a href="booking.php" class="btn btn-primary">
                                    <i class="ri-add-line me-1"></i> จองคอร์สใหม่
                                </a>
                            </div>
                            <div class="card-datatable table-responsive">
                                <table class="datatables-bookings table border-top">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>รหัสการจอง</th>
                                            <th>ชื่อลูกค้า</th>
                                            <th>วันที่จอง</th>
                                            <th>ผู้ทำรายการ</th>
                                            <th>ยอดรวม</th>
                                            <th>สถานะการจอง</th>
                                            <th>สถานะการชำระเงิน</th>
                                            <th>การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row_booking = mysqli_fetch_object($result_booking)): ?>
                                            <tr class="clickable-row" data-customer-id="<?php echo $row_booking->customer_id; ?>">
                                                <td></td>
                                                <td><?php echo formatOrderId($row_booking->oc_id); ?></td>
                                                <td><?php echo htmlspecialchars($row_booking->cus_firstname . ' ' . $row_booking->cus_lastname); ?></td>
                                                <td><?php echo convertToThaiDate($row_booking->booking_datetime); ?></td>
                                                <td><?php echo $row_booking->users_fname ?? 'N/A'; ?></td>
                                                <td><?php echo number_format($row_booking->order_net_total, 2); ?> บาท</td>
                                                <td>
                                                    <?php
                                                    $status_class = '';
                                                    switch ($row_booking->status) {
                                                        case 'confirmed':
                                                            $status_class = 'status-confirmed';
                                                            $status_text = 'ยืนยันแล้ว';
                                                            break;
                                                        case 'cancelled':
                                                            $status_class = 'status-cancelled';
                                                            $status_text = 'ยกเลิกแล้ว';
                                                            break;
                                                        default:
                                                            $status_class = 'status-pending';
                                                            $status_text = 'รอยืนยัน';
                                                    }
                                                    echo "<span class='status-badge $status_class'>$status_text</span>";
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $payment_class = '';
                                                    switch ($row_booking->order_payment) {
                                                        case 'เงินสด':
                                                            $payment_class = 'payment-cash';
                                                            break;
                                                        case 'บัตรเครดิต':
                                                            $payment_class = 'payment-credit';
                                                            break;
                                                        case 'โอนเงิน':
                                                            $payment_class = 'payment-transfer';
                                                            break;
                                                        default:
                                                            $payment_class = 'payment-unpaid';
                                                    }
                                                    if($row_booking->order_payment==null){ 
                                                        $order_payment="ไม่ได้ซื้อคอร์ส";
                                                    }else{
                                                        $order_payment=$row_booking->order_payment;
                                                    }
                                                    echo "<span class='payment-status $payment_class'>" . htmlspecialchars($order_payment) . "</span>";
                                                    ?>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow dropdown-toggle-z-index" data-bs-toggle="dropdown">
                                                            <i class="ri-more-fill"></i>
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-z-index">
                                                            <a class="dropdown-item dropdown-item-z-index" href="javascript:void(0);" onclick="showOrderDetails(<?php echo $row_booking->oc_id; ?>)">
                                                                <i class="ri-eye-line me-1"></i> ดูรายละเอียด
                                                            </a>
                                                            <?php if ($row_booking->status == 'pending'): ?>
                                                                <a class="dropdown-item dropdown-item-z-index" href="javascript:void(0);" onclick="confirmApprove('booking-show.php?booking_id=<?php echo $row_booking->id; ?>&approve=1')">
                                                                    <i class="ri-check-line me-1"></i> อนุมัติ
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if ($row_booking->status != 'cancelled'): ?>
                                                                <a class="dropdown-item dropdown-item-z-index" href="javascript:void(0);" onclick="confirmCancel('booking-show.php?booking_id=<?php echo $row_booking->id; ?>&cancel=1')">
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
                        <!--/ Booking List Table -->
                    </div>
                    <!-- / Content -->
                    <?php include 'footer.php'; ?>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Order Details -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">รายละเอียดการสั่งซื้อ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Order details will be loaded here -->
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
    <!-- <script src="../assets/js/tables-datatables-basic.js"></script> -->
    <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>


    <script>
$(document).ready(function() {
    var table = $('.datatables-bookings').DataTable({
        displayLength: 10,
        lengthMenu: [ 10, 25, 50, 75, 100],
        buttons: [], // Export buttons removed
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function(row) {
                        var data = row.data();
                        return 'รายละเอียดการจองของ ' + data[2];
                    }
                }),
                type: 'column',
                renderer: function(api, rowIdx, columns) {
                    var data = $.map(columns, function(col, i) {
                        return col.title !== '' // ? Do not show actions column in modal
                            ? '<tr data-dt-row="' +
                                col.rowIndex +
                                '" data-dt-column="' +
                                col.columnIndex +
                                '">' +
                                '<td>' +
                                col.title +
                                ':' +
                                '</td> ' +
                                '<td>' +
                                col.data +
                                '</td>' +
                                '</tr>'
                            : '';
                    }).join('');

                    return data ? $('<table class="table"/><tbody />').append(data) : false;
                }
            }
        },
        createdRow: function(row, data, dataIndex) {
            $(row).addClass('clickable-row');
            $(row).attr('data-cus-id', data[1]); // Assuming cus_id is the second column (index 1)
        }
    });

    // $('div.head-label').html('<h5 class="card-title mb-0">รายการจองคอร์สทั้งหมด</h5>');

    // เพิ่ม event listener สำหรับการคลิกที่แถว
    $('.datatables-bookings tbody').on('click', 'tr', function(e) {
        // ตรวจสอบว่าคลิกที่ปุ่ม dropdown หรือรายการเมนูหรือไม่
        if (!$(e.target).closest('.dropdown-toggle, .dropdown-item').length) {
            var customerId = $(this).data('customer-id');
            if (customerId) {
                window.location.href = 'customer-detail.php?id=' + customerId;
            }
        }
    });

    // เพิ่ม event listener สำหรับปุ่ม dropdown และรายการเมนู
    $('.dropdown-toggle, .dropdown-item').on('click', function(e) {
        e.stopPropagation(); // ป้องกันการ bubble up ของ event
    });
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
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถโหลดข้อมูลรายละเอียดการสั่งซื้อได้',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
            });
        }
    });
}

function confirmApprove(url) {
    Swal.fire({
        title: 'ยืนยันการอนุมัติ?',
        text: "คุณต้องการอนุมัติการจองนี้ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, อนุมัติ!',
        cancelButtonText: 'ยกเลิก',
        customClass: {
            confirmButton: 'btn btn-primary me-3',
            cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

function confirmCancel(url) {
    Swal.fire({
        title: 'ยืนยันการยกเลิก?',
        text: "คุณต้องการยกเลิกการจองนี้ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, ยกเลิก!',
        cancelButtonText: 'ไม่',
        customClass: {
            confirmButton: 'btn btn-danger me-3',
            cancelButton: 'btn btn-label-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

<?php if(isset($_SESSION['msg_ok'])): ?>
Swal.fire({
    icon: 'success',
    title: 'สำเร็จ!',
    text: '<?php echo $_SESSION['msg_ok']; ?>',
    customClass: {
        confirmButton: 'btn btn-success'
    },
    buttonsStyling: false
});
<?php unset($_SESSION['msg_ok']); endif; ?>

<?php if(isset($_SESSION['msg_error'])): ?>
Swal.fire({
    icon: 'error',
    title: 'เกิดข้อผิดพลาด!',
    text: '<?php echo $_SESSION['msg_error']; ?>',
    customClass: {
        confirmButton: 'btn btn-danger'
    },
    buttonsStyling: false
});
<?php unset($_SESSION['msg_error']); endif; ?>
</script>
</body>
</html>