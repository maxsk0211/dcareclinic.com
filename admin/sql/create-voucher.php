<?php
session_start();
require_once '../../dbcon.php';
header('Content-Type: application/json; charset=utf-8');

try {
    // ตรวจสอบข้อมูลที่จำเป็น
    if (!isset($_POST['amount']) || !isset($_POST['expire_date']) || !isset($_POST['discount_type'])) {
        throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน');
    }

    $amount = floatval($_POST['amount']);
    $expire_date = $_POST['expire_date'];
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    $discount_type = $_POST['discount_type'];
    $max_discount = isset($_POST['max_discount']) && !empty($_POST['max_discount']) ? 
                   floatval($_POST['max_discount']) : null;

    // ตรวจสอบความถูกต้องของข้อมูล
    if ($amount <= 0) {
        throw new Exception('มูลค่าต้องมากกว่า 0');
    }

    // ตรวจสอบกรณีเป็นเปอร์เซ็นต์
    if ($discount_type === 'percent') {
        if ($amount > 100) {
            throw new Exception('เปอร์เซ็นต์ส่วนลดต้องไม่เกิน 100%');
        }
    }

    // สร้างรหัสบัตรกำนัล
    function generateVoucherCode() {
        $prefix = 'GV';
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < 12; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $code;
    }

    // สร้างรหัสที่ไม่ซ้ำ
    do {
        $voucher_code = generateVoucherCode();
        $check = $conn->prepare("SELECT 1 FROM gift_vouchers WHERE voucher_code = ?");
        $check->bind_param("s", $voucher_code);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();
    } while ($exists);

    // เตรียม SQL
    $sql = "INSERT INTO gift_vouchers (
        voucher_code, 
        amount,
        max_discount,
        discount_type,
        expire_date,
        status,
        notes,
        created_by,
        created_at
    ) VALUES (?, ?, ?, ?, ?, 'unused', ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param(
        "sddsssi",
        $voucher_code,
        $amount,
        $max_discount,
        $discount_type,
        $expire_date,
        $notes,
        $_SESSION['users_id']
    );

    if (!$stmt->execute()) {
        throw new Exception("Error executing statement: " . $stmt->error);
    }

    $voucher_id = $stmt->insert_id;

    echo json_encode([
        'success' => true,
        'message' => 'สร้างบัตรกำนัลสำเร็จ',
        'voucher_id' => $voucher_id,
        'voucher_code' => $voucher_code
    ]);

} catch (Exception $e) {
    error_log("Create Voucher Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

if (isset($stmt)) {
    $stmt->close();
}
if (isset($conn)) {
    $conn->close();
}
?>