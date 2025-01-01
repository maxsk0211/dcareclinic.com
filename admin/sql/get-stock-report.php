<?php
require_once '../../dbcon.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['users_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

try {
    $branch_id = $_SESSION['branch_id'];
    $stock_type = $_GET['stock_type'] ?? 'drug'; // รับค่าประเภทสต็อค

    // กำหนดชื่อตาราง คอลัมน์ และการเชื่อมตาราง
    switch($stock_type) {
        case 'drug':
            $table = 'd';
            $table_name = 'drug d';
            $name_column = 'd.drug_name';
            $amount_column = 'd.drug_amount';
            $type_column = 'd.drug_type_id';
            $cost_column = 'd.drug_cost';
            $branch_column = 'd.branch_id';
            $type_table = 'drug_type dt';
            $type_join = 'LEFT JOIN drug_type dt ON d.drug_type_id = dt.drug_type_id';
            $type_name_column = 'dt.drug_type_name';
            $unit_table = 'unit u';
            $unit_join = 'LEFT JOIN unit u ON d.drug_unit_id = u.unit_id';
            $unit_column = 'd.drug_unit_id';
            break;
        
        case 'accessory':
            $table = 't';
            $table_name = 'accessories t';
            $name_column = 't.acc_name';
            $amount_column = 't.acc_amount';
            $type_column = 't.acc_type_id';
            $cost_column = 't.acc_cost';
            $branch_column = 't.branch_id';
            $type_table = 'acc_type tp';
            $type_join = 'LEFT JOIN acc_type tp ON t.acc_type_id = tp.acc_type_id';
            $type_name_column = 'tp.acc_type_name';
            $unit_table = 'unit u';
            $unit_join = 'LEFT JOIN unit u ON t.acc_unit_id = u.unit_id';
            $unit_column = 't.acc_unit_id';
            break;
        
        case 'tool':
            $table = 't';
            $table_name = 'tool t';
            $name_column = 't.tool_name';
            $amount_column = 't.tool_amount';
            $type_column = null; // เครื่องมืออาจไม่มีประเภท
            $cost_column = 't.tool_cost';
            $branch_column = 't.branch_id';
            $type_table = null;
            $type_join = '';
            $type_name_column = null;
            $unit_table = 'unit u';
            $unit_join = 'LEFT JOIN unit u ON t.tool_unit_id = u.unit_id';
            $unit_column = 't.tool_unit_id';
            break;
        
        default:
            throw new Exception('Invalid stock type');
    }

    // เริ่มต้นเงื่อนไข SQL
    $conditions = ["{$branch_column} = ?"];
    $params = [$branch_id];
    $types = "i";

    // เพิ่มเงื่อนไขประเภท (ถ้ามี)
    if (!empty($_GET['typeFilter']) && $type_column) {
        $conditions[] = "{$type_column} = ?";
        $params[] = $_GET['typeFilter'];
        $types .= "i";
    }

    // เพิ่มเงื่อนไขสถานะสต็อก
    if (!empty($_GET['stockStatus'])) {
        switch ($_GET['stockStatus']) {
            case 'low':
                $conditions[] = "{$amount_column} > 0 AND {$amount_column} < 10";
                break;
            case 'normal':
                $conditions[] = "{$amount_column} >= 10";
                break;
            case 'out':
                $conditions[] = "{$amount_column} <= 0";
                break;
        }
    }

    // สร้างเงื่อนไข WHERE
    $where = implode(" AND ", $conditions);
    
    // สร้าง SQL โดยใช้เงื่อนไขที่กำหนด
    $sql = "SELECT 
        {$table}.*, 
        " . ($type_name_column ? "{$type_name_column} as type_name, " : " 'เครื่องมือ' as type_name, ") . "
        {$unit_column} as unit_id,
        u.unit_name,
        ({$amount_column} * {$cost_column}) as total_value
    FROM {$table_name}
    {$type_join}
    {$unit_join}
    WHERE {$where}
    ORDER BY {$name_column}";

    // Prepare statement
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error . "\nSQL: " . $sql);
    }

    // Bind parameters
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    // Execute
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $result = $stmt->get_result();

    $items = [];
    $total_items = 0;
    $total_value = 0;
    $low_stock = 0;
    $out_of_stock = 0;
    $type_values = [];

    while ($row = $result->fetch_assoc()) {
        // Modify this part to use the dynamic amount column
        $amount = $row[$stock_type == 'drug' ? 'drug_amount' : 
                       ($stock_type == 'accessory' ? 'acc_amount' : 'tool_amount')];
        
        $row['amount'] = $amount; // Add amount to the row for frontend use
        $items[] = $row;
        
        $total_items++;
        $total_value += $row['total_value'];
        
        if ($amount <= 0) {
            $out_of_stock++;
        } elseif ($amount < 10) {
            $low_stock++;
        }

        // สะสมมูลค่าตามประเภท
        $type_name = $row['type_name'] ?? 'อื่นๆ';
        if (!isset($type_values[$type_name])) {
            $type_values[$type_name] = 0;
        }
        $type_values[$type_name] += $row['total_value'];
    }

    echo json_encode([
        'success' => true,
        'summary' => [
            'totalItems' => $total_items,
            'totalValue' => $total_value,
            'lowStockItems' => $low_stock,
            'outOfStockItems' => $out_of_stock
        ],
        'chartData' => [
            'labels' => array_keys($type_values),
            'values' => array_values($type_values)
        ],
        'items' => $items
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาด',
        'debug' => $e->getMessage()
    ]);
}

$conn->close();