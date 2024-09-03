<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['users_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['users_id'];

// ฟังก์ชันสำหรับแปลงวันที่เป็นภาษาไทยและ พ.ศ.
function thaiDateTime($datetime) {
    $thai_months = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน',
        7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    ];
    $date = new DateTime($datetime);
    $year = $date->format('Y') + 543;
    $month = $thai_months[(int)$date->format('n')];
    $day = $date->format('j');
    $time = $date->format('H:i');
    return "$day $month พ.ศ. $year เวลา $time น.";
}

// ฟังก์ชันสำหรับแปลสถานะเป็นภาษาไทย
function translateStatus($status) {
    switch ($status) {
        case 'pending':
            return 'รอดำเนินการ';
        case 'confirmed':
            return 'ยืนยันแล้ว';
        case 'cancelled':
            return 'ยกเลิก';
        default:
            return $status;
    }
}

$query = "SELECT cb.*, c.course_name, c.course_pic
          FROM course_bookings cb 
          LEFT JOIN order_course oc ON cb.id = oc.course_bookings_id
          LEFT JOIN order_detail od ON oc.oc_id = od.oc_id
          LEFT JOIN course c ON od.course_id = c.course_id 
          WHERE cb.cus_id = ? 
          ORDER BY cb.booking_datetime DESC";

$stmt = $conn->prepare($query);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>


<!doctype html>

<html
  lang="en"
  class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../../assets/"
  data-template="vertical-menu-template-no-customizer-starter"
  data-style="light">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>การนัดหมายของคุณ - D Care Clinic System</title>

    <meta name="description" content="" />

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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css"> 
    <style>
        .appointment-card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            margin-bottom: 20px;
        }
        .appointment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .appointment-image {
            height: 150px;
            object-fit: cover;
        }
        .appointment-status {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-confirmed { background-color: #28a745; color: #fff; }
        .status-cancelled { background-color: #dc3545; color: #fff; }
        .appointment-date {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .appointment-time {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        .appointment-course {
            font-weight: 500;
        }
        .no-appointments {
            text-align: center;
            padding: 50px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        
        <?php include 'menu.php'; ?>

        <!-- Layout container -->
        <div class="layout-page">
          
          <?php include 'navbar.php'; ?>

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                <h4 class="fw-bold py-3 mb-4">การนัดหมายของคุณ</h4>

                <div class="row">
                    <?php 
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()): 
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card appointment-card">
                            <img src="../img/course/<?php echo htmlspecialchars($row['course_pic'] ?? 'default.jpg'); ?>" class="card-img-top appointment-image" alt="<?php echo htmlspecialchars($row['course_name'] ?? 'คอร์ส'); ?>">
                            <div class="card-body">
                                <span class="appointment-status status-<?php echo strtolower($row['status']); ?>"><?php echo translateStatus($row['status']); ?></span>
                                <h5 class="card-title appointment-date"><?php echo thaiDateTime($row['booking_datetime']); ?></h5>
                                <p class="card-text appointment-course"><i class="mdi mdi-book-open-page-variant"></i> <?php echo htmlspecialchars($row['course_name'] ?? 'ไม่ระบุ'); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endwhile;
                    } else {
                    ?>
                    <div class="col-12">
                        <div class="no-appointments">
                            <h3><i class="mdi mdi-calendar-blank"></i></h3>
                            <p class="lead">คุณยังไม่มีการนัดหมาย</p>
                            <a href="user-courses.php" class="btn btn-primary">ดูคอร์สที่เปิดให้บริการ</a>
                        </div>
                    </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
    <!-- / Content -->

            <?php   include 'footer.php'; ?>

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- sweet Alerts 2 -->
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
    <script src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.dataTables.js"></script>

  </body>
</html>
