<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['users_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user_id = $_SESSION['users_id'];
$course_id = $_POST['courseId'];
$booking_date = $_POST['bookingDate'];
$booking_time = $_POST['bookingTime'];
$payment_method = $_POST['paymentMethod'];

// Validate inputs
if (empty($course_id) || empty($booking_date) || empty($booking_time) || empty($payment_method)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// แปลงวันที่จาก พ.ศ. เป็น ค.ศ.
$date_parts = explode('/', $booking_date);
$thai_year = intval($date_parts[2]);
$western_year = $thai_year - 543;
$booking_date = "{$western_year}-{$date_parts[1]}-{$date_parts[0]}";

// Combine date and time
$booking_datetime = date('Y-m-d H:i:s', strtotime("$booking_date $booking_time"));

// Check if the selected date is a clinic closure day
$closure_check = mysqli_query($conn, "SELECT 1 FROM clinic_closures WHERE closure_date = '$booking_date'");
if (mysqli_num_rows($closure_check) > 0) {
    echo json_encode(['success' => false, 'message' => 'Selected date is a clinic closure day']);
    exit;
}

// Check if the selected day is a closed day in clinic_hours
$day_of_week = date('l', strtotime($booking_date));
$hours_check = mysqli_query($conn, "SELECT 1 FROM clinic_hours WHERE day_of_week = '$day_of_week' AND is_closed = 1");
if (mysqli_num_rows($hours_check) > 0) {
    echo json_encode(['success' => false, 'message' => 'Selected day is a clinic closed day']);
    exit;
}

// Check for existing bookings
$existing_booking_check = mysqli_query($conn, "SELECT 1 FROM course_bookings WHERE booking_datetime = '$booking_datetime' AND status IN ('pending', 'confirmed')");
if (mysqli_num_rows($existing_booking_check) > 0) {
    echo json_encode(['success' => false, 'message' => 'Selected time slot is already booked']);
    exit;
}

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Insert into course_bookings
    $booking_query = "INSERT INTO course_bookings (branch_id, cus_id, booking_datetime, created_at, users_id, status) 
                      VALUES (1, ?, ?, NOW(), ?, 'pending')";
    $stmt = mysqli_prepare($conn, $booking_query);
    mysqli_stmt_bind_param($stmt, "isi", $user_id, $booking_datetime, $user_id);
    mysqli_stmt_execute($stmt);
    $booking_id = mysqli_insert_id($conn);

    // Insert into order_course
    $order_query = "INSERT INTO order_course (cus_id, users_id, course_bookings_id, order_datetime, order_payment, order_status) 
                    VALUES (?, ?, ?, NOW(), ?, 1)";
    $stmt = mysqli_prepare($conn, $order_query);
    mysqli_stmt_bind_param($stmt, "iiis", $user_id, $user_id, $booking_id, $payment_method);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);

    // Get course price
    $price_query = "SELECT course_price FROM course WHERE course_id = ?";
    $stmt = mysqli_prepare($conn, $price_query);
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $course = mysqli_fetch_assoc($result);

    // Insert into order_detail
    $detail_query = "INSERT INTO order_detail (oc_id, course_id, od_amount, od_price) 
                     VALUES (?, ?, 1, ?)";
    $stmt = mysqli_prepare($conn, $detail_query);
    mysqli_stmt_bind_param($stmt, "iid", $order_id, $course_id, $course['course_price']);
    mysqli_stmt_execute($stmt);

    // Handle file upload if payment method is transfer
    if ($payment_method === 'transfer' && isset($_FILES['paymentProof'])) {
        $file_name = $_FILES['paymentProof']['name'];
        $file_tmp = $_FILES['paymentProof']['tmp_name'];
        $file_destination = "../uploads/payment_proofs/" . $file_name;

        if (move_uploaded_file($file_tmp, $file_destination)) {
            // Update order with payment proof
            $update_query = "UPDATE order_course SET payment_proof = ? WHERE oc_id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "si", $file_name, $order_id);
            mysqli_stmt_execute($stmt);
        } else {
            throw new Exception("Failed to upload payment proof");
        }
    }

    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Booking successfully processed']);
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>