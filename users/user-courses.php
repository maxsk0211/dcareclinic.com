<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['users_id'])) {
    header('Location: ../login.php');
    exit;
}

$current_date = date('Y-m-d');
$search_term = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Fetch available courses
$available_query = "
    SELECT * FROM course 
    WHERE course_status = 1 
    AND course_start <= '$current_date' 
    AND course_end >= '$current_date'
    AND (course_name LIKE '%$search_term%' OR course_detail LIKE '%$search_term%')
    AND course_id NOT IN (
        SELECT od.course_id 
        FROM order_detail od 
        JOIN order_course oc ON od.oc_id = oc.oc_id 
        WHERE oc.cus_id = {$_SESSION['users_id']}
    )
";
$available_result = mysqli_query($conn, $available_query);

// Fetch clinic closure dates
$sql_closures = "SELECT closure_date FROM clinic_closures";
$result_closures = $conn->query($sql_closures);
$closed_dates = [];
while ($row = $result_closures->fetch_object()) {
    if ($row->closure_date) {
        $date = new DateTime($row->closure_date);
        $thaiYear = $date->format('Y') + 543;
        $closed_dates[] = $thaiYear . '-' . $date->format('m-d');
    }
}
error_log("Closed dates in Thai year: " . json_encode($closed_dates));

