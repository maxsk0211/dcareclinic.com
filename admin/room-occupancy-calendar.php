<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
include 'chk-session.php';
require '../dbcon.php';

// ตรวจสอบวันที่ที่เลือก
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$branch_id = $_SESSION['branch_id'];

// ดึงข้อมูลห้องทั้งหมด
$sql_rooms = "SELECT * FROM rooms WHERE branch_id = ? AND status = 'active'";
$stmt_rooms = $conn->prepare($sql_rooms);
$stmt_rooms->bind_param("i", $branch_id);
$stmt_rooms->execute();
$result_rooms = $stmt_rooms->get_result();
$rooms = $result_rooms->fetch_all(MYSQLI_ASSOC);

// ดึงข้อมูลวันที่ปิดคลินิก
$sql_closures = "SELECT closure_date FROM clinic_closures WHERE branch_id = ? AND MONTH(closure_date) = ? AND YEAR(closure_date) = ?";
$stmt_closures = $conn->prepare($sql_closures);
$stmt_closures->bind_param("iii", $branch_id, $selected_month, $selected_year);
$stmt_closures->execute();
$result_closures = $stmt_closures->get_result();
$closures = array();
while ($row = $result_closures->fetch_assoc()) {
    $closures[] = $row['closure_date'];
}

// แปลงเดือนเป็นภาษาไทย
function thaiMonth($month) {
    $months = array(
        "01"=>"มกราคม", "02"=>"กุมภาพันธ์", "03"=>"มีนาคม", "04"=>"เมษายน",
        "05"=>"พฤษภาคม", "06"=>"มิถุนายน", "07"=>"กรกฎาคม", "08"=>"สิงหาคม",
        "09"=>"กันยายน", "10"=>"ตุลาคม", "11"=>"พฤศจิกายน", "12"=>"ธันวาคม"
    );
    return $months[$month];
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
    <title>ปฏิทินการใช้งานห้อง - D Care Clinic</title>
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
    <style>
        .swal2-container {
          z-index: 1100; /* หรือค่าอื่นๆ ที่มากกว่า z-index ของ element อื่นๆ บนหน้าเว็บ */
        }
        .select2-container {
          z-index: 1099; /* กำหนดค่าตามความเหมาะสม */
        }
        .select2-dropdown {
          z-index: 1100; /* ควรมีค่ามากกว่า .select2-container */
        }
/* Calendar Container */
.calendar {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    margin: 20px 0;
    overflow: hidden;
}

/* Calendar Header */
.calendar-header {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    padding: 25px;
    text-align: center;
    font-size: 1.8rem;
    font-weight: 600;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    letter-spacing: 1px;
}

/* Calendar Grid */
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    border: 1px solid #e3e6f0;
    background: #f8f9fc;
}

/* Day Headers */
.calendar-day-header {
    background: #4e73df;
    color: white;
    padding: 15px 10px;
    text-align: center;
    font-weight: 600;
    font-size: 0.9rem;
    border-right: 1px solid rgba(255,255,255,0.2);
}

/* Calendar Days */
.calendar-day {
    min-height: 160px;
    padding: 10px;
    background: white;
    border: 1px solid #e3e6f0;
    transition: all 0.3s ease;
}

.calendar-day:hover {
    background: #f8f9fc;
    transform: scale(1.02);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    z-index: 2;
    border-color: #4e73df;
}

.calendar-day.inactive {
    background: #f8f9fc;
    color: #b7b9cc;
}

/* Day Content */
.calendar-day-content {
    height: 100%;
    display: flex;
    flex-direction: column;
}

/* Day Number */
.day-number {
    font-size: 1.2rem;
    font-weight: 600;
    color: #4e73df;
    padding: 5px 10px;
    background: #f8f9fc;
    border-radius: 8px;
    margin-bottom: 10px;
    text-align: center;
}

/* Room Summary */
.room-summary {
    padding: 10px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    border: 1px solid #e3e6f0;
    margin-top: auto;
}

.room-summary-stat {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
    padding: 5px 0;
    border-bottom: 1px dashed #e3e6f0;
}

.room-summary-stat:last-child {
    border-bottom: none;
}

.stat-label {
    color: #5a5c69;
    font-weight: 500;
    font-size: 0.85rem;
}

