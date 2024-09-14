<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Log the received data
error_log("Received POST data: " . print_r($_POST, true));

// ตรวจสอบสิทธิ์ของผู้ใช้
if ($_SESSION['position_id'] != 1 && $_SESSION['position_id'] != 2) {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์ในการยกเลิกการชำระเงิน']);
    exit;
}

$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;

if ($order_id == 0) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลคำสั่งซื้อ']);
    exit;
}

// ดึงข้อมูลสลิปการชำระเงินก่อนอัปเดต
$sql_get_slip = "SELECT payment_proofs FROM order_course WHERE oc_id = ?";
$stmt_get_slip = $conn->prepare($sql_get_slip);
$stmt_get_slip->bind_param("i", $order_id);
$stmt_get_slip->execute();
$result = $stmt_get_slip->get_result();
$old_payment_proof = '';
if ($row = $result->fetch_assoc()) {
    $old_payment_proof = $row['payment_proofs'];
}
$stmt_get_slip->close();

// อัปเดตข้อมูลในฐานข้อมูล
$sql = "UPDATE order_course SET order_payment = 'ยังไม่จ่ายเงิน', order_payment_date = NULL, payment_proofs = NULL WHERE oc_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $order_id);
    if ($stmt->execute()) {
        // ลบไฟล์สลิปการชำระเงิน (ถ้ามี)
        if (!empty($old_payment_proof)) {
            $file_path = '../../img/payment-proofs/' . $old_payment_proof;
            if (file_exists($file_path)) {
                if (unlink($file_path)) {
                    error_log("File deleted: $file_path");
                } else {
                    error_log("Failed to delete file: $file_path");
                }
            } else {
                error_log("File not found: $file_path");
            }
        }

        echo json_encode(['success' => true, 'message' => 'ยกเลิกการชำระเงินสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: ' . $conn->error]);
}

$conn->close();