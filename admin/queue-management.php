<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

function getStatusText($status) {
    switch($status) {
        case 'waiting': return 'รอดำเนินการ';
        case 'in_progress': return 'กำลังให้บริการ';
        case 'completed': return 'เสร็จสิ้น';
        case 'cancelled': return 'ยกเลิก';
        default: return $status;
    }
}

// ดึงข้อมูลห้องทั้งหมด
$sql_rooms = "SELECT * FROM rooms WHERE branch_id = {$_SESSION['branch_id']} AND status = 'active'";
$result_rooms = $conn->query($sql_rooms);
$rooms = [];
while ($room = $result_rooms->fetch_assoc()) {
    $rooms[] = $room;
}

// ดึงข้อมูลคิวสำหรับวันนี้
$today = date('Y-m-d');
$sql = "SELECT sq.*, c.cus_firstname, c.cus_lastname, cb.booking_datetime, 
               IFNULL(cb.is_follow_up, 0) as is_follow_up,
               r.room_name
        FROM service_queue sq
        LEFT JOIN customer c ON sq.cus_id = c.cus_id
        LEFT JOIN course_bookings cb ON sq.booking_id = cb.id
        LEFT JOIN rooms r ON cb.room_id = r.room_id
        WHERE sq.queue_date = '$today' AND sq.branch_id = {$_SESSION['branch_id']}
        ORDER BY sq.queue_time ASC";

$result = $conn->query($sql);

// ดึงข้อมูลการจองสำหรับวันนี้ที่ยังไม่ได้ถูกเพิ่มในคิว
$sql_bookings = "SELECT cb.id, cb.booking_datetime, c.cus_id, c.cus_firstname, c.cus_lastname,
                        r.room_id, r.room_name, cb.is_follow_up
                 FROM course_bookings cb
                 JOIN customer c ON cb.cus_id = c.cus_id
                 JOIN rooms r ON cb.room_id = r.room_id
                 WHERE DATE(cb.booking_datetime) = CURDATE() 
                 AND cb.branch_id = {$_SESSION['branch_id']}
                 AND cb.id NOT IN (SELECT booking_id FROM service_queue WHERE booking_id IS NOT NULL)
                 ORDER BY cb.booking_datetime ASC";
$result_bookings = $conn->query($sql_bookings);
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
    <title>จัดการคิวการให้บริการ - D Care Clinic</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet" />
    <!-- Icons -->
    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />
    <!-- Page CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/sweetalert2/sweetalert2.css" />

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
<style>
/* ส่วนแสดงเวลาปัจจุบัน */
.current-time-display {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    padding: 1.5rem;
    border-radius: 15px;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.current-time-display #currentDate {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0.5rem;
}

.current-time-display #currentTime {
    font-size: 2.5rem;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

/* Room Status Cards */
.room-status-container {
    margin-bottom: 2rem;
}

.room-status-card {
    border-radius: 12px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    border: none;
    position: relative;
    overflow: hidden;
}

