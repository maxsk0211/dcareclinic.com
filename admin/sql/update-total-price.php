<?php
require '../../dbcon.php';

$order_id = intval($_POST['order_id']);
$total_price = floatval($_POST['total_price']);

$sql = "UPDATE order_course SET order_net_total = ? WHERE oc_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("di", $total_price, $order_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();