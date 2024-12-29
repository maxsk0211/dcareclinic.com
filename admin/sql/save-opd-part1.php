<?php
session_start();
require '../../dbcon.php';

// ตรวจสอบสิทธิ์
$canEditPart1 = in_array($_SESSION['position_id'], [1, 2, 3, 4]);

if (!$canEditPart1) {
    echo json_encode(['success' => false, 'message' => 'คุณไม่มีสิทธิ์ในการบันทึกข้อมูลนี้']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn->begin_transaction();
    
    try {
        $queue_id = $_POST['queue_id'];
        $cus_id = $_POST['cus_id'];
        $weight = floatval($_POST['weight']);
        $height = floatval($_POST['height']);
        $bmi = floatval($_POST['bmi']);
        $fbs = floatval($_POST['fbs']);
        $systolic = floatval($_POST['systolic']);
        $pulsation = floatval($_POST['pulsation']);
        $smoking = mysqli_real_escape_string($conn, $_POST['smoking']);
        $alcohol = mysqli_real_escape_string($conn, $_POST['alcohol']);
        $drug_allergy = mysqli_real_escape_string($conn, $_POST['drug_allergy']);
        $food_allergy = mysqli_real_escape_string($conn, $_POST['food_allergy']);

        // อัพเดตข้อมูลในตาราง customer
        $update_customer_sql = "UPDATE customer SET 
            weight = ?,
            height = ?,
            cus_drugallergy = ?,
            cus_congenital = ?
            WHERE cus_id = ?";
        
        $stmt_customer = $conn->prepare($update_customer_sql);
        $stmt_customer->bind_param("ddssi", $weight, $height, $drug_allergy, $food_allergy, $cus_id);
        $stmt_customer->execute();

        // ตรวจสอบและบันทึกข้อมูล OPD
        $check_sql = "SELECT opd_id FROM opd WHERE queue_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $queue_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            // อัพเดตข้อมูลที่มีอยู่
            $row = $result->fetch_assoc();
            $opd_id = $row['opd_id'];
            $sql = "UPDATE opd SET 
                    Weight = ?, Height = ?, BMI = ?, 
                    FBS = ?, Systolic = ?, Pulsation = ?, 
                    opd_smoke = ?, opd_alcohol = ?, 
                    drug_allergy = ?, food_allergy = ? 
                    WHERE opd_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ddddddssssi", 
                $weight, $height, $bmi, 
                $fbs, $systolic, $pulsation, 
                $smoking, $alcohol, 
                $drug_allergy, $food_allergy, 
                $opd_id);
        } else {
            // เพิ่มข้อมูลใหม่
            $sql = "INSERT INTO opd (
                    queue_id, cus_id, Weight, Height, 
                    BMI, FBS, Systolic, Pulsation, 
                    opd_smoke, opd_alcohol, 
                    drug_allergy, food_allergy) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iidddddsssss", 
                $queue_id, $cus_id, $weight, $height, 
                $bmi, $fbs, $systolic, $pulsation, 
                $smoking, $alcohol, 
                $drug_allergy, $food_allergy);
        }

        if ($stmt->execute()) {
            $opd_id = $result->num_rows > 0 ? $opd_id : $conn->insert_id;
            $conn->commit();
            echo json_encode(['success' => true, 'opd_id' => $opd_id]);
        } else {
            throw new Exception("Error executing statement");
        }

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()]);
    }

    if (isset($stmt)) $stmt->close();
    if (isset($stmt_customer)) $stmt_customer->close();
    if (isset($check_stmt)) $check_stmt->close();

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>