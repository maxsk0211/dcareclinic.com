<?php
session_start();
require '../../dbcon.php';

if (!isset($_GET['service_id'])) {
    echo json_encode(['error' => 'Missing service_id']);
    exit;
}

$service_id = intval($_GET['service_id']);

// ปรับ SQL query ให้ดึงข้อมูลตำแหน่งมาด้วย
$sql = "SELECT ssr.staff_record_id, 
               ssr.staff_df,
               ssr.staff_df_type,
               ssr.staff_type,
               u.users_fname,
               u.users_lname,
               u.users_nickname,
               p.position_name,
               CONCAT(u.users_fname, ' ', u.users_lname) as staff_name
        FROM service_staff_records ssr
        JOIN users u ON ssr.staff_id = u.users_id
        JOIN position p ON u.position_id = p.position_id
        WHERE ssr.service_id = ? 
        AND ssr.staff_type IN ('doctor', 'nurse')
        ORDER BY ssr.staff_type ASC, ssr.staff_record_id DESC";

try {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $service_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $records = [];

    while ($row = $result->fetch_assoc()) {
        // เพิ่มชื่อเล่น (ถ้ามี)
        if (!empty($row['users_nickname'])) {
            $row['staff_name'] .= ' (' . $row['users_nickname'] . ')';
        }
        $records[] = $row;
    }

    echo json_encode($records);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>