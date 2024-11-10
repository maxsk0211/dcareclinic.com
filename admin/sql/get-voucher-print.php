<?php
session_start();
require_once '../../dbcon.php';
header('Content-Type: application/json; charset=utf-8');

try {
    // เพิ่ม Debug log
    error_log("Starting voucher print process");
    error_log("GET parameters: " . print_r($_GET, true));

    if (!isset($_GET['voucher_id'])) {
        throw new Exception('ไม่พบรหัสบัตรกำนัล');
    }

    $voucher_id = intval($_GET['voucher_id']);
    error_log("Voucher ID: " . $voucher_id);

    // แก้ไข SQL ให้เรียบง่ายขึ้น
    $sql = "SELECT 
        v.*,
        CONCAT(u.users_fname, ' ', u.users_lname) as creator_name,
        b.branch_name,
        b.branch_address,
        b.branch_phone,
        b.branch_line_id
    FROM gift_vouchers v
    LEFT JOIN users u ON v.created_by = u.users_id
    CROSS JOIN branch b 
    WHERE v.voucher_id = ? AND b.branch_id = 1";

    error_log("SQL Query: " . $sql);

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("i", $voucher_id);

    if (!$stmt->execute()) {
        throw new Exception("Error executing statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        throw new Exception('ไม่พบข้อมูลบัตรกำนัล ID: ' . $voucher_id);
    }

    error_log("Fetched data: " . print_r($data, true));

    // แยกข้อมูลให้เรียบง่าย
    $voucher = [
        'voucher_code' => $data['voucher_code'],
        'amount' => $data['amount'],
        'discount_type' => $data['discount_type'],
        'max_discount' => $data['max_discount'],
        'expire_date' => $data['expire_date'],
        'created_at' => $data['created_at'],
        'creator_name' => $data['creator_name']
    ];

    $branch = [
        'branch_name' => $data['branch_name'],
        'branch_address' => $data['branch_address'],
        'branch_phone' => $data['branch_phone'],
        'branch_line_id' => $data['branch_line_id']
    ];

    $response = [
        'success' => true,
        'data' => [
            'voucher' => $voucher,
            'branch' => $branch
        ]
    ];

    error_log("Response: " . json_encode($response));
    echo json_encode($response);

} catch (Exception $e) {
    error_log("Error in voucher print: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug' => [
            'voucher_id' => $voucher_id ?? null,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
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