/* สถานะห้องว่าง */
.room-status-available {
    background: linear-gradient(145deg, #f8fff8, #e8ffe8);
    border-left: 5px solid #28a745;
}

.room-status-available .room-status-header {
    color: #1e7e34;
}

.room-status-available .status-badge {
    background-color: #28a745;
    color: white;
}

/* สถานะกำลังใช้งาน */
.room-status-in-use {
    background: linear-gradient(145deg, #f8f9ff, #e8eeff);
    border-left: 5px solid #007bff;
}

.room-status-in-use .room-status-header {
    color: #0056b3;
}

.room-status-in-use .status-badge {
    background-color: #007bff;
    color: white;
}

/* สถานะจองแล้ว */
.room-status-reserved {
    background: linear-gradient(145deg, #fffff8, #fff8e8);
    border-left: 5px solid #ffc107;
}

.room-status-reserved .room-status-header {
    color: #d39e00;
}

.room-status-reserved .status-badge {
    background-color: #ffc107;
    color: black;
}

.room-status-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
}

.room-status-header h3 {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0;
}

.room-status-details {
    color: #495057;
}

.room-status-details .detail-item {
    display: flex;
    align-items: baseline;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.detail-item .label {
    width: 100px;
    font-weight: 500;
    color: #6c757d;
}

.detail-item .value {
    flex: 1;
    font-weight: 500;
}

.detail-item .value.highlight {
    color: #007bff;
    font-weight: 600;
}

/* Queue Table Styles */
.queue-table {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
    border: 1px solid #e9ecef;
    background-color: white;
    margin-top: 1rem;
}

.queue-table thead {
    background: linear-gradient(145deg, #f8f9fa, #e9ecef);
}

.queue-table thead th {
    color: #495057;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.9rem;
    padding: 1rem;
    border-bottom: 2px solid #dee2e6;
}

.queue-table tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid #e9ecef;
}

/* Queue Status Styles */
.queue-table tr.status-waiting {
    background-color: #fff;
}

.queue-table tr.status-in_progress {
    background: linear-gradient(145deg, #e8f4ff, #f0f9ff);
    position: relative;
    font-weight: 600;
    box-shadow: 0 2px 10px rgba(0, 123, 255, 0.1);
}

.queue-table tr.status-in_progress {
    background: linear-gradient(145deg, #e8f4ff, #f0f9ff);
    border-left: 4px solid #007bff;  /* ใช้ border-left แทน ::before */
    font-weight: 600;
    box-shadow: 0 2px 10px rgba(0, 123, 255, 0.1);
}

.queue-table tr.status-completed {
    background-color: #f8fff9;
}

.queue-table tr.status-cancelled {
    background-color: #fff5f5;
}

/* Status Badges */
.status-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

.status-badge.status-waiting {
    background-color: #ffc107;
    color: #000;
}

.status-badge.status-in_progress {
    background-color: #007bff;
    color: #fff;
    animation: pulse 2s infinite;
}

.status-badge.status-completed {
    background-color: #28a745;
    color: #fff;
}

.status-badge.status-cancelled {
    background-color: #dc3545;
    color: #fff;
}

/* Queue Elements */
.queue-number {
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
}

tr.status-in_progress .queue-number {
    color: #007bff;
}

.queue-type-indicator {
    font-size: 0.75rem;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    margin-right: 0.5rem;
}

.queue-type-indicator.follow-up {
    background-color: #e3f2fd;
    color: #1976d2;
}

.queue-type-indicator.booking {
    background-color: #f1f8e9;
    color: #7cb342;
}

/* Room Display */
.room-name {
    font-weight: 500;
    color: #666;
}

tr.status-in_progress .room-name {
    font-weight: 600;   
    color: #007bff;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.action-buttons .btn {
    padding: 0.3rem 0.8rem;
    font-size: 0.85rem;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    transition: all 0.2s ease;
}

.action-buttons .btn i {
    font-size: 1rem;
}

.action-buttons .btn-info {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.action-buttons .btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
}

/* Time Display */
.time-display {
    font-family: monospace;
    font-size: 1rem;
    color: #666;
}

tr.status-in_progress .time-display {
    color: #007bff;
    font-weight: 600;
}

/* Animations */
@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
    }
}

@keyframes highlight {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.02);
    }
    100% {
        transform: scale(1);
    }
}

.highlight-update {
    animation: highlight 0.5s ease-in-out;
}

/* Modal Styles */
.modal .select2-container {
    width: 100% !important;
}

.modal .select2-container .select2-selection--single {
    height: 38px !important;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

.modal .select2-container--bootstrap-5 .select2-selection {
    padding: 0.375rem 0.75rem;
}

.modal .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    line-height: 24px;
    padding-left: 0;
}

.select2-container {
    z-index: 9999;
}

.select2-dropdown {
    z-index: 9999;
}

/* Hover Effects */
.queue-table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.02);
}

