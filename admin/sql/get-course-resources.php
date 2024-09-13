<?php
require '../../dbcon.php';

function translateResourceType($type) {
    switch($type) {
        case 'drug':
            return 'ยา';
        case 'tool':
            return 'เครื่องมือ';
        case 'accessory':
            return 'อุปกรณ์';
        default:
            return $type;
    }
}


$order_id = intval($_GET['order_id']);
$course_id = intval($_GET['course_id']);

$sql = "SELECT ocr.*, 
               CASE 
                 WHEN ocr.resource_type = 'drug' THEN d.drug_name
                 WHEN ocr.resource_type = 'tool' THEN t.tool_name
                 WHEN ocr.resource_type = 'accessory' THEN a.acc_name
               END AS resource_name,
               CASE 
                 WHEN ocr.resource_type = 'drug' THEN u1.unit_name
                 WHEN ocr.resource_type = 'tool' THEN u2.unit_name
                 WHEN ocr.resource_type = 'accessory' THEN u3.unit_name
               END AS unit_name
        FROM order_course_resources ocr
        LEFT JOIN drug d ON ocr.resource_type = 'drug' AND ocr.resource_id = d.drug_id
        LEFT JOIN tool t ON ocr.resource_type = 'tool' AND ocr.resource_id = t.tool_id
        LEFT JOIN accessories a ON ocr.resource_type = 'accessory' AND ocr.resource_id = a.acc_id
        LEFT JOIN unit u1 ON d.drug_unit_id = u1.unit_id
        LEFT JOIN unit u2 ON t.tool_unit_id = u2.unit_id
        LEFT JOIN unit u3 ON a.acc_unit_id = u3.unit_id
        WHERE ocr.order_id = ? AND ocr.course_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();

$resources = [];
while ($row = $result->fetch_assoc()) {
    $resources[] = [
        'id' => $row['id'],
        'type' => translateResourceType($resource['resource_type']),
        'name' => $row['resource_name'],
        'quantity' => $row['quantity'],
        'unit' => $row['unit_name']
    ];
}

echo json_encode($resources);

$stmt->close();
$conn->close();