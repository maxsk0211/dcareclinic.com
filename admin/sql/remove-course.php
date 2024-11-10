<?php
require '../../dbcon.php';

$order_id = intval($_POST['order_id']);
$od_id = intval($_POST['od_id']);  // รับค่า od_id แทน course_id

// เริ่ม transaction
$conn->begin_transaction();

try {
    // 1. ลบข้อมูลการใช้งานคอร์สก่อน (course_usage)
    $sql_delete_usage = "DELETE FROM course_usage WHERE order_detail_id = ?";
    $stmt_usage = $conn->prepare($sql_delete_usage);
    $stmt_usage->bind_param("i", $od_id);
    $stmt_usage->execute();

    // 2. ลบข้อมูลทรัพยากรที่เกี่ยวข้อง
    $sql_resources = "DELETE ocr FROM order_course_resources ocr
                     INNER JOIN order_detail od ON od.course_id = ocr.course_id 
                     WHERE od.od_id = ? AND ocr.order_id = ?";
    $stmt_resources = $conn->prepare($sql_resources);
    $stmt_resources->bind_param("ii", $od_id, $order_id);
    $stmt_resources->execute();

    // 3. ลบข้อมูลจาก order_detail
    $sql = "DELETE FROM order_detail WHERE od_id = ? AND oc_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $od_id, $order_id);
    $stmt->execute();

    // Commit transaction
    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    // Rollback transaction ในกรณีที่เกิดข้อผิดพลาด
    $conn->rollback();
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}

// ปิดการเชื่อมต่อ
if (isset($stmt_usage)) $stmt_usage->close();
if (isset($stmt_resources)) $stmt_resources->close();
if (isset($stmt)) $stmt->close();
$conn->close();
?>