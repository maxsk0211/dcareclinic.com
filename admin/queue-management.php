<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

function getStatusText($status) {
    switch($status) {
        case 'waiting': return 'รอดำเนินการ';
        case 'in_progress': return 'กำลังให้บริการ';
        case 'completed': return 'เสร็จสิ้น';
        case 'cancelled': return 'ยกเลิก';
        default: return $status;
    }
}

// ดึงข้อมูลคิวสำหรับวันนี้
$today = date('Y-m-d');
$sql = "SELECT sq.*, c.cus_firstname, c.cus_lastname, cb.booking_datetime 
        FROM service_queue sq
        LEFT JOIN customer c ON sq.cus_id = c.cus_id
        LEFT JOIN course_bookings cb ON sq.booking_id = cb.id
        WHERE sq.queue_date = '$today' AND sq.branch_id = {$_SESSION['branch_id']}
        ORDER BY sq.queue_time ASC";

$result = $conn->query($sql);

// ดึงข้อมูลการจองสำหรับวันนี้
$sql_bookings = "SELECT cb.id, cb.booking_datetime, c.cus_id, c.cus_firstname, c.cus_lastname 
                 FROM course_bookings cb
                 JOIN customer c ON cb.cus_id = c.cus_id
                 WHERE DATE(cb.booking_datetime) = CURDATE() AND cb.branch_id = {$_SESSION['branch_id']}
                 AND cb.id NOT IN (SELECT booking_id FROM service_queue WHERE booking_id IS NOT NULL)
                 ORDER BY cb.booking_datetime ASC";
