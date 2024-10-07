<?php
session_start();
require '../../dbcon.php';

$branch_id = $_SESSION['branch_id'];

$sql = "SELECT room_id, room_name FROM rooms WHERE branch_id = ? ORDER BY room_name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $branch_id);
$stmt->execute();
$result = $stmt->get_result();

$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

header('Content-Type: application/json');
echo json_encode($rooms);
exit;