<?php
session_start();
require '../../dbcon.php';

if (isset($_GET['order_id'])) {
    function formatOrderId($orderId) {
    return 'ORDER-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
}
    $order_id = mysqli_real_escape_string($conn, $_GET['order_id']);
    
    $sql = "SELECT oc.*, cb.booking_datetime, cu.cus_firstname, cu.cus_lastname, cu.cus_tel, cu.cus_email,
            od.*, c.course_name, u.users_fname, u.users_lname , oc.users_id as u_id
            FROM order_course oc
            JOIN course_bookings cb ON oc.course_bookings_id = cb.id
            JOIN customer cu ON cb.cus_id = cu.cus_id
            JOIN order_detail od ON oc.oc_id = od.oc_id
            JOIN course c ON od.course_id = c.course_id
            LEFT JOIN users u ON oc.users_id = u.users_id
            WHERE oc.oc_id = '$order_id'";
    
    $result = $conn->query($sql);


function convertToThaiDate($date) {
    $thai_months = [
        1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.', 5 => 'พ.ค.', 6 => 'มิ.ย.',
        7 => 'ก.ค.', 8 => 'ส.ค.', 9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
    ];

    $date_parts = explode(' ', $date);
    $time = isset($date_parts[1]) ? $date_parts[1] : '';
    $date_parts = explode('-', $date_parts[0]);
    
    $day = intval($date_parts[2]);
    $month = $thai_months[intval($date_parts[1])];
    $year = intval($date_parts[0]) + 543;

    return "$day $month $year" . ($time ? " $time" : "");
}

    if ($result->num_rows > 0) {
        $order_info = $result->fetch_assoc();
        $formatted_order_id = formatOrderId($order_info['oc_id']);
        ?>
        <style>
            .order-details {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
            }
            .order-details h4 {
                color: #2c3e50;
                border-bottom: 2px solid #3498db;
                padding-bottom: 10px;
/*                margin-bottom: 20px;*/
            }
            .order-details .section {
                background-color: #f9f9f9;
                border: 1px solid #e0e0e0;
                border-radius: 5px;
                padding: 15px;
/*                margin-bottom: 20px;*/
            }
            .order-details .section h5 {
                color: #2980b9;
                margin-top: 0;
            }
            .order-details table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
            }
            .order-details th, .order-details td {
                border: 1px solid #ddd;
                padding: 12px;
                text-align: left;
            }
            .order-details th {
                background-color: #f2f2f2;
                font-weight: bold;
            }
            .order-details .total-row {
                font-weight: bold;
                background-color: #e8f4fd;
            }
            .payment-status {
                display: inline-block;
                padding: 5px 10px;
                border-radius: 15px;
                font-weight: bold;
            }
            .order-id {
                font-size: 1.2em;
                font-weight: bold;
                color: #3498db;
            }
            .payment-cash { background-color: #d4edda; color: #155724; }
            .payment-credit { background-color: #cce5ff; color: #004085; }
            .payment-transfer { background-color: #e2e3e5; color: #383d41; }
            .payment-unpaid { background-color: #f8d7da; color: #721c24; }
        </style>

        <div class="order-details">
             <h4>รายละเอียดการสั่งซื้อ <span class="order-id"><?php echo $formatted_order_id; ?></span></h4>
            
            <div class="section">
                <h5>ข้อมูลลูกค้า</h5>
                <p><strong>ชื่อ-นามสกุล:</strong> <?php echo htmlspecialchars($order_info['cus_firstname'] . ' ' . $order_info['cus_lastname']); ?></p>
                <p><strong>เบอร์โทรศัพท์:</strong> <?php echo htmlspecialchars($order_info['cus_tel']); ?></p>
                <p><strong>อีเมล:</strong> <?php echo htmlspecialchars($order_info['cus_email']); ?></p>
            </div>

            <div class="section">
                <h5>รายละเอียดการจอง</h5>
                <p><strong>วันที่จอง:</strong> <?php echo convertToThaiDate($order_info['booking_datetime']); ?></p>
                <p><strong>ผู้ทำรายการ:</strong> <?php if($order_info['u_id']!=0){ echo htmlspecialchars($order_info['users_fname'] . ' ' . $order_info['users_lname']); }else{ echo "จองเองผ่าน Line"; } ?></p>
                <p><strong>วันที่ทำรายการ:</strong> <?php echo convertToThaiDate($order_info['order_datetime']); ?></p>
                <?php
                $payment_class = '';
                switch ($order_info['order_payment']) {
                    case 'เงินสด':
                        $payment_class = 'payment-cash';
                        break;
                    case 'บัตรเครดิต':
                        $payment_class = 'payment-credit';
                        break;
                    case 'โอนเงิน':
                        $payment_class = 'payment-transfer';
                        break;
                    default:
                        $payment_class = 'payment-unpaid';
                }
                ?>
                <p><strong>สถานะการชำระเงิน:</strong> <span class="payment-status <?php echo $payment_class; ?>"><?php echo htmlspecialchars($order_info['order_payment']); ?></span></p>
            </div>

            <div class="section">
                <h5>รายการคอร์สที่สั่งซื้อ</h5>
                <table>
                    <thead>
                        <tr>
                            <th>คอร์ส</th>
                            <th>จำนวน</th>
                            <th>ราคาต่อหน่วย</th>
                            <th>ราคารวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        do {
                            $subtotal = $order_info['od_amount'] * $order_info['od_price'];
                            $total += $subtotal;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order_info['course_name']); ?></td>
                                <td><?php echo $order_info['od_amount']; ?></td>
                                <td><?php echo number_format($order_info['od_price'], 2); ?> บาท</td>
                                <td><?php echo number_format($subtotal, 2); ?> บาท</td>
                            </tr>
                            <?php
                        } while ($order_info = $result->fetch_assoc());
                        ?>
                        <tr class="total-row">
                            <td colspan="3">รวมทั้งสิ้น</td>
                            <td><?php echo number_format($total, 2); ?> บาท</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    } else {
        echo "<p>ไม่พบข้อมูลรายละเอียดการสั่งซื้อ</p>";
    }
} else {
    echo "<p>ไม่พบ ID การสั่งซื้อ</p>";
}
?>