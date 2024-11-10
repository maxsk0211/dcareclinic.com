<?php 
  session_start();
  
  include 'chk-session.php';
  require '../dbcon.php';

// ดึงข้อมูลตำแหน่งทั้งหมด เรียงตาม position_id
$positionSql = "SELECT position_id, position_name 
                FROM position 
                ORDER BY position_id";
$positions = $conn->query($positionSql);
$positionData = $positions->fetch_all(MYSQLI_ASSOC);

 ?>

<!doctype html>

<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="horizontal-menu-template-no-customizer-starter"
  data-style="light">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>จัดการสิทธ์การใช้งาน | dcareclinic.com</title>

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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css"> 

<style>
    /* ปรับแต่งตารางให้เลื่อนได้สวยงาม */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* ตรึงคอลัมน์แรกและสุดท้าย (optional) */
    .permission-table {
        position: relative;
    }

    .permission-table th,
    .permission-table td {
        white-space: nowrap;
        vertical-align: middle;
    }

    /* ปรับแต่ง checkbox */
    .permission-checkbox {
        margin: 0;
    }

    /* ปรับแต่ง badge */
    .permission-table .badge {
        font-size: 0.8em;
        font-weight: normal;
    }

    /* disabled checkbox สำหรับ admin */
    .permission-checkbox:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* สีพื้นหลังสำหรับ checkbox ที่ถูก disabled */
    .permission-checkbox:disabled:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
</style>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">
            <?php include 'navbar.php'; ?>
            <div class="layout-page">
                <div class="content-wrapper">
                    <?php include 'menu.php'; ?>
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Header -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="fw-bold mb-0">จัดการสิทธิ์การใช้งาน</h4>
                                            <p class="mb-0">กำหนดและควบคุมสิทธิ์การเข้าถึงระบบ</p>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <!-- Export Buttons -->
