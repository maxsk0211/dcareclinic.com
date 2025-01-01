<?php 
  session_start();
  
  include 'chk-session.php';
  require '../dbcon.php';
 ?>

<!doctype html>

<html
  lang="en"
  class="light-style layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="horizontal-menu-template-no-customizer-starter">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>จัดการเครื่องมือแพทย์ | dcareclinic.com</title>

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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css"> 
    <!-- SheetJS สำหรับ Export Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

    <!-- html2pdf สำหรับ Export PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
    body {
        background-color: #f8f9fa;
    }
    .container-xxl {
        animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 0 30px rgba(0,0,0,0.15);
    }
    .card-header {
        background-color: #4e73df;
        color: white;
        border-radius: 15px 15px 0 0;
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
    .btn {
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-info {
        background-color: #36b9cc;
        border-color: #36b9cc;
        color: white;
    }
    .btn-info:hover, .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .btn-warning {
        background-color: #f6c23e;
        border-color: #f6c23e;
        color: #333;
    }
    .table-responsive {
        border-radius: 15px;
        overflow: hidden;
    }
    .table {
        margin-bottom: 0;
    }
    .table thead th {
        background-color: #4e73df;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        padding: 15px;
    }
    .table tbody tr {
        transition: all 0.3s ease;
    }
    .table tbody tr:nth-of-type(even) {
        background-color: #f8f9fa;
    }
    .table tbody tr:hover {
        background-color: #e8eaf6;
        transform: scale(1.01);
    }
    .table td {
        vertical-align: middle;
        border: none;
        padding: 15px;
    }
    .table td a {
        color: #4e73df;
        transition: color 0.3s ease;
    }
    .table td a:hover {
        color: #224abe;
        text-decoration: none;
    }
    .badge {
        padding: 8px 12px;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 30px;
    }
    .badge-success {
        background-color: #1cc88a;
        color: white;
    }
    .badge-danger {
        background-color: #e74a3b;
        color: white;
    }
    .text-primary, .text-danger {
        transition: all 0.3s ease;
    }
    .text-primary:hover, .text-danger:hover {
        opacity: 0.8;
        transform: scale(1.1);
    }
    .modal-content {
        border-radius: 15px;
        box-shadow: 0 0 30px rgba(0,0,0,0.1);
    }
    .modal-header {
        background-color: #4e73df;
        color: white;
        border-radius: 15px 15px 0 0;
    }
    .modal-title {
        font-weight: 600;
    }
    .modal-footer {
        border-top: none;
    }
    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #ced4da;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.25);
        border-color: #4e73df;
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
              <!-- Tools List Table -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-white">ข้อมูลเครื่องมือแพทย์ในระบบทั้งหมด</h5>
                    <div>
                        <button type="button" class="btn btn-danger me-2" onclick="showToolHistory()">
                            <i class="ri-history-line me-1"></i> ประวัติการเปลี่ยนแปลง
                        </button>
                        <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#addToolModal">
                            <i class="ri-add-line me-1"></i> เพิ่มเครื่องมือ
                        </button>
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                            <i class="ri-scales-line me-1"></i> จัดการหน่วยนับ
                        </button>
                    </div>
                </div>
            </div>

                <div class="modal fade" id="addToolModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white">เพิ่มเครื่องมือใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="sql/tool-insert.php" method="post">
                    <div class="mb-3">
                        <label for="tool_name" class="form-label">ชื่อเครื่องมือ</label>
                        <input type="text" class="form-control" id="tool_name" name="tool_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="tool_detail" class="form-label">รายละเอียดเครื่องมือ</label>
                        <textarea class="form-control" id="tool_detail" name="tool_detail" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="branch_id" class="form-label">สาขา</label>
                            <select class="form-select" id="branch_id" name="branch_id" required>
                                <option value="">เลือกสาขา</option>
                                <?php 
                                $branch_id=$_SESSION['branch_id'];
                                $branch_sql = "SELECT * FROM branch where branch_id='$branch_id'";
                                $branch_result = mysqli_query($conn, $branch_sql);
                                while($branch = mysqli_fetch_object($branch_result)) { ?>
                                    <option value="<?php echo $branch->branch_id; ?>"><?php echo $branch->branch_name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tool_unit_id" class="form-label">หน่วยนับ</label>
                            <select class="form-select" id="tool_unit_id" name="tool_unit_id" required>
                                <option value="">เลือกหน่วยนับ</option>
                                <?php 
                                $unit_sql = "SELECT * FROM unit";
                                $unit_result = mysqli_query($conn, $unit_sql);
                                while ($unit = mysqli_fetch_object($unit_result)) { ?>
                                    <option value="<?php echo $unit->unit_id; ?>"><?php echo $unit->unit_name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tool_status" class="form-label">สถานะ</label>
                        <select class="form-select" id="tool_status" name="tool_status" required>
                            <option value="1">พร้อมใช้งาน</option>
                            <option value="0">ไม่พร้อมใช้งาน</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

                <!-- Modal for managing units -->
                <div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title text-white" id="addUnitModalLabel">จัดการหน่วยนับ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-6">
                            <h6>รายการหน่วยนับ</h6>
                            <table class="table">
                              <thead>
                                <tr>
                                  <th>รหัส</th>
                                  <th>ชื่อหน่วยนับ</th>
                                  <th>จัดการ</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php 
                                $sql = "SELECT * FROM unit";
                                $result = mysqli_query($conn, $sql);
                                while($row = mysqli_fetch_object($result)) { ?>
                                <tr>
                                  <td><?php echo $row->unit_id; ?></td>
                                  <td><?php echo $row->unit_name; ?></td>
                                  <td>
                                    <a href="" class="text-danger" onClick="confirmDelete('sql/unit-delete.php?id=<?php echo $row->unit_id; ?>'); return false;">
                                      <i class="ri-delete-bin-6-line"></i>
                                    </a>
                                  </td>
                                </tr>
                                <?php } ?>
                                <?php if (mysqli_num_rows($result) == 0) { ?>
                                <tr>
                                  <td colspan="3">ไม่พบข้อมูล</td>
                                </tr>
                                <?php } ?>
                              </tbody>
                            </table>
                          </div>
                          <div class="col-md-6">
                            <h6>เพิ่มหน่วยนับใหม่</h6>
                            <form action="sql/unit-insert.php" method="post">
                              <div class="mb-3">
                                <label for="unit_name" class="form-label">ชื่อหน่วยนับ</label>
                                <input type="text" class="form-control" id="unit_name" name="unit_name" required>
                              </div>
                              <button type="submit" class="btn btn-primary">บันทึก</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card">
    <div class="card-body">
        <div class="text-end mb-2">
            <button type="button" class="btn btn-primary me-2" onclick="showStockReport()">
                <i class="ri-file-list-3-line me-1"></i> รายงานสต็อค
            </button>
            <button type="button" class="btn btn-info" onclick="showTransactionReport()">
                <i class="ri-exchange-line me-1"></i> รายงานการเบิกเครื่องมือ
            </button>
        </div>
        <div class="table-responsive">
            <table id="toolTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>รหัสเครื่องมือ</th>
                        <th>สาขา</th>
                        <th>ชื่อเครื่องมือ</th>
                        <th>รายละเอียด</th>
                        <th>จำนวนคงเหลือ/หน่วยนับ</th>
                        <th>สถานะ</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $branch_id = $_SESSION['branch_id'];
                        $sql = "SELECT t.*, u.unit_name ,b.*
                                FROM tool t
                                LEFT JOIN branch b ON t.branch_id = b.branch_id
                                LEFT JOIN unit u ON t.tool_unit_id = u.unit_id
                                where b.branch_id='$branch_id'
                                ORDER BY t.tool_id DESC";
                        $result = mysqli_query($conn, $sql);

                        function formatToolId($id) {
                          if (!is_numeric($id)) {
                            return "Error: Input must be numeric.";
                          }
                          $idString = (string)$id;
                          $formattedId = 'TOOL-' . str_pad($idString, 6, '0', STR_PAD_LEFT);
                          return $formattedId;
                        }
                     while($row = mysqli_fetch_object($result)) { ?>
                    <tr>
                        <td><a href="tool-detail.php?tool_id=<?= $row->tool_id; ?>"><?php echo formatToolId($row->tool_id); ?></a></td>
                        <td><a href="tool-detail.php?tool_id=<?= $row->tool_id; ?>"><?php echo $row->branch_name; ?></a></td>
                        <td><a href="tool-detail.php?tool_id=<?= $row->tool_id; ?>"><?php echo $row->tool_name; ?></a></td>
                        <td><a href="tool-detail.php?tool_id=<?= $row->tool_id; ?>"><?php echo mb_strimwidth($row->tool_detail, 0, 50, "..."); ?></a></td>
                        <td><?php echo $row->tool_amount." ".$row->unit_name; ?></td>
                        <td>
                            <?php if ($row->tool_status == 1): ?>
                                <span class="badge badge-success">พร้อมใช้งาน</span>
                            <?php else: ?>
                                <span class="badge badge-danger">ไม่พร้อมใช้งาน</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="#" class="text-primary me-2" data-bs-toggle="modal" data-bs-target="#editToolModal<?php echo $row->tool_id; ?>">
                                <i class="ri-edit-line"></i>
                            </a>
                            <a href="#" class="text-danger" onclick="confirmDelete(<?php echo $row->tool_id; ?>); return false;">
                                <i class="ri-delete-bin-6-line"></i>
                            </a>
                        </td>
                    </tr>
                        <!-- Modal for editing tool -->
                        <div class="modal fade" id="editToolModal<?php echo $row->tool_id; ?>" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="editToolModalLabel">แก้ไขข้อมูลเครื่องมือ</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <form action="sql/tool-update.php" method="post">
                                  <input type="hidden" name="tool_id" value="<?php echo $row->tool_id; ?>">
                                  <div class="row">
                                    <div class="col mb-3">
                                      <label for="edit_tool_name" class="form-label">ชื่อเครื่องมือ</label>
                                      <input type="text" id="edit_tool_name" name="tool_name" class="form-control" value="<?php echo $row->tool_name; ?>" required />
                                    </div>
                                  </div>
                                  <div class="row">
                                    <div class="col mb-3">
                                      <label for="edit_tool_detail" class="form-label">รายละเอียดเครื่องมือ</label>
                                      <textarea id="edit_tool_detail" name="tool_detail" class="form-control"><?php echo $row->tool_detail; ?></textarea>
                                    </div>
                                  </div>
                                  <div class="row g-2">
                                    <div class="col mb-3">
                                      <label for="edit_branch_id" class="form-label">สาขา</label>
                                      <select id="edit_branch_id" name="branch_id" class="form-select" required>
                                        <?php
                                        if($_SESSION['position_id']==1){
                                          $branch_sql = "SELECT * FROM branch";
                                        }else{
                                          $branch_id=$_SESSION['branch_id'];
                                          $branch_sql = "SELECT * FROM branch where branch_id='$branch_id'";
                                        }
                                        $branch_result = mysqli_query($conn, $branch_sql);
                                        while ($branch = mysqli_fetch_object($branch_result)) {
                                          $selected = ($row->branch_id == $branch->branch_id) ? 'selected' : '';
                                          echo "<option value='{$branch->branch_id}' {$selected}>{$branch->branch_name}</option>";
                                        }
                                        ?>
                                      </select>
                                    </div>
<!--                                     <div class="col mb-3">
                                      <label for="edit_tool_amount" class="form-label">จำนวน</label>
                                      <input type="number" id="edit_tool_amount" name="tool_amount" class="form-control" value="<?php echo $row->tool_amount; ?>" required />
                                    </div> -->
                                    <div class="col mb-3">
                                      <label for="edit_tool_unit_id" class="form-label">หน่วยนับ</label>
                                      <select id="edit_tool_unit_id" name="tool_unit_id" class="form-select" required>
                                        <?php
                                        $unit_sql = "SELECT * FROM unit";
                                        $unit_result = mysqli_query($conn, $unit_sql);
                                        while ($unit = mysqli_fetch_object($unit_result)) {
                                          $selected = ($row->tool_unit_id == $unit->unit_id) ? 'selected' : '';
                                          echo "<option value='{$unit->unit_id}' {$selected}>{$unit->unit_name}</option>";
                                        }
                                        ?>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="row">
                                    <div class="col mb-3">
                                      <label for="edit_tool_status" class="form-label">สถานะ</label>
                                      <select id="edit_tool_status" name="tool_status" class="form-select" required>
                                        <option value="1" <?php echo ($row->tool_status == 1) ? 'selected' : ''; ?>>พร้อมใช้งาน</option>
                                        <option value="0" <?php echo ($row->tool_status == 0) ? 'selected' : ''; ?>>ไม่พร้อมใช้งาน</option>
                                      </select>
                                    </div>
                                  </div>
                                  <div class="row">
                                    <div class="col mb-3">
                                      <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                    </div>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
              </div>
            </div>
            <!--/ Content -->

            <!-- Footer -->
            <?php include 'footer.php'; ?>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!--/ Content wrapper -->
        </div>
        <!--/ Layout container -->
      </div>
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>

    <!--/ Layout wrapper -->

<!-- Modal สำหรับแสดง Activity Logs -->
<div class="modal fade" id="activityLogsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ประวัติการเปลี่ยนแปลงข้อมูลเครื่องมือแพทย์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <select id="actionFilter" class="form-select">
                        <option value="">ทั้งหมด</option>
                        <option value="create">เพิ่มข้อมูล</option>
                        <option value="update">แก้ไขข้อมูล</option>
                        <option value="delete">ลบข้อมูล</option>
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>วันที่และเวลา</th>
                                <th>การกระทำ</th>
                                <th>รหัสเครื่องมือ/ชื่อ</th>
                                <th>รายละเอียด</th>
                                <th>ผู้ดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody id="toolHistoryTableBody">
                            <!-- ข้อมูลจะถูกเพิ่มด้วย JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับรายงานสต็อค -->
<div class="modal fade" id="stockReportModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">รายงานสต็อคเครื่องมือคงเหลือ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- ส่วนฟิลเตอร์ -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label">สถานะคงเหลือ</label>
                        <select class="form-select" id="stockStatusFilter">
                            <option value="">ทั้งหมด</option>
                            <option value="low">ต่ำกว่าเกณฑ์</option>
                            <option value="normal">ปกติ</option>
                            <option value="out">หมด</option>
                        </select>
                    </div>
                </div>

                <!-- ส่วนสรุป -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6>จำนวนรายการทั้งหมด</h6>
                                <h3 id="totalItems">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6>มูลค่ารวม</h6>
                                <h3 id="totalValue">0.00</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h6>รายการต่ำกว่าเกณฑ์</h6>
                                <h3 id="lowStockItems">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h6>รายการที่หมด</h6>
                                <h3 id="outOfStockItems">0</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ตารางรายละเอียด -->
                <div class="table-responsive">
                    <table id="stockReportTable" class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>รหัสเครื่องมือ</th>
                                <th>ชื่อเครื่องมือ</th>
                                <th>คงเหลือ</th>
                                <th>หน่วยนับ</th>
                                <th>ต้นทุน/หน่วย</th>
                                <th>มูลค่ารวม</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- ข้อมูลจะถูกเพิ่มด้วย JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- ปุ่ม Export -->
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-success me-2" onclick="exportToExcel()">
                        <i class="ri-file-excel-2-line me-1"></i> Export Excel
                    </button>
                    <button type="button" class="btn btn-danger" onclick="exportToPDF()">
                        <i class="ri-file-pdf-line me-1"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal สำหรับรายงานการเบิก -->
<div class="modal fade" id="transactionReportModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">รายงานการเบิกเครื่องมือเข้า-ออก</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- ส่วนฟิลเตอร์ -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label">วันที่เริ่มต้น</label>
                        <input type="date" class="form-control" id="transStartDate">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">วันที่สิ้นสุด</label>
                        <input type="date" class="form-control" id="transEndDate">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">ประเภทรายการ</label>
                        <select class="form-select" id="transactionType">
                            <option value="">ทั้งหมด</option>
                            <option value="in">รับเข้า</option>
                            <option value="out">เบิกออก</option>
                        </select>
                    </div>
                </div>

                <!-- ส่วนสรุป -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6>จำนวนรายการทั้งหมด</h6>
                                <h3 id="totalTransactions">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6>มูลค่ารวมรับเข้า</h6>
                                <h3 id="totalInValue">0.00</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h6>มูลค่ารวมเบิกออก</h6>
                                <h3 id="totalOutValue">0.00</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6>มูลค่าคงเหลือ</h6>
                                <h3 id="netValue">0.00</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ตารางรายละเอียด -->
                <div class="table-responsive">
                    <table id="transactionTable" class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>วันที่-เวลา</th>
                                <th>รหัสเครื่องมือ</th>
                                <th>ชื่อเครื่องมือ</th>
                                <th>ประเภทรายการ</th>
                                <th>จำนวน</th>
                                <th>หน่วยนับ</th>
                                <th>ราคา/หน่วย</th>
                                <th>มูลค่ารวม</th>
                                <th>ผู้ดำเนินการ</th>
                                <th>หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- ข้อมูลจะถูกเพิ่มด้วย JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- ปุ่ม Export -->
                <div class="text-end mt-3">
                    <button type="button" class="btn btn-success me-2" onclick="exportTransactionToExcel()">
                        <i class="ri-file-excel-2-line me-1"></i> Export Excel
                    </button>
                    <button type="button" class="btn btn-danger" onclick="exportTransactionToPDF()">
                        <i class="ri-file-pdf-line me-1"></i> Export PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- sweet Alerts 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- datatables -->
    <!-- datatables -->
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/vendor/libs/cleavejs/cleave.js"></script>
    <script src="../assets/vendor/libs/cleavejs/cleave-phone.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>



    <script>
        function showToolHistory() {
    const tableBody = $('#toolHistoryTableBody');
    tableBody.html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary"></div></td></tr>');
    
    $('#activityLogsModal').modal('show');
    loadToolHistory();
}

function loadToolHistory(action = '') {
    $.ajax({
        url: 'sql/get-tool-history.php',
        type: 'GET',
        data: { action: action },
        success: function(response) {
            if (response.success) {
                updateToolHistoryTable(response.data);
            } else {
                $('#toolHistoryTableBody').html(`
                    <tr>
                        <td colspan="5" class="text-center text-danger">
                            ${response.message || 'เกิดข้อผิดพลาดในการโหลดข้อมูล'}
                        </td>
                    </tr>
                `);
            }
        },
        error: function() {
            $('#toolHistoryTableBody').html(`
                <tr>
                    <td colspan="5" class="text-center text-danger">
                        ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้
                    </td>
                </tr>
            `);
        }
    });
}

function updateToolHistoryTable(data) {
    const tableBody = $('#toolHistoryTableBody');
    tableBody.empty();

    if (!data || data.length === 0) {
        tableBody.html(`
            <tr>
                <td colspan="5" class="text-center">ไม่พบประวัติการเปลี่ยนแปลง</td>
            </tr>
        `);
        return;
    }

    data.forEach(item => {
        const actionMap = {
            'create': '<span class="badge bg-success">เพิ่มข้อมูล</span>',
            'update': '<span class="badge bg-warning">แก้ไขข้อมูล</span>',
            'delete': '<span class="badge bg-danger">ลบข้อมูล</span>'
        };

        let detailsHtml = '';
        let toolInfo = '';
        
        if (item.action === 'update' && item.details.changes) {
            detailsHtml = '<ul class="mb-0">';
            Object.entries(item.details.changes).forEach(([field, change]) => {
                detailsHtml += `<li><strong>${field}:</strong> ${change.from} ➜ ${change.to}</li>`;
            });
            detailsHtml += '</ul>';
            // กรณี update ใช้ชื่อจาก details
            toolInfo = item.details.tool_name || '';
        } else if (item.action === 'delete') {
            detailsHtml = `<strong>เหตุผล:</strong> ${item.details.reason}`;
            // กรณีลบ ใช้ข้อมูลจาก deleted_data
            if (item.details.deleted_data) {
                toolInfo = `TOOL-${String(item.details.deleted_data.tool_id).padStart(6, '0')} ${item.details.deleted_data.tool_name}`;
            }
        } else if (item.action === 'create') {
            // กรณีสร้างใหม่
            toolInfo = item.details.tool_name;
            detailsHtml = `<strong>เพิ่มเครื่องมือใหม่:</strong> ${item.details.tool_name}`;
        }

        tableBody.append(`
            <tr>
                <td>${item.created_at}</td>
                <td>${actionMap[item.action]}</td>
                <td>${toolInfo}</td>
                <td>${detailsHtml}</td>
                <td>${item.users_fname} ${item.users_lname}</td>
            </tr>
        `);
    });
}

// Event listener สำหรับ filter
$('#actionFilter').change(function() {
    loadToolHistory($(this).val());
});


      // DataTable initialization
      $(document).ready(function() {
        $('#toolTable').DataTable({
          language: {
            "lengthMenu": "แสดง _MENU_ แถวต่อหน้า",
            "zeroRecords": "ไม่พบข้อมูล",
            "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
            "infoEmpty": "ไม่มีข้อมูล",
            "infoFiltered": "(กรองข้อมูลจาก _MAX_ รายการทั้งหมด)",
            "search": "ค้นหา:",
            "paginate": {
              "first": "หน้าแรก",
              "last": "หน้าสุดท้าย",
              "next": "ถัดไป",
              "previous": "ก่อนหน้า"
            }
          },
          lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "ทั้งหมด"]],
          pagingType: 'full_numbers',
           responsive: true
        });


    $('#activityLogsTable').DataTable({
        language: {
            "lengthMenu": "แสดง _MENU_ แถวต่อหน้า",
            "zeroRecords": "ไม่พบข้อมูล",
            "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
            "infoEmpty": "ไม่มีข้อมูล",
            "search": "ค้นหา:",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            }
        },
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "ทั้งหมด"]],
    });

    // กรองข้อมูลตามประเภทการกระทำ
    $('#actionFilter').on('change', function() {
        var action = $(this).val().toLowerCase();
        $('#activityLogsTable').DataTable().column(2).search(action).draw();
    });
});

      // Delete confirmation
