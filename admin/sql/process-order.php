<?php
session_start();
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $users_id=$_SESSION['users_id'];
    $branch_id=$_SESSION['branch_id'];
    $booking_datetime = $_POST['booking_date'] . ' ' . $_POST['booking_time'] . ':00';
    // สร้างวัตถุ DateTime จากสตริงวันที่และเวลาในรูปแบบ พ.ศ.
    $datetime_obj = DateTime::createFromFormat('d/m/Y H:i:s', $booking_datetime);
    if ($datetime_obj) {
        // แปลงจาก พ.ศ. เป็น ค.ศ.
        $datetime_obj->modify('-543 years');

        // จัดรูปแบบวันที่และเวลาให้อยู่ในรูปแบบที่เหมาะสมสำหรับฐานข้อมูล
        $booking_datetime = $datetime_obj->format('Y-m-d H:i:s');

        // echo $booking_datetime; // ผลลัพธ์: 2024-08-27 16:00:00
    } else {
        echo "รูปแบบวันที่และเวลาไม่ถูกต้อง";
    }

    $selected_customer_id = $_POST['customer_select'];
    if (empty($selected_customer_id)) {
        $_SESSION['msg_error'] = "กรุณาเลือกลูกค้า";
        header("Location: ../booking.php");
        exit();
    }


    // สร้างคำสั่ง SQL โดยใช้ mysqli_real_escape_string
    $sql_insert = "INSERT INTO course_bookings (branch_id, cus_id, booking_datetime, users_id, status) 
                    VALUES ('$branch_id', '$selected_customer_id', '$booking_datetime', $users_id, 'confirmed')";

    // ดำเนินการ query
    if (mysqli_query($conn, $sql_insert)) {
        $course_bookings_id = mysqli_insert_id($conn);

    $customer_id = $_POST['customer_id'];
    $booking_datetime = $_POST['booking_datetime'];
    $selected_courses = json_decode($_POST['selected_courses'], true);
    $payment_method = $_POST['payment_method'];
    $users_id = $_SESSION['users_id'];

    // คำนวณราคารวม
    $total_price = 0;
    foreach ($selected_courses as $course) {
        $total_price += $course['price'];
    }

    // escape ตัวแปรเพื่อป้องกัน SQL injection
    $customer_id = mysqli_real_escape_string($conn, $customer_id);
    $payment_method = mysqli_real_escape_string($conn, $payment_method);

    // บันทึกข้อมูลในตาราง order_course (ใช้ mysqli_query)
    $sql_order = "INSERT INTO order_course (cus_id, users_id,course_bookings_id, order_datetime, order_payment, order_net_total, order_status) 
                    VALUES ('$customer_id', '$users_id','$course_bookings_id', NOW(), '$payment_method', $total_price, 1)";
    // exit();
    if (mysqli_query($conn, $sql_order)) {
        $order_id = mysqli_insert_id($conn); // ดึง ID ของ order ที่เพิ่ง insert
        
        // บันทึกข้อมูลในตาราง order_detail (ใช้ mysqli_query)
        foreach ($selected_courses as $course) {
            $course_id = mysqli_real_escape_string($conn, $course['id']);
            $course_price = mysqli_real_escape_string($conn, $course['price']);

            $sql_detail = "INSERT INTO order_detail (oc_id, course_id, od_amount, od_price) 
                            VALUES ('$order_id', '$course_id', 1, '$course_price')";
            
            if (!mysqli_query($conn, $sql_detail)) {
                // หากเกิดข้อผิดพลาดในการ insert order_detail ให้แจ้งเตือนและหยุดการทำงาน
                $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการบันทึกคำสั่งซื้อ: " . mysqli_error($conn);
                header("Location: ../booking.php");
                exit();
            }
        }

        $_SESSION['msg_ok'] = "บันทึกคำสั่งซื้อเรียบร้อยแล้ว";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการบันทึกคำสั่งซื้อ: " . mysqli_error($conn);
    }
}
    header("Location: ../booking.php");
    exit();
} else {
    $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการบันทึกคำสั่งซื้อ";
    header("Location: ../booking.php");
    exit();
}
?>