<!--                                             <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="ri-download-line me-1"></i> Export
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="exportPermissions('excel')">Excel</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="exportPermissions('pdf')">PDF</a></li>
                                                </ul>
                                            </div> -->
                                            <!-- Add Permission Button -->
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
                                                <i class="ri-add-line me-1"></i> เพิ่มสิทธิ์พิเศษ
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- เพิ่มส่วนนี้หลัง Header -->
                        <ul class="nav nav-tabs mb-4" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#rolePermissions">
                                    <i class="ri-group-line me-1"></i> สิทธิ์ตามตำแหน่ง
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#specialPermissions">
                                    <i class="ri-user-star-line me-1"></i> สิทธิ์พิเศษรายบุคคล
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- สิทธิ์ตามตำแหน่ง Tab -->
                            <div class="tab-pane fade show active" id="rolePermissions">
                                <!-- ส่วนค้นหาและกรอง -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label">หมวดหมู่</label>
                                                <select class="form-select" id="categoryFilter">
                                                    <option value="">ทั้งหมด</option>
                                                    <option value="การเงิน">การเงิน</option>
                                                    <option value="การบริการ">การบริการ</option>
                                                    <option value="จัดการทรัพยากร">จัดการทรัพยากร</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">ตำแหน่ง</label>
                                                <select class="form-select" id="positionFilter">
                                                    <option value="">ทั้งหมด</option>
                                                    <?php
                                                    $sql = "SELECT position_id, position_name FROM position ORDER BY position_id";
                                                    $result = $conn->query($sql);
                                                    while ($row = $result->fetch_assoc()) {
                                                        echo "<option value='{$row['position_id']}'>{$row['position_name']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">ค้นหา</label>
                                                <input type="text" class="form-control" id="searchPermission" placeholder="ค้นหาสิทธิ์...">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ตารางแสดงสิทธิ์ -->
                                <?php
                                // ดึงข้อมูลหมวดหมู่ทั้งหมด
                                $categories = $conn->query("SELECT DISTINCT category FROM permissions ORDER BY category");
                                
                                while ($category = $categories->fetch_assoc()):
    // ดึงข้อมูล permissions สำหรับแต่ละหมวดหมู่
    $permissionSql = "SELECT p.*, 
            GROUP_CONCAT(
                CASE WHEN rp.granted = 1 THEN rp.position_id ELSE NULL END
            ) as granted_positions,
            CASE 
                WHEN p.page != '' THEN CONCAT(p.permission_name, ' (', p.page, ')')
                ELSE p.permission_name
            END as display_name
            FROM permissions p 
            LEFT JOIN role_permissions rp ON p.permission_id = rp.permission_id 
            WHERE p.category = ?
            GROUP BY p.permission_id
            ORDER BY p.permission_name";

    $stmt = $conn->prepare($permissionSql);
    $stmt->bind_param("s", $category['category']);
    $stmt->execute();
    $permissions = $stmt->get_result();
?>
<div class="card permission-card mb-4" data-category="<?php echo $category['category']; ?>">
    <div class="card-header">
        <h5 class="mb-0"><?php echo $category['category']; ?></h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table permission-table">
                <thead>
                    <tr>
                        <th style="width: 300px; min-width: 300px;">สิทธิ์</th>
                        <?php foreach($positionData as $position): ?>
                            <th style="width: 100px; min-width: 100px;">
                                <?php echo htmlspecialchars($position['position_name']); ?>
                            </th>
                        <?php endforeach; ?>
                        <th style="width: 100px; min-width: 100px;">การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($perm = $permissions->fetch_assoc()):
                        // แปลง granted_positions เป็น array
                        $granted_positions = $perm['granted_positions'] ? explode(',', $perm['granted_positions']) : [];
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">
                                    <?php echo $perm['permission_name']; ?>
                                    <?php if($perm['page']): ?>
                                        <span class="badge bg-secondary ms-2">
                                            <?php echo $perm['page']; ?>
                                        </span>
                                    <?php endif; ?>
                                </span>
                                <?php if($perm['description']): ?>
                                    <small class="text-muted"><?php echo $perm['description']; ?></small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <?php foreach($positionData as $position): ?>
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input type="checkbox" 
                                           class="form-check-input permission-checkbox"
                                           data-permission-id="<?php echo $perm['permission_id']; ?>"
                                           data-position-id="<?php echo $position['position_id']; ?>"
                                           <?php 
                                           // ถ้าเป็นผู้ดูแลระบบ (position_id = 1)
                                           if($position['position_id'] == 1): ?>
                                               disabled checked
                                           <?php else: ?>
                                               <?php echo in_array($position['position_id'], $granted_positions) ? 'checked' : ''; ?>
                                           <?php endif; ?>>
                                </div>
                            </td>
                        <?php endforeach; ?>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-info"
                                    onclick="viewPermissionHistory(<?php echo $perm['permission_id']; ?>)">
                                <i class="ri-history-line"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php 
endwhile; 
?>
                            </div>


                            <!-- สิทธิ์พิเศษรายบุคคล Tab -->
                            <!-- ส่วนแสดงข้อมูลในแท็บสิทธิ์พิเศษรายบุคคล -->
                            <div class="tab-pane fade" id="specialPermissions">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="specialPermissionsTable">
                                                <thead>
                                                    <tr>
                                                        <th>ชื่อ-สกุล</th>
                                                        <th>ตำแหน่ง</th>
                                                        <th>สิทธิ์พิเศษ</th>
                                                        <th>วันที่เริ่มต้น</th>
                                                        <th>วันที่สิ้นสุด</th>
                                                        <th>สถานะ</th>
                                                        <th>ผู้อนุมัติ</th>
                                                        <th>การดำเนินการ</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                // SQL Query ดึงข้อมูล
                                                $sql = "SELECT usp.*, 
                                                              u.users_fname, u.users_lname,
                                                              p.position_name,
                                                              perm.permission_name,
                                                              granter.users_fname as granter_fname,
                                                              granter.users_lname as granter_lname
                                                       FROM user_specific_permissions usp
                                                       JOIN users u ON usp.users_id = u.users_id
                                                       JOIN position p ON u.position_id = p.position_id
                                                       JOIN permissions perm ON usp.permission_id = perm.permission_id
                                                       JOIN users granter ON usp.granted_by = granter.users_id
                                                       ORDER BY usp.created_at DESC";
                                                
                                                $result = $conn->query($sql);
                                                while ($row = $result->fetch_assoc()):
                                                ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $row['users_fname'] . ' ' . $row['users_lname']; ?>
                                                        </td>
                                                        <td><?php echo $row['position_name']; ?></td>
                                                        <td><?php echo $row['permission_name']; ?></td>
                                                        <td><?php echo $row['start_date'] ? date('d/m/Y', strtotime($row['start_date'])) : '-'; ?></td>
                                                        <td><?php echo $row['end_date'] ? date('d/m/Y', strtotime($row['end_date'])) : 'ไม่มีกำหนด'; ?></td>
                                                        <td>
                                                            <!-- แสดงสถานะด้วย badge -->
                                                            <?php 
                                                            $status = 'ใช้งานอยู่';
                                                            $statusClass = 'bg-success';
                                                            if ($row['end_date']) {
                                                                $endDate = strtotime($row['end_date']);
                                                                $now = time();
                                                                $days_remaining = ($endDate - $now) / (60 * 60 * 24);
                                                                
                                                                if ($days_remaining < 0) {
                                                                    $status = 'หมดอายุ';
                                                                    $statusClass = 'bg-danger';
                                                                } elseif ($days_remaining <= 7) {
                                                                    $status = 'ใกล้หมดอายุ';
                                                                    $statusClass = 'bg-warning';
                                                                }
                                                            }
                                                            ?>
                                                            <span class="badge <?php echo $statusClass; ?>">
                                                                <?php echo $status; ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo $row['granter_fname'] . ' ' . $row['granter_lname']; ?></td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-sm btn-info" 
                                                                        onclick="viewSpecialPermission(<?php echo $row['id']; ?>)">
                                                                    <i class="ri-eye-line"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-warning"
                                                                        onclick="editSpecialPermission(<?php echo $row['id']; ?>)">
                                                                    <i class="ri-edit-line"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                        onclick="revokeSpecialPermission(<?php echo $row['id']; ?>)">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Modals -->

                        <!-- Permission History Modal -->
                        <div class="modal fade" id="permissionHistoryModal" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">ประวัติการแก้ไขสิทธิ์</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Filters -->
                                        <div class="mb-3">
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <input type="date" class="form-control" id="historyDateFilter">
                                                </div>
                                                <div class="col-md-4">
                                                    <select class="form-select" id="historyActionFilter">
                                                        <option value="">ทั้งหมด</option>
                                                        <option value="grant">ให้สิทธิ์</option>
                                                        <option value="revoke">ยกเลิกสิทธิ์</option>
                                                        <option value="modify">แก้ไข</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" placeholder="ค้นหา..." id="historySearch">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- History Table -->
                                        <div class="table-responsive">
                                            <table class="table" id="historyTable">
                                                <thead>
                                                    <tr>
                                                        <th>วันที่</th>
                                                        <th>การดำเนินการ</th>
                                                        <th>ผู้ดำเนินการ</th>
                                                        <th>รายละเอียด</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Confirm Modal -->
                        <div class="modal fade" id="confirmModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">ยืนยันการดำเนินการ</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p id="confirmMessage"></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                        <button type="button" class="btn btn-primary" id="confirmAction">ยืนยัน</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bulk Edit Modal -->
                        <div class="modal fade" id="bulkEditModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">แก้ไขสิทธิ์หลายรายการ</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="bulkEditForm">
                                            <div class="mb-3">
                                                <label class="form-label">การดำเนินการ</label>
                                                <select class="form-select" name="bulk_action" required>
                                                    <option value="">เลือกการดำเนินการ</option>
                                                    <option value="grant">ให้สิทธิ์ทั้งหมด</option>
                                                    <option value="revoke">ยกเลิกสิทธิ์ทั้งหมด</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">หมายเหตุ</label>
                                                <textarea class="form-control" name="bulk_note" rows="3"></textarea>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                        <button type="button" class="btn btn-primary" onclick="submitBulkEdit()">บันทึก</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Import Modal -->
                        <div class="modal fade" id="importModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">นำเข้าข้อมูลสิทธิ์</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="importForm">
                                            <div class="mb-3">
                                                <label class="form-label">ไฟล์ Excel</label>
                                                <input type="file" class="form-control" accept=".xlsx,.xls" required>
                                                <small class="text-muted">ดาวน์โหลดแม่แบบได้ <a href="#">ที่นี่</a></small>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                        <button type="button" class="btn btn-primary" onclick="submitImport()">นำเข้า</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <?php include 'footer.php'; ?>
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">กำลังโหลด...</span>
        </div>
    </div>


<!-- Modal เพิ่มสิทธิ์พิเศษ -->
<div class="modal fade" id="addPermissionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">เพิ่มสิทธิ์พิเศษ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addSpecialPermissionForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- เลือกผู้ใช้ -->
                        <div class="col-md-12">
                            <label class="form-label">ผู้ใช้งาน <span class="text-danger">*</span></label>
                            <select class="form-select" name="users_id" required>
                                <option value="">เลือกผู้ใช้งาน</option>
                                <?php
                                $sql = "SELECT u.users_id, u.users_fname, u.users_lname, p.position_name 
                                       FROM users u 
                                       JOIN position p ON u.position_id = p.position_id 
                                       WHERE u.users_status = 1 
                                       ORDER BY p.position_id, u.users_fname";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['users_id']}'>{$row['users_fname']} {$row['users_lname']} ({$row['position_name']})</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- เลือกหมวดหมู่สิทธิ์ -->
                        <div class="col-md-6">
                            <label class="form-label">หมวดหมู่สิทธิ์ <span class="text-danger">*</span></label>
                            <select class="form-select" id="permissionCategory" required>
                                <option value="">เลือกหมวดหมู่</option>
                                <?php
                                $sql = "SELECT DISTINCT category FROM permissions ORDER BY category";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['category']}'>{$row['category']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- เลือกสิทธิ์ -->
                        <div class="col-md-6">
                            <label class="form-label">สิทธิ์ <span class="text-danger">*</span></label>
                            <select class="form-select" name="permission_id" id="permissionSelect" required disabled>
                                <option value="">เลือกสิทธิ์</option>
                            </select>
                        </div>

                        <!-- กำหนดระยะเวลา -->
                        <div class="col-md-6">
                            <label class="form-label">วันที่เริ่มต้น</label>
                            <input type="date" class="form-control" name="start_date" 
                                   value="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">วันที่สิ้นสุด</label>
                            <input type="date" class="form-control" name="end_date">
                            <small class="text-muted">ไม่ระบุ = ไม่มีกำหนด</small>
                        </div>

                        <!-- หมายเหตุ -->
                        <div class="col-12">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea class="form-control" name="note" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal แก้ไขสิทธิ์พิเศษ -->
