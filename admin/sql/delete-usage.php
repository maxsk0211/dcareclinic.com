<?php
session_start();
require_once '../../dbcon.php';
header('Content-Type: application/json');

// เพิ่ม error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตรวจสอบสิทธิ์
if ($_SESSION['position_id'] != 1 && $_SESSION['position_id'] != 2) {
    echo json_encode([
        'success' => false,
        'message' => 'คุณไม่มีสิทธิ์ในการลบข้อมูล'
    ]);
    exit;
}

try {
    // รับค่าพารามิเตอร์
    $usage_id = isset($_POST['usage_id']) ? intval($_POST['usage_id']) : 0;
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $usage_count = isset($_POST['usage_count']) ? intval($_POST['usage_count']) : 0;
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $order_detail_id = isset($_POST['order_detail_id']) ? intval($_POST['order_detail_id']) : 0;

    // ตรวจสอบความครบถ้วนของข้อมูล
    if (!$usage_id || !$course_id || !$usage_count || !$order_id || !$order_detail_id) {
        $missing_data = [];
        if (!$usage_id) $missing_data[] = 'usage_id';
        if (!$course_id) $missing_data[] = 'course_id';
        if (!$usage_count) $missing_data[] = 'usage_count';
        if (!$order_id) $missing_data[] = 'order_id';
        if (!$order_detail_id) $missing_data[] = 'order_detail_id';
        
        throw new Exception('ข้อมูลไม่ครบถ้วน (Missing: ' . implode(', ', $missing_data) . ')');
    }

    $conn->begin_transaction();

    // ลบข้อมูลการใช้บริการ
    $delete_sql = "DELETE FROM course_usage WHERE id = ? AND order_detail_id = ?";
    $stmt = $conn->prepare($delete_sql);
    if (!$stmt) {
        throw new Exception("เตรียมคำสั่ง SQL ไม่สำเร็จ: " . $conn->error);
    }

    $stmt->bind_param("ii", $usage_id, $order_detail_id);
    if (!$stmt->execute()) {
        throw new Exception("ไม่สามารถลบข้อมูลได้: " . $stmt->error);
    }

    if ($stmt->affected_rows === 0) {
        throw new Exception("ไม่พบข้อมูลที่ต้องการลบ");
    }

    // อัพเดทจำนวนครั้งที่ใช้ใน order_detail
    $update_sql = "UPDATE order_detail 
                   SET used_sessions = used_sessions - ? 
                   WHERE od_id = ? AND course_id = ?";
    $stmt_update = $conn->prepare($update_sql);
    if (!$stmt_update) {
        throw new Exception("เตรียมคำสั่ง SQL ไม่สำเร็จ: " . $conn->error);
    }

    $stmt_update->bind_param("iii", $usage_count, $order_detail_id, $course_id);
    if (!$stmt_update->execute()) {
        throw new Exception("ไม่สามารถอัพเดทข้อมูลได้: " . $stmt_update->error);
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'ลบข้อมูลสำเร็จ'
    ]);

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($stmt_update)) $stmt_update->close();
    if (isset($conn)) $conn->close();
}
?>