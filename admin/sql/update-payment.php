<?php
session_start();
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);
    $payment_type = $_POST['payment_type'];
    $received_amount = floatval($_POST['received_amount']);
    $seller_id = $_SESSION['users_id']; // ผู้รับเงิน

    $conn->begin_transaction();

    try {
        // อัพเดทข้อมูลการชำระเงิน
        $sql = "UPDATE order_course SET 
                order_payment = ?, 
                order_net_total = ?,  
                seller_id = ?,
                order_payment_date = NOW()
                WHERE oc_id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("sdii", $payment_type, $received_amount, $seller_id, $order_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // ถ้าเป็นการโอนเงิน ให้บันทึกรูปสลิป
        if ($payment_type === 'เงินโอน' && isset($_FILES['payment_slip'])) {
            $target_dir = "../../img/payment-proofs/";
            $file_extension = pathinfo($_FILES["payment_slip"]["name"], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES["payment_slip"]["tmp_name"], $target_file)) {
                $sql_slip = "UPDATE order_course SET payment_proofs = ? WHERE oc_id = ?";
                $stmt_slip = $conn->prepare($sql_slip);
                if ($stmt_slip === false) {
                    throw new Exception("Prepare failed for slip update: " . $conn->error);
                }
                $stmt_slip->bind_param("si", $new_filename, $order_id);
                if (!$stmt_slip->execute()) {
                    throw new Exception("Execute failed for slip update: " . $stmt_slip->error);
                }
            } else {
                throw new Exception("Failed to upload slip image.");
            }
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'บันทึกการชำระเงินสำเร็จ']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();