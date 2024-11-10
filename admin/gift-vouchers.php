<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';
?>

<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="horizontal-menu-template-no-customizer-starter"
  data-style="light">
<head>
    <!-- เหมือน bill.php -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>จัดการบัตรกํานัล - D Care Clinic</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />
    <!-- <link rel="stylesheet" href="../assets/vendor/fonts/flag-icons.css" /> -->

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
    <!-- sweet Alerts 2 -->
    <link rel="stylesheet" href="../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/sweetalert2/sweetalert2.css" />

    <!-- datatables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css"> 



    <style>
    .card {
        margin-bottom: 1.5rem;
    }

    .statistics-card {
        padding: 1.5rem;
        border-radius: 0.5rem;
        color: white;
    }

    .bg-gradient-info {
        background: linear-gradient(45deg, #3f51b5, #2196f3);
    }

    .bg-gradient-success {
        background: linear-gradient(45deg, #43a047, #8bc34a);
    }

    .bg-gradient-warning {
        background: linear-gradient(45deg, #fb8c00, #ffb74d);
    }

    .bg-gradient-danger {
        background: linear-gradient(45deg, #e53935, #ff5252);
    }

.status-badge {
    padding: 0.35em 0.65em;
    border-radius: 0.25rem;
    font-weight: 600;
    font-size: 0.85em;
    text-align: center;
    display: inline-block;
}

.status-unused {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.status-used {
    background-color: #ffebee;
    color: #c62828;
}

.status-expired {
    background-color: #eceff1;
    color: #546e7a;
}

    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }


.status-partial {
    background-color: #fff3e0;
    color: #ef6c00;
}

.status-unknown {
    background-color: #f5f5f5;
    color: #757575;
}

/* เพิ่ม CSS ใหม่สำหรับ Modal รายละเอียดบัตรกำนัล */
.voucher-detail-modal .modal-content {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.voucher-detail-modal .modal-header {
    background: linear-gradient(45deg, #2196F3, #1976D2);
    color: white;
    border-radius: 1rem 1rem 0 0;
    padding: 1.5rem;
}

.voucher-detail-modal .modal-body {
    padding: 2rem;
}

.voucher-detail-modal .modal-footer {
    border-top: 1px solid #eee;
    padding: 1rem 2rem;
}

.voucher-info-card {
    background: #f8f9fa;
    border-radius: 0.75rem;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid rgba(0,0,0,0.08);
}

.voucher-info-title {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.voucher-info-value {
    font-size: 1rem;
    color: #2d3436;
    margin-bottom: 0;
}

.voucher-code {
    background: #e3f2fd;
    color: #1565c0;
    padding: 1rem;
    border-radius: 0.5rem;
    font-family: monospace;
    font-size: 1.25rem;
    font-weight: 600;
    text-align: center;
    letter-spacing: 2px;
    margin-bottom: 1.5rem;
}

.voucher-amount {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 1rem;
    border-radius: 0.5rem;
    text-align: center;
    margin-bottom: 1.5rem;
}

.voucher-amount.percent {
    background: #fff3e0;
    color: #ef6c00;
}

.usage-history {
    margin-top: 1.5rem;
}

.usage-history-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.usage-history-table th {
    background: #f8f9fa;
    padding: 0.75rem;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.usage-history-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #dee2e6;
}

.alert-box {
    padding: 1rem;
    border-radius: 0.5rem;
    margin: 1rem 0;
}

.alert-box.info {
    background: #e3f2fd;
    color: #1565c0;
    border: 1px solid #bbdefb;
}

.alert-box.warning {
    background: #fff3e0;
    color: #ef6c00;
    border: 1px solid #ffe0b2;
}
    </style>
    <!-- Page JS -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
</head>

<body>
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">
            <?php include 'navbar.php'; ?>
            <div class="layout-page">
                <div class="content-wrapper">
                    <?php include 'menu.php'; ?>
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <!-- สถิติภาพรวม -->
                            <div class="col-xl-3 col-md-6">
                                <div class="card statistics-card bg-gradient-info">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">บัตรกำนัลทั้งหมด</h5>
                                        <h3 class="text-white mb-0" id="totalVouchers">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card statistics-card bg-gradient-success">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">พร้อมใช้งาน</h5>
                                        <h3 class="text-white mb-0" id="unusedVouchers">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card statistics-card bg-gradient-warning">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">ใช้แล้ว</h5>
                                        <h3 class="text-white mb-0" id="usedVouchers">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card statistics-card bg-gradient-danger">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">หมดอายุ</h5>
                                        <h3 class="text-white mb-0" id="expiredVouchers">0</h3>
                                    </div>
                                </div>
                            </div>

                            <!-- ตารางบัตรกำนัล -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">รายการบัตรกำนัล</h5>
                                        <button type="button" class="btn btn-primary" onclick="showAddVoucherModal()">
                                            <i class="ri-add-circle-line"></i> สร้างบัตรกำนัลใหม่
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table id="vouchersTable" class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>รหัสบัตรกำนัล</th>
                                                        <th>มูลค่า</th>
                                                        <th>วันที่สร้าง</th>
                                                        <th>วันหมดอายุ</th>
                                                        <th>สถานะ</th>
                                                        <th>ผู้สร้าง</th>
                                                        <th>วันที่ใช้</th>
                                                        <th>เลขที่บิล</th>
                                                        <th>จัดการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- ข้อมูลจะถูกเพิ่มด้วย DataTables -->
                                                </tbody>
                                            </table>
                                        </div>
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

    <!-- Modal สร้างบัตรกำนัลใหม่ -->
    <div class="modal fade" id="addVoucherModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">สร้างบัตรกำนัลใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addVoucherForm">
                        <div class="mb-3">
                            <label class="form-label">ประเภทส่วนลด</label>
                            <select class="form-select" id="discount_type" name="discount_type" required>
                                <option value="fixed">ลดเป็นจำนวนเงิน</option>
                                <option value="percent">ลดเป็นเปอร์เซ็นต์</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">มูลค่าส่วนลด</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="amount" name="amount" required min="1" step="0.01">
                                <span class="input-group-text" id="amountSuffix">บาท</span>
                            </div>
                        </div>
                        <div class="mb-3" id="maxDiscountField" style="display: none;">
                            <label class="form-label">จำนวนเงินส่วนลดสูงสุด</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="max_discount" name="max_discount" min="0" step="0.01">
                                <span class="input-group-text">บาท</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">วันหมดอายุ</label>
                            <input type="date" class="form-control" id="expireDate" name="expire_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" onclick="saveVoucher()">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal แสดงรายละเอียด -->
    <div class="modal fade" id="viewVoucherModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">รายละเอียดบัตรกำนัล</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="voucherDetails">
                    <!-- รายละเอียดจะถูกเพิ่มด้วย JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <!-- <button type="button" class="btn btn-primary" onclick="printVoucher(${data.voucher_id})">พิมพ์บัตรกำนัล</button> -->
                </div>
            </div>
        </div>
    </div>

<!-- Modal ประวัติการใช้งาน -->
<div class="modal fade" id="usageHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ประวัติการใช้งานบัตรกำนัล</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="voucher-info mb-3">
                    <!-- ข้อมูลบัตรกำนัลจะถูกเพิ่มด้วย JavaScript -->
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>วันที่</th>
                                <th>เลขที่บิล</th>
                                <th class="text-end">จำนวนเงิน</th>
                                <th class="text-end">ยอดคงเหลือ</th>
                                <th>สาขา</th>
                            </tr>
                        </thead>
                        <tbody id="usageHistoryTable">
                            <!-- ประวัติการใช้งานจะถูกเพิ่มด้วย JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

    <!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- datatables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.dataTables.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> -->
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js"></script>



    <script>
        let vouchersTable; // ประกาศตัวแปรในระดับ global
$(document).ready(function() {
    // กำหนดค่าเริ่มต้นสำหรับวันหมดอายุ (3 เดือนนับจากวันนี้)
    const defaultExpireDate = new Date();
    defaultExpireDate.setMonth(defaultExpireDate.getMonth() + 3);
    $('#expireDate').val(defaultExpireDate.toISOString().split('T')[0]);


    // DataTable initialization
    vouchersTable = $('#vouchersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'sql/get-vouchers.php',
            type: 'GET',
            data: function(d) {
                return {
                    ...d,
                    page: Math.floor(d.start / d.length) + 1,
                    per_page: d.length
                };
            }
        },
        columns: [
            { data: 'voucher_code' },
            { 
                // คอลัมน์มูลค่า/เปอร์เซ็นต์
                data: null,
                render: function(data) {
                    let html = '';
                    if (data.discount_type === 'percent') {
                        html = `<div class="fw-bold fs-5">${data.amount}%</div>`;
                        if (data.max_discount) {
                            html += `<small class="text-muted">สูงสุด ${formatCurrency(data.max_discount)}</small>`;
                        }
                    } else {
                        html = `<div class="fw-bold fs-5">${formatCurrency(data.amount)}</div>`;
                        if (data.remaining_amount !== null) {
                            if (data.remaining_amount > 0) {
                                html += `<small class="text-success">คงเหลือ ${formatCurrency(data.remaining_amount)}</small>`;
                            } else {
                                html += '<small class="text-danger">ใช้หมดแล้ว</small>';
                            }
                        }
                    }
                    return html;
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    return data ? formatDatetime(data) : '-';
                }
            },
            { 
                data: 'expire_date',
                render: function(data) {
                    return data ? formatDate(data) : '-';
                }
            },
            // ในส่วนของ DataTables columns
            {
                data: null,
                render: function(data) {
                    // console.log('Voucher data:', data);
                    let statusClass = '';
                    let statusText = '';

                    if (data.status === 'expired' || new Date(data.expire_date) < new Date()) {
                        statusClass = 'status-expired';
                        statusText = 'หมดอายุ';
                    } else if (data.discount_type === 'fixed') {
                        // บัตรแบบจำนวนเงิน
                        if (!data.customer_id) {
                            statusClass = 'status-unused';
                            statusText = 'พร้อมใช้งาน';
                        } else if (parseFloat(data.remaining_amount) === parseFloat(data.amount)) {
                            statusClass = 'status-unused';
                            statusText = 'พร้อมใช้งาน';
                        } else if (parseFloat(data.remaining_amount) > 0) {
                            statusClass = 'status-partial';
                            statusText = 'ใช้บางส่วน';
                        } else {
                            statusClass = 'status-used';
                            statusText = 'ใช้หมดแล้ว';
                        }
                    } else {
                        // บัตรแบบเปอร์เซ็นต์
                        if (data.customer_id) {
                            statusClass = 'status-used';
                            statusText = 'ใช้แล้ว';
                        } else {
                            statusClass = 'status-unused';
                            statusText = 'พร้อมใช้งาน';
                        }
                    }

                    let html = `<span class="status-badge ${statusClass}">${statusText}</span>`;
                    
                    // แสดงจำนวนเงินคงเหลือสำหรับบัตรแบบ fixed amount
                    if (data.discount_type === 'fixed' && data.customer_id) {
                        // html += `<div class="mt-1">
                        //     <small class="${parseFloat(data.remaining_amount) > 0 ? 'text-success' : 'text-danger'}">
                        //         คงเหลือ: ${formatCurrency(data.remaining_amount)}
                        //     </small>
                        // </div>`;
                    }

                    // แสดงชื่อผู้ใช้
                    if (data.customer_name) {
                        html += `<div class="mt-1">
                            <small class="text-muted">
                                <i class="ri-user-line"></i> ${data.customer_name}
                            </small>
                        </div>`;
                    }

                    return html;
                }
            },
            { 
                // ผู้สร้าง
                data: 'creator_name',
                render: function(data) {
                    return data || '-';
                }
            },
            { 
                // วันที่ใช้
                data: null,
                render: function(data) {
                    if (data.first_used_at) {
                        return `<div>${formatDatetime(data.first_used_at)}</div>
                                <small class="text-muted">ครั้งแรก</small>`;
                    }
                    return '-';
                }
            },
            { 
                // Order ID / ดูประวัติ
                data: null,
                render: function(data) {
                    if (data.discount_type === 'percent') {
                        // บัตรแบบเปอร์เซ็นต์ แสดง Order ID
                        return data.used_in_order ? 
                            `ORDER-${String(data.used_in_order).padStart(6, '0')}` : 
                            '-';
                    } else {
                        // บัตรแบบจำนวนเงิน แสดงปุ่มดูประวัติ
                        if (data.customer_id) {
                            return `<button class="btn btn-sm btn-outline-info" 
                                        onclick="viewUsageHistory(${data.voucher_id})">
                                        <i class="ri-history-line"></i> ดูประวัติ
                                    </button>`;
                        }
                        return '-';
                    }
                }
            },
            {
                // ปุ่มจัดการ
                data: null,
                orderable: false,
                render: function(data) {
                    let buttons = `
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-info" 
                                    onclick="viewVoucher(${data.voucher_id})" 
                                    data-bs-toggle="tooltip" 
                                    title="ดูรายละเอียด">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button type="button" class="btn btn-primary" onclick="printVoucher(${data.voucher_id})" data-id="${data.voucher_id}">
                                <i class="ri-printer-line"></i> พิมพ์บัตรกำนัล
                            </button>`;

                    // แสดงปุ่มยกเลิกเฉพาะบัตรที่ยังไม่ถูกใช้และผู้ใช้มีสิทธิ์
                    if (!data.customer_id && data.status === 'unused' && 
                        <?php echo $_SESSION['position_id']; ?> <= 2) {
                        buttons += `
                            <button type="button" class="btn btn-danger" 
                                    onclick="cancelVoucher(${data.voucher_id})" 
                                    data-bs-toggle="tooltip" 
                                    title="ยกเลิกบัตรกำนัล">
                                <i class="ri-delete-bin-line"></i>
                            </button>`;
                    }
                    
                    buttons += '</div>';
                    return buttons;
                }
            }
        ],
        order: [[2, 'desc']], // เรียงตามวันที่สร้างล่าสุด
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "ทั้งหมด"]],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
        }
    });

    // จัดการการเปลี่ยนประเภทส่วนลด
    $('#discount_type').change(function() {
        const type = $(this).val();
        const amountInput = $('#amount');
        const maxDiscountField = $('#maxDiscountField');
        const amountSuffix = $('#amountSuffix');

        if (type === 'percent') {
            // กรณีเลือกเป็นเปอร์เซ็นต์
            amountInput.attr('max', '100');
            amountSuffix.text('%');
            maxDiscountField.show(); // แสดงช่องจำนวนเงินสูงสุด
            
            // ถ้าเปลี่ยนเป็นเปอร์เซ็นต์และค่ามากกว่า 100 ให้รีเซ็ตเป็น 0
            if (parseFloat(amountInput.val()) > 100) {
                amountInput.val('');
            }
        } else {
            // กรณีเลือกเป็นจำนวนเงิน
            amountInput.removeAttr('max');
            amountSuffix.text('บาท');
            maxDiscountField.hide(); // ซ่อนช่องจำนวนเงินสูงสุด
            $('#max_discount').val(''); // เคลียร์ค่า
        }
    });

    // เรียกใช้ครั้งแรกเพื่อตั้งค่าเริ่มต้น
    $('#discount_type').trigger('change');

    // Event listener สำหรับการค้นหา
    $('#searchInput').on('keyup', function() {
        vouchersTable.search(this.value).draw();
    });

    // Event listener สำหรับ filter สถานะ
    $('#statusFilter').on('change', function() {
        const status = this.value;
        vouchersTable.column(4).search(status === 'all' ? '' : getStatusText(status)).draw();
    });

    // Event listener สำหรับการเปลี่ยนจำนวนรายการต่อหน้า
    $('#perPage').on('change', function() {
        vouchersTable.page.len(this.value).draw();
    });

    // โหลดสถิติ
    loadStatistics();

    // รีเฟรชทุก 5 นาที
    setInterval(function() {
        vouchersTable.ajax.reload(null, false);
        loadStatistics();
    }, 300000);
});

// ฟังก์ชัน loadVouchersPage แบบใหม่
function loadVouchersPage(page) {
    if (vouchersTable) {
        vouchersTable.page(page - 1).draw(false);
    }
}

function loadStatistics() {
    $.ajax({
        url: 'sql/get-voucher-statistics.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                console.error('Error:', response.error);
                return;
            }
            $('#totalVouchers').text(response.total || 0);
            $('#unusedVouchers').text(response.unused || 0);
            $('#usedVouchers').text(response.used || 0);
            $('#expiredVouchers').text(response.expired || 0);
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถโหลดข้อมูลสถิติได้: ' + error
            });
        }
    });
}


