<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['users_id'])) {
    header('Location: ../login.php');
    exit;
}

// escape ตัวแปรเพื่อป้องกัน SQL injection
$user_id = mysqli_real_escape_string($conn, $_SESSION['users_id']);

// สร้างคำสั่ง SQL โดยใช้ mysqli_real_escape_string
$sql = "SELECT * FROM customer WHERE cus_id = '$user_id'";

// ดำเนินการ query
$result = $conn->query($sql);
$users=mysqli_fetch_object($result);
if ($users->cus_firstname==null or $users->cus_lastname==null or $users->cus_id_card_number==null or $users->cus_birthday==null or $users->cus_title==null or $users->cus_gender==null or $users->cus_tel==null) {
    $_SESSION['msg_error']="กรุณากรอกข้อมูลให้ครบ ก่อนเริ่มใช้งาน";
    header('Location: user-profile.php');
    exit();
}
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

    <title>User Dashboard - D Care Clinic System</title>

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
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
                <h4 class="fw-bold py-3 mb-4">Dashboard</h4>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <h5 class="card-header">Welcome, <?php echo htmlspecialchars($users->cus_firstname . ' ' . $users->cus_lastname); ?>!</h5>
                            <div class="card-body">
                                <p>Here's a summary of your account:</p>
                                <ul>
                                    <li>Email: <?php echo htmlspecialchars($users->cus_email); ?></li>
                                    <li>Phone: <?php echo htmlspecialchars($users->cus_tel); ?></li>
                                    <li>Line ID: <?php echo htmlspecialchars($users->line_user_id); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <h5 class="card-header">Upcoming Appointments</h5>
                            <div class="card-body">
                                <!-- Fetch and display upcoming appointments -->
                                <?php
                                    // escape ตัวแปรเพื่อป้องกัน SQL injection
                                    $user_id = mysqli_real_escape_string($conn, $_SESSION['users_id']);

                                    // สร้างคำสั่ง SQL โดยใช้ mysqli_real_escape_string
                                    $sql = "SELECT * FROM course_bookings WHERE cus_id = '$user_id' AND booking_datetime > NOW() ORDER BY booking_datetime LIMIT 3";

                                    // ดำเนินการ query
                                    $result = $conn->query($sql);

                                    if ($result && $result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) { 
                                            echo "<p>" . htmlspecialchars(date('F j, Y, g:i a', strtotime($row['booking_datetime']))) . "</p>";
                                        }
                                    } else {
                                        echo "<p>No upcoming appointments.</p>";
}
                                ?>
                                <a href="user-appointments.php" class="btn btn-primary">View All Appointments</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <h5 class="card-header">Your Courses</h5>
                            <div class="card-body">
                                <!-- Fetch and display user's courses -->
                                <?php
                                $stmt = $conn->prepare("SELECT c.course_name FROM order_detail od JOIN order_course oc ON od.oc_id = oc.oc_id JOIN course c ON od.course_id = c.course_id WHERE oc.cus_id = ? LIMIT 3");
                                $stmt->bind_param("i", $_SESSION['users_id']);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<p>" . htmlspecialchars($row['course_name']) . "</p>";
                                    }
                                } else {
                                    echo "<p>No courses enrolled.</p>";
                                }
                                ?>
                                <a href="user-courses.php" class="btn btn-primary">View All Courses</a>
                            </div>
                        </div>
                    </div>
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
    <script>
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
