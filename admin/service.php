<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

// เพิ่ม error reporting เพื่อ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

$queue_id = isset($_GET['queue_id']) ? intval($_GET['queue_id']) : 0;

if (!$queue_id) {
    die("ไม่พบข้อมูลคิว");
}

// ดึงข้อมูลคิวและลูกค้า
$sql = "SELECT sq.*, c.*, cb.booking_datetime
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

$cus_id = $queue_data['cus_id'];

// ดึงข้อมูลคอร์สที่เคยจอง
$sql_courses = "SELECT DISTINCT od.od_id, od.course_id, c.course_name, c.course_price, cb.booking_datetime,
                       CASE WHEN cu.id IS NOT NULL THEN 1 ELSE 0 END AS is_used,
                       cu.queue_id AS used_queue_id, cu.used_date
                FROM course_bookings cb
                JOIN order_course oc ON cb.id = oc.course_bookings_id
                JOIN order_detail od ON oc.oc_id = od.oc_id
                JOIN course c ON od.course_id = c.course_id
                LEFT JOIN course_usage cu ON od.od_id = cu.od_id
                WHERE cb.cus_id = '$cus_id'
                AND cb.booking_datetime >= CURDATE()
                AND cb.status = 'confirmed'
                ORDER BY cb.booking_datetime ASC";
$result_courses = $conn->query($sql_courses);