function confirmDelete(toolId) {
    Swal.fire({
        title: 'ยืนยันการลบข้อมูล',
        html: `
            <form id="deleteForm">
                <div class="mb-3">
                    <label for="password" class="form-label">กรุณายืนยันรหัสผ่าน:</label>
                    <input type="password" class="form-control" id="password" required>
                </div>
                <div class="mb-3">
                    <label for="reason" class="form-label">เหตุผลในการลบ:</label>
                    <textarea class="form-control" id="reason" rows="3" 
                             placeholder="กรุณาระบุเหตุผลในการลบ" required></textarea>
                </div>
            </form>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ยืนยันการลบ',
        cancelButtonText: 'ยกเลิก',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const password = document.getElementById('password').value;
            const reason = document.getElementById('reason').value;
            
            if (!password || !reason) {
                Swal.showValidationMessage('กรุณากรอกข้อมูลให้ครบถ้วน');
                return false;
            }

            const formData = new FormData();
            formData.append('tool_id', toolId);
            formData.append('password', password);
            formData.append('reason', reason);

            return fetch('sql/tool-delete.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .catch(error => {
                throw new Error('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'ลบข้อมูลเครื่องมือเรียบร้อยแล้ว',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: result.value.message || 'ไม่สามารถลบข้อมูลได้'
                });
            }
        }
    });
}



//////////////////////////////////////////////////////
// ฟังก์ชันแสดง Modal รายงาน
function showStockReport() {
    loadStockData();
    $('#stockReportModal').modal('show');
}

// โหลดข้อมูลสต็อค
function loadStockData() {
    const filters = {
        stock_type: 'tool',
        stockStatus: $('#stockStatusFilter').val()
    };

    $.ajax({
        url: 'sql/get-stock-report.php',
        type: 'GET',
        data: filters,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (response.summary) updateSummary(response.summary);
                if (response.items) updateTable(response.items);
            } else {
                console.error('Error:', response.message);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถโหลดข้อมูลได้'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์'
            });
        }
    });
}

// อัพเดทข้อมูลสรุป
function updateSummary(summary) {
    if (!summary) return;
    
    $('#totalItems').text(summary.totalItems || 0);
    $('#totalValue').text(
        (summary.totalValue || 0).toLocaleString('th-TH', {
            style: 'currency',
            currency: 'THB'
        })
    );
    $('#lowStockItems').text(summary.lowStockItems || 0);
    $('#outOfStockItems').text(summary.outOfStockItems || 0);
}

// อัพเดทตาราง
function updateTable(items) {
    const tbody = $('#stockReportTable tbody');
    tbody.empty();

    if (!items || items.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="7" class="text-center">ไม่พบข้อมูล</td>
            </tr>
        `);
        return;
    }

    items.forEach(item => {
        const amount = parseFloat(item.amount || item.tool_amount || 0);
        const cost = parseFloat(item.cost || item.tool_cost || 0);
        const totalValue = amount * cost;

        const row = `
            <tr>
                <td>TOOL-${String(item.tool_id).padStart(6, '0')}</td>
                <td>${item.tool_name}</td>
                <td class="text-end">${amount.toLocaleString()}</td>
                <td>${item.unit_name}</td>
                <td class="text-end">${cost.toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="text-end">${totalValue.toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td>${getStatusBadge(amount)}</td>
            </tr>
        `;
        tbody.append(row);
    });
}

