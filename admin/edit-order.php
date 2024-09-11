<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    die("ไม่พบข้อมูลคำสั่งซื้อ");
}

// ดึงข้อมูลคำสั่งซื้อ
$sql = "SELECT oc.*, c.cus_firstname, c.cus_lastname, cb.booking_datetime
        FROM order_course oc
        JOIN course_bookings cb ON oc.course_bookings_id = cb.id
        JOIN customer c ON cb.cus_id = c.cus_id
        WHERE oc.oc_id = $order_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("ไม่พบข้อมูลคำสั่งซื้อที่ระบุ");
}

$order = $result->fetch_assoc();

// ดึงรายละเอียดคอร์ส
$sql_details = "SELECT od.*, c.course_name, c.course_price
                FROM order_detail od
                JOIN course c ON od.course_id = c.course_id
                WHERE od.oc_id = $order_id";
$result_details = $conn->query($sql_details);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>แก้ไขคำสั่งซื้อ - D Care Clinic</title>
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
        .course-item {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .course-resources {
            background-color: #e9ecef;
            border-radius: 8px;
            padding: 10px;
            margin-top: 10px;
        }
        .resource-item {
            background-color: #ffffff;
            border-radius: 5px;
            padding: 8px;
            margin-bottom: 5px;
        }
        .resource-item {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }
        .resource-item .row {
            align-items: center;
        }
        .resource-details {
            margin-bottom: 5px;
            line-height: 1.4;
        }
        .resource-total {
            margin-top: 5px;
            text-align: right;
            color: #6c757d;
        }

        .course-resources {
            margin-top: 20px;
        }
        .course-resources h6 {
            margin-bottom: 10px;
        }
        .table {
            font-size: 0.9em;
        }
        .table th {
            background-color: #f8f9fa;
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

                    <div class="container-xxl flex-grow-1 container-p-y">
                        <h4 class="fw-bold py-3 mb-4">แก้ไขคำสั่งซื้อ #<?php echo $order_id; ?></h4>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <h5 class="card-header">รายละเอียดคำสั่งซื้อ</h5>
                                    <div class="card-body">
                                        <form id="editOrderForm" method="post" action="sql/update-order.php">
                                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">ชื่อลูกค้า</label>
                                                <input type="text" class="form-control" value="<?php echo $order['cus_firstname'] . ' ' . $order['cus_lastname']; ?>" readonly>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">วันที่นัดรับบริการ</label>
                                                <input type="datetime-local" class="form-control" name="booking_datetime" value="<?php echo date('Y-m-d\TH:i', strtotime($order['booking_datetime'])); ?>">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">สถานะการชำระเงิน</label>
                                                <select class="form-select" name="order_payment">
                                                    <option value="ยังไม่จ่ายเงิน" <?php echo ($order['order_payment'] == 'ยังไม่จ่ายเงิน') ? 'selected' : ''; ?>>ยังไม่จ่ายเงิน</option>
                                                    <option value="จ่ายแล้ว" <?php echo ($order['order_payment'] == 'จ่ายแล้ว') ? 'selected' : ''; ?>>จ่ายแล้ว</option>
                                                </select>
                                            </div>
                                            
                                            <h6 class="mb-3">รายการคอร์ส</h6>
                                            <div id="courseList">
                                                <?php while ($detail = $result_details->fetch_assoc()): ?>
                                                    <div class="course-item" data-course-id="<?php echo $detail['course_id']; ?>">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-2">
                                                                <input type="text" class="form-control" name="course_name[]" value="<?php echo $detail['course_name']; ?>" readonly>
                                                            </div>
                                                            <div class="col-md-2 mb-2">
                                                                <input type="number" class="form-control" name="course_amount[]" value="<?php echo $detail['od_amount']; ?>" min="1">
                                                            </div>
                                                            <div class="col-md-3 mb-2">
                                                                <input type="number" class="form-control" name="course_price[]" value="<?php echo $detail['od_price']; ?>" step="0.01">
                                                            </div>
                                                            <div class="col-md-1 mb-2">
                                                                <button type="button" class="btn btn-danger btn-sm remove-course">ลบ</button>
                                                            </div>
                                                        </div>
                                                        
<div class="course-resources">
    <h6>ทรัพยากรที่ใช้จริง:</h6>
    <table class="table table-bordered table-striped resource-table">
        <thead>
            <tr>
                <th>ทรัพยากร</th>
                <th>ประเภท</th>
                <th>จำนวน</th>
                <th>หน่วยนับ</th>
                <th>การดำเนินการ</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // ตรวจสอบว่ามีข้อมูลใน order_course_resources หรือไม่
        $sql_check = "SELECT COUNT(*) as count FROM order_course_resources WHERE order_id = $order_id AND course_id = {$detail['course_id']}";
        $result_check = $conn->query($sql_check);
        $row_check = $result_check->fetch_assoc();

        if ($row_check['count'] == 0) {
            // ถ้าไม่มีข้อมูล ให้เพิ่มข้อมูลจาก course_resources
            $sql_insert = "INSERT INTO order_course_resources (order_id, course_id, resource_type, resource_id, quantity)
                           SELECT $order_id, {$detail['course_id']}, resource_type, resource_id, quantity
                           FROM course_resources
                           WHERE course_id = {$detail['course_id']}";
            $conn->query($sql_insert);
        }

        // ดึงข้อมูลจาก order_course_resources
        $sql_resources = "SELECT ocr.*, 
                                 d.drug_name, d.drug_unit_id,
                                 t.tool_name, t.tool_unit_id,
                                 a.acc_name, a.acc_unit_id,
                                 u.unit_name
                          FROM order_course_resources ocr
                          LEFT JOIN drug d ON ocr.resource_id = d.drug_id AND ocr.resource_type = 'drug'
                          LEFT JOIN tool t ON ocr.resource_id = t.tool_id AND ocr.resource_type = 'tool'
                          LEFT JOIN accessories a ON ocr.resource_id = a.acc_id AND ocr.resource_type = 'accessory'
                          LEFT JOIN unit u ON 
                              CASE 
                                  WHEN ocr.resource_type = 'drug' THEN d.drug_unit_id = u.unit_id
                                  WHEN ocr.resource_type = 'tool' THEN t.tool_unit_id = u.unit_id
                                  WHEN ocr.resource_type = 'accessory' THEN a.acc_unit_id = u.unit_id
                              END
                          WHERE ocr.order_id = $order_id AND ocr.course_id = {$detail['course_id']}";
        
        $result_resources = $conn->query($sql_resources);
        while ($resource = $result_resources->fetch_assoc()):
            $resource_name = '';
            $resource_type = '';
            switch ($resource['resource_type']) {
                case 'drug':
                    $resource_name = $resource['drug_name'];
                    $resource_type = 'ยา';
                    break;
                case 'tool':
                    $resource_name = $resource['tool_name'];
                    $resource_type = 'เครื่องมือ';
                    break;
                case 'accessory':
                    $resource_name = $resource['acc_name'];
                    $resource_type = 'อุปกรณ์เสริม';
                    break;
            }
        ?>
            <tr>
                <td><?php echo $resource_name; ?></td>
                <td><?php echo $resource_type; ?></td>
                <td>
                    <input type="number" class="form-control resource-quantity" 
                           name="resource[<?php echo $resource['id']; ?>][quantity]" 
                           value="<?php echo $resource['quantity']; ?>" step="0.01"
                           data-resource-id="<?php echo $resource['id']; ?>">
                </td>
                <td><?php echo $resource['unit_name']; ?></td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-resource" 
                            data-resource-id="<?php echo $resource['id']; ?>">ลบ</button>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addResourceModal">เพิ่มทรัพยากร</button>
</div>
                                                    </div>
                                                <?php endwhile; ?>
                                            </div>
                                            
                                            <button type="button" class="btn btn-primary mb-3" id="addCourse">เพิ่มคอร์ส</button>
                                            
                                            <div class="text-end mt-3">
                                                <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                                                <a href="service.php" class="btn btn-secondary">ยกเลิก</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include 'footer.php'; ?>
                </div>
            </div>
        </div>
    </div>


<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="addResourceModal" tabindex="-1" aria-labelledby="addResourceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addResourceModalLabel">เพิ่มทรัพยากร</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addResourceForm" onsubmit="return false;">
          <div class="mb-3">
            <label for="resourceType" class="form-label">ประเภททรัพยากร</label>
            <select class="form-select" id="resourceType" required>
              <option value="">เลือกประเภททรัพยากร</option>
              <option value="drug">ยา</option>
              <option value="tool">เครื่องมือ</option>
              <option value="accessory">อุปกรณ์เสริม</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="resourceId" class="form-label">ทรัพยากร</label>
            <select class="form-select" id="resourceId" required disabled>
              <option value="">เลือกทรัพยากร</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="resourceQuantity" class="form-label">จำนวน</label>
            <input type="number" class="form-control" id="resourceQuantity" min="0.01" step="0.01" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
        <button type="button" class="btn btn-primary" id="saveResource">บันทึก</button>
      </div>
    </div>
  </div>
</div>


    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <script>
$(document).ready(function() {
    // เมื่อเปลี่ยนประเภททรัพยากร
    $('#resourceType').change(function() {
        var resourceType = $(this).val();
        if (resourceType) {
            $.ajax({
                url: 'sql/get-resources.php',
                type: 'GET',
                data: { type: resourceType },
                dataType: 'json',
                success: function(resources) {
                    var options = '<option value="">เลือกทรัพยากร</option>';
                    resources.forEach(function(resource) {
                        options += '<option value="' + resource.id + '">' + resource.name + '</option>';
                    });
                    $('#resourceId').html(options).prop('disabled', false);
                }
            });
        } else {
            $('#resourceId').html('<option value="">เลือกทรัพยากร</option>').prop('disabled', true);
        }
    });

    // เมื่อกดปุ่มบันทึกใน modal
    $('#saveResource').click(function() {
        var resourceType = $('#resourceType').val();
        var resourceId = $('#resourceId').val();
        var quantity = $('#resourceQuantity').val();

        // ตรวจสอบว่าเลือกรายการครบหรือไม่
        if (!resourceType || !resourceId || !quantity) {
            alert('กรุณากรอกข้อมูลให้ครบทุกช่อง');
            return;
        }

        var orderId = $('input[name="order_id"]').val();
        var courseId = $('.course-item').data('course-id');

        $.ajax({
            url: 'sql/add-order-resource.php',
            type: 'POST',
            data: {
                order_id: orderId,
                course_id: courseId,
                resource_type: resourceType,
                resource_id: resourceId,
                quantity: quantity
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#addResourceModal').modal('hide');
                    location.reload(); // รีโหลดหน้าเพื่อแสดงทรัพยากรใหม่
                } else {
                    alert('เกิดข้อผิดพลาดในการเพิ่มทรัพยากร: ' + response.message);
                }
            }
        });
    });

    // เมื่อเปลี่ยนแปลงจำนวนทรัพยากร
    $('.resource-quantity').change(function() {
        var resourceId = $(this).data('resource-id');
        var quantity = $(this).val();
        var orderId = $('input[name="order_id"]').val();

        $.ajax({
            url: 'sql/update-order-resource.php',
            type: 'POST',
            data: {
                order_id: orderId,
                resource_id: resourceId,
                quantity: quantity
            },
            dataType: 'json',
            success: function(response) {
                if (!response.success) {
                    alert('เกิดข้อผิดพลาดในการอัพเดททรัพยากร');
                }
            }
        });
    });

    // รีเซ็ตฟอร์มเมื่อปิด modal
    $('#addResourceModal').on('hidden.bs.modal', function () {
        $('#addResourceForm')[0].reset();
        $('#resourceId').html('<option value="">เลือกทรัพยากร</option>').prop('disabled', true);
    });

    // เมื่อกดปุ่มลบทรัพยากร
    $('.remove-resource').click(function() {
        var resourceId = $(this).data('resource-id');
        var orderId = $('input[name="order_id"]').val();

        if (confirm('คุณแน่ใจหรือไม่ที่จะลบทรัพยากรนี้?')) {
            $.ajax({
                url: 'sql/delete-order-resource.php',
                type: 'POST',
                data: {
                    order_id: orderId,
                    resource_id: resourceId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload(); // รีโหลดหน้าเพื่อแสดงการเปลี่ยนแปลง
                    } else {
                        alert('เกิดข้อผิดพลาดในการลบทรัพยากร');
                    }
                }
            });
        }
    });

    // ป้องกันการกด Enter ใน modal
    $('#addResourceModal').on('keypress', function(event) {
        if (event.keyCode === 13) { // 13 คือ keyCode ของปุ่ม Enter
            event.preventDefault();
            return false;
        }
    });


});


    </script>
</body>
</html>