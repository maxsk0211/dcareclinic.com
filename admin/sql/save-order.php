<?php
require 'dbcon.php';

$data = json_decode(file_get_contents('php://input'), true);

$conn->begin_transaction();

try {
    // อัปเดตข้อมูลหลักของคำสั่งซื้อ
    $stmt = $conn->prepare("UPDATE order_course SET order_payment = ?, booking_datetime = ? WHERE oc_id = ?");
    $stmt->bind_param("ssi", $data['payment_method'], $data['booking_datetime'], $data['id']);
    $stmt->execute();

    // ลบรายการคอร์สและทรัพยากรเดิมทั้งหมด
    $stmt = $conn->prepare("DELETE FROM order_detail WHERE oc_id = ?");
    $stmt->bind_param("i", $data['id']);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM order_course_resources WHERE order_id = ?");
    $stmt->bind_param("i", $data['id']);
    $stmt->execute();

    // เพิ่มรายการคอร์สและทรัพยากรใหม่
    foreach ($data['courses'] as $course) {
        $stmt = $conn->prepare("INSERT INTO order_detail (oc_id, course_id, od_amount, od_price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $data['id'], $course['id'], $course['amount'], $course['price']);
        $stmt->execute();

        foreach ($course['resources'] as $resource) {
            $stmt = $conn->prepare("INSERT INTO order_course_resources (order_id, course_id, resource_type, resource_id, quantity) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisid", $data['id'], $course['id'], $resource['type'], $resource['id'], $resource['quantity']);
            $stmt->execute();
        }
    }

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}