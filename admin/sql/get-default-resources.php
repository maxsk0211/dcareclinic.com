<?php
require '../../dbcon.php';

if (isset($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);

    $sql = "SELECT cr.*, 
            CASE 
                WHEN cr.resource_type = 'drug' THEN d.drug_name
                WHEN cr.resource_type = 'tool' THEN t.tool_name
                WHEN cr.resource_type = 'accessory' THEN a.acc_name
            END AS name,
            CASE 
                WHEN cr.resource_type = 'drug' THEN u1.unit_name
                WHEN cr.resource_type = 'tool' THEN u2.unit_name
                WHEN cr.resource_type = 'accessory' THEN u3.unit_name
            END AS unit_name
            FROM course_resources cr
            LEFT JOIN drug d ON cr.resource_id = d.drug_id AND cr.resource_type = 'drug'
            LEFT JOIN tool t ON cr.resource_id = t.tool_id AND cr.resource_type = 'tool'
            LEFT JOIN accessories a ON cr.resource_id = a.acc_id AND cr.resource_type = 'accessory'
            LEFT JOIN unit u1 ON d.drug_unit_id = u1.unit_id
            LEFT JOIN unit u2 ON t.tool_unit_id = u2.unit_id
            LEFT JOIN unit u3 ON a.acc_unit_id = u3.unit_id
            WHERE cr.course_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $resources = [];
    while ($row = $result->fetch_assoc()) {
        $resources[] = [
            'type' => $row['resource_type'],
            'id' => $row['resource_id'],
            'name' => $row['name'],
            'quantity' => $row['quantity'],
            'unit' => $row['unit_name']
        ];
    }

    echo json_encode($resources);
} else {
    echo json_encode(['error' => 'Course ID is required']);
}