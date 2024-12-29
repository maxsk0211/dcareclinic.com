<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();

    try {
        // รับและทำความสะอาดข้อมูล
        $acc_name = mysqli_real_escape_string($conn, $_POST['acc_name']);
        $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
        $acc_type_id = mysqli_real_escape_string($conn, $_POST['acc_type_id']);
        $acc_properties = mysqli_real_escape_string($conn, $_POST['acc_properties']);
        $acc_unit_id = mysqli_real_escape_string($conn, $_POST['acc_unit_id']);
        $acc_status = mysqli_real_escape_string($conn, $_POST['acc_status']);

        // SQL สำหรับเพิ่มข้อมูล
        $sql = "INSERT INTO accessories (acc_name, branch_id, acc_type_id, acc_properties, acc_unit_id, acc_status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL");
        }

        $stmt->bind_param("siisis", 
            $acc_name, $branch_id, $acc_type_id, $acc_properties, $acc_unit_id, $acc_status
        );

        if (!$stmt->execute()) {
            throw new Exception("ไม่สามารถบันทึกข้อมูลได้");
        }

        $new_acc_id = $stmt->insert_id;

        // ดึงข้อมูลประเภทอุปกรณ์
        $type_sql = "SELECT acc_type_name FROM acc_type WHERE acc_type_id = ?";
        $stmt_type = $conn->prepare($type_sql);
        $stmt_type->bind_param("i", $acc_type_id);
        $stmt_type->execute();
        $type_result = $stmt_type->get_result();
        $type_data = $type_result->fetch_object();

        // เตรียมข้อมูลสำหรับ log
        $log_details = json_encode([
            'acc_name' => $acc_name,
            'acc_type' => $type_data->acc_type_name,
            'properties' => $acc_properties,
            'unit_id' => $acc_unit_id,
            'status' => $acc_status,
        ], JSON_UNESCAPED_UNICODE);

        // บันทึก log
        $log_sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, branch_id) 
                    VALUES (?, 'create', 'accessory', ?, ?, ?)";
        
        $stmt_log = $conn->prepare($log_sql);
        if (!$stmt_log) {
            throw new Exception("เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL สำหรับ log");
        }

        $user_id = $_SESSION['users_id'];
        $stmt_log->bind_param("iisi", $user_id, $new_acc_id, $log_details, $branch_id);

        if (!$stmt_log->execute()) {
            throw new Exception("ไม่สามารถบันทึก log ได้");
        }

        $conn->commit();
        $_SESSION['msg_ok'] = "เพิ่มข้อมูลอุปกรณ์เรียบร้อยแล้ว";

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }

    // ปิดการเชื่อมต่อ
    if (isset($stmt)) $stmt->close();
    if (isset($stmt_type)) $stmt_type->close();
    if (isset($stmt_log)) $stmt_log->close();
    $conn->close();
}

header("Location: ../accessories.php");
exit();
?>