// Fetch clinic hours
$sql_hours = "SELECT * FROM clinic_hours";
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
$result_bookings = $conn->query($sql_bookings);
$booked_slots = [];
while ($row = $result_bookings->fetch_object()) {
    if ($row->booking_datetime) {
        $booked_slots[] = $row->booking_datetime;
    }
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
        .course-card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            height: 100%;
            cursor: pointer;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .course-image {
            height: 200px;
            object-fit: cover;
        }
        .course-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .course-detail {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        .course-meta {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .course-price {
            font-weight: bold;
            color: #28a745;
        }
        .btn-enroll {
            width: 100%;
            margin-top: 1rem;
        }
        .search-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 2rem;
        }
        .section-title {
            border-left: 4px solid #696cff;
            padding-left: 10px;
            margin-bottom: 1.5rem;
        }
        .card-click-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }
        .card-body {
            position: relative;
        }
        .btn-enroll {
            position: relative;
            z-index: 2;
        }

    .course-card {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        height: 100%;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .course-image {
        height: 200px;
        object-fit: cover;
    }
    .course-title {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    .course-detail {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 1rem;
    }
    .course-meta {
        font-size: 0.8rem;
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
        margin-bottom: 10px;
    }
    .btn-enroll {
        width: 100%;
        margin-top: 1rem;
    }
    .card-body {
        padding-top: 2rem;
    }
            .course-card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            height: 100%;
            cursor: pointer;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .course-image {
            height: 200px;
            object-fit: cover;
        }
        .course-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .course-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 1rem;
        }
        .booking-modal .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        #timeSlots {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
        }
        .time-slot {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
        }
        .time-slot.selected {
            background-color: #28a745;
            color: white;
        }
        .time-slot.booked {
            background-color: #dc3545;
            color: white;
            cursor: not-allowed;
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
                        <h4 class="fw-bold py-3 mb-4">Available Courses</h4>

                        <!-- Search Form -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <form action="" method="GET" class="row g-3">
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" id="search" name="search" placeholder="Search for courses" value="<?php echo htmlspecialchars($search_term); ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">Search</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Available Courses -->
                        <div class="row">
                            <?php
                            $available_count = 0;
                            while ($course = mysqli_fetch_object($available_result)):
                                $available_count++;
                            ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 course-card">
                                        <img class="card-img-top course-image" src="../img/course/<?php echo htmlspecialchars($course->course_pic); ?>" alt="<?php echo htmlspecialchars($course->course_name); ?>">
                                        <div class="card-body">
                                            <h5 class="course-title"><?php echo htmlspecialchars($course->course_name); ?></h5>
                                            <p class="course-price"><?php echo number_format($course->course_price, 2); ?> THB</p>
                                            <p class="card-text"><?php echo htmlspecialchars(substr($course->course_detail, 0, 100)) . '...'; ?></p>
                                            <p class="course-meta">
                                                <i class="mdi mdi-calendar"></i> Start: <?php echo date('M j, Y', strtotime($course->course_start)); ?><br>
                                                <i class="mdi mdi-calendar-clock"></i> End: <?php echo date('M j, Y', strtotime($course->course_end)); ?>
                                            </p>
                                            <button class="btn btn-primary book-course" data-course-id="<?php echo $course->course_id; ?>">Book Now</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                            <?php if ($available_count == 0): ?>
                                <div class="col-12">
                                    <p class="text-muted">No available courses found matching your search.</p>
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

    <!-- Booking Modal -->
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Book Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bookingForm" method="POST">
                        <input type="hidden" id="courseId" name="courseId">
                        <div class="mb-3">
                            <label for="booking_date" class="form-label">Select Date</label>
                            <input type="text" class="form-control" id="booking_date" name="booking_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select Time</label>
                            <div id="timeSlots" class="row text-center"></div>
                        </div>
                        <input type="hidden" id="booking_time" name="booking_time">
                        <div class="mb-3">
                            <label for="paymentMethod" class="form-label">Payment Method</label>
                            <select class="form-select" id="paymentMethod" name="paymentMethod" required>
                                <option value="">Select payment method</option>
                                <option value="cash">Cash</option>
                                <option value="transfer">Bank Transfer</option>
                                <option value="credit_card">Credit Card</option>
                            </select>
                        </div>
                        <div id="paymentProofUpload" class="mb-3" style="display: none;">
                            <label for="paymentProof" class="form-label">Upload Payment Proof</label>
                            <input type="file" class="form-control" id="paymentProof" name="paymentProof">
                        </div>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Confirm Booking</button>
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


    <script>
document.addEventListener('DOMContentLoaded', function() {
    const clinicHours = <?php echo json_encode($clinic_hours); ?>;
    const closedDates = <?php echo json_encode($closed_dates); ?>;
    const bookedSlots = <?php echo json_encode($booked_slots); ?>;
    const closedDays = <?php echo json_encode($closed_days); ?>;



    let timeSelected = false;

    function checkSubmitButton() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = !timeSelected;
    }

    // แปลงวันที่ปิดทำการให้อยู่ในรูปแบบ YYYY-MM-DD
    const formattedClosedDates = closedDates.map(date => {
        const [year, month, day] = date.split('-');
        return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
    });

    // Initialize Flatpickr for date selection
    flatpickr.localize(flatpickr.l10ns.th);
    flatpickr("#booking_date", {
        minDate: "today",
        maxDate: new Date().fp_incr(30),
        disable: [
            function(date) {
                // แปลงวันที่ที่กำลังตรวจสอบให้อยู่ในรูปแบบ YYYY-MM-DD
                const thaiYear = date.getFullYear() + 543;
                const checkDate = `${thaiYear}-${(date.getMonth() + 1).toString().padStart(2, '0')}-${date.getDate().toString().padStart(2, '0')}`;
                console.log("Checking Thai date:", checkDate);
                
                // ตรวจสอบว่าวันที่อยู่ในรายการวันที่ปิดทำการหรือไม่
                if (closedDates.includes(checkDate)) {
                    console.log("Date is closed:", checkDate);
                    return true;
                }
                
                // ตรวจสอบวันในสัปดาห์
                const dayOfWeek = date.toLocaleString('en-us', {weekday: 'long'});
            return closedDays.includes(dayOfWeek);
            }
        ],
        dateFormat: "d/m/Y",
        locale: {
            ...flatpickr.l10ns.th,
            firstDayOfWeek: 1
            
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

    function updateTimeSlots(dateStr) {
        const [day, month, year] = dateStr.split('/');
        const selectedDate = new Date(year - 543, month - 1, day);
        const dayOfWeek = selectedDate.toLocaleString('en-us', {weekday:'long'});

        const hours = clinicHours[dayOfWeek];
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
                startTime.setMinutes(startTime.getMinutes() + 30);
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
            timeSlotsContainer.innerHTML = '<p>ไม่มีเวลาทำการในวันที่เลือก</p>';
        }
    }

    function formatThaiDate(date) {
        const thaiYear = date.getFullYear() + 543;
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        return `${day}/${month}/${thaiYear}`;
    }

    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('process-booking.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('การจองสำเร็จ!');
                $('#bookingModal').modal('hide');
                location.reload();
            } else {
                alert('เกิดข้อผิดพลาด: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('เกิดข้อผิดพลาดในการประมวลผลการจอง');
        });
    });

    document.getElementById('paymentMethod').addEventListener('change', function() {
        const paymentProofUpload = document.getElementById('paymentProofUpload');
        if (this.value === 'transfer') {
            paymentProofUpload.style.display = 'block';
        } else {
            paymentProofUpload.style.display = 'none';
        }
    });

    document.querySelectorAll('.book-course').forEach(button => {
        button.addEventListener('click', function() {
            const courseId = this.getAttribute('data-course-id');
            document.getElementById('courseId').value = courseId;
            $('#bookingModal').modal('show');
        });
    });
});
</script>
  </body>
</html>
