<?php
session_start();
require '../../dbcon.php';

// ตรวจสอบสิทธิ์ของผู้ใช้
// if ($_SESSION['position_id'] != 1 && $_SESSION['position_id'] != 2) {
//     echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ในการยกเลิกการชำระเงิน']);
//     exit;
// }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);

    $conn->begin_transaction();

    try {
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
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // ลบไฟล์สลิปการชำระเงิน (ถ้ามี)
        $sql_get_slip = "SELECT payment_proofs FROM order_course WHERE oc_id = ?";
        $stmt_get_slip = $conn->prepare($sql_get_slip);
        $stmt_get_slip->bind_param("i", $order_id);
        $stmt_get_slip->execute();
        $result = $stmt_get_slip->get_result();
        $row = $result->fetch_assoc();

        if ($row['payment_proofs']) {
            $file_path = "../../img/payment-proofs/" . $row['payment_proofs'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'ยกเลิกการชำระเงินสำเร็จ']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();