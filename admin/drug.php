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
  data-template="horizontal-menu-template-no-customizer-starter"
  data-style="light">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>จัดการยา | dcareclinic.com</title>

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

           <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">

              <!-- Users List Table -->
              <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between">
                  <h5 class="card-title mb-0 alert alert-danger">ข้อมูลยาในระบบทั้งหมด</h5>
                  <div>
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addDrugModal">เพิ่มยา</button>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addUnitModal">จัดการหน่วยนับ</button>
                  </div>
                </div>

<!-- Modal -->
<?php 




// ดึงข้อมูลประเภทยา
$drug_type_sql = "SELECT * FROM drug_type";
$drug_type_result = mysqli_query($conn, $drug_type_sql);

// ดึงข้อมูลหน่วยนับ
$unit_sql = "SELECT * FROM unit";
$unit_result = mysqli_query($conn, $unit_sql);

 ?>
<div class="modal fade" id="addDrugModal" tabindex="-1" aria-labelledby="addDrugModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addDrugModalLabel">เพิ่มยาใหม่</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="sql/drug-insert.php" method="post">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="drug_name" class="form-label">ชื่อยา</label>
                <input type="text" class="form-control" id="drug_name" name="drug_name" required>
              </div>
              <div class="mb-3">
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
              <div class="mb-3">
                <label for="drug_type_id" class="form-label">ประเภทยา</label>
                <select class="form-select" id="drug_type_id" name="drug_type_id" required>
                  <option value="">เลือกประเภทยา</option>
                  <?php while($drug_type = mysqli_fetch_object($drug_type_result)) { ?>
                    <option value="<?php echo $drug_type->drug_type_id; ?>"><?php echo $drug_type->drug_type_name; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="drug_properties" class="form-label">คุณสมบัติยา</label>
                <textarea class="form-control" id="drug_properties" name="drug_properties" rows="3"></textarea>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="drug_advice" class="form-label">คำแนะนำ</label>
                <input type="text" class="form-control" id="drug_advice" name="drug_advice">
              </div>
              <div class="mb-3">
                <label for="drug_warning" class="form-label">ข้อควรระวัง</label>
                <input type="text" class="form-control" id="drug_warning" name="drug_warning">
              </div>
<!--               <div class="mb-3">
                <label for="drug_amount" class="form-label">จำนวนคงเหลือ</label>
                <input type="number" class="form-control" id="drug_amount" name="drug_amount" required>
              </div> -->
              <div class="mb-3">
                <label for="drug_unit_id" class="form-label">หน่วยนับ</label>
                <select class="form-select" id="drug_unit_id" name="drug_unit_id" required>
                  <option value="">เลือกหน่วยนับ</option>
                  <?php while($unit = mysqli_fetch_object($unit_result)) { ?>
                    <option value="<?php echo $unit->unit_id; ?>"><?php echo $unit->unit_name; ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="drug_status" class="form-label">สถานะ</label>
                <select class="form-select" id="drug_status" name="drug_status" required>
                  <option value="1">พร้อมใช้งาน</option>
                  <option value="0">ไม่พร้อมใช้งาน</option>
                </select>
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
</div>
<!-- end modal -->
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
<!-- end modal -->


    <div class="container">
                <div class="card-datatable table-responsive">
    <table id="drugTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>รหัสยา</th>
                    <th>ชื่อยา</th>
                    <th>สาขา</th>
                    <th>ประเภทยา</th>
                    <th>จำนวนคงเหลือ/หน่วยนับ</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $branch_id=$_SESSION['branch_id'];
                // ดึงข้อมูลยาทั้งหมด
                $sql = "SELECT d.*, b.branch_name, dt.drug_type_name, u.unit_name 
                        FROM drug d
                        LEFT JOIN branch b ON d.branch_id = b.branch_id
                        LEFT JOIN drug_type dt ON d.drug_type_id = dt.drug_type_id
                        LEFT JOIN unit u ON d.drug_unit_id = u.unit_id
                        where b.branch_id='$branch_id'
                        ORDER BY d.drug_id DESC";
                $result = mysqli_query($conn, $sql);

function formatId($id) {
    // ตรวจสอบว่า $id เป็นตัวเลขหรือไม่
    if (!is_numeric($id)) {
        return "Error: Input must be numeric.";
    }

    // แปลง $id เป็นสตริงและนับจำนวนหลัก
    $idString = (string)$id;
    $digitCount = strlen($idString);

    // แสดงจำนวนหลักของ $id
    //echo "จำนวนหลักของ ID: " . $digitCount . "\n";

    // เติม '0' ด้านหน้าให้ครบ 6 หลัก และเพิ่ม 'd' นำหน้า
    $formattedId = 'D-' . str_pad($idString, 6, '0', STR_PAD_LEFT);

    return $formattedId;
}

                while($row = mysqli_fetch_object($result)) { ?>
                <tr>
                    <td><?php  ?>
<?php


// ตัวอย่างการใช้งาน

 echo formatId($row->drug_id) ;

?>

                  </td>
                    <td><?php echo $row->drug_name; ?></td>
                    <td><?php echo $row->branch_name; ?></td>
                    <td><?php echo $row->drug_type_name; ?></td>
                    <td><?php echo $row->drug_amount." ".$row->unit_name; ?></td>
                    <td><?php echo ($row->drug_status == 1) ? '<span class="badge bg-success">พร้อมใช้งาน</span>' : '<span class="badge bg-danger">ไม่พร้อมใช้งาน</span>'; ?></td>
                    <td>
                        <a href="" class="text-primary" data-bs-toggle="modal" data-bs-target="#editDrugModal<?php echo $row->drug_id; ?>">
                            <i class="ri-edit-line"></i>
                        </a>
                        <a href="" class="text-danger" onClick="confirmDelete('sql/drug-delete.php?id=<?php echo $row->drug_id; ?>'); return false;">
                            <i class="ri-delete-bin-6-line"></i>
                        </a>
                    </td>
                </tr>


                <!-- update  modal -->
                <div class="modal fade" id="editDrugModal<?php echo $row->drug_id; ?>" tabindex="-1" aria-labelledby="editDrugModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editDrugModalLabel">แก้ไขข้อมูลยา</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="sql/drug-update.php" method="post">
          <input type="hidden" name="drug_id" value="<?php echo $row->drug_id ?? ''; ?>">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="drug_name" class="form-label">ชื่อยา</label>
                <input type="text" class="form-control" id="drug_name" name="drug_name" value="<?php echo $row->drug_name ?? ''; ?>" required>
              </div>
              <div class="mb-3">
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
              <div class="mb-3">
                <label for="drug_type_id" class="form-label">ประเภทยา</label>
                <select class="form-select" id="drug_type_id" name="drug_type_id" required>
                  <?php
                  $drug_type_sql = "SELECT * FROM drug_type";
                  $drug_type_result = mysqli_query($conn, $drug_type_sql);
                  while($drug_type = mysqli_fetch_object($drug_type_result)) {
                    $selected = ($row->drug_type_id ?? '') == $drug_type->drug_type_id ? 'selected' : '';
                    echo "<option value='{$drug_type->drug_type_id}' {$selected}>{$drug_type->drug_type_name}</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="drug_properties" class="form-label">คุณสมบัติยา</label>
                <textarea class="form-control" id="drug_properties" name="drug_properties" rows="3"><?php echo $row->drug_properties ?? ''; ?></textarea>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="drug_advice" class="form-label">คำแนะนำ</label>
                <input type="text" class="form-control" id="drug_advice" name="drug_advice" value="<?php echo $row->drug_advice ?? ''; ?>">
              </div>
              <div class="mb-3">
                <label for="drug_warning" class="form-label">ข้อควรระวัง</label>
                <input type="text" class="form-control" id="drug_warning" name="drug_warning" value="<?php echo $row->drug_warning ?? ''; ?>">
              </div>
<!--               <div class="mb-3">
                <label for="drug_amount" class="form-label">จำนวนคงเหลือ</label>
                <input type="number" class="form-control" id="drug_amount" name="drug_amount" value="<?php echo $row->drug_amount ?? ''; ?>" required>
              </div> -->
              <div class="mb-3">
                <label for="drug_unit_id" class="form-label">หน่วยนับ</label>
                <select class="form-select" id="drug_unit_id" name="drug_unit_id" required>
                  <?php
                  $unit_sql = "SELECT * FROM unit";
                  $unit_result = mysqli_query($conn, $unit_sql);
                  while($unit = mysqli_fetch_object($unit_result)) {
                    $selected = ($row->drug_unit_id ?? '') == $unit->unit_id ? 'selected' : '';
                    echo "<option value='{$unit->unit_id}' {$selected}>{$unit->unit_name}</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="drug_status" class="form-label">สถานะ</label>
                <select class="form-select" id="drug_status" name="drug_status" required>
                  <option value="1" <?php echo ($row->drug_status ?? '') == 1 ? 'selected' : ''; ?>>ใช้งาน</option>
                  <option value="0" <?php echo ($row->drug_status ?? '') == 0 ? 'selected' : ''; ?>>ไม่ใช้งาน</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
            <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
                <!-- end modal update -->
                <?php } ?>
            </tbody>
        </table>
                </div>
</div>

              </div>
            </div>
            <!--/ Content -->

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
    <!-- sweet Alerts 2 -->
    <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js" />
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js"></script>
    <script src="../assets/vendor/libs/cleavejs/cleave.js"></script>
    <script src="../assets/vendor/libs/cleavejs/cleave-phone.js"></script>











    <script type="text/javascript">

 




      // ลบข้อมูล
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
                top.location = url;
              }
            });
          };


    // msg error
     <?php if(isset($_SESSION['msg_error'])){ ?>

      Swal.fire({
         icon: 'error',
         title: 'แจ้งเตือน!!',
         text: '<?php echo $_SESSION['msg_error']; ?>',
         customClass: {
              confirmButton: 'btn btn-danger waves-effect waves-light'
            },
         buttonsStyling: false

      })
    <?php unset($_SESSION['msg_error']); } ?>


    // msg ok 
    <?php if(isset($_SESSION['msg_ok'])){ ?>
      Swal.fire({
         icon: 'success',
         title: 'แจ้งเตือน!!',
         text: '<?php echo $_SESSION['msg_ok']; ?>',
         customClass: {
              confirmButton: 'btn btn-primary waves-effect waves-light'
            },
         buttonsStyling: false

      })
    <?php unset($_SESSION['msg_ok']); } ?>
    </script>

    <script>
$(document).ready(function() {
    $('#drugTable').DataTable({
        // ภาษาไทย
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
        lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "ทั้งหมด"] ],
        pagingType: 'full_numbers'
    });


});



    </script>

    <!-- ล้าง session -->
    <?php 
        unset($_SESSION['chk_users_username']); 
        unset($_SESSION['chk_users_fname']); 
        unset($_SESSION['chk_users_lname']); 
        unset($_SESSION['chk_users_nickname']); 
        unset($_SESSION['chk_users_tel']); 
        unset($_SESSION['chk_position_id']); 
        unset($_SESSION['chk_users_license']); 
    ?>
  </body>
</html>
