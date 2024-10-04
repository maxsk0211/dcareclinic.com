<?php
require '../../dbcon.php';

$doctor_id = $_GET['doctor_id'];
$oc_id = $_GET['oc_id'];

// ดึงข้อมูลแพทย์
$doctor_sql = "SELECT users_id as id, CONCAT(users_fname, ' ', users_lname) as name, users_license as license_number FROM users WHERE users_id = ?";
$doctor_stmt = $conn->prepare($doctor_sql);
$doctor_stmt->bind_param("i", $doctor_id);
$doctor_stmt->execute();
$doctor_result = $doctor_stmt->get_result();
$doctor_data = $doctor_result->fetch_assoc();

// ดึงข้อมูลลูกค้าและ OPD
$data_sql = "SELECT c.*, oc.oc_id, cb.id as booking_id, sq.queue_date, sq.queue_time, o.*
             FROM order_course oc
             JOIN customer c ON oc.cus_id = c.cus_id
             JOIN course_bookings cb ON oc.course_bookings_id = cb.id
             JOIN service_queue sq ON cb.id = sq.booking_id
             LEFT JOIN opd o ON sq.queue_id = o.queue_id
             WHERE oc.oc_id = ?";
$data_stmt = $conn->prepare($data_sql);
$data_stmt->bind_param("i", $oc_id);
$data_stmt->execute();
$data_result = $data_stmt->get_result();
$customer_data = $data_result->fetch_assoc();

if ($doctor_data && $customer_data) {
    $response = [
        'success' => true,
        'doctor' => $doctor_data,
        'customer' => $customer_data,
        'queue' => [
            'queue_date' => $customer_data['queue_date'],
            'queue_time' => $customer_data['queue_time']
        ],
        'opd' => [
            'Weight' => $customer_data['Weight'],
            'Height' => $customer_data['Height'],
            'Systolic' => $customer_data['Systolic'],
            'Pulsation' => $customer_data['Pulsation'],
            'opd_diagnose' => $customer_data['opd_diagnose'],
            'opd_note' => $customer_data['opd_note']
        ]
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'ไม่พบข้อมูลที่ต้องการ'
    ];
}

echo json_encode($response);
?>