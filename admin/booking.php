<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

$branch_id=$_SESSION['branch_id'];

// ดึงข้อมูลวันที่ปิดทำการ
$sql_closures = "SELECT closure_date FROM clinic_closures WHERE branch_id='$branch_id'";
$result_closures = $conn->query($sql_closures);
$closed_dates = [];
while ($row = $result_closures->fetch_object()) {
    if ($row->closure_date) {
        $closed_date = date('Y-m-d', strtotime($row->closure_date));
        if ($closed_date) {
            $closed_dates[] = $closed_date;
        } else {
            error_log("Invalid closure date format: " . $row->closure_date);
        }
    }
}

// ดึงข้อมูลเวลาทำการ
$sql_hours = "SELECT * FROM clinic_hours WHERE branch_id='$branch_id'";
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
echo "<script>const clinicHours = " . json_encode($clinic_hours) . ";</script>";
echo "<script>const closedDays = " . json_encode($closed_days) . ";</script>";



// ดึงข้อมูลการจองที่มีอยู่
$sql_bookings = "SELECT booking_datetime FROM course_bookings WHERE status IN ('pending', 'confirmed') and branch_id='$branch_id'";
$result_bookings = $conn->query($sql_bookings);
$booked_slots = [];
while ($row = $result_bookings->fetch_object()) {
    if ($row->booking_datetime) {
        $booked_slots[] = $row->booking_datetime;
    }
}

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

