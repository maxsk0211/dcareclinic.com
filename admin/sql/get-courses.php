<?php
session_start();
require '../../dbcon.php';

// ตรวจสอบว่ามีการส่ง branch_id มาหรือไม่
$branch_id = $_SESSION['branch_id'];

$sql = "SELECT course_id, course_name, course_price, course_amount 
        FROM course 
        WHERE course_status = 1 AND branch_id = '$branch_id'";

// ถ้ามี branch_id ให้เพิ่มเงื่อนไขในการค้นหา
// if ($branch_id > 0) {
//      $sql .= " AND branch_id = ?";
// }

$sql .= " ORDER BY course_name ASC";

$stmt = $conn->prepare($sql);

// if ($branch_id > 0) {
//     $stmt->bind_param("i", $branch_id);
// }

$stmt->execute();
$result = $stmt->get_result();

$courses = array();
while ($row = $result->fetch_assoc()) {
    $courses[] = array(
        'id' => $row['course_id'],
        'name' => $row['course_name'],
        'price' => $row['course_price'],
        'amount' => $row['course_amount']
    );
}

echo json_encode($courses);

$stmt->close();
$conn->close();