.stat-value {
    font-weight: 600;
    color: #4e73df;
    font-size: 0.85rem;
}

/* Occupancy Colors */
.occupancy-rate {
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 0.85rem;
}

.occupancy-low {
    background-color: #e3fcef;
    color: #1cc88a;
}

.occupancy-medium {
    background-color: #fff3cd;
    color: #f6c23e;
}

.occupancy-high {
    background-color: #f8d7da;
    color: #e74a3b;
}

/* No Schedule Indicator */
.no-schedule {
    text-align: center;
    color: #858796;
    padding: 20px 10px;
    font-style: italic;
    background: #f8f9fc;
    border-radius: 8px;
    margin-top: 10px;
}

/* Current Day Highlight */
.current-day {
    background: #fff8eb;
}

.current-day .day-number {
    background: #f6c23e;
    color: white;
}

/* Modal Styles */
.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.modal-header {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    border: none;
    padding: 20px 25px;
    border-radius: 15px 15px 0 0;
}

.modal-body {
    padding: 25px;
}

/* Room List in Modal */
.room-list {
    margin-top: 20px;
}

.room-item {
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #e3e6f0;
    transition: all 0.3s ease;
}

.room-item:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.room-status {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-open {
    background: #e3fcef;
    color: #1cc88a;
}

.status-closed {
    background: #f8d7da;
    color: #e74a3b;
}

/* Progress Bars */
.progress {
    height: 8px;
    border-radius: 4px;
    background: #eaecf4;
    margin: 10px 0;
}

.progress-bar {
    border-radius: 4px;
    transition: width 0.6s ease;
}

/* Summary Box in Modal */
.summary-box {
    background: #f8f9fc;
    border-radius: 10px;
    padding: 20px;
    margin-top: 20px;
}

.summary-title {
    color: #4e73df;
    font-weight: 600;
    margin-bottom: 15px;
    font-size: 1.1rem;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.summary-item {
    background: white;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    border: 1px solid #e3e6f0;
}

.summary-value {
    font-size: 1.5rem;
    font-weight: 600;
    color: #4e73df;
    margin: 5px 0;
}

.summary-label {
    color: #858796;
    font-size: 0.9rem;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .calendar-day {
        min-height: 120px;
        padding: 8px;
    }

    .day-number {
        font-size: 1rem;
    }

    .room-summary {
        font-size: 0.75rem;
    }

    .summary-grid {
        grid-template-columns: 1fr;
    }

    .modal-dialog {
        margin: 10px;
    }
}
    </style>

    <!-- Page JS -->
    <script src="../assets/vendor/js/helpers.js"></script>
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
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="py-3 mb-4">ปฏิทินการใช้งานห้อง</h4>
                            <div>
                                <form action="" method="GET" class="d-flex gap-3">
                                    <select name="month" class="form-select" onchange="this.form.submit()">
                                        <?php for($m = 1; $m <= 12; $m++): ?>
                                            <option value="<?= sprintf("%02d", $m) ?>" <?= $selected_month == sprintf("%02d", $m) ? 'selected' : '' ?>>
                                                <?= thaiMonth(sprintf("%02d", $m)) ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    <select name="year" class="form-select" onchange="this.form.submit()">
                                        <?php 
                                        $current_year = date('Y');
                                        for($y = $current_year - 1; $y <= $current_year + 1; $y++): 
                                        ?>
                                            <option value="<?= $y ?>" <?= $selected_year == $y ? 'selected' : '' ?>>
                                                <?= $y + 543 ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="calendar">
                            <div class="calendar-header">
                                <?= thaiMonth($selected_month) ?> <?= $selected_year + 543 ?>
                            </div>
                            <div class="calendar-grid">
                                <div class="calendar-day-header">อาทิตย์</div>
                                <div class="calendar-day-header">จันทร์</div>
                                <div class="calendar-day-header">อังคาร</div>
                                <div class="calendar-day-header">พุธ</div>
                                <div class="calendar-day-header">พฤหัสบดี</div>
                                <div class="calendar-day-header">ศุกร์</div>
                                <div class="calendar-day-header">เสาร์</div>
                                
                                <?php
                                $firstDay = mktime(0,0,0, $selected_month, 1, $selected_year);
                                $daysInMonth = date('t', $firstDay);
                                $startDay = date('w', $firstDay);
                                $prevMonthDays = date('t', mktime(0,0,0, $selected_month-1, 1, $selected_year));
                                
                                // วันที่ของเดือนก่อนหน้า
                                for($i = 0; $i < $startDay; $i++) {
                                    $day = $prevMonthDays - ($startDay - $i - 1);
                                    echo "<div class='calendar-day inactive'><div class='day-number'>$day</div></div>";
                                }
                                
                                // วันที่ของเดือนปัจจุบัน
                                for($day = 1; $day <= $daysInMonth; $day++) {
                                    $date = sprintf("%04d-%02d-%02d", $selected_year, $selected_month, $day);
                                    $dayClass = 'calendar-day';
                                    
                                    // ตรวจสอบว่าเป็นวันที่ปิดหรือไม่
                                    if(in_array($date, $closures)) {
                                        $dayClass .= ' closed-day';
                                    }
                                    
                                    echo "<div class='$dayClass' onclick='showDayDetails(\"$date\")'>";
                                    echo "<div class='calendar-day-content'>";
                                    echo "<div class='day-number'>$day</div>";
                                    
									// ในส่วนแสดงผลปฏิทิน
									if(!in_array($date, $closures)) {
									    // ดึงข้อมูลช่วงเวลาที่เปิดให้จอง
									    $sql_schedules = "SELECT 
									        r.room_id,
									        r.room_name,
									        rs.start_time,
									        rs.end_time,
									        rs.interval_minutes,
									        (
									            SELECT COUNT(*)
									            FROM course_bookings cb
									            WHERE cb.room_id = r.room_id 
									            AND DATE(cb.booking_datetime) = ?
									            AND TIME(cb.booking_datetime) BETWEEN rs.start_time AND rs.end_time
									        ) as booked_slots
									    FROM rooms r
									    LEFT JOIN room_schedules rs ON r.room_id = rs.room_id AND rs.date = ?
									    WHERE r.branch_id = ? AND r.status = 'active'";
									    
									    $stmt_schedules = $conn->prepare($sql_schedules);
									    $stmt_schedules->bind_param("ssi", $date, $date, $branch_id);
									    $stmt_schedules->execute();
									    $result_schedules = $stmt_schedules->get_result();
									    
									    $total_slots = 0;
									    $total_booked = 0;
									    
									    while($row = $result_schedules->fetch_assoc()) {
									        if ($row['start_time'] && $row['end_time']) {
									            // คำนวณจำนวนช่วงเวลาที่จองได้
									            $start = strtotime($row['start_time']);
									            $end = strtotime($row['end_time']);
									            $interval = $row['interval_minutes'] * 60; // แปลงเป็นวินาที
									            $slots = floor(($end - $start) / $interval);
									            
									            $total_slots += $slots;
									            $total_booked += $row['booked_slots'];
									        }
									    }
									    
                                            if($total_slots > 0) {
                                                    $occupancy = ($total_booked / $total_slots) * 100;
                                                    echo "<div class='room-summary'>";
                                                    echo "<div class='room-summary-stat'>";
                                                    echo "<span class='stat-label'>ช่วงเวลาทั้งหมด:</span>";
                                                    echo "<span class='stat-value'>$total_slots</span>";
                                                    echo "</div>";
                                                    
                                                    echo "<div class='room-summary-stat'>";
                                                    echo "<span class='stat-label'>จองแล้ว:</span>";
                                                    echo "<span class='stat-value'>$total_booked</span>";
                                                    echo "</div>";
                                                    
                                                    $occupancy_class = '';
                                                    if ($occupancy >= 80) {
                                                        $occupancy_class = 'occupancy-high';
                                                    } elseif ($occupancy >= 50) {
                                                        $occupancy_class = 'occupancy-medium';
                                                    } else {
                                                        $occupancy_class = 'occupancy-low';
                                                    }
                                                    
                                                    echo "<div class='room-summary-stat'>";
                                                    echo "<span class='stat-label'>อัตราการใช้:</span>";
                                                    echo "<span class='occupancy-rate $occupancy_class'>" . number_format($occupancy, 0) . "%</span>";
                                                    echo "</div>";
                                                    echo "</div>";
                                                } else {
                                                    echo "<div class='no-schedule'>ไม่มีตารางเวลา</div>";
                                                }
									}
                                    
                                    echo "</div></div>";
                                }
                                
                                // วันที่ของเดือนถัดไป
                                $totalCells = 42; // 6 weeks * 7 days
                                $remainingCells = $totalCells - ($daysInMonth + $startDay);
                                for($i = 1; $i <= $remainingCells; $i++) {
                                    echo "<div class='calendar-day inactive'><div class='day-number'>$i</div></div>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                    <?php include 'footer.php'; ?>
                </div>
            </div>
        </div>
    </div>

<!-- Modal แสดงรายละเอียดรายวัน -->
<div class="modal fade" id="dayDetailsModal" tabindex="-1" role="dialog" aria-labelledby="dayDetailsTitle">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dayDetailsTitle">รายละเอียดการจองห้อง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="dayDetailsContent">
                <!-- เนื้อหาจะถูกเพิ่มด้วย JavaScript -->
            </div>
        </div>
    </div>
</div>


<!-- Modal สำหรับจัดการตารางเวลา -->
<div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="scheduleModalTitle">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="scheduleModalTitle">จัดการตารางเวลาและคอร์ส</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="scheduleForm">
                    <input type="hidden" id="roomId" name="roomId">
                    <input type="hidden" id="scheduleId" name="scheduleId">
                    <input type="hidden" name="date" id="scheduleDate">
                    <div class="mb-3">
                        <label for="scheduleName" class="form-label">ชื่อช่วงเวลา</label>
                        <input type="text" class="form-control" id="scheduleName" name="scheduleName" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="startTime" class="form-label">เวลาเริ่ม</label>
                                <input type="time" class="form-control" id="startTime" name="startTime" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="endTime" class="form-label">เวลาสิ้นสุด</label>
                                <input type="time" class="form-control" id="endTime" name="endTime" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="intervalMinutes" class="form-label">ช่วงเวลา (นาที)</label>
                        <input type="number" class="form-control" id="intervalMinutes" name="intervalMinutes" value="30" required>
                    </div>
                    <div class="mb-3">
                        <label for="courses" class="form-label">คอร์สที่สามารถจองได้</label>
                        <select class="form-control" id="courses" name="courses[]" multiple>
                            <!-- ตัวเลือกคอร์สจะถูกเพิ่มด้วย JavaScript -->
                        </select>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary" id="saveScheduleBtn">บันทึกตารางเวลา</button>
                        <button type="button" class="btn btn-danger" id="deleteScheduleBtn" style="display:none;">ลบตารางเวลา</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Core JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="../assets/vendor/libs/jquery/jquery.js"></script>
<script src="../assets/vendor/libs/popper/popper.js"></script>
<script src="../assets/vendor/js/bootstrap.js"></script>
<script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
<script src="../assets/vendor/libs/hammer/hammer.js"></script>
<script src="../assets/vendor/js/menu.js"></script>

<!-- Vendors JS -->

<!-- Main JS -->
<script src="../assets/js/main.js"></script>

<!-- Page JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>

<script>
$(document).ready(function() {

    const timeConfig = {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        defaultDate: undefined,  // ไม่กำหนดค่าเริ่มต้น
        onOpen: function(selectedDates, dateStr, instance) {
            // ถ้ามีค่าอยู่แล้ว ให้ใช้ค่านั้น
            if (instance.input.value) {
                instance.setDate(instance.input.value, false);
            }
        }
    };

    flatpickr("#startTime", timeConfig);
    flatpickr("#endTime", timeConfig);

	// Initialize select2
    console.log('Initializing select2');
    $('#courses').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'เลือกคอร์ส',
        allowClear: true,
        ajax: {
            url: 'sql/get-courses.php',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                console.log('Select2 ajax params:', params);
                return {
                    search: params.term,
                    selected: $('#courses').val()
                };
            },
            processResults: function(data, params) {
                console.log('Processing select2 results:', data);
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };
            },
            cache: true
        }
    });

    // เพิ่ม event listeners เพื่อ debug
    $('#courses').on('select2:select', function(e) {
        console.log('Course selected:', e.params.data);
    });

    $('#courses').on('select2:unselect', function(e) {
        console.log('Course unselected:', e.params.data);
    });

    $('#courses').on('change', function() {
        console.log('Courses changed, new value:', $(this).val());
    });

    function formatCourse(course) {
        if (!course.id) return course.text;
        return $(`
            <div>
                <strong>${course.text}</strong>
                ${course.price ? `<br><small>ราคา: ${course.price} บาท</small>` : ''}
                ${course.amount ? `<small> (${course.amount} ครั้ง)</small>` : ''}
            </div>
        `);
    }

    function formatCourseSelection(course) {
        return course.text || course.id;
    }
     // จัดการการส่งฟอร์ม
    $('#scheduleForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'sql/save-schedule.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                let result = JSON.parse(response);
                if (result.success) {
                    Swal.fire('สำเร็จ', result.message, 'success').then(() => {
                        $('#scheduleModal').modal('hide');
                        location.reload();
                    });
                } else {
                    Swal.fire('ผิดพลาด', result.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
                }
            },
            error: function() {
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
            }
        });
    });
        // เพิ่ม Event Handler สำหรับ Modal
    $('#scheduleModal').on('hidden.bs.modal', function () {
        resetScheduleForm();
        // ล้าง content ที่เพิ่มเข้าไปก่อนฟอร์ม
        $('#scheduleForm').prevAll().remove();
    });

    // Handle delete button
    $('#deleteScheduleBtn').on('click', function() {
        let scheduleId = $('#scheduleId').val();
        console.log('Checking schedule ID:', scheduleId);
        
        if (scheduleId) {
            // ตรวจสอบการจองก่อน
            $.ajax({
                url: 'sql/check-schedule-bookings.php',
                type: 'POST',
                data: { scheduleId: scheduleId },
                dataType: 'json', // เพิ่มบรรทัดนี้
                success: function(response) {
                    console.log('Check bookings response:', response);
                    // ไม่ต้อง parse JSON อีก เพราะ jQuery จะทำให้อัตโนมัติ
                    if (response.hasBookings) {
                        console.log('Found bookings, showing error');
                        Swal.fire({
                            icon: 'error',
                            title: 'ไม่สามารถลบได้',
                            text: response.message || 'มีการจองในช่วงเวลานี้แล้ว ไม่สามารถลบได้'
                        });
                    } else {
                        console.log('No bookings found, showing confirm dialog');
                        // ถ้าไม่มีการจอง แสดง confirm dialog
                        Swal.fire({
                            title: 'ยืนยันการลบ?',
                            text: "คุณต้องการลบตารางเวลานี้ใช่หรือไม่?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'ใช่, ลบเลย',
                            cancelButtonText: 'ยกเลิก'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: 'sql/delete-schedule.php',
                                    type: 'POST',
                                    data: { scheduleId: scheduleId },
                                    dataType: 'json', // เพิ่มบรรทัดนี้ด้วย
                                    success: function(deleteResponse) {
                                        if (deleteResponse.success) {
                                            Swal.fire('สำเร็จ', 'ลบข้อมูลเรียบร้อย', 'success').then(() => {
                                                $('#scheduleModal').modal('hide');
                                                location.reload();
                                            });
                                        } else {
                                            Swal.fire('ผิดพลาด', deleteResponse.message || 'เกิดข้อผิดพลาดในการลบข้อมูล', 'error');
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Delete error:', error);
                                        console.error('Response:', xhr.responseText);
                                        Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการลบข้อมูล', 'error');
                                    }
                                });
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Check bookings error:', error);
                    console.error('Response:', xhr.responseText);
                    Swal.fire('Error', 'เกิดข้อผิดพลาดในการตรวจสอบข้อมูล', 'error');
                }
            });
        }
    });
});	

