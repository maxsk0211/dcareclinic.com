<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

// เพิ่ม error reporting เพื่อ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);
$branch_id=$_SESSION['branch_id'];
// คิวรี่ข้อมูลจากตาราง order_course และตารางที่เกี่ยวข้อง
$sql = "SELECT oc.oc_id, oc.order_datetime, c.cus_firstname, c.cus_lastname, 
               oc.order_payment, oc.order_net_total, cb.booking_datetime
        FROM order_course oc
        JOIN customer c ON oc.cus_id = c.cus_id
        JOIN course_bookings cb ON oc.course_bookings_id = cb.id
        WHERE oc.branch_id='$branch_id'
        ORDER BY oc.order_datetime DESC";
$result = $conn->query($sql);
?>
<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>รายการบิล - D Care Clinic</title>

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
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <style>

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
                    <div class="container flex-grow-1 container-p-y">
                        <!-- <h4 class="py-3 mb-4"><span class="text-muted fw-light"></span> รายการบิล</h4> -->
                        
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title">รายการคำสั่งซื้อทั้งหมด</h5>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#logModal">
                                    <i class="ri-history-line me-1"></i> ประวัติการยกเลิกการชำระเงิน
                                </button>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped" id="orderTable">
                                    <thead>
                                        <tr>
                                            <th>เลขที่บิล</th>
                                            <th>วันที่สร้างบิล</th>
                                            <th>ชื่อลูกค้า</th>
                                            <th>วันที่นัดรับบริการ</th>
                                            <th>สถานะการชำระเงิน</th>
                                            <th>ยอดรวม</th>
                                            <th>ดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo 'ORDER-' . str_pad($row['oc_id'], 6, '0', STR_PAD_LEFT); ?></td>
                                            <td data-order="<?php echo date('Y-m-d H:i:s', strtotime($row['order_datetime'])); ?>">
                                                <?php echo date('d/m/Y H:i', strtotime($row['order_datetime'])); ?>
                                            </td>
                                            <td><?php echo $row['cus_firstname'] . ' ' . $row['cus_lastname']; ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($row['booking_datetime'])); ?></td>
                                            <td><?php echo $row['order_payment']; ?></td>
                                            <td><?php echo number_format($row['order_net_total'], 2); ?> บาท</td>
                                            <td>
                                                <a href="edit-order.php?id=<?php echo $row['oc_id']; ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                                <a href="bill.php?id=<?php echo $row['oc_id']; ?>" class="btn btn-primary btn-sm">บิล</a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <?php include 'footer.php'; ?>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- / Content wrapper -->
            </div>
            <!-- / Layout container -->
        </div>
    </div>
    <!-- / Layout wrapper -->

<!-- เพิ่ม Modal สำหรับแสดง Log -->
<div class="modal fade" id="logModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ประวัติการยกเลิกการชำระเงิน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table" id="logTable">
                        <thead>
                            <tr>
                                <th>วันที่</th>
                                <th>ผู้ยกเลิก</th>
                                <th>เลขที่บิล</th>
                                <th>ลูกค้า</th>
                                <th>จำนวนเงิน</th>
                                <th>เหตุผล</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query logs
                            $log_sql = "SELECT al.*, u.users_fname, u.users_lname 
                                      FROM activity_logs al
                                      JOIN users u ON al.user_id = u.users_id
                                      WHERE al.action = 'cancel_payment'
                                      AND al.branch_id = ?
                                      ORDER BY al.id DESC";
                            $stmt = $conn->prepare($log_sql);
                            $stmt->bind_param("i", $_SESSION['branch_id']);
                            $stmt->execute();
                            $logs = $stmt->get_result();
                            
                            while($log = $logs->fetch_assoc()) {
                                $details = json_decode($log['details'], true);
                                ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?></td>
                                    <td><?php echo $log['users_fname'] . ' ' . $log['users_lname']; ?></td>
                                    <td><?php echo 'ORDER-' . str_pad($log['entity_id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo $details['customer_info']['name']; ?></td>
                                    <td><?php echo number_format($details['payment_info']['amount'], 2); ?></td>
                                    <td><?php echo $details['reason']; ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#logTable').DataTable({
            "pageLength": 10,
            "ordering": false,  
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
            }
        });

        $('#orderTable').DataTable({
            "pageLength": 25,
            "order": [[1, "desc"]], // เรียงลำดับคอลัมน์ที่ 1 (วันที่สั่งซื้อ) จากมากไปน้อย
            "columnDefs": [
                { "type": "date", "targets": 1 } // กำหนดให้คอลัมน์ที่ 1 เป็นประเภทวันที่
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
            }
        });


    });

// Display success message
      <?php if(isset($_SESSION['msg_ok'])){ ?>
        Swal.fire({
          icon: 'success',
          title: 'แจ้งเตือน!',
          text: '<?php echo $_SESSION['msg_ok']; ?>',
          customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light'
          },
          buttonsStyling: false
        });
      <?php unset($_SESSION['msg_ok']); } ?>

      // Display error message
      <?php if(isset($_SESSION['msg_error'])){ ?>
        Swal.fire({
          icon: 'error',
          title: 'แจ้งเตือน!',
          text: '<?php echo $_SESSION['msg_error']; ?>',
          customClass: {
            confirmButton: 'btn btn-danger waves-effect waves-light'
          },
          buttonsStyling: false
        });
      <?php unset($_SESSION['msg_error']); } ?>
</script>
</body>
</html>