function showAddVoucherModal() {
    $('#addVoucherForm')[0].reset();
    $('#addVoucherModal').modal('show');
}

function saveVoucher() {
    const form = document.getElementById('addVoucherForm');
    const formData = new FormData(form);

    // ตรวจสอบข้อมูล
    const amount = parseFloat(formData.get('amount'));
    const discountType = formData.get('discount_type');
    const expireDate = formData.get('expire_date');
    const maxDiscount = formData.get('max_discount');

    if (!amount || !expireDate || !discountType) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณากรอกข้อมูลให้ครบ',
            text: 'กรุณากรอกมูลค่า, ประเภทส่วนลด และวันหมดอายุ'
        });
        return;
    }

    // ตรวจสอบกรณีเป็นเปอร์เซ็นต์
    if (discountType === 'percent') {
        if (amount <= 0 || amount > 100) {
            Swal.fire({
                icon: 'warning',
                title: 'ข้อมูลไม่ถูกต้อง',
                text: 'กรุณาระบุเปอร์เซ็นต์ส่วนลดระหว่าง 1-100'
            });
            return;
        }

        // ตรวจสอบจำนวนเงินสูงสุด (ถ้ามี)
        if (maxDiscount && parseFloat(maxDiscount) <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'ข้อมูลไม่ถูกต้อง',
                text: 'จำนวนเงินส่วนลดสูงสุดต้องมากกว่า 0'
            });
            return;
        }
    }

    $.ajax({
        url: 'sql/create-voucher.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                let message = `
                    รหัสบัตรกำนัล: ${response.voucher_code}<br>
                    ประเภท: ${discountType === 'fixed' ? 'ลดเป็นจำนวนเงิน' : 'ลดเป็นเปอร์เซ็นต์'}<br>
                    มูลค่า: ${discountType === 'fixed' ? 
                        formatCurrency(amount) : 
                        amount + '%'}
                `;

                // เพิ่มข้อมูลจำนวนเงินสูงสุด (ถ้ามี)
                if (discountType === 'percent' && maxDiscount) {
                    message += `<br>จำนวนเงินสูงสุด: ${formatCurrency(parseFloat(maxDiscount))}`;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'สร้างบัตรกำนัลสำเร็จ',
                    html: message,
                    showConfirmButton: true
                }).then((result) => {
                    $('#addVoucherModal').modal('hide');
                    vouchersTable.ajax.reload();
                    loadStatistics();
                    if (result.isConfirmed) {
                        printVoucher(response.voucher_id);
                    }
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
                text: 'ไม่สามารถบันทึกข้อมูลได้'
            });
        }
    });
}

function viewVoucher(voucherId) {
    $.ajax({
        url: 'sql/get-voucher-details.php',
        type: 'GET',
        data: { voucher_id: voucherId },
        success: function(response) {
            if (response.success) {
                const voucher = response.data;
                let html = `
                    <div class="voucher-code">
                        ${voucher.voucher_code}
                    </div>

                    <div class="voucher-amount ${voucher.discount_type === 'percent' ? 'percent' : ''}">
                        <h4 class="mb-0">
                            ${voucher.discount_type === 'percent' ? 
                                `ส่วนลด ${voucher.amount}%` + 
                                (voucher.max_discount ? `<br><small>(สูงสุด ${formatCurrency(voucher.max_discount)})</small>` : '')
                                : 
                                formatCurrency(voucher.amount)
                            }
                        </h4>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="voucher-info-card">
                                <h6 class="voucher-info-title">สถานะ</h6>
                                <p class="voucher-info-value">
                                    <span class="status-badge status-${voucher.status}">
                                        ${getStatusText(voucher.status)}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="voucher-info-card">
                                <h6 class="voucher-info-title">วันหมดอายุ</h6>
                                <p class="voucher-info-value">${formatDate(voucher.expire_date)}</p>
                            </div>
                        </div>
                    </div>

                    <div class="voucher-info-card">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="voucher-info-title">ผู้สร้าง</h6>
                                <p class="voucher-info-value">${voucher.creator_name}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="voucher-info-title">วันที่สร้าง</h6>
                                <p class="voucher-info-value">${formatDatetime(voucher.created_at)}</p>
                            </div>
                        </div>
                    </div>

                    ${voucher.notes ? `
                        <div class="voucher-info-card">
                            <h6 class="voucher-info-title">หมายเหตุ</h6>
                            <p class="voucher-info-value">${voucher.notes}</p>
                        </div>
                    ` : ''}

                    ${voucher.customer_id ? `
                        <div class="alert-box warning">
                            <h6 class="mb-2">ข้อมูลการใช้งาน</h6>
                            ${voucher.customer_name ? 
                                `<p class="mb-1">ผู้ใช้: ${voucher.customer_name}</p>` : 
                                ''
                            }
                            ${voucher.first_actual_usage ? 
                                `<p class="mb-1">เริ่มใช้เมื่อ: ${formatDatetime(voucher.first_actual_usage)}</p>` : 
                                ''
                            }
                            ${voucher.discount_type === 'fixed' ? `
                                <p class="mb-1">มูลค่าคงเหลือ: ${formatCurrency(
                                    voucher.first_actual_usage ? 
                                    (parseFloat(voucher.remaining_amount) || 0) : 
                                    parseFloat(voucher.amount)
                                )}</p>
                            ` : ''}
                        </div>
                    ` : ''}

                    <div class="alert-box info">
                        <i class="ri-information-line me-2"></i>
                        บัตรกำนัลนี้สามารถใช้ได้กับทุกสาขาและทุกคอร์ส
                        ${voucher.discount_type === 'fixed' ? 
                            '<br>สามารถใช้ได้หลายครั้งจนกว่าจะหมดมูลค่าหรือหมดอายุ' : 
                            '<br>สามารถใช้ได้ครั้งเดียวเท่านั้น'
                        }
                    </div>`;

                if (voucher.usage_history && voucher.usage_history.length > 0) {
                    let totalUsed = 0;
                    html += `
                        <div class="usage-history">
                            <h6 class="mb-3">ประวัติการใช้งาน</h6>
                            <div class="table-responsive">
                                <table class="usage-history-table">
                                    <thead>
                                        <tr>
                                            <th>วันที่</th>
                                            <th>รายละเอียด</th>
                                            <th class="text-end">จำนวนเงิน</th>
                                            ${voucher.discount_type === 'fixed' ? 
                                                `<th class="text-end">คงเหลือ</th>` : 
                                                ''
                                            }
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${voucher.usage_history.map(h => {
                                            if (h.used_at) {
                                                totalUsed += parseFloat(h.amount_used) || 0;
                                                const remainingAmount = parseFloat(voucher.amount) - totalUsed;
                                                return `
                                                    <tr>
                                                        <td>${formatDatetime(h.used_at)}</td>
                                                        <td>
                                                            <div>Order-${String(h.order_id).padStart(6, '0')}</div>
                                                            <small class="text-muted">${h.branch_name || ''}</small>
                                                        </td>
                                                        <td class="text-end">${formatCurrency(parseFloat(h.amount_used) || 0)}</td>
                                                        ${voucher.discount_type === 'fixed' ? 
                                                            `<td class="text-end">${formatCurrency(remainingAmount)}</td>` : 
                                                            ''
                                                        }
                                                    </tr>
                                                `;
                                            }
                                            return '';
                                        }).join('')}
                                    </tbody>
                                    ${voucher.discount_type === 'fixed' ? `
                                        <tfoot>
                                            <tr>
                                                <td colspan="2" class="text-end"><strong>ยอดรวมที่ใช้ไป:</strong></td>
                                                <td class="text-end"><strong>${formatCurrency(totalUsed)}</strong></td>
                                                <td class="text-end"><strong>${formatCurrency(parseFloat(voucher.amount) - totalUsed)}</strong></td>
                                            </tr>
                                        </tfoot>
                                    ` : ''}
                                </table>
                            </div>
                        </div>
                    `;
                }

                $('#voucherDetails').html(html);
                $('#viewVoucherModal').addClass('voucher-detail-modal').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message
                });
            }
        }
    });
}

function printVoucher(voucherId) {
    console.log('Printing voucher ID:', voucherId); // Debug line

    if (!voucherId) {
        console.error('No voucher ID provided');
        return;
    }
    $.ajax({
        url: 'sql/get-voucher-print.php',
        type: 'GET',
        data: { 
            voucher_id: voucherId
        },
        dataType: 'json',
        beforeSend: function() {
            console.log('Sending request with voucher ID:', voucherId); // Debug line
        },
        success: function(response) {
            console.log('Print voucher response:', response);
            if (response.success) {
                const { voucher, branch } = response.data;
                 const printContent = `
                    <style>
                        @page {
                            size: A5 portrait;
                            margin: 0;
                        }
                        body {
                            font-family: 'Sarabun', sans-serif;
                            margin: 0;
                            padding: 0;
                            width: 148mm; /* A5 width */
                            height: 170mm; /* A5 height */
                            box-sizing: border-box;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            background-color: white;
                        }
                        .voucher {
                            width: 90mm; /* ปรับความกว้างให้แคบลง */
                            height: 168mm; /* 80% ของความสูง A5 */
                            margin: auto;
                            border: 2px solid #000;
                            border-radius: 8px;
                            padding: 6mm;
                            background: white;
                            display: flex;
                            flex-direction: column;
                            position: relative;
                            box-sizing: border-box;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 3mm;
                        }
                        .logo {
                            width: 10mm;
                            height: auto;
                            margin-bottom: 2mm;
                        }
                        .branch-name {
                            font-size: 14pt;
                            font-weight: bold;
                            color: #1a365d;
                            margin: 2mm 0;
                        }
                        .branch-info {
                            font-size: 8pt;
                            color: #4a5568;
                            line-height: 1.3;
                            margin-bottom: 3mm;
                        }
                        .divider {
                            width: 100%;
                            border-top: 1px dashed #718096;
                            margin: 1mm 0;
                        }
                        .gift-voucher {
                            font-size: 15pt;
                            font-weight: bold;
                            color: #2d3748;
                            margin: 1mm 0;
                            text-align: center;
                            letter-spacing: 2px;
                        }
                        .amount {
                            font-size: 18pt;
                            font-weight: bold;
                            text-align: center;
                            margin: 2mm 0;
                            color: #2d3748;
                            background: ${voucher.discount_type === 'percent' ? '#ebf8ff' : '#f0fff4'};
                            padding: 2mm 4mm;
                            border-radius: 4px;
                            word-break: break-word;
                        }
                        .code {
                            font-size: 14pt;
                            font-weight: bold;
                            text-align: center;
                            margin: 1mm 0;
                            color: #2b6cb0;
                            font-family: monospace;
                            letter-spacing: 1.5px;
                            background: #f8fafc;
                            padding: 2mm;
                            border-radius: 4px;
                            border: 1px dashed #2b6cb0;
                            word-break: break-all;
                        }
                        .details {
                            font-size: 9pt;
                            text-align: center;
                            margin: 1mm 0;
                            color: #4a5568;
                            line-height: 1.4;
                        }
                        .conditions {
                            font-size: 8pt;
                            text-align: left;
                            margin: 4mm 0;
                            color: #718096;
                            background: #f7fafc;
                            padding: 3mm;
                            border-radius: 4px;
                            flex-grow: 1;
                        }
                        .conditions ul {
                            margin: 1mm 0;
                            padding-left: 4mm;
                        }
                        .conditions li {
                            margin: 0.8mm 0;
                            line-height: 1.3;
                        }
                        .footer {
                            font-size: 7pt;
                            text-align: center;
                            color: #a0aec0;
                            margin-top: 2mm;
                            padding: 2mm 0;
                        }
                        @media print {
                            body {
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            .voucher {
                                page-break-inside: avoid;
                                border-color: black !important;
                            }
                        }
                    </style>
                    <div class="voucher">
                        <div class="header">
                            <img src="../img/d.png" alt="Logo" class="logo">
                            <div class="branch-name">${branch.branch_name}</div>
                            <div class="branch-info">
                                ${branch.branch_address ? `<div>${branch.branch_address}</div>` : ''}
                                ${branch.branch_phone ? `<div>โทร: ${branch.branch_phone}</div>` : ''}
                                ${branch.branch_line_id ? `<div>Line: ${branch.branch_line_id}</div>` : ''}
                            </div>
                        </div>

                        <div class="divider"></div>

                        <div class="gift-voucher">GIFT VOUCHER</div>
                        
                        <div class="amount">
                            ${voucher.discount_type === 'percent' ? 
                                `ส่วนลด ${voucher.amount}%` :
                                new Intl.NumberFormat('th-TH', {
                                    style: 'currency',
                                    currency: 'THB'
                                }).format(voucher.amount)
                            }
                            ${voucher.discount_type === 'percent' && voucher.max_discount ? 
                                `<div style="font-size: 11pt; margin-top: 2mm;">
                                    สูงสุด ${new Intl.NumberFormat('th-TH', {
                                        style: 'currency',
                                        currency: 'THB'
                                    }).format(voucher.max_discount)}
                                </div>` : ''
                            }
                        </div>

                        <div class="code">${voucher.voucher_code}</div>

                        <div class="details">
                            <div>วันที่ออกบัตร: ${new Date(voucher.created_at).toLocaleDateString('th-TH')}</div>
                            <div>วันหมดอายุ: ${new Date(voucher.expire_date).toLocaleDateString('th-TH')}</div>
                        </div>

                        <div class="divider"></div>

                        <div class="conditions">
                            <strong>เงื่อนไขการใช้บริการ</strong>
                            <ul>
                                <li>ใช้ได้กับทุกคอร์สบริการ</li>
                                <li>กรุณาแสดงบัตรกำนัลก่อนใช้บริการ</li>
                                ${voucher.discount_type === 'fixed' ? `
                                    <li>สามารถใช้ได้หลายครั้งจนกว่าจะหมดมูลค่าหรือหมดอายุ</li>
                                    <li>บัตรกำนัลจะผูกกับลูกค้าที่ใช้ครั้งแรก</li>
                                ` : `
                                    <li>ใช้ได้เพียงครั้งเดียวเท่านั้น</li>
                                    <li>ส่วนลด ${voucher.amount}% จากราคาปกติ</li>
                                    ${voucher.max_discount ? `<li>ส่วนลดสูงสุด ${formatCurrency(voucher.max_discount)}</li>` : ''}
                                `}
                                <li>ไม่สามารถแลกเปลี่ยนหรือทอนเป็นเงินสดได้</li>
                                <li>บัตรกำนัลมีอายุถึง ${formatDate(voucher.expire_date)}</li>
                            </ul>
                        </div>

                        <div class="divider"></div>

                        <div class="footer">
                            <small>เอกสารนี้ออกโดยระบบคอมพิวเตอร์</small>
                        </div>
                    </div>
                `;

                const printWindow = window.open('', '', 'height=600,width=400');
                printWindow.document.write('<html><head><title>Print Voucher</title>');
                printWindow.document.write('<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">');
                printWindow.document.write('</head><body>');
                printWindow.document.write(printContent);
                printWindow.document.write('</body></html>');
                printWindow.document.close();

                setTimeout(() => {
                    printWindow.print();
                }, 500);
            }
            else {
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถดึงข้อมูลบัตรกำนัลได้'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Print voucher error:', {
                status: status,
                error: error,
                response: xhr.responseText,
                voucherId: voucherId // Debug line
            });
            
            try {
                const errorResponse = JSON.parse(xhr.responseText);
                console.log('Error debug info:', errorResponse.debug);
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อผิดพลาด',
                    text: 'ไม่สามารถพิมพ์บัตรกำนัลได้: ' + (errorResponse.message || error)
                });
            } catch (e) {
                console.error('Error parsing response:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อผิดพลาด',
                    text: 'เกิดข้อผิดพลาดในการพิมพ์บัตรกำนัล'
                });
            }
        }
    });
}

// ต่อจาก script เดิม...
function getStatusText(status) {
    switch(status) {
        case 'unused':
            return 'พร้อมใช้งาน';
        case 'used':
            return 'ใช้แล้ว';
        case 'expired':
            return 'หมดอายุ';
        default:
            return status;
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('th-TH', {
        style: 'currency',
        currency: 'THB'
    }).format(amount);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatDatetime(dateString) {
    return new Date(dateString).toLocaleString('th-TH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function validateVoucherForm() {
    const amount = parseFloat($('#amount').val());
    const expireDate = new Date($('#expireDate').val());
    const today = new Date();
    let isValid = true;
    let errorMessage = '';

    // ตรวจสอบมูลค่า
    if (!amount || amount <= 0) {
        errorMessage = 'กรุณาระบุมูลค่าบัตรกำนัลให้ถูกต้อง';
        isValid = false;
    }

    // ตรวจสอบวันหมดอายุ
    if (!expireDate || expireDate < today) {
        errorMessage = 'วันหมดอายุต้องเป็นวันที่ในอนาคต';
        isValid = false;
    }

    if (!isValid) {
        Swal.fire({
            icon: 'warning',
            title: 'ข้อมูลไม่ถูกต้อง',
            text: errorMessage
        });
    }

    return isValid;
}

// ฟังก์ชันสำหรับการโหลดข้อมูลแบบแบ่งหน้า
function loadVouchersPage(page = 1) {
    if (vouchersTable) {
        vouchersTable.draw(false);
    }
}

// ฟังก์ชันอัพเดทตาราง
function updateVouchersTable(vouchers) {
    const tableBody = $('#vouchersTable tbody');
    tableBody.empty();

    vouchers.forEach(voucher => {
        const row = `
            <tr>
                <td>${voucher.voucher_code}</td>
                <td class="text-end">${formatCurrency(voucher.amount)}</td>
                <td>${formatDate(voucher.created_at)}</td>
                <td>${formatDate(voucher.expire_date)}</td>
                <td>
                    <span class="status-badge status-${voucher.status}">
                        ${getStatusText(voucher.status)}
                    </span>
                </td>
                <td>${voucher.creator_name}</td>
                <td>${voucher.used_at ? formatDate(voucher.used_at) : '-'}</td>
                <td>${voucher.used_in_order ? 'ORDER-' + String(voucher.used_in_order).padStart(6, '0') : '-'}</td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-info" onclick="viewVoucher(${voucher.voucher_id})">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button type="button" class="btn btn-primary" onclick="printVoucher(${voucher.voucher_id})">
                            <i class="ri-printer-line"></i>
                        </button>
                        ${voucher.status === 'unused' && <?php echo $_SESSION['position_id']; ?> <= 2 ? `
                            <button type="button" class="btn btn-danger" onclick="cancelVoucher(${voucher.voucher_id})">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
        tableBody.append(row);
    });
}

// ฟังก์ชันอัพเดท pagination
function updatePagination(pagination) {
    if (!pagination) return;
    
    const paginationEl = $('.pagination');
    paginationEl.empty();

    // Previous button
    if (pagination.current_page > 1) {
        paginationEl.append(`
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="loadVouchersPage(${pagination.current_page - 1})">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
            </li>
        `);
    }

    // Page numbers
    for (let i = 1; i <= pagination.total_pages; i++) {
        paginationEl.append(`
            <li class="page-item ${pagination.current_page === i ? 'active' : ''}">
                <a class="page-link" href="javascript:void(0)" onclick="loadVouchersPage(${i})">${i}</a>
            </li>
        `);
    }

    // Next button
    if (pagination.current_page < pagination.total_pages) {
        paginationEl.append(`
            <li class="page-item">
                <a class="page-link" href="javascript:void(0)" onclick="loadVouchersPage(${pagination.current_page + 1})">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
            </li>
        `);
    }
}

// Event handlers
$(document).ready(function() {
    // ตั้งค่า DataTable
    // const table = $('#vouchersTable').DataTable({
    //     language: {
    //         url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/th.json'
    //     },
    //     order: [[2, 'desc']], // เรียงตามวันที่สร้างล่าสุด
    //     pageLength: 10,
    //     lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "ทั้งหมด"]],
    //     responsive: true
    // });

    // Event listener สำหรับการค้นหา
    $('#searchInput').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Event listener สำหรับ filter สถานะ
    $('#statusFilter').on('change', function() {
        const status = this.value;
        table.column(4).search(status === 'all' ? '' : getStatusText(status)).draw();
    });

    // Event listener สำหรับการเปลี่ยนจำนวนรายการต่อหน้า
    $('#perPage').on('change', function() {
        table.page.len(this.value).draw();
    });

    // โหลดข้อมูลครั้งแรก
    loadVouchersPage();
    loadStatistics();

    // รีเฟรชข้อมูลทุก 5 นาที
    setInterval(function() {
        loadVouchersPage($('.pagination .active').text());
        loadStatistics();
    }, 300000);
});

// Event handler สำหรับฟอร์มสร้างบัตรกำนัล
$('#addVoucherForm').on('submit', function(e) {
    e.preventDefault();
    if (validateVoucherForm()) {
        saveVoucher();
    }
});

// Export functions
function exportToExcel() {
    const fileTitle = 'วาวเชอร์_' + new Date().toISOString().slice(0,10);
    table.button('.buttons-excel').trigger();
}

function exportToPDF() {
    const fileTitle = 'วาวเชอร์_' + new Date().toISOString().slice(0,10);
    table.button('.buttons-pdf').trigger();
}

// Print function
function printVoucherList() {
    table.button('.buttons-print').trigger();
}

// Utility functions for date handling
function getThaiMonth(month) {
    const thaiMonths = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];
    return thaiMonths[month];
}

function displayError(message) {
    Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด',
        text: message
    });
}

function displaySuccess(message) {
    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ',
        text: message,
        timer: 1500,
        showConfirmButton: false
    });
}

// Initialize tooltips
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
function cancelVoucher(voucherId) {
    // ตรวจสอบสิทธิ์เบื้องต้น
    if (<?php echo $_SESSION['position_id']; ?> > 2) {
        Swal.fire({
            icon: 'error',
            title: 'ไม่มีสิทธิ์',
            text: 'คุณไม่มีสิทธิ์ในการยกเลิกบัตรกำนัล'
        });
        return;
    }

    // แสดง SweetAlert2 เพื่อยืนยันการยกเลิก
    Swal.fire({
        title: 'ยืนยันการยกเลิกบัตรกำนัล',
        text: 'คุณต้องการยกเลิกบัตรกำนัลนี้ใช่หรือไม่? การดำเนินการนี้ไม่สามารถย้อนกลับได้',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ยกเลิกบัตรกำนัล',
        cancelButtonText: 'ปิด',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            // ขอเหตุผลในการยกเลิก
            return Swal.fire({
                title: 'ระบุเหตุผลในการยกเลิก',
                input: 'textarea',
                inputPlaceholder: 'กรุณาระบุเหตุผลในการยกเลิกบัตรกำนัล',
                inputAttributes: {
                    'aria-label': 'กรุณาระบุเหตุผลในการยกเลิกบัตรกำนัล'
                },
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                inputValidator: (value) => {
                    if (!value) {
                        return 'กรุณาระบุเหตุผลในการยกเลิก';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    return result.value;
                }
                return false;
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            // ส่งคำขอยกเลิกไปยังเซิร์ฟเวอร์
            $.ajax({
                url: 'sql/cancel-voucher.php',
                type: 'POST',
                data: {
                    voucher_id: voucherId,
                    cancel_reason: result.value
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'ยกเลิกบัตรกำนัลสำเร็จ',
                            text: 'บัตรกำนัลถูกยกเลิกเรียบร้อยแล้ว',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            // รีเฟรชตารางและสถิติ
                            $('#vouchersTable').DataTable().ajax.reload();
                            loadStatistics();
                        });
                    } else {
                        // แสดงข้อผิดพลาด
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: response.message || 'ไม่สามารถยกเลิกบัตรกำนัลได้'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // แสดงข้อผิดพลาดกรณีเกิด error จาก AJAX
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์',
                        footer: error
                    });
                }
            });
        }
    });
}

function handleAjaxError(xhr, status, error) {
    console.error('AJAX Error:', {
        status: status,
        error: error,
        response: xhr.responseText
    });
    
    try {
        const response = JSON.parse(xhr.responseText);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: response.error || 'ไม่สามารถโหลดข้อมูลได้'
        });
    } catch (e) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์'
        });
    }
}

// ฟังก์ชันแปลงข้อความส่วนลด
function getDiscountText(voucher) {
    if (voucher.discount_type === 'percent') {
        let text = `${voucher.amount}%`;
        if (voucher.max_discount) {
            text += ` (สูงสุด ${formatCurrency(voucher.max_discount)})`;
        }
        return text;
    } else {
        return formatCurrency(voucher.amount);
    }
}

// ฟังก์ชันดูประวัติการใช้งาน
window.viewUsageHistory = function(voucherId) {
    $.ajax({
        url: 'sql/get-voucher-usage.php',
        type: 'GET',
        data: { voucher_id: voucherId },
        success: function(response) {
            if (response.success) {
                // แสดงข้อมูลบัตรกำนัล
                const voucher = response.data.voucher;
                const history = response.data.usage_history;

                let voucherInfo = `
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>รหัสบัตรกำนัล:</strong> ${voucher.voucher_code}</p>
                                    <p class="mb-1"><strong>มูลค่า:</strong> ${formatCurrency(voucher.amount)}</p>
                                    <p class="mb-1"><strong>ยอดคงเหลือ:</strong> ${formatCurrency(voucher.remaining_amount)}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>ผู้ถือบัตร:</strong> ${voucher.customer_name}</p>
                                    <p class="mb-1"><strong>วันที่เริ่มใช้:</strong> ${formatDatetime(voucher.first_used_at)}</p>
                                    <p class="mb-1"><strong>วันหมดอายุ:</strong> ${formatDate(voucher.expire_date)}</p>
                                </div>
                            </div>
                        </div>
                    </div>`;
                $('.voucher-info').html(voucherInfo);

                // แสดงประวัติการใช้งาน
                let historyHtml = '';
                let totalUsed = 0;

                history.forEach(item => {
                    totalUsed += parseFloat(item.amount_used);
                    historyHtml += `
                        <tr>
                            <td>${formatDatetime(item.used_at)}</td>
                            <td>ORDER-${String(item.order_id).padStart(6, '0')}</td>
                            <td class="text-end">${formatCurrency(item.amount_used)}</td>
                            <td class="text-end">${formatCurrency(item.remaining_amount)}</td>
                            <td>${item.branch_name}</td>
                        </tr>`;
                });

                // เพิ่มแถวสรุปยอดรวม
                historyHtml += `
                    <tr class="table-light fw-bold">
                        <td colspan="2" class="text-end">รวมทั้งหมด:</td>
                        <td class="text-end">${formatCurrency(totalUsed)}</td>
                        <td colspan="2"></td>
                    </tr>`;

                $('#usageHistoryTable').html(historyHtml);

                // แสดง Modal
                $('#usageHistoryModal').modal('show');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถโหลดข้อมูลได้'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์'
            });
        }
    });
};
    </script>
</body>
</html>