<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        // รับค่า order_id และตรวจสอบความถูกต้อง
        if (!isset($_GET['order_id'])) {
            throw new Exception("ไม่พบ order_id");
        }
        
        $order_id = intval($_GET['order_id']);
        if ($order_id <= 0) {
            throw new Exception("order_id ไม่ถูกต้อง");
        }

        // คิวรี่ข้อมูล
        $sql = "SELECT al.*, u.users_fname, u.users_lname 
                FROM activity_logs al
                LEFT JOIN users u ON al.user_id = u.users_id
                WHERE al.entity_type = 'payment' 
                AND al.action = 'cancel_payment'
                AND al.entity_id = ?
                ORDER BY al.created_at DESC";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("การเตรียมคำสั่ง SQL ล้มเหลว: " . $conn->error);
        }

        $stmt->bind_param("i", $order_id);
        if (!$stmt->execute()) {
            throw new Exception("การดำเนินการคำสั่ง SQL ล้มเหลว: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $history = [];

        // ดึงข้อมูลและจัดรูปแบบ
        while ($row = $result->fetch_assoc()) {
            // ตรวจสอบและแปลงข้อมูล JSON
            $details = json_decode($row['details'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON decode error for order_id $order_id: " . json_last_error_msg());
                $details = null;
            }

            // จัดรูปแบบวันที่
            $timestamp = strtotime($row['created_at']);
            $formattedDate = date('d/m/Y H:i:s', $timestamp);

            $history[] = [
                'created_at' => $formattedDate,
                'users_fname' => $row['users_fname'] ?? 'ไม่ระบุ',
                'users_lname' => $row['users_lname'] ?? '',
                'details' => $details ?? [
                    'reason' => 'ไม่มีข้อมูล',
                    'payment_info' => [
                        'amount' => 0,
                        'payment_type' => 'ไม่ระบุ',
                        'payment_date' => null
                    ],
                    'customer_info' => [
                        'name' => 'ไม่ระบุ'
                    ]
                ]
            ];
        }

        // ส่งข้อมูลกลับ
        echo json_encode([
            'success' => true,
            'data' => $history
        ], JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        error_log("Error in get-payment-cancellation-history.php: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($conn)) {
            $conn->close();
        }
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ], JSON_UNESCAPED_UNICODE);
}