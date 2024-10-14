<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

$canEditPart1 = $_SESSION['can_edit_opd_part1'] ?? false;
$canEditPart2 = $_SESSION['can_edit_opd_part2'] ?? false;

$queue_id = isset($_GET['queue_id']) ? $_GET['queue_id'] : null;

if (!$queue_id) {
    die("ไม่พบข้อมูลคิว");
}

// ดึงข้อมูลคิวและลูกค้า
$sql = "SELECT sq.*, c.cus_id, c.cus_firstname, c.cus_lastname, 
               (SELECT od.course_id 
                FROM order_detail od 
                JOIN order_course oc ON od.oc_id = oc.oc_id
                WHERE oc.course_bookings_id = cb.id 
                LIMIT 1) AS course_id
        FROM service_queue sq
        LEFT JOIN customer c ON sq.cus_id = c.cus_id
        LEFT JOIN course_bookings cb ON sq.booking_id = cb.id
        WHERE sq.queue_id = $queue_id";
$result = $conn->query($sql);

if ($result === false) {
    die("เกิดข้อผิดพลาดในการค้นหาข้อมูล: " . $conn->error);
}

if ($result->num_rows == 0) {
    die("ไม่พบข้อมูลคิวที่ระบุ");
}

$queue_data = $result->fetch_assoc();

// ตรวจสอบว่ามีข้อมูล OPD อยู่แล้วหรือไม่
$sql_check_opd = "SELECT * FROM opd WHERE queue_id = $queue_id";
$result_check_opd = $conn->query($sql_check_opd);

if ($result_check_opd === false) {
    die("เกิดข้อผิดพลาดในการค้นหาข้อมูล OPD: " . $conn->error);
}

$opd_data = $result_check_opd->num_rows > 0 ? $result_check_opd->fetch_assoc() : null;

// เพิ่มการกำหนดค่าเริ่มต้นสำหรับฟิลด์เหล่านี้
$smoking = $opd_data['opd_smoke'] ?? '';
$alcohol = $opd_data['opd_alcohol'] ?? '';
$drug_allergy = $opd_data['drug_allergy'] ?? '';
$food_allergy = $opd_data['food_allergy'] ?? '';


$background_images = glob("../img/drawing/default/*");

// ดึงข้อมูลวันที่ปิดทำการ
$sql_closures = "SELECT closure_date FROM clinic_closures";
$result_closures = $conn->query($sql_closures);
$closed_dates = [];
while ($row = $result_closures->fetch_object()) {
    if ($row->closure_date) {
        $closed_date = date('Y-m-d', strtotime($row->closure_date));
        if ($closed_date) {
            $closed_dates[] = $closed_date;
        }
    }
}

// ดึงข้อมูลเวลาทำการ
$sql_hours = "SELECT * FROM clinic_hours";
$result_hours = $conn->query($sql_hours);
$clinic_hours = [];
$closed_days = [];
while ($row = $result_hours->fetch_object()) {
    $clinic_hours[$row->day_of_week] = [
        'start_time' => $row->start_time,
        'end_time' => $row->end_time,
        'is_closed' => $row->is_closed
    ];
    if ($row->is_closed == 1) {
        $closed_days[] = $row->day_of_week;
    }
}

// ดึงข้อมูลการจองที่มีอยู่
$sql_bookings = "SELECT booking_datetime FROM course_bookings WHERE status IN ('pending', 'confirmed')";
$result_bookings = $conn->query($sql_bookings);
$booked_slots = [];
while ($row = $result_bookings->fetch_object()) {
    if ($row->booking_datetime) {
        $booked_slots[] = $row->booking_datetime;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>OPD - D Care Clinic</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet" />
    <!-- Icons -->
    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />
    <!-- Page CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/sweetalert2/sweetalert2.css" />

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
<style>
@media print {
    @page {
        size: A5;
/*        margin: 10mm; /* กำหนดระยะขอบของหน้า */*/
    }
    body {
        width: 148mm;
        height: 210mm;
        margin: 0;
        padding: 10mm;
        font-family: Arial, sans-serif;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    td {
        padding: 2mm;
        border: 1px solid #ddd;
    }
}
    .opd-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .opd-header h2 {
        margin: 0;
        font-size: 28px;
    }
    .opd-info {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
    }
    .opd-info span {
        font-size: 18px;
    }
    .form-section {
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .form-section h3 {
        color: #007bff;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
        margin-bottom: 20px;
        font-size: 24px;
    }
    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #495057;
    }
    .form-control, .form-select {
        font-size: 16px;
        padding: 10px;
        border: 2px solid #ced4da;
        border-radius: 8px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        background-color: #f8f9fa;
    }
    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .form-control:disabled, .form-select:disabled {
        background-color: #e9ecef;
        opacity: 1;
        color: #6c757d;
        border-color: #ced4da;
    }
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 12px 24px;
        font-size: 20px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    textarea.form-control {
        min-height: 120px;
    }
    #drawingModal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.9);
    }
    .drawing-container {
        display: flex;
        height: 100%;
        max-width: 1600px;
        margin: 0 auto;
    }
    .drawing-tools {
        width: 200px;
        background-color: #f0f0f0;
        padding: 20px;
        display: flex;
        flex-direction: column;
    }
    .drawing-tools h4, #backgroundSelector h4 {
        margin-bottom: 15px;
        color: #333;
    }
    .color-btn, .action-btn {
        margin-bottom: 10px;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .color-btn:hover, .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .black-btn { background-color: #333; color: white; }
    .red-btn { background-color: #ff4136; color: white; }
    .blue-btn { background-color: #0074d9; color: white; }
    .clear-btn { background-color: #ff851b; color: white; }
    .save-btn { background-color: #2ecc40; color: white; }
    .close-btn { background-color: #aaaaaa; color: white; }
    .upload-btn { background-color: #3498db; color: white; width: 100%; }
    .canvas-container {
        flex-grow: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #fff;
    }
    #drawingCanvas {
        border: 1px solid #ddd;
    }
    #backgroundSelector {
        width: 200px;
        background-color: #f0f0f0;
        padding: 20px;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }
    .background-images-container {
        flex-grow: 1;
        overflow-y: auto;
        margin-bottom: 20px;
    }
    #backgroundSelector img {
        width: 100%;
        margin-bottom: 10px;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s ease;
    }
    #backgroundSelector img:hover {
        transform: scale(1.05);
    }
    #backgroundSelector img.selected {
        border-color: #0074d9;
    }
    .drawing-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: flex-start;
        margin-top: 20px; /* เพิ่มระยะห่างด้านบน */
    }
    .drawing-item {
        position: relative;
        width: 150px;
        margin-bottom: 15px; /* เพิ่มระยะห่างด้านล่าง */
    }
    .drawing-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }
    .drawing-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        transition: transform 0.3s ease;
        cursor: pointer;
    }
    .drawing-item:hover img {
        transform: scale(1.05);
    }
    .drawing-datetime {
        font-size: 12px;
        color: #666;
        text-align: center;
        margin-top: 5px;
    }
    .delete-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: rgba(220, 53, 69, 0.8);
        color: white;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        font-size: 16px;
        line-height: 25px;
        text-align: center;
        cursor: pointer;
        transition: background-color 0.3s ease;
        display: none; /* ซ่อนปุ่มลบเริ่มต้น */
        z-index: 2500;
    }
    .drawing-item:hover .delete-btn {
        display: block; /* แสดงปุ่มลบเมื่อ hover */
    }
    .delete-btn:hover {
        background-color: rgba(220, 53, 69, 1);
    }
    .image-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .image-modal.active {
        opacity: 1;
        visibility: visible;
    }
    .modal-content {
        max-width: 90%;
        max-height: 90%;
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        padding: 20px;
        box-sizing: border-box;
        position: relative;
    }
    .modal-content img {
        display: block;
        max-width: 100%;
        max-height: calc(90vh - 100px);
        margin: 0 auto;
        object-fit: contain;
    }
    .close-modal {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        font-size: 20px;
        line-height: 30px;
        text-align: center;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .close-modal:hover {
        background-color: rgba(0, 0, 0, 0.8);
    }
    .nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        padding: 16px;
        font-size: 18px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .nav-btn:hover {
        background-color: rgba(0, 0, 0, 0.8);
    }
    .prev-btn {
        left: 10px;
    }
    .next-btn {
        right: 10px;
    }
    .modal-datetime {
        margin-top: 10px;
        text-align: center;
        font-size: 14px;
        color: #666;
    }
    .btn-primary, .btn-secondary {
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    .btn-primary:hover, .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .color-btn.active {
        border: 2px solid #fff;
        box-shadow: 0 0 5px rgba(0,0,0,0.5);
    }
    .time-slot {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .time-slot.booked {
        background-color: #ff8785;
        cursor: not-allowed;
    }
    .time-slot.selected {
        background-color: #8cff85;
    }
    .time-slot:hover:not(.disabled) {
        transform: scale(1.05);
    }
.gallery-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: flex-start;
}

.gallery-item {
    position: relative;
    width: calc(33.333% - 10px);
    margin-bottom: 15px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.gallery-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

.gallery-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-item:hover img {
    transform: scale(1.05);
}

.gallery-item-type {
    position: absolute;
    top: 10px;
    left: 10px;
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
}

.delete-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: rgba(220, 53, 69, 0.8);
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    font-size: 18px;
    line-height: 30px;
    text-align: center;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: none;
}

.gallery-item:hover .delete-btn {
    display: block;
}

.delete-btn:hover {
    background-color: rgba(220, 53, 69, 1);
}

#imageModal .modal-content {
    border-radius: 10px;
    overflow: hidden;
}

#imageModal .modal-body {
    padding: 0;
}