/* Responsive Design */
@media (max-width: 768px) {
    .room-status-card {
        margin-bottom: 1rem;
    }
    
    .room-status-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .status-badge {
        align-self: flex-start;
    }

    .action-buttons {
        flex-direction: column;
    }

    .action-buttons .btn {
        width: 100%;
        justify-content: center;
    }

    .queue-table {
        font-size: 0.9rem;
    }
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
                        <h4 class="py-3 mb-4"><span class="text-muted fw-light">การจัดการคิว /</span> คิวการให้บริการวันนี้</h4>

                        <!-- Current Time Display -->
                        <div class="current-time-display">
                            <div id="currentDate" class="fs-5"></div>
                            <div id="currentTime" class="fs-1 fw-bold"></div>
                        </div>

                        <!-- Room Status Overview -->
                        <div class="room-status-container">
                            <div class="row" id="roomStatusContainer">
                                <?php foreach ($rooms as $room): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="room-status-card room-status-available" id="room-<?php echo $room['room_id']; ?>">
                                        <div class="room-status-header">
                                            <h3><?php echo htmlspecialchars($room['room_name']); ?></h3>
                                            <span class="status-badge">ว่าง</span>
                                        </div>
                                        <div class="room-status-details">
                                            <div class="detail-item">
                                                <span class="label">สถานะ:</span>
                                                <span class="value room-current-status">พร้อมให้บริการ</span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="label">ผู้ใช้งาน:</span>
                                                <span class="value highlight room-current-user">-</span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="label">เวลา:</span>
                                                <span class="value highlight room-status-time">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Queue Management -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">รายการคิวการให้บริการ</h5>
                                <div>
                                                                    <a href="room-service-summary.php" class="btn btn-danger">สรุปการให้บริการรายห้อง</a>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQueueModal">
                                    <i class="ri-add-line me-1"></i> เพิ่มคิวใหม่
                                </button>
                                </div>

                            </div>
                            <div class="card-body">
                                <!-- Queue Table -->
                                <div class="table-responsive">
                                    <table class="table table-striped queue-table" id="queueTable">
                                        <thead>
                                            <tr>
                                                <th>หมายเลขคิว</th>
                                                <th>ห้อง</th>
                                                <th>ชื่อลูกค้า</th>
                                                <th>เวลานัด</th>
                                                <th>สถานะ</th>
                                                <th>การดำเนินการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Queue data will be dynamically populated here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <?php include 'footer.php'; ?>
                    <!-- / Footer -->
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Add Queue Modal -->
    <div class="modal fade" id="addQueueModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มคิวใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addQueueForm">
                        <div class="mb-3">
                            <label class="form-label">ประเภทการจอง</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="booking_type" id="booked" value="booked" checked>
                                <label class="btn btn-outline-primary" for="booked">การจองล่วงหน้า</label>

                                <input type="radio" class="btn-check" name="booking_type" id="walk_in" value="walk_in">
                                <label class="btn btn-outline-primary" for="walk_in">Walk-in</label>
                            </div>
                        </div>

                        <!-- ในส่วนของ Booked Fields -->
                        <div id="bookedFields">
                            <div class="mb-3">
                                <label for="booking_id" class="form-label">เลือกการจอง</label>
                                <select class="form-select" id="booking_id" name="booking_id">
                                    <option value="">เลือกการจอง</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <div id="booking_info" class="text-muted small">
                                    <div id="selected_room"></div>
                                    <div id="selected_time"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Walk-in Fields -->
                        <div id="walkInFields" style="display: none;">
                            <div class="mb-3">
                                <label for="cus_id" class="form-label">ค้นหาลูกค้า</label>
                                <select class="form-select" id="cus_id" name="cus_id">
                                    <option value="">ค้นหาลูกค้า...</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="room_id" class="form-label">เลือกห้อง</label>
                                <select class="form-select" id="room_id" name="room_id">
                                    <option value="">เลือกห้อง</option>
                                    <?php foreach ($rooms as $room): ?>
                                    <option value="<?php echo $room['room_id']; ?>">
                                        <?php echo htmlspecialchars($room['room_name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="queue_time" class="form-label">เวลา</label>
                                <input type="time" class="form-control" id="queue_time" name="queue_time">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">หมายเหตุ</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" onclick="addQueue()">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>


    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <!-- <script src="../assets/js/tables-datatables-basic.js"></script> -->
    <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- Core JS -->

    <!-- Page JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
    <!-- Page JS -->

<script>
// การจัดการเวลาและการแสดงผลแบบ Real-time
$(document).ready(function() {
    // อัพเดทเวลาทุกวินาที
    setInterval(updateDateTime, 1000);
    // อัพเดทสถานะห้องทุก 30 วินาที
    setInterval(refreshRoomStatus, 30000);
    // อัพเดทตารางคิวทุก 30 วินาที
    setInterval(refreshQueueTable, 30000);
    
    // เริ่มต้นแสดงข้อมูล
    updateDateTime();
    refreshRoomStatus();
    refreshQueueTable();

  // Initialize Select2 for customer search
    $('#cus_id').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#addQueueModal'),
        ajax: {
            url: 'sql/get-customers.php',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        placeholder: 'ค้นหาลูกค้า...',
        minimumInputLength: 1,
        language: {
            inputTooShort: function() {
                return "กรุณาพิมพ์อย่างน้อย 1 ตัวอักษร";
            },
            noResults: function() {
                return "ไม่พบข้อมูลลูกค้า";
            },
            searching: function() {
                return "กำลังค้นหา...";
            }
        }
    });

    // Initialize Select2 for booking selection
    $('#booking_id').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#addQueueModal'),
        placeholder: 'เลือกการจอง'
    });

    // Handle booking type change
    $('input[name="booking_type"]').change(function() {
        const isWalkIn = $(this).val() === 'walk_in';
        $('#bookedFields').toggle(!isWalkIn);
        $('#walkInFields').toggle(isWalkIn);
        if (isWalkIn) {
            setCurrentTime();
        }
    });

    // Modal events
    $('#addQueueModal').on('show.bs.modal', function() {
        resetModal();
        loadBookings();
    });

    $('#addQueueModal').on('hidden.bs.modal', function() {
        resetModal();
    });

});



