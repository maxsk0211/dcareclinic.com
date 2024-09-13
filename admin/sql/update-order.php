<?php
session_start();
include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $order_payment = isset($_POST['order_payment']) ? $_POST['order_payment'] : '';

    if ($order_id === 0 || empty($order_payment)) {
        $_SESSION['msg_error'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
        header("Location: ../edit-order.php?id=" . $order_id);
        exit();
    }

    $payment_proofs = '';
    $order_payment_date = null;

    // จัดการกับการอัพโหลดไฟล์
    if ($order_payment === 'เงินโอน' && isset($_FILES['payment_slip']) && $_FILES['payment_slip']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['payment_slip']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['payment_slip']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($file_ext, $allowed_extensions)) {
            // สร้างชื่อไฟล์ใหม่
            $new_file_name = uniqid('slip_', true) . '_' . bin2hex(random_bytes(8)) . '.' . $file_ext;
            $upload_path = '../../img/payment-proofs/' . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $payment_proofs = $new_file_name;
            } else {
                $_SESSION['msg_error'] = "ไม่สามารถอัพโหลดไฟล์ได้";
                header("Location: ../edit-order.php?id=" . $order_id);
                exit();
            }
        } else {
            $_SESSION['msg_error'] = "กรุณาอัพโหลดไฟล์รูปภาพเท่านั้น (jpg, jpeg, png, gif)";
            header("Location: ../edit-order.php?id=" . $order_id);
            exit();
        }
    }

    // กำหนดวันที่ชำระเงิน
    if (in_array($order_payment, ['เงินสด', 'เงินโอน', 'บัตรเครดิต'])) {
        $order_payment_date = date('Y-m-d H:i:s');
    }

    // อัปเดตข้อมูลในฐานข้อมูล
    $sql = "UPDATE order_course SET order_payment = ?, payment_proofs = ?, order_payment_date = ? WHERE oc_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssi", $order_payment, $payment_proofs, $order_payment_date, $order_id);
        if ($stmt->execute()) {
            $_SESSION['msg_ok'] = "อัปเดตข้อมูลสำเร็จ";
        } else {
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error;
    }

    $conn->close();

    header("Location: ../edit-order.php?id=" . $order_id);
    exit();
} else {
    header("Location: ../index.php");
    exit();
}
?>