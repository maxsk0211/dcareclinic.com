<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
include 'chk-session.php';
require '../dbcon.php';

// ตรวจสอบว่ามีการเลือกวันที่หรือไม่
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');



// ดึงข้อมูลห้องทั้งหมด
$branch_id = $_SESSION['branch_id'];
// ดึงข้อมูลห้องทั้งหมดพร้อมสถานะรายวัน
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

$rooms = [];
while ($room = $result_rooms->fetch_assoc()) {
    $rooms[] = $room;
}
// ดึงรายชื่อห้องที่มีอยู่
$sql_room_names = "SELECT DISTINCT room_name FROM rooms WHERE branch_id = ?";
$stmt_room_names = $conn->prepare($sql_room_names);
$stmt_room_names->bind_param("i", $branch_id);
$stmt_room_names->execute();
$result_room_names = $stmt_room_names->get_result();
$room_names = $result_room_names->fetch_all(MYSQLI_ASSOC);

function thaiDate($date) {
    $thai_month_arr = array(
        "01"=>"ม.ค.", "02"=>"ก.พ.", "03"=>"มี.ค.", "04"=>"เม.ย.", "05"=>"พ.ค.", "06"=>"มิ.ย.", 
        "07"=>"ก.ค.", "08"=>"ส.ค.", "09"=>"ก.ย.", "10"=>"ต.ค.", "11"=>"พ.ย.", "12"=>"ธ.ค."
    );
    
    $year = date("Y", strtotime($date)) + 543;
    $month = date("m", strtotime($date));
    $day = date("d", strtotime($date));
    
    $thai_date_return = $day . " " . $thai_month_arr[$month] . " " . $year;
    
    return $thai_date_return;
}

function formatTime($time) {
    return date("H:i", strtotime($time));
}