// โหลดรายการจองที่ยังไม่ได้เพิ่มคิว
function loadBookings() {
    $.ajax({
        url: 'sql/get-available-bookings.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (!response.success) {
                console.error('Error:', response.message);
                return;
            }

            const select = $('#booking_id');
            select.empty();
            select.append('<option value="">เลือกการจอง</option>');
            
            response.data.forEach(booking => {
                select.append(`
                    <option value="${booking.id}" 
                            data-room-id="${booking.room_id || ''}"
                            data-room-name="${booking.room_name || ''}"
                            data-time="${booking.time}">
                        ${booking.text}
                    </option>
                `);
            });
        },
        error: function(xhr, status, error) {
            console.error('ไม่สามารถโหลดข้อมูลการจองได้:', error);
            if (xhr.responseText) {
                console.error('Server response:', xhr.responseText);
            }
        }
    });
}

// เพิ่ม Event listener สำหรับการเปลี่ยนแปลงการเลือกการจอง
$('#booking_id').on('change', function() {
    const selectedOption = $(this).find('option:selected');
    const roomId = selectedOption.data('room-id');
    const roomName = selectedOption.data('room-name');
    const time = selectedOption.data('time');

    // ถ้ามีการเลือกการจอง
    if ($(this).val()) {
        // แสดงข้อมูลห้องและเวลาในฟอร์ม (ถ้ามี element สำหรับแสดง)
        $('#selected_room').text(roomName || 'ไม่ระบุห้อง');
        $('#selected_time').text(time || '');
    } else {
        // ล้างข้อมูลเมื่อไม่มีการเลือก
        $('#selected_room').text('');
        $('#selected_time').text('');
    }
});

// เพิ่มคิวใหม่
function addQueue() {
    const formData = new FormData($('#addQueueForm')[0]);
    
    $.ajax({
        url: 'sql/add-queue-process.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'เพิ่มคิวสำเร็จ',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    $('#addQueueModal').modal('hide');
                    refreshQueueTable();
                    refreshRoomStatus();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถเพิ่มคิวได้'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('เกิดข้อผิดพลาดในการเพิ่มคิว:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
            });
        }
    });
}

