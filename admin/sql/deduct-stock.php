<?php
session_start();
require '../../dbcon.php';

function deductStock($orderId) {
    global $conn;
    
    try {
        $conn->begin_transaction();
        
        // ดึงข้อมูลทรัพยากรที่ต้องตัด
        $sql = "SELECT * FROM order_course_resources WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // ตรวจสอบว่ามี session หรือไม่
        if (!isset($_SESSION['users_id']) || !isset($_SESSION['branch_id'])) {
            throw new Exception('ไม่พบข้อมูล Session กรุณาเข้าสู่ระบบใหม่');
        }

        $userId = $_SESSION['users_id'];
        $branchId = $_SESSION['branch_id'];
        
        // สร้างเลข order แบบมี padding
        $orderNumber = 'ORDER-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
        // แก้ไขรูปแบบ notes
        $notes = "ตัดสต๊อกจากการใช้บริการ {$orderNumber}";
        
        while ($row = $result->fetch_assoc()) {
            $type = $row['resource_type'];
            $resourceId = $row['resource_id'];
            $quantity = $row['quantity'];
            
            // อัพเดทสต๊อกตามประเภท
            switch ($type) {
                case 'drug':
                    $updateSql = "UPDATE drug SET drug_amount = drug_amount - ? WHERE drug_id = ?";
                    break;
                case 'tool':
                    $updateSql = "UPDATE tool SET tool_amount = tool_amount - ? WHERE tool_id = ?";
                    break;
                case 'accessory':
                    $updateSql = "UPDATE accessories SET acc_amount = acc_amount - ? WHERE acc_id = ?";
                    break;
                default:
                    throw new Exception('ไม่พบประเภทของทรัพยากร');
            }
            
            $updateStmt = $conn->prepare($updateSql);
            if (!$updateStmt) {
                throw new Exception('เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL');
            }
            
            $updateStmt->bind_param("di", $quantity, $resourceId);
            if (!$updateStmt->execute()) {
                throw new Exception('เกิดข้อผิดพลาดในการอัพเดทสต๊อก');
            }
            
            // บันทึก stock transaction ด้วย notes ที่แก้ไขแล้ว
            $transactionSql = "INSERT INTO stock_transactions 
                             (transaction_date, users_id, quantity, stock_type, 
                              related_id, status, branch_id, notes) 
                             VALUES (NOW(), ?, ?, ?, ?, 1, ?, ?)";
            
            $transStmt = $conn->prepare($transactionSql);
            if (!$transStmt) {
                throw new Exception('เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL สำหรับบันทึกประวัติ');
            }

            $transStmt->bind_param("idsiss", $userId, $quantity, $type, $resourceId, $branchId, $notes);
            if (!$transStmt->execute()) {
                throw new Exception('เกิดข้อผิดพลาดในการบันทึกประวัติการตัดสต๊อก');
            }
        }
        
        $conn->commit();
        return ['success' => true];
        
    } catch (Exception $e) {
        $conn->rollback();
        return [
            'success' => false, 
            'error' => $e->getMessage()
        ];
    }
}

// ถ้าเรียกโดยตรงผ่าน AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    
    if ($orderId > 0) {
        $result = deductStock($orderId);
        echo json_encode($result);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid order ID'
        ]);
    }
    exit;
}
?>