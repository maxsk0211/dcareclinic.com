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
        border-radius: 15px;
        margin-bottom: 30px;
        overflow: hidden;
    }
    
    .card-header {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border-bottom: none;
        padding: 20px;
    }
    
    .card-title {
        margin-bottom: 0;
        font-weight: 600;
        font-size: 1.25rem;
    }
    
    .card-body {
        padding: 30px;
    }
    
    .order-item, .customer-info {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .order-item:hover, .customer-info:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .order-item h6, .customer-info h6 {
        color: #4e73df;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 10px;
        margin-bottom: 15px;
        font-weight: 600;
    }
    
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .order-id {
        font-size: 1.2em;
        color: #4e73df;
        margin: 0;
    }
    
    .order-info p {
        margin-bottom: 5px;
    }
    
    .course-list-title {
        margin-top: 20px;
        margin-bottom: 10px;
        color: #4e73df;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 5px;
    }
    
    .course-list {
        list-style-type: none;
        padding: 0;
        margin-bottom: 0;
    }
    
    .course-item {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 10px 15px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .course-info {
        display: flex;
        justify-content: space-between;
        width: 100%;
        margin-right: 10px;
    }
    
    .course-name {
        font-weight: 500;
        color: #333;
    }
    
    .course-price {
        font-weight: 600;
        color: #28a745;
    }
    
    .total-price {
        font-size: 1.2em;
        font-weight: 700;
        color: #4e73df;
        text-align: right;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 2px solid #e9ecef;
    }
    
    .badge {
        font-size: 0.85em;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .bg-warning {
        background-color: #ffc107 !important;
        color: #000;
    }
    
    .bg-success {
        background-color: #28a745 !important;
    }
    
    .bg-info {
        background-color: #17a2b8 !important;
    }
    
    .text-muted {
        color: #6c757d !important;
    }
    
    .customer-info strong {
        font-weight: 600;
        color: #495057;
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
    
    .ri-calendar-2-line,
    .ri-calendar-check-line {
        margin-right: 5px;
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
                                        <h5 class="card-title"><i class="ri-user-fill mr-2"></i> ข้อมูลลูกค้า</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="customer-info">
                                            <h6>ข้อมูลส่วนตัว</h6>
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
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0"><i class="ri-shopping-cart-fill mr-2"></i> รายละเอียดคำสั่งซื้อสำหรับการบริการวันนี้</h5>

                                        <?php 
                                        // ดึงข้อมูลคำสั่งซื้อที่เกี่ยวข้องกับคิวปัจจุบัน
                                        $sql_order = "SELECT oc.oc_id, oc.order_datetime, oc.order_payment, oc.order_net_total, oc.order_status,
                                                             cb.booking_datetime
                                                      FROM service_queue sq
                                                      JOIN course_bookings cb ON sq.booking_id = cb.id
                                                      JOIN order_course oc ON cb.id = oc.course_bookings_id
                                                      WHERE sq.queue_id = '$queue_id'";
                                        $result_order = $conn->query($sql_order);

                                        if ($result_order->num_rows > 0): 
                                            $order = $result_order->fetch_assoc();

                                        ?>
                                        <a href="edit-order.php?id=<?php echo $order['oc_id']; ?>" class="btn btn-primary btn-sm">
                                            <i class="ri-edit-2-line"></i> แก้ไขคำสั่งซื้อ
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($result_order->num_rows > 0): ?>
                                            <div class="order-item">
                                                <div class="order-header">

                                                    <h6 class="order-id">คำสั่งซื้อ #<?php echo 'ORDER-' . str_pad($order['oc_id'], 6, '0', STR_PAD_LEFT); ?></h6>
                                                    <span class="badge <?php echo ($order['order_payment'] == 'ยังไม่จ่ายเงิน') ? 'bg-warning' : 'bg-success'; ?>">
                                                        <?php echo $order['order_payment']; ?>
                                                    </span>
                                                </div>
                                                <div class="order-info">
                                                    <p><i class="ri-calendar-2-line"></i> <strong>วันที่สั่งซื้อ:</strong> <?php echo date('d/m/Y H:i', strtotime($order['order_datetime'])); ?></p>
                                                    <p><i class="ri-calendar-check-line"></i> <strong>วันที่นัดรับบริการ:</strong> <?php echo date('d/m/Y H:i', strtotime($order['booking_datetime'])); ?></p>
                                                </div>
                                                
                                                <h6 class="course-list-title">รายการคอร์ส:</h6>
                                                <ul class="course-list">
                                                <?php
                                                $sql_details = "SELECT od.od_id, c.course_name, od.od_amount, od.od_price,
                                                       CASE WHEN cu.id IS NOT NULL THEN 1 ELSE 0 END AS is_used
                                                FROM order_detail od
                                                JOIN course c ON od.course_id = c.course_id
                                                LEFT JOIN course_usage cu ON od.od_id = cu.od_id AND cu.queue_id = '$queue_id'
                                                WHERE od.oc_id = '{$order['oc_id']}'";
                                                $result_details = $conn->query($sql_details);
                                                $result_details->data_seek(0);
                                                while($detail = $result_details->fetch_assoc()):
                                                ?>
                                                    <li class="course-item">
                                                        <div class="course-info">
                                                            <span class="course-name"><?php echo $detail['course_name']; ?></span>
                                                            <span class="course-price"><?php echo number_format($detail['od_price'], 2); ?> บาท</span>
                                                        </div>
                                                        <?php if($detail['is_used']): ?>
                                                            <span class="badge bg-info">ใช้บริการแล้ว</span>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endwhile; ?>
                                                </ul>
                                                <div class="total-price">
                                                    <strong>ราคารวม:</strong> <span><?php echo number_format($order['order_net_total'], 2); ?> บาท</span>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted text-center">ไม่พบข้อมูลคำสั่งซื้อสำหรับการบริการนี้</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
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