// รีเซ็ต Modal
function resetModal() {
    const form = $('#addQueueForm')[0];
    form.reset();
    
    $('#cus_id').val(null).trigger('change');
    $('#booking_id').val(null).trigger('change');
    
    // Set default to booked type
    $('input[name="booking_type"][value="booked"]').prop('checked', true).trigger('change');
    
    if ($('#walk_in').is(':checked')) {
        setCurrentTime();
    }
}

// ตั้งค่าเวลาปัจจุบัน
function setCurrentTime() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    $('#queue_time').val(`${hours}:${minutes}`);
}

// ตรวจสอบความพร้อมของห้องก่อนบันทึก
function validateRoomAvailability(roomId, timeSlot) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: 'sql/check-room-availability.php',
            type: 'POST',
            data: {
                room_id: roomId,
                time_slot: timeSlot
            },
            dataType: 'json',
            success: function(response) {
                resolve(response.available);
            },
            error: function(xhr, status, error) {
                console.error('ไม่สามารถตรวจสอบความพร้อมของห้องได้:', error);
                reject(error);
            }
        });
    });
}

// จัดการการเปลี่ยนแปลงเวลา
$('#queue_time').on('change', function() {
    const timeValue = $(this).val();
    if (timeValue) {
        const [hours, minutes] = timeValue.split(':').map(Number);
        const validHours = Math.min(Math.max(hours, 0), 23);
        const validMinutes = Math.min(Math.max(minutes, 0), 59);
        $(this).val(
            `${String(validHours).padStart(2, '0')}:${String(validMinutes).padStart(2, '0')}`
        );
    }
});

// อัพเดทเวลาทุกนาที สำหรับ Walk-in
setInterval(function() {
    if ($('#walk_in').is(':checked') && $('#addQueueModal').is(':visible')) {
        setCurrentTime();
    }
}, 60000);


// อัพเดทการแสดงวันที่และเวลาปัจจุบัน
function updateDateTime() {
    const now = new Date();
    const dateOptions = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric'
    };
    const timeOptions = { 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit', 
        hour12: false 
    };

    const dateString = now.toLocaleDateString('th-TH', dateOptions);
    const timeString = now.toLocaleTimeString('th-TH', timeOptions);

    $('#currentDate').text(dateString);
    $('#currentTime').text(timeString);
}