function getSchedulesForRoom($roomId, $date) {
    global $conn;
    $sql = "SELECT * FROM room_schedules WHERE room_id = ? AND date = ? ORDER BY start_time";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $roomId, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>


<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../../assets/"
  data-template="horizontal-menu-template-no-customizer-starter"
  data-style="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>จัดการห้อง - D Care Clinic</title>
    

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
        .room-card {
            margin-bottom: 20px;
        }
        .room-schedule {
            margin-top: 10px;
        }
        .swal2-container {
          z-index: 1100; /* หรือค่าอื่นๆ ที่มากกว่า z-index ของ element อื่นๆ บนหน้าเว็บ */
        }
        .select2-container {
          z-index: 1099; /* กำหนดค่าตามความเหมาะสม */
        }
        .select2-dropdown {
          z-index: 1100; /* ควรมีค่ามากกว่า .select2-container */
        }
   body {
        background-color: #f8f9fa;
    }
/*    .container-xxl {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }*/
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15);
    }
    .card-header {
        background-color: #4e73df;
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 1rem;
    }
    .card-body {
        padding: 1.5rem;
    }
    .room-card {
        margin-bottom: 2rem;
    }
    .room-card .card-title {
        font-size: 1.25rem;
        font-weight: bold;
        margin-bottom: 1rem;
    }
    .room-schedule {
        background-color: #f1f3f9;
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1rem;
    }
    .room-schedule h6 {
        color: #4e73df;
        margin-bottom: 0.5rem;
    }
    .list-group-item {
        border: none;
        background-color: transparent;
        padding: 0.5rem 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .btn {
        border-radius: 50px;
        padding: 0.5rem 1rem;
        font-weight: 600;
    }
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
    }
    .btn-info {
        background-color: #36b9cc;
        border-color: #36b9cc;
        color: white;
    }
    .btn-info:hover {
        background-color: #2c9faf;
        border-color: #2c9faf;
    }
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }
    .modal-content {
        border-radius: 15px;
    }
    .modal-header {
        background-color: #4e73df;
        color: white;
        border-radius: 15px 15px 0 0;
    }
    .form-control, .form-select {
        border-radius: 50px;
    }
    .select2-container--bootstrap-5 .select2-selection {
        border-radius: 50px;
    }
    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
        border-radius: 10px;
    }
    @media (max-width: 768px) {
        .container-xxl {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        .card {
            margin-bottom: 1rem;
        }
    }
    .card.room-closed {
        background-color: #fff5f5;
        border: 1px solid #dc3545;
    }
    .card.room-closed .card-header {
        background-color: #dc3545;
    }
    .card.room-closed .room-schedule {
        background-color: #ffe5e5;
    }
    .badge {
        padding: 0.5em 0.75em;
        border-radius: 50px;
        font-weight: 600;
    }
    .badge-success {
        background-color: #28a745;
        color: white;
    }
    .badge-danger {
        background-color: #dc3545;
        color: white;
    }
    .btn-toggle-open {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
    }
    .btn-toggle-open:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    .btn-toggle-closed {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    .btn-toggle-closed:hover {
        background-color: #c82333;
        border-color: #bd2130;
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
                    <?php include 'menu.php';  ?>
                    <!-- / Menu -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <br>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="py-3 mb-4">จัดการห้องประจำวัน    </h4>
                            <button class="btn btn-primary" onclick="showManageRoomsModal()">
                                <i class="ri-add-line align-middle me-1"></i> จัดการห้อง
                            </button>
                        </div>
                        <!-- เลือกวันที่ -->
                        <div class="card mb-4 ">
                            <div class="card-body ">
                                <form action="" method="GET" id="dateForm" class="row align-items-center">
                                    <div class="col-md-4">
                                        <label for="date" class="form-label">เลือกวันที่</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo $selected_date; ?>">
                                    </div>
                                    <div class="col-md-4 mt-3 mt-md-0">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="ri-search-line"></i> แสดงข้อมูล
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="alert alert-info text-center mb-4 h3 text-primary">
                            <i class="ri-calendar-check-line"></i> วันที่แสดงข้อมูล: <?php echo thaiDate($selected_date); ?>
                        </div>

                        <!-- ในส่วนของ HTML ที่แสดงรายละเอียดห้อง -->
                        <div class="row" id="roomsContainer">
                            <?php foreach ($rooms as $room): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card room-card <?php echo $room['daily_status'] == 'closed' ? 'room-closed' : ''; ?>">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0 text-white"><?php echo $room['room_name']; ?></h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text mt-4">
                                            สถานะ: 
                                            <span class="badge <?php echo $room['daily_status'] == 'open' ? 'badge-success' : 'badge-danger'; ?>">
                                                <?php echo $room['daily_status'] == 'open' ? 'เปิดใช้งาน' : 'ปิดใช้งาน'; ?>
                                            </span>
                                        </p>
                                        <div class="room-schedule">
                                            <?php $schedules = getSchedulesForRoom($room['room_id'], $selected_date); ?>
                                            <h6><i class="ri-calendar-line"></i> ตารางเวลา: <?php if (isset($schedule)): ?> <p class="text-danger">ไม่มี</p> <?php endif ?></h6>
                                            <ul class="list-group">
                                            <?php
                                            foreach ($schedules as $schedule):
                                            ?>
                                                <li class="list-group-item">
                                                    <span>
                                                        <i class="ri-time-line"></i> <?php echo $schedule['schedule_name']; ?>: 
                                                        <?php echo formatTime($schedule['start_time']); ?> - <?php echo formatTime($schedule['end_time']); ?>
                                                    </span>
                                                    <button class="btn btn-sm btn-primary" onclick="editSchedule(<?php echo $schedule['schedule_id']; ?>)">
                                                        <i class="ri-edit-line"></i> แก้ไข
                                                    </button>
                                                </li>
                                            <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <button class="btn btn-primary mt-2" onclick="showScheduleModal(<?php echo $room['room_id']; ?>)">
                                            <i class="ri-add-line"></i> เพิ่มช่วงเวลา
                                        </button>
                                        <button class="btn <?php echo $room['daily_status'] == 'open' ? 'btn-toggle-closed' : 'btn-toggle-open'; ?> mt-2" 
                                                onclick="toggleRoomStatus(<?php echo $room['room_id']; ?>, '<?php echo $room['daily_status'] == 'open' ? 'closed' : 'open'; ?>', '<?php echo addslashes($room['room_name']); ?>')">
                                            <i class="ri-toggle-line"></i> 
                                            <?php echo $room['daily_status'] == 'open' ? 'ปิดการใช้งาน' : 'เปิดการใช้งาน'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- ปุ่มเพิ่มห้องใหม่ -->
                        <div class="text-center mt-4">
                            <!-- <button class="btn btn-success" onclick="addRoom()">เพิ่มห้องใหม่</button> -->
                        </div>
                    </div>

                    <?php include 'footer.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal สำหรับจัดการห้อง -->
    <div class="modal fade" id="manageRoomsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white">จัดการห้อง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <button class="btn btn-primary" data-bs-dismiss="modal" onclick="showAddRoomForm()">เพิ่มห้องใหม่</button>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ชื่อห้อง</th>
                                <th>สถานะ</th>
                                <th>การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="roomsTableBody">
                            <!-- ข้อมูลห้องจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal สำหรับเพิ่ม/แก้ไขห้อง -->
    <div class="modal fade" id="roomFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="roomFormModalTitle">เพิ่มห้องใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="roomForm">
                        <input type="hidden" id="roomId" name="roomId">
                        <div class="mb-3">
                            <label for="roomName" class="form-label">ชื่อห้อง</label>
                            <input type="text" class="form-control" id="roomName" name="roomName" required>
                        </div>
                        <div class="mb-3">
                            <label for="roomStatus" class="form-label">สถานะ</label>
                            <select class="form-select" id="roomStatus" name="roomStatus" required>
                                <option value="active">เปิดใช้งาน</option>
                                <option value="inactive">ปิดใช้งาน</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="showManageRoomsModal()">ปิด</button>

                    </form>
                </div>
            </div>
        </div>
    </div>



<div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="scheduleModalTitle">จัดการตารางเวลาและคอร์ส</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="scheduleForm">
                    <input type="hidden" id="roomId" name="roomId">
                    <input type="hidden" id="scheduleId" name="scheduleId">
                    <input type="hidden" name="date" id="scheduleDate" value="<?php echo $selected_date; ?>">
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
    flatpickr("#date", {
        dateFormat: "Y-m-d",
        locale: "th",
        onChange: function(selectedDates, dateStr, instance) {
            // ส่งฟอร์มอัตโนมัติเมื่อมีการเลือกวันที่
            $('#dateForm').submit();
        }
    });

    flatpickr("#startTime", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });

    flatpickr("#endTime", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true
    });

    $('#courses').select2({
        theme: 'bootstrap-5',
        ajax: {
            url: 'sql/get-courses.php',
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });

    $('#courses').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'เลือกคอร์ส',
        allowClear: true
    });

    loadRoomNames();
    $('input[type="time"]').attr('step', '60');
    loadCourses();



});

