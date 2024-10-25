<?php
require '../../dbcon.php';

function checkStockStatus($orderId) {
    global $conn;
    $insufficientItems = [];
    
    // ดึงข้อมูลทรัพยากรที่ใช้ในคำสั่งซื้อ
    $sql = "SELECT ocr.*, 
            CASE 
                WHEN ocr.resource_type = 'drug' THEN d.drug_name
                WHEN ocr.resource_type = 'tool' THEN t.tool_name
                WHEN ocr.resource_type = 'accessory' THEN a.acc_name
            END as resource_name,
            CASE 
                WHEN ocr.resource_type = 'drug' THEN d.drug_amount
                WHEN ocr.resource_type = 'tool' THEN t.tool_amount
                WHEN ocr.resource_type = 'accessory' THEN a.acc_amount
            END as current_stock
            FROM order_course_resources ocr
            LEFT JOIN drug d ON ocr.resource_type = 'drug' AND ocr.resource_id = d.drug_id
            LEFT JOIN tool t ON ocr.resource_type = 'tool' AND ocr.resource_id = t.tool_id
            LEFT JOIN accessories a ON ocr.resource_type = 'accessory' AND ocr.resource_id = a.acc_id
            WHERE ocr.order_id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $requiredAmount = $row['quantity'];
        $currentStock = $row['current_stock'];
        
        if ($currentStock < $requiredAmount) {
            $insufficientItems[] = [
                'type' => $row['resource_type'],
                'name' => $row['resource_name'],
                'required' => $requiredAmount,
                'current' => $currentStock,
                'willBeNegative' => $currentStock - $requiredAmount
            ];
        }
    }
    
    return [
        'hasInsufficientStock' => !empty($insufficientItems),
        'insufficientItems' => $insufficientItems
    ];
}

// ถ้าเรียกโดยตรงผ่าน AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    
    if ($orderId > 0) {
        $result = checkStockStatus($orderId);
        echo json_encode($result);
    } else {
        echo json_encode(['error' => 'Invalid order ID']);
    }
}
?>