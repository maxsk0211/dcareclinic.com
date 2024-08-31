<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

// ดึงข้อมูลการจองทั้งหมด
$sql = "SELECT cb.*, cu.cus_firstname, cu.cus_lastname, u.users_fname, oc.oc_id, oc.order_net_total, oc.order_payment
        FROM course_bookings cb
        JOIN customer cu ON cb.cus_id = cu.cus_id
        JOIN users u ON cb.users_id = u.users_id
        LEFT JOIN order_course oc ON cb.id = oc.course_bookings_id
        WHERE cb.branch_id = {$_SESSION['branch_id']}
        ORDER BY cb.id DESC";

$result_booking = $conn->query($sql);

if (!$result_booking) {
    die("Error fetching bookings: " . $conn->error);
}

// ฟังก์ชันยกเลิกการจอง
if (isset($_GET['del']) && $_GET['del'] == 1) {
    $booking_id = mysqli_real_escape_string($conn, $_GET['booking_id']);
    $cancel_sql = "UPDATE course_bookings SET status = 'cancelled' WHERE id = '$booking_id'";
    if (mysqli_query($conn, $cancel_sql)) {
        $_SESSION['msg_ok'] = "ยกเลิกการจองเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการยกเลิกการจอง: " . mysqli_error($conn);
    }
    header("Location: booking-detail.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter">
<head>
    <!-- [เนื้อหาส่วน head ยังคงเหมือนเดิม] -->
</head>

<body>
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">
            <?php include 'navbar.php'; ?>
            <div class="layout-page">
                <div class="content-wrapper">
                    <?php include 'menu.php'; ?>
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="card">
                            <div class="card-header">
                                <h4>รายละเอียดการจองคอร์ส</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="bookingsTable" class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>วันที่จอง</th>
                                                <th>ชื่อลูกค้า</th>
                                                <th>ผู้ทำรายการ</th>
                                                <th>ยอดรวม</th>
                                                <th>วิธีการชำระเงิน</th>
                                                <th>สถานะ</th>
                                                <th>การดำเนินการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $i = 1; 
                                            while ($row_booking = mysqli_fetch_object($result_booking)): 
                                            ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td><?php echo (new DateTime($row_booking->booking_datetime))->modify('+543 years')->format('d/m/Y H:i:s'); ?></td>
                                                    <td><?php echo htmlspecialchars($row_booking->cus_firstname . ' ' . $row_booking->cus_lastname); ?></td>
                                                    <td><?php echo $row_booking->users_fname; ?></td>
                                                    <td><?php echo number_format($row_booking->order_net_total, 2); ?> บาท</td>
                                                    <td><?php echo $row_booking->order_payment; ?></td>
                                                    <td>
                                                        <?php
                                                        switch ($row_booking->status) {
                                                            case 'confirmed':
                                                                echo '<span class="badge bg-success">ยืนยันแล้ว</span>';
                                                                break;
                                                            case 'cancelled':
                                                                echo '<span class="badge bg-danger">ยกเลิกแล้ว</span>';
                                                                break;
                                                            default:
                                                                echo '<span class="badge bg-warning">รอยืนยัน</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-info btn-sm" onclick="showOrderDetails(<?php echo $row_booking->oc_id; ?>)">รายละเอียด</button>
                                                        <?php if ($row_booking->status == 'confirmed'): ?>
                                                            <button class="btn btn-danger btn-sm" onclick="confirmDelete('booking-detail.php?booking_id=<?php echo $row_booking->id; ?>&del=1')">ยกเลิกการจอง</button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
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

    <!-- [Scripts ยังคงเหมือนเดิม] -->

    <script>
    $(document).ready(function() {
        $('#bookingsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
            },
            "order": [[0, "desc"]]
        });
    });

    function confirmDelete(url) {
        Swal.fire({
            title: 'คุณแน่ใจหรือไม่ที่จะยกเลิกการจอง?',
            text: "การยกเลิกนี้ไม่สามารถกู้คืนได้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ใช่, ยกเลิกการจอง!',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    function showOrderDetails(orderId) {
        $.ajax({
            url: 'get_order_details.php',
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

    // [โค้ด JavaScript สำหรับแสดง SweetAlert ยังคงเหมือนเดิม]
    </script>
</body>
</html>