<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tool_id = mysqli_real_escape_string($conn, $_POST['tool_id']);
    $tool_name = mysqli_real_escape_string($conn, $_POST['tool_name']);
    $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
    $tool_detail = mysqli_real_escape_string($conn, $_POST['tool_detail']);
    $tool_unit_id = mysqli_real_escape_string($conn, $_POST['tool_unit_id']);
    $tool_status = mysqli_real_escape_string($conn, $_POST['tool_status']);

    // Get original data for comparison
    $original_sql = "SELECT * FROM tool WHERE tool_id = '$tool_id'";
    $original_result = mysqli_query($conn, $original_sql);
    $original_data = mysqli_fetch_assoc($original_result);

    // Check for duplicate name
    $check_sql = "SELECT * FROM tool WHERE tool_name = '$tool_name' AND branch_id = '$branch_id' AND tool_id != '$tool_id'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['msg_error'] = "มีเครื่องมือชื่อนี้ในสาขานี้อยู่แล้ว";
    } else {
        mysqli_begin_transaction($conn);
        
        try {
            $sql = "UPDATE tool SET 
                    tool_name = '$tool_name', 
                    branch_id = '$branch_id', 
                    tool_detail = '$tool_detail', 
                    tool_unit_id = '$tool_unit_id', 
                    tool_status = '$tool_status' 
                    WHERE tool_id = '$tool_id'";

            if (mysqli_query($conn, $sql)) {
                // Record changes for logging
                $changes = array();
                if ($original_data['tool_name'] != $tool_name) $changes['tool_name'] = ['from' => $original_data['tool_name'], 'to' => $tool_name];
                if ($original_data['branch_id'] != $branch_id) $changes['branch_id'] = ['from' => $original_data['branch_id'], 'to' => $branch_id];
                if ($original_data['tool_detail'] != $tool_detail) $changes['tool_detail'] = ['from' => $original_data['tool_detail'], 'to' => $tool_detail];
                if ($original_data['tool_unit_id'] != $tool_unit_id) $changes['tool_unit_id'] = ['from' => $original_data['tool_unit_id'], 'to' => $tool_unit_id];
                if ($original_data['tool_status'] != $tool_status) $changes['tool_status'] = ['from' => $original_data['tool_status'], 'to' => $tool_status];

                // Log the update
                $user_id = $_SESSION['users_id'];
                $details = json_encode([
                    'changes' => $changes,
                    'tool_name' => $tool_name
                ]);
                
                $log_sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, branch_id) 
                           VALUES ('$user_id', 'update', 'tool', '$tool_id', '$details', '$branch_id')";
                           
                mysqli_query($conn, $log_sql);
                
                mysqli_commit($conn);
                $_SESSION['msg_ok'] = "อัพเดตข้อมูลเครื่องมือแพทย์เรียบร้อยแล้ว";
            } else {
                throw new Exception("Error updating tool: " . mysqli_error($conn));
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        }
    }
} else {
    $_SESSION['msg_error'] = "ไม่พบข้อมูลที่ส่งมา";
}
// exit();
mysqli_close($conn);
header("Location: ../tool.php");
exit();
?>