function showManageRoomsModal() {
    loadRooms();
    loadRoomNames();
    $('#manageRoomsModal').modal('show');
}

function openScheduleModal(roomId) {
    $('#scheduleRoomId').val(roomId);
    $('#scheduleModal').modal('show');
    console.log("Opening modal for Room ID:", roomId); // เพิ่ม log นี้
}
function loadRoomNames() {
    $.ajax({
        url: 'sql/get-room-names.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // console.log("Room data received:", data);
            let options = '<option value="">เลือกห้อง</option>';
            data.forEach(function(room) {
                options += `<option value="${room.room_id}">${room.room_name}</option>`;
            });
            $('#roomSelect').html(options);
            // console.log("Options generated:", options);
        },
        error: function(xhr, status, error) {
            console.error('เกิดข้อผิดพลาดในการโหลดรายชื่อห้อง:', status, error);
            console.log(xhr.responseText);
            $('#roomSelect').html('<option value="">ไม่สามารถโหลดรายชื่อห้องได้</option>');
        }
    });
}

function loadRooms() {
    $.ajax({
        url: 'sql/get-rooms.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let tableBody = '';
            data.forEach(function(room) {
                tableBody += `
                    <tr>
                        <td>${room.room_name}</td>
                        <td>${room.status === 'active' ? 'เปิดใช้งาน' : 'ปิดใช้งาน'}</td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="editRoom(${room.room_id})" data-bs-dismiss="modal">แก้ไข</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteRoom(${room.room_id})">ลบ</button>
                        </td>
                    </tr>
                `;
            });
            $('#roomsTableBody').html(tableBody);
        },
        error: function() {
            Swal.fire('Error', 'เกิดข้อผิดพลาดในการโหลดข้อมูลห้อง', 'error');
        }
    });
}

