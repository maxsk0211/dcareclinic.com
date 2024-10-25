<?php
require_once '../../dbcon.php';
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // รับค่าจาก POST request
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $usage_date = isset($_POST['usage_date']) ? $_POST['usage_date'] : null;
    $usage_count = isset($_POST['usage_count']) ? intval($_POST['usage_count']) : 1;
    $notes = isset($_POST['notes']) ? $_POST['notes'] : '';

    // ตรวจสอบข้อมูล
    if ($course_id === 0 || $order_id === 0 || $usage_date === null) {
        throw new Exception('กรุณาระบุข้อมูลให้ครบถ้วน');
    }

    $conn->begin_transaction();

    // 1. หา order_detail_id และตรวจสอบจำนวนครั้งที่เหลือ
    $check_sql = "SELECT od.od_id, od.used_sessions, (c.course_amount * od.od_amount) as total_sessions
                  FROM order_detail od
                  JOIN course c ON od.course_id = c.course_id
                  WHERE od.course_id = ? AND od.oc_id = ?";
    
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        throw new Exception("Error preparing check statement: " . $conn->error);
    }

    $check_stmt->bind_param("ii", $course_id, $order_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $usage_data = $result->fetch_assoc();

    if (!$usage_data) {
        throw new Exception('ไม่พบข้อมูลคอร์ส');
    }

    $remaining = $usage_data['total_sessions'] - $usage_data['used_sessions'];
    if ($usage_count > $remaining) {
        throw new Exception("จำนวนครั้งที่ใช้เกินกว่าที่เหลืออยู่ ($remaining ครั้ง)");
    }

    // 2. บันทึกการใช้บริการ
    $insert_sql = "INSERT INTO course_usage (order_detail_id, usage_date, usage_count, notes) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    if (!$insert_stmt) {
        throw new Exception("Error preparing insert statement: " . $conn->error);
    }

    $insert_stmt->bind_param("isis", $usage_data['od_id'], $usage_date, $usage_count, $notes);
    if (!$insert_stmt->execute()) {
        throw new Exception("Error executing insert: " . $insert_stmt->error);
    }

    // 3. อัพเดทจำนวนครั้งที่ใช้
    $update_sql = "UPDATE order_detail SET used_sessions = used_sessions + ? WHERE od_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    if (!$update_stmt) {
        throw new Exception("Error preparing update statement: " . $conn->error);
    }

    $update_stmt->bind_param("ii", $usage_count, $usage_data['od_id']);
    if (!$update_stmt->execute()) {
        throw new Exception("Error executing update: " . $update_stmt->error);
    }

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'บันทึกการใช้บริการเรียบร้อยแล้ว',
        'data' => [
            'used' => $usage_count,
            'remaining' => $remaining - $usage_count
        ]
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
    if (isset($check_stmt)) $check_stmt->close();
    if (isset($insert_stmt)) $insert_stmt->close();
    if (isset($update_stmt)) $update_stmt->close();
    if (isset($conn)) $conn->close();
}
?>