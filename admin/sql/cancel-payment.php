<?php
session_start();
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);
    $password = $_POST['password'];
    $reason = trim($_POST['reason']);
    $user_id = $_SESSION['users_id'];

    // 1. ตรวจสอบรหัสผ่าน
    $password_check_sql = "SELECT users_password FROM users WHERE users_id = ?";
    $stmt = $conn->prepare($password_check_sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare statement failed: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user || $user['users_password'] !== $password) {
        echo json_encode([
            'success' => false, 
            'message' => 'รหัสผ่านไม่ถูกต้อง'
        ]);
        exit;
    }

    // เริ่ม transaction
    $conn->begin_transaction();

    try {
        // เก็บข้อมูลก่อนยกเลิก
        $sql_old_data = "SELECT oc.*, c.cus_firstname, c.cus_lastname 
                        FROM order_course oc 
                        JOIN customer c ON oc.cus_id = c.cus_id 
                        WHERE oc.oc_id = ?";
        $stmt = $conn->prepare($sql_old_data);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $old_data = $stmt->get_result()->fetch_assoc();

        // 2. ยกเลิกการชำระเงิน
        $sql = "UPDATE order_course SET 
                order_payment = 'ยังไม่จ่ายเงิน', 
                seller_id = NULL,
                order_payment_date = NULL,
                payment_proofs = NULL
                WHERE oc_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("i", $order_id);
        
        if (!$stmt->execute()) {
            throw new Exception("ไม่สามารถยกเลิกการชำระเงินได้: " . $stmt->error);
        }

        // 3. คืนทรัพยากร
        $sql_resources = "SELECT resource_type, resource_id, quantity 
                         FROM order_course_resources 
                         WHERE order_id = ?";
        $stmt = $conn->prepare($sql_resources);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $resources = $stmt->get_result();

        while ($resource = $resources->fetch_assoc()) {
            switch ($resource['resource_type']) {
                case 'drug':
                    $sql_update = "UPDATE drug SET drug_amount = drug_amount + ? WHERE drug_id = ?";
                    break;
                case 'accessory':
                    $sql_update = "UPDATE accessories SET acc_amount = acc_amount + ? WHERE acc_id = ?";
                    break;
                case 'tool':
                    $sql_update = "UPDATE tool SET tool_amount = tool_amount + ? WHERE tool_id = ?";
                    break;
                default:
                    break; // เปลี่ยนจาก continue เป็น break
            }
            
            $stmt = $conn->prepare($sql_update);
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error . " SQL: " . $sql_update);
            }
            $stmt->bind_param("di", $resource['quantity'], $resource['resource_id']);
            
            if (!$stmt->execute()) {
                throw new Exception("ไม่สามารถคืนทรัพยากรได้: " . $stmt->error);
            }

            // บันทึก transaction การคืนทรัพยากร
            $notes = "คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-" . str_pad($order_id, 6, "0", STR_PAD_LEFT);
            $sql_transaction = "INSERT INTO stock_transactions 
                              (transaction_date, users_id, quantity, stock_type, related_id, 
                               status, branch_id, notes) 
                              VALUES (NOW(), ?, ?, ?, ?, 1, ?, ?)";
            $stmt = $conn->prepare($sql_transaction);
            if (!$stmt) {
                throw new Exception("Prepare statement failed: " . $conn->error);
            }
            $stmt->bind_param("idsiis", 
                $user_id,
                $resource['quantity'],
                $resource['resource_type'],
                $resource['resource_id'],
                $_SESSION['branch_id'],
                $notes
            );
            
            if (!$stmt->execute()) {
                throw new Exception("ไม่สามารถบันทึกประวัติการคืนทรัพยากรได้: " . $stmt->error);
            }
        }

        // 4. บันทึก activity log
        $details = json_encode([
            'reason' => $reason,
            'payment_info' => [
                'amount' => $old_data['order_net_total'],
                'payment_type' => $old_data['order_payment'],
                'payment_date' => $old_data['order_payment_date']
            ],
            'customer_info' => [
                'name' => $old_data['cus_firstname'] . ' ' . $old_data['cus_lastname']
            ]
        ], JSON_UNESCAPED_UNICODE);

        $log_sql = "INSERT INTO activity_logs 
                    (user_id, action, entity_type, entity_id, details, branch_id) 
                    VALUES (?, 'cancel_payment', 'payment', ?, ?, ?)";
        $stmt = $conn->prepare($log_sql);
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }
        $stmt->bind_param("iisi", 
            $user_id,
            $order_id,
            $details,
            $_SESSION['branch_id']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("ไม่สามารถบันทึกประวัติการยกเลิกได้: " . $stmt->error);
        }

        // 5. ลบไฟล์สลิป (ถ้ามี)
        if ($old_data['payment_proofs']) {
            $file_path = "../../img/payment-proofs/" . $old_data['payment_proofs'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        // ทำการ commit เมื่อทุกอย่างสำเร็จ
        $conn->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        // ถ้าเกิดข้อผิดพลาดให้ rollback
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}