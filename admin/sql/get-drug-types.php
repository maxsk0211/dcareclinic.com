<?php
require_once '../../dbcon.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['users_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

try {
    $branch_id = $_SESSION['branch_id'];
    $sql = "SELECT drug_type_id, drug_type_name FROM drug_type WHERE branch_id = ? OR branch_id = 0 ORDER BY drug_type_name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $branch_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();