#imageModal .modal-image-container {
    position: relative;
    overflow: hidden;
    border-radius: 10px 10px 0 0;
}

#modalImage {
    width: 100%;
    height: auto;
    transition: transform 0.3s ease;
}

#imageModal .modal-image-container:hover #modalImage {
    transform: scale(1.05);
}

#imageModal .modal-info {
    padding: 20px;
}

#imageModal .modal-footer {
    border-top: none;
    padding-top: 0;
}

    </style>
</head>
<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">
            <!-- Navbar -->
            <?php include 'navbar.php'; ?>
            <!-- / Navbar -->

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Menu -->
                    <?php include 'menu.php'; ?>
                    <!-- / Menu -->
                    <?php
                    // ... (โค้ดอื่นๆ ที่มีอยู่เดิม) ...

                    // เพิ่มส่วนนี้หลังจาก include 'menu.php';
                    if (isset($_SESSION['success_msg'])) {
                        echo "<div class='alert alert-success'>" . $_SESSION['success_msg'] . "</div>";
                        unset($_SESSION['success_msg']);
                    }
                    if (isset($_SESSION['error_msg'])) {
                        echo "<div class='alert alert-danger'>" . $_SESSION['error_msg'] . "</div>";
                        unset($_SESSION['error_msg']);
                    }

                    // ... (โค้ดอื่นๆ ที่มีอยู่เดิม) ...
                    ?>
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="opd-header">
                            <h2>การตรวจเบื้องต้น (OPD)</h2>
                            <div class="opd-info">
                                <span>HN: <?php echo 'HN-' . str_pad($queue_data['cus_id'], 6, '0', STR_PAD_LEFT); ?></span>
                                <span>ชื่อ-นามสกุล: <?php echo $queue_data['cus_firstname'] . ' ' . $queue_data['cus_lastname']; ?></span>
                                <span>หมายเลขคิว: <?php echo $queue_data['queue_number']; ?></span>
                                <button id="printButton" class="btn btn-primary">พิมพ์ OPD</button>
                                <input type="hidden" name="cus_id" value="<?=$queue_data['cus_id']?>">
                            </div>
                        </div>

                        <!-- ส่วนแรกของฟอร์ม -->
                        <form id="opdFormPart1" method="post">
                        <div class="alert <?php echo $canEditPart1 ? 'alert-info' : 'alert-warning'; ?> mb-4">
                            <strong>สถานะ:</strong> 
                            <?php echo $canEditPart1 ? 'คุณสามารถแก้ไขข้อมูลในส่วนนี้ได้' : 'คุณสามารถดูข้อมูลเท่านั้น'; ?>
                        </div>
                            <input type="hidden" name="queue_id" value="<?php echo $queue_id; ?>">
                            <input type="hidden" name="cus_id" value="<?php echo $queue_data['cus_id']; ?>">
                            <input type="hidden" name="course_id" value="<?php echo $queue_data['course_id']; ?>">

                            <div class="form-section border-2 border-primary">
                                <h3>ข้อมูลสุขภาพทั่วไป</h3>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="weight" class="form-label">น้ำหนัก (กก.)</label>
                                        <input type="number" class="form-control" id="weight" name="weight" step="0.1" required value="<?php echo $opd_data['Weight'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="height" class="form-label">ส่วนสูง (ซม.)</label>
                                        <input type="number" class="form-control" id="height" name="height" step="0.1" required value="<?php echo $opd_data['Height'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="bmi" class="form-label">BMI</label>
                                        <input type="number" class="form-control" id="bmi" name="bmi" step="0.01" readonly value="<?php echo $opd_data['BMI'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-section border-2 border-primary">
                                <h3>ข้อมูลสัญญาณชีพ</h3>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="fbs" class="form-label">FBS (mg/dL)</label>
                                        <input type="number" class="form-control" id="fbs" name="fbs" step="0.1" required value="<?php echo $opd_data['FBS'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="systolic" class="form-label">ความดันโลหิต (mmHg)</label>
                                        <input type="number" class="form-control" id="systolic" name="systolic" required value="<?php echo $opd_data['Systolic'] ?? ''; ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="pulsation" class="form-label">ชีพจร (ครั้ง/นาที)</label>
                                        <input type="number" class="form-control" id="pulsation" name="pulsation" required value="<?php echo $opd_data['Pulsation'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>
                             <div class="form-section border-2 border-primary">
                                <h3>ข้อมูลพฤติกรรมเสี่ยงและการแพ้</h3>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="smoking" class="form-label">สูบบุหรี่</label>
                                        <select class="form-select" id="smoking" name="smoking" required>
                                            <option value="">เลือก</option>
                                            <option value="ไม่สูบ" <?php echo ($smoking == 'ไม่สูบ') ? 'selected' : ''; ?>>ไม่สูบ</option>
                                            <option value="สูบ" <?php echo ($smoking == 'สูบ') ? 'selected' : ''; ?>>สูบ</option>
                                            <option value="เคยสูบแต่เลิกแล้ว" <?php echo ($smoking == 'เคยสูบแต่เลิกแล้ว') ? 'selected' : ''; ?>>เคยสูบแต่เลิกแล้ว</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="alcohol" class="form-label">ดื่มสุรา</label>
                                        <select class="form-select" id="alcohol" name="alcohol" required>
                                            <option value="">เลือก</option>
                                            <option value="ไม่ดื่ม" <?php echo ($alcohol == 'ไม่ดื่ม') ? 'selected' : ''; ?>>ไม่ดื่ม</option>
                                            <option value="ดื่ม" <?php echo ($alcohol == 'ดื่ม') ? 'selected' : ''; ?>>ดื่ม</option>
                                            <option value="เคยดื่มแต่เลิกแล้ว" <?php echo ($alcohol == 'เคยดื่มแต่เลิกแล้ว') ? 'selected' : ''; ?>>เคยดื่มแต่เลิกแล้ว</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="drug_allergy" class="form-label">แพ้ยา</label>
                                        <textarea class="form-control" id="drug_allergy" name="drug_allergy" rows="2"><?php echo htmlspecialchars($drug_allergy); ?></textarea>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="food_allergy" class="form-label">โรคประจำตัว</label>
                                        <textarea class="form-control" id="food_allergy" name="food_allergy" rows="2"><?php echo htmlspecialchars($food_allergy); ?></textarea>
                                    </div>
                                </div>
                            </div>
                                <input type="hidden" id="opd_id" name="opd_id" value="<?php echo $opd_data['opd_id'] ?? ''; ?>">
                            <?php if ($canEditPart1): ?>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary" id="savePartOne">บันทึกข้อมูลเบื้องต้น</button>
                            </div>
                            <?php endif; ?>
                            <div style="display: none;" id="btnparttwo">
                                <button type="button" class="btn btn-secondary" id="showPartTwo">ถัดไป</button>  
                            </div>
                        </form>

                        <!-- ส่วนที่สองของฟอร์ม (ซ่อนไว้ก่อน) -->

                        <div id="opdFormPart2" style="display: none;">
                        <div class="alert <?php echo $canEditPart1 ? 'alert-info' : 'alert-warning'; ?> mb-4">
                            <strong>สถานะ:</strong> 
                            <?php echo $canEditPart2 ? 'คุณสามารถแก้ไขข้อมูลในส่วนนี้ได้' : 'คุณสามารถดูข้อมูลเท่านั้น'; ?>
                        </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-2 border-primary">
                                            <div class="form-section">
                                                <h3>การตรวจร่างกาย</h3>
                                                <div class="alert alert-warning" id="DrawingModalalert" >
                                                    <p>กรุณาเพิ่มข้อมูล OPD ก่อน</p>
                                                </div>
                                                <div class="mb-3" id="DrawingModalbtn" style="display: none;">
                                                    <!-- <label for="opd_physical" class="form-label">การตรวจร่างกาย</label> -->
                                                    <button type="button" class="btn btn-primary"   onclick="openDrawingModal()">วาดภาพการตรวจร่างกาย</button>
                                                </div>
                                                <div id="savedDrawings" class="drawing-gallery">
                                                    <!-- รูปภาพที่บันทึกแล้วจะแสดงที่นี่ -->
                                                </div>
                                            </div>
                                        </div>
                                        <!-- รูป -->
                                        <div class="card border-2 border-primary">
                                            <div class="form-section">
                                                <h3>รูป Before / After</h3>
                                                <form id="beforeAfterForm" enctype="multipart/form-data">
                                                    <div class="mb-3">
                                                        <label for="beforeAfterImage" class="form-label">เลือกรูปภาพ</label>
                                                        <input type="file" class="form-control" id="beforeAfterImage" name="beforeAfterImage" accept="image/*" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="imageDescription" class="form-label">คำอธิบายรูปภาพ</label>
                                                        <textarea class="form-control" id="imageDescription" name="imageDescription" rows="3" required></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">ประเภทรูปภาพ</label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="imageType" id="imageBefore" value="before" required>
                                                            <label class="form-check-label" for="imageBefore">
                                                                Before
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="imageType" id="imageAfter" value="after" required>
                                                            <label class="form-check-label" for="imageAfter">
                                                                After
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">บันทึกรูปภาพ</button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="card border-2 border-primary mt-4">
                                            <div class="form-section">
                                                <h3>แกลเลอรี Before / After</h3>
                                                <div id="beforeAfterGallery" class="row gallery-container">
                                                    <!-- รูปภาพจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal สำหรับแสดงรูปภาพ -->
                                        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-xl">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="imageModalLabel">รายละเอียดรูปภาพ</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6 modal-image-container">
                                                                <img id="modalImage" src="" alt="Before/After Image" class="img-fluid">
                                                            </div>
                                                            <div class="col-md-6 modal-info">
                                                                <h5 id="modalImageType"></h5>
                                                                <p id="modalImageDescription"></p>
                                                                <p id="modalImageDateTime"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" id="prevImage">ก่อนหน้า</button>
                                                        <button type="button" class="btn btn-secondary" id="nextImage">ถัดไป</button>
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">ปิด</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- รูป -->
                                        <div class="card border-2 border-primary">
                                            <form id="opdFormPart2Form" method="post">
                                                <input type="hidden" name="opd_id" id="opd_id" value="">
                                                <input type="hidden" id="saved_drawings" name="saved_drawings" value="">

                                            
                                                <div class="form-section">
                                                    <h3>การวินิจฉัยและหมายเหตุ</h3>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="opd_diagnose" class="form-label">วินิจฉัย</label>
                                                                <textarea class="form-control" id="opd_diagnose" name="opd_diagnose" rows="3" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="opd_note" class="form-label">หมายเหตุ</label>
                                                                <textarea class="form-control" id="opd_note" name="opd_note" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="text-center">
                                                        <?php if ($canEditPart2): ?>
                                                            <input type="hidden" id="opd_id" name="opd_id" value="<?php echo $opd_data['opd_id'] ?? ''; ?>">
                                                            <button type="submit" class="btn btn-submit">บันทึกข้อมูล</button>
                                                        <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </form>
                                        </div>
                                    <div class="col-md-6">
                                        
                                        <div class="form-section">
                                            <h3>นัดหมายติดตามผล</h3>
                                            <div class="mb-3">
                                                <label for="follow_up_date" class="form-label">วันที่นัดติดตามผล</label>
                                                <input type="text" class="form-control" id="follow_up_date" name="follow_up_date" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">เลือกเวลา</label>
                                                <div id="timeSlots" class="row"></div>
                                            </div>
                                            <div id="selectedTimeInfo" class="mb-3"></div>
                                            <input type="hidden" id="follow_up_time" name="follow_up_time">
                                            <div class="mb-3">
                                                <label for="follow_up_note" class="form-label">หมายเหตุการติดตามผล</label>
                                                <textarea class="form-control" id="follow_up_note" name="follow_up_note" rows="3"></textarea>
                                            </div>
                                            <button type="button" class="btn btn-primary" onclick="saveFollowUp()">บันทึกการนัดติดตามผล</button>
                                        </div>

                                        <div class="form-section">
                                            <h3>ประวัติการนัดติดตามผล</h3>
                                            <div class="alert alert-warning" id="DrawingModalalert" >
                                                <p>กรุณาเพิ่มข้อมูล OPD ก่อน</p>
                                            </div>
                                            <div id="followUpHistory">
                                                <!-- ข้อมูลประวัติการนัดติดตามผลจะถูกเพิ่มที่นี่ -->
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <button type="button" class="btn btn-secondary" id="backToPartOne">ย้อนกลับ</button>

                        </div>
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <?php include 'footer.php'; ?>
                    <!-- / Footer -->
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Modal สำหรับการวาดภาพ -->
    <div id="drawingModal">
        <div class="drawing-container">
            <div id="backgroundSelector">
                <h4>เลือกพื้นหลัง:</h4>
                <div class="background-images-container">
                    <?php foreach($background_images as $index => $image): ?>
                        <img src="<?php echo $image; ?>" onclick="selectBackground('<?php echo $image; ?>', this)" alt="Background <?php echo $index + 1; ?>">
                    <?php endforeach; ?>
                </div>
                <input type="file" id="imageUpload" accept="image/*" style="display: none;">
                <button onclick="document.getElementById('imageUpload').click();" class="action-btn upload-btn">อัปโหลดรูป</button>
            </div>
            <div class="canvas-container">
                <canvas id="drawingCanvas"></canvas>
            </div>
            <div class="drawing-tools">
                <h4>เครื่องมือวาด</h4>
                <button onclick="changeColor('black')" class="color-btn black-btn">สีดำ</button>
                <button onclick="changeColor('red')" class="color-btn red-btn">สีแดง</button>
                <button onclick="changeColor('blue')" class="color-btn blue-btn">สีน้ำเงิน</button>
                <button onclick="clearCanvas()" class="action-btn clear-btn">ล้าง</button>
                <button onclick="saveDrawing()" class="action-btn save-btn">บันทึก</button>
                <button onclick="closeDrawingModal()" class="action-btn close-btn">ปิด</button>
            </div>
        </div>
    </div>
    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <!-- <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js" /> -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>
     <!-- เพิ่ม import สำหรับ Flatpickr ถ้ายังไม่มี -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
    <!-- Page JS -->
