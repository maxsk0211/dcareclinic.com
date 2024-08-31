<?php
require '../../dbcon.php';

$searchTerm = $_GET['q'];

$sql = "SELECT cus_id, cus_id_card_number, cus_firstname, cus_lastname, cus_email, cus_tel, cus_image 
        FROM customer 
        WHERE cus_id_card_number LIKE '%$searchTerm%' 
           OR cus_firstname LIKE '%$searchTerm%' 
           OR cus_lastname LIKE '%$searchTerm%' 
           OR cus_email LIKE '%$searchTerm%' 
           OR cus_tel LIKE '%$searchTerm%'";

$result = $conn->query($sql);

$customers = [];
while ($row = $result->fetch_assoc()) {
    $customers[] = [
        'id' => $row['cus_id'],
        'text' => $row['cus_firstname'] . ' ' . $row['cus_lastname'],
        'id_card' => $row['cus_id_card_number'],
        'email' => $row['cus_email'],
        'tel' => $row['cus_tel'],
        'image' => $row['cus_image']
    ];
}

echo json_encode($customers);