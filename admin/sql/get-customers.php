<?php
session_start();
include '../chk-session.php';
require '../../dbcon.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

// ฟังก์ชันสำหรับแปลง cus_id เป็นรูปแบบ HN
function formatHN($cusId) {
    return 'HN-' . str_pad($cusId, 6, '0', STR_PAD_LEFT);
}

$sql = "SELECT cus_id, cus_firstname, cus_lastname, cus_tel, cus_id_card_number
        FROM customer 
        WHERE cus_firstname LIKE '%$search%' 
           OR cus_lastname LIKE '%$search%' 
           OR cus_tel LIKE '%$search%'
           OR cus_id_card_number LIKE '%$search%'
           OR CONCAT('HN-', LPAD(cus_id, 6, '0')) LIKE '%$search%'
        LIMIT 10";

$result = $conn->query($sql);

$customers = [];
while ($row = $result->fetch_assoc()) {
    $customers[] = [
        'id' => $row['cus_id'],
        'text' => formatHN($row['cus_id']) . ' - ' . $row['cus_firstname'] . ' ' . $row['cus_lastname'] . ' (' . $row['cus_tel'] . ') - ' . $row['cus_id_card_number']
    ];
}

echo json_encode($customers);