// อัพเดทสถานะของห้องทั้งหมด
function refreshRoomStatus() {
    $.ajax({
        url: 'sql/get-room-status.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (!response.success) {
                console.error('Error:', response.message);
                return;
            }

            response.data.forEach(room => {
                const roomCard = $(`#room-${room.room_id}`);
                const statusIcon = updateRoomStatusIcon(room.status);
                
                roomCard.removeClass('room-status-available room-status-in-use room-status-reserved')
                       .addClass(`room-status-${room.status}`);
                
                roomCard.find('.room-status-header h3').html(`${statusIcon} ${room.room_name}`);
                roomCard.find('.status-badge').text(getStatusText(room.status));
                roomCard.find('.room-current-status').text(getStatusText(room.status));
                roomCard.find('.room-current-user').text(room.current_user);
                roomCard.find('.room-status-time').text(room.time_slot);
                
                if (room.status_changed) {
                    roomCard.addClass('highlight-update');
                    setTimeout(() => {
                        roomCard.removeClass('highlight-update');
                    }, 2000);
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('ไม่สามารถอัพเดทสถานะห้องได้:', error);
        }
    });
}


// แปลงสถานะเป็นข้อความภาษาไทย
function getStatusText(status) {
    const statusMap = {
        'available': 'ว่าง',
        'in_use': 'กำลังให้บริการ',
        'reserved': 'จองแล้ว'
    };
    return statusMap[status] || status;
}

// ฟังก์ชันสำหรับจัดรูปแบบเวลา
function formatTime(timeString) {
    if (!timeString) return '-';
    const time = new Date(`1970-01-01T${timeString}`);
    return time.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit' });
}

// การจัดการตารางคิว
function refreshQueueTable() {
    $.ajax({
        url: 'sql/get-queue-data.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (!response.success) {
                console.error('Error:', response.message);
                return;
            }

            const tbody = $('#queueTable tbody');
            tbody.empty();
            
            if (response.data.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="6" class="text-center">ไม่มีคิวในวันนี้</td>
                    </tr>
                `);
                return;
            }

            response.data.forEach(queue => {
                const row = createQueueRow(queue);
                tbody.append(row);
            });

            // อัพเดทสถานะ OPD สำหรับคิวที่กำลังให้บริการ
            response.data.forEach(queue => {
                if (queue.service_status === 'in_progress') {
                    checkOPDStatus(queue.queue_id);
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('ไม่สามารถโหลดข้อมูลคิวได้:', error);
            if (xhr.responseText) {
                console.error('Server response:', xhr.responseText);
            }
        }
    });
}

// สร้างแถวข้อมูลคิว
function createQueueRow(queue) {
    const typePrefix = queue.is_follow_up ? 
        '<span class="queue-type-indicator follow-up">ติดตามผล</span>' : 
        '<span class="queue-type-indicator booking">จองคอร์ส</span>';

    const statusIcon = getStatusIcon(queue.service_status);
    let actions = '';
    
    switch(queue.service_status) {
        case 'waiting':
            actions = `
                <td class="action-buttons">
                    <button class="btn btn-sm btn-primary" onclick="updateStatus(${queue.queue_id}, 'in_progress')">
                        <i class="ri-play-line"></i> เริ่มให้บริการ
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="confirmCancelQueue(${queue.queue_id})">
                        <i class="ri-close-circle-line"></i> ยกเลิก
                    </button>
                </td>
            `;
            break;
            
        case 'in_progress':
            actions = `
                <td class="action-buttons">
                    <a href="opd.php?queue_id=${queue.queue_id}" 
                       id="opd-btn-${queue.queue_id}" 
                       class="btn btn-sm btn-info opd-btn" 
                       data-queue-id="${queue.queue_id}">
                       <i class="ri-file-list-3-line"></i> OPD
                    </a>
                    <a href="service.php?queue_id=${queue.queue_id}" 
                       class="btn btn-sm btn-info service-btn">
                       <i class="ri-service-line"></i> บริการ
                    </a>
                    <button class="btn btn-sm btn-danger" onclick="confirmCancelQueue(${queue.queue_id})">
                        <i class="ri-close-circle-line"></i> ยกเลิก
                    </button>
                </td>
            `;
            break;
            
        case 'completed':
            actions = `
                <td class="action-buttons">
                    <button class="btn btn-sm btn-warning" onclick="revertStatus(${queue.queue_id})">
                        <i class="ri-restart-line"></i> ยกเลิกสถานะ
                    </button>
                </td>
            `;
            break;
            
        case 'cancelled':
            actions = '<td class="action-buttons"></td>';
            break;
    }
    
    return `
        <tr data-queue-id="${queue.queue_id}" class="status-${queue.service_status}">
            <td>
                <div class="queue-number">${queue.queue_number}</div>
            </td>
            <td>
                <div class="room-name">${queue.room_name}</div>
            </td>
            <td>
                <div class="customer-info">
                    ${typePrefix}
                    <span class="customer-name">${queue.cus_firstname} ${queue.cus_lastname}</span>
                </div>
            </td>
            <td>
                <div class="time-display">${queue.display_time}</div>
            </td>
            <td>
                <span class="status-badge status-${queue.service_status}">
                    ${statusIcon} ${queue.status_text}
                </span>
            </td>
            ${actions}
        </tr>
    `;
}

function getStatusIcon(status) {
    switch(status) {
        case 'waiting':
            return '<i class="ri-time-line"></i>';
        case 'in_progress':
            return '<i class="ri-service-line"></i>';
        case 'completed':
            return '<i class="ri-checkbox-circle-line"></i>';
        case 'cancelled':
            return '<i class="ri-close-circle-line"></i>';
        default:
            return '';
    }
}

// สร้างปุ่มดำเนินการ
function createActionButtons(queue) {
    let buttons = '';
    
    switch(queue.service_status) {
        case 'waiting':
            buttons = `
                <button class="btn btn-sm btn-primary" onclick="updateStatus(${queue.queue_id}, 'in_progress')">
                    <i class="ri-play-line"></i> เริ่มให้บริการ
                </button>
            `;
            break;
            
        case 'in_progress':
            buttons = `
                <a href="opd.php?queue_id=${queue.queue_id}" 
                   id="opd-btn-${queue.queue_id}" 
                   class="btn btn-sm btn-info opd-btn" 
                   data-queue-id="${queue.queue_id}">
                   <i class="ri-file-list-3-line"></i> OPD
                </a>
                <a href="service.php?queue_id=${queue.queue_id}" 
                   class="btn btn-sm btn-info service-btn">
                   <i class="ri-service-line"></i> บริการ
                </a>
            `;
            break;
            
        case 'completed':
            buttons = `
                <button class="btn btn-sm btn-warning" onclick="revertStatus(${queue.queue_id})">
                    <i class="ri-restart-line"></i> ยกเลิกสถานะ
                </button>
            `;
            break;
    }

    if (!['completed', 'cancelled'].includes(queue.service_status)) {
        buttons += `
            <button class="btn btn-sm btn-danger" onclick="confirmCancelQueue(${queue.queue_id})">
                <i class="ri-close-circle-line"></i> ยกเลิก
            </button>
        `;
    }

    return buttons;
}

// ตรวจสอบสถานะ OPD
function checkOPDStatus(queueId) {
    $.ajax({
        url: 'sql/check-opd-status.php',
        type: 'GET',
        data: { queue_id: queueId },
        dataType: 'json',
        success: function(response) {
            const opdBtn = $(`#opd-btn-${queueId}`);
            
            if (response.has_opd) {
                opdBtn.removeClass('btn-info').addClass(
                    response.opd_status === 1 ? 'btn-success' : 'btn-info'
                );
            }
        },
        error: function(error) {
            console.error('ไม่สามารถตรวจสอบสถานะ OPD ได้:', error);
        }
    });
}