function showAddRoomForm() {
    $('#roomFormModalTitle').text('เพิ่มห้องใหม่');
    $('#roomId').val('');
    $('#roomName').val('');
    $('#roomStatus').val('active');
    $('#roomFormModal').modal('show');
}

function editRoom(roomId) {
    $.ajax({
        url: 'sql/get-room.php',
        type: 'GET',
        data: { roomId: roomId },
        dataType: 'json',
        success: function(data) {
            $('#roomFormModalTitle').text('แก้ไขห้อง');
            $('#roomId').val(data.room_id);
            $('#roomName').val(data.room_name);
            $('#roomStatus').val(data.status);
            $('#roomFormModal').modal('show');
        },
        error: function() {
            Swal.fire('Error', 'เกิดข้อผิดพลาดในการโหลดข้อมูลห้อง', 'error');
        }
    });
}

$('#roomForm').submit(function(e) {
    e.preventDefault();
    $.ajax({
        url: 'sql/save-room.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('สำเร็จ', response.message, 'success').then(() => {
                    $('#roomFormModal').modal('hide');
                    loadRooms();
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
        }
    });
});

function deleteRoom(roomId) {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "คุณต้องการลบห้องนี้ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'sql/delete-room.php',
                type: 'POST',
                data: { roomId: roomId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('สำเร็จ', response.message, 'success').then(() => {
                            loadRooms();
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'เกิดข้อผิดพลาดในการลบห้อง', 'error');
                }
            });
        }
    });
}

function saveRoom() {
    let formData = new FormData($('#roomForm')[0]);
    
    $.ajax({
        url: 'sql/save-room.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('สำเร็จ', response.message, 'success').then(() => {
                    $('#roomModal').modal('hide');
                    loadRooms();
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
        }
    });
}

function loadCourses() {
    $.ajax({
        url: 'sql/get-courses.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#courses').empty();
            data.forEach(function(course) {
                $('#courses').append(new Option(course.name, course.id));
            });
            $('#courses').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'เลือกคอร์ส',
                allowClear: true
            });
        },
        error: function() {
            console.error('เกิดข้อผิดพลาดในการโหลดรายชื่อคอร์ส');
        }
    });
}

// function saveRoomDetail() {
//     let formData = new FormData($('#roomDetailForm')[0]);
    
//     $.ajax({
//         url: 'sql/save-room-detail.php',
//         type: 'POST',
//         data: formData,
//         processData: false,
//         contentType: false,
//         dataType: 'json',
//         success: function(response) {
//             if (response.success) {
//                 Swal.fire('สำเร็จ', response.message, 'success').then(() => {
//                     $('#roomModal').modal('hide');
//                 });
//             } else {
//                 Swal.fire('Error', response.message, 'error');
//             }
//         },
//         error: function() {
//             Swal.fire('Error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
//         }
//     });
// }

function showScheduleModal(roomId, scheduleId = null) {
    console.log("Setting Room ID to:", roomId);
    $('#scheduleModal #roomId').val(roomId);
    $('#scheduleModal #scheduleId').val(scheduleId);
    resetScheduleForm();
    
    if (scheduleId) {
        // กำลังแก้ไขตารางเวลาที่มีอยู่
        $('#deleteScheduleBtn').show();
        $('#scheduleModalTitle').text('แก้ไขตารางเวลาและคอร์ส');
        loadSchedule(scheduleId);
    } else {
        // กำลังเพิ่มตารางเวลาใหม่
        $('#deleteScheduleBtn').hide();
        $('#scheduleModalTitle').text('เพิ่มตารางเวลาและคอร์ส');
    }
    
    $('#scheduleModal').modal('show');
}

