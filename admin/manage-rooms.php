<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
include 'chk-session.php';
require '../dbcon.php';

// ตรวจสอบว่ามีการเลือกวันที่หรือไม่
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');



// ดึงข้อมูลห้องทั้งหมด
$branch_id = $_SESSION['branch_id'];
$sql_rooms = "SELECT * FROM rooms WHERE branch_id = ? AND status = 'active'";
$stmt_rooms = $conn->prepare($sql_rooms);
$stmt_rooms->bind_param("i", $branch_id);
$stmt_rooms->execute();
$result_rooms = $stmt_rooms->get_result();

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
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="py-3 mb-4"><span class="text-muted fw-light">จัดการ /</span> ห้อง</h4>
                            <button class="btn btn-primary" onclick="showManageRoomsModal()">
                                <i class="ri-add-line align-middle me-1"></i> จัดการห้อง
                            </button>
                        </div>
                        <!-- เลือกวันที่ -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <form action="" method="GET" id="dateForm">
                                    <div class="mb-3">
                                        <label for="date" class="form-label">เลือกวันที่</label>
                                        <input type="text" class="form-control" id="date" name="date" value="<?php echo $selected_date; ?>">
                                    </div>
                                </form>
                            </div>
                        </div>

                         <!-- แสดงรายการห้อง -->
                        <div class="row" id="roomsContainer">
                            <?php while($room = $result_rooms->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card room-card">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $room['room_name']; ?></h5>
                                        <p class="card-text">สถานะ: <?php echo $room['status'] == 'active' ? 'เปิดใช้งาน' : 'ปิดใช้งาน'; ?></p>
                                        <div class="room-schedule">
                                            <!-- แสดงตารางเวลาของห้อง (ถ้ามี) -->
                                        </div>
                                        <button class="btn btn-primary mt-2" onclick="editRoom(<?php echo $room['room_id']; ?>)">แก้ไขห้อง</button>
                                        <button class="btn btn-info mt-2" onclick="showScheduleModal(<?php echo $room['room_id']; ?>)">จัดการตารางเวลา</button>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
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
                    <h5 class="modal-title">จัดการห้อง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <h5 class="modal-title" id="roomFormModalTitle">เพิ่มห้องใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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



<!-- Modal สำหรับจัดการตารางเวลาและคอร์ส -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalTitle">จัดการตารางเวลาและคอร์ส</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="scheduleForm">
                    <input type="hidden" id="scheduleId" name="scheduleId">
                    <input type="hidden" id="scheduleRoomId" name="roomId">
                    <input type="hidden" id="scheduleDate" name="date" value="<?php echo $selected_date; ?>">
                    <div class="mb-3">
                        <label class="form-label">วันที่: <?php echo thaiDate($selected_date); ?></label>
                    </div>
                    <div class="row">
                        <div class="col-auto">
                            <div class="mb-3">
                                <label for="startTime" class="form-label">เวลาเริ่ม</label>
                                <input type="time" class="form-control" id="startTime" name="startTime" required>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="mb-3">
                                <label for="endTime" class="form-label">เวลาสิ้นสุด</label>
                                <input type="time" class="form-control" id="endTime" name="endTime" required>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="mb-3">
                                <label for="intervalMinutes" class="form-label">ช่วงเวลา (นาที)</label>
                                <input type="number" class="form-control" id="intervalMinutes" name="intervalMinutes" value="30" required>
                            </div>
                        </div>
                    </div>


                    <div class="mb-3">
                        <label for="courses" class="form-label">คอร์สที่สามารถจองได้</label>
                        <select class="form-control" id="courses" name="courses[]" multiple>
                            <!-- ตัวเลือกคอร์สจะถูกเพิ่มด้วย JavaScript -->
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" id="saveScheduleBtn">บันทึกตารางเวลา</button>
                    <button type="button" class="btn btn-danger" id="deleteScheduleBtn" style="display:none;">ลบตารางเวลา</button>
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
        // flatpickr("#scheduleDate", {
        //     dateFormat: "Y-m-d",
        //     locale: "th"
        // });

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
        // เรียกใช้ฟังก์ชันเมื่อหน้าเว็บโหลดเสร็จ
        loadRoomNames();
        // กำหนดให้ input type="time" แสดงผลเป็น 24 ชั่วโมง
        $('input[type="time"]').attr('step', '60');
        
        loadCourses(); // โหลดคอร์สเมื่อหน้าโหลดเสร็จ
    });

function showManageRoomsModal() {
    loadRooms();
    loadRoomNames();  // เพิ่มบรรทัดนี้
    $('#manageRoomsModal').modal('show');
}