<script>

let currentImages = []; // ตัวแปร global สำหรับเก็บข้อมูลรูปภาพทั้งหมด
let canvas, ctx;
let isDrawing = false;
let currentColor = 'black'; // กำหนดสีเริ่มต้นเป็นสีดำ
let backgroundImage = new Image();

// เพิ่มตัวแปรสำหรับเก็บสถานะสิทธิ์
const canEditPart1 = <?php echo json_encode($canEditPart1); ?>;
const canEditPart2 = <?php echo json_encode($canEditPart2); ?>;



function msg_ok(message){
        Swal.fire({
        icon: 'success',
        title: 'แจ้งเตือน!!',
        text: message,
        customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light'
        },
        buttonsStyling: false
    })
}

function msg_error(messageทช){
      Swal.fire({
         icon: 'error',
         title: 'แจ้งเตือน!!',
         text: message,
         customClass: {
              confirmButton: 'btn btn-danger waves-effect waves-light'
            },
         buttonsStyling: false

      })
}


$(document).ready(function() {
    // เพิ่มการกำหนดค่า Flatpickr สำหรับเลือกวันที่
    flatpickr("#follow_up_date", {
        dateFormat: "Y-m-d",
        minDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            fetchAvailableSlots(dateStr);
        }
    });

    $('#showPartTwo').on('click', function() {
        const opdId = $('#opd_id').val();
        if (opdId) {
            loadOPDData(opdId, 2);  // เพิ่มพารามิเตอร์ 2 เพื่อระบุว่าเป็นส่วนที่ 2
        } else {
            console.error('ไม่พบ OPD ID');
        }btnparttwo
        $('#opdFormPart1').hide();
        $('#opdFormPart2').show();
    });

    $('#backToPartOne').on('click', function() {
        $('#opdFormPart2').hide();
        $('#opdFormPart1').show();
    });

    // คำนวณ BMI อัตโนมัติ
    function calculateBMI() {
        var weight = parseFloat($('#weight').val());
        var height = parseFloat($('#height').val()) / 100; // แปลงเซนติเมตรเป็นเมตร
        if (weight && height) {
            var bmi = weight / (height * height);
            $('#bmi').val(bmi.toFixed(2));
        }
    }

    $('#weight, #height').on('input', calculateBMI);

    // จัดการการส่งฟอร์มส่วนแรก
    if (canEditPart1) {
        $('#opdFormPart1').on('submit', function(e) {
            e.preventDefault();
            // console.log('Submitting Part 1 form');
            $.ajax({
                url: 'sql/save-opd-part1.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    // console.log('Part 1 submission response:', response);
                    if (response.success) {
                        // alert('บันทึกข้อมูลเบื้องต้นสำเร็จ');
                        msg_ok('บันทึกข้อมูลเบื้องต้นสำเร็จ');
                        $('#opd_id').val(response.opd_id);
                        $('input[name="opd_id"]').val(response.opd_id); // อัพเดตค่า opd_id ในทุกฟอร์ม
                        $('#opdFormPart1').hide();
                        $('#opdFormPart2').show();
                        loadSavedDrawings(response.opd_id);
                        loadOPDData(response.opd_id, 2);

                        $('#followUpSection').show(); // แสดงส่วนของการนัดหมายติดตามผล
                        $('#DrawingModalbtn').show(); 
                        $('#btnparttwo').show();
                        // $('#DrawingModalalert').hide();
                        document.getElementById("DrawingModalalert").style.display = "none";
                        loadFollowUpHistory(); // โหลดประวัติการนัดติดตามผล
                    } else {
                        // alert('เกิดข้อผิดพลาด: ' + response.message);
                        msg_error('เกิดข้อผิดพลาด'+response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // console.error('AJAX error:', textStatus, errorThrown);
                    // alert('เกิดข้อผิดพลาดในการส่งข้อมูล');
                    msg_error('เกิดข้อผิดพลาดในการส่งข้อมูล')   
                }
            });
        });
    } else {
        $('#opdFormPart1 input, #opdFormPart1 select, #opdFormPart1 textarea').prop('disabled', true);
    }

    // จัดการการส่งฟอร์มส่วนที่สอง
    if (canEditPart2) {
        $('#opdFormPart2Form').on('submit.opdForm', function(e) {
            // console.log('Form submitted');
            e.preventDefault();
            var opdId = $('#opd_id').val(); // ดึงค่า opd_id ที่อัพเดตล่าสุด
            var formData = $(this).serialize() + '&opd_id=' + opdId; // เพิ่ม opd_id ลงในข้อมูลที่จะส่ง
            $.ajax({
                url: 'sql/save-opd-part2.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // alert('บันทึกข้อมูลทั้งหมดสำเร็จ');
                        // window.location.href = 'service.php?queue_id=' + response.queue_id;
                        // window.location.href = 'queue-management.php';
                        // msg_ok('บันทึกข้อมูลทั้งหมดสำเร็จ','queue-management.php');
                        Swal.fire({
                            icon: 'success',
                            title: 'แจ้งเตือน!!',
                            text: 'บันทึกข้อมูลทั้งหมดสำเร็จ',
                            customClass: {
                                confirmButton: 'btn btn-primary waves-effect waves-light'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'queue-management.php'; 
                            }
                        });
                    } else {
                        // alert('เกิดข้อผิดพลาด: ' + response.message);
                        msg_error('เกิดข้อผิดพลาด: ' + response.message);
                    }
                },
                error: function() {
                    msg_error('เกิดข้อผิดพลาดในการส่งข้อมูล');
                }
            });
        });
    } else {
        $('#opdFormPart2Form input, #opdFormPart2Form select, #opdFormPart2Form textarea').prop('disabled', true);
    }

    // เพิ่ม event listener สำหรับปุ่มสี
    const colorButtons = document.querySelectorAll('.color-btn');
    colorButtons.forEach(button => {
        button.addEventListener('click', function() {
            const color = this.classList[1].replace('-btn', '');
            changeColor(color);
        });
    });

    // โหลดข้อมูล OPD ที่บันทึกไว้
