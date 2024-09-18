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

// ดึงข้อมูลการจองสำหรับวันนี้ที่ยังไม่ได้ถูกเพิ่มในคิว
$sql_bookings = "SELECT cb.id, cb.booking_datetime, c.cus_id, c.cus_firstname, c.cus_lastname 
                 FROM course_bookings cb
                 JOIN customer c ON cb.cus_id = c.cus_id
                 WHERE DATE(cb.booking_datetime) = CURDATE() 
                 AND cb.branch_id = {$_SESSION['branch_id']}
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
        height: 38px !important;
        line-height: 38px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    .select2-search__field {
        width: 100% !important;
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
    .select2-result-customer__name {
        font-weight: bold;
        color: #333;
        word-wrap: break-word;
    }
    .current-datetime {
        background: linear-gradient(to right, #f6d365 0%, #fda085 100%);
        border-radius: 10px;
        padding: 10px 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    #currentDate {
        color: #333;
        font-size: 1.1rem;
    }

    #currentTime {
        color: #fff;
        font-size: 1.8rem;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }
    .queue-table .queue-number {
        font-size: 18px;
        font-weight: 600;
    }

    .queue-table .customer-name {
        font-size: 17px;
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
                        <div class="current-datetime text-end">
                            <div id="currentDate" class="fs-5 fw-bold"></div>
                            <div id="currentTime" class="fs-3 fw-bold text-primary"></div>
                        </div>
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">รายการคิวการให้บริการ</h5>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQueueModal">
                                    <i class="ri-add-line me-1"></i> เพิ่มคิวใหม่
                                </button>
                            </div>
                            <div class="card-body">
                                <!-- ในส่วนของตาราง -->
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
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while($row = $result->fetch_assoc()): ?>
                                        <tr data-queue-id="<?php echo $row['queue_id']; ?>">
                                            <td class="queue-number"><?php echo $row['queue_number']; ?></td>
                                            <td class="customer-name"><?php echo $row['cus_firstname'] . ' ' . $row['cus_lastname']; ?></td>
                                            <td><?php echo $row['booking_datetime'] ? date('H:i', strtotime($row['booking_datetime'])) : ($row['queue_time'] ? date('H:i', strtotime($row['queue_time'])) : 'ไม่ระบุ'); ?></td>
                                            <td><span class="queue-status status-badge status-<?php echo $row['service_status']; ?>"><?php echo getStatusText($row['service_status']); ?></span></td>
                                            <td class="action-buttons">
                                                <?php if($row['service_status'] == 'waiting'): ?>
                                                    <button class="btn btn-sm btn-primary" onclick="updateStatus(<?php echo $row['queue_id']; ?>, 'in_progress')">เริ่มให้บริการ</button>
                                                <?php elseif($row['service_status'] == 'in_progress'): ?>
                                                    <a href="opd.php?queue_id=<?php echo $row['queue_id']; ?>" 
                                                       id="opd-btn-<?php echo $row['queue_id']; ?>" 
                                                       class="btn btn-sm btn-info opd-btn" 
                                                       data-queue-id="<?php echo $row['queue_id']; ?>">OPD</a>
                                                    <a href="service.php?queue_id=<?php echo $row['queue_id']; ?>" 
                                                       id="service-btn-<?php echo $row['queue_id']; ?>" 
                                                       class="btn btn-sm btn-info service-btn">บริการ</a>
                                                <?php elseif($row['service_status'] == 'completed'): ?>
                                                    <button class="btn btn-sm btn-warning" onclick="revertStatus(<?php echo $row['queue_id']; ?>)">ยกเลิกสถานะ</button>
                                                <?php endif; ?>
                                                <?php if($row['service_status'] != 'cancelled' && $row['service_status'] != 'completed'): ?>
                                                    <button class="btn btn-sm btn-danger" onclick="confirmCancelQueue(<?php echo $row['queue_id']; ?>)">ยกเลิก</button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">ยังไม่มีคิวในวันนี้</td>
                                        </tr>
                                    <?php endif; ?>
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
                        <label for="booking_type" class="form-label">ประเภทการจอง</label>
                        <select class="form-select" id="booking_type" name="booking_type" required>
                            <option value="booked">การจองล่วงหน้า</option>
                            <option value="walk_in">Walk-in</option>
                        </select>
                    </div>
                    <div id="bookedFields">
                        <div class="mb-3">
                            <label for="booking_id" class="form-label">เลือกการจอง</label>
                            <select class="form-select" id="booking_id" name="booking_id" required>
                                <option value="">เลือกการจอง</option>
                                <!-- ตัวเลือกการจองจะถูกเพิ่มด้วย JavaScript -->
                            </select>
                        </div>
                    </div>
                    <div id="walkInFields" style="display: none;">
                        <div class="mb-3">
                            <label for="cus_id" class="form-label">ค้นหาลูกค้า</label>
                            <select class="form-select" id="cus_id" name="cus_id" required>
                                <option value="">ค้นหาลูกค้าด้วย ชื่อ, เบอร์โทร, HN หรือเลขบัตรประชาชน</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="queue_time" class="form-label">เวลาคิว</label>
                            <input type="time" class="form-control" id="queue_time" name="queue_time" required>
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
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
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

    <!-- Core JS -->

    <!-- Page JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/th.js"></script>
    <!-- Page JS -->

<script>
$(document).ready(function() {
    // เรียกใช้ checkOPDStatus สำหรับคิวที่มีสถานะ 'in_progress'
    $('.opd-btn').each(function() {
        var queueId = $(this).data('queue-id');
        checkOPDStatus(queueId);
    });

    var table = $('.datatables-bookings').DataTable({
        displayLength: 10,
        lengthMenu: [ 10, 25, 50, 75, 100],
        buttons: [],
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal({
                    header: function(row) {
                        var data = row.data();
                        return 'รายละเอียดการจองของ ' + data[2];
                    }
                }),
                type: 'column',
                renderer: function(api, rowIdx, columns) {
                    var data = $.map(columns, function(col, i) {
                        return col.title !== ''
                            ? '<tr data-dt-row="' +
                                col.rowIndex +
                                '" data-dt-column="' +
                                col.columnIndex +
                                '">' +
                                '<td>' +
                                col.title +
                                ':' +
                                '</td> ' +
                                '<td>' +
                                col.data +
                                '</td>' +
                                '</tr>'
                            : '';
                    }).join('');

                    return data ? $('<table class="table"/><tbody />').append(data) : false;
                }
            }
        },
        createdRow: function(row, data, dataIndex) {
            $(row).addClass('clickable-row');
            $(row).attr('data-cus-id', data[1]);
        }
    });

    $('.datatables-bookings tbody').on('click', 'tr', function(e) {
        if (!$(e.target).closest('.dropdown-toggle, .dropdown-item').length) {
            var customerId = $(this).data('customer-id');
            if (customerId) {
                window.location.href = 'customer-detail.php?id=' + customerId;
            }
        }
    });

    $('.dropdown-toggle, .dropdown-item').on('click', function(e) {
        e.stopPropagation();
    });

    // สำหรับการเลือกลูกค้า
    $('#cus_id').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#addQueueModal'),
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
        placeholder: 'ค้นหาลูกค้า...',
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
    }).on('select2:open', function(e) {
        setTimeout(function() {
            $('.select2-search__field').get(0).focus();
        }, 10);
    });

    // สำหรับการเลือกการจอง
    $('#booking_id').select2({
        theme: 'bootstrap-5',
        placeholder: 'เลือกการจอง'
    }).on('select2:open', function (e) {
        console.log('Booking Select2 opened');
    });

    // เปลี่ยนประเภทการจอง
    $('#booking_type').change(function() {
        if ($(this).val() === 'walk_in') {
            $('#bookedFields').hide();
            $('#walkInFields').show();
            setCurrentTime();
        } else {
            $('#bookedFields').show();
            $('#walkInFields').hide();
        }
    });

    // เรียกใช้ฟังก์ชันเมื่อเปิด Modal
    $('#addQueueModal').on('show.bs.modal', function () {
        if ($('#booking_type').val() === 'walk_in') {
            setCurrentTime();
        }
    });

    updateAllOPDButtons();
    setInterval(updateAllOPDButtons, 30000);

    // อัพเดทเวลาทุกวินาที
    setInterval(updateDateTime, 1000);

    // เรียกใช้ฟังก์ชันครั้งแรกเพื่อแสดงเวลาทันที
    updateDateTime();

    // เรียกใช้ refreshQueueTable ทุก 30 วินาที
    setInterval(refreshQueueTable, 30000);
});