function loadRoomNames() {
    $.ajax({
        url: 'sql/get-room-names.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log("Room data received:", data);
            let options = '<option value="">เลือกห้อง</option>';
            data.forEach(function(room) {
                options += `<option value="${room.room_id}">${room.room_name}</option>`;
            });
            $('#roomSelect').html(options);
            console.log("Options generated:", options);
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
    // สำหรับการจัดการห้อง
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
                                location.reload(); // รีโหลดหน้าเพื่ออัปเดตรายการห้อง
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

// function addRoom() {
//     $('#roomForm')[0].reset();
//     $('#roomId').val('');
//     $('#roomModalTitle').text('เพิ่มห้องใหม่');
//     $('#courses').val(null).trigger('change');
//     loadRoomNames();
//     loadCourses();
//     $('#roomModal').modal('show');
// }

function loadCourses() {
    $.ajax({
        url: 'sql/get-courses.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log("Courses data received:", data);
            $('#courses').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'เลือกคอร์ส',
                allowClear: true,
                data: data.map(function(course) {
                    return {
                        id: course.id,
                        text: course.name + ' - ราคา: ' + course.price + ' บาท, จำนวน: ' + course.amount + ' ครั้ง'
                    };
                })
            });
        },
        error: function(xhr, status, error) {
            console.error('เกิดข้อผิดพลาดในการโหลดรายชื่อคอร์ส:', status, error);
            console.log(xhr.responseText);
        }
    });
}

function saveRoomDetail() {
    let formData = new FormData($('#roomDetailForm')[0]);
    
    $.ajax({
        url: 'sql/save-room-detail.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('สำเร็จ', response.message, 'success').then(() => {
                    $('#roomModal').modal('hide');
                    // อัปเดตข้อมูลห้องหรือรีโหลดหน้าตามต้องการ
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

// สำหรับการจัดการตารางเวลาและคอร์ส
function showScheduleModal(roomId) {
    $('#scheduleRoomId').val(roomId);
    $('#scheduleId').val('');
    resetScheduleForm(); // รีเซ็ตฟอร์มก่อนโหลดข้อมูลใหม่
    loadSchedule(roomId);
    $('#scheduleModal').modal('show');
}

function loadSchedule(roomId) {
    $.ajax({
        url: 'sql/get-schedule.php',
        type: 'GET',
        data: { 
            roomId: roomId,
            date: $('#scheduleDate').val()
        },
        dataType: 'json',
        success: function(data) {
            if (data) {
                $('#scheduleId').val(data.schedule_id);
                $('#startTime').val(data.start_time);
                $('#endTime').val(data.end_time);
                $('#intervalMinutes').val(data.interval_minutes);
                
                // โหลดคอร์สก่อน แล้วค่อยเลือก
                loadCourses(data.courses);
                
                $('#deleteScheduleBtn').show();
            } else {
                resetScheduleForm();
            }
        },
        error: function() {
            console.error('เกิดข้อผิดพลาดในการโหลดข้อมูลตารางเวลา');
            resetScheduleForm();
        }
    });
}

function resetScheduleForm() {
    $('#scheduleId').val('');
    $('#startTime').val('09:00');
    $('#endTime').val('18:00');
    $('#intervalMinutes').val('30');
    $('#courses').val(null).trigger('change');
    $('#deleteScheduleBtn').hide();
    loadCourses(); // โหลดคอร์สใหม่เมื่อรีเซ็ตฟอร์ม
}

$('#scheduleForm').submit(function(e) {
    e.preventDefault();
    $.ajax({
        url: 'sql/save-schedule.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire('สำเร็จ', response.message, 'success').then(() => {
                    $('#scheduleModal').modal('hide');
                    // อัปเดตการแสดงผลตารางเวลาในหน้าหลัก (ถ้ามี)
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
            $('#courses').trigger('change'); // ทริกเกอร์ change event เพื่ออัปเดต UI
        },
        error: function() {
            console.error('เกิดข้อผิดพลาดในการโหลดรายชื่อคอร์ส');
        }
    });
}

$('#deleteScheduleBtn').click(function() {
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
            $.ajax({
                url: 'sql/delete-schedule.php',
                type: 'POST',
                data: { scheduleId: $('#scheduleId').val() },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('สำเร็จ', 'ลบตารางเวลาเรียบร้อยแล้ว', 'success').then(() => {
                            $('#scheduleModal').modal('hide');
                            // อัปเดตการแสดงผลตารางเวลาในหน้าหลัก (ถ้ามี)
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
});
    </script>

</body>
</html>
