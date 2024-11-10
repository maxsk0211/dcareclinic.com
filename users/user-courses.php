<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['users_id'])) {
    header('Location: ../login.php');
    exit;
}

$current_date = date('Y-m-d');
$search_term = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$selected_branch = isset($_GET['branch_id']) ? intval($_GET['branch_id']) : 0;

// Fetch branches - แก้ไขโดยไม่ใช้ branch_status
$branches_query = "SELECT branch_id, branch_name FROM branch WHERE branch_id > 0";
$branches_result = mysqli_query($conn, $branches_query);

if (!$branches_result) {
    die("Error fetching branches: " . mysqli_error($conn));
}

$branches = [];
while ($row = mysqli_fetch_assoc($branches_result)) {
    $branches[] = $row;
}

// Fetch available courses based on selected branch
$available_query = "
    SELECT c.*, 
           COUNT(od.course_id) AS booking_count
    FROM course c
    LEFT JOIN (
        SELECT od.course_id
        FROM order_detail od 
        JOIN order_course oc ON od.oc_id = oc.oc_id 
        WHERE oc.cus_id = {$_SESSION['users_id']}
    ) od ON c.course_id = od.course_id
    WHERE c.course_status = 1 
    AND c.course_start <= '$current_date' 
    AND c.course_end >= '$current_date'
";

if ($selected_branch > 0) {
    $available_query .= " AND c.branch_id = $selected_branch";
}

if (!empty($search_term)) {
    $available_query .= " AND (c.course_name LIKE '%$search_term%' OR c.course_detail LIKE '%$search_term%')";
}

$available_query .= " GROUP BY c.course_id ORDER BY c.course_start ASC";
$available_result = mysqli_query($conn, $available_query);

// Fetch clinic closure dates
$sql_closures = "SELECT closure_date FROM clinic_closures";
if ($selected_branch > 0) {
    $sql_closures .= " WHERE branch_id = $selected_branch";
}
$result_closures = $conn->query($sql_closures);
$closed_dates = [];
while ($row = $result_closures->fetch_object()) {
    if ($row->closure_date) {
        $date = new DateTime($row->closure_date);
        $thaiYear = $date->format('Y') + 543;
        $closed_dates[] = $thaiYear . '-' . $date->format('m-d');
    }
}

// Fetch clinic hours
$sql_hours = "SELECT * FROM clinic_hours";
if ($selected_branch > 0) {
    $sql_hours .= " WHERE branch_id = $selected_branch";
}
$result_hours = $conn->query($sql_hours);
$clinic_hours = [];
$closed_days = [];
while ($row = $result_hours->fetch_object()) {
    $clinic_hours[$row->day_of_week] = [
        'start_time' => $row->start_time,
        'end_time' => $row->end_time,
        'is_closed' => $row->is_closed
    ];
    if ($row->is_closed == 1) {
        $closed_days[] = $row->day_of_week;
    }
}

// Fetch existing bookings
$sql_bookings = "SELECT booking_datetime FROM course_bookings WHERE status IN ('pending', 'confirmed')";
if ($selected_branch > 0) {
    $sql_bookings .= " AND branch_id = $selected_branch";
}
$result_bookings = $conn->query($sql_bookings);
$booked_slots = [];
while ($row = $result_bookings->fetch_object()) {
    if ($row->booking_datetime) {
        $booked_slots[] = $row->booking_datetime;
    }
}