// ในส่วนของ JavaScript ที่แสดง Modal
function showDayDetails(date) {
    $.ajax({
        url: 'sql/get-day-room-details.php',
        type: 'GET',
        data: { date: date },
        success: function(response) {
            let data = JSON.parse(response);
            let content = `<h6 class="text-center">วันที่ ${formatThaiDate(date)}</h6>`;
            
            content += `<div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ห้อง</th>
                            <th>สถานะ</th>
                            <th>ช่วงเวลาที่เปิด</th>
                            <th>คอร์สที่จองได้</th>
                            <th>จำนวนที่จองได้</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>`;
            
            data.rooms.forEach(room => {
                let scheduleRows = room.schedules.map(schedule => `
                    <div class="mb-2">
                        <div>${schedule.start_time} - ${schedule.end_time}</div>
                        <div class="small text-muted">
                            ทุก ${schedule.interval_minutes} นาที<br>
                            คอร์ส: ${schedule.available_courses.join(', ') || '-'}
                        </div>
                    </div>
                `).join('');

                content += `<tr>
                    <td>${room.room_name}</td>
                    <td>
                        <span class="badge bg-${room.status === 'open' ? 'success' : 'danger'}">
                            ${room.status === 'open' ? 'เปิด' : 'ปิด'}
                        </span>
                    </td>
                    <td>${scheduleRows || '-'}</td>
                    <td>${room.schedules.map(s => s.available_courses.length).reduce((a,b) => a + b, 0) || 0} คอร์ส</td>
                    <td>${room.total_slots}</td>

                    <td>
                        <button class="btn btn-sm btn-primary" onclick="manageRoomSchedule(${room.room_id}, '${date}')">
                            <i class="ri-time-line"></i> จัดการตารางเวลา
                        </button>
                    </td>
                </tr> 
                <tr>
                    <td colspan="6">
                        <div class="progress">
                            <div class="progress-bar ${getOccupancyClass(room.occupancy_rate)}" 
                                 role="progressbar" 
                                 style="width: ${room.occupancy_rate}%">
                                ${room.booked_slots}/${room.total_slots}
                            </div>
                        </div>
                    </td>
                </tr>
                `;
            });
            
            content += `</tbody></table></div>`;

            // แสดงสรุป
            if (data.summary) {
                content += `<div class="alert alert-info mt-3">
                    <h6>สรุปภาพรวม</h6>
                    <p>
                        จำนวนห้องที่เปิด: ${data.summary.open_rooms}<br>
                        ช่วงเวลาทั้งหมดที่จองได้: ${data.summary.total_slots_available}<br>
                        จองแล้ว: ${data.summary.total_slots_booked} ช่วงเวลา<br>
                        อัตราการจองเฉลี่ย: ${data.summary.average_occupancy}%
                    </p>
                </div>`;
            }

            $('#dayDetailsContent').html(content);
            $('#dayDetailsModal').modal('show');
        },
        error: function() {
            Swal.fire('Error', 'ไม่สามารถโหลดข้อมูลได้', 'error');
        }
    });
}

