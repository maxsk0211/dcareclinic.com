<?php
session_start();
// include 'chk-session.php';
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if (isset($data['queue_id']) && isset($data['selected_courses'])) {
        $queue_id = intval($data['queue_id']);
        $selected_courses = $data['selected_courses'];

        if ($queue_id === 0 || empty($selected_courses)) {
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้องหรือไม่ครบถ้วน']);
            exit;
        }

        $conn->begin_transaction();

        try {
            foreach ($selected_courses as $od_id) {
                $insert_sql = "INSERT INTO course_usage (od_id, queue_id) VALUES (?, ?)";
                $stmt = $conn->prepare($insert_sql);
                $stmt->bind_param("ii", $od_id, $queue_id);
                $stmt->execute();
            }

            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'บันทึกการใช้บริการสำเร็จ']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();