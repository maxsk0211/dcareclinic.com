<?php
session_start();
require_once '../../dbcon.php';
header('Content-Type: application/json; charset=utf-8');

try {
    if (!isset($_GET['voucher_id'])) {
        throw new Exception('ไม่พบรหัสบัตรกำนัล');
    }

    $voucher_id = intval($_GET['voucher_id']);

    // ดึงข้อมูลบัตรกำนัลและข้อมูลสาขา
    $sql = "SELECT 
        v.*,
        CONCAT(u.users_fname, ' ', u.users_lname) as creator_name,
        b.branch_name,
        b.branch_address,
        b.branch_phone,
        b.branch_email,
        b.branch_tax_id,
        b.branch_license_no,
        b.branch_services,
        b.branch_description,
        b.branch_logo,
        b.branch_website,
        b.branch_line_id
    FROM gift_vouchers v
    LEFT JOIN users u ON v.created_by = u.users_id
    LEFT JOIN branch b ON b.branch_id = 1"; // ใช้สาขาหลัก branch_id = 1

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }

    if (!$stmt->execute()) {
        throw new Exception("Error executing statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        throw new Exception('ไม่พบข้อมูลบัตรกำนัล');
    }

    // แยกข้อมูล
    $voucher = [
        'voucher_id' => $data['voucher_id'],
        'voucher_code' => $data['voucher_code'],
        'amount' => $data['amount'],
        'discount_type' => $data['discount_type'],
        'max_discount' => $data['max_discount'],
        'expire_date' => $data['expire_date'],
        'status' => $data['status'],
        'created_at' => $data['created_at'],
        'creator_name' => $data['creator_name']
    ];

    $branch = [
        'branch_name' => $data['branch_name'],
        'branch_address' => $data['branch_address'],
        'branch_phone' => $data['branch_phone'],
        'branch_email' => $data['branch_email'],
        'branch_tax_id' => $data['branch_tax_id'],
        'branch_license_no' => $data['branch_license_no'],
        'branch_services' => $data['branch_services'],
        'branch_description' => $data['branch_description'],
        'branch_logo' => $data['branch_logo'],
        'branch_website' => $data['branch_website'],
        'branch_line_id' => $data['branch_line_id']
    ];

    echo json_encode([
        'success' => true,
        'data' => [
            'voucher' => $voucher,
            'branch' => $branch
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// ปิดการเชื่อมต่อ
if (isset($stmt)) {
    $stmt->close();
}
if (isset($conn)) {
    $conn->close();
}
?>