$result_bookings = $conn->query($sql_bookings);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>จัดการคิวการให้บริการ - D Care Clinic</title>
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
    .queue-table {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    .queue-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .queue-table td, .queue-table th {
        vertical-align: middle;
    }
    .status-badge {
        padding: 0.25em 0.6em;
        font-size: 0.75em;
        font-weight: 700;
        border-radius: 0.25rem;
        text-transform: uppercase;
    }
    .status-waiting {
        background-color: #ffc107;
        color: #000;
    }
    .status-in-progress {
        background-color: #17a2b8;
        color: #fff;
    }
    .status-completed {
        background-color: #28a745;
        color: #fff;
    }
    .status-cancelled {
        background-color: #dc3545;
        color: #fff;
    }
    .action-buttons .btn {
        margin-right: 5px;
    }
    .swal2-container {
        z-index: 9999 !important;
    }
    .modal .select2-container {
        width: 100% !important;
    }

    .modal .select2-container .select2-selection--single {
        height: 38px;
        line-height: 38px;
    }

    .modal .select2-container--bootstrap-5 .select2-selection {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .modal .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 12px;
    }

    .modal .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .select2-container {
    z-index: 9999;
    }
    .select2-dropdown {
        z-index: 9999;
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
                        <h4 class="py-3 mb-4"><span class="text-muted fw-light">การจัดการคิว /</span> คิวการให้บริการวันนี้</h4>

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">รายการคิวการให้บริการ</h5>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQueueModal">
                                    <i class="ri-add-line me-1"></i> เพิ่มคิวใหม่
                                </button>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped queue-table">
                                    <thead>
                                        <tr>
                                            <th>หมายเลขคิว</th>
                                            <th>ชื่อลูกค้า</th>
                                            <th>เวลานัด</th>
                                            <th>สถานะ</th>
                                            <th>การดำเนินการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = $result->fetch_assoc()): ?>
                                        <tr data-queue-id="<?php echo $row['queue_id']; ?>">
                                            <td><?php echo $row['queue_number']; ?></td>
                                            <td><?php echo $row['cus_firstname'] . ' ' . $row['cus_lastname']; ?></td>
                                            <td><?php echo $row['booking_datetime'] ? date('H:i', strtotime($row['booking_datetime'])) : 'ไม่ได้นัด'; ?></td>
                                            <td><span class="queue-status status-badge status-<?php echo $row['service_status']; ?>"><?php echo getStatusText($row['service_status']); ?></span></td>
                                            <td class="action-buttons">
                                                <?php if($row['service_status'] != 'completed' && $row['service_status'] != 'cancelled'): ?>
                                                    <button class="btn btn-sm btn-primary" onclick="updateStatus(<?php echo $row['queue_id']; ?>, 'in_progress')">เริ่มให้บริการ</button>
                                                    <button class="btn btn-sm btn-success" onclick="updateStatus(<?php echo $row['queue_id']; ?>, 'completed')">เสร็จสิ้น</button>
                                                <?php endif; ?>
                                                <?php if($row['service_status'] != 'cancelled'): ?>
                                                    <button class="btn btn-sm btn-danger" onclick="updateStatus(<?php echo $row['queue_id']; ?>, 'cancelled')">ยกเลิก</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
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

    <!-- Modal เพิ่มคิวใหม่ -->
<div class="modal fade" id="addQueueModal" tabindex="-1" aria-labelledby="addQueueModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQueueModalLabel">เพิ่มคิวใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addQueueForm">
                    <div class="mb-3">
                        <label for="booking_id" class="form-label">เลือกการจอง</label>
                        <select class="form-select" id="booking_id" name="booking_id">
                            <option value="">เลือกการจอง</option>
                            <?php while($row = $result_bookings->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>" data-cus-id="<?php echo $row['cus_id']; ?>">
                                    <?php echo $row['cus_firstname'] . ' ' . $row['cus_lastname'] . ' - ' . date('H:i', strtotime($row['booking_datetime'])); ?>
                                </option>
                            <?php endwhile; ?>
                            <option value="walk_in">Walk-in (ไม่มีการจอง)</option>
                        </select>
                    </div>
                    <div id="walkInFields" style="display: none;">
                        <div class="mb-3">
                            <label for="cus_id" class="form-label">เลือกลูกค้า</label>
                            <select class="form-select" id="cus_id" name="cus_id">
                                <option value="">เลือกลูกค้า</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="queue_time" class="form-label">เวลาคิว</label>
                            <input type="time" class="form-control" id="queue_time" name="queue_time" step="60">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">หมายเหตุ</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" onclick="addQueue()">บันทึกคิว</button>
            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>


    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <!-- <script src="../assets/js/tables-datatables-basic.js"></script> -->
    <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- Page JS -->
<script>
function updateStatus(queueId, newStatus) {
    $.ajax({
        url: 'sql/update-queue-status.php',
        type: 'POST',
        data: {
            queue_id: queueId,
            status: newStatus
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // อัพเดทสถานะในตารางโดยไม่ต้องรีโหลดหน้า
                const statusCell = $(`tr[data-queue-id="${queueId}"] .queue-status`);
                statusCell.removeClass().addClass(`queue-status status-badge status-${newStatus}`);
                statusCell.text(getStatusText(newStatus));
                
                // อัพเดทปุ่มการดำเนินการ
                updateActionButtons(queueId, newStatus);

                // แสดงข้อความแจ้งเตือน
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'อัพเดทสถานะสำเร็จ',
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                console.error('Server response:', response);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถอัพเดทสถานะได้'
                });
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX error:', textStatus, errorThrown);
            console.log('Response Text:', jqXHR.responseText);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
            });
        }
    });
}

function getStatusText(status) {
    switch(status) {
        case 'waiting': return 'รอดำเนินการ';
        case 'in_progress': return 'กำลังให้บริการ';
        case 'completed': return 'เสร็จสิ้น';
        case 'cancelled': return 'ยกเลิก';
        default: return status;
    }
}

function updateActionButtons(queueId, currentStatus) {
    const actionCell = $(`tr[data-queue-id="${queueId}"] .action-buttons`);
    let buttons = '';

    if (currentStatus !== 'completed' && currentStatus !== 'cancelled') {
        buttons += `<button class="btn btn-sm btn-primary" onclick="updateStatus(${queueId}, 'in_progress')">เริ่มให้บริการ</button>`;
        buttons += `<button class="btn btn-sm btn-success" onclick="updateStatus(${queueId}, 'completed')">เสร็จสิ้น</button>`;
    }
    
    if (currentStatus !== 'cancelled') {
        buttons += `<button class="btn btn-sm btn-danger" onclick="updateStatus(${queueId}, 'cancelled')">ยกเลิก</button>`;
    }

    actionCell.html(buttons);
}

