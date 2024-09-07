<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

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

$background_images = glob("../img/drawing/default/*");
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
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .form-section h3 {
            color: #333;
            border-bottom: 2px solid #764ba2;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 22px;
        }
        .form-label {
            font-size: 18px;
            font-weight: 500;
            color: #333;
        }
        .form-control, .form-select {
            font-size: 18px;
            padding: 12px;
            border: 2px solid #ced4da;
            border-radius: 8px;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .form-control:focus, .form-select:focus {
            border-color: #764ba2;
            box-shadow: 0 0 0 0.2rem rgba(118, 75, 162, 0.25);
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
        .drawing-tools h4 {
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
        .upload-btn {
            background-color: #3498db;
            color: white;
        }
            .position-relative {
        position: relative;
        }
        .position-absolute {
            position: absolute;
        }
        .top-0 {
            top: 0;
        }
        .end-0 {
            right: 0;
        }
        .drawing-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: flex-start;
        }

        .drawing-item {
            position: relative;
            width: 150px;
            height: 180px; /* เพิ่มความสูงเพื่อรองรับข้อความวันที่ */
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .drawing-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .drawing-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .drawing-item:hover img {
            transform: scale(1.1);
        }
        .drawing-datetime {
            padding: 5px;
            text-align: center;
            font-size: 12px;
            color: #666;
            background-color: #f8f8f8;
        }

        .modal-datetime {
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
            color: #666;
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
            line-height: 1;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .drawing-item:hover .delete-btn {
            opacity: 1;
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
            background-color: rgba(0, 0, 0, 0.7);
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
            max-height: calc(90vh - 40px); /* 40px for padding */
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

        .modal-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            padding: 20px;
            box-sizing: border-box;
        }

        .modal-content img {
            display: block;
            max-width: 100%;
            max-height: calc(90vh - 100px); /* 100px for padding and datetime */
            margin: 0 auto;
            object-fit: contain;
        }
        .color-btn.active {
            border: 2px solid #fff;
            box-shadow: 0 0 5px rgba(0,0,0,0.5);
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
                            </div>
                        </div>

                        <!-- ส่วนแรกของฟอร์ม -->
                        <form id="opdFormPart1" method="post">
                            <input type="hidden" name="queue_id" value="<?php echo $queue_id; ?>">
                            <input type="hidden" name="cus_id" value="<?php echo $queue_data['cus_id']; ?>">
                            <input type="hidden" name="course_id" value="<?php echo $queue_data['course_id']; ?>">
                            
                            <div class="form-section">
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

                            <div class="form-section">
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

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary" id="savePartOne">บันทึกข้อมูลเบื้องต้น</button>
                            </div>
                        </form>

                        <!-- ส่วนที่สองของฟอร์ม (ซ่อนไว้ก่อน) -->
                        <div id="opdFormPart2" style="display: none;">
                            <form id="opdFormPart2Form" method="post">
                                <input type="hidden" name="opd_id" id="opd_id" value="">
                                
                                <div class="form-section">
                                    <h3>การตรวจร่างกาย</h3>
                                    <div class="mb-3">
                                        <label for="opd_physical" class="form-label">การตรวจร่างกาย</label>
                                        <button type="button" class="btn btn-primary" onclick="openDrawingModal()">วาดภาพการตรวจร่างกาย</button>
                                        <div id="savedDrawings" class="mt-3">
                                            <!-- รูปภาพที่บันทึกแล้วจะแสดงที่นี่ -->
                                        </div>
                                    </div>
                                    <input type="hidden" id="saved_drawings" name="saved_drawings" value="">
                                </div>

                                <div class="form-section">
                                    <h3>การวินิจฉัยและหมายเหตุ</h3>
                                    <div class="mb-3">
                                        <label for="opd_diagnose" class="form-label">วินิจฉัย</label>
                                        <textarea class="form-control" id="opd_diagnose" name="opd_diagnose" rows="3" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="opd_note" class="form-label">หมายเหตุ</label>
                                        <textarea class="form-control" id="opd_note" name="opd_note" rows="3"></textarea>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-submit">บันทึกข้อมูลทั้งหมด</button>
                                </div>
                            </form>
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

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
<script>
let currentImages = []; // ตัวแปร global สำหรับเก็บข้อมูลรูปภาพทั้งหมด
let canvas, ctx;
let isDrawing = false;
let currentColor = 'black'; // กำหนดสีเริ่มต้นเป็นสีดำ
let backgroundImage = new Image();

$(document).ready(function() {
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
    $('#opdFormPart1').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'sql/save-opd-part1.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('บันทึกข้อมูลเบื้องต้นสำเร็จ');
                    $('#opd_id').val(response.opd_id);
                    $('#opdFormPart1').hide();
                    $('#opdFormPart2').show();
                    loadSavedDrawings(response.opd_id);
                } else {
                    alert('เกิดข้อผิดพลาด: ' + response.message);
                }
            },
            error: function() {
                alert('เกิดข้อผิดพลาดในการส่งข้อมูล');
            }
        });
    });

    // จัดการการส่งฟอร์มส่วนที่สอง
    $('#opdFormPart2Form').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'sql/save-opd-part2.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('บันทึกข้อมูลทั้งหมดสำเร็จ');
                    window.location.href = 'queue-management.php';
                } else {
                    alert('เกิดข้อผิดพลาด: ' + response.message);
                }
            },
            error: function() {
                alert('เกิดข้อผิดพลาดในการส่งข้อมูล');
            }
        });
    });

    // เพิ่ม event listener สำหรับปุ่มสี
    const colorButtons = document.querySelectorAll('.color-btn');
    colorButtons.forEach(button => {
        button.addEventListener('click', function() {
            const color = this.classList[1].replace('-btn', '');
            changeColor(color);
        });
    });
});