function thaiDate($date) {
    $thai_months = [
        1 => 'มกราคม', 2 => 'กุมภาพันธ์', 3 => 'มีนาคม', 4 => 'เมษายน', 5 => 'พฤษภาคม', 6 => 'มิถุนายน',
        7 => 'กรกฎาคม', 8 => 'สิงหาคม', 9 => 'กันยายน', 10 => 'ตุลาคม', 11 => 'พฤศจิกายน', 12 => 'ธันวาคม'
    ];
    $date_parts = explode('-', $date);
    $year = intval($date_parts[0]) + 543;
    $month = intval($date_parts[1]);
    $day = intval($date_parts[2]);
    return $day . ' ' . $thai_months[$month] . ' พ.ศ. ' . $year;
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

    <title>User Courses - D Care Clinic System</title>

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
    <link rel="stylesheet" href="../assets/vendor/libs/flatpickr/flatpickr.css" />
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

    <style>
        /* Global Styles */
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Course Card Styles */
        .course-card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            height: 100%;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            border: none;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .course-image {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .course-title {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 0.7rem;
            color: #333;
        }
        .course-detail {
            font-size: 0.95rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        .course-meta {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .course-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
            background-color: #e9f7ef;
            border-radius: 5px;
            padding: 5px 10px;
            display: inline-block;
            margin-bottom: 15px;
        }

        /* Branch Selection Styles */
        .branch-select {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        /* Time Slot Styles */
        .time-slot {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .time-slot:hover:not(.disabled) {
            background-color: #e9ecef;
        }
        .time-slot.selected {
            background-color: #28a745;
            color: white;
        }
        .time-slot.disabled {
            background-color: #f8f9fa;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Modal Styles */
        .booking-modal .modal-content {
            border-radius: 15px;
        }
        .booking-modal .modal-header {
            background-color: #f8f9fa;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .booking-modal .modal-body {
            padding: 20px;
        }
        .booking-modal .modal-footer {
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }
    </style>
  </head>

  <body>
    <!-- Layout wrapper -->
    < <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include 'menu.php'; ?>
            <div class="layout-page">
                <?php include 'navbar.php'; ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="branch-select">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h4 class="fw-bold py-3 mb-4">เลือกสาขา</h4>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-select" id="branchSelect" name="branch_id">
                                        <option value="">กรุณาเลือกสาขา</option>
                                        <?php foreach ($branches as $branch): ?>
                                            <option value="<?php echo $branch['branch_id']; ?>" 
                                                    <?php echo ($selected_branch == $branch['branch_id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($branch['branch_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Search Form -->
                        <div class="card mb-4" <?php echo $selected_branch ? '' : 'style="display: none;"'; ?>>
                            <div class="card-body">
                                <form action="" method="GET" class="row g-3">
                                    <input type="hidden" name="branch_id" value="<?php echo $selected_branch; ?>">
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="search" name="search" 
                                               placeholder="ค้นหาคอร์ส" value="<?php echo htmlspecialchars($search_term); ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">ค้นหา</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Available Courses -->
                        <div class="row course-container" <?php echo $selected_branch ? '' : 'style="display: none;"'; ?>>
                            <?php
                            $available_count = 0;
                            if ($selected_branch && $available_result):
                                while ($course = mysqli_fetch_object($available_result)):
                                    $available_count++;
                            ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 course-card">
                                        <img class="card-img-top course-image" 
                                             src="../img/course/<?php echo htmlspecialchars($course->course_pic); ?>" 
                                             alt="<?php echo htmlspecialchars($course->course_name); ?>">
                                        <div class="card-body">
                                            <h5 class="course-title"><?php echo htmlspecialchars($course->course_name); ?></h5>
                                            <p class="course-price"><?php echo number_format($course->course_price, 2); ?> บาท</p>
                                            <p class="card-text"><?php echo htmlspecialchars(substr($course->course_detail, 0, 100)) . '...'; ?></p>
                                            <p class="course-meta">
                                                <i class="ri-calendar-line"></i> เริ่ม: <?php echo thaiDate($course->course_start); ?><br>
                                                <i class="ri-calendar-check-line"></i> สิ้นสุด: <?php echo thaiDate($course->course_end); ?>
                                            </p>
                                            <?php if ($course->booking_count > 0): ?>
                                                <p class="text-info">คุณได้จองคอร์สนี้แล้ว <?php echo $course->booking_count; ?> ครั้ง</p>
                                            <?php endif; ?>
                                            <button class="btn btn-primary book-course" 
                                                    data-course-id="<?php echo $course->course_id; ?>"
                                                    data-branch-id="<?php echo $course->branch_id; ?>"
                                                    data-duration="<?php echo $course->duration; ?>">
                                                จองตอนนี้
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php 
                                endwhile;
                            endif;
                            
                            if ($selected_branch && $available_count == 0): 
                            ?>
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        ไม่พบคอร์สที่ตรงกับการค้นหาของคุณ
                                    </div>
                                </div>
                            <?php elseif (!$selected_branch): ?>
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        กรุณาเลือกสาขาเพื่อดูคอร์สที่มีให้บริการ
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php include 'footer.php'; ?>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>

  <div class="modal fade booking-modal" id="bookingModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">จองคอร์ส</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bookingForm" method="POST">
                        <input type="hidden" id="courseId" name="courseId">
                        <input type="hidden" id="branchId" name="branchId">
                        <div class="mb-3">
                            <label for="booking_date" class="form-label">เลือกวันที่</label>
                            <input type="text" class="form-control" id="booking_date" name="booking_date" required readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">เลือกเวลา</label>
                            <div id="timeSlots" class="row text-center"></div>
                        </div>
                        <input type="hidden" id="booking_time" name="booking_time">
                        <input type="hidden" id="selected_room_id" name="selected_room_id">
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            ยืนยันการจอง
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <!-- sweet Alerts 2 -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
    document.addEventListener('DOMContentLoaded', function() {
        const clinicHours = <?php echo json_encode($clinic_hours); ?>;
        const closedDates = <?php echo json_encode($closed_dates); ?>;
        const bookedSlots = <?php echo json_encode($booked_slots); ?>;
        const closedDays = <?php echo json_encode($closed_days); ?>;
        let timeSelected = false;

        // Branch Selection Handler
        $('#branchSelect').change(function() {
            const branchId = $(this).val();
            if (branchId) {
                window.location.href = `user-courses.php?branch_id=${branchId}`;
            } else {
                window.location.href = 'user-courses.php';
            }
        });

        // Booking Button Click Handler
        $('.book-course').click(function(e) {
            e.preventDefault();
            const courseId = $(this).data('course-id');
            const branchId = $(this).data('branch-id');
            const duration = $(this).data('duration');

            $('#courseId').val(courseId);
            $('#branchId').val(branchId);

            // Fetch available slots from the server
            $.ajax({
                url: 'sql/get-available-slots.php',
                method: 'POST',
                data: { 
                    course_id: courseId,
                    branch_id: branchId,
                    selected_date: new Date().toISOString().split('T')[0]
                },
                dataType: 'json', // เพิ่มบรรทัดนี้
                success: function(response) {
                    try {
                        if (response.error) {
                            Swal.fire('เกิดข้อผิดพลาด', response.error, 'error');
                            return;
                        }

                        const availableDates = response.available_dates;
                        const availableSlots = response.available_slots;

                        if (!availableDates || !availableSlots) {
                            Swal.fire('เกิดข้อผิดพลาด', 'ไม่พบข้อมูลตารางเวลา', 'error');
                            return;
                        }

                        initializeDatePicker(response);
                        $('#bookingModal').modal('show');

                    } catch (e) {
                        console.error('Error processing response:', e, response);
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถประมวลผลข้อมูลได้', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
                    console.log('Response:', xhr.responseText);
                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์', 'error');
                }
            });
        });

        // Initialize Datepicker
        function initializeDatePicker(response) {
            if (!response || !response.available_dates) {
                console.error('Invalid response:', response);
                Swal.fire('เกิดข้อผิดพลาด', 'ข้อมูลวันที่ไม่ถูกต้อง', 'error');
                return;
            }

            flatpickr("#booking_date", {
                locale: 'th',
                minDate: "today",
                maxDate: new Date().fp_incr(30),
                enable: response.available_dates, // เปลี่ยนจาก disable เป็น enable
                onChange: function(selectedDates, dateStr) {
                    if (selectedDates.length > 0) {
                        fetchAvailableSlots(selectedDates[0]);
                    }
                },
                onReady: function(selectedDates, dateStr, instance) {
                    const yearInput = instance.currentYearElement;
                    yearInput.value = parseInt(yearInput.value) + 543;
                }
            });
        }

        // Fetch Available Time Slots
        function fetchAvailableSlots(selectedDate, duration) {
            const formattedDate = formatDate(selectedDate);
            const courseId = $('#courseId').val();
            const branchId = $('#branchId').val();

            $.ajax({
                url: 'sql/get-available-slots.php',
                method: 'POST',
                data: {
                    selected_date: formattedDate,
                    course_id: courseId,
                    branch_id: branchId,
                    duration: duration
                },
                dataType: 'json', // เพิ่มบรรทัดนี้
                success: function(response) {
                    if (response.error) {
                        Swal.fire('เกิดข้อผิดพลาด', response.error, 'error');
                        return;
                    }

                    if (response.available_slots) {
                        renderTimeSlots(response.available_slots);
                    } else {
                        console.error('No available slots in response:', response);
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่พบช่วงเวลาที่ว่าง', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    console.log('Response:', xhr.responseText);
                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์', 'error');
                }
            });
        }

        // Render Time Slots
        function renderTimeSlots(slots) {
            const container = $('#timeSlots');
            container.empty();

            if (!Array.isArray(slots)) {
                console.error('Invalid slots data:', slots);
                return;
            }

            if (slots.length === 0) {
                container.html('<div class="col-12"><div class="alert alert-info">ไม่มีช่วงเวลาว่างในวันที่เลือก</div></div>');
                return;
            }

            slots.forEach(slot => {
                const slotElement = $('<div>', {
                    class: 'col-md-3 mb-2'
                });

                const buttonClass = getSlotButtonClass(slot.status);
                const isDisabled = slot.status === 'fully_booked';

                const button = $('<button>', {
                    type: 'button',
                    class: `btn ${buttonClass} time-slot w-100`,
                    text: formatTimeDisplay(slot.time),
                    disabled: isDisabled
                });

                if (!isDisabled) {
                    button.data({
                        time: slot.time,
                        roomId: slot.available_rooms[0]?.room_id
                    });
                }

                const roomInfo = $('<small>', {
                    class: 'd-block',
                    text: getSlotAvailabilityText(slot)
                });

                slotElement.append(button).append(roomInfo);
                container.append(slotElement);
            });

            // Add click handler for time slots
            $('.time-slot:not(:disabled)').click(function() {
                $('.time-slot').removeClass('selected');
                $(this).addClass('selected');
                
                $('#booking_time').val($(this).data('time'));
                $('#selected_room_id').val($(this).data('roomId'));
                $('#submitBtn').prop('disabled', false);
            });
        }
        
        // เพิ่มฟังก์ชัน helper
        function getSlotButtonClass(status) {
            switch(status) {
                case 'fully_booked':
                    return 'btn-secondary';
                case 'partially_booked':
                    return 'btn-warning';
                default:
                    return 'btn-outline-primary';
            }
        }

        // Format time for display
        function formatTimeDisplay(time) {
            return time.substring(0, 5);
        }

        // Get button class based on slot status
        function getSlotButtonClass(slot) {
            switch(slot.status) {
                case 'fully_booked':
                    return 'btn-secondary';
                case 'partially_booked':
                    return 'btn-warning';
                default:
                    return 'btn-outline-primary';
            }
        }

        // Get availability text
        function getSlotAvailabilityText(slot) {
            if (slot.status === 'fully_booked') {
                return 'ไม่ว่าง';
            }
            return `ว่าง ${slot.available_rooms.length} ห้อง`;
        }

        // Format date
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Booking Form Submit Handler
        $('#bookingForm').submit(function(e) {
            e.preventDefault();
            
            const formData = {
                course_id: $('#courseId').val(),
                branch_id: $('#branchId').val(),
                booking_date: $('#booking_date').val(),
                booking_time: $('#booking_time').val(),
                room_id: $('#selected_room_id').val()
            };

            // แสดง loading
            Swal.fire({
                title: 'กำลังดำเนินการ',
                text: 'กรุณารอสักครู่...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: 'process-booking.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(result) {
                    if (result && result.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'จองสำเร็จ',
                            html: `
                                <p>การจองคอร์สของคุณเสร็จสมบูรณ์</p>
                                <p>รหัสการจอง: ${result.booking_id}</p>
                            `,
                            showConfirmButton: true
                        }).then((swalResult) => {
                            if (swalResult.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: result.message || 'ไม่สามารถทำการจองได้'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // ปิด loading
                    Swal.close();

                    console.error('AJAX Error:', status, error);
                    if (xhr.responseText) {
                        try {
                            const errorResult = JSON.parse(xhr.responseText);
                            Swal.fire('เกิดข้อผิดพลาด', errorResult.message || 'ไม่สามารถทำการจองได้', 'error');
                        } catch (e) {
                            console.error('Error parsing error response:', e);
                            Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์', 'error');
                        }
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์', 'error');
                    }
                }
            });
        });
    });
    </script>
</body>
</html>