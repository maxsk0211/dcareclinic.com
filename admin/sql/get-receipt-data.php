<?php
require '../../dbcon.php';

// เพิ่ม error reporting เพื่อ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

$oc_id = isset($_GET['oc_id']) ? intval($_GET['oc_id']) : 0;

if ($oc_id > 0) {
    $receiptData = getReceiptData($oc_id);
    if ($receiptData) {
        echo json_encode($receiptData);
    } else {
        echo json_encode(['error' => 'ไม่พบข้อมูลใบเสร็จ']);
    }
} else {
    echo json_encode(['error' => 'ไม่ได้ระบุ oc_id']);
}

function getReceiptData($oc_id) {
    global $conn;
    
    $sql = "SELECT oc.*, c.cus_title, c.cus_firstname, c.cus_lastname, 
            c.cus_address, c.cus_district, c.cus_city, c.cus_province, c.cus_postal_code, 
            c.cus_id_card_number,
            u.users_fname, u.users_lname,
            GROUP_CONCAT(CONCAT(co.course_id, ':', co.course_name, ':', od.od_amount, ':', od.od_price) SEPARATOR '|') as items
            FROM order_course oc
            JOIN customer c ON oc.cus_id = c.cus_id
            JOIN order_detail od ON oc.oc_id = od.oc_id
            JOIN course co ON od.course_id = co.course_id
            LEFT JOIN users u ON oc.seller_id = u.users_id
            WHERE oc.oc_id = ?
            GROUP BY oc.oc_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $oc_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        
        // จัดรูปแบบที่อยู่
        $data['full_address'] = implode(' ', array_filter([
            $data['cus_address'],
            $data['cus_district'],
            $data['cus_city'],
            $data['cus_province'],
            $data['cus_postal_code']
        ]));

        // แยกข้อมูลที่อยู่
        $data['address_parts'] = [
            'address' => $data['cus_address'],
            'district' => $data['cus_district'],
            'city' => $data['cus_city'],
            'province' => $data['cus_province'],
            'postal_code' => $data['cus_postal_code']
        ];

        // แปลงข้อมูลรายการสินค้า
        $itemsArray = [];
        $total = 0;
        $items = explode('|', $data['items']);
        foreach ($items as $item) {
            list($courseId, $courseName, $amount, $price) = explode(':', $item);
            $subtotal = $amount * $price;
            $total += $subtotal;
            $itemsArray[] = [
                'course_id' => 'C-' . str_pad($courseId, 6, '0', STR_PAD_LEFT),
                'course_name' => $courseName,
                'amount' => $amount,
                'price' => $price,
                'subtotal' => $subtotal
            ];
        }
        $data['items_array'] = $itemsArray;
        $data['total_amount'] = $total;

        // จัดรูปแบบวันที่
        $data['formatted_order_datetime'] = date('d/m/Y H:i', strtotime($data['order_datetime']));
        $data['formatted_deposit_date'] = $data['deposit_date'] ? date('d/m/Y H:i', strtotime($data['deposit_date'])) : '-';
        $data['formatted_order_payment_date'] = $data['order_payment_date'] ? date('d/m/Y H:i', strtotime($data['order_payment_date'])) : '-';

        // เพิ่มข้อมูลผู้ขาย
        $data['seller_name'] = $data['users_fname'] . ' ' . $data['users_lname'];

        return $data;
    }
    return null;
}