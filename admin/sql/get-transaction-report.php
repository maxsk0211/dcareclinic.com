<?php
require_once '../../dbcon.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['users_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'กรุณาเข้าสู่ระบบใหม่อีกครั้ง'
    ]);
    exit();
}

try {
    $branch_id = $_SESSION['branch_id'];
    $stock_type = $_GET['stock_type'] ?? 'drug'; // รับค่าประเภทสต็อค
    
    $conditions = ["st.branch_id = ? AND st.status = 1 AND st.stock_type = ?"];
    $params = [$branch_id, $stock_type];
    $types = "is"; // i สำหรับ branch_id, s สำหรับ stock_type

    // Filter by date range
    if (!empty($_GET['startDate'])) {
        $conditions[] = "DATE(st.transaction_date) >= ?";
        $params[] = $_GET['startDate'];
        $types .= "s";
    }
    if (!empty($_GET['endDate'])) {
        $conditions[] = "DATE(st.transaction_date) <= ?";
        $params[] = $_GET['endDate'];
        $types .= "s";
    }

    // Filter by transaction type
    if (!empty($_GET['transactionType'])) {
        if ($_GET['transactionType'] == 'in') {
            $conditions[] = "(st.quantity > 0 OR st.notes LIKE '%คืนสต็อก%')";
        } else if ($_GET['transactionType'] == 'out') {
            $conditions[] = "(st.quantity < 0 OR st.notes LIKE '%ORDER%')";
        }
    }

    // สร้าง JOIN และ SELECT ตามประเภทสต็อค
    $item_join = "";
    $type_join = "";
    $item_name = "";
    $type_name = "";
    
    switch($stock_type) {
        case 'drug':
            $item_join = "LEFT JOIN drug d ON st.related_id = d.drug_id";
            $type_join = "LEFT JOIN drug_type dt ON d.drug_type_id = dt.drug_type_id";
            $unit_join = "LEFT JOIN unit u ON d.drug_unit_id = u.unit_id";
            $item_name = "d.drug_name";
            $type_name = "dt.drug_type_name";
            break;
            
        case 'accessory':
            $item_join = "LEFT JOIN accessories a ON st.related_id = a.acc_id";
            $type_join = "LEFT JOIN acc_type at ON a.acc_type_id = at.acc_type_id";
            $unit_join = "LEFT JOIN unit u ON a.acc_unit_id = u.unit_id";
            $item_name = "a.acc_name";
            $type_name = "at.acc_type_name";
            break;
            
        case 'tool':
            $item_join = "LEFT JOIN tool t ON st.related_id = t.tool_id";
            $unit_join = "LEFT JOIN unit u ON t.tool_unit_id = u.unit_id";
            $item_name = "t.tool_name";
            break;
    }

    $where = implode(" AND ", $conditions);
    
    // แก้ไขส่วนการ JOIN กับตาราง unit
    $sql = "SELECT 
            st.*,
            usr.users_fname, 
            usr.users_lname,
            {$item_name} as item_name,
            " . ($stock_type != 'tool' ? "{$type_name} as type_name," : "") . "
            u.unit_name,
            CASE 
                WHEN st.notes LIKE '%ตัดสต็อกจากการใช้บริการ%' THEN 'ใช้ในคอร์ส'
                WHEN st.notes LIKE '%คืนสต็อกจากการยกเลิกชำระเงิน%' THEN 'รับเข้า'
                WHEN st.quantity > 0 THEN 'รับเข้า'
                ELSE 'เบิกออก'
            END as transaction_type_name,
            CASE 
                WHEN st.quantity > 0 THEN st.quantity
                ELSE ABS(st.quantity)
            END as display_quantity,
            (ABS(st.quantity) * st.cost_per_unit) as total_value
        FROM stock_transactions st
        {$item_join}
        {$type_join}
        {$unit_join}
        LEFT JOIN users usr ON st.users_id = usr.users_id
        WHERE {$where}
        ORDER BY st.transaction_id DESC";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    
    $items = [];
    $total_transactions = 0;
    $total_in_value = 0;
    $total_out_value = 0;
    $daily_transactions = [];

    while ($row = $result->fetch_assoc()) {
        // จัดรูปแบบข้อมูลเพิ่มเติม
        $row['notes'] = formatNotes($row['notes']);
        $items[] = $row;
        
        $total_transactions++;
        
        if ($row['transaction_type_name'] == 'รับเข้า' || $row['transaction_type_name'] == 'คืนสต็อก') {
            $total_in_value += $row['total_value'];
        } else {
            $total_out_value += $row['total_value'];
        }

        // สะสมข้อมูลรายวัน
        $date = date('Y-m-d', strtotime($row['transaction_date']));
        if (!isset($daily_transactions[$date])) {
            $daily_transactions[$date] = ['in' => 0, 'out' => 0];
        }
        
        if ($row['transaction_type_name'] == 'รับเข้า' || $row['transaction_type_name'] == 'คืนสต็อก') {
            $daily_transactions[$date]['in'] += $row['total_value'];
        } else {
            $daily_transactions[$date]['out'] += $row['total_value'];
        }
    }

    // เรียงข้อมูลตามวันที่
    ksort($daily_transactions);

    echo json_encode([
        'success' => true,
        'summary' => [
            'totalTransactions' => $total_transactions,
            'totalInValue' => $total_in_value,
            'totalOutValue' => $total_out_value,
            'netValue' => $total_in_value - $total_out_value
        ],
        'chartData' => [
            'dates' => array_keys($daily_transactions),
            'inValues' => array_column($daily_transactions, 'in'),
            'outValues' => array_column($daily_transactions, 'out')
        ],
        'items' => $items
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ]);
}

// ฟังก์ชันจัดรูปแบบหมายเหตุ
function formatNotes($notes) {
    if (empty($notes)) {
        return '-';
    }

    // จัดการกรณี ORDER
    if (strpos($notes, 'ORDER-') !== false) {
        return str_replace('ORDER-', 'รหัสการสั่งซื้อ: ', $notes);
    }

    // จัดการกรณีคืนสต็อก
    if (strpos($notes, 'คืนสต็อกจากการยกเลิกชำระเงิน') !== false) {
        return str_replace('คืนสต็อกจากการยกเลิกชำระเงิน', 'คืนสต็อก - ยกเลิกการชำระเงิน', $notes);
    }

    // จัดการกรณีตัดสต็อก
    if (strpos($notes, 'ตัดสต็อกจากการใช้บริการ') !== false) {
        return str_replace('ตัดสต็อกจากการใช้บริการ', 'ใช้บริการ', $notes);
    }

    return $notes;
}

$conn->close();