// แปลงสถานะคิวเป็นข้อความภาษาไทย
function getQueueStatusText(status) {
    const statusMap = {
        'waiting': 'รอดำเนินการ',
        'in_progress': 'กำลังให้บริการ',
        'completed': 'เสร็จสิ้น',
        'cancelled': 'ยกเลิก'
    };
    return statusMap[status] || status;
}

// การจัดการสถานะคิวและการยืนยัน
function updateStatus(queueId, newStatus) {
    // ตรวจสอบเงื่อนไขพิเศษก่อนอัพเดทสถานะ
    if (newStatus === 'in_progress') {
        checkRoomAvailabilityBeforeStart(queueId);
    } else {
        processStatusUpdate(queueId, newStatus);
    }
}

// ตรวจสอบความพร้อมของห้องก่อนเริ่มให้บริการ
function checkRoomAvailabilityBeforeStart(queueId) {
    $.ajax({
        url: 'sql/check-queue-room.php',
        type: 'POST',
        data: { queue_id: queueId },
        dataType: 'json',
        success: function(response) {
            if (response.room_available) {
                processStatusUpdate(queueId, 'in_progress');
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'ห้องไม่ว่าง',
                    text: 'ห้องที่เลือกกำลังถูกใช้งานอยู่ กรุณารอหรือเลือกห้องอื่น',
                    showCancelButton: true,
                    confirmButtonText: 'เลือกห้องใหม่',
                    cancelButtonText: 'ยกเลิก',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-outline-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        showRoomSelectionModal(queueId);
                    }
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('ไม่สามารถตรวจสอบสถานะห้องได้:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถตรวจสอบสถานะห้องได้'
            });
        }
    });
}