function loadSchedule(scheduleId) {
    $.ajax({
        url: 'sql/get-schedule.php',
        type: 'GET',
        data: { scheduleId: scheduleId },
        dataType: 'json',
        success: function(data) {
            if (data) {
                $('#scheduleName').val(data.schedule_name);
                $('#startTime').val(data.start_time);
                $('#endTime').val(data.end_time);
                $('#intervalMinutes').val(data.interval_minutes);
                // โหลดข้อมูลคอร์ส
                if (data.courses) {
                    $('#courses').val(data.courses.split(',')).trigger('change');
                }
            }
        },
        error: function() {
            console.error('เกิดข้อผิดพลาดในการโหลดข้อมูลตารางเวลา');
        }
    });
}

function resetScheduleForm() {
    // $('#roomId').val('');  // ไม่ต้องรีเซ็ต roomId
    $('#scheduleId').val('');
    $('#scheduleName').val('');
    $('#startTime').val('');
    $('#endTime').val('');
    $('#intervalMinutes').val('30');
    $('#courses').val(null).trigger('change');
}


$('#scheduleModal').on('shown.bs.modal', function () {
    console.log("Modal shown. Room ID in form:", $('#scheduleForm #roomId').val());
});

function loadCourses(selectedCourses = []) {
    $.ajax({
        url: 'sql/get-courses.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            $('#courses').empty();
            data.forEach(function(course) {
                var option = new Option(course.name, course.id, false, selectedCourses.includes(course.id.toString()));
                $('#courses').append(option);
            });
            $('#courses').trigger('change');
        },
        error: function() {
            console.error('เกิดข้อผิดพลาดในการโหลดรายชื่อคอร์ส');
        }
    });
}

$('#deleteScheduleBtn').click(function() {
    var scheduleId = $('#scheduleId').val();
    if (!scheduleId) {
        Swal.fire('Error', 'ไม่พบข้อมูลตารางเวลา', 'error');
        return;
    }

    // ตรวจสอบการจองก่อน
    $.ajax({
        url: 'sql/check-schedule-bookings.php',
        type: 'POST',
        data: { scheduleId: scheduleId },
        dataType: 'json',
        success: function(checkResponse) {
            if (checkResponse.hasBookings) {
                // ถ้ามีการจอง แสดงข้อความแจ้งเตือน
                Swal.fire({
                    title: 'ไม่สามารถลบได้',
                    text: "มีการจองคอร์สในช่วงเวลานี้แล้ว ไม่สามารถลบตารางเวลาได้",
                    icon: 'error',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'เข้าใจแล้ว'
                });
            } else {
                // ถ้าไม่มีการจอง แสดง confirm dialog
                Swal.fire({
                    title: 'ยืนยันการลบ?',
                    text: "คุณแน่ใจหรือไม่ที่จะลบตารางเวลานี้?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'ใช่, ลบเลย!',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ดำเนินการลบเมื่อยืนยัน
                        $.ajax({
                            url: 'sql/delete-schedule.php',
                            type: 'POST',
                            data: { scheduleId: scheduleId },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('สำเร็จ', 'ลบตารางเวลาเรียบร้อยแล้ว', 'success').then(() => {
                                        $('#scheduleModal').modal('hide');
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'เกิดข้อผิดพลาดในการลบข้อมูล', 'error');
                            }
                        });
                    }
                });
            }
        },
        error: function() {
            Swal.fire('Error', 'เกิดข้อผิดพลาดในการตรวจสอบข้อมูล', 'error');
        }
    });
});



// function deleteSchedule(scheduleId) {
//     Swal.fire({
//         title: 'ยืนยันการลบ?',
//         text: "คุณแน่ใจหรือไม่ที่จะลบตารางเวลานี้?",
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonColor: '#d33',
//         cancelButtonColor: '#3085d6',
//         confirmButtonText: 'ใช่, ลบเลย!',
//         cancelButtonText: 'ยกเลิก'
//     }).then((result) => {
//         if (result.isConfirmed) {
//             $.ajax({
//                 url: 'sql/delete-schedule.php',
//                 type: 'POST',
//                 data: { scheduleId: scheduleId },
//                 dataType: 'json',
//                 success: function(response) {
//                     if (response.success) {
//                         Swal.fire('สำเร็จ', 'ลบตารางเวลาเรียบร้อยแล้ว', 'success').then(() => {
//                             location.reload();
//                         });
//                     } else {
//                         Swal.fire('Error', response.message, 'error');
//                     }
//                 },
//                 error: function() {
//                     Swal.fire('Error', 'เกิดข้อผิดพลาดในการลบข้อมูล', 'error');
//                 }
//             });
//         }
//     });
// }

