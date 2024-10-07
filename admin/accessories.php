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

    <title>จัดการอุปกรณ์การแพทย์ | dcareclinic.com</title>

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
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 0 30px rgba(0,0,0,0.15);
    }
    .card-header {
        background-color: #4e73df;
        color: white;
        border-bottom: none;
        padding: 20px 25px;
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
              <!-- Accessories List Table -->
              <div class="card mb-4">
                  <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="card-title mb-0 text-white">ข้อมูลอุปกรณ์การแพทย์ในระบบทั้งหมด</h5>
                      <div>
                          <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#addAccessoryModal">
                              <i class="ri-add-line me-1"></i> เพิ่มอุปกรณ์
                          </button>
                          <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                              <i class="ri-scales-line me-1"></i> จัดการหน่วยนับ
                          </button>
                      </div>
                  </div>
              </div>

                <!-- Modal for adding new accessory -->
                <div class="modal fade" id="addAccessoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white">เพิ่มอุปกรณ์ใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="sql/accessory-insert.php" method="post">
                    <div class="mb-3">
                        <label for="acc_name" class="form-label">ชื่ออุปกรณ์</label>
                        <input type="text" class="form-control" id="acc_name" name="acc_name" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="acc_type_id" class="form-label">ประเภทอุปกรณ์</label>
                            <select class="form-select" id="acc_type_id" name="acc_type_id" required>
                                <option value="">เลือกประเภทอุปกรณ์</option>
                               <?php
                                $acc_type_sql = "SELECT * FROM acc_type";
                                $acc_type_result = mysqli_query($conn, $acc_type_sql);
                                while ($acc_type = mysqli_fetch_object($acc_type_result)) {
                                  echo "<option value='{$acc_type->acc_type_id}'>{$acc_type->acc_type_name}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="branch_id" class="form-label">สาขา</label>
                              <select class="form-select" id="branch_id" name="branch_id" required>
                                <option value="">เลือกสาขา</option>
                                <?php 
                                // ดึงข้อมูลสาขา

                                $branch_id=$_SESSION['branch_id'];
                                $branch_sql = "SELECT * FROM branch where branch_id='$branch_id'";
                                $branch_result = mysqli_query($conn, $branch_sql);
                                 ?>
                                <?php while($branch = mysqli_fetch_object($branch_result)) { ?>
                                  <option value="<?php echo $branch->branch_id; ?>"><?php echo $branch->branch_name; ?></option>
                                <?php } ?>
                              </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="acc_properties" class="form-label">คุณสมบัติอุปกรณ์</label>
                        <textarea class="form-control" id="acc_properties" name="acc_properties" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="acc_unit_id" class="form-label">หน่วยนับ</label>
                            <select class="form-select" id="acc_unit_id" name="acc_unit_id" required>
                                <option value="">เลือกหน่วยนับ</option>
                                <?php 
                                $unit_sql = "SELECT * FROM unit";
                                $unit_result = mysqli_query($conn, $unit_sql);
                                while ($unit = mysqli_fetch_object($unit_result)) { ?>
                                    <option value="<?php echo $unit->unit_id; ?>"><?php echo $unit->unit_name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="acc_status" class="form-label">สถานะ</label>
                            <select class="form-select" id="acc_status" name="acc_status" required>
                                <option value="1">พร้อมใช้งาน</option>
                                <option value="0">ไม่พร้อมใช้งาน</option>
                            </select>
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
</div>

                <!-- Modal for managing accessory types -->
                <div class="modal fade" id="addUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white">จัดการหน่วยนับ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3">รายการหน่วยนับ</h6>
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table table-sm table-hover">
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
                                            <a href="#" class="text-danger" onClick="confirmDeleteUnit('sql/unit-delete.php?id=<?php echo $row->unit_id; ?>'); return false;">
                                                <i class="ri-delete-bin-6-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3 text-white">เพิ่มหน่วยนับใหม่</h6>
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
        <div class="table-responsive">
            <table id="accessoryTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>รหัสอุปกรณ์</th>
                        <th>ชื่ออุปกรณ์</th>
                        <th>สาขา</th>
                        <th>ประเภท</th>
                        <th>จำนวนคงเหลือ/หน่วยนับ</th>
                        <th>สถานะ</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                       $branch_id = $_SESSION['branch_id'];
                        $sql = "SELECT a.*, at.acc_type_name, u.unit_name ,b.*
                                FROM accessories a
                                LEFT JOIN branch b ON a.branch_id = b.branch_id
                                LEFT JOIN acc_type at ON a.acc_type_id = at.acc_type_id
                                LEFT JOIN unit u ON a.acc_unit_id = u.unit_id
                                where b.branch_id='$branch_id'
                                ORDER BY a.acc_id DESC";
                        $result = mysqli_query($conn, $sql);

                        function formatAccId($id) {
                          if (!is_numeric($id)) {
                            return "Error: Input must be numeric.";
                          }
                          $idString = (string)$id;
                          $formattedId = 'ACC-' . str_pad($idString, 6, '0', STR_PAD_LEFT);
                          return $formattedId;
                        }




                    while($row = mysqli_fetch_object($result)) { ?>
                    <tr>
                        <td><a href="accessories-detail.php?acc_id=<?= $row->acc_id; ?>"><?php echo formatAccId($row->acc_id); ?></a></td>
                        <td><a href="accessories-detail.php?acc_id=<?= $row->acc_id; ?>"><?php echo $row->acc_name; ?></a></td>
                        <td><a href="accessories-detail.php?acc_id=<?= $row->acc_id; ?>"><?php echo $row->branch_name; ?></a></td>
                        <td><a href="accessories-detail.php?acc_id=<?= $row->acc_id; ?>"><?php echo $row->acc_type_name; ?></a></td>
                        <td><a href="accessories-detail.php?acc_id=<?= $row->acc_id; ?>"><?php echo $row->acc_amount." ".$row->unit_name; ?></a></td>
                        <td>
                            <?php if ($row->acc_status == 1): ?>
                                <span class="badge badge-success">พร้อมใช้งาน</span>
                            <?php else: ?>
                                <span class="badge badge-danger">ไม่พร้อมใช้งาน</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="#" class="text-primary me-2" data-bs-toggle="modal" data-bs-target="#editAccessoryModal<?php echo $row->acc_id; ?>">
                                <i class="ri-edit-line"></i>
                            </a>
                            <a href="" class="text-danger" onClick="confirmDelete('sql/accessory-delete.php?id=<?php echo $row->acc_id; ?>'); return false;">
                              <i class="ri-delete-bin-6-line"></i>
                            </a>
                        </td>
                    </tr>
                    <!-- Modal for editing accessory -->
                        <div class="modal fade" id="editAccessoryModal<?php echo $row->acc_id; ?>" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="editAccessoryModalLabel">แก้ไขข้อมูลอุปกรณ์</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <form action="sql/accessory-update.php" method="post">
                                  <input type="hidden" name="acc_id" value="<?php echo $row->acc_id; ?>">
                                  <div class="row">
                                    <div class="col mb-3">
                                      <label for="edit_acc_name" class="form-label">ชื่ออุปกรณ์</label>
                                      <input type="text" id="edit_acc_name" name="acc_name" class="form-control" value="<?php echo $row->acc_name; ?>" required />
                                    </div>
                                  </div>
                                  <div class="row g-2">
                                    <div class="col mb-3">
                                      <label for="edit_acc_type_id" class="form-label">ประเภทอุปกรณ์</label>
                                      <select id="edit_acc_type_id" name="acc_type_id" class="form-select" required>
                                        <?php
                                        $acc_type_sql = "SELECT * FROM acc_type";
                                        $acc_type_result = mysqli_query($conn, $acc_type_sql);
                                        while ($acc_type = mysqli_fetch_object($acc_type_result)) {
                                          $selected = ($row->acc_type_id == $acc_type->acc_type_id) ? 'selected' : '';
                                          echo "<option value='{$acc_type->acc_type_id}' {$selected}>{$acc_type->acc_type_name}</option>";
                                        }
                                        ?>
                                      </select>
                                    </div>
                                    <div class="col mb-3">
                                      <label for="branch_id" class="form-label">สาขา</label>
                                      <select class="form-select" id="branch_id" name="branch_id" required>
                                        <?php
                                        if($_SESSION['position_id']==1){
                                          $branch_sql = "SELECT * FROM branch";
                                        }else{
                                          $branch_id=$_SESSION['branch_id'];
                                          $branch_sql = "SELECT * FROM branch where branch_id='$branch_id'";
                                        }
                                        $branch_result = mysqli_query($conn, $branch_sql);
                                        while($branch = mysqli_fetch_object($branch_result)) {
                                          $selected = ($row->branch_id ?? '') == $branch->branch_id ? 'selected' : '';
                                          echo "<option value='{$branch->branch_id}' {$selected}>{$branch->branch_name}</option>";
                                        }
                                        ?>
                                      </select>
                                    </div>
<!--                                     <div class="col mb-3">
                                      <label for="edit_acc_amount" class="form-label">จำนวน</label>
                                      <input type="number" id="edit_acc_amount" name="acc_amount" class="form-control" value="<?php echo $row->acc_amount; ?>" required />
                                    </div> -->
                                  </div>
                                  <div class="row">
                                    <div class="col mb-3">
                                      <label for="edit_acc_properties" class="form-label">คุณสมบัติอุปกรณ์</label>
                                      <textarea id="edit_acc_properties" name="acc_properties" class="form-control"><?php echo $row->acc_properties; ?></textarea>
                                    </div>
                                  </div>
                                  <div class="row g-2">
                                    <div class="col mb-3">
                                      <label for="edit_acc_unit_id" class="form-label">หน่วยนับ</label>
                                      <select id="edit_acc_unit_id" name="acc_unit_id" class="form-select" required>
                                        <?php
                                        $unit_sql = "SELECT * FROM unit";
                                        $unit_result = mysqli_query($conn, $unit_sql);
                                        while ($unit = mysqli_fetch_object($unit_result)) {
                                          $selected = ($row->acc_unit_id == $unit->unit_id) ? 'selected' : '';
                                          echo "<option value='{$unit->unit_id}' {$selected}>{$unit->unit_name}</option>";
                                        }
                                        ?>
                                      </select>
                                    </div>
                                    <div class="col mb-3">
                                      <label for="edit_acc_status" class="form-label">สถานะ</label>
                                      <select id="edit_acc_status" name="acc_status" class="form-select" required>
                                        <option value="1" <?php echo ($row->acc_status == 1) ? 'selected' : ''; ?>>พร้อมใช้งาน</option>
                                        <option value="0" <?php echo ($row->acc_status == 0) ? 'selected' : ''; ?>>ไม่พร้อมใช้งาน</option>
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
     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/vendor/libs/cleavejs/cleave.js"></script>
    <script src="../assets/vendor/libs/cleavejs/cleave-phone.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>


    <script>
      // DataTable initialization
      $(document).ready(function() {
        $('#accessoryTable').DataTable({
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
          pagingType: 'full_numbers'
        });
      });

      // Delete confirmation
function confirmDeleteUnit(url) {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "คุณต้องการลบหน่วยนับนี้ใช่หรือไม่?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    })
}
      // Delete confirmation
      function confirmDelete(url) {
        Swal.fire({
          title: 'คุณแน่ใจหรือไม่ที่จะลบข้อมูล?',
          text: "การลบจะทำให้ข้อมูลหาย ไม่สามารถกู้คืนมาได้!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'ใช่ ฉันต้องการลบข้อมูล!',
          customClass: {
            confirmButton: 'btn btn-danger me-1 waves-effect waves-light',
            cancelButton: 'btn btn-outline-secondary waves-effect'
          },
          buttonsStyling: false
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = url;
          }
        });
      }
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