// ฟังก์ชันแสดงสถานะ
function getStatusBadge(amount) {
    if (amount <= 0) {
        return '<span class="badge bg-danger">หมด</span>';
    } else if (amount < 10) {
        return '<span class="badge bg-warning">ต่ำกว่าเกณฑ์</span>';
    }
    return '<span class="badge bg-success">ปกติ</span>';
}

// Event Listeners
$('#stockStatusFilter').change(loadStockData);
// ฟังก์ชันแสดง Modal รายงาน
function showTransactionReport() {
    loadTransactionData();
    $('#transactionReportModal').modal('show');
}

// โหลดข้อมูลการเบิก
function loadTransactionData() {
    const filters = {
        startDate: $('#transStartDate').val(),
        endDate: $('#transEndDate').val(),
        transactionType: $('#transactionType').val(),
        stock_type: 'tool'
    };

    $.ajax({
        url: 'sql/get-transaction-report.php',
        type: 'GET',
        data: filters,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateTransactionSummary(response.summary);
                updateTransactionTable(response.items);
            } else {
                console.error('Error:', response.message);
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: response.message || 'ไม่สามารถโหลดข้อมูลได้'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์'
            });
        }
    });
}

// อัพเดทข้อมูลสรุปการเบิก
function updateTransactionSummary(summary) {
    if (!summary) return;
    
    $('#totalTransactions').text(summary.totalTransactions.toLocaleString());
    $('#totalInValue').text(summary.totalInValue.toLocaleString('th-TH', {
        style: 'currency',
        currency: 'THB'
    }));
    $('#totalOutValue').text(summary.totalOutValue.toLocaleString('th-TH', {
        style: 'currency',
        currency: 'THB'
    }));
    $('#netValue').text(summary.netValue.toLocaleString('th-TH', {
        style: 'currency',
        currency: 'THB'
    }));
}

