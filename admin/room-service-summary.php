<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
include 'chk-session.php';
require '../dbcon.php';

// ตรวจสอบวันที่ที่เลือก
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$branch_id = $_SESSION['branch_id'];

// ดึงข้อมูลห้องทั้งหมด
$sql_rooms = "
    SELECT r.*, 
           COALESCE(rs.daily_status, 'closed') AS daily_status
    FROM rooms r
    LEFT JOIN room_status rs ON r.room_id = rs.room_id AND rs.date = ?
    WHERE r.branch_id = ? AND r.status = 'active'
";
$stmt_rooms = $conn->prepare($sql_rooms);
$stmt_rooms->bind_param("si", $selected_date, $branch_id);
$stmt_rooms->execute();
$result_rooms = $stmt_rooms->get_result();

// Function สำหรับแปลงเดือนเป็นภาษาไทย
function thaiDate($date) {
    $thai_month_arr = array(
        "01"=>"ม.ค.", "02"=>"ก.พ.", "03"=>"มี.ค.", "04"=>"เม.ย.", 
        "05"=>"พ.ค.", "06"=>"มิ.ย.", "07"=>"ก.ค.", "08"=>"ส.ค.",
        "09"=>"ก.ย.", "10"=>"ต.ค.", "11"=>"พ.ย.", "12"=>"ธ.ค."
    );
    
    $year = date("Y", strtotime($date)) + 543;
    $month = date("m", strtotime($date));
    $day = date("d", strtotime($date));
    
    return $day . " " . $thai_month_arr[$month] . " " . $year;
}

// Function สำหรับจัดรูปแบบเวลา
function formatTime($time) {
    return date("H:i", strtotime($time));
}

// Function คำนวณอัตราการใช้งานห้อง
function calculateRoomUsage($roomId, $date, $conn) {
    // นับจำนวนสล็อตเวลาทั้งหมด
    $sql_slots = "SELECT SUM(
                    TIMESTAMPDIFF(MINUTE, start_time, end_time) / interval_minutes
                  ) as total_slots 
                  FROM room_schedules 
                  WHERE room_id = ? AND date = ?";
    
    // นับจำนวนการจองทั้งหมด
    $sql_bookings = "SELECT COUNT(*) as booked_slots
                     FROM course_bookings
                     WHERE room_id = ? 
                     AND DATE(booking_datetime) = ?
                     AND status != 'cancelled'";

    // ดึงข้อมูลสล็อตทั้งหมด
    $stmt = $conn->prepare($sql_slots);
    $stmt->bind_param("is", $roomId, $date);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc();
    
    // ดึงข้อมูลการจอง
    $stmt = $conn->prepare($sql_bookings);
    $stmt->bind_param("is", $roomId, $date);
    $stmt->execute();
    $booked = $stmt->get_result()->fetch_assoc();

    if ($total['total_slots'] > 0) {
        return ($booked['booked_slots'] / $total['total_slots']) * 100;
    }
    return 0;
}

// เพิ่มฟังก์ชันดึงตารางเวลาของห้อง
function getRoomSchedules($roomId, $date, $conn) {
   $sql = "SELECT schedule_id, start_time, end_time, interval_minutes 
           FROM room_schedules 
           WHERE room_id = ? AND date = ?
           ORDER BY start_time ASC"; // เพิ่ม ORDER BY
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("is", $roomId, $date);
   $stmt->execute();
   return $stmt->get_result();
}

// เพิ่มฟังก์ชันหาการจองในช่วงเวลา
function getBookingForTimeSlot($roomId, $dateTime, $conn) {
   $sql = "SELECT cb.*, c.cus_firstname, c.cus_lastname, co.course_name, oc.order_payment
           FROM course_bookings cb
           JOIN customer c ON cb.cus_id = c.cus_id
           JOIN order_course oc ON cb.id = oc.course_bookings_id
           JOIN order_detail od ON oc.oc_id = od.oc_id
           JOIN course co ON od.course_id = co.course_id
           WHERE cb.room_id = ? 
           AND cb.booking_datetime = ?
           AND cb.status != 'cancelled'
           ORDER BY cb.booking_datetime ASC"; // เพิ่ม ORDER BY
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("is", $roomId, $dateTime);
   $stmt->execute();
   return $stmt->get_result()->fetch_assoc();
}

