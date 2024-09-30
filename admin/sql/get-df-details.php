<?php
session_start();
require '../../dbcon.php';

if (!isset($_GET['staff_id']) || !isset($_GET['staff_type']) || !isset($_GET['start_date']) || !isset($_GET['end_date'])) {
    echo "ข้อมูลไม่ครบถ้วน";
    exit;
}

$staff_id = $_GET['staff_id'];
$staff_type = $_GET['staff_type'];
$start_date = $_GET['start_date'];
$end_date = $_GET['end_date'];

$sql = "
    SELECT 
        oc.oc_id,
        oc.order_datetime,
        c.cus_firstname,
        c.cus_lastname,
        oc.order_net_total,
        ssr.staff_df,
        ssr.staff_df_type,
        CASE 
            WHEN ssr.staff_df_type = 'amount' THEN ssr.staff_df
            WHEN ssr.staff_df_type = 'percent' THEN (oc.order_net_total * ssr.staff_df / 100)
            ELSE 0
        END as df_amount
    FROM service_staff_records ssr
    JOIN service_queue sq ON ssr.service_id = sq.queue_id
    JOIN order_course oc ON sq.booking_id = oc.course_bookings_id
    JOIN customer c ON oc.cus_id = c.cus_id
    WHERE ssr.staff_id = ?
    AND ssr.staff_type = ?
    AND oc.order_payment != 'ยังไม่จ่ายเงิน'
    AND DATE(oc.order_datetime) BETWEEN ? AND ?
    ORDER BY oc.order_datetime DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $staff_id, $staff_type, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

$staff_name = "";
$total_df = 0;

echo "<div id='printArea'>";
echo "<h4>รายละเอียด Doctor Fee</h4>";
echo "<table class='table table-striped'>";
echo "<thead><tr><th>วันที่</th><th>ชื่อลูกค้า</th><th>ยอดขาย</th><th>DF (%)</th><th>DF (บาท)</th></tr></thead>";
echo "<tbody>";

while ($row = $result->fetch_assoc()) {
    if (empty($staff_name)) {
        $staff_name = $row['cus_firstname'] . ' ' . $row['cus_lastname'];
    }
    
    $order_id = 'ORDER-' . str_pad($row['oc_id'], 5, '0', STR_PAD_LEFT);
    
    echo "<tr>";
    echo "<td>" . date('d/m/Y H:i', strtotime($row['order_datetime'])) . "</td>";
    echo "<td>" . $order_id . " " . $row['cus_firstname'] . ' ' . $row['cus_lastname'] . "</td>";
    echo "<td style='text-align: right;'>" . number_format($row['order_net_total'], 2) . "</td>";
    
    if ($row['staff_df_type'] == 'percent') {
        echo "<td style='text-align: right;'>" . $row['staff_df'] . "%</td>";
    } else {
        echo "<td></td>";
    }
    
    echo "<td style='text-align: right;'>" . number_format($row['df_amount'], 2) . "</td>";
    echo "</tr>";

    $total_df += $row['df_amount'];
}

echo "</tbody>";
echo "<tfoot><tr><th colspan='4' style='text-align: right;'>รวม DF ทั้งหมด</th><th style='text-align: right;'>" . number_format($total_df, 2) . " บาท</th></tr></tfoot>";
echo "</table>";

echo "<p>ชื่อ-นามสกุล: " . $staff_name . "</p>";
echo "<p>ประเภท: " . ($staff_type == 'doctor' ? 'แพทย์' : 'พยาบาล') . "</p>";
echo "<p>ช่วงเวลา: " . date('d/m/Y', strtotime($start_date)) . " ถึง " . date('d/m/Y', strtotime($end_date)) . "</p>";
echo "</div>";

$stmt->close();
$conn->close();