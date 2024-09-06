<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

$hn = isset($_GET['hn']) ? $_GET['hn'] : '';
$cus_id = substr($hn, 3); // ตัด HN- ออกเพื่อให้เหลือแค่ตัวเลข

// ดึงข้อมูลลูกค้า
$sql_customer = "SELECT * FROM customer WHERE cus_id = '$cus_id'";
$result_customer = $conn->query($sql_customer);
$customer = $result_customer->fetch_assoc();

// ดึงข้อมูลคอร์สที่เคยจอง
$sql_courses = "SELECT DISTINCT od.course_id, c.course_name, c.course_price, cb.booking_datetime
                FROM course_bookings cb
                JOIN order_course oc ON cb.id = oc.course_bookings_id
                JOIN order_detail od ON oc.oc_id = od.oc_id
                JOIN course c ON od.course_id = c.course_id
                WHERE cb.cus_id = '$cus_id'
                AND cb.booking_datetime >= CURDATE()
                AND cb.status = 'confirmed'
                ORDER BY cb.booking_datetime ASC";
$result_courses = $conn->query($sql_courses);

// สร้าง JSON ของข้อมูลแพทย์
$sql_doctors = "SELECT * FROM users WHERE position_id = 3"; // สมมติว่า position_id 3 คือแพทย์
$result_doctors = $conn->query($sql_doctors);
$doctors = array();
$result_doctors->data_seek(0);
while($doctor = $result_doctors->fetch_assoc()) {
    $doctors[] = array(
        'id' => $doctor['users_id'],
        'name' => $doctor['users_fname'] . ' ' . $doctor['users_lname']
    );
}
$doctors_json = json_encode($doctors);


