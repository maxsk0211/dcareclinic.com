<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'data' => []];

try {
    if (!isset($_GET['order_id'])) {
        throw new Exception('ไม่พบรหัสรายการ');
    }

    $order_id = intval($_GET['order_id']);

    $sql = "SELECT dcl.*,
            CONCAT(u.users_fname, ' ', u.users_lname) as cancelled_by_name
            FROM deposit_cancellation_logs dcl
            LEFT JOIN users u ON dcl.cancelled_by = u.users_id
            WHERE dcl.order_id = ?
            ORDER BY dcl.cancelled_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'cancelled_at' => $row['cancelled_at'],
            'deposit_amount' => $row['deposit_amount'],
            'cancellation_reason' => $row['cancellation_reason'],
            'cancelled_by_name' => $row['cancelled_by_name']
        ];
    }

    $response['success'] = true;
    $response['data'] = $data;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
$conn->close();