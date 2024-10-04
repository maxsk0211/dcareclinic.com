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
  }

  .card-body {
    padding: 30px;
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

  .btn {
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  .btn-success {
    background-color: #1cc88a;
    border-color: #1cc88a;
  }

  .btn-success:hover {
    background-color: #17a673;
    border-color: #17a673;
    transform: translateY(-2px);
  }

  .btn-primary {
    background-color: #4e73df;
    border-color: #4e73df;
  }

  .btn-primary:hover {
    background-color: #2e59d9;
    border-color: #2e59d9;
    transform: translateY(-2px);
  }

  .table {
    border-collapse: separate;
    border-spacing: 0 15px;
  }

  .table thead th {
    border-bottom: none;
    background-color: #4e73df;
    color: white;
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 1px;
    padding: 15px;
  }

  .table tbody tr {
    background-color: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
  }

  .table tbody tr:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  }

  .table td {
    vertical-align: middle;
    border: none;
    padding: 15px;
  }

  .badge {
    padding: 8px 12px;
    font-size: 0.8rem;
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

  .text-warning, .text-danger {
    transition: all 0.3s ease;
  }

  .text-warning:hover, .text-danger:hover {
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

           <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
<div class="card mb-4">
  <div class="card-header">
    <h5 class="card-title text-white">เพิ่มประเภทคอร์สใหม่</h5>
  </div>
  <div class="card-body">
    <br>
    <form action="sql/type-course-insert.php" method="post" class="row g-3 align-items-center">
      <div class="col-auto">
        <label for="course_type_name" class="form-label">ชื่อประเภทคอร์ส:</label>
      </div>
      <div class="col-auto">
        <input type="text" name="course_type_name" id="course_type_name" class="form-control border-1 border-primary" required maxlength="50">
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-success">
          <i class="ri-add-line me-1"></i> บันทึก
        </button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h5 class="card-title text-white">รายการประเภทคอร์สทั้งหมด</h5>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table id="courseTypesTable" class="table table-hover">
        <thead>
          <tr>
            <th class="text-center">#</th>
            <th>ชื่อประเภทคอร์ส</th>
            <th class="text-center">สถานะ</th>
            <th class="text-center">การจัดการ</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $i = 1;
            $sql_show_course_types = "SELECT * FROM `course_type` ORDER BY `course_type`.`course_type_id` ASC";
            $result_show_course_types = $conn->query($sql_show_course_types);
          while ($row = $result_show_course_types->fetch_object()) {
          ?>
          <tr>
            <td class="text-center"><?= $i++ ?></td>
            <td><?= $row->course_type_name ?></td>
            <td class="text-center">
              <?php if ($row->course_type_status == 1): ?>
                <span class="badge badge-success">พร้อมใช้งาน</span>
              <?php else: ?>
                <span class="badge badge-danger">ไม่พร้อมใช้งาน</span>
              <?php endif ?>
            </td>
            <td class="text-center">
              <a href="#" class="text-warning me-2" data-bs-toggle="modal" data-bs-target="#editCourseTypeModal<?= $row->course_type_id ?>">
                <i class="ri-edit-box-line"></i>
              </a>
              <a href="#" class="text-danger" onClick="confirmDelete('sql/course-type-delete.php?id=<?php echo $row->course_type_id; ?>'); return false;">
                <i class="ri-delete-bin-6-line"></i>
              </a>
            </td>
          </tr>
          <div class="modal fade" id="editCourseTypeModal<?= $row->course_type_id ?>" tabindex="-1" aria-labelledby="editCourseTypeModalLabel<?= $row->course_type_id ?>" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white" id="editCourseTypeModalLabel<?= $row->course_type_id ?>">แก้ไขประเภทคอร์ส</h5>
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
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
        <button type="submit" class="btn btn-primary" form="editCourseTypeForm<?= $row->course_type_id ?>">บันทึก</button>
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
