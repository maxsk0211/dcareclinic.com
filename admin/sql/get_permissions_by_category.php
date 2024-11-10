<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    $category = $_GET['category'] ?? '';
    
    if (empty($category)) {
        throw new Exception('ไม่พบข้อมูลหมวดหมู่');
    }

    // แก้ไข SQL ให้รวมชื่อหน้าเพจ
    $sql = "SELECT 
            permission_id as id, 
            permission_name as name,
            page,
            CASE 
                WHEN page != '' THEN CONCAT(permission_name, ' (', page, ')')
                ELSE permission_name
            END as display_name
        FROM permissions 
        WHERE category = ? AND status = 1
        ORDER BY permission_name";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $permissions = [];
    while ($row = $result->fetch_assoc()) {
        $permissions[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'page' => $row['page'],
            'display_name' => $row['display_name']
        ];
    }
    
    echo json_encode($permissions);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>