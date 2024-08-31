<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

// ดึงข้อมูลการจองทั้งหมด (ใช้ mysqli_query)
$sql = "SELECT cb.*, c.course_name, cu.cus_firstname, cu.cus_lastname, u.*
        FROM course_bookings cb
        JOIN course c ON cb.course_id = c.course_id
        JOIN customer cu ON cb.cus_id = cu.cus_id
        JOIN users u ON cb.users_id = u.users_id
        WHERE cb.branch_id = {$_SESSION['branch_id']}
        ORDER BY cb.id DESC";

$result_booking = $conn->query($sql);

if (!$result_booking) {
    die("Error fetching bookings: " . $conn->error);
}

// $bookings = [];
// while ($row = $result->fetch_object()) { // ใช้ mysqli_fetch_object
//     $bookings[] = $row;
// }

// ฟังก์ชันยกเลิกการจอง (ใช้ mysqli_query)
if (isset($_GET['del']) and $_GET['del']==1) {
    $booking_id = $_GET['booking_id'];

    // escape ตัวแปรเพื่อป้องกัน SQL injection
    $booking_id = mysqli_real_escape_string($conn, $booking_id);

    $cancel_sql = "UPDATE course_bookings SET status = 'cancelled' WHERE id = '$booking_id'";
    if (mysqli_query($conn, $cancel_sql)) {
        $_SESSION['msg_ok'] = "ยกเลิกการจองเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการยกเลิกการจอง: " . mysqli_error($conn);
    }
    header("Location: booking-show.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>แสดงการจองคอร์ส - D Care Clinic</title>
    <meta name="description" content="" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />
    <!-- <link rel="stylesheet" href="../assets/vendor/fonts/flag-icons.css" /> -->

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
    <!-- sweet Alerts 2 -->
    <link rel="stylesheet" href="../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/sweetalert2/sweetalert2.css" />

    <!-- datatables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script src="../assets/js/config.js"></script>
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

                        <!-- Basic Bootstrap Table -->
                        <div class="card">
                            <div class="container">
                        

                            <div class="table-responsive text-nowrap">
                                <h4 class="py-3 mb-4">แสดงการจองคอร์ส</h4>
                                <table id="bookingsTable" class="table table-striped table-hover table-primary">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>วันที่จอง</th>
                                            <th>ชื่อคอร์ส</th>
                                            <th>ชื่อลูกค้า</th>
                                            <th>จองเมื่อ</th>
                                            <th>ผู้ทำรายการ</th>
                                            <th>สถานะ</th>
                                            <th>การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; while ($row_booking=mysqli_fetch_object($result_booking)): ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php $date_obj = new DateTime($row_booking->booking_datetime); $date_obj->modify('+543 years');echo $date_obj->format('d/m/Y H:i:s'); ?></td>
                                                <td><?php echo htmlspecialchars($row_booking->course_name); ?></td>
                                                <td><?php echo htmlspecialchars($row_booking->cus_firstname . ' ' . $row_booking->cus_lastname); ?></td>
                                                <td><?php $date_obj = new DateTime($row_booking->created_at); $date_obj->modify('+543 years');echo $date_obj->format('d/m/Y H:i:s'); ?></td>
                                                <td><?php echo $row_booking->users_fname ?></td>
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
                                                    <?php if ($row_booking->status == 'confirmed'): ?>
                                                        <a href="" class="btn btn-xs btn-danger" onClick="confirmDelete('booking-show.php?booking_id=<?php echo $row_booking->id; ?>&del=1'); return false;">ยกเลิกการจอง</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </div>
                        <!--/ Basic Bootstrap Table -->
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

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js" />
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- datatables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <!-- <script src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script> -->
    <!-- <script src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.js"></script> -->
    <!-- <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.dataTables.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> -->
    <!-- <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js"></script> -->
    <script src="../assets/vendor/libs/cleavejs/cleave.js"></script>
    <script src="../assets/vendor/libs/cleavejs/cleave-phone.js"></script>


    <!-- Page JS -->
    <script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#bookingsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
            },
            "order": [[0, "asc"]] // Sort by booking date in descending order
        });
    });
      // ลบข้อมูล
          function confirmDelete(url) {
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
                top.location = url;
              }
            });
          };

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