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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css"> 
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
              <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between">
                  <h5 class="card-title mb-0 alert alert-danger">ข้อมูลอุปกรณ์การแพทย์ในระบบทั้งหมด</h5>
                  <div>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addAccessoryModal">เพิ่มอุปกรณ์</button>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addUnitModal">จัดการหน่วยนับ</button>
                  </div>
                </div>

                <!-- Modal for adding new accessory -->
                <div class="modal fade" id="addAccessoryModal" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel3">เพิ่มอุปกรณ์ใหม่</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <form action="sql/accessory-insert.php" method="post">
                          <div class="row">
                            <div class="col mb-3">
                              <label for="acc_name" class="form-label">ชื่ออุปกรณ์</label>
                              <input type="text" id="acc_name" name="acc_name" class="form-control" required />
                            </div>
                          </div>
                          <div class="row g-2">
                            <div class="col mb-3">
                              <label for="acc_type_id" class="form-label">ประเภทอุปกรณ์</label>
                              <select id="acc_type_id" name="acc_type_id" class="form-select" required>
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
                            <div class="col mb-3">
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
<!--                             <div class="col mb-3">
                              <label for="acc_amount" class="form-label">จำนวน</label>
                              <input type="number" id="acc_amount" name="acc_amount" class="form-control" required />
                            </div> -->
                          </div>
                          <div class="row">
                            <div class="col mb-3">
                              <label for="acc_properties" class="form-label">คุณสมบัติอุปกรณ์</label>
                              <textarea id="acc_properties" name="acc_properties" class="form-control"></textarea>
                            </div>
                          </div>
                          <div class="row g-2">
                            <div class="col mb-3">
                              <label for="acc_unit_id" class="form-label">หน่วยนับ</label>
                              <select id="acc_unit_id" name="acc_unit_id" class="form-select" required>
                                <option value="">เลือกหน่วยนับ</option>
                                <?php
                                $unit_sql = "SELECT * FROM unit";
                                $unit_result = mysqli_query($conn, $unit_sql);
                                while ($unit = mysqli_fetch_object($unit_result)) {
                                  echo "<option value='{$unit->unit_id}'>{$unit->unit_name}</option>";
                                }
                                ?>
                              </select>
                            </div>
                            <div class="col mb-3">
                              <label for="acc_status" class="form-label">สถานะ</label>
                              <select id="acc_status" name="acc_status" class="form-select" required>
                                <option value="1">พร้อมใช้งาน</option>
                                <option value="0">ไม่พร้อมใช้งาน</option>
                              </select>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col mb-3">
                              <button type="submit" class="btn btn-primary">บันทึก</button>
                              <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Modal for managing accessory types -->
                <div class="modal fade" id="addUnitModal" tabindex="-1" aria-labelledby="addUnitModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addUnitModalLabel">จัดการหน่วยนับ</h5>
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

                <div class="container">
                  <div class="card-datatable table-responsive">
                    <table id="accessoryTable" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th>รหัสอุปกรณ์</th>
                          <th>ชื่ออุปกรณ์</th>
                          <th>สาขา</th>
                          <th>ประเภท</th>
                          <th>จำนวนคงเหลือ/หน่วยนับ</th>
                          <th>สถานะ</th>
                          <th>จัดการ</th>
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
                          <td><?php echo formatAccId($row->acc_id); ?></td>
                          <td><?php echo $row->acc_name; ?></td>
                          <td><?php echo $row->branch_name; ?></td>
                          <td><?php echo $row->acc_type_name; ?></td>
                          <td><?php echo $row->acc_amount." ".$row->unit_name; ?></td>
                          <td><?php echo ($row->acc_status == 1) ? '<span class="badge bg-success">พร้อมใช้งาน</span>' : '<span class="badge bg-danger">ไม่พร้อมใช้งาน</span>'; ?></td>
                          <td>
                            <a href="" class="text-primary" data-bs-toggle="modal" data-bs-target="#editAccessoryModal<?php echo $row->acc_id; ?>" >
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
    <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>

    <!-- datatables -->
    <script src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.dataTables.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js"></script>

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