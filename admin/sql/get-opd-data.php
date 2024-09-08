<?php
session_start();
require '../../dbcon.php';

if (isset($_GET['opd_id'])) {
    $opd_id = intval($_GET['opd_id']);
    
    $sql = "SELECT * FROM opd WHERE opd_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $opd_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $opd_data = $result->fetch_assoc();
        // เพิ่มข้อมูลที่ต้องการส่งกลับ
        $response_data = [
            'Weight' => $opd_data['Weight'],
            'Height' => $opd_data['Height'],
            'BMI' => $opd_data['BMI'],
            'FBS' => $opd_data['FBS'],
            'Systolic' => $opd_data['Systolic'],
            'Pulsation' => $opd_data['Pulsation'],
            'opd_diagnose' => $opd_data['opd_diagnose'],
            'opd_note' => $opd_data['opd_note'],
            'opd_smoke' => $opd_data['opd_smoke'],
            'opd_alcohol' => $opd_data['opd_alcohol'],
            'drug_allergy' => $opd_data['drug_allergy'],
            'food_allergy' => $opd_data['food_allergy']
        ];
        echo json_encode(['success' => true, 'data' => $response_data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูล OPD']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่ได้ระบุ OPD ID']);
}

$conn->close();
?>