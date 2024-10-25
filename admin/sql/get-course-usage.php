<?php
require_once '../../dbcon.php';
header('Content-Type: application/json');

// เพิ่ม error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($course_id === 0 || $order_id === 0) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

try {
    $sql = "
        SELECT od.used_sessions, c.course_amount AS total_sessions
        FROM order_detail od
        JOIN course c ON od.course_id = c.course_id
        WHERE od.course_id = ? AND od.oc_id = ?
    ";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("ii", $course_id, $order_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        echo json_encode([
            'used_sessions' => intval($data['used_sessions']),
            'total_sessions' => intval($data['total_sessions'])
        ]);
    } else {
        echo json_encode(['error' => 'Course not found in this order']);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}