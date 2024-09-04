<?php
require '../../dbcon.php';

if (isset($_GET['order_id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['order_id']);
    
    $sql = "SELECT oc.*, od.*, c.course_name 
            FROM order_course oc
            JOIN order_detail od ON oc.oc_id = od.oc_id
            JOIN course c ON od.course_id = c.course_id
            WHERE oc.oc_id = '$order_id'";
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        ?>
        <h4>รายการสั่งซื้อ #<?php echo $order['oc_id']; ?></h4>
        <p><strong>วันที่สั่งซื้อ:</strong> <?php echo convertToThaiDate($order['order_datetime']); ?></p>
        <p><strong>คอร์ส:</strong> <?php echo $order['course_name']; ?></p>
        <p><strong>จำนวน:</strong> <?php echo $order['od_amount']; ?></p>
        <p><strong>ราคาต่อหน่วย:</strong> <?php echo number_format($order['od_price'], 2); ?> บาท</p>
        <p><strong>ราคารวม:</strong> <?php echo number_format($order['order_net_total'], 2); ?> บาท</p>
        <p><strong>วิธีการชำระเงิน:</strong> <?php echo $order['order_payment']; ?></p>
        <?php
    } else {
        echo "ไม่พบข้อมูลการสั่งซื้อ";
    }
} else {
    echo "ไม่พบรหัสการสั่งซื้อ";
}

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
?>