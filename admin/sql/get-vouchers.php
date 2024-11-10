<?php
session_start();
require_once '../../dbcon.php';
header('Content-Type: application/json; charset=utf-8');

try {
    // ตรวจสอบ session
    if (!isset($_SESSION['users_id'])) {
        throw new Exception('กรุณาเข้าสู่ระบบใหม่');
    }

    // รับพารามิเตอร์
    $search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
    $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
    $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
    $order_column = isset($_GET['order'][0]['column']) ? intval($_GET['order'][0]['column']) : 2;
    $order_dir = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'desc';

    // กำหนดคอลัมน์ที่สามารถเรียงลำดับได้
    $columns = array(
        0 => 'voucher_code',
        1 => 'amount',
        2 => 'created_at',
        3 => 'expire_date',
        4 => 'status',
        5 => 'creator_name',
        6 => 'used_at',
        7 => 'used_in_order'
    );

    // Base query
$sql = "SELECT 
    v.*,
    CONCAT(u.users_fname, ' ', u.users_lname) as creator_name,
    CONCAT(c.cus_firstname, ' ', c.cus_lastname) as customer_name,
    CASE 
        WHEN v.remaining_amount IS NULL AND v.customer_id IS NOT NULL 
            AND v.discount_type = 'fixed' 
        THEN v.amount 
        ELSE v.remaining_amount 
    END as remaining_amount,
    COALESCE(
        (SELECT MIN(used_at) 
         FROM voucher_usage_history 
         WHERE voucher_id = v.voucher_id), 
        v.first_used_at
    ) as first_usage_date
    FROM gift_vouchers v
    LEFT JOIN users u ON v.created_by = u.users_id
    LEFT JOIN customer c ON v.customer_id = c.cus_id";
    
    $conditions = [];
    $params = [];
    $types = "";

    // เพิ่มเงื่อนไขการค้นหา
    if (!empty($search)) {
        $searchPattern = "%$search%";
        $conditions[] = "(
            v.voucher_code LIKE ? OR 
            CONCAT(u.users_fname, ' ', u.users_lname) LIKE ? OR
            CONCAT(c.cus_firstname, ' ', c.cus_lastname) LIKE ?
        )";
        array_push($params, $searchPattern, $searchPattern, $searchPattern);
        $types .= "sss";
    }

    // รวมเงื่อนไขทั้งหมด
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    // นับจำนวนทั้งหมด
    $total_sql = "SELECT COUNT(*) as total FROM gift_vouchers";
    $total_stmt = $conn->prepare($total_sql);
    if (!$total_stmt) {
        throw new Exception("Error preparing total statement: " . $conn->error);
    }
    $total_stmt->execute();
    $total_records = $total_stmt->get_result()->fetch_assoc()['total'];

    // นับจำนวนที่กรอง
    $filtered_records = $total_records;
    if (!empty($conditions)) {
        $count_sql = "SELECT COUNT(*) as total FROM gift_vouchers v
                     LEFT JOIN users u ON v.created_by = u.users_id
                     LEFT JOIN customer c ON v.customer_id = c.cus_id
                     WHERE " . implode(" AND ", $conditions);
        $count_stmt = $conn->prepare($count_sql);
        if (!$count_stmt) {
            throw new Exception("Error preparing count statement: " . $conn->error);
        }
        $count_stmt->bind_param($types, ...$params);
        $count_stmt->execute();
        $filtered_records = $count_stmt->get_result()->fetch_assoc()['total'];
    }

    // เพิ่ม ORDER BY
    if (isset($columns[$order_column])) {
        $sql .= " ORDER BY {$columns[$order_column]} $order_dir";
    }

    // เพิ่ม LIMIT
    $sql .= " LIMIT ?, ?";
    array_push($params, $start, $length);
    $types .= "ii";

    // ดึงข้อมูล
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error preparing main statement: " . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        throw new Exception("Error executing statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $data = array();
    
    // ในส่วนของการ fetch ข้อมูล
    while ($row = $result->fetch_assoc()) {
        // คำนวณสถานะที่ถูกต้อง
        $today = date('Y-m-d');
        $expireDate = $row['expire_date'];
        
        // เริ่มต้นตรวจสอบสถานะ
        if (strtotime($expireDate) < strtotime($today)) {
            $row['status'] = 'expired';
        } else {
            if ($row['discount_type'] === 'percent') {
                // สำหรับบัตรแบบเปอร์เซ็นต์
                $row['status'] = $row['customer_id'] ? 'used' : 'unused';
            } else {
                // สำหรับบัตรแบบจำนวนเงิน
                if (!$row['customer_id']) {
                    $row['status'] = 'unused';
                } else {
                    // ถ้ามีการผูกกับลูกค้าแต่ยังไม่มีประวัติการใช้งาน
                    if ($row['remaining_amount'] === $row['amount']) {
                        $row['status'] = 'unused';
                    } elseif ($row['remaining_amount'] > 0) {
                        $row['status'] = 'partial';
                    } else {
                        $row['status'] = 'used';
                    }
                }
            }
        }
        
        $data[] = $row;
    }

    echo json_encode([
        "draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
        "recordsTotal" => $total_records,
        "recordsFiltered" => $filtered_records,
        "data" => $data,
        "success" => true
    ]);

} catch (Exception $e) {
    error_log("Get Vouchers Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}

// ปิดการเชื่อมต่อ
if (isset($stmt) && $stmt instanceof mysqli_stmt) {
    $stmt->close();
}
if (isset($total_stmt) && $total_stmt instanceof mysqli_stmt) {
    $total_stmt->close();
}
if (isset($count_stmt) && $count_stmt instanceof mysqli_stmt) {
    $count_stmt->close();
}
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>