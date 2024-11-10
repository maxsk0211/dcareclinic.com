<?php
session_start();
require_once '../../dbcon.php';
header('Content-Type: application/json; charset=utf-8');

try {
    // ตรวจสอบ session
    if (!isset($_SESSION['users_id'])) {
        throw new Exception('กรุณาเข้าสู่ระบบใหม่');
    }

    // ตรวจสอบสิทธิ์
    // if ($_SESSION['position_id'] > 2) {
    //     throw new Exception('ไม่มีสิทธิ์ยกเลิกบัตรกำนัล');
    // }

    // ตรวจสอบข้อมูลที่ส่งมา
    if (!isset($_POST['voucher_id'])) {
        throw new Exception('ไม่พบรหัสบัตรกำนัล');
    }
    if (!isset($_POST['cancel_reason']) || empty($_POST['cancel_reason'])) {
        throw new Exception('กรุณาระบุเหตุผลในการยกเลิก');
    }

    $voucher_id = intval($_POST['voucher_id']);
    $cancel_reason = trim($_POST['cancel_reason']);
    $current_date = date('Y-m-d H:i:s');

    // เริ่ม transaction
    $conn->begin_transaction();

    try {
        // ตรวจสอบว่าบัตรกำนัลมีอยู่จริงและยังไม่ถูกใช้งาน
        $check_sql = "SELECT status, notes FROM gift_vouchers 
                     WHERE voucher_id = ?
                     FOR UPDATE";
        $check_stmt = $conn->prepare($check_sql);
        if (!$check_stmt) {
            throw new Exception("Error preparing check statement: " . $conn->error);
        }

        $check_stmt->bind_param("i", $voucher_id);
        if (!$check_stmt->execute()) {
            throw new Exception("Error executing check statement: " . $check_stmt->error);
        }

        $result = $check_stmt->get_result();
        if ($result->num_rows === 0) {
            throw new Exception('ไม่พบข้อมูลบัตรกำนัล');
        }

        $voucher = $result->fetch_assoc();
        if ($voucher['status'] !== 'unused') {
            throw new Exception('ไม่สามารถยกเลิกบัตรกำนัลที่ถูกใช้งานแล้วหรือหมดอายุ');
        }

        // สร้างข้อความหมายเหตุใหม่
        $new_notes = $voucher['notes'] ? 
            $voucher['notes'] . "\n---\n" : '';
        $new_notes .= "ยกเลิกเมื่อ: " . $current_date . "\n";
        $new_notes .= "เหตุผล: " . $cancel_reason . "\n";
        $new_notes .= "ยกเลิกโดย: " . $_SESSION['users_fname'] . ' ' . $_SESSION['users_lname'];

        // อัพเดทสถานะบัตรกำนัล
        $update_sql = "UPDATE gift_vouchers 
                      SET status = 'expired',
                          notes = ?
                      WHERE voucher_id = ?";
        
        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            throw new Exception("Error preparing update statement: " . $conn->error);
        }

        $update_stmt->bind_param("si", $new_notes, $voucher_id);
        if (!$update_stmt->execute()) {
            throw new Exception("Error executing update statement: " . $update_stmt->error);
        }

        if ($update_stmt->affected_rows === 0) {
            throw new Exception('ไม่สามารถอัพเดทสถานะบัตรกำนัล');
        }

        // บันทึก log
        $log_sql = "INSERT INTO activity_logs 
                    (user_id, action, entity_type, entity_id, details, branch_id, created_at) 
                    VALUES (?, 'cancel', 'voucher', ?, ?, ?, NOW())";
        
        $log_details = json_encode([
            'reason' => $cancel_reason,
            'cancelled_at' => $current_date,
            'cancelled_by' => $_SESSION['users_fname'] . ' ' . $_SESSION['users_lname']
        ], JSON_UNESCAPED_UNICODE);

        $log_stmt = $conn->prepare($log_sql);
        if ($log_stmt) {
            $log_stmt->bind_param("iiis", 
                $_SESSION['users_id'], 
                $voucher_id, 
                $log_details,
                $_SESSION['branch_id']
            );
            $log_stmt->execute();
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'ยกเลิกบัตรกำนัลเรียบร้อยแล้ว'
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// ปิดการเชื่อมต่อ
if (isset($check_stmt)) $check_stmt->close();
if (isset($update_stmt)) $update_stmt->close();
if (isset($log_stmt)) $log_stmt->close();
if ($conn) $conn->close();
?>