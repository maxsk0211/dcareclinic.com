<?php
session_start();
require '../../dbcon.php';

$room_id = $_GET['roomId'];
$branch_id = $_SESSION['branch_id'];

$sql = "SELECT * FROM rooms WHERE room_id = ? AND branch_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $room_id, $branch_id);
$stmt->execute();
$result = $stmt->get_result();

if ($room = $result->fetch_assoc()) {
    echo json_encode($room);
} else {
    echo json_encode(['error' => 'Room not found']);
}