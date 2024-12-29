<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn->begin_transaction();

    try {
        // รับค่าและทำความสะอาดข้อมูล
        $acc_id = mysqli_real_escape_string($conn, $_POST['acc_id']);
        $acc_name = mysqli_real_escape_string($conn, $_POST['acc_name']);
        $branch_id = mysqli_real_escape_string($conn, $_POST['branch_id']);
        $acc_type_id = mysqli_real_escape_string($conn, $_POST['acc_type_id']);
        $acc_properties = mysqli_real_escape_string($conn, $_POST['acc_properties']);
        $acc_unit_id = mysqli_real_escape_string($conn, $_POST['acc_unit_id']);
        $acc_status = mysqli_real_escape_string($conn, $_POST['acc_status']);

        // ดึงข้อมูลเดิมก่อนอัพเดท
        $old_data_sql = "SELECT a.*, at.acc_type_name 
                        FROM accessories a
                        LEFT JOIN acc_type at ON a.acc_type_id = at.acc_type_id
                        WHERE a.acc_id = ?";
        $stmt_old = $conn->prepare($old_data_sql);
        $stmt_old->bind_param("i", $acc_id);
        $stmt_old->execute();
        $old_data = $stmt_old->get_result()->fetch_assoc();

        // อัพเดทข้อมูล
        $sql = "UPDATE accessories SET 
                acc_name = ?, 
                branch_id = ?, 
                acc_type_id = ?, 
                acc_properties = ?, 
                acc_unit_id = ?,
                acc_status = ? 
                WHERE acc_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siisiii", 
            $acc_name, $branch_id, $acc_type_id, $acc_properties,
            $acc_unit_id, $acc_status, $acc_id
        );

        if (!$stmt->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการอัพเดทข้อมูล");
        }

        // ดึงข้อมูลประเภทอุปกรณ์ใหม่
        $type_sql = "SELECT acc_type_name FROM acc_type WHERE acc_type_id = ?";
        $stmt_type = $conn->prepare($type_sql);
        $stmt_type->bind_param("i", $acc_type_id);
        $stmt_type->execute();
        $new_type = $stmt_type->get_result()->fetch_object()->acc_type_name;

        // เตรียมข้อมูลการเปลี่ยนแปลงสำหรับ log
        $changes = [];
        if ($old_data['acc_name'] !== $acc_name) {
            $changes['acc_name'] = ['from' => $old_data['acc_name'], 'to' => $acc_name];
        }
        if ($old_data['acc_type_name'] !== $new_type) {
            $changes['acc_type'] = ['from' => $old_data['acc_type_name'], 'to' => $new_type];
        }
        if ($old_data['acc_properties'] !== $acc_properties) {
            $changes['properties'] = ['from' => $old_data['acc_properties'], 'to' => $acc_properties];
        }
        if ($old_data['acc_status'] != $acc_status) {
            $changes['status'] = ['from' => $old_data['acc_status'], 'to' => $acc_status];
        }

        // บันทึก log ถ้ามีการเปลี่ยนแปลง
        if (!empty($changes)) {
            $log_details = json_encode([
                'changes' => $changes,
                'acc_name' => $acc_name,
                'acc_code' => 'ACC-' . str_pad($acc_id, 6, '0', STR_PAD_LEFT)
            ], JSON_UNESCAPED_UNICODE);

            $log_sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, branch_id) 
                        VALUES (?, 'update', 'accessory', ?, ?, ?)";
            
            $stmt_log = $conn->prepare($log_sql);
            $user_id = $_SESSION['users_id'];
            $stmt_log->bind_param("iisi", $user_id, $acc_id, $log_details, $branch_id);
            
            if (!$stmt_log->execute()) {
                throw new Exception("เกิดข้อผิดพลาดในการบันทึก log");
            }
        }

        $conn->commit();
        $_SESSION['msg_ok'] = "อัพเดทข้อมูลอุปกรณ์เรียบร้อยแล้ว";

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }

    // ปิดการเชื่อมต่อ
    if (isset($stmt)) $stmt->close();
    if (isset($stmt_old)) $stmt_old->close();
    if (isset($stmt_type)) $stmt_type->close();
    if (isset($stmt_log)) $stmt_log->close();
    $conn->close();
}

header("Location: ../accessories.php");
exit();
?>