?>
<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="horizontal-menu-template-no-customizer-starter"
  data-style="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>สรุปการให้บริการรายห้อง - D Care Clinic</title>
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

    <!-- Page CSS -->

        <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
        <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/sweetalert2/sweetalert2.css" />

    <!-- เพิ่ม CSS อื่นๆ ตามที่มีในหน้าอื่น -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
   <style>
        .room-card {
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }
        
        .room-card:hover {
            transform: translateY(-5px);
        }
        
        .usage-indicator {
            height: 8px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .usage-low {
            background: linear-gradient(to right, #28a745 0%, #28a745 100%);
        }
        
        .usage-medium {
            background: linear-gradient(to right, #ffc107 0%, #ffc107 100%);
        }
        
        .usage-high {
            background: linear-gradient(to right, #dc3545 0%, #dc3545 100%);
        }
        
        .time-slot {
            padding: 10px;
            border-radius: 4px;
            margin: 5px 0;
            font-size: 0.9rem;
        }
        
        .time-slot.available {
            background-color: #e8f5e9;
            border-left: 4px solid #28a745;
        }
        
        .time-slot.booked {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        
        .time-slot.in-use {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
        }

        .room-status {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .status-open {
            background-color: #d4edda;
            color: #155724;
        }

        .status-closed {
            background-color: #f8d7da;
            color: #721c24;
        }

        .occupancy-rate {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
        }

        .booking-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
        }

        .date-picker-wrapper {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .schedule-container {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .schedule-container::-webkit-scrollbar {
            width: 6px;
        }

        .schedule-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .schedule-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
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
               <div class="container-xxl flex-grow-1 container-p-y">
                   <div class="row mb-4">
                       <div class="col">
                           <h4>สรุปการใช้งานห้อง</h4>
                       </div>
                   </div>

                   <!-- Date Picker -->
                   <div class="card mb-4">
                       <div class="card-body">
                           <form action="" method="GET" id="dateForm" class="row align-items-center">
                               <div class="col-md-4">
                                   <label for="date" class="form-label">เลือกวันที่</label>
                                   <input type="date" class="form-control" id="date" name="date" 
                                          value="<?php echo $selected_date; ?>" onchange="this.form.submit()">
                               </div>
                           </form>
                       </div>
                   </div>

                   <!-- Room Cards -->
                   <div class="row">
                       <?php while($room = $result_rooms->fetch_assoc()): 
                           $usage_rate = calculateRoomUsage($room['room_id'], $selected_date, $conn);
                           $usage_class = '';
                           if($usage_rate >= 80) {
                               $usage_class = 'usage-high';
                           } else if($usage_rate >= 50) {
                               $usage_class = 'usage-medium';
                           } else {
                               $usage_class = 'usage-low';
                           }
                       ?>
                       <div class="col-md-6 col-lg-4">
                           <div class="card room-card">
                               <div class="card-header d-flex justify-content-between align-items-center">
                                   <h5 class="card-title mb-0"><?php echo $room['room_name']; ?></h5>
                                   <span class="room-status <?php echo $room['daily_status'] == 'open' ? 'status-open' : 'status-closed'; ?>">
                                       <?php echo $room['daily_status'] == 'open' ? 'เปิดใช้งาน' : 'ปิดใช้งาน'; ?>
                                   </span>
                               </div>
                               <div class="card-body">
                                   <div class="occupancy-rate text-center">
                                       <?php echo number_format($usage_rate, 1); ?>%
                                   </div>
                                   <div class="usage-indicator <?php echo $usage_class; ?>" 
                                        style="width: <?php echo $usage_rate; ?>%">
                                   </div>
                                   
                                   <?php
                                   // ดึงข้อมูลการจองของห้อง
                                   $sql_bookings = "SELECT 
                                       cb.id,
                                       cb.booking_datetime,
                                       c.cus_firstname,
                                       c.cus_lastname,
                                       co.course_name,
                                       oc.order_payment
                                   FROM course_bookings cb
                                   JOIN customer c ON cb.cus_id = c.cus_id
                                   JOIN order_course oc ON cb.id = oc.course_bookings_id
                                   JOIN order_detail od ON oc.oc_id = od.oc_id
                                   JOIN course co ON od.course_id = co.course_id
                                   WHERE cb.room_id = ? 
                                   AND DATE(cb.booking_datetime) = ?
                                   ORDER BY cb.booking_datetime ASC";

                                   $stmt_bookings = $conn->prepare($sql_bookings);
                                   $stmt_bookings->bind_param("is", $room['room_id'], $selected_date);
                                   $stmt_bookings->execute();
                                   $bookings = $stmt_bookings->get_result();
                                   ?>

                                   <div class="schedule-container mt-3">
									    <?php
									    $schedules = getRoomSchedules($room['room_id'], $selected_date, $conn);
									    $total_slots = 0;
									    $booked_slots = 0;
									    
									    while($schedule = $schedules->fetch_assoc()):
									        $start = strtotime($schedule['start_time']);
									        $end = strtotime($schedule['end_time']);
									        $interval = $schedule['interval_minutes'] * 60;
									        
									        for($time = $start; $time < $end; $time += $interval):
									            $dateTime = $selected_date . ' ' . date('H:i:s', $time);
									            $booking = getBookingForTimeSlot($room['room_id'], $dateTime, $conn);
									            $total_slots++;
									            if($booking) $booked_slots++;
									            
									            $status_class = $booking ? 'booked' : 'available';
									            if($booking && strtotime($dateTime) < time()) {
									                $status_class = 'in-use';
									            }
									    ?>
									    <div class="time-slot <?php echo $status_class; ?>">
									        <div class="fw-bold"><?php echo date('H:i', $time); ?></div>
									        <?php if($booking): ?>
									            <div>ลูกค้า: <?php echo $booking['cus_firstname'] . ' ' . $booking['cus_lastname']; ?></div>
									            <div>คอร์ส: <?php echo $booking['course_name']; ?></div>
									            <div class="text-muted">สถานะ: <?php echo $booking['order_payment']; ?></div>
									        <?php else: ?>
									            <div class="text-muted">ว่าง</div>
									        <?php endif; ?>
									    </div>
									    <?php 
									        endfor;
									    endwhile;
									    
									    // คำนวณเปอร์เซ็นต์การจอง
									    $usage_rate = $total_slots > 0 ? ($booked_slots / $total_slots) * 100 : 0;
									    ?>
									</div>
                               </div>
                           </div>
                       </div>
                       <?php endwhile; ?>
                   </div>
               </div>
               <!-- / Content -->

               <?php include 'footer.php'; ?>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
   // Initialize flatpickr date picker
   flatpickr("#date", {
       dateFormat: "Y-m-d",
       locale: "th",
       onChange: function(selectedDates, dateStr, instance) {
           document.getElementById('dateForm').submit();
       }
   });
});
</script>
</body>
</html>