function showAlert(type, message) {
    Swal.fire({
        icon: type,
        title: type === 'success' ? 'สำเร็จ' : 'ข้อผิดพลาด',
        text: message,
        timer: 2000,
        showConfirmButton: false
    });
}

 function addQueue() {
    const formData = new FormData(document.getElementById('addQueueForm'));
    
    $.ajax({
        url: 'sql/add-queue-process.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            $('#addQueueModal').modal('hide');
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'เพิ่มคิวสำเร็จ',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    $('#addQueueModal').modal('hide');
                    refreshQueueTable();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message
                });
            }
        },
        error: function() {
            $('#addQueueModal').modal('hide');
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
            });
        }
    });
}

function refreshQueueTable() {
    $.ajax({
        url: 'sql/get-queue-data.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            let tableBody = '';
            data.forEach(function(row) {
                tableBody += `
                    <tr data-queue-id="${row.queue_id}">
                        <td>${row.queue_number}</td>
                        <td>${row.cus_firstname} ${row.cus_lastname}</td>
                        <td>${row.booking_datetime ? new Date(row.booking_datetime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : 'ไม่ได้นัด'}</td>
                        <td><span class="queue-status status-badge status-${row.service_status}">${getStatusText(row.service_status)}</span></td>
                        <td class="action-buttons">
                            ${row.service_status !== 'completed' && row.service_status !== 'cancelled' ?
                                `<button class="btn btn-sm btn-primary" onclick="updateStatus(${row.queue_id}, 'in_progress')">เริ่มให้บริการ</button>
                                 <button class="btn btn-sm btn-success" onclick="updateStatus(${row.queue_id}, 'completed')">เสร็จสิ้น</button>` : ''}
                            ${row.service_status !== 'cancelled' ?
                                `<button class="btn btn-sm btn-danger" onclick="updateStatus(${row.queue_id}, 'cancelled')">ยกเลิก</button>` : ''}
                        </td>
                    </tr>
                `;
            });
            $('.queue-table tbody').html(tableBody);
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถโหลดข้อมูลคิวได้'
            });
        }
    });
}

$(document).ready(function() {
    console.log('Document ready');
    $('#cus_id').on('select2:open', function (e) {
        console.log('Select2 opened');
    });

    $('#cus_id').on('select2:closing', function (e) {
        console.log('Select2 closing');
    });

    $('#cus_id').on('select2:select', function (e) {
        console.log('Select2 selected:', e.params.data);
    });
    // เริ่มต้น Select2 สำหรับการเลือกลูกค้า
    $('#cus_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'เลือกลูกค้า',
        width: '100%', // เพิ่มบรรทัดนี้
        dropdownParent: $('#addQueueModal'), // เพิ่มบรรทัดนี้
        ajax: {
            url: 'sql/get-customers.php',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    search: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        minimumInputLength: 1,
        language: {
            inputTooShort: function() {
                return "กรุณาพิมพ์อย่างน้อย 1 ตัวอักษร";
            },
            noResults: function() {
                return "ไม่พบข้อมูลลูกค้า";
            },
            searching: function() {
                return "กำลังค้นหา...";
            }
        }
    });

    console.log('Select2 initialized');

    // เพิ่ม event listener สำหรับการเลือกลูกค้า
    $('#cus_id').on('select2:select', function (e) {
        var data = e.params.data;
        console.log('เลือกลูกค้า:', data);
    });

    // แสดง/ซ่อนฟิลด์ Walk-in
    $('#booking_id').change(function() {
        if ($(this).val() === 'walk_in') {
            $('#walkInFields').show();
            $('#cus_id').prop('required', true);
            $('#queue_time').prop('required', true);
        } else {
            $('#walkInFields').hide();
            $('#cus_id').prop('required', false);
            $('#queue_time').prop('required', false);
        }
    });

    // รีเฟรชตารางทุก 1 นาที
    setInterval(refreshQueueTable, 60000);
});
</script>
</body>
</html>