function getOccupancyClass(rate) {
    if (rate >= 80) return 'bg-danger';
    if (rate >= 50) return 'bg-warning';
    return 'bg-success';
}

function formatThaiDate(dateStr) {
    let date = new Date(dateStr);
    let thaiMonths = [
        "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน",
        "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม",
        "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
    ];
    return `${date.getDate()} ${thaiMonths[date.getMonth()]} ${date.getFullYear() + 543}`;
}

// อัพเดทฟังก์ชัน manageRoomSchedule ให้มีการจัดการ error
function manageRoomSchedule(roomId, date) {
    // ปิด modal รายละเอียดวัน
    $('#dayDetailsModal').modal('hide');
    
    // กำหนดค่าเริ่มต้นสำหรับการเพิ่มตารางเวลาใหม่
    $('#scheduleModal #roomId').val(roomId);
    $('#scheduleModal #scheduleId').val('');
    $('#scheduleModal #scheduleDate').val(date);
    
    // รีเซ็ตฟอร์ม
    resetScheduleForm();
    
    // เรียกดูตารางเวลาที่มีอยู่ของห้องในวันนั้น
    $.ajax({
        url: 'sql/get-room-schedules.php',
        type: 'GET',
        data: { 
            roomId: roomId,
            date: date
        },
        success: function(response) {
            let schedules = JSON.parse(response);
            let content = '<div class="mb-4">';
            content += '<h6>ตารางเวลาที่มีอยู่:</h6>';
            
            if (schedules.length > 0) {
                content += '<ul class="list-group">';
                schedules.forEach(schedule => {
                    content += `
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${schedule.schedule_name}</strong><br>
                                ${schedule.start_time} - ${schedule.end_time} (ทุก ${schedule.interval_minutes} นาที)<br>
                                <small class="text-muted">คอร์ส: ${schedule.courses.join(', ') || 'ไม่มี'}</small>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-primary me-2" onclick="editSchedule(${schedule.schedule_id})">
                                    <i class="ri-edit-line"></i>
                                </button>
                            </div>
                        </li>
                    `;
                });
                content += '</ul>';
            } else {
                content += '<p class="text-muted">ยังไม่มีตารางเวลา</p>';
            }
            content += '</div>';
            
            
            $('#scheduleModal .modal-body').prepend(content);
            $('#scheduleModal').modal('show');
        }
    });
}

