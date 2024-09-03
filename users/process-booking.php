<?php
session_start();
require_once '../dbcon.php';

// Function to log errors
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, '../error.log');
}

// Function to generate new file name
function generateNewFileName($originalFileName) {
    $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
    $dateTime = date('Ymd-His');
    $randomString = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 10);
    return $dateTime . '-' . $randomString . '.' . $extension;
}

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
$booking_date = $_POST['booking_date'];
$booking_time = $_POST['booking_time'];
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
$booking_date = sprintf('%04d-%02d-%02d', $western_year, $date_parts[1], $date_parts[0]);

// Combine date and time
$booking_datetime = $booking_date . ' ' . $booking_time . ':00';

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Check if the selected date is a clinic closure day
    $closure_check = mysqli_query($conn, "SELECT 1 FROM clinic_closures WHERE closure_date = '$booking_date'");
    if (mysqli_num_rows($closure_check) > 0) {
        throw new Exception('Selected date is a clinic closure day');
    }

    // Check if the selected day is a closed day in clinic_hours
    $day_of_week = date('l', strtotime($booking_date));
    $hours_check = mysqli_query($conn, "SELECT 1 FROM clinic_hours WHERE day_of_week = '$day_of_week' AND is_closed = 1");
    if (mysqli_num_rows($hours_check) > 0) {
        throw new Exception('Selected day is a clinic closed day');
    }

    // Check for existing bookings
    $existing_booking_check = mysqli_query($conn, "SELECT 1 FROM course_bookings WHERE booking_datetime = '$booking_datetime' AND status IN ('pending', 'confirmed')");
    if (mysqli_num_rows($existing_booking_check) > 0) {
        throw new Exception('Selected time slot is already booked');
    }

    // Get course details including branch_id and price
    $course_query = "SELECT branch_id, course_price FROM course WHERE course_id = ?";
    $stmt = mysqli_prepare($conn, $course_query);
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $course = mysqli_fetch_assoc($result);

    if (!$course) {
        throw new Exception('Course not found');
    }

    $branch_id = $course['branch_id'];
    $course_price = $course['course_price'];

    // Insert into course_bookings
    $booking_query = "INSERT INTO course_bookings (branch_id, cus_id, booking_datetime, created_at, users_id, status) 
                      VALUES (?, ?, ?, NOW(), ?, 'pending')";
    $stmt = mysqli_prepare($conn, $booking_query);
    mysqli_stmt_bind_param($stmt, "iisi", $branch_id, $user_id, $booking_datetime, $user_id);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error inserting into course_bookings: " . mysqli_stmt_error($stmt));
    }
    $booking_id = mysqli_insert_id($conn);

    // Insert into order_course
    $order_query = "INSERT INTO order_course (cus_id, users_id, course_bookings_id, order_datetime, order_payment, order_net_total, order_status) 
                    VALUES (?, ?, ?, NOW(), ?, ?, 1)";
    $stmt = mysqli_prepare($conn, $order_query);
    mysqli_stmt_bind_param($stmt, "iiisi", $user_id, $user_id, $booking_id, $payment_method, $course_price);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error inserting into order_course: " . mysqli_stmt_error($stmt));
    }
    $order_id = mysqli_insert_id($conn);

    // Insert into order_detail
    $detail_query = "INSERT INTO order_detail (oc_id, course_id, od_amount, od_price) 
                     VALUES (?, ?, 1, ?)";
    $stmt = mysqli_prepare($conn, $detail_query);
    mysqli_stmt_bind_param($stmt, "iid", $order_id, $course_id, $course_price);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Error inserting into order_detail: " . mysqli_stmt_error($stmt));
    }

    // Handle file upload if payment method is transfer
    if ($payment_method === 'transfer' && isset($_FILES['paymentProof'])) {
        $original_file_name = $_FILES['paymentProof']['name'];
        $file_tmp = $_FILES['paymentProof']['tmp_name'];
        
        // Generate new file name
        $new_file_name = generateNewFileName($original_file_name);
        
        $file_destination = "../img/payment-proofs/" . $new_file_name;

        if (move_uploaded_file($file_tmp, $file_destination)) {
            // Update order with payment proof
            $update_query = "UPDATE order_course SET payment_proofs = ? WHERE oc_id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "si", $new_file_name, $order_id);
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Error updating payment proof: " . mysqli_stmt_error($stmt));
            }
        } else {
            throw new Exception("Failed to upload payment proof");
        }
    }

    mysqli_commit($conn);
    echo json_encode(['success' => true, 'message' => 'Booking successfully processed']);
} catch (Exception $e) {
    mysqli_rollback($conn);
    logError('Booking error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

mysqli_close($conn);
?>