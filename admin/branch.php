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

    <title>จัดการสาขา | dcareclinic.com</title>

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
              <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">ข้อมูลสาขาในระบบทั้งหมด</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBranchModal">
                  <i class="ri-add-line me-1"></i> เพิ่มสาขา
                </button>
              </div>

              <div class="card-datatable table-responsive">
                <table class="table border-top">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>ชื่อสาขา</th>
                      <th>ที่อยู่</th>
                      <th>เบอร์โทร</th>
                      <th>อีเมล</th>
                      <th>เลขประจำตัวผู้เสียภาษี</th>
                      <th>เลขที่ใบอนุญาต</th>
                      <th>บริการ</th>
                      <th>โลโก้</th>
                      <th>การจัดการ</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql = "SELECT * FROM branch";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                      while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['branch_id'] . "</td>";
                        echo "<td>" . $row['branch_name'] . "</td>";
                        echo "<td>" . ($row['branch_address'] ?? '-') . "</td>";
                        echo "<td>" . ($row['branch_phone'] ?? '-') . "</td>";
                        echo "<td>" . ($row['branch_email'] ?? '-') . "</td>";
                        echo "<td>" . ($row['branch_tax_id'] ?? '-') . "</td>";
                        echo "<td>" . ($row['branch_license_no'] ?? '-') . "</td>";
                        echo "<td>" . ($row['branch_services'] ?? '-') . "</td>";
                        echo "<td>";
                        if ($row['branch_logo']) {
                          echo "<img src='../img/" . $row['branch_logo'] . "' class='rounded' style='max-width: 50px;'>";
                        } else {
                          echo "-";
                        }
                        echo "</td>";
                        echo "<td>
                          <button type='button' class='btn btn-warning btn-sm' onclick='editBranch(" . $row['branch_id'] . ")'>แก้ไข</button>
                          <button type='button' class='btn btn-danger btn-sm' onclick='deleteBranch(" . $row['branch_id'] . ")'>ลบ</button>
                        </td>";
                        echo "</tr>";
                      }
                    } else {
                      echo "<tr><td colspan='10' class='text-center'>ไม่พบข้อมูล</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
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

  <!-- Add Branch Modal -->
  <div class="modal fade" id="addBranchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <form class="modal-content" action="sql/branch-insert.php" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">เพิ่มสาขาใหม่</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">ชื่อสาขา <span class="text-danger">*</span></label>
              <input type="text" name="branch_name" class="form-control" required maxlength="50">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">ที่อยู่</label>
              <input type="text" name="branch_address" class="form-control" maxlength="200">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">เบอร์โทร</label>
              <input type="tel" name="branch_phone" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">อีเมล</label>
              <input type="email" name="branch_email" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">เลขประจำตัวผู้เสียภาษี</label>
              <input type="number" name="branch_tax_id" class="form-control" >
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">เลขที่ใบอนุญาต</label>
              <input type="text" name="branch_license_no" class="form-control" maxlength="50">
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">บริการ</label>
              <textarea name="branch_services" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">โลโก้</label>
              <input type="file" name="branch_logo" class="form-control" accept="image/png,image/jpeg">
              <small class="text-muted">อนุญาต: JPG, PNG ขนาดไม่เกิน 2MB</small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">บันทึก</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Branch Modal -->
  <div class="modal fade" id="editBranchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <form class="modal-content" action="sql/branch-update.php" method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">แก้ไขข้อมูลสาขา</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="branch_id" id="edit_branch_id">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">ชื่อสาขา <span class="text-danger">*</span></label>
              <input type="text" name="branch_name" id="edit_branch_name" class="form-control" required maxlength="50">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">ที่อยู่</label>
              <input type="text" name="branch_address" id="edit_branch_address" class="form-control" maxlength="200">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">เบอร์โทร</label>
              <input type="tel" name="branch_phone" id="edit_branch_phone" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">อีเมล</label>
              <input type="email" name="branch_email" id="edit_branch_email" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">เลขประจำตัวผู้เสียภาษี</label>
              <input type="number" name="branch_tax_id" id="edit_branch_tax_id" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">เลขที่ใบอนุญาต</label>
              <input type="text" name="branch_license_no" id="edit_branch_license_no" class="form-control" maxlength="50">
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">บริการ</label>
              <textarea name="branch_services" id="edit_branch_services" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">โลโก้</label>
              <input type="file" name="branch_logo" class="form-control" accept="image/png,image/jpeg">
              <small class="text-muted">อนุญาต: JPG, PNG ขนาดไม่เกิน 2MB</small>
              <div id="current_logo" class="mt-2"></div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-primary">บันทึก</button>
        </div>
      </form>
    </div>
  </div>
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

    <script type="text/javascript">

 // Delete confirmation
    function deleteBranch(id) {
      Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "การลบข้อมูลจะไม่สามารถกู้คืนได้!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ใช่, ลบเลย!',
        cancelButtonText: 'ยกเลิก',
        customClass: {
          confirmButton: 'btn btn-danger me-3',
          cancelButton: 'btn btn-outline-secondary'
        },
        buttonsStyling: false
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'sql/branch-delete.php?branch_id=' + id;
        }
      });
    }

