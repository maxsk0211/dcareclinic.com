<?php
session_start();
require '../../dbcon.php';

$branch_id = $_SESSION['branch_id'];

$sql = "SELECT * FROM rooms WHERE branch_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $branch_id);
$stmt->execute();
$result = $stmt->get_result();

$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

echo json_encode($rooms);