<div class="modal fade" id="editPermissionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark">แก้ไขสิทธิ์พิเศษ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSpecialPermissionForm">
                <input type="hidden" name="permission_id" id="editPermissionId">
                <div class="modal-body">
                    <!-- จะเติมข้อมูลด้วย JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal ดูรายละเอียดสิทธิ์พิเศษ -->
<div class="modal fade" id="viewPermissionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">รายละเอียดสิทธิ์พิเศษ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- ข้อมูลผู้ใช้ -->
                <div class="card mb-3">
                    <div class="card-body" id="viewUserDetails">
                        <!-- จะเติมข้อมูลด้วย JavaScript -->
                    </div>
                </div>

                <!-- ข้อมูลสิทธิ์ -->
                <div class="card mb-3">
                    <div class="card-body" id="viewPermissionDetails">
                        <!-- จะเติมข้อมูลด้วย JavaScript -->
                    </div>
                </div>

                <!-- ประวัติการแก้ไข -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">ประวัติการแก้ไข</h6>
                        <button class="btn btn-sm btn-outline-secondary" onclick="clearViewHistoryFilters()">
                            <i class="ri-refresh-line"></i> ล้างตัวกรอง
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="mb-3">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="date" class="form-control" id="viewHistoryDateFilter" placeholder="กรองตามวันที่">
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" id="viewHistoryActionFilter">
                                        <option value="">ทั้งหมด</option>
                                        <option value="ให้สิทธิ์">ให้สิทธิ์</option>
                                        <option value="ยกเลิกสิทธิ์">ยกเลิกสิทธิ์</option>
                                        <option value="แก้ไข">แก้ไข</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="viewHistorySearch" placeholder="ค้นหา...">
                                </div>
                            </div>
                        </div>

                        <!-- History Table -->
                        <div class="table-responsive">
                            <table class="table" id="viewHistoryTable">
                                <thead>
                                    <tr>
                                        <th>วันที่</th>
                                        <th>การดำเนินการ</th>
                                        <th>ผู้ดำเนินการ</th>
                                        <th>รายละเอียด</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- จะเติมข้อมูลด้วย JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

    <!-- Core JS -->
    <!-- sweet Alerts 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Required Scripts -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <script src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.dataTables.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <!-- <script src="js/permissions.js"></script> -->

    <!-- Page JS -->