// รีเซ็ตตัวชี้ข้อมูลกลับไปที่จุดเริ่มต้น
$result_courses->data_seek(0);
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
        .course-item {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .course-item.used {
            background-color: #f8f9fa;
            opacity: 0.7;
        }
        .course-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .course-date {
            font-size: 0.8em;
            color: #6c757d;
        }
        .badge-used {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.8em;
        }
        .customer-info {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .customer-info h5 {
            color: #007bff;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .customer-info p {
            margin-bottom: 10px;
        }
        .customer-info strong {
            font-weight: 600;
            margin-right: 10px;
        }
        .service-details .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #007bff;
        }

        .service-details .card-title {
            color: #007bff;
            font-weight: bold;
        }

        .used-courses-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .used-course-item {
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .used-course-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .used-course-item .course-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .used-course-item .course-name {
            font-weight: bold;
            color: #333;
        }

        .used-course-item .course-price {
            color: #28a745;
            font-weight: bold;
        }

        .used-course-item .usage-date {
            font-size: 0.9em;
            color: #6c757d;
        }

        .used-course-item .usage-date i {
            margin-right: 5px;
        }

        .no-courses {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 4px;
            color: #6c757d;
        }
        .total-price {
            margin-top: 15px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 4px;
            text-align: right;
            font-size: 1.1em;
        }

        .total-price strong {
            margin-right: 10px;
        }

        .total-price span {
            color: #28a745;
            font-weight: bold;
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
                                            <form id="courseSelectionForm">
                                                <?php while($course = $result_courses->fetch_assoc()): ?>
                                                    <div class="course-item <?php echo $course['is_used'] ? 'used' : ''; ?>">
                                                        <div class="course-info">
                                                            <div>
                                                                <input class="form-check-input" type="checkbox" 
                                                                       name="selected_courses[]" 
                                                                       value="<?php echo $course['od_id']; ?>" 
                                                                       id="course_<?php echo $course['od_id']; ?>" 
                                                                       <?php echo $course['is_used'] ? 'disabled checked' : ''; ?>>
                                                                <label class="form-check-label" for="course_<?php echo $course['od_id']; ?>">
                                                                    <?php echo $course['course_name']; ?> - 
                                                                    <?php echo number_format($course['course_price'], 2); ?> บาท
                                                                </label>
                                                            </div>
                                                            <?php if ($course['is_used']): ?>
                                                                <span class="badge-used">ใช้งานแล้ว</span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="course-date">
                                                            วันที่จอง: <?php echo date('d/m/Y H:i', strtotime($course['booking_datetime'])); ?>
                                                            <?php if ($course['is_used']): ?>
                                                                <br>วันที่ใช้บริการ: <?php echo date('d/m/Y H:i', strtotime($course['used_date'])); ?>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endwhile; ?>
                                                <button type="submit" class="btn btn-primary mt-3">บันทึกการใช้บริการ</button>
                                            </form>
                                        <?php else: ?>
                                            <p class="text-muted">ไม่พบคอร์สที่จองไว้สำหรับวันนี้หรือในอนาคต</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h5 class="card-title"><i class="ri-user-fill mr-2"></i> ข้อมูลลูกค้า</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="customer-info">
                                            <h5>ข้อมูลส่วนตัว</h5>
                                            <p><strong>รหัสลูกค้า (HN):</strong> <?php echo 'HN-' . str_pad($queue_data['cus_id'], 6, '0', STR_PAD_LEFT); ?></p>
                                            <p><strong>ชื่อ-นามสกุล:</strong> <?php echo $queue_data['cus_firstname'] . ' ' . $queue_data['cus_lastname']; ?></p>
                                            <p><strong>ชื่อเล่น:</strong> <?php echo $queue_data['cus_nickname']; ?></p>
                                            <p><strong>เพศ:</strong> <?php echo $queue_data['cus_gender']; ?></p>
                                            <p><strong>วันเกิด:</strong> <?php echo date('d/m/Y', strtotime($queue_data['cus_birthday'])); ?></p>
                                            <p><strong>เลขบัตรประชาชน:</strong> <?php echo $queue_data['cus_id_card_number']; ?></p>
                                            <p><strong>กรุ๊ปเลือด:</strong> <?php echo $queue_data['cus_blood']; ?></p>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4 service-details">
                                    <div class="card-header">
                                        <h5 class="card-title"><i class="ri-file-list-3-line mr-2"></i> รายละเอียดบริการ</h5>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="mb-3">คอร์สที่ใช้บริการในครั้งนี้:</h6>
                                        <?php
                                        $sql_used_courses = "SELECT c.course_name, c.course_price, cu.used_date
                                                             FROM course_usage cu
                                                             JOIN order_detail od ON cu.od_id = od.od_id
                                                             JOIN course c ON od.course_id = c.course_id
                                                             WHERE cu.queue_id = $queue_id";
                                        $result_used_courses = $conn->query($sql_used_courses);
                                        if ($result_used_courses->num_rows > 0): ?>
                                            <div class="used-courses-list">
                                                <?php while($used_course = $result_used_courses->fetch_assoc()): ?>
                                                    <div class="used-course-item">
                                                        <div class="course-info">
                                                            <span class="course-name"><?php echo $used_course['course_name']; ?></span>
                                                            <span class="course-price"><?php echo number_format($used_course['course_price'], 2); ?> บาท</span>
                                                        </div>
                                                        <div class="usage-date">
                                                            <i class="ri-time-line"></i> <?php echo date('d/m/Y H:i', strtotime($used_course['used_date'])); ?>
                                                        </div>
                                                    </div>
                                                <?php endwhile; ?>
                                                <?php
                                                    $total_price = 0;
                                                    $result_used_courses->data_seek(0); // รีเซ็ตตัวชี้ข้อมูล
                                                    while($used_course = $result_used_courses->fetch_assoc()) {
                                                        $total_price += $used_course['course_price'];
                                                    }
                                                    ?>
                                                    <div class="total-price">
                                                        <strong>ราคารวม:</strong> <span><?php echo number_format($total_price, 2); ?> บาท</span>
                                                    </div>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted no-courses">ยังไม่มีการใช้บริการคอร์สในครั้งนี้</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6"></div>
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
 

  document.getElementById('courseSelectionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var selectedCourses = Array.from(document.querySelectorAll('input[name="selected_courses[]"]:checked'))
                                   .map(el => el.value);
        
        if (selectedCourses.length === 0) {
            alert('กรุณาเลือกคอร์สอย่างน้อยหนึ่งรายการ');
            return;
        }

        // ส่งข้อมูลไปยัง server ด้วย AJAX
        fetch('sql/update-course-usage.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                queue_id: <?php echo $queue_id; ?>,
                selected_courses: selectedCourses
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('บันทึกการใช้บริการสำเร็จ');
                location.reload(); // รีโหลดหน้าเพื่อแสดงสถานะใหม่
            } else {
                alert('เกิดข้อผิดพลาด: ' + data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล');
        });
    });
</script>
</body>
</html>