$booking_datetime = null;
$search_term = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['select_datetime']) && $_POST['select_datetime'] == 1) {
        $booking_datetime = $_POST['booking_date'] . ' ' . $_POST['booking_time'] . ':00';
        $selected_customer_id = $_POST['customer_select'];

    }
    
    if (isset($_POST['search_course'])) {
        $search_term = $_POST['search_course'];
        $booking_datetime = $_POST['booking_date'] . ' ' . $_POST['booking_time'] . ':00';
        $selected_customer_id = $_POST['customer_select'];
    }

    if (isset($_POST['booking']) && $_POST['booking'] == 1) {
        $users_id=$_SESSION['users_id'];
        $branch_id=$_SESSION['branch_id'];
        $course_id = $_POST['course_id'];
        $booking_datetime = $_POST['booking_date'] . ' ' . $_POST['booking_time'] . ':00';
        // สร้างวัตถุ DateTime จากสตริงวันที่และเวลาในรูปแบบ พ.ศ.
        $datetime_obj = DateTime::createFromFormat('d/m/Y H:i:s', $booking_datetime);

        if ($datetime_obj) {
            // แปลงจาก พ.ศ. เป็น ค.ศ.
            $datetime_obj->modify('-543 years');

            // จัดรูปแบบวันที่และเวลาให้อยู่ในรูปแบบที่เหมาะสมสำหรับฐานข้อมูล
            $booking_datetime = $datetime_obj->format('Y-m-d H:i:s');

            echo $booking_datetime; // ผลลัพธ์: 2024-08-27 16:00:00
        } else {
            echo "รูปแบบวันที่และเวลาไม่ถูกต้อง";
        }

        $selected_customer_id = $_POST['customer_select'];
        if (empty($selected_customer_id)) {
            $_SESSION['msg_error'] = "กรุณาเลือกลูกค้า";
            header("Location: booking.php");
            exit();
        }


        // สร้างคำสั่ง SQL โดยใช้ mysqli_real_escape_string
        $sql_insert = "INSERT INTO course_bookings (branch_id, cus_id, booking_datetime, users_id, status) 
                        VALUES ('$branch_id', '$selected_customer_id', '$booking_datetime', $users_id, 'confirmed')";

        // ดำเนินการ query
        if (mysqli_query($conn, $sql_insert)) {
            $_SESSION['msg_ok'] = "จองคอร์สสำเร็จ";
            header("Location: booking-show.php");
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการจองคอร์ส: " . mysqli_error($conn);
            header("Location: booking.php");
        }
        exit();
    }
}

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
                        <div class="row">
                            <!-- booking datetime -->
                            <?php if(!isset($booking_datetime)){ ?>


                            <form id="bookingForm" method="POST">
                                <div class="card mb-4">
                                <div class="text-end m-5"><a href="booking-show.php" class="btn btn-danger">ข้อมูลการจอง</a></div>

                                <div class="row">
                                    <div class="offset-md-2 col-md-4">
                                        
                                            <h5 class="card-header">เลือกลูกค้า</h5>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="customer_select" class="form-label">ค้นหาลูกค้า</label>
                                                    <select id="customer_select" name="customer_select" class="form-select select2" style="width: 100%;">
                                                        <option value="">เลือกลูกค้า</option>
                                                        <?php foreach ($customers as $customer): ?>
                                                            <option value="<?php echo $customer['cus_id']; ?>"><?php echo $customer['cus_id_card_number'] . ' - ' . $customer['cus_firstname'] . ' ' . $customer['cus_lastname']; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div id="customerDetails" class="mt-4" style="display: none;">
                                                    <div class="text-center mb-3">
                                                        <img id="customerImage" src="" alt="รูปลูกค้า" class="img-fluid rounded-circle" style="max-width: 150px; max-height: 150px;">
                                                    </div>
                                                    <table class="table table-borderless">
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
                                    <div class="col-md-4">
                                        
                                            <h5 class="card-header">โปรดเลือกวันที่</h5>
                                            <div class="card-body">

                                                <div class="mb-3">
                                                    <label for="booking_date" class="form-label">เลือกวันที่</label>
                                                    <input type="text" class="form-control" id="booking_date" name="booking_date" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">เลือกเวลา</label>
                                                    <div id="timeSlots" class="row text-center">
                                                    
                                                    </div>
                                                </div>
                                                <input type="hidden" id="booking_time" name="booking_time">
                                                <input type="hidden" id="selected_customer_id" name="selected_customer_id">
                                                <input type="hidden" name="select_datetime" value="1">
                                            </div>
                                        </div>
                                        <div class="d-grid gap-2 col-6 mx-auto mb-5">
                                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>ถัดไป</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <?php } ?>
                            <?php if(isset($booking_datetime)){ ?>
                            <?php $sql_cus="SELECT * FROM `customer` WHERE cus_id = '$selected_customer_id'";
                                  $result_cus=$conn->query($sql_cus);
                                  $row_cus=mysqli_fetch_object($result_cus);
                             ?>
                            <div class="row">
                                <div class="text-center">
                                    <h3 class="text-danger">วันที่จอง : <?php echo $booking_datetime; ?></h3>
                                    <h4>ลูกค้า : <?php echo $row_cus->cus_firstname." ".$row_cus->cus_lastname; ?></h3>
                                </div>
                                <div class="col-md-4">
                                <div class="card border-2 border-primary">
                                    <div class="card-header bg-primary text-center ">
                                        <h4 class="text-white mt-3">รายการสั่งซื้อ</h4>
                                    </div>
                                    <div class="card-body">
                                        <br>
                                        <div id="orderList">
                                            <!-- รายการคอร์สที่เลือกจะแสดงที่นี่ -->
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <strong>ราคารวม:</strong>
                                            <span id="totalPrice">0</span> บาท
                                        </div>
                                        <form id="orderForm" method="POST" action="sql/process-order.php" class="mt-3">
                                            <input type="hidden" name="customer_id" value="<?php echo $selected_customer_id; ?>">
                                            <input type="hidden" name="booking_datetime" value="<?php echo $booking_datetime; ?>">
                                            <input type="hidden" name="selected_courses" id="selectedCoursesInput">
                                            <div class="mb-3">
                                                <label for="payment_method" class="form-label">วิธีการชำระเงิน</label>
                                                <select class="form-select" id="payment_method" name="payment_method" required>
                                                    <option value="">เลือกวิธีการชำระเงิน</option>
                                                    <option value="ยังไม่จ่ายเงิน">ยังไม่จ่ายเงิน</option>
                                                    <option value="เงินสด">เงินสด</option>
                                                    <option value="บัตรเครดิต">บัตรเครดิต</option>
                                                    <option value="เงินโอน">เงินโอน</option>
                                                </select>
                                            </div>
                                            <input type="hidden" name="booking_datetime" value="<?= $booking_datetime; ?>">
                                            <input type="hidden" name="customer_select" value="<?= $selected_customer_id; ?>">
                                            <input type="hidden" name="booking_date" value="<?= $_POST['booking_date'] ?>">
                                            <input type="hidden" name="booking_time" value="<?= $_POST['booking_time'] ?>">
                                            <button type="submit" class="btn btn-primary" form="orderForm">บันทึกคำสั่งซื้อ</button>
                                        </form>
                                    </div>
                                </div>
                                </div>
                                <div class="col-md-8">
                            <div id="courseSection">
                                <form method="POST" action="">
                                    <div class="mb-3">

                                        <div class="text-end">
                                            <a href="booking.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> ย้อนกลับ</a>
                                            
                                        </div>
                                        <label for="courseSearch" class="form-label">ค้นหาวันที่</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="courseSearch" name="search_course" placeholder="พิมพ์ชื่อคอร์ส..." value="<?= htmlspecialchars($search_term) ?>">
                                            <button type="submit" class="btn btn-primary">ค้นหา</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="booking_date" value="<?= $_POST['booking_date'] ?>">
                                    <input type="hidden" name="booking_time" value="<?= $_POST['booking_time'] ?>">
                                    <input type="hidden" name="customer_select" value="<?= $selected_customer_id; ?>">
                                    <input type="hidden" name="select_datetime" value="1">
                                </form>
                                <div id="courseList" class="row">
                                    <?php if (empty($display_courses)): ?>
                                        <div class="col-12">
                                            <p>ไม่พบคอร์สที่ตรงกับการค้นหา</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($display_courses as $course): ?>
                                            <div class="col-md-3 mb-3">
                                                <div class="card course-item" data-course-id="<?= $course['id'] ?>" data-course-name="<?= htmlspecialchars($course['name']) ?>" data-course-price="<?= $course['price'] ?>">
                                                    <img src="../img/course/<?= htmlspecialchars($course['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($course['name']) ?>">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?= htmlspecialchars($course['name']) ?></h5>
                                                        <p class="card-text">ราคา: <?= number_format($course['price']) ?> บาท</p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        </div>
                                     </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php } ?>
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
<script src="../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

