<?php
require_once '../../dbcon.php';
header('Content-Type: application/json');

$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

try {
    $sql = "SELECT cu.id, cu.usage_date, cu.usage_count, cu.notes, 
            cu.order_detail_id, od.course_id
            FROM course_usage cu
            JOIN order_detail od ON cu.order_detail_id = od.od_id
            WHERE od.oc_id = ? AND od.course_id = ?
            ORDER BY cu.usage_date DESC";
                
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $order_id, $course_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $usage_history = array();
    
    while ($row = $result->fetch_assoc()) {
        $usage_history[] = array(
            'id' => $row['id'],
            'usage_date' => $row['usage_date'],
            'usage_count' => $row['usage_count'],
            'notes' => $row['notes'],
            'order_detail_id' => $row['order_detail_id'],
            'course_id' => $row['course_id']
        );
    }
    
    echo json_encode($usage_history);

} catch (Exception $e) {
    error_log("Error in get-usage-details.php: " . $e->getMessage());
    echo json_encode([]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>