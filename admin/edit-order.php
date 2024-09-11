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
                                                <input type="datetime-local" class="form-control" name="booking_datetime" id="booking_datetime" value="<?php echo date('Y-m-d\TH:i', strtotime($order['booking_datetime'])); ?>">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">สถานะการชำระเงิน</label>
                                                <select class="form-select" name="order_payment" id="payment_method">
                                                    <option value="ยังไม่จ่ายเงิน" <?php echo ($order['order_payment'] == 'ยังไม่จ่ายเงิน') ? 'selected' : ''; ?>>ยังไม่จ่ายเงิน</option>
                                                    <option value="จ่ายแล้ว" <?php echo ($order['order_payment'] == 'จ่ายแล้ว') ? 'selected' : ''; ?>>จ่ายแล้ว</option>
                                                </select>
                                            </div>
                                            
                                            <h6 class="mb-3">รายการคอร์ส</h6>
                                            <div id="courseList">
                                                <!-- รายการคอร์สจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                            </div>
                                            <button type="button" class="btn btn-primary mb-3" onclick="addNewCourse()">เพิ่มคอร์ส</button>
                                            
                                            <div class="text-end mt-3">
                                                <h5>ราคารวม: <span id="totalPrice">0</span> บาท</h5>
                                                <button type="button" class="btn btn-primary" onclick="saveOrder()">บันทึกการแก้ไข</button>
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

 <!-- Template สำหรับรายการคอร์ส -->
    <template id="courseTemplate">
        <div class="course-item mb-3" data-course-id="">
            <div class="row">
                <div class="col-md-4">
                    <select class="form-select course-select" onchange="updateCourseDetails(this)">
                        <option value="">เลือกคอร์ส</option>
                        <!-- ตัวเลือกคอร์สจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control course-amount" value="1" min="1" onchange="updateTotalPrice()">
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control course-price" readonly>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-danger" onclick="removeCourse(this)">ลบคอร์ส</button>
                    <button type="button" class="btn btn-info" onclick="showResourceModal(this)">จัดการทรัพยากร</button>
                </div>
            </div>
            <div class="resources-table mt-2">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ประเภท</th>
                            <th>ชื่อทรัพยากร</th>
                            <th>จำนวน</th>
                            <th>หน่วย</th>
                            <th>การดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- รายการทรัพยากรจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </template>

    <!-- Modal สำหรับจัดการทรัพยากร -->
    <div class="modal fade" id="resourceModal" tabindex="-1" aria-labelledby="resourceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="resourceModalLabel">จัดการทรัพยากร</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addResourceForm">
                        <div class="mb-3">
                            <label for="resourceType" class="form-label">ประเภททรัพยากร</label>
                            <select class="form-select" id="resourceType" required>
                                <option value="">เลือกประเภท</option>
                                <option value="drug">ยา</option>
                                <option value="tool">เครื่องมือ</option>
                                <option value="accessory">อุปกรณ์เสริม</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="resourceId" class="form-label">ทรัพยากร</label>
                            <select class="form-select" id="resourceId" required>
                                <option value="">เลือกทรัพยากร</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="resourceQuantity" class="form-label">จำนวน</label>
                            <input type="number" class="form-control" id="resourceQuantity" required min="0.01" step="0.01">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" onclick="addResource()">เพิ่มทรัพยากร</button>
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
let currentOrderId;

function loadExistingOrder() {
    currentOrderId = document.querySelector('input[name="order_id"]').value;
    $.ajax({
        url: 'sql/get-order-details.php',
        type: 'GET',
        data: { id: currentOrderId },
        success: function(response) {
            const orderDetails = JSON.parse(response);
            $('#booking_datetime').val(orderDetails.booking_datetime);
            $('#payment_method').val(orderDetails.payment_status);
            
            orderDetails.courses.forEach(course => {
                addCourseToList(course);
            });
            
            updateTotalPrice();
        },
        error: function(xhr, status, error) {
            console.error('Error loading order details:', error);
        }
    });
}