function loadOPDData(opdId, part = 1) {
    console.log('Loading OPD data for ID:', opdId, 'Part:', part);
    // เลือกทุก div ที่มี class "alert alert-warning"
    const alerts = document.querySelectorAll('.alert.alert-warning');
    $.ajax({
        url: 'sql/get-opd-data.php',
        type: 'GET',
        data: { opd_id: opdId },
        dataType: 'json',
        success: function(response) {
            // console.log('OPD data received:', response);
            if (response.success) {
                if (part === 1 || part === undefined) {
                    $('#weight').val(response.data.Weight);
                    $('#height').val(response.data.Height);
                    $('#bmi').val(response.data.BMI);
                    $('#fbs').val(response.data.FBS);
                    $('#systolic').val(response.data.Systolic);
                    $('#pulsation').val(response.data.Pulsation);
                    
                    // เพิ่มการกำหนดค่าให้กับ select fields
                    setSelectedOption('smoking', response.data.opd_smoke);
                    setSelectedOption('alcohol', response.data.opd_alcohol);
                    
                    // กำหนดค่าให้กับ textarea fields
                    $('#drug_allergy').val(response.data.drug_allergy);
                    $('#food_allergy').val(response.data.food_allergy);
                    $('#btnparttwo').show();
                }
                if (part === 2) {
                    $('#opd_diagnose').val(response.data.opd_diagnose);
                    $('#opd_note').val(response.data.opd_note);
                    loadSavedDrawings(opdId);

                    // แสดงส่วนของการนัดหมายติดตามผลเมื่อมีข้อมูล OPD
                    $('#followUpSection').show();
                    $('#DrawingModalbtn').show();
                    $('#btnparttwo').show();
                    // $('#DrawingModalalert').hide();
                    // document.getElementById("DrawingModalalert").style.display = "none";
                    // วนลูปผ่านทุกองค์ประกอบและซ่อน
                    alerts.forEach(function(alert) {
                        alert.style.display = "none";
                    });
                    loadFollowUpHistory()

                }
                console.log('Form updated with OPD data');
            } else {
                console.error('Failed to load OPD data:', response.message);
                $('#followUpSection').hide();
                $('#DrawingModalbtn').hide();
                $('#btnparttwo').hide();
                // $('#DrawingModalalert').show();
                // document.getElementById("DrawingModalalert").style.display = "block";
                // วนลูปผ่านทุกองค์ประกอบและซ่อน
                alerts.forEach(function(alert) {
                    alert.style.display = "block";
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX error:', textStatus, errorThrown);
            $('#followUpSection').hide();
            $('#DrawingModalbtn').hide();
            $('#btnparttwo').hide();
            // $('#DrawingModalalert').show();
                alerts.forEach(function(alert) {
                    alert.style.display = "block";
                });
            // document.getElementById("DrawingModalalert").style.display = "block";
        }
    });
}

    function setSelectedOption(selectId, value) {
        if (value) {
            $(`#${selectId} option`).removeAttr('selected');
            $(`#${selectId} option[value="${value}"]`).prop('selected', true);
        }
    }

    // เรียกใช้ฟังก์ชันโหลดข้อมูลเมื่อหน้าเว็บโหลดเสร็จ
    var opdId = $('#opd_id').val();
    console.log('Initial OPD ID:', opdId);
    if (opdId) {
        loadOPDData(opdId);
    } else {
        console.log('No OPD ID found, skipping data load');
    }
});

function loadSavedDrawings(opdId) {
    return fetch('sql/get-saved-drawings.php?opd_id=' + opdId)
        .then(response => response.json())
        .then(data => {
            const savedDrawingsContainer = document.getElementById('savedDrawings');
            const savedDrawingsInput = document.getElementById('saved_drawings');
            savedDrawingsContainer.innerHTML = '';
            
            if (data.length > 0) {
                data.forEach((drawing, index) => {
                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'drawing-item';

                    const img = document.createElement('img');
                    img.src = '../img/drawing/' + drawing.image_path;
                    img.alt = 'Patient Drawing';
                    img.onclick = () => viewImage(index, data);

                    // เพิ่มเงื่อนไขการแสดงปุ่มลบตามสิทธิ์
                    if (canEditPart2) {
                        const deleteBtn = document.createElement('button');
                        deleteBtn.innerHTML = '&times;';
                        deleteBtn.className = 'delete-btn';
                        deleteBtn.onclick = (e) => {
                            e.stopPropagation();
                            deleteImage(drawing.id, imgContainer);
                        };
                        imgContainer.appendChild(deleteBtn);
                    }

                    const dateTime = document.createElement('div');
                    dateTime.className = 'drawing-datetime';
                    dateTime.textContent = drawing.created_at;

                    imgContainer.appendChild(img);
                    imgContainer.appendChild(dateTime);
                    savedDrawingsContainer.appendChild(imgContainer);
                });
                savedDrawingsInput.value = JSON.stringify(data.map(d => d.image_path));
            } else {
                savedDrawingsContainer.innerHTML = '<p>ไม่มีภาพวาด</p>';
            }
            return data;
        })
        .catch(error => {
            console.error('Error loading saved drawings:', error);
            return [];
        });
}

function viewImage(index, images) {
    const modal = document.createElement('div');
    modal.className = 'image-modal';

    const modalContent = document.createElement('div');
    modalContent.className = 'modal-content';

    const img = document.createElement('img');
    img.src = '../img/drawing/' + images[index].image_path;
    img.alt = 'Full size patient drawing';

    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '&times;';
    closeBtn.className = 'close-modal';
    closeBtn.onclick = (e) => {
        e.stopPropagation();
        closeModal();
    };

    const dateTime = document.createElement('div');
    dateTime.className = 'modal-datetime';
    dateTime.textContent = 'Created: ' + images[index].created_at;

    const prevBtn = document.createElement('button');
    prevBtn.innerHTML = '&#10094;';
    prevBtn.className = 'nav-btn prev-btn';
    prevBtn.onclick = (e) => {
        e.stopPropagation();
        changeImage(-1);
    };

    const nextBtn = document.createElement('button');
    nextBtn.innerHTML = '&#10095;';
    nextBtn.className = 'nav-btn next-btn';
    nextBtn.onclick = (e) => {
        e.stopPropagation();
        changeImage(1);
    };

    modalContent.appendChild(img);
    modalContent.appendChild(closeBtn);
    modalContent.appendChild(dateTime);
    modalContent.appendChild(prevBtn);
    modalContent.appendChild(nextBtn);
    modal.appendChild(modalContent);
    document.body.appendChild(modal);

    setTimeout(() => modal.classList.add('active'), 10);

    let currentIndex = index;

    function closeModal() {
        modal.classList.remove('active');
        setTimeout(() => document.body.removeChild(modal), 300);
    }

    function changeImage(direction) {
        currentIndex = (currentIndex + direction + images.length) % images.length;
        img.src = '../img/drawing/' + images[currentIndex].image_path;
        dateTime.textContent = 'Created: ' + images[currentIndex].created_at;
    }

    modal.onclick = (e) => {
        if (e.target === modal) {
            closeModal();
        }
    };
}

function deleteImage(imageId, imgContainer) {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "คุณต้องการลบรูปภาพนี้ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก',
        customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light me-1',
            cancelButton: 'btn btn-danger waves-effect waves-light'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('sql/delete-drawing.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + imageId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    imgContainer.remove();
                    // Update hidden input
                    const savedDrawingsInput = document.getElementById('saved_drawings');
                    let savedDrawings = JSON.parse(savedDrawingsInput.value);
                    savedDrawings = savedDrawings.filter(path => !path.includes(data.deleted_file));
                    savedDrawingsInput.value = JSON.stringify(savedDrawings);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'ลบสำเร็จ!',
                        text: 'รูปภาพถูกลบเรียบร้อยแล้ว',
                        customClass: {
                            confirmButton: 'btn btn-success waves-effect waves-light'
                        },
                        buttonsStyling: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด!',
                        text: 'ไม่สามารถลบรูปภาพได้: ' + data.message,
                        customClass: {
                            confirmButton: 'btn btn-danger waves-effect waves-light'
                        },
                        buttonsStyling: false
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด!',
                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์',
                    customClass: {
                        confirmButton: 'btn btn-danger waves-effect waves-light'
                    },
                    buttonsStyling: false
                });
            });
        }
    });
}

