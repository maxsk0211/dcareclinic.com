<?php 
  session_start();
  
  include 'chk-session.php';
  require '../dbcon.php';

  // ดึงข้อมูลประวัติการแก้ไข
$sql_logs = "SELECT al.*, u.users_fname, u.users_lname 
             FROM activity_logs al
             LEFT JOIN users u ON al.user_id = u.users_id 
             WHERE al.entity_type = 'course'
             ORDER BY al.created_at DESC";
$result_logs = $conn->query($sql_logs);

if (!$result_logs) {
    die("Error fetching logs: " . $conn->error);
}
// เพิ่มฟังก์ชันนี้ไว้ด้านบนของไฟล์ course.php
function convertToThaiDate($date) {
    $thai_months = [
        1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.', 5 => 'พ.ค.', 6 => 'มิ.ย.',
        7 => 'ก.ค.', 8 => 'ส.ค.', 9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
    ];

    $date_parts = explode(' ', $date);
    $time = isset($date_parts[1]) ? $date_parts[1] : '';
    $date_parts = explode('-', $date_parts[0]);
    
    $day = intval($date_parts[2]);
    $month = $thai_months[intval($date_parts[1])];
    $year = intval($date_parts[0]) + 543;

    return "$day $month $year" . ($time ? " $time" : "");
}
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

    <title>จัดการคอร์ส | dcareclinic.com</title>

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
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
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
  
  .btn-info {
    background-color: #17a2b8;
    border-color: #17a2b8;
    color: white;
  }
  
  .btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
  }
  
  .table {
    border-collapse: separate;
    border-spacing: 0 0.5rem;
  }
  
  .table thead th {
    border: none;
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
  }
  
  .table tbody tr {
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.075);
    border-radius: 5px;
    transition: all 0.2s ease;
  }
  
  .table tbody tr:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }
  
  .table tbody td {
    border: none;
    vertical-align: middle;
    background-color: white;
  }
  
  .table tbody td:first-child {
    border-top-left-radius: 5px;
    border-bottom-left-radius: 5px;
  }
  
  .table tbody td:last-child {
    border-top-right-radius: 5px;
    border-bottom-right-radius: 5px;
  }
  
  .badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
  }
  
  .text-warning, .text-danger {
    transition: color 0.2s ease;
  }
  
  .text-warning:hover, .text-danger:hover {
    opacity: 0.7;
  }
      #historyTable thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    #historyTable tbody tr:hover {
        background-color: rgba(0,0,0,.02);
        cursor: pointer;
    }
    #historyModal .modal-body {
        padding: 1.5rem;
    }
    #historyTable .badge {
        font-size: 0.85em;
        padding: 0.4em 0.8em;
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

              <!-- Users List Table -->
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="card-title mb-0 alert alert-info">ข้อมูลคอร์สในระบบทั้งหมด</h5>
                  <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                    <i class="ri-add-line me-1"></i> เพิ่มคอร์ส
                  </button>
                </div>
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCourseModalLabel">เพิ่มคอร์ส</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addCourseForm" method="post" action="sql/course-insert.php" enctype="multipart/form-data"> 
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="branch_id" class="form-label">สาขา:</label>
                <select class="form-select" id="branch_id" name="branch_id" required>
                  <option value="" selected disabled>โปรดเลือก</option>
                  <?php
                  // ดึงข้อมูลสาขาจากฐานข้อมูล
                  if($_SESSION['position_id']==1){
                    $sql_branch = "SELECT * FROM branch";
                  }else{
                    $branch_id=$_SESSION['branch_id'];
                    $sql_branch = "SELECT * FROM branch where branch_id='$branch_id'";
                  }

                  $result_branch = $conn->query($sql_branch);
                  while ($row_branch = $result_branch->fetch_object()) {
                    echo "<option value='" . $row_branch->branch_id . "'>" . $row_branch->branch_name . "</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="course_name" class="form-label">ชื่อคอร์ส:</label>
                <input type="text" class="form-control" id="course_name" name="course_name" required>
              </div>
            </div>
            <div class="col-12">
              <div class="mb-3">
                <label for="course_detail" class="form-label">รายละเอียดคอร์ส:</label>
                <textarea class="form-control" id="course_detail" name="course_detail"></textarea>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="course_price" class="form-label">ราคาคอร์ส:</label>
                <input type="number" class="form-control" id="course_price" name="course_price" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="course_amount" class="form-label">จำนวนครั้ง:</label>
                <input type="number" class="form-control" id="course_amount" name="course_amount" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="course_type_id" class="form-label">ประเภทคอร์ส:</label>
                <select class="form-select" id="course_type_id" name="course_type_id" required>
                  <option value="" selected disabled>โปรดเลือก</option>
                  <?php
                  // ดึงข้อมูลประเภทคอร์สจากฐานข้อมูล
                  $sql_course_type = "SELECT * FROM course_type";
                  $result_course_type = $conn->query($sql_course_type);
                  while ($row_course_type = $result_course_type->fetch_object()) {
                    echo "<option value='" . $row_course_type->course_type_id . "'>" . $row_course_type->course_type_name . "</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="course_start" class="form-label">เริ่มคอร์ส (พ.ศ.):</label>
                <input type="text" class="form-control date-mask" id="course_start" name="course_start" required placeholder="dd/mm/yyyy">
                <div id="course_start_error" class="invalid-feedback" style="display: none;">กรอกวันที่ผิดพลาด</div> 
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="course_end" class="form-label">สิ้นสุดคอร์ส (พ.ศ.):</label>
                <input type="text" class="form-control date-mask" id="course_end" name="course_end" required placeholder="dd/mm/yyyy">
                <div id="course_end_error" class="invalid-feedback" style="display: none;">กรอกวันที่ผิดพลาด</div> 
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="course_pic" class="form-label">รูปภาพ:</label>
                <input type="file" class="form-control" id="course_pic" name="course_pic">
              </div>
            </div>
            <div class="col-12">
              <div class="mb-3">
                <label for="course_note" class="form-label">หมายเหตุ:</label>
                <textarea class="form-control" id="course_note" name="course_note"></textarea>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="course_status" class="form-label">สถานะ:</label>
                <select class="form-select" id="course_status" name="course_status" required>
                  <option value="1">พร้อมใช้งาน</option>
                  <option value="0">ไม่พร้อมใช้งาน</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            <button type="submit" class="btn btn-primary" disabled>บันทึก</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
                

  <div class="card-body">
    <div class="text-end my-2">
      <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#historyModal">
          <i class="ri-history-line me-2"></i> ประวัติการแก้ไข
      </button>
    </div>
    <div class="table-responsive">
      <table id="coursesTable" class="table table-hover">
          <thead>
            <tr>
              <!-- <th class="text-center">ลำดับ</th> -->
              <th class="text-center">รหัส</th>
              <th>ชื่อคอร์ส</th>
              <th>หมวดหมู่</th>
              <th>ราคาคอร์ส</th>
              <th>จำนวนที่ใช้</th>
              <th>สถานะ</th>
              <th class="text-center">ตัวเลือก</th>
            </tr>
          </thead>
          <tbody>
        <?php

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
          $formattedId = 'C-' . str_pad($idString, 6, '0', STR_PAD_LEFT);

          return $formattedId;
      }


        $i = 1;
        $branch_id=$_SESSION['branch_id'];
        $sql_show_courses = "SELECT * FROM `course` where branch_id='$branch_id' ORDER BY `course`.`course_id` ASC";

        $result_show_courses = $conn->query($sql_show_courses);
        while ($row = $result_show_courses->fetch_object()) {
        ?>
        <tr> 
            <!-- <td class="text-center"><a href="course-detail.php?id=<?= $row->course_id ?>"><?= $i++ ?></a></td> -->
            <td class="text-center"><a href="course-detail.php?id=<?= $row->course_id ?>"><?= formatId($row->course_id); ?></a></td>
            <td><a href="course-detail.php?id=<?= $row->course_id ?>"><?= $row->course_name ?></a></td>
            <td><a href="course-detail.php?id=<?= $row->course_id ?>">
                <?php
                $course_type_id = $row->course_type_id;
                $sql_course_type = "SELECT course_type_name FROM course_type WHERE course_type_id = $course_type_id ";
                $result_course_type = $conn->query($sql_course_type);
                $course_type_name = $result_course_type->fetch_object()->course_type_name;
                ?>
                <?= $course_type_name ?></a>
            </td>
            <td><a href="course-detail.php?id=<?= $row->course_id ?>"><?= $row->course_price." บาท" ?></a></td>
            <td><a href="course-detail.php?id=<?= $row->course_id ?>"><?= $row->course_amount . " ครั้ง" ?></a></td> 
            <td class="text-center">
                <?php if ($row->course_status == 1): ?>
                    <span class="badge bg-success">พร้อมใช้งาน</span>
                <?php else: ?>
                    <span class="badge bg-danger">ไม่พร้อมใช้งาน</span>
                <?php endif ?>
            </td>
            <td class="text-center">
                <a href="#" class="text-warning" data-bs-toggle="modal" data-bs-target="#editCourseModal<?= $row->course_id ?>"><i class="ri-edit-box-line"></i></a>
                <a href="#" class="text-danger" onClick="confirmDelete(<?php echo $row->course_id; ?>); return false;"><i class="ri-delete-bin-6-line"></i></a>
            </td>
        </tr>
        <!-- update -->
        <div class="modal fade" id="editCourseModal<?= $row->course_id ?>" tabindex="-1" aria-labelledby="editCourseModalLabel<?= $row->course_id ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editCourseModalLabel<?= $row->course_id ?>">แก้ไขคอร์ส</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editCourseForm<?= $row->course_id ?>" method="post" action="sql/course-update.php" enctype="multipart/form-data">
          <input type="hidden" name="course_id" value="<?= $row->course_id ?>">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="branch_id" class="form-label">สาขา:</label>
                <select class="form-select" id="branch_id" name="branch_id" required>
                  <option value="" disabled>โปรดเลือก</option>
                  <?php
                  // ดึงข้อมูลสาขาจากฐานข้อมูล
                  if($_SESSION['position_id']==1){
                    $sql_branch = "SELECT * FROM branch";
                  }else{
                    $branch_id=$_SESSION['branch_id'];
                    $sql_branch = "SELECT * FROM branch where branch_id='$branch_id'";
                  }
                  $result_branch = $conn->query($sql_branch);
                  while ($row_branch = $result_branch->fetch_object()) {
                    $selected = ($row_branch->branch_id == $row->branch_id) ? 'selected' : '';
                    echo "<option value='" . $row_branch->branch_id . "' $selected>" . $row_branch->branch_name . "</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="course_name" class="form-label">ชื่อคอร์ส:</label>
                <input type="text" class="form-control" id="course_name" name="course_name" value="<?= $row->course_name ?>" required>
              </div>
            </div>
            <div class="col-12">
              <div class="mb-3">
                <label for="course_detail" class="form-label">รายละเอียดคอร์ส:</label>
                <textarea class="form-control" id="course_detail" name="course_detail"><?= $row->course_detail ?></textarea>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="course_price" class="form-label">ราคาคอร์ส:</label>
                <input type="number" class="form-control" id="course_price" name="course_price" value="<?= $row->course_price ?>" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="course_amount" class="form-label">จำนวนครั้ง:</label>
                <input type="number" class="form-control" id="course_amount" name="course_amount" value="<?= $row->course_amount ?>" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label for="course_type_id" class="form-label">ประเภทคอร์ส:</label>
                <select class="form-select" id="course_type_id" name="course_type_id" required>
                  <option value="" disabled>โปรดเลือก</option>
                  <?php
                  // ดึงข้อมูลประเภทคอร์สจากฐานข้อมูล
                  $sql_course_type = "SELECT * FROM course_type";
                  $result_course_type = $conn->query($sql_course_type);
                  while ($row_course_type = $result_course_type->fetch_object()) {
                    $selected = ($row_course_type->course_type_id == $row->course_type_id) ? 'selected' : '';
                    echo "<option value='" . $row_course_type->course_type_id . "' $selected>" . $row_course_type->course_type_name . "</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="course_start" class="form-label">เริ่มคอร์ส (พ.ศ.):</label>
                <input type="text" class="form-control date-mask" id="course_start" name="course_start"
                       value="<?php 
                            // แปลงวันที่จากฐานข้อมูล (YYYY-MM-DD) เป็น timestamp
                            $timestamp = strtotime($row->course_start); 
                            // แปลง timestamp เป็น พ.ศ. และจัดรูปแบบเป็น dd/mm/YYYY
                            echo date('d/m/Y', $timestamp + 543 * 365 * 24 * 60 * 60); 
                       ?>" required>
                <div id="course_start_error" class="invalid-feedback" style="display: none;">กรอกวันที่ผิดพลาด</div> 

              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="course_end" class="form-label">สิ้นสุดคอร์ส (พ.ศ.):</label>
                <input type="text" class="form-control date-mask" id="course_end" name="course_end"
                       value="<?php 
                            // แปลงวันที่จากฐานข้อมูล (YYYY-MM-DD) เป็น timestamp
                            $timestamp = strtotime($row->course_end); 
                            // แปลง timestamp เป็น พ.ศ. และจัดรูปแบบเป็น dd/mm/YYYY
                            echo date('d/m/Y', $timestamp + 543 * 365 * 24 * 60 * 60); 
                       ?>" required>
                <div id="course_end_error" class="invalid-feedback" style="display: none;">กรอกวันที่ผิดพลาด</div> 
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="course_pic" class="form-label">รูปภาพ:</label>
                <input type="file" class="form-control" id="course_pic" name="course_pic">
                <?php if (!empty($row->course_pic)): ?>
                  <img src="../../img/course/<?= $row->course_pic ?>" alt="รูปภาพคอร์ส" width="100">
                <?php endif; ?>
              </div>
            </div>
            <div class="col-12">
              <div class="mb-3">
                <label for="course_note" class="form-label">หมายเหตุ:</label>
                <textarea class="form-control" id="course_note" name="course_note"><?= $row->course_note ?></textarea>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="course_status" class="form-label">สถานะ:</label>
                <select class="form-select" id="course_status" name="course_status" required>
                  <option value="1" <?= ($row->course_status == 1) ? 'selected' : '' ?>>พร้อมใช้งาน</option>
                  <option value="0" <?= ($row->course_status == 0) ? 'selected' : '' ?>>ไม่พร้อมใช้งาน</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            <button type="submit" class="btn btn-primary" form="editCourseForm<?= $row->course_id ?>">บันทึก</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- update -->


        <?php
        }
        ?>
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
<!-- Modal แสดงประวัติ -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">ประวัติการแก้ไขข้อมูลคอร์ส</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="historyTable">
                        <thead>
                            <tr>
                                <th>วันที่-เวลา</th>
                                <th>การดำเนินการ</th>
                                <th>ผู้ดำเนินการ</th>
                                <th>รายละเอียด</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($log = $result_logs->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo convertToThaiDate($log['created_at']); ?></td>
                                    <td>
                                        <?php
                                        $action_class = '';
                                        $action_text = '';
                                        switch($log['action']) {
                                            case 'update':
                                                $action_class = 'warning';
                                                $action_text = 'แก้ไขข้อมูล';
                                                break;
                                            case 'delete':
                                                $action_class = 'danger';
                                                $action_text = 'ลบข้อมูล';
                                                break;
                                            default:
                                                $action_class = 'primary';
                                                $action_text = $log['action'];
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $action_class; ?>"><?php echo $action_text; ?></span>
                                    </td>
                                    <td><?php echo $log['users_fname'] . ' ' . $log['users_lname']; ?></td>
                                    <td>
                                        <?php
                                        $details = json_decode($log['details'], true);
                                        if ($details && is_array($details)) {
                                            if (isset($details['reason'])) {
                                                echo "รหัสคอร์ส: " . htmlspecialchars($details['deleted_data']['course_id']) . "<br>";
                                                echo "เหตุผล: " . htmlspecialchars($details['reason']) . "<br>";
                                                echo "คอร์สที่ลบ: " . htmlspecialchars($details['deleted_data']['course_name']);
                                            }
                                            if (isset($details['changes'])) {
                                                echo "รหัสคอร์ส: " . htmlspecialchars($details['course_code']) . "<br>";
                                                echo "คอร์ส: " . htmlspecialchars($details['course_name']) . "<br>";
                                                foreach($details['changes'] as $field => $change) {
                                                    echo htmlspecialchars($field) . ": " . 
                                                         htmlspecialchars($change['from']) . " → " . 
                                                         htmlspecialchars($change['to']) . "<br>";
                                                }
                                            }
                                        }
                                        ?>
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
    <!-- Core JS -->
    <!-- sweet Alerts 2 -->
    <!-- <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js" /> -->
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
    <!-- <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script> -->
    <!-- <script src="https://cdn.datatables.net/2.1.3/js/dataTables.js"></script> -->
    <!-- <script src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.js"></script> -->
    <!-- <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.dataTables.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> -->
    <!-- <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js"></script> -->
    <script src="../assets/vendor/libs/cleavejs/cleave.js"></script>
    <script src="../assets/vendor/libs/cleavejs/cleave-phone.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>










    <script type="text/javascript">
        $(document).ready(function() {
              // Initialize history table
    $('#historyTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
        },
        "order": [[0, "desc"]], // เรียงตามวันที่ล่าสุด
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "ทั้งหมด"]],
    });

    // เพิ่ม event listener สำหรับ historyModal
    $('#historyModal').on('shown.bs.modal', function () {
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust()
            .responsive.recalc();
    });

 
    // ตรวจสอบวันที่เมื่อมีการเปลี่ยนแปลงค่าใน input
    $('#course_start, #course_end').on('input', function() {
        var startDate = $('#course_start').val();
        var endDate = $('#course_end').val();

        // แปลงวันที่จากรูปแบบ dd/mm/yyyy (พ.ศ.) เป็น Date object
        var startDateObj = convertThaiDateToDateObj(startDate);
        var endDateObj = convertThaiDateToDateObj(endDate);

        // ตรวจสอบว่าวันที่สิ้นสุดมากกว่าวันที่เริ่มต้นหรือไม่
        if (endDateObj <= startDateObj) {
        $('#addCourseForm button[type="submit"]').prop('disabled', true); // ปิดใช้งานปุ่มบันทึก
        $('#course_end_error').show(); // แสดงข้อความแจ้งเตือน
        } else {
        $('#addCourseForm button[type="submit"]').prop('disabled', false); // เปิดใช้งานปุ่มบันทึก
        $('#course_end_error').hide(); // ซ่อนข้อความแจ้งเตือน
        }
    });

    // ฟังก์ชันแปลงวันที่ไทยเป็น Date object
    function convertThaiDateToDateObj(thaiDate) {
        var parts = thaiDate.split("/");
        var day = parseInt(parts[0]);
        var month = parseInt(parts[1]) - 1; // เดือนใน JavaScript เริ่มจาก 0
        var year = parseInt(parts[2]) - 543; // แปลง พ.ศ. เป็น ค.ศ.
        return new Date(year, month, day);
    }

    // ฟังก์ชันยืนยันการลบ
    function confirmDeleteCourseType(courseTypeId) {
    rusure('sql/course_type-delete.php?id=' + courseTypeId);
    }

    // ตรวจสอบวันที่เมื่อมีการเปลี่ยนแปลงค่าใน input (สำหรับ Modal แก้ไข)
    $('body').on('input', '.modal .date-mask', function() {
        var modal = $(this).closest('.modal'); // หา Modal ที่เกี่ยวข้อง
        var startDate = modal.find('#course_start').val();
        var endDate = modal.find('#course_end').val();

        // แปลงวันที่จากรูปแบบ dd/mm/yyyy (พ.ศ.) เป็น Date object
        var startDateObj = convertThaiDateToDateObj(startDate);
        var endDateObj = convertThaiDateToDateObj(endDate);

        // ตรวจสอบว่าวันที่สิ้นสุดมากกว่าวันที่เริ่มต้นหรือไม่
        if (endDateObj <= startDateObj) {
            modal.find('button[type="submit"]').prop('disabled', true); // ปิดใช้งานปุ่มบันทึก
            modal.find('#course_end_error').show(); // แสดงข้อความแจ้งเตือน
        } else {
            modal.find('button[type="submit"]').prop('disabled', false); // เปิดใช้งานปุ่มบันทึก
            modal.find('#course_end_error').hide(); // ซ่อนข้อความแจ้งเตือน
        }
    });
    
});


        //date input
        $(".date-mask").each(function() {
            new Cleave(this, { // ใช้ 'this' เพื่ออ้างอิงถึง element ปัจจุบันใน loop
                date: true,
                delimiter: "/",
                datePattern: ["d", "m", "Y"]
            });
        });



    // ลบข้อมูล
function confirmDelete(courseId) {
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
            formData.append('course_id', courseId); // ส่ง course_id โดยตรง
            formData.append('password', password);
            formData.append('reason', reason);

            return fetch('sql/course-delete.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'เกิดข้อผิดพลาดในการลบข้อมูล');
                }
                return data;
            })
            .catch(error => {
                throw new Error(error.message || 'เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'ลบข้อมูลสำเร็จ',
                text: 'ข้อมูลคอร์สถูกลบเรียบร้อยแล้ว',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                location.reload();
            });
        }
    }).catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: error.message
        });
    });
}

    // modal insert error

    <?php if(isset($_SESSION['msg_error']) and isset($_SESSION['insert_error'])){ ?>
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

      })
    <?php unset($_SESSION['msg_error']); unset($_SESSION['insert_error']); } ?>

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
    $('#coursesTable').DataTable({
             "pageLength": 25,
            "order": [[1, "desc"]], // เรียงลำดับคอลัมน์ที่ 1 (วันที่สั่งซื้อ) จากมากไปน้อย
            "columnDefs": [
                { "type": "date", "targets": 1 } // กำหนดให้คอลัมน์ที่ 1 เป็นประเภทวันที่
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
            }
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
