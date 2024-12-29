<?php
session_start();
require '../../dbcon.php';

// กรณีรับข้อมูล POST จาก AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // ตรวจสอบข้อมูลที่ส่งมา
    if (!isset($_POST['tool_id'], $_POST['password'], $_POST['reason'])) {
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
        exit;
    }

    $tool_id = mysqli_real_escape_string($conn, $_POST['tool_id']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $user_id = $_SESSION['users_id'];

    // ตรวจสอบรหัสผ่าน
    $password_check_sql = "SELECT * FROM users WHERE users_id = '$user_id' AND users_password = '$password'";
    $password_result = mysqli_query($conn, $password_check_sql);

    if (mysqli_num_rows($password_result) == 0) {
        echo json_encode(['success' => false, 'message' => 'รหัสผ่านไม่ถูกต้อง']);
        exit;
    }

    // Get tool info before deletion for logging
    $tool_sql = "SELECT * FROM tool WHERE tool_id = '$tool_id'";
    $tool_result = mysqli_query($conn, $tool_sql);
    $tool_data = mysqli_fetch_assoc($tool_result);

    if (!$tool_data) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลเครื่องมือที่ต้องการลบ']);
        exit;
    }

    mysqli_begin_transaction($conn);
    
    try {
        // Delete the tool
        $delete_sql = "DELETE FROM tool WHERE tool_id = '$tool_id'";
        
        if (mysqli_query($conn, $delete_sql)) {
            // Log the deletion
            $details = json_encode([
                'reason' => $reason,
                'deleted_data' => [
                    'tool_id' => $tool_data['tool_id'],
                    'tool_name' => $tool_data['tool_name'],
                    'tool_detail' => $tool_data['tool_detail']
                ]
            ]);
            
            $log_sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, branch_id) 
                       VALUES ('$user_id', 'delete', 'tool', '$tool_id', '$details', '{$tool_data['branch_id']}')";
                       
            if (!mysqli_query($conn, $log_sql)) {
                throw new Exception("Error logging deletion");
            }
            
            mysqli_commit($conn);
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Error deleting tool");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage()]);
    }

    mysqli_close($conn);
    exit;
}

// กรณีเรียกหน้าโดยตรง
header("Location: ../tool.php");
exit;
?>