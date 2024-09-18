<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
session_start();
require '../../dbcon.php';
file_put_contents('debug.log', print_r($_POST, true) . "\n" . print_r($_FILES, true), FILE_APPEND);
// ตรวจสอบว่าได้รับไฟล์หรือไม่
if(isset($_FILES['deposit_slip'])) {
    file_put_contents('debug.log', "File received\n", FILE_APPEND);
} else {
    file_put_contents('debug.log', "No file received\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = intval($_POST['order_id']);
    $deposit_amount = floatval($_POST['deposit_amount']);
    $deposit_payment_type = $_POST['deposit_payment_type'];
    
$deposit_slip_image = null;
if ($deposit_payment_type == 'เงินโอน' && isset($_FILES['deposit_slip']) && $_FILES['deposit_slip']['error'] == 0) {
    $target_dir = "../../img/payment-proofs/";
    $file_extension = pathinfo($_FILES["deposit_slip"]["name"], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Log ข้อมูลไฟล์
    file_put_contents('debug.log', "Attempting to move file to: $target_file\n", FILE_APPEND);
    
    if (move_uploaded_file($_FILES["deposit_slip"]["tmp_name"], $target_file)) {
        $deposit_slip_image = $new_filename;
        file_put_contents('debug.log', "File moved successfully\n", FILE_APPEND);
    } else {
        $error = error_get_last();
        file_put_contents('debug.log', "Failed to move file: " . $error['message'] . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'message' => 'Failed to upload file: ' . $error['message']]);
        exit;
    }
}else{
    // ถ้าไม่มีการอัพโหลดไฟล์ใหม่ ให้ใช้ชื่อไฟล์เดิม (ถ้ามี)
    $deposit_slip_image = $conn->query("SELECT deposit_slip_image FROM order_course WHERE oc_id = $order_id")->fetch_assoc()['deposit_slip_image'];
}

    $sql = "UPDATE order_course SET 
            deposit_amount = ?, 
            deposit_payment_type = ?, 
            deposit_slip_image = ?, 
            deposit_date = NOW() 
            WHERE oc_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dssi", $deposit_amount, $deposit_payment_type, $deposit_slip_image, $order_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();