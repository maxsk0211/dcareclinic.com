<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์มและทำการ escape
    $tool_name = mysqli_real_escape_string($conn, $_POST['tool_name']);
    $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
    $tool_detail = mysqli_real_escape_string($conn, $_POST['tool_detail']);
    $tool_amount = mysqli_real_escape_string($conn, 0);
    $tool_unit_id = mysqli_real_escape_string($conn, $_POST['tool_unit_id']);
    $tool_status = mysqli_real_escape_string($conn, $_POST['tool_status']);

    // ตรวจสอบว่าชื่อเครื่องมือซ้ำหรือไม่
    $check_sql = "SELECT * FROM tool WHERE tool_name = '$tool_name' AND branch_id = '$branch_id'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['msg_error'] = "มีเครื่องมือชื่อนี้ในสาขานี้อยู่แล้ว กรุณาตรวจสอบอีกครั้ง";
    } else {
        // เริ่ม transaction
        mysqli_begin_transaction($conn);
        
        try {
            // เพิ่มข้อมูลเครื่องมือ
            $sql = "INSERT INTO tool (tool_name, branch_id, tool_detail, tool_amount, tool_unit_id, tool_status) 
                    VALUES ('$tool_name', '$branch_id', '$tool_detail', '$tool_amount', '$tool_unit_id', '$tool_status')";
            
            if (mysqli_query($conn, $sql)) {
                $tool_id = mysqli_insert_id($conn);
                
                // บันทึก log
                $user_id = $_SESSION['users_id'];
                $details = json_encode([
                    'tool_name' => $tool_name,
                    'branch_id' => $branch_id,
                    'tool_detail' => $tool_detail,
                    'tool_amount' => $tool_amount,
                    'tool_unit_id' => $tool_unit_id,
                    'tool_status' => $tool_status
                ]);
                
                $log_sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, branch_id) 
                           VALUES ('$user_id', 'create', 'tool', '$tool_id', '$details', '$branch_id')";
                           
                mysqli_query($conn, $log_sql);
                
                mysqli_commit($conn);
                $_SESSION['msg_ok'] = "เพิ่มเครื่องมือแพทย์ใหม่เรียบร้อยแล้ว";
            } else {
                throw new Exception("Error inserting tool: " . mysqli_error($conn));
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . $e->getMessage();
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