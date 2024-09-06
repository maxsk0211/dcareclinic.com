<?php
session_start();
include '../chk-session.php';
require '../../dbcon.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $queue_id = $_POST['queue_id'];
    $cus_id = $_POST['cus_id'];
    $course_id = $_POST['course_id'];
    $nurse_id = $_SESSION['users_id']; // สมมติว่าใช้ ID ของผู้ใช้ที่ล็อกอินอยู่

    $weight = floatval($_POST['weight']);
    $height = floatval($_POST['height']);
    $bmi = floatval($_POST['bmi']);
    $fbs = floatval($_POST['fbs']);
    $systolic = floatval($_POST['systolic']);
    $pulsation = floatval($_POST['pulsation']);
    $opd_diagnose = mysqli_real_escape_string($conn, $_POST['opd_diagnose']);
    $opd_note = mysqli_real_escape_string($conn, $_POST['opd_note']);
    $opd_smoke = mysqli_real_escape_string($conn, $_POST['opd_smoke']);
    $opd_alcohol = mysqli_real_escape_string($conn, $_POST['opd_alcohol']);
    $opd_physical = mysqli_real_escape_string($conn, $_POST['opd_physical']);

    // ตรวจสอบว่ามีข้อมูล OPD อยู่แล้วหรือไม่
    $check_sql = "SELECT opd_id FROM opd WHERE queue_id = $queue_id";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // อัพเดตข้อมูลที่มีอยู่
        $opd_id = $check_result->fetch_assoc()['opd_id'];
        $sql = "UPDATE opd SET 
                Weight = $weight,
                Height = $height,
                BMI = $bmi,
                FBS = $fbs,
                Systolic = $systolic,
                Pulsation = $pulsation,
                opd_diagnose = '$opd_diagnose',
                opd_note = '$opd_note',
                opd_smoke = '$opd_smoke',
                opd_alcohol = '$opd_alcohol',
                opd_physical = '$opd_physical',
                nurse_id = $nurse_id,
                updated_at = CURRENT_TIMESTAMP
                WHERE opd_id = $opd_id";
    } else {
        // เพิ่มข้อมูลใหม่
        $sql = "INSERT INTO opd (queue_id, cus_id, course_id, nurse_id, Weight, Height, BMI, FBS, Systolic, Pulsation, opd_diagnose, opd_note, opd_smoke, opd_alcohol, opd_physical)
                VALUES ($queue_id, $cus_id, $course_id, $nurse_id, $weight, $height, $bmi, $fbs, $systolic, $pulsation, '$opd_diagnose', '$opd_note', '$opd_smoke', '$opd_alcohol', '$opd_physical')";
    }

    if ($conn->query($sql) === TRUE) {
        // อัพเดตสถานะคิวเป็น 'completed'
        $update_queue_sql = "UPDATE service_queue SET service_status = 'completed' WHERE queue_id = $queue_id";
        $conn->query($update_queue_sql);

        $_SESSION['success_msg'] = "บันทึกข้อมูล OPD สำเร็จ";
        header("Location: ../queue-management.php");
    } else {
        $_SESSION['error_msg'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $conn->error;
        header("Location: ../opd.php?queue_id=$queue_id");
    }
} else {
    header("Location: ../queue-management.php");
}
exit();