// เพิ่มฟังก์ชัน getBadgeClass ตรงนี้
function getBadgeClass(type) { 
    switch(type) { 
        case 'รับเข้า': 
            return 'bg-success'; // สีเขียว 
        case 'เบิกออก': 
            return 'bg-danger'; // สีแดง 
        case 'ใช้ในคอร์ส': 
            return 'bg-info'; // สีฟ้า 
        default: 
            return 'bg-secondary'; 
    } 
}

// อัพเดทตารางการเบิก
function updateTransactionTable(items) {
    const tbody = $('#transactionTable tbody');
    tbody.empty();

    if (!items || items.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="10" class="text-center">ไม่พบข้อมูล</td>
            </tr>
        `);
        return;
    }

    items.forEach(item => {
        const badgeClass = getBadgeClass(item.transaction_type_name);

        const row = `
            <tr>
                <td>${formatDateTime(item.transaction_date)}</td>
                <td>TOOL-${String(item.related_id).padStart(6, '0')}</td>
                <td>${item.item_name}</td>
                <td><span class="badge ${badgeClass}">${item.transaction_type_name}</span></td>
                <td class="text-end">${formatNumber(item.display_quantity)}</td>
                <td>${item.unit_name}</td>
                <td class="text-end">${formatNumber(item.cost_per_unit)}</td>
                <td class="text-end">${formatNumber(item.total_value)}</td>
                <td>${item.users_fname} ${item.users_lname}</td>
                <td>${item.notes}</td>
            </tr>
        `;
        tbody.append(row);
    });
}

// ฟังก์ชันช่วยจัดรูปแบบ
function formatDateTime(dateTimeStr) {
    const date = new Date(dateTimeStr);
    return date.toLocaleString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });
}

function formatNumber(number, decimals = 2) {
    return Number(number).toLocaleString('th-TH', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

function formatNote(note) {
    if (!note) return '-';
    
    // กรณีเป็นการคืนสต็อก
    if (note.includes('คืนสต็อกจากการยกเลิกชำระเงิน')) {
        return note.replace(
            'คืนสต็อกจากการยกเลิกชำระเงิน ORDER-', 
            'รับเข้า - ยกเลิกการสั่งซื้อ รหัสสินค้า: '
        );
    }
    
    // กรณีเป็นการใช้ในคอร์ส
    if (note.includes('ตัดสต็อกจากการใช้บริการ')) {
        return note.replace(
            'ตัดสต็อกจากการใช้บริการ ORDER-', 
            'เบิกออก - ใช้ในคอร์ส รหัสสินค้า: '
        );
    }
    
    // กรณีอื่นๆ
    return note;
}

// Event Listeners
$('#transactionType, #transStartDate, #transEndDate').change(loadTransactionData);
// Export to Excel
function exportToExcel() {
    const wb = XLSX.utils.book_new();
    
    // สร้าง worksheet สำหรับสรุป
    const summaryData = [
        ['รายงานสต็อคเครื่องมือคงเหลือ'],
        ['วันที่ออกรายงาน:', new Date().toLocaleString('th-TH')],
        [''],
        ['สรุปภาพรวม'],
        ['จำนวนรายการทั้งหมด:', $('#totalItems').text()],
        ['มูลค่ารวม:', $('#totalValue').text()],
        ['รายการต่ำกว่าเกณฑ์:', $('#lowStockItems').text()],
        ['รายการที่หมด:', $('#outOfStockItems').text()]
    ];
    
    const wsSummary = XLSX.utils.aoa_to_sheet(summaryData);
    XLSX.utils.book_append_sheet(wb, wsSummary, "สรุป");

    // สร้าง worksheet สำหรับรายละเอียด
    const table = document.getElementById('stockReportTable');
    const wsDetails = XLSX.utils.table_to_sheet(table);
    XLSX.utils.book_append_sheet(wb, wsDetails, "รายละเอียด");

    // บันทึกไฟล์
    XLSX.writeFile(wb, `stock_report_${new Date().toISOString().slice(0,10)}.xlsx`);
}

// Export to PDF
async function exportToPDF() {
    try {
        Swal.fire({
            title: 'กำลังสร้าง PDF',
            html: 'กรุณารอสักครู่...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const currentDate = new Date().toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        const content = `
            <div style="font-family: 'Sarabun', sans-serif;">
                <!-- ส่วนหัว -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">รายงานสต็อคเครื่องมือแพทย์คงเหลือ</h2>
                    <p style="margin: 5px 0;">วันที่ออกรายงาน: ${currentDate}</p>
                </div>

                <!-- เงื่อนไขการกรอง -->
                <div style="margin-bottom: 15px; font-size: 14px;">
                    <p>เงื่อนไขการกรอง:</p>
                    <table style="width: 100%; margin-bottom: 10px;">
                        <tr>
                            <td>สถานะคงเหลือ: ${$('#stockStatusFilter option:selected').text()}</td>
                        </tr>
                    </table>
                </div>

                <!-- ส่วนสรุป -->
                <div style="margin-bottom: 20px;">
                    <h3 style="margin-bottom: 10px;">สรุปภาพรวม</h3>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                        <tr style="background-color: #f8f9fa;">
                            <td style="padding: 8px; border: 1px solid #ddd; width: 25%;">จำนวนรายการทั้งหมด:</td>
                            <td style="padding: 8px; border: 1px solid #ddd; width: 25%;"><strong>${$('#totalItems').text()}</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd; width: 25%;">มูลค่ารวม:</td>
                            <td style="padding: 8px; border: 1px solid #ddd; width: 25%;"><strong>${$('#totalValue').text()}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">รายการต่ำกว่าเกณฑ์:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><strong>${$('#lowStockItems').text()}</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">รายการที่หมด:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><strong>${$('#outOfStockItems').text()}</strong></td>
                        </tr>
                    </table>
                </div>

                <!-- ตารางข้อมูล -->
                <div style="margin-bottom: 20px;">
                    <h3 style="margin-bottom: 10px;">รายละเอียดสต็อค</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <thead>
                            <tr style="background-color: #4e73df; color: white;">
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">รหัสเครื่องมือ</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">ชื่อเครื่องมือ</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">คงเหลือ</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">ต้นทุน/หน่วย</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">มูลค่ารวม</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${Array.from($('#stockReportTable tbody tr')).map(row => `
                                <tr>
                                    <td style="border: 1px solid #ddd; padding: 8px;">${$(row).find('td').eq(0).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">${$(row).find('td').eq(1).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${$(row).find('td').eq(2).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${$(row).find('td').eq(4).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${$(row).find('td').eq(5).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                        ${getStatusForPDF($(row).find('td').eq(6).text())}
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>

                <!-- ลายเซ็นต์ -->
                <div style="margin-top: 30px; text-align: right;">
                    <p>ผู้ออกรายงาน: ................................................</p>
                    <p style="margin-top: 5px;">(${$('#currentUserName').text()})</p>
                    <p style="margin-top: 5px;">วันที่: ${currentDate}</p>
                </div>
            </div>
        `;

        const opt = {
            margin: 10,
            filename: `stock_report_${new Date().toISOString().slice(0, 10)}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { 
                scale: 2,
                useCORS: true,
                logging: true
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'landscape'
            },
            pagebreak: { mode: 'avoid-all' }
        };

        // สร้าง PDF
        const pdf = await html2pdf().set(opt).from(content).save();
        
        // ปิด loading และแสดงข้อความสำเร็จ
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ',
            text: 'สร้างไฟล์ PDF เรียบร้อยแล้ว',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('PDF Export Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถสร้างไฟล์ PDF ได้'
        });
    }
}

