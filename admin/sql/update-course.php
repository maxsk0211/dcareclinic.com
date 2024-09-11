<?php
require '../../dbcon.php';

$order_id = intval($_POST['order_id']);
$course_id = intval($_POST['course_id']);
$amount = intval($_POST['amount']);
$price = floatval($_POST['price']);

$sql = "UPDATE order_detail SET od_amount = ?, od_price = ? WHERE oc_id = ? AND course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("idii", $amount, $price, $order_id, $course_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();