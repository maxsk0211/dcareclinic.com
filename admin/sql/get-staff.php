<?php
require '../../dbcon.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';

if (empty($type)) {
    echo json_encode(['error' => 'Staff type is required']);
    exit;
}

$sql = "SELECT users_id as id, CONCAT(users_fname, ' ', users_lname) as name 
        FROM users 
        WHERE position_id = ? AND users_status = 1";

$position_id = ($type == 'doctor') ? 3 : 4; // 3 for doctors, 4 for nurses

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $position_id);
$stmt->execute();
$result = $stmt->get_result();

$staff = [];
while ($row = $result->fetch_assoc()) {
    $staff[] = $row;
}

echo json_encode($staff);

$stmt->close();
$conn->close();