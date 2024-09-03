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

if (!isset($_GET['id'])) {
    header('Location: user-courses.php');
    exit;
}

$course_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM course WHERE course_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    header('Location: user-courses.php');
    exit;
}

// Check if the user is enrolled in this course
$stmt = $conn->prepare("
    SELECT 1 FROM order_detail od 
    JOIN order_course oc ON od.oc_id = oc.oc_id 
    WHERE oc.cus_id = ? AND od.course_id = ?
");
$stmt->bind_param("ii", $_SESSION['users_id'], $course_id);
$stmt->execute();
$is_enrolled = $stmt->get_result()->num_rows > 0;
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

    <title><?php echo htmlspecialchars($course['course_name']); ?> - D Care Clinic System</title>
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
        .course-header {
            background-color: #f8f9fa;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .course-image {
            max-height: 400px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .course-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .course-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 1rem;
        }
        .course-meta {
            margin-bottom: 1rem;
        }
        .course-meta i {
            margin-right: 0.5rem;
        }
        .course-description {
            font-size: 1.1rem;
            line-height: 1.6;
        }
        .enroll-section {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
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
                        <div class="course-header">
                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h1 class="course-title"><?php echo htmlspecialchars($course['course_name']); ?></h1>
                                        <div class="course-price"><?php echo number_format($course['course_price'], 2); ?> THB</div>
                                        <div class="course-meta">
                                            <p><i class="mdi mdi-calendar"></i> Start Date: <?php echo date('F j, Y', strtotime($course['course_start'])); ?></p>
                                            <p><i class="mdi mdi-calendar-clock"></i> End Date: <?php echo date('F j, Y', strtotime($course['course_end'])); ?></p>
                                            <p><i class="mdi mdi-clock-outline"></i> Duration: <?php echo $course['duration']; ?> minutes</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <img src="../img/course/<?php echo htmlspecialchars($course['course_pic']); ?>" alt="<?php echo htmlspecialchars($course['course_name']); ?>" class="img-fluid course-image">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="container">
                            <div class="row">
                                <div class="col-md-8">
                                    <h2>Course Description</h2>
                                    <div class="course-description">
                                        <?php echo nl2br(htmlspecialchars($course['course_detail'])); ?>
                                    </div>
                                    <?php if (!empty($course['course_note'])): ?>
                                        <h3 class="mt-4">Additional Notes</h3>
                                        <p><?php echo nl2br(htmlspecialchars($course['course_note'])); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <div class="enroll-section">
                                        <?php if ($is_enrolled): ?>
                                            <h3>You are enrolled in this course</h3>
                                            <p>Access your course materials and track your progress.</p>
                                            <a href="#" class="btn btn-primary btn-lg btn-block">Go to Course</a>
                                        <?php else: ?>
                                            <h3>Enroll in this course</h3>
                                            <p>Join now to start your learning journey!</p>
                                            <a href="enroll-course.php?id=<?php echo $course_id; ?>" class="btn btn-success btn-lg btn-block">Enroll Now</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