// ฟังก์ชันช่วยจัดรูปแบบสถานะสำหรับ PDF
function getStatusForPDF(status) {
    const statusText = status.toLowerCase();
    if (statusText.includes('หมด')) {
        return `<span style="color: #dc3545; font-weight: bold;">หมด</span>`;
    } else if (statusText.includes('ต่ำกว่าเกณฑ์')) {
        return `<span style="color: #ffc107; font-weight: bold;">ต่ำกว่าเกณฑ์</span>`;
    }
    return `<span style="color: #28a745; font-weight: bold;">ปกติ</span>`;
}


async function exportTransactionToPDF() {
    try {
        // แสดง loading
        Swal.fire({
            title: 'กำลังสร้าง PDF',
            html: 'กรุณารอสักครู่...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const currentDate = new Date().toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        const content = `
            <div style="font-family: 'Sarabun', sans-serif;">
                <!-- ส่วนหัว -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">รายงานการเบิกเครื่องมือเข้า-ออก</h2>
                    <p style="margin: 5px 0;">วันที่ออกรายงาน: ${currentDate}</p>
                </div>

                <!-- เงื่อนไขการกรอง -->
                <div style="margin-bottom: 15px; font-size: 14px;">
                    <p>เงื่อนไขการกรอง:</p>
                    <table style="width: 100%; margin-bottom: 10px;">
                        <tr>
                            <td>วันที่เริ่มต้น: ${$('#transStartDate').val() || '-'}</td>
                            <td>วันที่สิ้นสุด: ${$('#transEndDate').val() || '-'}</td>
                        </tr>
                        <tr>
                            <td>ประเภทรายการ: ${$('#transactionType option:selected').text()}</td>
                        </tr>
                    </table>
                </div>

                <!-- ส่วนสรุป -->
                <div style="margin-bottom: 20px;">
                    <h3 style="margin-bottom: 10px;">สรุปภาพรวม</h3>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                        <tr style="background-color: #f8f9fa;">
                            <td style="padding: 8px; border: 1px solid #ddd; width: 25%;">จำนวนรายการทั้งหมด:</td>
                            <td style="padding: 8px; border: 1px solid #ddd; width: 25%;"><strong>${$('#totalTransactions').text()}</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd; width: 25%;">มูลค่าสุทธิ:</td>
                            <td style="padding: 8px; border: 1px solid #ddd; width: 25%;"><strong>${$('#netValue').text()}</strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">มูลค่ารวมรับเข้า:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><strong>${$('#totalInValue').text()}</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">มูลค่ารวมเบิกออก:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><strong>${$('#totalOutValue').text()}</strong></td>
                        </tr>
                    </table>
                </div>

                <!-- ตารางข้อมูล -->
                <div style="margin-bottom: 20px;">
                    <h3 style="margin-bottom: 10px;">รายการเบิกเครื่องมือ</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <thead>
                            <tr style="background-color: #4e73df; color: white;">
                                <th style="border: 1px solid #ddd; padding: 8px;">วันที่-เวลา</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">รหัสเครื่องมือ</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">ชื่อเครื่องมือ</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">ประเภทรายการ</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">จำนวน</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">ราคา/หน่วย</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">มูลค่ารวม</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">ผู้ดำเนินการ</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">หมายเหตุ</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${Array.from($('#transactionTable tbody tr')).map(row => `
                                <tr>
                                    <td style="border: 1px solid #ddd; padding: 8px;">${$(row).find('td').eq(0).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">${$(row).find('td').eq(1).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">${$(row).find('td').eq(2).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                        ${getTransactionTypeBadge($(row).find('td').eq(3).text())}
                                    </td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${$(row).find('td').eq(4).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${$(row).find('td').eq(6).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${$(row).find('td').eq(7).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">${$(row).find('td').eq(8).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">${$(row).find('td').eq(9).text()}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>

                <!-- ลายเซ็นต์ -->
                <div style="margin-top: 30px; text-align: right;">
                    <p>ผู้ออกรายงาน: ................................................</p>
                    <p style="margin-top: 5px;">(${$('#currentUserName').text()})</p>
                    <p style="margin-top: 5px;">วันที่: ${currentDate}</p>
                </div>
            </div>
        `;

        const opt = {
            margin: 10,
            filename: `transaction_report_${new Date().toISOString().slice(0, 10)}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { 
                scale: 2,
                useCORS: true,
                logging: true
            },
            jsPDF: {
                unit: 'mm',
                format: 'a4',
                orientation: 'landscape'
            },
            pagebreak: { mode: 'avoid-all' }
        };

        // สร้าง PDF
        const pdf = await html2pdf().set(opt).from(content).save();
        
        // ปิด loading และแสดงข้อความสำเร็จ
        Swal.fire({
            icon: 'success',
            title: 'สำเร็จ',
            text: 'สร้างไฟล์ PDF เรียบร้อยแล้ว',
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('PDF Export Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่สามารถสร้างไฟล์ PDF ได้'
        });
    }
}

// ฟังก์ชันช่วยจัดรูปแบบประเภทรายการสำหรับ PDF
function getTransactionTypeBadge(type) {
    switch(type.trim()) {
        case 'รับเข้า':
            return `<span style="color: #28a745; font-weight: bold;">รับเข้า</span>`;
        case 'ใช้ในคอร์ส':
            return `<span style="color: #17a2b8; font-weight: bold;">ใช้ในคอร์ส</span>`;
        case 'เบิกออก':
            return `<span style="color: #dc3545; font-weight: bold;">เบิกออก</span>`;
        default:
            return type;
    }
}

function exportTransactionToExcel() {
    const wb = XLSX.utils.book_new();
    
    // สร้าง worksheet สำหรับสรุป
    const summaryData = [
        ['รายงานการเบิกเครื่องมือเข้า-ออก'],
        ['วันที่ออกรายงาน:', new Date().toLocaleString('th-TH')],
        [''],
        ['เงื่อนไขการค้นหา:'],
        ['วันที่เริ่มต้น:', $('#transStartDate').val() || '-'],
        ['วันที่สิ้นสุด:', $('#transEndDate').val() || '-'],
        ['ประเภทรายการ:', $('#transactionType option:selected').text()],
        [''],
        ['สรุปภาพรวม'],
        ['จำนวนรายการทั้งหมด:', $('#totalTransactions').text()],
        ['มูลค่ารวมรับเข้า:', $('#totalInValue').text()],
        ['มูลค่ารวมเบิกออก:', $('#totalOutValue').text()],
        ['มูลค่าคงเหลือ:', $('#netValue').text()]
    ];
    
    const wsSummary = XLSX.utils.aoa_to_sheet(summaryData);
    XLSX.utils.book_append_sheet(wb, wsSummary, "สรุป");

    // สร้าง worksheet สำหรับรายละเอียด
    const headers = [
        'วันที่-เวลา',
        'รหัสเครื่องมือ',
        'ชื่อเครื่องมือ',
        'ประเภทรายการ',
        'จำนวน',
        'หน่วยนับ',
        'ราคา/หน่วย',
        'มูลค่ารวม',
        'ผู้ดำเนินการ',
        'หมายเหตุ'
    ];

    const wsData = [headers];
    
    $('#transactionTable tbody tr').each(function() {
        wsData.push([
            $(this).find('td').eq(0).text(),
            $(this).find('td').eq(1).text(),
            $(this).find('td').eq(2).text(),
            $(this).find('td').eq(3).text().trim(),
            $(this).find('td').eq(4).text().replace(/,/g, ''),
            $(this).find('td').eq(5).text(),
            $(this).find('td').eq(6).text().replace(/,/g, ''),
            $(this).find('td').eq(7).text().replace(/,/g, ''),
            $(this).find('td').eq(8).text(),
            $(this).find('td').eq(9).text()
        ]);
    });

    const wsDetails = XLSX.utils.aoa_to_sheet(wsData);

    // กำหนดความกว้างคอลัมน์
    const wscols = [
        {wch: 20}, // วันที่-เวลา
        {wch: 12}, // รหัสเครื่องมือ
        {wch: 30}, // ชื่อเครื่องมือ
        {wch: 15}, // ประเภทรายการ
        {wch: 10}, // จำนวน
        {wch: 10}, // หน่วยนับ
        {wch: 12}, // ราคา/หน่วย
        {wch: 12}, // มูลค่ารวม
        {wch: 20}, // ผู้ดำเนินการ
        {wch: 30}  // หมายเหตุ
    ];
    wsDetails['!cols'] = wscols;

    XLSX.utils.book_append_sheet(wb, wsDetails, "รายการเบิกเครื่องมือ");

    // Export ไฟล์
    XLSX.writeFile(wb, `transaction_report_${new Date().toISOString().slice(0, 10)}.xlsx`);
}
//////////////////////////////////////////////////////
      // Display success message
      <?php if(isset($_SESSION['msg_ok'])){ ?>
        Swal.fire({
          icon: 'success',
          title: 'แจ้งเตือน!',
          text: '<?php echo $_SESSION['msg_ok']; ?>',
          customClass: {
            confirmButton: 'btn btn-primary waves-effect waves-light'
          },
          buttonsStyling: false
        });
      <?php unset($_SESSION['msg_ok']); } ?>

      // Display error message
      <?php if(isset($_SESSION['msg_error'])){ ?>
        Swal.fire({
          icon: 'error',
          title: 'แจ้งเตือน!',
          text: '<?php echo $_SESSION['msg_error']; ?>',
          customClass: {
            confirmButton: 'btn btn-danger waves-effect waves-light'
          },
          buttonsStyling: false
        });
      <?php unset($_SESSION['msg_error']); } ?>
    </script>
  </body>
</html>