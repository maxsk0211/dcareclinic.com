<?php
session_start();
require '../../dbcon.php';

$courseIds = isset($_GET['courseIds']) ? $_GET['courseIds'] : [];
$branch_id = $_SESSION['branch_id'];

if (!empty($courseIds)) {
    $placeholders = str_repeat('?,', count($courseIds) - 1) . '?';
    
    $sql = "SELECT 
        course_id as id,
        course_name as name,
        course_price as price,
        course_amount as amount
    FROM course 
    WHERE course_id IN ($placeholders)
    AND branch_id = ?
    AND course_status = 1";
    
    // สร้าง parameter types string
    $types = str_repeat('i', count($courseIds)) . 'i';
    
    // สร้าง array ของ parameters
    $params = array_merge($courseIds, [$branch_id]);
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $courses = [];
    
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    
    echo json_encode($courses);
} else {
    echo json_encode([]);
}
?>