// แสดง Modal เลือกห้องใหม่
function showRoomSelectionModal(queueId) {
    $.ajax({
        url: 'sql/get-available-rooms.php',
        type: 'GET',
        dataType: 'json',
        success: function(rooms) {
            let roomOptions = rooms.map(room => 
                `<option value="${room.room_id}">${room.room_name}</option>`
            ).join('');

            Swal.fire({
                title: 'เลือกห้องใหม่',
                html: `
                    <select id="new-room-select" class="form-select mb-3">
                        <option value="">เลือกห้อง</option>
                        ${roomOptions}
                    </select>
                `,
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-secondary'
                },
                preConfirm: () => {
                    const newRoomId = document.getElementById('new-room-select').value;
                    if (!newRoomId) {
                        Swal.showValidationMessage('กรุณาเลือกห้อง');
                        return false;
                    }
                    return newRoomId;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateQueueRoom(queueId, result.value);
                }
            });
        },
        error: function(xhr, status, error) {
            console.error('ไม่สามารถโหลดข้อมูลห้องได้:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถโหลดข้อมูลห้องได้'
            });
        }
    });
}

// อัพเดทห้องและสถานะคิว
function updateQueueRoom(queueId, newRoomId) {
    $.ajax({
        url: 'sql/update-queue-room.php',
        type: 'POST',
        data: {
            queue_id: queueId,
            room_id: newRoomId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                processStatusUpdate(queueId, 'in_progress');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถอัพเดทห้องได้'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('ไม่สามารถอัพเดทห้องได้:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถอัพเดทห้องได้'
            });
        }
    });
}

// ดำเนินการอัพเดทสถานะคิว
function processStatusUpdate(queueId, newStatus) {
    $.ajax({
        url: 'sql/update-queue-status.php',
        type: 'POST',
        data: {
            queue_id: queueId,
            status: newStatus
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showSuccessMessage(newStatus);
                refreshQueueTable();
                refreshRoomStatus();
                
                // ถ้าสถานะเป็น in_progress ให้เริ่มตรวจสอบ OPD
                if (newStatus === 'in_progress') {
                    checkOPDStatus(queueId);
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถอัพเดทสถานะได้'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('ไม่สามารถอัพเดทสถานะได้:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถอัพเดทสถานะได้'
            });
        }
    });
}

// แสดงข้อความสำเร็จตามสถานะ
function showSuccessMessage(status) {
    let message = '';
    switch(status) {
        case 'in_progress':
            message = 'เริ่มให้บริการสำเร็จ';
            break;
        case 'completed':
            message = 'เสร็จสิ้นการให้บริการ';
            break;
        case 'cancelled':
            message = 'ยกเลิกคิวสำเร็จ';
            break;
        default:
            message = 'อัพเดทสถานะสำเร็จ';
    }

    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ',
        text: message,
        showConfirmButton: false,
        timer: 1500
    });
}

// ยืนยันการยกเลิกคิว
function confirmCancelQueue(queueId) {
    Swal.fire({
        title: 'ยืนยันการยกเลิก',
        text: "คุณต้องการยกเลิกคิวนี้ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก',
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-outline-secondary'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            updateStatus(queueId, 'cancelled');
        }
    });
}

// ยืนยันการยกเลิกสถานะเสร็จสิ้น
function revertStatus(queueId) {
    Swal.fire({
        title: 'ยืนยันการยกเลิกสถานะ',
        text: "คุณต้องการยกเลิกสถานะ 'เสร็จสิ้น' และกลับไปเป็น 'กำลังให้บริการ' ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก',
        customClass: {
            confirmButton: 'btn btn-warning',
            cancelButton: 'btn btn-outline-secondary'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            updateStatus(queueId, 'in_progress');
        }
    });
}
function updateRoomStatusIcon(status) {
    switch(status) {
        case 'available':
            return '<i class="ri-checkbox-circle-line text-success"></i>';
        case 'in_use':
            return '<i class="ri-time-line text-primary"></i>';
        case 'reserved':
            return '<i class="ri-calendar-check-line text-warning"></i>';
        default:
            return '';
    }
}
</script>
</body>
</html>