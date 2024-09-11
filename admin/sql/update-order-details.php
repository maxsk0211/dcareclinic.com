<?php
require '../../dbcon.php';

$order_id = intval($_POST['order_id']);
$booking_datetime = $_POST['booking_datetime'];
$payment_status = $_POST['payment_status'];

$sql = "UPDATE order_course SET booking_datetime = ?, order_payment = ? WHERE oc_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $booking_datetime, $payment_status, $order_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();