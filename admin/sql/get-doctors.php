<?php
require '../../dbcon.php';

$sql = "SELECT users_id as id, CONCAT(users_fname, ' ', users_lname) as name 
        FROM users 
        WHERE position_id = 3 AND users_status = 1";
$result = $conn->query($sql);

$doctors = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}

echo json_encode($doctors);
$conn->close();
?>