<?php
session_start();
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);
    $password = $_POST['password'];
    $reason = trim($_POST['reason']);
    $user_id = $_SESSION['users_id'];

    // ตรวจสอบรหัสผ่าน
    $password_check_sql = "SELECT users_password FROM users WHERE users_id = ?";
    $stmt = $conn->prepare($password_check_sql);
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
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $old_data = $stmt->get_result()->fetch_assoc();

        // ยกเลิกการชำระเงิน
        $sql = "UPDATE order_course SET 
                order_payment = 'ยังไม่จ่ายเงิน', 
                order_net_total = 0,
                seller_id = NULL,
                order_payment_date = NULL,
                payment_proofs = NULL
                WHERE oc_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        
        if (!$stmt->execute()) {
            throw new Exception("ไม่สามารถยกเลิกการชำระเงินได้");
        }

        // บันทึก log
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
        $stmt->bind_param("iisi", 
            $user_id,
            $order_id,
            $details,
            $_SESSION['branch_id']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("ไม่สามารถบันทึกประวัติการยกเลิกได้");
        }

        // ลบไฟล์สลิป (ถ้ามี)
        if ($old_data['payment_proofs']) {
            $file_path = "../../img/payment-proofs/" . $old_data['payment_proofs'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        $conn->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}