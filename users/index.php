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

// ฟังก์ชันสำหรับแปลงวันที่เป็นภาษาไทยและ พ.ศ.
function thaiDate($date) {
    $thai_months = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน',
        7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    ];
    $date = new DateTime($date);
    $year = $date->format('Y') + 543;
    $month = $thai_months[(int)$date->format('n')];
    $day = $date->format('j');
    return "$day $month พ.ศ. $year";
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
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .welcome-banner h2 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .info-card {
            background-color: #fff;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }
        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .info-card h3 {
            color: #4e73df;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        .appointment-item, .course-item {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: background-color 0.3s ease;
        }
        .appointment-item:hover, .course-item:hover {
            background-color: #e9ecef;
        }
        .course-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 15px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        .btn-custom {
            background-color: #4e73df;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn-custom:hover {
            background-color: #2e59d9;
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
                    <h2 class="text-white">ยินดีต้อนรับ, คุณ<?php echo htmlspecialchars($users->cus_firstname . ' ' . $users->cus_lastname); ?>!</h2>
                    <p>นี่คือภาพรวมบัญชีและกิจกรรมที่กำลังจะมาถึงของคุณ</p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-card">
                            <h3><i class="mdi mdi-calendar-clock"></i> การนัดหมายที่กำลังจะมาถึง</h3>
                            <?php
                            if ($appointments_result->num_rows > 0) {
                                while ($appointment = $appointments_result->fetch_assoc()) {
                                    echo "<div class='appointment-item'>";
                                    echo "<strong>" . thaiDate($appointment['booking_datetime']) . " เวลา " . date('H:i', strtotime($appointment['booking_datetime'])) . " น.</strong><br>";
                                    echo "คอร์ส: " . htmlspecialchars($appointment['course_name'] ?? 'ไม่ระบุ');
                                    echo "</div>";
                                }
                            } else {
                                echo "<p>ไม่มีการนัดหมายที่กำลังจะมาถึง</p>";
                            }
                            ?>
                            <a href="user-appointments.php" class="btn btn-custom mt-3">ดูการนัดหมายทั้งหมด</a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card">
                            <h3><i class="mdi mdi-book-open-variant"></i> คอร์สล่าสุดของคุณ</h3>
                            <?php
                            if ($courses_result->num_rows > 0) {
                                while ($course = $courses_result->fetch_assoc()) {
                                    echo "<div class='course-item d-flex align-items-center'>";
                                    echo "<img src='../img/course/" . htmlspecialchars($course['course_pic']) . "' alt='" . htmlspecialchars($course['course_name']) . "' class='course-image'>";
                                    echo "<div>";
                                    echo "<strong>" . htmlspecialchars($course['course_name']) . "</strong><br>";
                                    echo "ลงทะเบียนเมื่อ: " . thaiDate($course['order_datetime']);
                                    echo "</div>";
                                    echo "</div>";
                                }
                            } else {
                                echo "<p>ยังไม่มีคอร์สที่ลงทะเบียน</p>";
                            }
                            ?>
                            <a href="user-courses.php" class="btn btn-custom mt-3">ดูคอร์สทั้งหมด</a>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="info-card">
                            <h3><i class="mdi mdi-account-circle"></i> ข้อมูลโปรไฟล์ของคุณ</h3>
                            <ul class="list-unstyled">
                                <li><strong>อีเมล:</strong> <?php echo htmlspecialchars($users->cus_email); ?></li>
                                <li><strong>เบอร์โทรศัพท์:</strong> <?php echo htmlspecialchars($users->cus_tel); ?></li>
                                <li><strong>ไลน์ไอดี:</strong> <?php echo htmlspecialchars($users->line_user_id); ?></li>
                            </ul>
                            <a href="user-profile.php" class="btn btn-custom">แก้ไขโปรไฟล์</a>
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
