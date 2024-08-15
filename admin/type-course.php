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

    <title>จัดประเภทคอร์ส | dcareclinic.com</title>

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
                <div class="card-header border-bottom d-flex justify-content-between">
                    <h5 class="card-title mb-0 alert alert-primary">ข้อมูลประเภทคอร์สในระบบทั้งหมด</h5>
                </div>

                <div class="row">
                  <div class="offset-md-4">
                    <form action="sql/type-course-insert.php" method="post" class="d-flex align-items-center"> 
                      <div>
                        <label for="course_type_name" class="col-form-label">ชื่อประเภทคอร์ส : </label>
                      </div>
                      <div>
                        <input type="text" name="course_type_name" id="course_type_name" class="form-control" aria-describedby="ชื่อสาขา" required max="50">
                      </div>
                      <div>
                        <button type="submit" class="btn btn-success">บันทึก</button>
                      </div>
                    </form>
                  </div>
                </div>

                
                <div class="row">
                  <div class="offset-md-4 col-md-4">
                    
                    <div class="card-datatable table-responsive">
                      <?php
                      // ... (โค้ดส่วนอื่นๆ เช่น session_start(), include, require) ...

                      // ดึงข้อมูลจากฐานข้อมูล
                      $sql = "SELECT branch_id, branch_name FROM branch";
                      $result = $conn->query($sql);
                      ?>

                      <div class="card-datatable table-responsive">
                        <table id="courseTypesTable" class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th class="text-center">คำสั่ง</th>
            <th class="text-center">#</th>
            <th>ชื่อประเภทคอร์ส</th>
            <th>สถานะ</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1; // ตัวแปรนับลำดับ
        $sql_show_course_types = "SELECT * FROM `course_type` ORDER BY `course_type`.`course_type_id` ASC";
        $result_show_course_types = $conn->query($sql_show_course_types);
        while ($row = $result_show_course_types->fetch_object()) {
        ?>
        <tr>
            <td class="text-center">
                <a href="#" class="text-warning" data-bs-toggle="modal" data-bs-target="#editCourseTypeModal<?= $row->course_type_id ?>"><i class="ri-edit-box-line"></i></a>
                <a href="#" class="text-danger" onClick="confirmDelete('sql/course-type-delete.php?id=<?php echo $row->course_type_id; ?>'); return false;"><i class="ri-delete-bin-6-line"></i></a>
            </td>
            <td class="text-center"><?= $i++ ?></td>
            <td><?= $row->course_type_name ?></td>
            <td class="text-center">
                <?php if ($row->course_type_status == 1): ?>
                    <span class="badge bg-success">พร้อมใช้งาน</span>
                <?php else: ?>
                    <span class="badge bg-danger">ไม่พร้อมใช้งาน</span>
                <?php endif ?>
            </td>
        </tr>

        <div class="modal fade" id="editCourseTypeModal<?= $row->course_type_id ?>" tabindex="-1" aria-labelledby="editCourseTypeModalLabel<?= $row->course_type_id ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCourseTypeModalLabel<?= $row->course_type_id ?>">แก้ไขประเภทคอร์ส</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editCourseTypeForm<?= $row->course_type_id ?>" method="post" action="sql/course-type-update.php">
                            <input type="hidden" name="course_type_id" value="<?= $row->course_type_id ?>">
                            <div class="mb-3">
                                <label for="course_type_name" class="form-label">ชื่อประเภทคอร์ส:</label>
                                <input type="text" class="form-control" id="course_type_name" name="course_type_name" value="<?= $row->course_type_name ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="course_type_status" class="form-label">สถานะ:</label>
                                <select class="form-select" id="course_type_status" name="course_type_status" required>
                                    <option value="1" <?= ($row->course_type_status == 1) ? 'selected' : '' ?>>พร้อมใช้งาน</option>
                                    <option value="0" <?= ($row->course_type_status == 0) ? 'selected' : '' ?>>ไม่พร้อมใช้งาน</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">บันทึก</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
        }
        ?>
    </tbody>
</table>
                      </div>

                    </div>

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

    <script type="text/javascript">



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
