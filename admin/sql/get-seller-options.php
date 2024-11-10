<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    // ดึงข้อมูลผู้ใช้ที่มีตำแหน่งเป็นพนักงานต้อนรับ หรือตำแหน่งที่เกี่ยวข้องกับการขาย
    $sql = "SELECT 
                users_id as id,
                CONCAT(users_fname, ' ', users_lname) as name,
                users_nickname,
                position_id
            FROM users 
            WHERE users_status = 1
            AND (branch_id = ? OR branch_id = 0)
            ORDER BY users_fname";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $branch_id = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : 1;
    $stmt->bind_param("i", $branch_id);

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $sellers = [];
    
    while ($row = $result->fetch_assoc()) {
        $displayName = $row['name'];
        if (!empty($row['users_nickname'])) {
            $displayName .= ' (' . $row['users_nickname'] . ')';
        }
        $sellers[] = [
            'id' => $row['id'],
            'name' => $displayName
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $sellers
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();