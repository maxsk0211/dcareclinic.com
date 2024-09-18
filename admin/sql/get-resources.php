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
        $sql = "SELECT d.drug_id as id, d.drug_name as name, d.drug_unit_id, d.drug_amount as stock, u.unit_name
                FROM drug d
                JOIN unit u ON d.drug_unit_id = u.unit_id
                WHERE d.drug_status = 1";
        break;
    case 'tool':
        $sql = "SELECT t.tool_id as id, t.tool_name as name, t.tool_unit_id, t.tool_amount as stock, u.unit_name
                FROM tool t
                JOIN unit u ON t.tool_unit_id = u.unit_id
                WHERE t.tool_status = 1";
        break;
    case 'accessory':
        $sql = "SELECT a.acc_id as id, a.acc_name as name, a.acc_unit_id, a.acc_amount as stock, u.unit_name
                FROM accessories a
                JOIN unit u ON a.acc_unit_id = u.unit_id
                WHERE a.acc_status = 1";
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
        'unit_name' => $row['unit_name'],
        'stock' => $row['stock']
    );
}

echo json_encode($resources);

$stmt->close();
$conn->close();