function openDrawingModal() {
    if (!canEditPart2) {
        msg_error('คุณไม่มีสิทธิ์ในการวาดภาพ');
        return;
    }
    document.getElementById('drawingModal').style.display = 'block';
    canvas = document.getElementById('drawingCanvas');
    ctx = canvas.getContext('2d');
    
    // ปรับขนาด canvas ให้พอดีกับ container
    const container = document.querySelector('.canvas-container');
    canvas.width = container.clientWidth - 40; // ลบ padding
    canvas.height = container.clientHeight - 40; // ลบ padding

    // กำหนดสีเริ่มต้นเป็นสีดำ
    ctx.strokeStyle = currentColor;
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';

    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    // เพิ่ม event listener สำหรับการปรับขนาดหน้าต่าง
    window.addEventListener('resize', resizeCanvas);

    // ตั้งค่าสีเริ่มต้นเป็นสีดำ
    changeColor('black');
}

function resizeCanvas() {
    const container = document.querySelector('.canvas-container');
    canvas.width = container.clientWidth - 40;
    canvas.height = container.clientHeight - 40;
    // วาดภาพพื้นหลังใหม่ (ถ้ามี)
    if (backgroundImage.src) {
        ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);
    }
}

function selectBackground(imageSrc, element) {
    if (element) {
        // Remove 'selected' class from all images
        document.querySelectorAll('#backgroundSelector img').forEach(img => img.classList.remove('selected'));
        // Add 'selected' class to clicked image
        element.classList.add('selected');
    }

    backgroundImage = new Image();
    backgroundImage.onload = function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);
    };
    backgroundImage.src = imageSrc;
}

function startDrawing(e) {
    isDrawing = true;
    draw(e);
}

function draw(e) {
    if (!isDrawing || !isDrawingModalOpen()) return;
    
    const rect = canvas.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    ctx.lineTo(x, y);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(x, y);
}

function stopDrawing() {
    isDrawing = false;
    ctx.beginPath();
}