function checkOPDStatus(queueId) {
    $.ajax({
        url: 'sql/check-opd-status.php',
        type: 'GET',
        data: { queue_id: queueId },
        dataType: 'json',
        success: function(response) {
            var opdBtn = $('#opd-btn-' + queueId);
            var serviceBtn = $('#service-btn-' + queueId);
            console.log("OPD Status Response for queue " + queueId + ":", response);
            if (response.has_opd) {
                if (response.opd_status === 1) {
                    opdBtn.removeClass('btn-info').addClass('btn-success');
                    // serviceBtn.removeClass('d-none');
                    // serviceBtn.show();
                } else {
                    opdBtn.removeClass('btn-success').addClass('btn-info');
                    // serviceBtn.addClass('d-none');
                    // serviceBtn.hide();
                }
            } else {
                opdBtn.removeClass('btn-success').addClass('btn-info');
                // serviceBtn.addClass('d-none');
                // serviceBtn.hide();
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to check OPD status for queue ID:', queueId, 'Error:', error);
        }
    });
}

function updateAllOPDButtons() {
    $('.opd-btn').each(function() {
        var queueId = $(this).data('queue-id');
        checkOPDStatus(queueId);
    });
}

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
                updateQueueRow(queueId, newStatus);
                checkOPDStatus(queueId);
                
                let message = 'อัพเดทสถานะสำเร็จ';
                if (newStatus === 'cancelled') {
                    message = 'ยกเลิกคิวสำเร็จ';
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: message,
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

function updateQueueRow(queueId, newStatus) {
    const row = $(`tr[data-queue-id="${queueId}"]`);
    if (row.length) {
        const statusCell = row.find('.queue-status');
        statusCell.removeClass().addClass(`queue-status status-badge status-${newStatus}`);
        statusCell.text(getStatusText(newStatus));
        
        const actionCell = row.find('.action-buttons');
        let actionButtons = '';
        if (newStatus === 'waiting') {
            actionButtons = `<button class="btn btn-sm btn-primary" onclick="updateStatus(${queueId}, 'in_progress')">เริ่มให้บริการ</button>`;
        } else if (newStatus === 'in_progress') {
            actionButtons = `
                <a href="opd.php?queue_id=${queueId}" id="opd-btn-${queueId}" class="btn btn-sm btn-info opd-btn" data-queue-id="${queueId}">OPD</a>
                <a href="service.php?queue_id=${queueId}" id="service-btn-${queueId}" class="btn btn-sm btn-info service-btn">บริการ</a>
            `;
        } else if (newStatus === 'completed') {
            actionButtons = `<button class="btn btn-sm btn-warning" onclick="revertStatus(${queueId})">ยกเลิกสถานะ</button>`;
        }
        
        if (newStatus !== 'cancelled' && newStatus !== 'completed') {
            actionButtons += `<button class="btn btn-sm btn-danger" onclick="confirmCancelQueue(${queueId})">ยกเลิก</button>`;
        }
        
        actionCell.html(actionButtons);

        if (newStatus === 'in_progress') {
            checkOPDStatus(queueId);
        }
    }
}
function confirmCancelQueue(queueId) {
    Swal.fire({
        title: 'ยืนยันการยกเลิก?',
        text: "คุณแน่ใจหรือไม่ที่จะยกเลิกคิวนี้?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ยกเลิก!',
        cancelButtonText: 'ไม่, ยกเลิกการดำเนินการ',
          customClass: {
            confirmButton: 'btn btn-danger me-1 waves-effect waves-light',
            cancelButton: 'btn btn-outline-secondary waves-effect'
          },
              buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            updateStatus(queueId, 'cancelled');
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

function showOrderDetails(orderId) {
    $.ajax({
        url: 'sql/get-order-details.php',
        type: 'GET',
        data: { order_id: orderId },
        success: function(response) {
            $('#orderDetailsContent').html(response);
            $('#orderDetailsModal').modal('show');
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถโหลดข้อมูลรายละเอียดการสั่งซื้อได้',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false
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
            if (data.length > 0) {
                data.forEach(function(row) {
                    let appointmentTime = row.booking_datetime ? new Date(row.booking_datetime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false}) :
                                          (row.queue_time ? new Date('1970-01-01T' + row.queue_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', hour12: false}) : 'ไม่ระบุ');
                    let actionButtons = '';
                    if (row.service_status == 'waiting') {
                        actionButtons = `<button class="btn btn-sm btn-primary" onclick="updateStatus(${row.queue_id}, 'in_progress')">เริ่มให้บริการ</button>`;
                    } else if (row.service_status == 'in_progress') {
                        actionButtons = `
                            <a href="opd.php?queue_id=${row.queue_id}" id="opd-btn-${row.queue_id}" class="btn btn-sm btn-info opd-btn" data-queue-id="${row.queue_id}">OPD</a>
                            <a href="service.php?queue_id=${row.queue_id}" id="service-btn-${row.queue_id}" class="btn btn-sm btn-info service-btn">บริการ</a>
                        `;
                    } else if (row.service_status == 'completed') {
                        actionButtons = `<button class="btn btn-sm btn-warning" onclick="revertStatus(${row.queue_id})">ยกเลิกสถานะ</button>`;
                    }
                    
                    if (row.service_status != 'cancelled' && row.service_status != 'completed') {
                        actionButtons += `<button class="btn btn-sm btn-danger" onclick="confirmCancelQueue(${row.queue_id})">ยกเลิก</button>`;
                    }
                    
                    tableBody += `
                        <tr data-queue-id="${row.queue_id}">
                            <td>${row.queue_number}</td>
                            <td>${row.cus_firstname} ${row.cus_lastname}</td>
                            <td>${appointmentTime}</td>
                            <td><span class="queue-status status-badge status-${row.service_status}">${getStatusText(row.service_status)}</span></td>
                            <td class="action-buttons">
                                ${actionButtons}
                            </td>
                        </tr>
                    `;
                });
            } else {
                tableBody = '<tr><td colspan="5" class="text-center">ยังไม่มีคิวในวันนี้</td></tr>';
            }
            $('.queue-table tbody').html(tableBody);
            data.forEach(function(row) {
                if (row.service_status == 'in_progress') {
                    checkOPDStatus(row.queue_id);
                }
            });
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

function updateDateTime() {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const dateString = now.toLocaleDateString('th-TH', options);
    const timeString = now.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

    document.getElementById('currentDate').textContent = dateString;
    document.getElementById('currentTime').textContent = timeString;
}

function loadBookings() {
    $.ajax({
        url: 'sql/get-available-bookings.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            var select = $('#booking_id');
            select.empty();
            select.append('<option value="">เลือกการจอง</option>');
            $.each(data, function(index, booking) {
                select.append('<option value="' + booking.id + '">' + booking.cus_firstname + ' ' + booking.cus_lastname + ' - ' + booking.time + '</option>');
            });
        },
        error: function() {
            console.error('ไม่สามารถโหลดข้อมูลการจองได้');
        }
    });
}

function addQueue() {
    var formData = new FormData(document.getElementById('addQueueForm'));
    
    $.ajax({
        url: 'sql/add-queue-process.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
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
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
            });
        }
    });
}
function revertStatus(queueId) {
    Swal.fire({
        title: 'ยืนยันการยกเลิกสถานะ?',
        text: "คุณต้องการยกเลิกสถานะ 'เสร็จสิ้น' และกลับไปเป็น 'กำลังให้บริการ' ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, ยกเลิกสถานะ',
        cancelButtonText: 'ไม่, ยกเลิกการดำเนินการ',
        reverseButtons: true,
        customClass: {
            confirmButton: 'btn btn-success me-1',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            updateStatus(queueId, 'in_progress');
        }
    });
}

function resetAddQueueModal() {
    $('#addQueueForm')[0].reset();
    $('#booking_type').val('booked').trigger('change');
    $('#booking_id').val(null).trigger('change');
    $('#cus_id').val(null).trigger('change');
    if ($('#booking_type').val() === 'walk_in') {
        setCurrentTime();
    }
}

$('#addQueueModal').on('show.bs.modal', function (e) {
    resetAddQueueModal();
    loadBookings();
});

$('#addQueueModal').on('hidden.bs.modal', function (e) {
    resetAddQueueModal();
});

$('#saveQueueBtn').on('click', function() {
    addQueue();
});

function updateCurrentTime() {
    if ($('#booking_type').val() === 'walk_in' && $('#addQueueModal').is(':visible')) {
        setCurrentTime();
    }
}

setInterval(updateCurrentTime, 60000);

$('#queue_time').on('change', function() {
    var timeValue = $(this).val();
    if (timeValue) {
        var [hours, minutes] = timeValue.split(':');
        hours = Math.min(Math.max(parseInt(hours, 10), 0), 23);
        minutes = Math.min(Math.max(parseInt(minutes, 10), 0), 59);
        $(this).val(hours.toString().padStart(2, '0') + ':' + minutes.toString().padStart(2, '0'));
    }
});

function setCurrentTime() {
    var now = new Date();
    var hours = now.getHours().toString().padStart(2, '0');
    var minutes = now.getMinutes().toString().padStart(2, '0');
    $('#queue_time').val(hours + ':' + minutes);
}
</script>
</body>
</html>