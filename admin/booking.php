<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

$branch_id=$_SESSION['branch_id'];
$selected_customer_id = isset($_POST['selected_customer_id']) ? $_POST['selected_customer_id'] : '';
$customer_name = isset($_POST['customer_name']) ? $_POST['customer_name'] : '';
// ดึงข้อมูลคอร์ส
$sql_courses = "SELECT course_id, course_name, course_price, course_pic FROM course WHERE course_status = 1 and branch_id='$branch_id'";
$result_courses = $conn->query($sql_courses);
$courses = [];
while ($row = $result_courses->fetch_object()) {
    $courses[] = [
        'id' => $row->course_id,
        'name' => $row->course_name,
        'price' => $row->course_price,
        'image' => $row->course_pic
    ];
}


$search_term = '';



$display_courses = $courses;
if (!empty($search_term)) {
    $display_courses = array_filter($courses, function($course) use ($search_term) {
        return stripos($course['name'], $search_term) !== false;
    });
}


function getCustomers($conn) {
    $sql = "SELECT cus_id, cus_id_card_number, cus_firstname, cus_lastname, cus_email, cus_tel FROM customer";
    $result = $conn->query($sql);
    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
    return $customers;
}

$customers = getCustomers($conn);

function formatCustomerId($cusId) {
    $paddedId = str_pad($cusId, 6, '0', STR_PAD_LEFT);
    return "HN-" . $paddedId;
}
?>