function editSchedule(scheduleId) {
    resetScheduleForm();
    
    $.ajax({
        url: 'sql/get-schedule.php',
        type: 'GET',
        data: { scheduleId: scheduleId },
        dataType: 'json',
        success: function(data) {
            if (data) {
                console.log("Received data:", data);  // เพิ่ม log นี้
                $('#scheduleId').val(data.schedule_id);
                $('#scheduleForm #roomId').val(data.room_id);  // เปลี่ยนเป็นแบบนี้
                $('#scheduleName').val(data.schedule_name);
                $('#startTime').val(data.start_time);
                $('#endTime').val(data.end_time);
                $('#intervalMinutes').val(data.interval_minutes);
                
                if (data.courses) {
                    if (Array.isArray(data.courses)) {
                        $('#courses').val(data.courses).trigger('change');
                    } else if (typeof data.courses === 'string') {
                        $('#courses').val(data.courses.split(',')).trigger('change');
                    }
                }
                
                console.log("Editing schedule. Room ID:", data.room_id);
                console.log("Room ID set in form:", $('#scheduleForm #roomId').val());  // เพิ่ม log นี้
                $('#deleteScheduleBtn').show();
                $('#scheduleModal').modal('show');
            } else {
                Swal.fire('Error', 'ไม่พบข้อมูลตารางเวลา', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error: ", status, error);
            console.log("Response Text: ", xhr.responseText);
            Swal.fire('Error', 'เกิดข้อผิดพลาดในการโหลดข้อมูล', 'error');
        }
    });
}

function showScheduleModal(roomId) {
    console.log("Setting Room ID to:", roomId);
    $('#scheduleModal #roomId').val(roomId);  // เพิ่ม #scheduleModal เพื่อให้แน่ใจว่าเราเลือก input ถูกต้อง
    $('#scheduleModal #scheduleId').val('');
    resetScheduleForm();
    $('#scheduleModal').modal('show');
}


$('#scheduleForm').submit(function(e) {
    e.preventDefault();
    var roomId = $('#scheduleForm #roomId').val();
    console.log("Submitting form. Room ID in form:", roomId);

    if (!roomId) {
        console.error("Room ID is missing!");
        Swal.fire('Error', 'ไม่พบข้อมูล Room ID', 'error');
        return;
    }

    var formData = $(this).serialize();
    console.log("Form data before sending:", formData);

    $.ajax({
        url: 'sql/save-schedule.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('สำเร็จ', 'บันทึกตารางเวลาเรียบร้อยแล้ว', 'success').then(() => {
                    $('#scheduleModal').modal('hide');
                    location.reload();
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            console.log("Response Text:", xhr.responseText);
            Swal.fire('Error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'error');
        }
    });
});
function toggleRoomStatus(roomId, newStatus, roomName) {
    let actionText = newStatus === 'open' ? 'เปิด' : 'ปิด';
    Swal.fire({
        title: `ยืนยันการ${actionText}ห้อง?`,
        text: `คุณต้องการ${actionText}ห้อง "${roomName}" สำหรับวันนี้ใช่หรือไม่?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: `ใช่, ${actionText}ห้อง!`,
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'sql/toggle-room-status.php',
                type: 'POST',
                data: {
                    roomId: roomId,
                    status: newStatus,
                    date: $('#date').val()
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'สำเร็จ!',
                            `${actionText}ห้อง "${roomName}" เรียบร้อยแล้ว`,
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'เกิดข้อผิดพลาด!',
                            response.message,
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'เกิดข้อผิดพลาด!',
                        'ไม่สามารถติดต่อกับเซิร์ฟเวอร์ได้',
                        'error'
                    );
                }
            });
        }
    });
}
</script>

</body>
</html>