<!-- Main JS -->
<script src="../assets/js/main.js"></script>

<!-- Page JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>

<script>

$(document).ready(function() {
let selectedCourses = [];
const $submitButton = $('button[type="submit"][form="orderForm"]');

function updateSubmitButton() {
    $submitButton.prop('disabled', selectedCourses.length === 0);
}

$('.course-item').on('click', function() {
    const courseId = $(this).data('course-id');
    const courseName = $(this).data('course-name');
    const coursePrice = $(this).data('course-price');

    if($(this).hasClass('selected')) {
        $(this).removeClass('selected');
        selectedCourses = selectedCourses.filter(course => course.id !== courseId);
    } else {
        $(this).addClass('selected');
        selectedCourses.push({id: courseId, name: courseName, price: coursePrice});
    }

    updateOrderList();
    updateSubmitButton();
});

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
updateSubmitButton();
});

document.addEventListener('DOMContentLoaded', function() {
    let customerSelected = false;
    let timeSelected = false;

    function checkSubmitButton() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = !(customerSelected && timeSelected);
    }

    // Initialize Select2 for customer search
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
                    results: data
                };
            },
            cache: true
        }
    }).on('select2:select', function(e) {
        var data = e.params.data;
        document.getElementById('selected_customer_id').value = data.id;
        customerSelected = true;
        checkSubmitButton();
        
        // Display customer details
        $('#customerImage').attr('src', data.image);
        $('#customerName').text(data.text);
        $('#customerIdCard').text(data.id_card);
        $('#customerEmail').text(data.email);
        $('#customerTel').text(data.tel);
        $('#customerDetails').fadeIn();
    }).on('select2:unselect', function() {
        document.getElementById('selected_customer_id').value = '';
        customerSelected = false;
        checkSubmitButton();
        $('#customerDetails').fadeOut();
    });

    // Initialize Flatpickr for date selection
    flatpickr.localize(flatpickr.l10ns.th);
    const closedDates = <?php echo json_encode($closed_dates); ?>;
    const clinicHours = <?php echo json_encode($clinic_hours); ?>;
    const bookedSlots = <?php echo json_encode($booked_slots); ?>;
    const closedDays = <?php echo json_encode($closed_days); ?>;
    // console.log(closedDates);
    const today = new Date();
    const oneMonthLater = new Date(today);
    oneMonthLater.setMonth(oneMonthLater.getMonth() + 1);

    flatpickr("#booking_date", {
        minDate: "today",
        maxDate: new Date().fp_incr(30), // 30 days from now
        // console.log("Closed dates:", closedDates),
        disable: [
            function(date) {
                // ตรวจสอบวันที่ปิดที่กำหนดไว้
                const isClosedDate = closedDates.some(closedDate => {
                    const closed = new Date(closedDate);
                    return date.getFullYear() === closed.getFullYear() &&
                           date.getMonth() === closed.getMonth() &&
                           date.getDate() === closed.getDate();
                });

                // ตรวจสอบวันในสัปดาห์ที่ปิด
                const dayOfWeek = date.toLocaleString('en-us', {weekday: 'long'});
                const isClosedDay = closedDays.includes(dayOfWeek);

                // คืนค่า true ถ้าเป็นวันที่ปิดหรือวันในสัปดาห์ที่ปิด
                return isClosedDate || isClosedDay;
            }
        ],
        dateFormat: "d/m/Y",
        locale: {
            ...flatpickr.l10ns.th,
            reformatAfterEdit: true,
        },
        onReady: function(selectedDates, dateStr, instance) {
            instance.currentYearElement.textContent = parseInt(instance.currentYearElement.textContent) + 543;
        },
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                const thaiDate = formatThaiDate(selectedDates[0]);
                instance.input.value = thaiDate;
                updateTimeSlots(thaiDate);
                timeSelected = false;
                checkSubmitButton();
            }
        },
        onYearChange: function(selectedDates, dateStr, instance) {
            setTimeout(function() {
                let yearElem = instance.currentYearElement;
                yearElem.textContent = parseInt(yearElem.textContent) + 543;
            }, 0);
        },
        formatDate: function(date, format) {
            return formatThaiDate(date);
        },
        parseDate: function(datestr, format) {
            if (!datestr) return undefined;
            const parts = datestr.split('/');
            if (parts.length !== 3) return undefined;
            const thaiYear = parseInt(parts[2], 10);
            const month = parseInt(parts[1], 10) - 1;
            const day = parseInt(parts[0], 10);
            return new Date(thaiYear - 543, month, day);
        }
    });

    // Function to update time slots
    function updateTimeSlots(dateStr) {
        const [day, month, year] = dateStr.split('/');
        const selectedDate = new Date(year - 543, month - 1, day);
        const dayOfWeek = selectedDate.toLocaleString('en-us', {weekday:'long'});
        console.log("วันที่เลือก:", dayOfWeek);

        const hours = clinicHours[dayOfWeek];
        console.log("เวลาทำการของวันที่เลือก:", hours);

        const timeSlotsContainer = document.getElementById('timeSlots');
        timeSlotsContainer.innerHTML = '';

        if (hours && hours.is_closed != 1) {
            const startTime = new Date(`2000-01-01T${hours.start_time}`);
            const endTime = new Date(`2000-01-01T${hours.end_time}`);

            const now = new Date();
            const isToday = selectedDate.toDateString() === now.toDateString();

            while (startTime < endTime) {
                const timeStr = startTime.toTimeString().slice(0, 5);
                const fullDateStr = `${year - 543}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                const fullDateTimeStr = `${fullDateStr} ${timeStr}:00`;
                const isBooked = bookedSlots.includes(fullDateTimeStr);
                
                const slotDateTime = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate(), startTime.getHours(), startTime.getMinutes());
                const isPastTime = isToday && slotDateTime < now;

                const slot = document.createElement('div');
                slot.className = `col-4 col-sm-3 mb-2`;
                
                if (isPastTime) {
                    slot.innerHTML = `<div class="time-slot btn btn-outline-secondary disabled" data-time="${timeStr}">${timeStr}</div>`;
                } else if (isBooked) {
                    slot.innerHTML = `<div class="time-slot btn btn-outline-danger disabled" data-time="${timeStr}">${timeStr}</div>`;
                } else {
                    slot.innerHTML = `<div class="time-slot btn btn-outline-primary" data-time="${timeStr}">${timeStr}</div>`;
                }
                
                timeSlotsContainer.appendChild(slot);
                startTime.setMinutes(startTime.getMinutes() + 15);
            }

            document.querySelectorAll('.time-slot:not(.disabled)').forEach(slot => {
                slot.addEventListener('click', function() {
                    document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                    this.classList.add('selected');
                    document.getElementById('booking_time').value = this.dataset.time;
                    timeSelected = true;
                    checkSubmitButton();
                });
            });
        } else {
            console.log("ไม่มีเวลาทำการหรือคลินิกปิดในวันที่เลือก");
            timeSlotsContainer.innerHTML = '<p>ไม่มีเวลาทำการในวันที่เลือก</p>';
        }
    }

    // Helper function to format date in Thai format
    function formatThaiDate(date) {
        const thaiYear = date.getFullYear() + 543;
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        return `${day}/${month}/${thaiYear}`;
    }

    // Handle course search form submission
    // const searchForm = document.querySelector('form');
    // const searchInput = document.getElementById('courseSearch');
    // if (searchForm && searchInput) {
    //     searchForm.addEventListener('submit', function(e) {
    //         if (searchInput.value.trim() === '') {
    //             e.preventDefault();
    //             alert('กรุณากรอกคำค้นหา');
    //         }
    //     });
    // }
});


// Display success message
      <?php if(isset($_SESSION['msg_ok'])){ ?>
        Swal.fire({
          icon: 'success',
          title: 'แจ้งเตือน!',
          text: '<?php echo $_SESSION['msg_ok']; ?>',
          customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light'
          },
          buttonsStyling: false
        });
      <?php unset($_SESSION['msg_ok']); } ?>

      // Display error message
      <?php if(isset($_SESSION['msg_error'])){ ?>
        Swal.fire({
          icon: 'error',
          title: 'แจ้งเตือน!',
          text: '<?php echo $_SESSION['msg_error']; ?>',
          customClass: {
            confirmButton: 'btn btn-danger waves-effect waves-light'
          },
          buttonsStyling: false
        });
      <?php unset($_SESSION['msg_error']); } ?>
</script>
</body>
</html>