function changeColor(color) {
    currentColor = color;
    if (canvas && ctx) {
        ctx.strokeStyle = currentColor;
    }
    
    // อัปเดตสถานะปุ่มสี
    document.querySelectorAll('.color-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    const activeButton = document.querySelector(`.${color}-btn`);
    if (activeButton) {
        activeButton.classList.add('active');
    }
}

function clearCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    if (backgroundImage.src) {
        ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);
    }
    ctx.beginPath();
}

function closeDrawingModal() {
    document.getElementById('drawingModal').style.display = 'none';
}

function saveDrawing() {
    if (!canEditPart2) {
        msg_error('คุณไม่มีสิทธิ์ในการบันทึกภาพ');
        return;
    }
    const imageData = canvas.toDataURL('image/png');
    const opd_id = document.getElementById('opd_id').value;
    
    fetch('sql/save-drawing.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'image=' + encodeURIComponent(imageData) + '&opd_id=' + opd_id
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadSavedDrawings(opd_id);
            closeDrawingModal();
        } else {
            console.error('Error saving drawing:', data.message);
        }
    })
    .catch((error) => {
        console.error('Error:', error);
    });
}

function isDrawingModalOpen() {
    return document.getElementById('drawingModal').style.display === 'block';
}

// Event listener สำหรับการอัปโหลดรูปภาพ
document.getElementById('imageUpload').addEventListener('change', function(e) {
    if (!canEditPart2) {
        msg_error('คุณไม่มีสิทธิ์ในการอัปโหลดภาพ');
        return;
    }
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            selectBackground(event.target.result);
        }
        reader.readAsDataURL(file);
    }
});

// ฟังก์ชันสำหรับการตรวจสอบสิทธิ์
function checkPermission(action) {
    switch(action) {
        case 'editPart1':
            return canEditPart1;
        case 'editPart2':
            return canEditPart2;
        default:
            return false;
    }
}

// ปรับปรุงการแสดงผลและการทำงานตามสิทธิ์
function updateUIBasedOnPermissions() {
    if (!canEditPart1) {
        $('#opdFormPart1 input, #opdFormPart1 select, #opdFormPart1 textarea').prop('disabled', true);
        $('#savePartOne').hide();
    }
    if (!canEditPart2) {
        $('#opdFormPart2Form input, #opdFormPart2Form select, #opdFormPart2Form textarea').prop('disabled', true);
        $('.btn-submit').hide();
        $('.delete-btn').hide();
    }
    // แสดงปุ่ม "ถัดไป" สำหรับทุกคน
    $('#showPartTwo').show();
}

var clinicHours = <?php echo json_encode($clinic_hours); ?>;
var closedDays = <?php echo json_encode($closed_days); ?>;
var closedDates = <?php echo json_encode($closed_dates); ?>;
var bookedSlots = <?php echo json_encode($booked_slots); ?>;

console.log('Clinic Hours:', clinicHours);
console.log('Closed Days:', closedDays);
console.log('Closed Dates:', closedDates);
console.log('Booked Slots:', bookedSlots);

document.addEventListener('DOMContentLoaded', function() {

    // Initialize Flatpickr for date selection
    flatpickr.localize(flatpickr.l10ns.th);
    const clinicHours = <?php echo json_encode($clinic_hours); ?>;
    const closedDays = <?php echo json_encode($closed_days); ?>;
    const closedDates = <?php echo json_encode($closed_dates); ?>;
    const bookedSlots = <?php echo json_encode($booked_slots); ?>;


    console.log('DOM fully loaded');
    const followUpDateElem = document.getElementById('follow_up_date');

    if (followUpDateElem) {
        console.log('Initializing Flatpickr');
        flatpickr("#follow_up_date", {
            minDate: "today",
            maxDate: new Date().fp_incr(30), // 30 days from now
            disable: [
                function(date) {
                    // Disable closed dates
                    const dateString = date.toISOString().split('T')[0];
                    if (closedDates.includes(dateString)) {
                        return true;
                    }
                    
                    // Disable closed days of the week
                    const dayOfWeek = date.toLocaleString('en-us', {weekday: 'long'});
                    return closedDays.includes(dayOfWeek);
                }
            ],
            dateFormat: "d/m/Y",
            locale: {
                ...flatpickr.l10ns.th,
                reformatAfterEdit: true,
            },
            onReady: function(selectedDates, dateStr, instance) {
                instance.currentYearElement.textContent = parseInt(instance.currentYearElement.textContent) + 543;
            },
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const thaiDate = formatThaiDate(selectedDates[0]);
                    instance.input.value = thaiDate;
                    updateTimeSlots(thaiDate);
                }
            },
            onYearChange: function(selectedDates, dateStr, instance) {
                setTimeout(function() {
                    let yearElem = instance.currentYearElement;
                    yearElem.textContent = parseInt(yearElem.textContent) + 543;
                }, 0);
            },
            formatDate: function(date, format) {
                return formatThaiDate(date);
            },
            parseDate: function(datestr, format) {
                if (!datestr) return undefined;
                const parts = datestr.split('/');
                if (parts.length !== 3) return undefined;
                const thaiYear = parseInt(parts[2], 10);
                const month = parseInt(parts[1], 10) - 1;
                const day = parseInt(parts[0], 10);
                return new Date(thaiYear - 543, month, day);
            }
        });
    } else {
        console.error('follow_up_date element not found');
    }





    // Helper function to format date in Thai format
    function formatThaiDate(date) {
        const thaiYear = date.getFullYear() + 543;
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        return `${day}/${month}/${thaiYear}`;
    }
});

function updateTimeSlots(availableSlots) {
    const timeSlotsContainer = document.getElementById('timeSlots');
    timeSlotsContainer.innerHTML = '';

    availableSlots.forEach(function(slot) {
        const slotElement = document.createElement('div');
        slotElement.className = 'col-md-3 mb-2';
        
        let buttonClass = 'btn-outline-primary';
        let buttonText = slot.time;
        let isDisabled = false;

        switch(slot.status) {
            case 'fully_booked':
                buttonClass = 'btn-danger';
                buttonText += ' (เต็ม)';
                isDisabled = true;
                break;
            case 'partially_booked':
                buttonClass = 'btn-warning';
                buttonText += ` (ว่าง ${slot.available_rooms_count})`;
                break;
        }

        slotElement.innerHTML = `
            <button class="btn ${buttonClass} time-slot w-100" 
                    ${isDisabled ? 'disabled' : ''}
                    data-time="${slot.time}"
                    data-available-rooms='${JSON.stringify(slot.available_rooms)}'
                    data-interval="${slot.interval_minutes}">
                ${buttonText}
            </button>
        `;

        timeSlotsContainer.appendChild(slotElement);
    });

    // เพิ่ม event listener สำหรับปุ่มเวลา
    $('.time-slot').on('click', function() {
        $('.time-slot').removeClass('selected');
        $(this).addClass('selected');
        const selectedTime = $(this).data('time');
        const availableRooms = $(this).data('available-rooms');
        const intervalMinutes = $(this).data('interval');
        
        $('#follow_up_time').val(selectedTime);
        updateSelectedTimeInfo(selectedTime, availableRooms, intervalMinutes);
    });
}
function updateSelectedTimeInfo(time, availableRooms, intervalMinutes) {
    const selectedInfo = document.getElementById('selectedTimeInfo');
    if (availableRooms.length > 0) {
        const roomNames = availableRooms.map(room => room.room_name).join(', ');
        selectedInfo.innerHTML = `เวลาที่เลือก: ${time}<br>ห้องที่ว่าง: ${roomNames}<br>ระยะเวลา: ${intervalMinutes} นาที`;
    } else {
        selectedInfo.innerHTML = 'ไม่มีห้องว่างในเวลาที่เลือก';
    }
}
function saveFollowUp() {
    const followUpDate = $('#follow_up_date').val();
    const followUpTime = $('#follow_up_time').val();
    const followUpNote = $('#follow_up_note').val();
    const opdId = $('#opd_id').val();

    if (!followUpDate || !followUpTime) {
        Swal.fire({
            icon: 'error',
            title: 'ข้อมูลไม่ครบถ้วน',
            text: 'กรุณาเลือกวันที่และเวลานัดหมาย'
        });
        return;
    }

    $.ajax({
        url: 'sql/save-follow-up.php',
        type: 'POST',
        data: {
            opd_id: opdId,
            follow_up_date: followUpDate,
            follow_up_time: followUpTime,
            follow_up_note: followUpNote
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกการนัดติดตามผลสำเร็จ',
                    text: 'ข้อมูลการนัดติดตามผลถูกบันทึกเรียบร้อยแล้ว'
                }).then(() => {
                    // รีเซ็ตฟอร์ม
                    $('#follow_up_date').val('');
                    $('#follow_up_time').val('');
                    $('#follow_up_note').val('');
                    $('#timeSlots').empty();
                    $('#selectedTimeInfo').empty();
                    
                    // โหลดข้อมูลประวัติการนัดติดตามผลใหม่
                    loadFollowUpHistory();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถบันทึกการนัดติดตามผลได้: ' + response.message
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
            });
        }
    });
}

