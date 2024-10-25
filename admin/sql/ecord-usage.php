<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// ตรวจสอบว่ามีการล็อกอินหรือไม่
if (!isset($_SESSION['users_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
$usage_date = isset($_POST['usage_date']) ? $_POST['usage_date'] : null;
$notes = isset($_POST['notes']) ? $_POST['notes'] : '';

if (!$order_id || !$course_id || !$usage_date) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $conn->begin_transaction();

    // ดึง od_id จาก order_detail
    $stmt = $conn->prepare("SELECT od_id FROM order_detail WHERE oc_id = ? AND course_id = ?");
    $stmt->bind_param("ii", $order_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Order detail not found");
    }
    $row = $result->fetch_assoc();
    $order_detail_id = $row['od_id'];

    // เพิ่มข้อมูลการใช้บริการใหม่
    $stmt = $conn->prepare("INSERT INTO course_usage (order_detail_id, usage_date, notes) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $order_detail_id, $usage_date, $notes);
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert usage record");
    }

    // อัปเดตจำนวนครั้งที่ใช้บริการใน order_detail
    $stmt = $conn->prepare("UPDATE order_detail SET used_sessions = used_sessions + 1 WHERE od_id = ?");
    $stmt->bind_param("i", $order_detail_id);
    if (!$stmt->execute()) {
        throw new Exception("Failed to update used sessions");
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Usage recorded successfully']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $conn->close();
}