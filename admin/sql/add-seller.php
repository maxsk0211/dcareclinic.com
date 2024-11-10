<?php
session_start();
require '../../dbcon.php';

// ตรวจสอบว่ามีการส่งข้อมูลที่จำเป็นครบหรือไม่
if (!isset($_POST['service_id']) || !isset($_POST['staff_id']) || 
    !isset($_POST['commission_amount']) || !isset($_POST['commission_type'])) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$service_id = intval($_POST['service_id']);
$staff_id = intval($_POST['staff_id']);
$commission_amount = floatval($_POST['commission_amount']);
$commission_type = $_POST['commission_type'];
$branch_id = $_SESSION['branch_id'];

// ตรวจสอบว่าผู้ขายคนนี้ถูกเพิ่มไปแล้วหรือยัง
$check_sql = "SELECT staff_record_id 
              FROM service_staff_records 
              WHERE service_id = ? 
              AND staff_id = ? 
              AND staff_type = 'seller'";

$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $service_id, $staff_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'ผู้ขายคนนี้ถูกเพิ่มไปแล้ว']);
    exit;
}

// เพิ่มข้อมูลผู้ขาย
$sql = "INSERT INTO service_staff_records 
        (service_id, staff_id, staff_type, staff_df, staff_df_type, branch_id) 
        VALUES (?, ?, 'seller', ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iidsi", $service_id, $staff_id, $commission_amount, $commission_type, $branch_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'เพิ่มผู้ขายสำเร็จ']);
} else {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเพิ่มข้อมูล: ' . $conn->error]);
}

$stmt->close();
$check_stmt->close();
$conn->close();