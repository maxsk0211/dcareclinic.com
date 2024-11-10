<?php
require '../../dbcon.php';

if (isset($_GET['branch_id'])) {
    $branch_id = $_GET['branch_id'];
    
    $sql = "SELECT * FROM branch WHERE branch_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $branch_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            echo json_encode($row);
        } else {
            echo json_encode(['error' => 'ไม่พบข้อมูล']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['error' => 'เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL']);
    }
    
    $conn->close();
} else {
    echo json_encode(['error' => 'ไม่ได้ระบุ ID']);
}
?>