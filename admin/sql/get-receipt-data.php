<?php
require_once '../../dbcon.php';

$oc_id = $_GET['oc_id'];
$response = [];

try {
    // ดึงข้อมูลของ order และ branch (คงเดิม)
    $sql = "SELECT oc.*, c.*, b.*,
            CONCAT(u.users_fname, ' ', u.users_lname) as seller_name
            FROM order_course oc
            JOIN customer c ON oc.cus_id = c.cus_id
            JOIN branch b ON oc.branch_id = b.branch_id
            LEFT JOIN users u ON oc.seller_id = u.users_id
            WHERE oc.oc_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $oc_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    // ดึงข้อมูลรายการสินค้าพร้อมการปรับราคา
    $sql = "SELECT od.*, c.course_name,
            (SELECT pal.old_price 
             FROM price_adjustment_logs pal 
             WHERE pal.order_id = od.oc_id 
             AND pal.course_id = od.course_id 
             ORDER BY pal.adjusted_at DESC LIMIT 1) as original_price,
            (SELECT pal.new_price 
             FROM price_adjustment_logs pal 
             WHERE pal.order_id = od.oc_id 
             AND pal.course_id = od.course_id 
             ORDER BY pal.adjusted_at DESC LIMIT 1) as adjusted_price
            FROM order_detail od
            JOIN course c ON od.course_id = c.course_id
            WHERE od.oc_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $oc_id);
    $stmt->execute();
    $items_result = $stmt->get_result();
    
    $items_array = [];
    $subtotal = 0;

    while ($item = $items_result->fetch_assoc()) {
        $has_price_adjustment = !is_null($item['original_price']);
        $current_price = $has_price_adjustment ? $item['adjusted_price'] : $item['od_price'];
        $total_price = $current_price * $item['od_amount'];
        
        $items_array[] = [
            'course_id' => $item['course_id'],
            'course_name' => $item['course_name'],
            'amount' => $item['od_amount'],
            'price' => $current_price,
            'total_price' => $total_price,
            'has_price_adjustment' => $has_price_adjustment,
            'original_price' => $item['original_price'],
            'adjusted_price' => $item['adjusted_price']
        ];
        
        $subtotal += $total_price;
    }

    // ดึงข้อมูลส่วนลดบัตรกำนัล
    $sql = "SELECT vh.*, gv.voucher_code, gv.discount_type 
            FROM voucher_usage_history vh
            JOIN gift_vouchers gv ON vh.voucher_id = gv.voucher_id
            WHERE vh.order_id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $oc_id);
    $stmt->execute();
    $voucher_result = $stmt->get_result();
    $voucher_discounts = [];
    $total_voucher_discount = 0;

    while ($voucher = $voucher_result->fetch_assoc()) {
        $voucher_discounts[] = $voucher;
        $total_voucher_discount += floatval($voucher['amount_used']);
    }

    // ดึงข้อมูลมัดจำ
    $deposit_amount = floatval($order['deposit_amount']);

    // คำนวณยอดรวมต่างๆ
    $summary = [
        'subtotal' => $subtotal,                     // ยอดรวมทั้งหมด
        'deposit' => $deposit_amount,                // จำนวนเงินมัดจำ
        'voucher_discount' => $total_voucher_discount, // ส่วนลดจากบัตรกำนัล
        'net_total' => $subtotal - $deposit_amount - $total_voucher_discount // ยอดสุทธิ
    ];

    $response = [
        'success' => true,
        'order' => $order,
        'items_array' => $items_array,
        'voucher_discounts' => $voucher_discounts,
        'summary' => $summary,
        'branch_info' => [
            'name' => $order['branch_name'],
            'address' => $order['branch_address'],
            'phone' => $order['branch_phone'],
            'email' => $order['branch_email'],
            'tax_id' => $order['branch_tax_id'],
            'license_no' => $order['branch_license_no']
        ]
    ];

} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>