// ปรับฟังก์ชันเดิมที่มีอยู่
function showNewScheduleForm() {
    $('#scheduleForm').show();
    $('#deleteScheduleBtn').hide();
    resetScheduleForm();
}


// ฟังก์ชันสำหรับแสดงฟอร์มเพิ่มตารางเวลาใหม่
function showNewScheduleForm(roomId, date) {
    resetScheduleForm();
    $('#scheduleForm').show();
    $('#scheduleModal #roomId').val(roomId);
    $('#scheduleModal #scheduleDate').val(date);
    $('#scheduleModalTitle').text('เพิ่มตารางเวลาใหม่');
    $('#deleteScheduleBtn').hide();
}

// อัพเดทฟังก์ชัน editSchedule เพื่อรองรับการโหลดคอร์สที่เลือกไว้
function editSchedule(scheduleId) {
    console.log('Starting editSchedule with scheduleId:', scheduleId);
    
    $.ajax({
        url: 'sql/get-schedule.php',
        type: 'GET',
        data: { scheduleId: scheduleId },
        success: function(data) {
            console.log('Raw response from get-schedule.php:', data);
            let schedule = JSON.parse(data);
            console.log('Selected course IDs:', schedule.courses);
            
            // กำหนดค่าฟอร์ม
            $('#scheduleId').val(schedule.schedule_id);
            $('#scheduleName').val(schedule.schedule_name);
            $('#startTime').val(schedule.start_time);
            $('#endTime').val(schedule.end_time);
            $('#intervalMinutes').val(schedule.interval_minutes);
            
            // แสดงปุ่มลบ
            $('#deleteScheduleBtn').show();
            
            // รีเซ็ต select2
            $('#courses').empty();
            
            if (schedule.courses && schedule.courses.length > 0) {
                // ดึงเฉพาะข้อมูลคอร์สที่เลือกไว้
                $.ajax({
                    url: 'sql/get-selected-courses.php', // เปลี่ยนเป็นไฟล์ใหม่
                    type: 'GET',
                    data: { courseIds: schedule.courses },
                    success: function(response) {
                        console.log('Selected courses response:', response);
                        let courses = JSON.parse(response);
                        courses.forEach(function(course) {
                            console.log('Adding selected course:', course);
                            let newOption = new Option(course.name, course.id, true, true);
                            // กำหนดค่าและ trigger change เพื่อให้ flatpickr อัพเดท
                            $('#startTime').val(schedule.start_time).trigger('change');
                            $('#endTime').val(schedule.end_time).trigger('change');
                            $('#courses').append(newOption);
                        });
                        $('#courses').trigger('change');
                    }
                });
            }
        }
    });
}

// ปรับฟังก์ชัน resetScheduleForm
function resetScheduleForm() {
    $('#scheduleId').val('');
    $('#scheduleName').val('');
    
    // รีเซ็ตค่าเวลาโดยไม่กำหนดค่าเริ่มต้น
    $('#startTime').val('').trigger('change');
    $('#endTime').val('').trigger('change');
    
    $('#intervalMinutes').val('30');
    $('#courses').empty().trigger('change');
    $('#deleteScheduleBtn').hide();
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
}

// เพิ่ม Event Listener สำหรับ Modal
$('#scheduleModal').on('hidden.bs.modal', function () {
    resetScheduleForm();
    // ล้าง content ที่เพิ่มเข้าไปก่อนฟอร์ม
    $('#scheduleForm').prevAll().remove();
});
</script>
</body>
</html>