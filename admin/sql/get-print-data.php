<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require '../../dbcon.php';

$cusId = $_GET['cus_id'] ?? null;
$opdId = $_GET['opd_id'] ?? null;

if (!$cusId) {
    echo json_encode(['error' => 'Missing customer ID']);
    exit;
}

// ดึงข้อมูลลูกค้า
$customerQuery = "SELECT * FROM customer WHERE cus_id = ?";
$customerStmt = $conn->prepare($customerQuery);
$customerStmt->bind_param("i", $cusId);
$customerStmt->execute();
$customerResult = $customerStmt->get_result();
$customerData = $customerResult->fetch_assoc();

if (!$customerData) {
    echo json_encode(['error' => 'Customer not found']);
    exit;
}

// ดึงข้อมูล OPD ถ้ามี
$opdData = null;
if ($opdId) {
    $opdQuery = "SELECT * FROM opd WHERE opd_id = ?";
    $opdStmt = $conn->prepare($opdQuery);
    $opdStmt->bind_param("i", $opdId);
    $opdStmt->execute();
    $opdResult = $opdStmt->get_result();
    $opdData = $opdResult->fetch_assoc();
}

// คำนวณอายุ
$birthDate = new DateTime($customerData['cus_birthday']);
$today = new DateTime();
$age = $today->diff($birthDate);

// สร้างรหัส HN
$hn = 'HN-' . str_pad($customerData['cus_id'], 6, '0', STR_PAD_LEFT);

$printData = [
    'customer' => array_merge($customerData, ['hn' => $hn]),
    'opd' => $opdData,
    'currentDate' => date('d/m/Y'),
    'printDateTime' => date('d/m/Y H:i:s'),
    'age' => $age->y . ' ปี ' . $age->m . ' เดือน ' . $age->d . ' วัน'
];

try {
    echo json_encode($printData, JSON_THROW_ON_ERROR);
} catch (JsonException $e) {
    echo json_encode(['error' => 'JSON encoding error: ' . $e->getMessage()]);
}