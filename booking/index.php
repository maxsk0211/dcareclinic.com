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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จองคอร์ส - D Care Clinic</title>
    <!-- เพิ่ม CSS ที่จำเป็น -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">จองคอร์ส</h2>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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