function loadFollowUpHistory() {
    const opdId = $('#opd_id').val();
    if (!opdId) {
        console.log('No OPD ID available, skipping follow-up history load');
        return;
    }
    $.ajax({
        url: 'sql/get-follow-up-history.php',
        type: 'GET',
        data: { opd_id: opdId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateFollowUpHistoryTable(response.data);
            } else {
                console.error('Failed to load follow-up history:', response.message);
                $('#followUpHistory').html('<p>เกิดข้อผิดพลาดในการโหลดประวัติการนัดติดตามผล</p>');
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX error:', textStatus, errorThrown);
            $('#followUpHistory').html('<p>ไม่สามารถโหลดประวัติการนัดติดตามผลได้</p>');
        }
    });
}

function updateFollowUpHistoryTable(data) {
    // console.log('Updating follow-up history table with data:', data);
    let historyHtml = '<table class="table">';
    historyHtml += '<thead><tr><th>วันที่และเวลานัด</th><th>หมายเหตุ</th><th>สถานะ</th><th>การดำเนินการ</th></tr></thead>';
    historyHtml += '<tbody>';
    
    const currentDate = new Date();

    if (data.length > 0) {
        data.forEach(function(item) {
            const appointmentDate = new Date(item.booking_datetime_raw);
            const canCancel = appointmentDate > currentDate && item.status !== 'cancelled';

            historyHtml += `<tr>
                <td>${item.booking_datetime}</td>
                <td>${item.note}</td>
                <td>${getStatusBadge(item.status)}</td>
                <td>`;
            
            if (canCancel) {
                historyHtml += `<button class="btn btn-danger btn-sm" onclick="cancelFollowUp(${item.id})">ยกเลิก</button>`;
            } else {
                historyHtml += `<span class="text-muted">ไม่สามารถยกเลิกได้</span>`;
            }

            historyHtml += `</td></tr>`;
        });
    } else {
        historyHtml += '<tr><td colspan="4">ไม่พบประวัติการนัดติดตามผล</td></tr>';
    }
    
    historyHtml += '</tbody></table>';
    $('#followUpHistory').html(historyHtml);
}

function getStatusBadge(status) {
    switch(status) {
        case 'confirmed':
            return '<span class="badge bg-success">ยืนยันแล้ว</span>';
        case 'cancelled':
            return '<span class="badge bg-danger">ยกเลิกแล้ว</span>';
        case 'completed':
            return '<span class="badge bg-info">เสร็จสิ้น</span>';
        default:
            return '<span class="badge bg-secondary">ไม่ทราบสถานะ</span>';
    }
}
function cancelFollowUp(followUpId) {
    Swal.fire({
        title: 'ยืนยันการยกเลิก',
        text: "คุณแน่ใจหรือไม่ที่จะยกเลิกการนัดติดตามผลนี้?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ยกเลิก!',
        cancelButtonText: 'ไม่, ยกเลิกการดำเนินการ'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'sql/cancel-follow-up.php',
                type: 'POST',
                data: { follow_up_id: followUpId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'ยกเลิกแล้ว!',
                            'การนัดติดตามผลถูกยกเลิกเรียบร้อยแล้ว',
                            'success'
                        ).then(() => {
                            loadFollowUpHistory();
                        });
                    } else {
                        Swal.fire(
                            'เกิดข้อผิดพลาด!',
                            response.message,
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'เกิดข้อผิดพลาด!',
                        'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                        'error'
                    );
                }
            });
        }
    });
}


// เรียกใช้ฟังก์ชันเมื่อโหลดหน้าเว็บ
$(document).ready(function() {
    updateUIBasedOnPermissions();
    // โหลดประวัติการนัดติดตามผลเมื่อโหลดหน้า
    loadFollowUpHistory();

});

$(document).ready(function() {
    // โหลดรูปภาพ Before/After เมื่อโหลดหน้า
    loadBeforeAfterImages();

    // จัดการการส่งฟอร์มรูปภาพ Before/After
    $('#beforeAfterForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('opd_id', $('#opd_id').val());

        $.ajax({
            url: 'sql/save-before-after-image.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire('สำเร็จ', 'บันทึกรูปภาพเรียบร้อยแล้ว', 'success');
                    $('#beforeAfterForm')[0].reset();
                    loadBeforeAfterImages();
                } else {
                    Swal.fire('ผิดพลาด', 'ไม่สามารถบันทึกรูปภาพได้', 'error');
                }
            },
            error: function() {
                Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
            }
        });
    });

    $('#prevImage').click(function() {
        if (currentImageIndex > 0) {
            currentImageIndex--;
            updateModalContent();
        }
    });

    $('#nextImage').click(function() {
        if (currentImageIndex < currentImages.length - 1) {
            currentImageIndex++;
            updateModalContent();
        }
    });
});


// เพิ่มหรือปรับปรุงส่วนนี้
let currentImageIndex = 0;
function loadBeforeAfterImages() {
    var opdId = $('#opd_id').val();
    $.ajax({
        url: 'sql/get-before-after-images.php',
        type: 'GET',
        data: { opd_id: opdId },
        success: function(response) {
            if (response.success) {
                currentImages = response.images;
                var gallery = $('#beforeAfterGallery');
                gallery.empty();
                response.images.forEach(function(image, index) {
                    gallery.append(`
                        <div class="gallery-item">
                            <img src="../img/before-after/${image.image_path}" alt="${image.image_type}" 
                                 onclick="showImageModal(${index})">
                            <span class="gallery-item-type">${image.image_type}</span>
                            <button class="delete-btn" onclick="deleteImage(${image.id}, this)">&times;</button>
                        </div>
                    `);
                });
            } else {
                $('#beforeAfterGallery').html('<p>ไม่พบรูปภาพ</p>');
            }
        },
        error: function() {
            $('#beforeAfterGallery').html('<p>เกิดข้อผิดพลาดในการโหลดรูปภาพ</p>');
        }
    });
}

function deleteImage(imageId, button) {
    event.stopPropagation(); // ป้องกันการเปิด modal
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "คุณต้องการลบรูปภาพนี้ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'sql/delete-before-after-image.php',
                type: 'POST',
                data: { id: imageId },
                success: function(response) {
                    if (response.success) {
                        $(button).closest('.gallery-item').remove();
                        Swal.fire('ลบแล้ว!', 'รูปภาพถูกลบเรียบร้อยแล้ว', 'success');
                    } else {
                        Swal.fire('ผิดพลาด!', 'ไม่สามารถลบรูปภาพได้', 'error');
                    }
                },
                error: function() {
                    Swal.fire('ผิดพลาด!', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                }
            });
        }
    });
}

function showImageModal(index) {
    currentImageIndex = index;
    updateModalContent();
    $('#imageModal').modal('show');
}

function updateModalContent() {
    const image = currentImages[currentImageIndex];
    $('#modalImage').attr('src', `../img/before-after/${image.image_path}`);
    $('#modalImageType').text(image.image_type.charAt(0).toUpperCase() + image.image_type.slice(1));
    $('#modalImageDescription').text(image.description);
    $('#modalImageDateTime').text('บันทึกเมื่อ: ' + image.created_at);
}