<script>
// ฟังก์ชันสำหรับการกรองข้อมูล
$(document).ready(function() {
    
    // เพิ่มการจัดการแท็บ
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr("href");
        if (target === '#specialPermissions') {
            // รีโหลดตารางเมื่อเปิดแท็บสิทธิ์พิเศษ
            if ($.fn.DataTable.isDataTable('#specialPermissionsTable')) {
                $('#specialPermissionsTable').DataTable().ajax.reload();
            }
        }
    });

    // กรองตามหมวดหมู่
    $('#categoryFilter').change(function() {
        const category = $(this).val();
        if (category) {
            $('.permission-card').hide();
            $(`.permission-card[data-category="${category}"]`).show();
        } else {
            $('.permission-card').show();
        }
    });

    // ค้นหาสิทธิ์
    $('#searchPermission').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('.permission-table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // จัดการการเปลี่ยนแปลงสิทธิ์
    $('.permission-checkbox').change(function() {
        if (!$(this).prop('disabled')) {
            const permissionId = $(this).data('permission-id');
            const positionId = $(this).data('position-id');
            const granted = $(this).prop('checked');

            updatePermission(permissionId, positionId, granted);
        }
    });

    // Initialize DataTable
    $('#specialPermissionsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: 'sql/get_special_permissions.php',
            type: 'GET',
            dataSrc: 'data'
        },
        columns: [
            { data: 'users_fullname' },
            { data: 'position_name' },
            { data: 'permission_name' },
            { data: 'start_date' },
            { data: 'end_date' },
            { 
                data: null,
                render: function(data) {
                    return `<span class="badge ${data.status_class}">${data.status_text}</span>`;
                }
            },
            { data: 'granted_by' },
            {
                data: 'id',
                render: function(data) {
                    return `
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-info" onclick="viewSpecialPermission(${data})">
                                <i class="ri-eye-line"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" onclick="editSpecialPermission(${data})">
                                <i class="ri-edit-line"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="revokeSpecialPermission(${data})">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json',
            emptyTable: 'ไม่พบข้อมูลสิทธิ์พิเศษ'
        },
        order: [[4, 'desc']], // เรียงตามวันที่สิ้นสุด จากใหม่ไปเก่า
        responsive: true
    });

    // กรองตามตำแหน่ง
    $('#specialPositionFilter').change(function() {
        const position = $(this).val();
        specialPermissionsTable
            .column(1)
            .search(position)
            .draw();
    });

    // กรองตามสถานะ
    $('#statusFilter').change(function() {
        const status = $(this).val();
        specialPermissionsTable
            .column(5)
            .search(status)
            .draw();
    });

    // ค้นหาทั่วไป
    $('#searchSpecialPermission').keyup(function() {
        specialPermissionsTable.search(this.value).draw();
    });

    // แก้ไขการ submit form เพิ่มสิทธิ์พิเศษ
    $('#addSpecialPermissionForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'sql/add_special_permission.php',
            method: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#addPermissionModal').modal('hide');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'เพิ่มสิทธิ์พิเศษเรียบร้อยแล้ว',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // เปลี่ยนไปที่แท็บสิทธิ์พิเศษ
                        $('a[href="#specialPermissions"]').tab('show');
                        // รีโหลดข้อมูลในตาราง
                        $('#specialPermissionsTable').DataTable().ajax.reload();
                    });
                } else {
                    Swal.fire('ข้อผิดพลาด', response.message || 'ไม่สามารถบันทึกข้อมูลได้', 'error');
                }
            },
            error: function() {
                Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
            }
        });
    });


    // อาจเพิ่มฟังก์ชันสำหรับ scroll indicator ถ้าต้องการ
    $('.table-responsive').on('scroll', function() {
        if(this.scrollLeft > 0) {
            $(this).addClass('has-scroll');
        } else {
            $(this).removeClass('has-scroll');
        }
    });

});

// ฟังก์ชันอัพเดทสิทธิ์
function updatePermission(permissionId, positionId, granted) {
    Swal.fire({
        title: `ยืนยันการ${granted ? 'เพิ่ม' : 'ยกเลิก'}สิทธิ์`,
        text: granted ? 
              "คุณต้องการเพิ่มสิทธิ์นี้ใช่หรือไม่?" : 
              "คุณต้องการยกเลิกสิทธิ์นี้ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'sql/update_permission.php',
                method: 'POST',
                data: {
                    permission_id: permissionId,
                    position_id: positionId,
                    granted: granted
                },
                success: function(response) {
                    if (response.success) {
                        if (response.changed) {
                            Swal.fire({
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            // ถ้าไม่มีการเปลี่ยนแปลง
                            const checkbox = $(`.permission-checkbox[data-permission-id="${permissionId}"][data-position-id="${positionId}"]`);
                            checkbox.prop('checked', !granted);

                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true
                            });

                            Toast.fire({
                                icon: 'info',
                                title: response.message
                            });
                        }
                    } else {
                        Swal.fire('ข้อผิดพลาด', response.message || 'ไม่สามารถบันทึกการเปลี่ยนแปลงได้', 'error');
                        // location.reload();
                    }
                },
                error: function() {
                    Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                    // location.reload();
                }
            });
        } else {
            // ยกเลิกการเปลี่ยนแปลง คืนค่าเช็คบ็อกซ์
            const checkbox = $(`.permission-checkbox[data-permission-id="${permissionId}"][data-position-id="${positionId}"]`);
            checkbox.prop('checked', !granted);
        }
    });
}

// ฟังก์ชันดูประวัติการเปลี่ยนแปลงสิทธิ์
function viewPermissionHistory(permissionId) {
    // แสดง loading
    $('#permissionHistoryModal').modal('show');
    $('#historyTable tbody').html(`
        <tr>
            <td colspan="4" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">กำลังโหลด...</span>
                </div>
            </td>
        </tr>
    `);

    // เรียกข้อมูลประวัติ
    $.ajax({
        url: 'sql/get_permission_history.php',
        method: 'GET',
        data: { permission_id: permissionId },
        success: function(response) {
            if (response.success) {
                let html = '';
                if (response.data.length > 0) {
                    response.data.forEach(item => {
                        html += `
                            <tr>
                                <td>${item.date}</td>
                                <td>
                                    <span class="badge ${item.action_class}">
                                        ${item.action}
                                    </span>
                                </td>
                                <td>${item.performed_by}</td>
                                <td>${item.details}</td>
                            </tr>
                        `;
                    });
                } else {
                    html = `
                        <tr>
                            <td colspan="4" class="text-center">ไม่พบประวัติการแก้ไข</td>
                        </tr>
                    `;
                }
                $('#historyTable tbody').html(html);
            } else {
                $('#historyTable tbody').html(`
                    <tr>
                        <td colspan="4" class="text-center text-danger">
                            ${response.message || 'เกิดข้อผิดพลาดในการโหลดข้อมูล'}
                        </td>
                    </tr>
                `);
            }
        },
        error: function() {
            $('#historyTable tbody').html(`
                <tr>
                    <td colspan="4" class="text-center text-danger">
                        เกิดข้อผิดพลาดในการเชื่อมต่อ
                    </td>
                </tr>
            `);
        }
    });

    // จัดการการกรองข้อมูล
    $('#historyDateFilter').on('change', function() {
        filterHistory();
    });

    $('#historyActionFilter').on('change', function() {
        filterHistory();
    });

    $('#historySearch').on('keyup', function() {
        filterHistory();
    });
}

// ฟังก์ชันกรองข้อมูลประวัติ
function filterHistory() {
    const dateFilter = $('#historyDateFilter').val();
    const actionFilter = $('#historyActionFilter').val();
    const searchText = $('#historySearch').val().toLowerCase();

    $('#historyTable tbody tr').each(function() {
        let show = true;
        const row = $(this);

        // กรองตามวันที่
        if (dateFilter) {
            const rowDate = row.find('td:first').text();
            if (rowDate.indexOf(dateFilter) === -1) {
                show = false;
            }
        }

        // กรองตามประเภทการกระทำ
        if (actionFilter) {
            const action = row.find('td:eq(1)').text().trim();
            if (action !== actionFilter) {
                show = false;
            }
        }

        // กรองตามข้อความค้นหา
        if (searchText) {
            const rowText = row.text().toLowerCase();
            if (rowText.indexOf(searchText) === -1) {
                show = false;
            }
        }

        row.toggle(show);
    });

    // แสดงข้อความเมื่อไม่พบข้อมูล
    const visibleRows = $('#historyTable tbody tr:visible').length;
    if (visibleRows === 0) {
        $('#historyTable tbody').append(`
            <tr class="no-results">
                <td colspan="4" class="text-center">ไม่พบข้อมูลที่ตรงกับเงื่อนไข</td>
            </tr>
        `);
    } else {
        $('#historyTable tr.no-results').remove();
    }
}

// เพิ่มฟังก์ชันล้างตัวกรองประวัติ
function clearHistoryFilters() {
    $('#historyDateFilter').val('');
    $('#historyActionFilter').val('');
    $('#historySearch').val('');
    filterHistory();
}

// อัพเดทฟังก์ชัน viewSpecialPermission
function viewSpecialPermission(id) {
    $.ajax({
        url: 'sql/get_special_permission_details.php',
        method: 'GET',
        data: { id: id },
        success: function(response) {
            if (response.success) {
                const data = response.data;
                
                // แสดงข้อมูลผู้ใช้
                $('#viewUserDetails').html(`
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>ชื่อ-สกุล:</strong> ${data.user.fullname}</p>
                            <p class="mb-2"><strong>ตำแหน่ง:</strong> ${data.user.position}</p>
                        </div>
                    </div>
                `);

                // แสดงข้อมูลสิทธิ์
                $('#viewPermissionDetails').html(`
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>สิทธิ์:</strong> ${data.permission.name}</p>
                            <p class="mb-2"><strong>หมวดหมู่:</strong> ${data.permission.category}</p>
                            <p class="mb-2"><strong>วันที่เริ่มต้น:</strong> ${data.start_date || '-'}</p>
                            <p class="mb-2"><strong>วันที่สิ้นสุด:</strong> ${data.end_date || 'ไม่มีกำหนด'}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>สถานะ:</strong> 
                                <span class="badge ${data.status.class}">
                                    ${data.status.text}
                                </span>
                            </p>
                            <p class="mb-2"><strong>ผู้อนุมัติ:</strong> ${data.granted_by}</p>
                            <p class="mb-2"><strong>วันที่อนุมัติ:</strong> ${data.created_at}</p>
                            <p class="mb-2"><strong>หมายเหตุ:</strong> ${data.note || '-'}</p>
                        </div>
                    </div>
                `);

                // แสดงประวัติการแก้ไข
                let historyHtml = '';
                if (data.history.length > 0) {
                    data.history.forEach(item => {
                        historyHtml += `
                            <tr>
                                <td>${item.date}</td>
                                <td>
                                    <span class="badge ${item.action_class}">
                                        ${item.action}
                                    </span>
                                </td>
                                <td>${item.performed_by}</td>
                                <td>${item.details}</td>
                            </tr>
                        `;
                    });
                } else {
                    historyHtml = `
                        <tr>
                            <td colspan="4" class="text-center">ไม่พบประวัติการแก้ไข</td>
                        </tr>
                    `;
                }
                $('#viewHistoryTable tbody').html(historyHtml);

                // ล้างตัวกรองก่อนแสดง Modal
                clearViewHistoryFilters();
                
                // แสดง Modal
                $('#viewPermissionModal').modal('show');
            } else {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถดึงข้อมูลได้', 'error');
            }
        },
        error: function() {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
        }
    });
}

// ฟังก์ชันแก้ไขสิทธิ์พิเศษ
function editSpecialPermission(id) {
    $.ajax({
        url: 'sql/get_special_permission_details.php',
        method: 'GET',
        data: { id: id },
        success: function(response) {
            if (response.success) {
                const data = response.data;
                
                // เติมข้อมูลในฟอร์มแก้ไข
                $('#editPermissionId').val(id);
                $('#editSpecialPermissionForm .modal-body').html(`
                    <div class="row g-3">
                        <div class="col-12">
                            <p><strong>ผู้ใช้งาน:</strong> ${data.user.fullname} (${data.user.position})</p>
                            <p><strong>สิทธิ์:</strong> ${data.permission.name}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">วันที่เริ่มต้น</label>
                            <input type="date" class="form-control" name="start_date" 
                                   value="${data.start_date_raw || ''}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">วันที่สิ้นสุด</label>
                            <input type="date" class="form-control" name="end_date" 
                                   value="${data.end_date_raw || ''}">
                            <small class="text-muted">ไม่ระบุ = ไม่มีกำหนด</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea class="form-control" name="note" rows="3">${data.note || ''}</textarea>
                        </div>
                    </div>
                `);

                // แสดง Modal
                $('#editPermissionModal').modal('show');
            } else {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถดึงข้อมูลได้', 'error');
            }
        },
        error: function() {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
        }
    });
}

// ฟังก์ชันยกเลิกสิทธิ์พิเศษ
function revokeSpecialPermission(id) {
    Swal.fire({
        title: 'ยืนยันการยกเลิกสิทธิ์',
        text: "คุณต้องการยกเลิกสิทธิ์พิเศษนี้ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, ยกเลิกสิทธิ์',
        cancelButtonText: 'ไม่, ยกเลิก',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6'
    }).then((result) => {
        if (result.isConfirmed) {
            // ขอเหตุผลในการยกเลิก
            Swal.fire({
                title: 'ระบุเหตุผลในการยกเลิก',
                input: 'textarea',
                inputPlaceholder: 'กรุณาระบุเหตุผล...',
                inputAttributes: {
                    'aria-label': 'กรุณาระบุเหตุผล'
                },
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                preConfirm: (reason) => {
                    if (!reason) {
                        Swal.showValidationMessage('กรุณาระบุเหตุผลในการยกเลิก');
                    }
                    return reason;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'sql/revoke_special_permission.php',
                        method: 'POST',
                        data: {
                            id: id,
                            reason: result.value
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'สำเร็จ',
                                    text: 'ยกเลิกสิทธิ์พิเศษเรียบร้อยแล้ว',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('ข้อผิดพลาด', response.message || 'ไม่สามารถยกเลิกสิทธิ์ได้', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
                        }
                    });
                }
            });
        }
    });
}

// จัดการการเลือกหมวดหมู่สิทธิ์
$('#permissionCategory').change(function() {
    const category = $(this).val();
    const permissionSelect = $('#permissionSelect');
    
    if (category) {
        // เรียกข้อมูลสิทธิ์ตามหมวดหมู่
        $.ajax({
            url: 'sql/get_permissions_by_category.php',
            method: 'GET',
            data: { category: category },
            success: function(response) {
                permissionSelect.html('<option value="">เลือกสิทธิ์</option>');
                response.forEach(function(perm) {
                    // ใช้ display_name แทน name เพื่อแสดงทั้งชื่อสิทธิ์และหน้าเพจ
                    const displayText = perm.display_name;
                    permissionSelect.append(
                        `<option value="${perm.id}" data-page="${perm.page || ''}">${displayText}</option>`
                    );
                });
                permissionSelect.prop('disabled', false);

                // เพิ่ม select2 เพื่อให้ดูสวยงามและค้นหาได้
                if ($.fn.select2) {
                    permissionSelect.select2({
                        width: '100%',
                        placeholder: 'เลือกสิทธิ์',
                        allowClear: true,
                        templateResult: formatPermissionOption,
                        templateSelection: formatPermissionOption
                    });
                }
            },
            error: function() {
                Swal.fire('ข้อผิดพลาด', 'ไม่สามารถดึงข้อมูลสิทธิ์ได้', 'error');
            }
        });
    } else {
        permissionSelect.html('<option value="">เลือกสิทธิ์</option>');
        permissionSelect.prop('disabled', true);
        if ($.fn.select2) {
            permissionSelect.select2('destroy');
        }
    }
});




// บันทึกการแก้ไขสิทธิ์พิเศษ
$('#editSpecialPermissionForm').submit(function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'sql/update_special_permission.php',
        method: 'POST',
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'บันทึกการแก้ไขเรียบร้อยแล้ว',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('ข้อผิดพลาด', response.message || 'ไม่สามารถบันทึกข้อมูลได้', 'error');
            }
        },
        error: function() {
            Swal.fire('ข้อผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
        }
    });
});
// เพิ่มฟังก์ชันสำหรับการกรองในหน้ารายละเอียด
function filterViewHistory() {
    const dateFilter = $('#viewHistoryDateFilter').val();
    const actionFilter = $('#viewHistoryActionFilter').val().toLowerCase();
    const searchText = $('#viewHistorySearch').val().toLowerCase();

    $('#viewHistoryTable tbody tr').each(function() {
        let show = true;
        const row = $(this);

        // กรองตามวันที่
        if (dateFilter) {
            const rowDate = row.find('td:first').text();
            if (!rowDate.includes(dateFilter)) {
                show = false;
            }
        }

        // กรองตามการดำเนินการ
        if (actionFilter) {
            const action = row.find('td:eq(1)').text().trim().toLowerCase();
            if (!action.includes(actionFilter)) {
                show = false;
            }
        }

        // กรองตามข้อความค้นหา
        if (searchText) {
            const rowText = row.text().toLowerCase();
            if (!rowText.includes(searchText)) {
                show = false;
            }
        }

        row.toggle(show);
    });

    // แสดงข้อความเมื่อไม่พบข้อมูล
    const visibleRows = $('#viewHistoryTable tbody tr:visible').length;
    if (visibleRows === 0) {
        $('#viewHistoryTable tbody').append(`
            <tr class="no-results">
                <td colspan="4" class="text-center">ไม่พบข้อมูลที่ตรงกับเงื่อนไข</td>
            </tr>
        `);
    } else {
        $('#viewHistoryTable tr.no-results').remove();
    }
}

// เพิ่ม event listeners สำหรับตัวกรอง
$('#viewHistoryDateFilter, #viewHistoryActionFilter').on('change', filterViewHistory);
$('#viewHistorySearch').on('keyup', filterViewHistory);

// ฟังก์ชันล้างตัวกรอง
function clearViewHistoryFilters() {
    $('#viewHistoryDateFilter').val('');
    $('#viewHistoryActionFilter').val('');
    $('#viewHistorySearch').val('');
    filterViewHistory();
}


</script>
  </body>
</html>
