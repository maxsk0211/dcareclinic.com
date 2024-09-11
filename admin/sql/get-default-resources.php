<?php
require '../../dbcon.php';

$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($course_id && $order_id) {
    // ตรวจสอบว่ามีข้อมูลใน order_course_resources หรือไม่
    $sql_check = "SELECT COUNT(*) as count FROM order_course_resources WHERE order_id = $order_id AND course_id = $course_id";
    $result_check = $conn->query($sql_check);
    $row_check = $result_check->fetch_assoc();

    if ($row_check['count'] > 0) {
        // ถ้ามีข้อมูล ให้ดึงจาก order_course_resources
        $sql = "SELECT ocr.*, 
                       CASE 
                           WHEN ocr.resource_type = 'drug' THEN d.drug_name
                           WHEN ocr.resource_type = 'tool' THEN t.tool_name
                           WHEN ocr.resource_type = 'accessory' THEN a.acc_name
                       END AS name,
                       u.unit_name
                FROM order_course_resources ocr
                LEFT JOIN drug d ON ocr.resource_id = d.drug_id AND ocr.resource_type = 'drug'
                LEFT JOIN tool t ON ocr.resource_id = t.tool_id AND ocr.resource_type = 'tool'
                LEFT JOIN accessories a ON ocr.resource_id = a.acc_id AND ocr.resource_type = 'accessory'
                LEFT JOIN unit u ON 
                    CASE 
                        WHEN ocr.resource_type = 'drug' THEN d.drug_unit_id = u.unit_id
                        WHEN ocr.resource_type = 'tool' THEN t.tool_unit_id = u.unit_id
                        WHEN ocr.resource_type = 'accessory' THEN a.acc_unit_id = u.unit_id
                    END
                WHERE ocr.order_id = $order_id AND ocr.course_id = $course_id";
    } else {
        // ถ้าไม่มีข้อมูล ให้ดึงจาก course_resources
        $sql = "SELECT cr.*, 
                       CASE 
                           WHEN cr.resource_type = 'drug' THEN d.drug_name
                           WHEN cr.resource_type = 'tool' THEN t.tool_name
                           WHEN cr.resource_type = 'accessory' THEN a.acc_name
                       END AS name,
                       u.unit_name
                FROM course_resources cr
                LEFT JOIN drug d ON cr.resource_id = d.drug_id AND cr.resource_type = 'drug'
                LEFT JOIN tool t ON cr.resource_id = t.tool_id AND cr.resource_type = 'tool'
                LEFT JOIN accessories a ON cr.resource_id = a.acc_id AND cr.resource_type = 'accessory'
                LEFT JOIN unit u ON 
                    CASE 
                        WHEN cr.resource_type = 'drug' THEN d.drug_unit_id = u.unit_id
                        WHEN cr.resource_type = 'tool' THEN t.tool_unit_id = u.unit_id
                        WHEN cr.resource_type = 'accessory' THEN a.acc_unit_id = u.unit_id
                    END
                WHERE cr.course_id = $course_id";
    }

    $result = $conn->query($sql);

    $resources = array();
    while ($row = $result->fetch_assoc()) {
        $resources[] = $row;
    }

    echo json_encode($resources);
} else {
    echo json_encode([]);
}
?>