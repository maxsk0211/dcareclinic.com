<?php
require '../../dbcon.php';

header('Content-Type: application/json');

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id == 0) {
    echo json_encode(['error' => 'Invalid order ID']);
    exit;
}

try {
    // ดึงข้อมูลการสั่งซื้อ การชำระเงิน และข้อมูลมัดจำ
    $sql_order = "SELECT oc.*, 
                         u.users_fname, 
                         u.users_lname,
                         SUM(od.od_price * od.od_amount) as total_price
                  FROM order_course oc
                  LEFT JOIN users u ON oc.users_id = u.users_id
                  LEFT JOIN order_detail od ON oc.oc_id = od.oc_id
                  WHERE oc.oc_id = ?
                  GROUP BY oc.oc_id";
                  
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("i", $order_id);
    $stmt_order->execute();
    $result_order = $stmt_order->get_result();
    $order = $result_order->fetch_assoc();

    if (!$order) {
        echo json_encode(['error' => 'Order not found']);
        exit;
    }

    // ดึงข้อมูลการใช้บัตรกำนัล
    $sql_vouchers = "SELECT v.*, vh.amount_used
                     FROM voucher_usage_history vh
                     JOIN gift_vouchers v ON vh.voucher_id = v.voucher_id
                     WHERE vh.order_id = ?";
                     
    $stmt_vouchers = $conn->prepare($sql_vouchers);
    $stmt_vouchers->bind_param("i", $order_id);
    $stmt_vouchers->execute();
    $result_vouchers = $stmt_vouchers->get_result();
    
    $vouchers = [];
    $total_voucher_discount = 0;
    while ($voucher = $result_vouchers->fetch_assoc()) {
        $vouchers[] = [
            'code' => $voucher['voucher_code'],
            'type' => $voucher['discount_type'],
            'amount' => $voucher['amount_used']
        ];
        $total_voucher_discount += $voucher['amount_used'];
    }

    // แปลงวันที่มัดจำและวันที่ชำระเงินให้อยู่ในรูปแบบที่ต้องการ
    $deposit_date = null;
    if ($order['deposit_date']) {
        $deposit_timestamp = strtotime($order['deposit_date']);
        $buddhist_year = date('Y', $deposit_timestamp) + 543;
        $deposit_date = date('j F ', $deposit_timestamp) . $buddhist_year . ' เวลา ' . date('H:i', $deposit_timestamp);
    }

    $payment_date = null;
    if ($order['order_payment_date']) {
        $payment_date = date('d/m/Y H:i:s', strtotime($order['order_payment_date']));
    }

    // คำนวณยอดเงินและจัดเตรียมข้อมูล
    $payment_details = [
        'order_id' => $order['oc_id'],
        'total_amount' => $order['total_price'],
        'deposit' => [
            'amount' => $order['deposit_amount'],
            'date' => $deposit_date,
            'payment_type' => $order['deposit_payment_type']
        ],
        'net_amount' => $order['total_price'] - $order['deposit_amount'] - $total_voucher_discount,
        'payment_status' => $order['order_payment'] ?? 'ยังไม่จ่ายเงิน',
        'payment_amount' => $order['total_price'] - $order['deposit_amount'] - $total_voucher_discount,
        'payment_date' => $payment_date,
        'payment_by' => $order['users_fname'] ? $order['users_fname'] . ' ' . $order['users_lname'] : null,
        'vouchers' => $vouchers
    ];

    echo json_encode($payment_details, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

$conn->close();