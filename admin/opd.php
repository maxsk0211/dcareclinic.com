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
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
        }
        #drawingCanvas {
            display: block;
            margin: auto;
            border: 1px solid #ddd;
            background-color: white;
        }
        .drawing-tools {
            text-align: center;
            margin-top: 10px;
        }
        .drawing-tools button {
            margin: 0 5px;
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

                        <form id="opdForm" method="post" action="sql/save-opd.php">
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

                            <div class="form-section">
                                <h3>ข้อมูลสุขภาพเพิ่มเติม</h3>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="opd_smoke" class="form-label">สูบบุหรี่</label>
                                        <select class="form-select" id="opd_smoke" name="opd_smoke" required>
                                            <option value="">เลือก</option>
                                            <option value="ไม่สูบ" <?php echo ($opd_data['opd_smoke'] ?? '') == 'ไม่สูบ' ? 'selected' : ''; ?>>ไม่สูบ</option>
                                            <option value="สูบ" <?php echo ($opd_data['opd_smoke'] ?? '') == 'สูบ' ? 'selected' : ''; ?>>สูบ</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="opd_alcohol" class="form-label">ดื่มสุรา</label>
                                        <select class="form-select" id="opd_alcohol" name="opd_alcohol" required>
                                            <option value="">เลือก</option>
                                            <option value="ไม่ดื่ม" <?php echo ($opd_data['opd_alcohol'] ?? '') == 'ไม่ดื่ม' ? 'selected' : ''; ?>>ไม่ดื่ม</option>
                                            <option value="ดื่ม" <?php echo ($opd_data['opd_alcohol'] ?? '') == 'ดื่ม' ? 'selected' : ''; ?>>ดื่ม</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="opd_physical" class="form-label">การตรวจร่างกาย</label>
                                    <button type="button" class="btn btn-primary" onclick="openDrawingModal()">วาดภาพการตรวจร่างกาย</button>
                                    <input type="hidden" id="opd_physical" name="opd_physical" value="<?php echo $opd_data['opd_physical'] ?? ''; ?>">
                                </div>
                            </div>

                            <div class="form-section">
                                <h3>การวินิจฉัยและหมายเหตุ</h3>
                                <div class="mb-3">
                                    <label for="opd_diagnose" class="form-label">วินิจฉัย</label>
                                    <textarea class="form-control" id="opd_diagnose" name="opd_diagnose" rows="3" required><?php echo $opd_data['opd_diagnose'] ?? ''; ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="opd_note" class="form-label">หมายเหตุ</label>
                                    <textarea class="form-control" id="opd_note" name="opd_note" rows="3"><?php echo $opd_data['opd_note'] ?? ''; ?></textarea>
                                </div>
                            </div>

                            // ในส่วนท้ายของฟอร์ม opd.php
                            <div class="text-center">
                                <button type="submit" class="btn btn-submit">บันทึกข้อมูล</button>
                                <a href="service.php?hn=<?php echo 'HN-' . str_pad($queue_data['cus_id'], 6, '0', STR_PAD_LEFT); ?>" class="btn btn-primary">ไปยังหน้าบริการ</a>
                            </div>
                        </form>
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
    <div id="drawingModal">
        <div class="drawing-tools">
            <button onclick="changeColor('black')">สีดำ</button>
            <button onclick="changeColor('red')">สีแดง</button>
            <button onclick="changeColor('blue')">สีน้ำเงิน</button>
            <button onclick="clearCanvas()">ล้าง</button>
            <button onclick="saveDrawing()">บันทึก</button>
            <button onclick="closeDrawingModal()">ปิด</button>
        </div>
        <canvas id="drawingCanvas"></canvas>
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
    });

 let canvas, ctx, isDrawing = false, currentColor = 'black';

    function openDrawingModal() {
        document.getElementById('drawingModal').style.display = 'block';
        canvas = document.getElementById('drawingCanvas');
        ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth * 0.8;
        canvas.height = window.innerHeight * 0.8;

        // โหลดภาพพื้นหลัง (ถ้ามี)
        const backgroundImage = new Image();
        backgroundImage.onload = function() {
            ctx.drawImage(backgroundImage, 0, 0, canvas.width, canvas.height);
        };
        backgroundImage.src = '../img/drawing/face-treatment.jpg'; // ตั้งค่าพาธของภาพพื้นหลังตามที่คุณต้องการ

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseout', stopDrawing);
    }

    function startDrawing(e) {
        isDrawing = true;
        draw(e);
    }

    function draw(e) {
        if (!isDrawing) return;
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = currentColor;

        ctx.lineTo(e.clientX - canvas.offsetLeft, e.clientY - canvas.offsetTop);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(e.clientX - canvas.offsetLeft, e.clientY - canvas.offsetTop);
    }

    function stopDrawing() {
        isDrawing = false;
        ctx.beginPath();
    }

    function changeColor(color) {
        currentColor = color;
    }

    function clearCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    function closeDrawingModal() {
        document.getElementById('drawingModal').style.display = 'none';
    }

    function saveDrawing() {
        const imageData = canvas.toDataURL('image/png');
        document.getElementById('opd_physical').value = imageData;

        // ส่งข้อมูลภาพไปยังเซิร์ฟเวอร์เพื่อบันทึก
        fetch('sql/save-drawing.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'image=' + encodeURIComponent(imageData)
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            closeDrawingModal();
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    }
    </script>
</body>
</html>