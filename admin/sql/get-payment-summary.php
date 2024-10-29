<?php
session_start();
require '../../dbcon.php';

header('Content-Type: application/json');

try {
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
    
    if ($order_id <= 0) {
        throw new Exception('Invalid order ID');
    }

    // 1. ดึงข้อมูลออเดอร์และยอดรวม
    $sql_order = "SELECT oc.*, 
                  (SELECT SUM(od.od_price * od.od_amount) 
                   FROM order_detail od 
                   WHERE od.oc_id = oc.oc_id) as total_amount
                  FROM order_course oc 
                  WHERE oc.oc_id = ?";
    
    $stmt = $conn->prepare($sql_order);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $order_result = $stmt->get_result();
    $order_data = $order_result->fetch_assoc();

    if (!$order_data) {
        throw new Exception('Order not found');
    }

    // 2. ดึงข้อมูลการมัดจำ
    $deposit_data = [
        'amount' => floatval($order_data['deposit_amount']),
        'date' => $order_data['deposit_date'],
        'payment_type' => $order_data['deposit_payment_type']
    ];

    // 3. ดึงข้อมูลการใช้บัตรกำนัล
    $sql_vouchers = "SELECT vh.*, gv.voucher_code, gv.discount_type 
                     FROM voucher_usage_history vh
                     JOIN gift_vouchers gv ON vh.voucher_id = gv.voucher_id
                     WHERE vh.order_id = ?";
    
    $stmt = $conn->prepare($sql_vouchers);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    $voucher_result = $stmt->get_result();
    $voucher_usage = [];
    $total_voucher_discount = 0;
    
    while ($voucher = $voucher_result->fetch_assoc()) {
        $voucher_usage[] = $voucher;
        $total_voucher_discount += floatval($voucher['amount_used']);
    }

    // 4. คำนวณยอดสุทธิ (แก้ไขส่วนนี้)
    $total = floatval($order_data['total_amount']);
    $deposit = floatval($deposit_data['amount']);
    $voucher_discount = floatval($total_voucher_discount);
    
    // คำนวณยอดที่ต้องชำระ
    $remaining = $total - $deposit - $voucher_discount;

    // สร้าง response
    $response = [
        'success' => true,
        'data' => [
            'summary' => [
                'total' => $total,
                'remaining' => $remaining,
                'voucher_discount' => $voucher_discount
            ],
            'deposit' => $deposit_data,
            'voucher_usage' => $voucher_usage
        ]
    ];

    header('Content-Type: application/json');
    echo json_encode($response);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>