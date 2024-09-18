<?php
require '../../dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
    $staff_type = isset($_POST['staff_type']) ? $_POST['staff_type'] : '';
    $staff_id = isset($_POST['staff_id']) ? intval($_POST['staff_id']) : 0;
    $df_amount = isset($_POST['df_amount']) ? floatval($_POST['df_amount']) : 0;
    $df_type = isset($_POST['df_type']) ? $_POST['df_type'] : '';

    if ($service_id == 0 || empty($staff_type) || $staff_id == 0 || $df_amount == 0 || empty($df_type)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }

    $sql = "INSERT INTO service_staff_records (service_id, staff_id, staff_type, staff_df, staff_df_type) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisds", $service_id, $staff_id, $staff_type, $df_amount, $df_type);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error inserting record: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();