// Function to edit branch
    function editBranch(id) {
      // เรียกข้อมูลสาขาจาก API
      fetch('sql/branch-get.php?branch_id=' + id)
        .then(response => response.json())
        .then(data => {
          if (!data.error) {
            // กำหนดค่าให้กับฟอร์มแก้ไข
            document.getElementById('edit_branch_id').value = data.branch_id;
            document.getElementById('edit_branch_name').value = data.branch_name;
            document.getElementById('edit_branch_address').value = data.branch_address || '';
            document.getElementById('edit_branch_phone').value = data.branch_phone || '';
            document.getElementById('edit_branch_email').value = data.branch_email || '';
            document.getElementById('edit_branch_tax_id').value = data.branch_tax_id || '';
            document.getElementById('edit_branch_license_no').value = data.branch_license_no || '';
            document.getElementById('edit_branch_services').value = data.branch_services || '';
            
            // แสดงรูปโลโก้ปัจจุบัน (ถ้ามี)
            const currentLogoDiv = document.getElementById('current_logo');
            if (data.branch_logo) {
              currentLogoDiv.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                  <img src="../img/${data.branch_logo}" alt="Current Logo" class="rounded" style="max-width: 100px;">
                  <span class="text-muted">โลโก้ปัจจุบัน</span>
                </div>`;
            } else {
              currentLogoDiv.innerHTML = '<span class="text-muted">ไม่มีโลโก้</span>';
            }

            // เปิด Modal
            new bootstrap.Modal(document.getElementById('editBranchModal')).show();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'ผิดพลาด!',
              text: data.error,
              customClass: {
                confirmButton: 'btn btn-danger'
              },
              buttonsStyling: false
            });
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire({
            icon: 'error',
            title: 'ผิดพลาด!',
            text: 'เกิดข้อผิดพลาดในการดึงข้อมูล',
            customClass: {
              confirmButton: 'btn btn-danger'
            },
            buttonsStyling: false
          });
        });
    }



















    // msg error
    <?php if(isset($_SESSION['msg_error'])){ ?>
    window.onload = function() {
        var myModal = new bootstrap.Modal(document.getElementById('addUserModal'));
        myModal.show();
    }
      Swal.fire({
         icon: 'error',
         title: 'แจ้งเตือน!!',
         text: '<?php echo $_SESSION['msg_error']; ?>',
         customClass: {
              confirmButton: 'btn btn-danger waves-effect waves-light'
            },
         buttonsStyling: false

      });
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

      });
    <?php unset($_SESSION['msg_ok']); } ?>





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
