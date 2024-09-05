<?php
session_start();
// include '../chk-session.php';
require '../../dbcon.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT cus_id as id, CONCAT(cus_firstname, ' ', cus_lastname, ' (', cus_tel, ')') as text 
        FROM customer 
        WHERE cus_firstname LIKE '%$search%' 
           OR cus_lastname LIKE '%$search%' 
           OR cus_tel LIKE '%$search%'
        LIMIT 10";

$result = $conn->query($sql);

$customers = [];
while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
}

echo json_encode($customers);