<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>จองคอร์ส - D Care Clinic</title>

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
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/sweetalert2/sweetalert2.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
        <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
    <style>
        .time-slot {
            cursor: pointer;
        }
        .time-slot.booked {
            background-color: #ff8785;
            cursor: not-allowed;
        }
        .time-slot.selected {
            background-color: #8cff85;
        }
        .course-item {
            height: 100%;
        }
        .course-item .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .course-item .card-body {
            display: flex;
            flex-direction: column;
        }
        .course-item .form-check {
            margin-top: auto;
        }

        #customerDetails {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        #customerImage {
            border: 3px solid #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        #customerDetails table {
            margin-top: 20px;
        }
        #customerDetails th {
            color: #6c757d;
            font-weight: 600;
        }
    .select2-container .select2-selection--single {
        height: 48px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    .select2-container .select2-selection--single, 
    #booking_date {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .course-item {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .course-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .course-item.selected {
        border: 2px solid #007bff;
        background-color: #e7f1ff;
    }

    .course-item.selected::after {
        content: '✓';
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #007bff;
        color: white;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    #selected_course {
        background-color: #e7f1ff;
        border: 1px solid #b8daff;
        border-radius: 5px;
        padding: 10px;
        margin-top: 10px;
    }

    #selected_course h4 {
        color: #004085;
        margin-bottom: 0;
    }
    #backToCourseBtn {
        font-size: 0.9rem;
        padding: 0.375rem 0.75rem;
    }

    #backToCourseBtn i {
        font-size: 1rem;
        vertical-align: middle;
    }
    .flatpickr-calendar {
        font-family: 'Sarabun', sans-serif; /* หรือฟอนต์ภาษาไทยที่คุณใช้ */
    }

    .flatpickr-current-month .flatpickr-monthDropdown-months {
        font-size: 1rem;
    }

    .flatpickr-current-month .numInputWrapper {
        font-size: 1rem;
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
                    <div class="container flex-grow-1 container-p-y">
    
        <?php if(!isset($_POST['select_datetime'])){ ?>
        <div class="row">    
            <form id="bookingForm" method="POST">
                <div class="card mb-4">
                    <div class="text-end m-5"><a href="booking-show.php" class="btn btn-danger">ข้อมูลการจอง</a></div>
                    <div class="row">
                        <div class="offset-md-3 col-md-6">
                            <h5 class="card-header">เลือกลูกค้า</h5>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="customer_select" class="form-label">ค้นหาลูกค้า</label>
                                    <select id="customer_select" name="customer_select" class="form-select select2" style="width: 100%;">
                                        <option value="">เลือกลูกค้า</option>
                                    </select>
                                </div>
                                <div id="customerDetails" class="mt-4" style="display: none;">
                                    <div class="text-center mb-3">
                                        <img id="customerImage" src="" alt="รูปลูกค้า" class="img-fluid rounded-circle" style="max-width: 150px; max-height: 150px;">
                                    </div>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>รหัสลูกค้า:</th>
                                            <td id="customerHN"></td>
                                        </tr>
                                        <tr>
                                            <th>ชื่อ-นามสกุล:</th>
                                            <td id="customerName"></td>
                                        </tr>
                                        <tr>
                                            <th>เลขบัตรประชาชน:</th>
                                            <td id="customerIdCard"></td>
                                        </tr>
                                        <tr>
                                            <th>อีเมล:</th>
                                            <td id="customerEmail"></td>
                                        </tr>
                                        <tr>
                                            <th>เบอร์โทร:</th>
                                            <td id="customerTel"></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="selected_customer_id" name="selected_customer_id" value="<?php echo htmlspecialchars($selected_customer_id); ?>">
                    <input type="hidden" name="select_datetime" value="1">
                    <div class="d-grid gap-2 col-3 mx-auto my-5">
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>ถัดไป</button>
                    </div>
                </div>
            </form>
        </div>
        <?php } else { 
            if (!empty($selected_customer_id)) {
                $selected_customer_id = $_POST['selected_customer_id'];
                $sql_cus = "SELECT cus_id, cus_firstname, cus_lastname FROM `customer` WHERE cus_id = '$selected_customer_id'";
                $result_cus = $conn->query($sql_cus);
                // $row_cus = $result_cus->fetch_object();
                
                if ($row_cus = $result_cus->fetch_object()) {
                    $customer_name = formatCustomerId($row_cus->cus_id) . " " . $row_cus->cus_firstname . " " . $row_cus->cus_lastname;
                } else {
                    $customer_name = '';
                }
            } else {
                $customer_name = '';
            }
        ?>
            <div class="text-center mb-4">
                <h3>ลูกค้า: <?php echo htmlspecialchars($customer_name); ?></h3>
                <div id="selected_course" class="mt-3"></div>
            </div>
            <div class="row">
                <div class="offset-md-1 col-md-10">
                    <div id="courseSection">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <div class="text-end">
                                    <a href="booking.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> ย้อนกลับ</a>
                                </div>
<!--                                 <label for="courseSearch" class="form-label">ค้นหาคอร์ส</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="courseSearch" name="search_course" placeholder="พิมพ์ชื่อคอร์ส..." value="<?= htmlspecialchars($search_term) ?>">
                                    <button type="submit" class="btn btn-primary">ค้นหา</button>
                                </div> -->
                            </div>
                            <input type="hidden" name="selected_customer_id" value="<?= $selected_customer_id; ?>">
                            <input type="hidden" name="select_datetime" value="1">
                        </form>
                        <div id="courseList" class="row">
                            <?php foreach ($display_courses as $course): ?>
                                <div class="col-md-4 col-lg-3 mb-3">
                                    <div class="card course-item" data-course-id="<?= $course['id'] ?>" data-course-name="<?= htmlspecialchars($course['name']) ?>" data-course-price="<?= $course['price'] ?>" data-course-image="<?= htmlspecialchars($course['image']) ?>">
                                        <img src="../img/course/<?= htmlspecialchars($course['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($course['name']) ?>">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($course['name']) ?></h5>
                                            <p class="card-text">ราคา: <?= number_format($course['price']) ?> บาท</p>
                                            <button type="button" class="btn btn-primary select-course">เลือกคอร์สนี้</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="row">
            <div class="offset-md-2 col-md-8">
                <div id="dateTimeSection" style="display: none;">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title">เลือกวันและเวลา</h5>
                                <button type="button" class="btn btn-secondary" id="backToCourseBtn">
                                    <i class="ri-arrow-left-line me-1"></i> กลับไปเลือกคอร์ส
                                </button>
                            </div>
                            <div class="mb-3">
                                <label for="booking_date" class="form-label">เลือกวันที่</label>
                                <input type="text" class="form-control" id="booking_date" name="booking_date" placeholder="เลือกวันที่" required readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">เลือกเวลา</label>
                                <div id="timeSlots" class="row text-center">
                                </div>
                            </div>
                            <input type="hidden" id="booking_time" name="booking_time">
                            <input type="hidden" id="selected_room_id" name="selected_room_id">
                            <input type="hidden" id="selected_course_id" name="selected_course_id">
                            <button type="button" id="confirmBookingBtn" class="btn btn-primary mt-3">ยืนยันการจอง</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

                    <!-- / Content -->

                    <!-- Footer -->
                    <?php include 'footer.php'; ?>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- / Content wrapper -->
            </div>
            <!-- / Layout container -->
        </div>
    </div>
    <!-- / Layout wrapper -->

<!-- Core JS -->
<script src="../assets/vendor/libs/jquery/jquery.js"></script>
<script src="../assets/vendor/libs/popper/popper.js"></script>
<script src="../assets/vendor/js/bootstrap.js"></script>
<script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
<script src="../assets/vendor/libs/hammer/hammer.js"></script>
<script src="../assets/vendor/js/menu.js"></script>

<!-- Vendors JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Main JS -->
<script src="../assets/js/main.js"></script>

<!-- Page JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>

<script>
var selectedCustomerId = '<?php echo addslashes($selected_customer_id); ?>';
// var customerName = '<?php echo addslashes($customer_name); ?>';
$(document).ready(function() {
let selectedCourses = [];

function updateOrderList() {
    const orderList = $('#orderList');
    orderList.empty();

    let totalPrice = 0;

    selectedCourses.forEach(course => {
        orderList.append(`
            <div class="d-flex justify-content-between mb-2">
                <span>${course.name}</span>
                <span>${course.price.toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2})} บาท</span>
            </div>
        `);
        totalPrice += course.price;
    });

    $('#totalPrice').text(totalPrice.toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#selectedCoursesInput').val(JSON.stringify(selectedCourses));
}

    // เพิ่ม event listener สำหรับปุ่มย้อนกลับ
    $('#backToCourseBtn').on('click', function() {
        $('#dateTimeSection').hide();
        $('#courseSection').show();
        $('#selected_course').html(''); // ล้างข้อมูลคอร์สที่เลือก
    });


   $('.select-course').on('click', function() {
        var courseCard = $(this).closest('.course-item');
        var courseId = courseCard.data('course-id');
        var courseName = courseCard.data('course-name');
        var coursePrice = courseCard.data('course-price');
        var courseImage = courseCard.data('course-image');

        // เก็บค่า course_id
        $('#selected_course_id').val(courseId);
        console.log('Selected Course ID:', courseId);

         Swal.fire({
            title: 'ยืนยันการเลือกคอร์ส',
            html: `
                <img src="../img/course/${courseImage}" style="max-width: 200px; margin-bottom: 10px;">
                <p><strong>${courseName}</strong></p>
                <p>ราคา: ${coursePrice.toLocaleString()} บาท</p>
            `,
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
             if (result.isConfirmed) {
                var formattedCourseId = 'C-' + courseId.toString().padStart(6, '0');
                $('#selected_course').html(`
                    <h4>คอร์สที่เลือก: ${formattedCourseId} - ${courseName}</h4>
                `);
                // ส่ง AJAX request เพื่อดึงข้อมูลวันและเวลาที่สามารถจองได้
                $.ajax({
                    url: 'sql/get-available-slots.php',
                    method: 'POST',
                    data: { 
                        course_id: courseId,
                        selected_date: new Date().toISOString().split('T')[0] // ส่งวันที่ปัจจุบัน
                    },
                    success: function(response) {
                        // console.log('Raw response:', response);
                        try {
                            var availableSlots = JSON.parse(response);
                            console.log('Parsed availableSlots:', availableSlots);
                            showDateTimePicker(availableSlots);
                        } catch (e) {
                            console.error('Error parsing JSON:', e);
                            Swal.fire('เกิดข้อผิดพลาด', 'ข้อมูลไม่ถูกต้อง', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', status, error);
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถดึงข้อมูลการจองได้', 'error');
                    }
                });
            }
        });
    });

    $('#confirmBookingBtn').on('click', function() {
        const customerId = selectedCustomerId;
        const courseId = $('#selected_course_id').val();
        const bookingDate = $('#booking_date').val();
        const bookingTime = $('#booking_time').val();
        const selectedRoomId = $('#selected_room_id').val();
        const selectedSlot = $('.time-slot.selected');
        const intervalMinutes = selectedSlot.data('interval');
        const availableRooms = selectedSlot.data('available-rooms');

        if (!customerId || !courseId || !bookingDate || !bookingTime || !selectedRoomId) {
            Swal.fire('ข้อมูลไม่ครบถ้วน', 'กรุณาเลือกข้อมูลให้ครบถ้วน', 'warning');
            return;
        }

        // สร้าง bookingDateTime
        const bookingDateTime = formatDateTimeForDatabase(bookingDate, bookingTime);

        // ดึงชื่อลูกค้า
        const customerName = '<?php echo addslashes($customer_name); ?>';
        const customerHN = 'HN-' + customerId.toString().padStart(6, '0');

        // ดึงข้อมูลคอร์สที่เลือก
        const selectedCourse = $('.course-item[data-course-id="' + courseId + '"]');
        const courseName = selectedCourse.data('course-name');
        const coursePrice = selectedCourse.data('course-price');

        // คำนวณเวลาสิ้นสุด
        const startTime = new Date(`2000-01-01T${bookingTime}`);
        const endTime = new Date(startTime.getTime() + intervalMinutes * 60000);
        const startTimeString = startTime.toTimeString().slice(0,5);
        const endTimeString = endTime.toTimeString().slice(0,5);

        const roomName = availableRooms && availableRooms.length > 0 ? availableRooms[0].room_name : 'ไม่ระบุ';

        Swal.fire({
            title: 'ยืนยันการจอง',
            html: `
                <p><strong>ลูกค้า:</strong> ${customerName} </p>
                <p><strong>คอร์ส:</strong>รหัส: C-${courseId.toString().padStart(6, '0')} ${courseName} </p>
                <p><strong>ราคา:</strong> ${coursePrice.toLocaleString()} บาท</p>
                <p><strong>วันที่:</strong> ${bookingDate}</p>
                <p><strong>เวลา:</strong> ${startTimeString} - ${endTimeString}</p>
                <p><strong>ระยะเวลา:</strong> ${intervalMinutes} นาที</p>
                <p><strong>ห้อง:</strong> ${roomName}</p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'sql/save-booking.php',
                    method: 'POST',
                    data: {
                        customer_id: selectedCustomerId,
                        course_id: courseId,
                        booking_datetime: bookingDateTime,
                        course_price: coursePrice,
                        room_id: selectedRoomId
                    },
                    success: function(response) {
                        const result = JSON.parse(response);
                        if (result.success) {
                            Swal.fire('สำเร็จ', 'บันทึกการจองเรียบร้อยแล้ว', 'success')
                            .then(() => {
                                window.location.href = 'booking-show.php';
                            });
                        } else {
                            Swal.fire('เกิดข้อผิดพลาด', result.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถบันทึกการจองได้', 'error');
                    }
                });
            }
        });
    });

});

// ฟังก์ชันสำหรับแปลงรูปแบบวันที่และเวลา
function formatDateTimeForDatabase(date, time) {
    // แปลงวันที่จากรูปแบบ "วัน เดือน ปี" เป็น "YYYY-MM-DD"
    const [day, month, year] = date.split(' ');
    const monthIndex = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 
                        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'].indexOf(month) + 1;
    const formattedDate = `${parseInt(year) - 543}-${monthIndex.toString().padStart(2, '0')}-${day.padStart(2, '0')}`;
    
    // รวมวันที่และเวลา
    return `${formattedDate} ${time}:00`;
}

function showDateTimePicker(response) {
    const availableDates = response.available_dates;

    $('#courseSection').hide();
    $('#dateTimeSection').show();

    // รีเซ็ตค่าใน input และ timeSlots
    $('#booking_date').val('');
    $('#timeSlots').html('');
    $('#booking_time').val('');
    $('#selected_room_id').val('');

    // กำหนดค่าให้กับ flatpickr สำหรับเลือกวันที่
    flatpickr("#booking_date", {
        enable: availableDates,
        dateFormat: "Y-m-d",
        locale: "th",
        onChange: function(selectedDates, dateStr, instance) {
            const thaiDate = formatThaiDate(selectedDates[0]);
            instance.input.value = thaiDate;
            fetchAvailableSlots(dateStr);
        }
    });
}

function fetchAvailableSlots(selectedDate) {
    $.ajax({
        url: 'sql/get-available-slots.php',
        method: 'POST',
        data: { 
            course_id: $('#selected_course_id').val(),
            selected_date: selectedDate
        },
        success: function(response) {
            console.log('Raw response:', response);
            try {
                var data = JSON.parse(response);
                console.log('Parsed data:', data);
                updateTimeSlots(data.available_slots);
            } catch (e) {
                console.error('Error parsing JSON:', e);
                Swal.fire('เกิดข้อผิดพลาด', 'ข้อมูลไม่ถูกต้อง', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', status, error);
            Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถดึงข้อมูลการจองได้', 'error');
        }
    });
}

// ฟังก์ชันสำหรับแปลงวันที่เป็นรูปแบบไทย
function formatThaiDate(date) {
    const thaiMonths = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];
    const day = date.getDate();
    const month = thaiMonths[date.getMonth()];
    const year = date.getFullYear() + 543; // แปลงเป็น พ.ศ.
    return `${day} ${month} ${year}`;
}

function updateTimeSlots(availableSlots) {
    console.log('Available slots:', availableSlots);
    var timeSlotsHtml = '';

    availableSlots.forEach(function(slot) {
        console.log('Processing slot:', slot);
        var buttonClass = 'btn-outline-primary';
        var buttonText = slot.time;
        var roomInfo = '';

        switch(slot.status) {
            case 'fully_booked':
                buttonClass = 'btn-danger disabled';
                roomInfo = 'ไม่ว่าง';
                break;
            case 'partially_booked':
            case 'available':
                buttonClass = slot.status === 'partially_booked' ? 'btn-warning' : 'btn-outline-primary';
                roomInfo = `ว่าง ${slot.available_rooms_count} ห้อง`;
                break;
        }

        timeSlotsHtml += `
            <div class="col-md-3 mb-2">
                <button type="button" class="btn ${buttonClass} time-slot w-100" 
                        data-time="${slot.time}" data-status="${slot.status}" 
                        data-available-rooms='${JSON.stringify(slot.available_rooms)}'
                        data-interval="${slot.interval_minutes}">
                    ${buttonText}<br>${roomInfo}
                </button>
            </div>
        `;
    });

    $('#timeSlots').html(timeSlotsHtml);

    $('.time-slot').on('click', function() {
        $('.time-slot').removeClass('selected');
        $(this).addClass('selected');

        var selectedTime = $(this).data('time');
        var status = $(this).data('status');
        var availableRooms = $(this).data('available-rooms');
        var intervalMinutes = $(this).data('interval');

        console.log('Selected time:', selectedTime);
        console.log('Status:', status);
        console.log('Available rooms:', availableRooms);
        console.log('Interval:', intervalMinutes);

        $('#booking_time').val(selectedTime);

        if (availableRooms && availableRooms.length > 0) {
            $('#selected_room_id').val(availableRooms[0].room_id);
            var roomName = availableRooms[0].room_name;
        } else {
            $('#selected_room_id').val('');
            var roomName = 'ไม่มีห้องว่าง';
        }

        var startTime = new Date(`2000-01-01T${selectedTime}`);
        var endTime = new Date(startTime.getTime() + intervalMinutes * 60000);
        var startTimeString = startTime.toTimeString().slice(0,5);
        var endTimeString = endTime.toTimeString().slice(0,5);

        Swal.fire({
            title: 'ข้อมูลการจอง',
            html: `คุณกำลังจองคอร์ส<br>
                   เวลา: ${startTimeString} - ${endTimeString}<br>
                   ห้อง: ${roomName}<br>
                   ระยะเวลา: ${intervalMinutes} นาที`,
            icon: 'info',
            confirmButtonText: 'เข้าใจแล้ว'
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    let customerSelected = false;
    const submitBtn = document.getElementById('submitBtn');

    $('#customer_select').select2({
        placeholder: 'ค้นหาลูกค้า...',
        allowClear: true,
        minimumInputLength: 2,
        ajax: {
            url: 'sql/search-customers.php',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.id,
                            text: 'HN-' + item.id.padStart(6, '0') + ' : ' + item.text,
                            hn: 'HN-' + item.id.padStart(6, '0'),
                            id_card: item.id_card,
                            email: item.email,
                            tel: item.tel,
                            image: item.image
                        };
                    })
                };
            },
            cache: true
        }
    }).on('select2:select', function(e) {
        var data = e.params.data;
        $('#selected_customer_id').val(data.id);
        customerSelected = true;
        submitBtn.disabled = false;
        
        // Display customer details
        $('#customerImage').attr('src', data.image || '../assets/img/avatars/1.png');
        $('#customerHN').text(data.hn);
        $('#customerName').text(data.text.split(' : ')[1]);
        $('#customerIdCard').text(data.id_card);
        $('#customerEmail').text(data.email);
        $('#customerTel').text(data.tel);
        $('#customerDetails').fadeIn();
    }).on('select2:unselect', function() {
        $('#selected_customer_id').val('');
        customerSelected = false;
        submitBtn.disabled = true;
        $('#customerDetails').fadeOut();
    });
});




</script>
</body>
</html>