<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['users_id'])) {
    header('Location: ../login.php');
    exit;
}

// Fetch user's enrolled courses
$stmt = $conn->prepare("
    SELECT c.*, od.od_amount, oc.order_datetime 
    FROM order_detail od 
    JOIN order_course oc ON od.oc_id = oc.oc_id 
    JOIN course c ON od.course_id = c.course_id 
    WHERE oc.cus_id = ? 
    ORDER BY oc.order_datetime DESC
");
$stmt->bind_param("i", $_SESSION['users_id']);
$stmt->execute();
$enrolled_courses = $stmt->get_result();

// Fetch available courses
$stmt = $conn->prepare("
    SELECT * FROM course 
    WHERE course_status = 1 
    AND course_id NOT IN (
        SELECT od.course_id 
        FROM order_detail od 
        JOIN order_course oc ON od.oc_id = oc.oc_id 
        WHERE oc.cus_id = ?
    )
");
$stmt->bind_param("i", $_SESSION['users_id']);
$stmt->execute();
$available_courses = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template">
<head>
    <!-- ... (same head content as user-dashboard.php) ... -->
    <title>User Courses - D Care Clinic System</title>
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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css"> 

</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="index.html" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <!-- Your logo here -->
                        </span>
                        <span class="app-brand-text demo menu-text fw-bolder ms-2">D Care Clinic</span>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <li class="menu-item ">
                        <a href="index.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Dashboard">Dashboard</div>
                        </a>
                    </li>
                    <li class="menu-item ">
                        <a href="user-profile.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user"></i>
                            <div data-i18n="Profile">Profile</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="user-appointments.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-calendar"></i>
                            <div data-i18n="Appointments">Appointments</div>
                        </a>
                    </li>
                    <li class="menu-item active">
                        <a href="user-courses.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-book"></i>
                            <div data-i18n="Courses">Courses</div>
                        </a>
                    </li>
                </ul>
            </aside>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <!-- ... (navbar content) ... -->
                </nav>
                <!-- / Navbar -->

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Courses</h4>

        <!-- Enrolled Courses -->
        <div class="card mb-4">
            <h5 class="card-header">Your Enrolled Courses</h5>
            <div class="card-body">
                <div class="row">
                    <?php while ($course = $enrolled_courses->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <img class="card-img-top" src="../img/course/<?php echo htmlspecialchars($course['course_pic']); ?>" alt="<?php echo htmlspecialchars($course['course_name']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($course['course_name']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars(substr($course['course_detail'], 0, 100)) . '...'; ?></p>
                                    <p class="card-text"><small class="text-muted">Enrolled on: <?php echo date('F j, Y', strtotime($course['order_datetime'])); ?></small></p>
                                    <a href="course-details.php?id=<?php echo $course['course_id']; ?>" class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Available Courses -->
        <div class="card">
            <h5 class="card-header">Available Courses</h5>
            <div class="card-body">
                <div class="row">
                    <?php while ($course = $available_courses->fetch_assoc()): ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <img class="card-img-top" src="../img/course/<?php echo htmlspecialchars($course['course_pic']); ?>" alt="<?php echo htmlspecialchars($course['course_name']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($course['course_name']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars(substr($course['course_detail'], 0, 100)) . '...'; ?></p>
                                    <p class="card-text"><small class="text-muted">Price: <?php echo number_format($course['course_price'], 2); ?> THB</small></p>
                                    <a href="course-details.php?id=<?php echo $course['course_id']; ?>" class="btn btn-primary">View Details</a>
                                    <a href="enroll-course.php?id=<?php echo $course['course_id']; ?>" class="btn btn-success">Enroll</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->

<!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <!-- ... (footer content) ... -->
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>

    <!--/ Layout wrapper -->

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js"></script>
</body>
</html>