function addCourseToList(course) {
    const courseList = document.getElementById('courseList');
    const courseItem = document.createElement('div');
    courseItem.className = 'course-item mb-3';
    courseItem.dataset.courseId = course.id;
    
    courseItem.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <input type="text" class="form-control course-name" value="${course.name}" readonly>
            </div>
            <div class="col-md-2">
                <input type="number" class="form-control course-amount" value="${course.amount}" min="1" onchange="updateCourse(this)">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control course-price" value="${course.price}" onchange="updateCourse(this)">
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-danger" onclick="removeCourse(this)">ลบคอร์ส</button>
            </div>
        </div>
        <div class="resources-table mt-2">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ประเภท</th>
                        <th>ชื่อทรัพยากร</th>
                        <th>จำนวน</th>
                        <th>หน่วย</th>
                        <th>การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    ${course.resources.map(resource => `
                        <tr data-resource-id="${resource.id}">
                            <td>${resource.type}</td>
                            <td>${resource.name}</td>
                            <td><input type="number" value="${resource.quantity}" min="0.01" step="0.01" onchange="updateResource(this)"></td>
                            <td>${resource.unit}</td>
                            <td><button onclick="removeResource(this)" class="btn btn-sm btn-danger">ลบ</button></td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
            <button type="button" class="btn btn-info" onclick="showResourceModal(this)">เพิ่มทรัพยากร</button>
        </div>
    `;
    
    courseList.appendChild(courseItem);
}

function updateCourse(input) {
    const courseItem = input.closest('.course-item');
    const courseId = courseItem.dataset.courseId;
    const amount = courseItem.querySelector('.course-amount').value;
    const price = courseItem.querySelector('.course-price').value;
    
    $.ajax({
        url: 'sql/update-course.php',
        type: 'POST',
        data: {
            order_id: currentOrderId,
            course_id: courseId,
            amount: amount,
            price: price
        },
        success: function(response) {
            console.log('Course updated successfully');
            updateTotalPrice();
        },
        error: function(xhr, status, error) {
            console.error('Error updating course:', error);
        }
    });
}

function removeCourse(button) {
    const courseItem = button.closest('.course-item');
    const courseId = courseItem.dataset.courseId;
    
    $.ajax({
        url: 'sql/remove-course.php',
        type: 'POST',
        data: {
            order_id: currentOrderId,
            course_id: courseId
        },
        success: function(response) {
            console.log('Course removed successfully');
            courseItem.remove();
            updateTotalPrice();
        },
        error: function(xhr, status, error) {
            console.error('Error removing course:', error);
        }
    });
}

function updateResource(input) {
    const row = input.closest('tr');
    const resourceId = row.dataset.resourceId;
    const quantity = input.value;
    
    $.ajax({
        url: 'sql/update-resource.php',
        type: 'POST',
        data: {
            resource_id: resourceId,
            quantity: quantity
        },
        success: function(response) {
            console.log('Resource updated successfully');
        },
        error: function(xhr, status, error) {
            console.error('Error updating resource:', error);
        }
    });
}

function removeResource(button) {
    const row = button.closest('tr');
    const resourceId = row.dataset.resourceId;
    
    $.ajax({
        url: 'sql/remove-resource.php',
        type: 'POST',
        data: {
            resource_id: resourceId
        },
        success: function(response) {
            console.log('Resource removed successfully');
            row.remove();
        },
        error: function(xhr, status, error) {
            console.error('Error removing resource:', error);
        }
    });
}

function showResourceModal(button) {
    currentCourseItem = button.closest('.course-item');
    $('#resourceModal').modal('show');
}

function addResource() {
    const type = $('#resourceType').val();
    const id = $('#resourceId').val();
    const quantity = $('#resourceQuantity').val();
    const name = $('#resourceId option:selected').text();
    const unit = ''; // ต้องดึงข้อมูลหน่วยนับจากฐานข้อมูล
    
    $.ajax({
        url: 'sql/add-resource.php',
        type: 'POST',
        data: {
            order_id: currentOrderId,
            course_id: currentCourseItem.dataset.courseId,
            resource_type: type,
            resource_id: id,
            quantity: quantity
        },
        success: function(response) {
            console.log('Resource added successfully');
            const newRow = `
                <tr data-resource-id="${response.resource_id}">
                    <td>${type}</td>
                    <td>${name}</td>
                    <td><input type="number" value="${quantity}" min="0.01" step="0.01" onchange="updateResource(this)"></td>
                    <td>${unit}</td>
                    <td><button onclick="removeResource(this)" class="btn btn-sm btn-danger">ลบ</button></td>
                </tr>
            `;
            currentCourseItem.querySelector('.resources-table tbody').insertAdjacentHTML('beforeend', newRow);
            $('#resourceModal').modal('hide');
        },
        error: function(xhr, status, error) {
            console.error('Error adding resource:', error);
        }
    });
}

function updateTotalPrice() {
    let total = 0;
    document.querySelectorAll('.course-item').forEach(item => {
        const price = parseFloat(item.querySelector('.course-price').value);
        const amount = parseInt(item.querySelector('.course-amount').value);
        total += price * amount;
    });
    document.getElementById('totalPrice').textContent = total.toFixed(2);
    
    // Update total price in database
    $.ajax({
        url: 'sql/update-total-price.php',
        type: 'POST',
        data: {
            order_id: currentOrderId,
            total_price: total
        },
        success: function(response) {
            console.log('Total price updated successfully');
        },
        error: function(xhr, status, error) {
            console.error('Error updating total price:', error);
        }
    });
}

// Load existing order when page loads
$(document).ready(function() {
    loadExistingOrder();
});

// Auto-save when changing booking datetime or payment status
$('#booking_datetime, #payment_method').on('change', function() {
    $.ajax({
        url: 'sql/update-order-details.php',
        type: 'POST',
        data: {
            order_id: currentOrderId,
            booking_datetime: $('#booking_datetime').val(),
            payment_status: $('#payment_method').val()
        },
        success: function(response) {
            console.log('Order details updated successfully');
        },
error: function(xhr, status, error) {
            console.error('Error updating order details:', error);
        }
    });
});

// Function to load resources for the modal
function loadResources(type) {
    $.ajax({
        url: 'sql/get-resources.php',
        type: 'GET',
        data: { type: type },
        success: function(response) {
            const resources = JSON.parse(response);
            const select = $('#resourceId');
            select.empty();
            select.append('<option value="">เลือกทรัพยากร</option>');
            resources.forEach(resource => {
                select.append(`<option value="${resource.id}">${resource.name}</option>`);
            });
        },
        error: function(xhr, status, error) {
            console.error('Error loading resources:', error);
        }
    });
}

// Event listener for resource type change
$('#resourceType').on('change', function() {
    loadResources($(this).val());
});

// No need for a separate save button, as everything is auto-saved


    </script>
</body>
</html>