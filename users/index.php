<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['users_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = mysqli_real_escape_string($conn, $_SESSION['users_id']);

$sql = "SELECT * FROM customer WHERE cus_id = '$user_id'";
$result = $conn->query($sql);
$users = mysqli_fetch_object($result);

if ($users->cus_firstname == null || $users->cus_lastname == null || $users->cus_id_card_number == null || $users->cus_birthday == null || $users->cus_title == null || $users->cus_gender == null || $users->cus_tel == null) {
    $_SESSION['msg_info'] = "กรุณากรอกข้อมูลให้ครบ ก่อนเริ่มใช้งาน";
    header('Location: user-profile.php');
    exit();
}

// Fetch upcoming appointments
$appointments_query = "SELECT cb.*, c.course_name 
                       FROM course_bookings cb
                       LEFT JOIN order_course oc ON cb.id = oc.course_bookings_id
                       LEFT JOIN order_detail od ON oc.oc_id = od.oc_id
                       LEFT JOIN course c ON od.course_id = c.course_id
                       WHERE cb.cus_id = '$user_id' AND cb.booking_datetime > NOW() 
                       ORDER BY cb.booking_datetime ASC LIMIT 3";
$appointments_result = $conn->query($appointments_query);

// Fetch enrolled courses
$courses_query = "SELECT c.course_name, c.course_pic, oc.order_datetime 
                  FROM order_detail od 
                  JOIN order_course oc ON od.oc_id = oc.oc_id 
                  JOIN course c ON od.course_id = c.course_id 
                  WHERE oc.cus_id = '$user_id' 
                  ORDER BY oc.order_datetime DESC LIMIT 3";
$courses_result = $conn->query($courses_query);
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
        <style>
        .welcome-banner {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .welcome-banner h2 {
            color: #333;
            margin-bottom: 10px;
        }
        .info-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease-in-out;
        }
        .info-card:hover {
            transform: translateY(-5px);
        }
        .info-card h3 {
            color: #4e73df;
            margin-bottom: 15px;
        }
        .appointment-item, .course-item {
            background-color: #f1f3f9;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .course-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 10px;
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
                        <div class="welcome-banner">
                            <h2>Welcome, <?php echo htmlspecialchars($users->cus_firstname . ' ' . $users->cus_lastname); ?>!</h2>
                            <p>Here's an overview of your account and upcoming activities.</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <h3><i class="mdi mdi-calendar-clock"></i> Upcoming Appointments</h3>
                                    <?php
                                    if ($appointments_result->num_rows > 0) {
                                        while ($appointment = $appointments_result->fetch_assoc()) {
                                            echo "<div class='appointment-item'>";
                                            echo "<strong>" . date('F j, Y, g:i a', strtotime($appointment['booking_datetime'])) . "</strong><br>";
                                            echo "Course: " . htmlspecialchars($appointment['course_name'] ?? 'N/A');
                                            echo "</div>";
                                        }
                                    } else {
                                        echo "<p>No upcoming appointments.</p>";
                                    }
                                    ?>
                                    <a href="user-appointments.php" class="btn btn-primary mt-3">View All Appointments</a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <h3><i class="mdi mdi-book-open-variant"></i> Your Recent Courses</h3>
                                    <?php
                                    if ($courses_result->num_rows > 0) {
                                        while ($course = $courses_result->fetch_assoc()) {
                                            echo "<div class='course-item d-flex align-items-center'>";
                                            echo "<img src='../img/course/" . htmlspecialchars($course['course_pic']) . "' alt='" . htmlspecialchars($course['course_name']) . "' class='course-image'>";
                                            echo "<div>";
                                            echo "<strong>" . htmlspecialchars($course['course_name']) . "</strong><br>";
                                            echo "Enrolled on: " . date('F j, Y', strtotime($course['order_datetime']));
                                            echo "</div>";
                                            echo "</div>";
                                        }
                                    } else {
                                        echo "<p>No courses enrolled.</p>";
                                    }
                                    ?>
                                    <a href="user-courses.php" class="btn btn-primary mt-3">View All Courses</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="info-card">
                                    <h3><i class="mdi mdi-account-circle"></i> Your Profile Summary</h3>
                                    <ul class="list-unstyled">
                                        <li><strong>Email:</strong> <?php echo htmlspecialchars($users->cus_email); ?></li>
                                        <li><strong>Phone:</strong> <?php echo htmlspecialchars($users->cus_tel); ?></li>
                                        <li><strong>Line ID:</strong> <?php echo htmlspecialchars($users->line_user_id); ?></li>
                                    </ul>
                                    <a href="user-profile.php" class="btn btn-outline-primary">Edit Profile</a>
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
