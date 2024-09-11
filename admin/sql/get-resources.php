<?php
require '../../dbcon.php';

if (isset($_GET['type'])) {
    $type = $_GET['type'];
    
    switch ($type) {
        case 'drug':
            $sql = "SELECT drug_id as id, drug_name as name FROM drug WHERE drug_status = 1";
            break;
        case 'tool':
            $sql = "SELECT tool_id as id, tool_name as name FROM tool WHERE tool_status = 1";
            break;
        case 'accessory':
            $sql = "SELECT acc_id as id, acc_name as name FROM accessories WHERE acc_status = 1";
            break;
        default:
            echo json_encode([]);
            exit;
    }
    
    $result = $conn->query($sql);
    $resources = [];
    
    while ($row = $result->fetch_assoc()) {
        $resources[] = $row;
    }
    
    echo json_encode($resources);
} else {
    echo json_encode([]);
}
?>