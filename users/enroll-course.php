<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['users_id'])) {
    header('Location: ../login.php');
    exit;
}
$user_id = mysqli_real_escape_string($conn, $_SESSION['users_id']);

$sql = "SELECT * FROM customer WHERE cus_id = '$user_id'";
$result = $conn->query($sql);
$users = mysqli_fetch_object($result);

if ($users->cus_firstname == null || $users->cus_lastname == null || $users->cus_id_card_number == null || $users->cus_birthday == null || $users->cus_title == null || $users->cus_gender == null || $users->cus_tel == null) {
    $_SESSION['msg_info'] = "กรุณากรอกข้อมูลให้ครบ ก่อนเริ่มใช้งาน";
    header('Location: user-profile.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: user-courses.php');
    exit;
}

$course_id = $_GET['id'];

// Check if the user is already enrolled in this course
$stmt = $conn->prepare("
    SELECT 1 FROM order_detail od 
    JOIN order_course oc ON od.oc_id = oc.oc_id 
    WHERE oc.cus_id = ? AND od.course_id = ?
");
$stmt->bind_param("ii", $_SESSION['users_id'], $course_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $_SESSION['error'] = "You are already enrolled in this course.";
    header('Location: user-courses.php');
    exit;
}

// Get course details
$stmt = $conn->prepare("SELECT * FROM course WHERE course_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

if (!$course) {
    $_SESSION['error'] = "Course not found.";
    header('Location: user-courses.php');
    exit;
}

// Create a new order
$conn->begin_transaction();

try {
    // Insert into order_course
    $stmt = $conn->prepare("INSERT INTO order_course (cus_id, users_id, order_datetime, order_payment, order_net_total, order_status) VALUES (?, ?, NOW(), 'Pending', ?, 1)");
    $stmt->bind_param("iid", $_SESSION['users_id'], $_SESSION['users_id'], $course['course_price']);
    $stmt->execute();
    $oc_id = $conn->insert_id;

    // Insert into order_detail
    $stmt = $conn->prepare("INSERT INTO order_detail (oc_id, course_id, od_amount, od_price) VALUES (?, ?, 1, ?)");
    $stmt->bind_param("iid", $oc_id, $course_id, $course['course_price']);
    $stmt->execute();

    $conn->commit();
    $_SESSION['success'] = "You have successfully enrolled in the course.";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "An error occurred while enrolling in the course. Please try again.";
}

header('Location: user-courses.php');
exit;
?>