document.getElementById('printButton').addEventListener('click', function() {
  var opdId = $('#opd_id').val();
  var cusId = $('input[name="cus_id"]').val();

  if (!cusId) {
    alert('ไม่พบข้อมูล Customer ID');
    return;
  }

  console.log('Sending request with cus_id:', cusId, 'and opd_id:', opdId);

  $.ajax({
    url: 'sql/get-print-data.php',
    type: 'GET',
    data: { opd_id: opdId, cus_id: cusId },
    dataType: 'json',
    success: function(data) {
      if(data.error) {
        console.error('Error:', data.error);
        alert('เกิดข้อผิดพลาดในการดึงข้อมูล: ' + data.error);
        return;
      }

      var printContent = `
        <style>
            @page {
                size: A5 landscape;
                margin: 0;
            }
            body {
                font-family: 'Sarabun', sans-serif;
                line-height: 1.3;
                margin: 0;
                padding: 5mm;
                width: 200mm;
                height: 138mm;
                font-size: 9pt;
            }
            .container {
                border: 1px solid #ccc;
                padding: 5mm;
                height: 128mm;
            }
            h1 {
                text-align: center;
                margin: 0 0 3mm 0;
                font-size: 14pt;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 3mm;
                font-size: 10pt;
            }
            th, td {
                border: 1px solid #ccc;
                padding: 1.5mm;
                text-align: left;
                vertical-align: top;
            }
            .info-grid {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                gap: 2mm;
            }
            .info-item {
                margin-bottom: 1mm;
            }
            .info-item:nth-child(3n) {
                text-align: right;
            }
            .measurement-table th {
                background-color: #e6e6e6;
            }
            .diagnosis-table {
                margin-top: 3mm;
            }
            .checkbox-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 2mm;
                margin-top: 2mm;
            }
            .checkbox-item {
                display: flex;
                align-items: center;
            }
            .checkbox {
                width: 4mm;
                height: 4mm;
                border: 1px solid #000;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-right: 1mm;
                font-size: 8pt;
            }
        </style>
        <div class="container">
            <h1>ใบตรวจรักษาผู้ป่วย (OPD)</h1>
            <div class="info-grid">
                <div class="info-item"><strong>รหัสผู้ป่วย:</strong> ${data.customer.hn || ''}</div>
                <div class="info-item"><strong>เพศ:</strong> ${data.customer.cus_gender || ''} <strong>อายุ:</strong> ${data.age || ''}</div>
                <div class="info-item"><strong>วันที่:</strong> ${data.currentDate || ''}</div>
                <div class="info-item"><strong>ชื่อ:</strong> ${data.customer.cus_firstname || ''} ${data.customer.cus_lastname || ''}</div>
                <div class="info-item"><strong>กรุ๊ปเลือด:</strong> ${data.customer.cus_blood || ''}</div>
                <div class="info-item"><strong>สถานพยาบาล:</strong> DEMO CLINIC</div>
                <div class="info-item"><strong>เลขที่บัตรประชาชน:</strong> ${data.customer.cus_id_card_number || ''}</div>
                <div class="info-item"><strong>ที่อยู่:</strong> ${data.customer.cus_address || ''}</div>
                <div class="info-item"><strong>ลักษณะการให้บริการ:</strong> คลินิก ศัลยกรรม</div>
                <div class="info-item"></div>
                <div class="info-item">${data.customer.cus_district || ''} ${data.customer.cus_city || ''} ${data.customer.cus_province || ''}</div>
                <div class="info-item">เสริมความงาม</div>
                <div class="info-item"><strong>เบอร์โทร:</strong> ${data.customer.cus_tel || ''}</div>
                <div class="info-item">${data.customer.cus_postal_code || ''}</div>
                <div class="info-item"><strong>เลขที่ใบอนุญาต:</strong> 42211789168</div>
                <div class="info-item"><strong>โรคประจำตัว:</strong> ${data.opd && data.opd.food_allergy ? data.opd.food_allergy : ''}</div>
                <div class="info-item"><strong>แพ้ยา:</strong> ${data.opd && data.opd.drug_allergy ? data.opd.drug_allergy : ''}</div>
                <div class="info-item"><strong>ที่อยู่:</strong> 100/1 ซ วิภาวดี 1 รัชดา จังหวัดกรุงเทพ</div>
                <div class="info-item"><strong>(พิมพ์เมื่อ: ${data.printDateTime || ''})</strong></div>
                <div class="info-item"></div>
                <div class="info-item">รหัสไปรษณีย์ 10100</div>
            </div>
            <div class="checkbox-grid">
                <div class="checkbox-item">
                    <span class="checkbox">${data.opd && data.opd.opd_smoke === 'สูบ' ? '✓' : ''}</span>
                    <span>สูบบุหรี่</span>
                </div>
                <div class="checkbox-item">
                    <span class="checkbox">${data.opd && data.opd.opd_smoke === 'ไม่สูบ' ? '✓' : ''}</span>
                    <span>ไม่สูบบุหรี่</span>
                </div>
                <div class="checkbox-item">
                    <span class="checkbox">${data.opd && data.opd.opd_alcohol === 'ดื่ม' ? '✓' : ''}</span>
                    <span>ดื่มสุรา</span>
                </div>
                <div class="checkbox-item">
                    <span class="checkbox">${data.opd && data.opd.opd_alcohol === 'ไม่ดื่ม' ? '✓' : ''}</span>
                    <span>ไม่ดื่มสุรา</span>
                </div>
            </div>
            <table class="measurement-table">
                <tr>
                    <th>Weight/น้ำหนัก</th>
                    <th>Height/ส่วนสูง</th>
                    <th>BMI/ค่าดัชนีมวลกาย</th>
                    <th>FBS (mg/dL)</th>
                    <th>ความดันโลหิต (mmHg)</th>
                    <th>ชีพจร (ครั้ง/นาที)</th>
                </tr>
                <tr style="height: 45px">
                    <td>${data.opd ? (data.opd.Weight || '') : ''} ${data.opd && data.opd.Weight ? 'กิโลกรัม' : ''}</td>
                    <td>${data.opd ? (data.opd.Height || '') : ''} ${data.opd && data.opd.Height ? 'เซนติเมตร' : ''}</td>
                    <td>${data.opd ? (data.opd.BMI || '') : ''}</td>
                    <td>${data.opd ? (data.opd.FBS || '') : ''}</td>
                    <td>${data.opd ? (data.opd.Systolic || '') : ''}</td>
                    <td>${data.opd ? (data.opd.Pulsation || '') : ''}</td>
                </tr>
            </table>
            <table class="measurement-table">
                <tr>
                    <th>วินิจฉัย</th>
                    <th>หมายเหตุ</th>
                </tr>
                <tr style="height: 110px">
                    <td>${data.opd ? (data.opd.opd_diagnose || '') : ''}</td>
                    <td>${data.opd ? (data.opd.opd_note || '') : ''}</td>
                </tr>
            </table>
        </div>
      `;

      var printWindow = window.open('', '', 'height=600,width=800');
      printWindow.document.write('<html><head><title>Print OPD</title>');
      printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">');
      printWindow.document.write('</head><body>');
      printWindow.document.write(printContent);
      printWindow.document.write('</body></html>');
      printWindow.document.close();
      printWindow.print();
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.error('AJAX error:', textStatus, errorThrown);
      console.log('Response Text:', jqXHR.responseText);
      alert('เกิดข้อผิดพลาดในการดึงข้อมูลสำหรับการพิมพ์: ' + textStatus);
    }
  });
});

function fetchAvailableSlots(selectedDate) {
    $.ajax({
        url: 'sql/get-follow-up-slots.php',
        method: 'POST',
        data: { selected_date: selectedDate },
        success: function(response) {
            try {
                const availableSlots = JSON.parse(response);
                updateTimeSlots(availableSlots);
            } catch (error) {
                console.error('Error parsing JSON:', error);
                alert('เกิดข้อผิดพลาดในการประมวลผลข้อมูล');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', status, error);
            alert('ไม่สามารถดึงข้อมูลการจองได้');
        }
    });
}
</script>
</body>
</html>