<?php
require '../../dbcon.php';

$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;

if ($service_id == 0) {
    echo json_encode(['error' => 'Invalid service ID']);
    exit;
}

$sql = "SELECT ssr.*, CONCAT(u.users_fname, ' ', u.users_lname) as staff_name
        FROM service_staff_records ssr
        JOIN users u ON ssr.staff_id = u.users_id
        WHERE ssr.service_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $service_id);
$stmt->execute();
$result = $stmt->get_result();

$records = [];
while ($row = $result->fetch_assoc()) {
    $records[] = $row;
}

echo json_encode($records);

$stmt->close();
$conn->close();