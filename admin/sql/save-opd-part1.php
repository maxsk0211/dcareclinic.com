<?php
session_start();
require '../../dbcon.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $queue_id = $_POST['queue_id'];
    $cus_id = $_POST['cus_id'];
    $course_id = $_POST['course_id'];
    $weight = floatval($_POST['weight']);
    $height = floatval($_POST['height']);
    $bmi = floatval($_POST['bmi']);
    $fbs = floatval($_POST['fbs']);
    $systolic = floatval($_POST['systolic']);
    $pulsation = floatval($_POST['pulsation']);

    // ตรวจสอบว่ามีข้อมูล OPD อยู่แล้วหรือไม่
    $check_sql = "SELECT opd_id FROM opd WHERE queue_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $queue_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // อัพเดตข้อมูลที่มีอยู่
        $row = $result->fetch_assoc();
        $opd_id = $row['opd_id'];
        $sql = "UPDATE opd SET Weight = ?, Height = ?, BMI = ?, FBS = ?, Systolic = ?, Pulsation = ? WHERE opd_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ddddddi", $weight, $height, $bmi, $fbs, $systolic, $pulsation, $opd_id);
    } else {
        // เพิ่มข้อมูลใหม่
        $sql = "INSERT INTO opd (queue_id, cus_id, Weight, Height, BMI, FBS, Systolic, Pulsation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iidddddd", $queue_id, $cus_id, $weight, $height, $bmi, $fbs, $systolic, $pulsation);
    }

    if ($stmt->execute()) {
        $opd_id = $result->num_rows > 0 ? $opd_id : $conn->insert_id;
        echo json_encode(['success' => true, 'opd_id' => $opd_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close(); 