// ดึงข้อมูลพยาบาล
$sql_nurses = "SELECT * FROM users WHERE position_id = 4"; // สมมติว่า position_id 4 คือพยาบาล
$result_nurses = $conn->query($sql_nurses);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <!-- เพิ่ม head content เหมือนกับหน้าอื่นๆ -->
    <title>บริการ - D Care Clinic</title>
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
          body {
        background-color: #f8f9fa;
    }
    
    .card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    
    .card-header {
        background-color: #4e73df;
        color: white;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        padding: 15px 20px;
    }
    
    .card-title {
        margin-bottom: 0;
        font-weight: 600;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .list-group-item {
        border: none;
        background-color: #f1f3f9;
        margin-bottom: 10px;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    
    .list-group-item:hover {
        background-color: #e9ecef;
    }
    
    .form-check-input:checked + .form-check-label {
        text-decoration: none;
        color: #4e73df;
    }
    
    .text-muted {
        font-size: 0.85em;
        color: #6c757d !important;
    }
    
    .btn {
        border-radius: 5px;
        padding: 8px 16px;
        font-weight: 500;
    }
    
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
    }
    
    .btn-secondary {
        background-color: #858796;
        border-color: #858796;
    }
    
    .btn-secondary:hover {
        background-color: #717384;
        border-color: #717384;
    }
    
    .doctor-field, .nurse-field {
        background-color: #fff;
        border: 1px solid #e3e6f0;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    
    .doctor-field:hover, .nurse-field:hover {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .form-label {
        font-weight: 600;
        color: #5a5c69;
    }
    
    .form-control, .form-select {
        border-radius: 5px;
        border: 1px solid #d1d3e2;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #bac8f3;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .input-group {
        margin-top: 10px;
    }
    
    .remove-doctor, .remove-nurse {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }

    #selectedCourses {
        background-color: #e8f0fe;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }

    #selectedCourses div {
        background-color: white;
        border-radius: 3px;
        padding: 10px;
        margin-bottom: 5px;
    }
        .list-group-item .form-check-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .list-group-item .text-muted {
            font-size: 0.85em;
        }
         #addDoctor, #addNurse {
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .doctor-field, .nurse-field {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
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

                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="py-3 mb-4"><span class="text-muted fw-light">บริการ /</span> รายละเอียดบริการ</h4>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title"><i class="ri-calendar-event-fill mr-2"></i> คอร์สที่จองไว้</h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($result_courses->num_rows > 0): ?>
                                            <ul class="list-group">
                                                <?php while($course = $result_courses->fetch_assoc()): ?>
                                                    <li class="list-group-item">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="<?php echo $course['course_id']; ?>" id="course_<?php echo $course['course_id']; ?>">
                                                            <label class="form-check-label" for="course_<?php echo $course['course_id']; ?>">
                                                                <?php echo $course['course_name']; ?> - 
                                                                <?php echo number_format($course['course_price'], 2); ?> บาท
                                                                <small class="text-muted ml-2">
                                                                    (วันที่จอง: <?php echo date('d/m/Y H:i', strtotime($course['booking_datetime'])); ?>)
                                                                </small>
                                                            </label>
                                                        </div>
                                                    </li>
                                                <?php endwhile; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p class="text-muted">ไม่พบคอร์สที่จองไว้สำหรับวันนี้หรือในอนาคต</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title">ข้อมูลลูกค้า</h5>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>HN:</strong> <?php echo $hn; ?></p>
                                        <p><strong>ชื่อ-นามสกุล:</strong> <?php echo $customer['cus_firstname'] . ' ' . $customer['cus_lastname']; ?></p>
                                        <p><strong>เบอร์โทร:</strong> <?php echo $customer['cus_tel']; ?></p>
                                        <!-- เพิ่มข้อมูลลูกค้าอื่นๆ ตามต้องการ -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title"><i class="ri-file-list-3-fill mr-2"></i> รายละเอียดบริการ</h5>
                            </div>
                            <div class="card-body">
                                <form id="serviceForm">
                                    <div id="selectedCourses" class="mb-4">
                                        <h6 class="mb-3">คอร์สที่เลือก:</h6>
                                        <!-- รายการคอร์สที่เลือกจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                    </div>
                                    <div id="doctorContainer" class="mb-4">
                                        <h6 class="mb-3">แพทย์:</h6>
                                        <!-- ส่วนของแพทย์จะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                    </div>
                                    <button type="button" class="btn btn-secondary mb-3" id="addDoctor"><i class="ri-user-add-fill mr-1"></i> เพิ่มแพทย์</button>
                                    <div id="nurseContainer" class="mb-4">
                                        <h6 class="mb-3">พยาบาล:</h6>
                                        <!-- ส่วนของพยาบาลจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                    </div>
                                    <button type="button" class="btn btn-secondary mb-3" id="addNurse"><i class="ri-nurse-fill mr-1"></i> เพิ่มพยาบาล</button>
                                    <div class="mb-3">
                                        <label for="note" class="form-label">หมายเหตุ</label>
                                        <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><i class="ri-save-fill mr-1"></i> บันทึกข้อมูล</button>
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
    var doctors = <?php echo $doctors_json; ?>;

$(document).ready(function() {
    // เพิ่มคอร์สที่เลือกลงในรายการ
    $('input[type="checkbox"]').change(function() {
        updateSelectedCourses();
    });

    // เพิ่มแพทย์
    $('#addDoctor').click(function() {
        addDoctorField();
    });

    // เพิ่มพยาบาล
    $('#addNurse').click(function() {
        addNurseField();
    });

    // บันทึกข้อมูล
    $('#serviceForm').submit(function(e) {
        e.preventDefault();
        // ทำการบันทึกข้อมูลที่นี่
        console.log('บันทึกข้อมูล');
    });

    function updateSelectedCourses() {
        var selectedCourses = '';
        $('input[type="checkbox"]:checked').each(function() {
            var courseId = $(this).val();
            var courseName = $(this).next('label').text();
            selectedCourses += '<div class="mb-3">' + courseName + '</div>';
        });
        $('#selectedCourses').html(selectedCourses);
    }

    function addDoctorField() {
            var doctorHtml = `
                <div class="mb-3 doctor-field">
                    <label class="form-label">แพทย์</label>
                    <select class="form-select doctor-select" name="doctor[]">
                        <option value="">เลือกแพทย์</option>
                        ${doctors.map(doctor => `<option value="${doctor.id}">${doctor.name}</option>`).join('')}
                    </select>
                    <div class="input-group mt-2">
                        <input type="number" class="form-control doctor-df" name="doctor_df[]" min="0" max="100" placeholder="DF">
                        <select class="form-select doctor-df-type" name="doctor_df_type[]">
                            <option value="amount">บาท</option>
                            <option value="percent">%</option>
                        </select>
                        <button type="button" class="btn btn-danger remove-doctor">ลบ</button>
                    </div>
                </div>
            `;
            $('#doctorContainer').append(doctorHtml);
        }

     // เพิ่มแพทย์
        $('#addDoctor').click(function() {
            addDoctorField();
        });

        // ลบแพทย์
        $(document).on('click', '.remove-doctor', function() {
            $(this).closest('.doctor-field').remove();
        });

        // เพิ่มแพทย์เริ่มต้นหนึ่งคน
        // addDoctorField();

    function addNurseField() {
        var nurseHtml = `
            <div class="mb-3 nurse-field">
                <label class="form-label">พยาบาล</label>
                <select class="form-select nurse-select" name="nurse[]">
                    <option value="">เลือกพยาบาล</option>
                    <?php 
                    $result_nurses->data_seek(0);
                    while($nurse = $result_nurses->fetch_assoc()): 
                    ?>
                        <option value="<?php echo $nurse['users_id']; ?>"><?php echo $nurse['users_fname'] . ' ' . $nurse['users_lname']; ?></option>
                    <?php endwhile; ?>
                </select>
                <div class="input-group mt-2">
                    <input type="number" class="form-control nurse-df" name="nurse_df[]" min="0" max="100" placeholder="DF">
                    <select class="form-select nurse-df-type" name="nurse_df_type[]">
                        <option value="amount">บาท</option>
                        <option value="percent">%</option>
                    </select>
                    <button type="button" class="btn btn-danger remove-nurse">ลบ</button>
                </div>
            </div>
        `;
        $('#nurseContainer').append(nurseHtml);
    }

    // ลบแพทย์
    $(document).on('click', '.remove-doctor', function() {
        $(this).closest('.doctor-field').remove();
    });

    // ลบพยาบาล
    $(document).on('click', '.remove-nurse', function() {
        $(this).closest('.nurse-field').remove();
    });

    // เพิ่มแพทย์และพยาบาลเริ่มต้นหนึ่งคน
    addDoctorField();
    addNurseField();
});
</script>
</body>
</html>