function loadSavedDrawings(opdId) {
    fetch('sql/get-saved-drawings.php?opd_id=' + opdId)
    .then(response => response.json())
    .then(data => {
        const savedDrawingsContainer = document.getElementById('savedDrawings');
        const savedDrawingsInput = document.getElementById('saved_drawings');
        const savedDrawings = [];

        savedDrawingsContainer.innerHTML = '';
        savedDrawingsContainer.className = 'drawing-gallery';

        currentImages = data; // เก็บข้อมูลรูปภาพทั้งหมด

        data.forEach((drawing, index) => {
            const imgContainer = document.createElement('div');
            imgContainer.className = 'drawing-item';

            const img = document.createElement('img');
            img.src = '../img/drawing/' + drawing.image_path;
            img.alt = 'Patient Drawing';
            img.onclick = () => viewImage(index);

            const deleteBtn = document.createElement('button');
            deleteBtn.innerHTML = '&times;';
            deleteBtn.className = 'delete-btn';
            deleteBtn.onclick = (e) => {
                e.stopPropagation();
                deleteImage(drawing.id, imgContainer);
            };

            const dateTime = document.createElement('div');
            dateTime.className = 'drawing-datetime';
            dateTime.textContent = drawing.created_at;

            imgContainer.appendChild(img);
            imgContainer.appendChild(deleteBtn);
            imgContainer.appendChild(dateTime);
            savedDrawingsContainer.appendChild(imgContainer);

            savedDrawings.push(drawing.image_path);
        });

        savedDrawingsInput.value = JSON.stringify(savedDrawings);
    })
    .catch(error => console.error('Error loading saved drawings:', error));
}

function viewImage(index) {
    const modal = document.createElement('div');
    modal.className = 'image-modal';

    const modalContent = document.createElement('div');
    modalContent.className = 'modal-content';

    const img = document.createElement('img');
    img.src = '../img/drawing/' + currentImages[index].image_path;
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
    dateTime.textContent = 'Created: ' + currentImages[index].created_at;

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
        currentIndex = (currentIndex + direction + currentImages.length) % currentImages.length;
        img.src = '../img/drawing/' + currentImages[currentIndex].image_path;
        dateTime.textContent = 'Created: ' + currentImages[currentIndex].created_at;
    }

    modal.onclick = (e) => {
        if (e.target === modal) {
            closeModal();
        }
    };
}

function deleteImage(imageId, imgContainer) {
    if (confirm('คุณแน่ใจหรือไม่ที่จะลบรูปภาพนี้?')) {
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
            } else {
                alert('เกิดข้อผิดพลาดในการลบรูปภาพ: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

function openDrawingModal() {
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
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            selectBackground(event.target.result);
        }
        reader.readAsDataURL(file);
    }
});

</script>
</body>
</html>