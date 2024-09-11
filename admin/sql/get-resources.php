<?php
require '../../dbcon.php';

$type = isset($_GET['type']) ? $_GET['type'] : '';
$branch_id = isset($_GET['branch_id']) ? intval($_GET['branch_id']) : 0;

if (empty($type)) {
    echo json_encode(array('error' => 'Resource type is required'));
    exit;
}

switch ($type) {
    case 'drug':
        $sql = "SELECT drug_id as id, drug_name as name, drug_unit_id, drug_amount as stock
                FROM drug 
                WHERE drug_status = 1";
        break;
    case 'tool':
        $sql = "SELECT tool_id as id, tool_name as name, tool_unit_id, tool_amount as stock
                FROM tool 
                WHERE tool_status = 1";
        break;
    case 'accessory':
        $sql = "SELECT acc_id as id, acc_name as name, acc_unit_id, acc_amount as stock
                FROM accessories 
                WHERE acc_status = 1";
        break;
    default:
        echo json_encode(array('error' => 'Invalid resource type'));
        exit;
}

if ($branch_id > 0) {
    $sql .= " AND branch_id = ?";
}

$sql .= " ORDER BY name ASC";

$stmt = $conn->prepare($sql);

if ($branch_id > 0) {
    $stmt->bind_param("i", $branch_id);
}

$stmt->execute();
$result = $stmt->get_result();

$resources = array();
while ($row = $result->fetch_assoc()) {
    $resources[] = array(
        'id' => $row['id'],
        'name' => $row['name'],
        'unit_id' => $row['drug_unit_id'] ?? $row['tool_unit_id'] ?? $row['acc_unit_id'],
        'stock' => $row['stock']
    );
}

echo json_encode($resources);

$stmt->close();
$conn->close();