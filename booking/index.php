<?php
session_start();
// include 'chk-session.php';
require '../dbcon.php';



$_SESSION['cus_id']=1;
// ดึงข้อมูลวันที่ปิดทำการ
$sql_closures = "SELECT closure_date FROM clinic_closures";
$result_closures = $conn->query($sql_closures);
$closed_dates = [];
while ($row = $result_closures->fetch_assoc()) {
    $closed_dates[] = $row['closure_date'];
}

// ดึงข้อมูลเวลาทำการ
$sql_hours = "SELECT * FROM clinic_hours";
$result_hours = $conn->query($sql_hours);
$clinic_hours = [];
while ($row = $result_hours->fetch_assoc()) {
    $clinic_hours[$row['day_of_week']] = $row;
}

// ดึงข้อมูลการจองที่มีอยู่
$sql_bookings = "SELECT booking_datetime FROM course_bookings WHERE status = 'confirmed'";
$result_bookings = $conn->query($sql_bookings);
$booked_slots = [];
while ($row = $result_bookings->fetch_assoc()) {
    $booked_slots[] = $row['booking_datetime'];
}

// ดึงข้อมูลคอร์ส
$sql_courses = "SELECT * FROM course WHERE course_status = 1";
$result_courses = $conn->query($sql_courses);

// ฟังก์ชันสำหรับบันทึกการจอง
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];
    $cus_id = $_SESSION['cus_id']; // สมมติว่ามี session ของลูกค้า
    $booking_datetime = $_POST['booking_date'] . ' ' . $_POST['booking_time'];

    $sql_insert = "INSERT INTO course_bookings (course_id, cus_id, booking_datetime, status) VALUES (?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("iis", $course_id, $cus_id, $booking_datetime);

    if ($stmt->execute()) {
        $_SESSION['msg_ok'] = "จองคอร์สสำเร็จ รอการยืนยันจากเจ้าหน้าที่";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการจองคอร์ส";
    }
    $stmt->close();
    header("Location: course-bookings-users.php");
    exit();
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
    <style>
        .time-slot {
            cursor: pointer;
        }
        .time-slot.booked {
            background-color: #f8d7da;
            cursor: not-allowed;
        }
        .time-slot.selected {
            background-color: #d4edda;
        }
    </style>

    <!-- Helpers -->
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
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <h5 class="card-header">จองคอร์ส</h5>
                                    <div class="card-body">
                                        <form id="bookingForm" method="POST">
                                            <div class="mb-3">
                                                <label for="course_id" class="form-label">เลือกคอร์ส</label>
                                                <select class="form-select" id="course_id" name="course_id" required>
                                                    <option value="">เลือกคอร์ส</option>
                                                    <?php while($course = $result_courses->fetch_assoc()): ?>
                                                        <option value="<?php echo $course['course_id']; ?>"><?php echo $course['course_name']; ?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="booking_date" class="form-label">เลือกวันที่</label>
                                                <input type="text" class="form-control" id="booking_date" name="booking_date" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">เลือกเวลา</label>
                                                <div id="timeSlots" class="row">
                                                    <!-- เวลาจะถูกเพิ่มด้วย JavaScript -->
                                                </div>
                                            </div>
                                            <input type="hidden" id="booking_time" name="booking_time">
                                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>จองคอร์ส</button>
                                        </form>
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
    <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const closedDates = <?php echo json_encode($closed_dates); ?>;
        const clinicHours = <?php echo json_encode($clinic_hours); ?>;
        const bookedSlots = <?php echo json_encode($booked_slots); ?>;

        flatpickr("#booking_date", {
            minDate: "today",
            disable: closedDates,
            onChange: function(selectedDates, dateStr, instance) {
                updateTimeSlots(dateStr);
            }
        });

        function updateTimeSlots(dateStr) {
            const dayOfWeek = new Date(dateStr).toLocaleString('en-us', {weekday:'long'});
            const hours = clinicHours[dayOfWeek];
            const timeSlotsContainer = document.getElementById('timeSlots');
            timeSlotsContainer.innerHTML = '';

            if (hours && hours.is_closed != 1) {
                const startTime = new Date(`2000-01-01T${hours.start_time}`);
                const endTime = new Date(`2000-01-01T${hours.end_time}`);

                while (startTime < endTime) {
                    const timeStr = startTime.toTimeString().slice(0, 5);
                    const isBooked = bookedSlots.includes(`${dateStr} ${timeStr}:00`);
                    const slot = document.createElement('div');
                    slot.className = `col-md-3 mb-2`;
                    slot.innerHTML = `<div class="time-slot btn btn-outline-primary ${isBooked ? 'booked' : ''}" data-time="${timeStr}">${timeStr}</div>`;
                    timeSlotsContainer.appendChild(slot);

                    startTime.setHours(startTime.getHours() + 1);
                }

                // เพิ่ม event listener สำหรับ time slots
                document.querySelectorAll('.time-slot:not(.booked)').forEach(slot => {
                    slot.addEventListener('click', function() {
                        document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                        this.classList.add('selected');
                        document.getElementById('booking_time').value = this.dataset.time;
                        document.getElementById('submitBtn').disabled = false;
                    });
                });
            } else {
                timeSlotsContainer.innerHTML = '<p>ไม่มีเวลาทำการในวันที่เลือก</p>';
            }
        }
    });
    </script>
</body>
</html>