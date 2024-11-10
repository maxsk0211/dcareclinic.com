<?php
require '../../dbcon.php';
header('Content-Type: application/json');

// ฟังก์ชันแปลงวันที่เป็นรูปแบบไทย
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

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'Missing booking ID']);
    exit;
}
$vouchers = []; // ประกาศตัวแปรให้เป็น array ว่างก่อน
$booking_id = intval($_GET['id']);

try {
    // ดึงข้อมูลการจองพร้อมข้อมูลที่เกี่ยวข้อง
    $sql = "SELECT 
        cb.*,
        c.cus_id,
        c.cus_firstname,
        c.cus_lastname,
        c.cus_nickname,
        c.cus_tel,
        c.cus_email,
        c.cus_address,
        c.cus_district,
        c.cus_city,
        c.cus_province,
        c.cus_postal_code,
        r.room_name,
        r.room_id,
        u.users_fname,
        u.users_lname,
        oc.oc_id,
        oc.order_net_total,
        oc.order_payment,
        oc.order_payment_date,
        oc.deposit_amount,
        oc.deposit_payment_type,
        oc.deposit_date,
        fn.note as follow_up_note
    FROM course_bookings cb
    LEFT JOIN customer c ON cb.cus_id = c.cus_id
    LEFT JOIN rooms r ON cb.room_id = r.room_id
    LEFT JOIN users u ON cb.users_id = u.users_id
    LEFT JOIN order_course oc ON cb.id = oc.course_bookings_id
    LEFT JOIN follow_up_notes fn ON cb.id = fn.booking_id
    WHERE cb.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    if (!$booking) {
        echo json_encode(['error' => 'Booking not found']);
        exit;
    }

    // ดึงข้อมูลคอร์สที่จอง
    $sql_courses = "SELECT 
        c.course_id,
        c.course_name,
        c.course_amount,
        c.course_price,
        od.od_amount,
        od.od_price,
        od.detail as course_detail,
        COALESCE(cu.used_sessions, 0) as used_sessions
    FROM order_detail od
    JOIN course c ON od.course_id = c.course_id
    LEFT JOIN (
        SELECT order_detail_id, COUNT(*) as used_sessions 
        FROM course_usage 
        GROUP BY order_detail_id
    ) cu ON od.od_id = cu.order_detail_id
    WHERE od.oc_id = ?";

    $stmt = $conn->prepare($sql_courses);
    $stmt->bind_param('i', $booking['oc_id']);
    $stmt->execute();
    $result_courses = $stmt->get_result();
    $courses = [];

    if ($booking['oc_id']) { // ตรวจสอบว่ามี order_id ก่อน
        // ดึงข้อมูลบัตรกำนัลที่ใช้
        $sql_vouchers = "SELECT 
            gv.voucher_code,
            gv.amount,
            gv.discount_type,
            vuh.amount_used
        FROM voucher_usage_history vuh
        JOIN gift_vouchers gv ON vuh.voucher_id = gv.voucher_id
        WHERE vuh.order_id = ?";

        $stmt = $conn->prepare($sql_vouchers);
        $stmt->bind_param('i', $booking['oc_id']);
        $stmt->execute();
        $result_vouchers = $stmt->get_result();

        while ($voucher = $result_vouchers->fetch_assoc()) {
            $vouchers[] = [
                'code' => $voucher['voucher_code'],
                'amount' => $voucher['amount'],
                'type' => $voucher['discount_type'],
                'used_amount' => $voucher['amount_used']
            ];
        }
    }

    while ($course = $result_courses->fetch_assoc()) {
        // ดึงข้อมูลทรัพยากรที่ใช้ในแต่ละคอร์ส
        $sql_resources = "SELECT 
            ocr.*,
            CASE 
                WHEN ocr.resource_type = 'drug' THEN d.drug_name
                WHEN ocr.resource_type = 'tool' THEN t.tool_name
                WHEN ocr.resource_type = 'accessory' THEN a.acc_name
            END as resource_name,
            CASE 
                WHEN ocr.resource_type = 'drug' THEN u1.unit_name
                WHEN ocr.resource_type = 'tool' THEN u2.unit_name
                WHEN ocr.resource_type = 'accessory' THEN u3.unit_name
            END as unit_name
        FROM order_course_resources ocr
        LEFT JOIN drug d ON ocr.resource_type = 'drug' AND ocr.resource_id = d.drug_id
        LEFT JOIN tool t ON ocr.resource_type = 'tool' AND ocr.resource_id = t.tool_id
        LEFT JOIN accessories a ON ocr.resource_type = 'accessory' AND ocr.resource_id = a.acc_id
        LEFT JOIN unit u1 ON d.drug_unit_id = u1.unit_id
        LEFT JOIN unit u2 ON t.tool_unit_id = u2.unit_id
        LEFT JOIN unit u3 ON a.acc_unit_id = u3.unit_id
        WHERE ocr.order_id = ? AND ocr.course_id = ?";

        $stmt = $conn->prepare($sql_resources);
        $stmt->bind_param('ii', $booking['oc_id'], $course['course_id']);
        $stmt->execute();
        $result_resources = $stmt->get_result();
        
        $resources = [];
        while ($resource = $result_resources->fetch_assoc()) {
            $resources[] = [
                'type' => $resource['resource_type'],
                'name' => $resource['resource_name'],
                'quantity' => $resource['quantity'],
                'unit' => $resource['unit_name']
            ];
        }

        // ดึงข้อมูลบัตรกำนัลที่ใช้
        

        

        while ($voucher = $result_vouchers->fetch_assoc()) {
            $vouchers[] = [
                'code' => $voucher['voucher_code'],
                'amount' => $voucher['amount'],
                'type' => $voucher['discount_type'],
                'used_amount' => $voucher['amount_used']
            ];
        }

        $courses[] = [
            'id' => $course['course_id'],
            'name' => $course['course_name'],
            'total_sessions' => $course['course_amount'],
            'used_sessions' => $course['used_sessions'],
            'price' => $course['od_price'],
            'amount' => $course['od_amount'],
            'detail' => $course['course_detail'],
            'resources' => $resources
        ];
    }

    // จัดเตรียมข้อมูลสำหรับส่งกลับ
    $response = [
        'booking' => [
            'id' => $booking['id'],
            'datetime' => convertToThaiDate($booking['booking_datetime']),
            'status' => $booking['status'],
            'status_class' => match($booking['status']) {
                'confirmed' => 'success',
                'cancelled' => 'danger',
                default => 'warning'
            },
            'status_text' => match($booking['status']) {
                'confirmed' => 'ยืนยันแล้ว',
                'cancelled' => 'ยกเลิกแล้ว',
                default => 'รอยืนยัน'
            },
            'room' => $booking['room_name'],
            'follow_up' => [
                'is_follow_up' => (bool)$booking['is_follow_up'],
                'note' => $booking['follow_up_note']
            ]
        ],
        'customer' => [
            'id' => $booking['cus_id'],
            'fullname' => $booking['cus_firstname'] . ' ' . $booking['cus_lastname'],
            'nickname' => $booking['cus_nickname'],
            'tel' => $booking['cus_tel'],
            'email' => $booking['cus_email'],
            'address' => implode(' ', array_filter([
                $booking['cus_address'],
                $booking['cus_district'],
                $booking['cus_city'],
                $booking['cus_province'],
                $booking['cus_postal_code']
            ]))
        ],
        'courses' => $courses,
        'payment' => [
            'total' => floatval($booking['order_net_total'] ?? 0),
            'status' => $booking['order_payment'],
            'status_class' => match($booking['order_payment'] ?? '') {
                'เงินสด' => 'success',
                'บัตรเครดิต' => 'info',
                'โอนเงิน' => 'primary',
                default => 'danger'
            },
            'payment_date' => $booking['order_payment_date'] ? convertToThaiDate($booking['order_payment_date']) : null,
            'deposit' => [
                'amount' => floatval($booking['deposit_amount'] ?? 0),
                'payment_type' => $booking['deposit_payment_type'],
                'date' => $booking['deposit_date'] ? convertToThaiDate($booking['deposit_date']) : null
            ],
            'vouchers' => $vouchers // ใช้ array ที่ประกาศไว้ด้านบน
        ],
        'staff' => [
            'id' => $booking['users_id'],
            'name' => $booking['users_fname'] . ' ' . $booking['users_lname']
        ]
    ];
    $response = json_decode(json_encode($response), true);
    echo json_encode($response, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$conn->close();
?>