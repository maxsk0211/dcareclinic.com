<?php
session_start();
require '../../dbcon.php';

if (isset($_GET['order_id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['order_id']);
    
    $sql = "SELECT od.*, c.course_name 
            FROM order_detail od
            JOIN course c ON od.course_id = c.course_id
            WHERE od.oc_id = '$order_id'";
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table class='table'>";
        echo "<thead><tr><th>คอร์ส</th><th>จำนวน</th><th>ราคา</th></tr></thead>";
        echo "<tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
            echo "<td>" . $row['od_amount'] . "</td>";
            echo "<td>" . number_format($row['od_price'], 2) . " บาท</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "ไม่พบข้อมูลรายละเอียดการสั่งซื้อ";
    }
} else {
    echo "ไม่พบ ID การสั่งซื้อ";
}
?>