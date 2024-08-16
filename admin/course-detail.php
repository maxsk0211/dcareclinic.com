<?php
session_start();

include 'chk-session.php';
require '../dbcon.php';

// รับค่า course_id จาก query parameter
if (isset($_GET['id'])) {
    $course_id = $_GET['id'];

    // ทำความสะอาดข้อมูล (Sanitize) เพื่อป้องกัน SQL injection
    $course_id = mysqli_real_escape_string($conn, $course_id);

    // ดึงข้อมูลคอร์สจากฐานข้อมูล
    $sql = "SELECT * FROM course WHERE course_id = '$course_id'";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_object()) {
        // ดึงข้อมูลสาขาจากฐานข้อมูล
        $sql_branch = "SELECT branch_name FROM branch WHERE branch_id = " . $row->branch_id;
        $result_branch = $conn->query($sql_branch);
        $branch_name = $result_branch->fetch_object()->branch_name;

        // ดึงข้อมูลประเภทคอร์สจากฐานข้อมูล
        $sql_course_type = "SELECT course_type_name FROM course_type WHERE course_type_id = " . $row->course_type_id;
        $result_course_type = $conn->query($sql_course_type);
        $course_type_name = $result_course_type->fetch_object()->course_type_name;
    } else {
        // กรณีที่ไม่พบข้อมูลคอร์ส หรือเกิดข้อผิดพลาดในการ query
        $_SESSION['msg_error'] = "ไม่พบข้อมูลคอร์ส หรือเกิดข้อผิดพลาด: " . mysqli_error($conn);
        header("Location: course.php"); 
        exit();
    }
} else {
    // กรณีที่ไม่ได้ส่ง course_id มาใน URL
    $_SESSION['msg_error'] = "ไม่ได้ระบุรหัสคอร์ส";
    header("Location: course.php"); 
    exit();
}
?>

<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter" data-style="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>รายละเอียดคอร์ส | dcareclinic.com</title>

    <meta name="description" content="" />

    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet" />

    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />

    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />

    <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <script src="../assets/vendor/js/helpers.js"></script>

    <script src="../assets/js/config.js"></script>

    <link rel="stylesheet" href="../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/sweetalert2/sweetalert2.css" />
</head>

<body>
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">

            <?php include 'navbar.php'; ?>

            <div class="layout-page">
                <div class="content-wrapper">

                    <?php include 'menu.php'; ?>

                    <div class="container-xxl flex-grow-1 container-p-y">

<div class="card">
              <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title   
 mb-0">รายละเอียดคอร์ส</h5>
                <a href="course.php" class="btn btn-secondary">ย้อนกลับ</a>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-6 d-flex justify-content-center"> 
                    <div class="mb-3">
                      <label class="form-label"><strong>รูปภาพ:</strong></label><br>
                      <img src="../../img/course/<?= $row->course_pic ?>" alt="รูปภาพคอร์ส" class="img-fluid rounded img-thumbnail" style="width: 400px; height: auto;">
                    </div>
                  </div>
                  <div class="col-md-6 text-end">
                    <div class="row mb-3"> <div class="col-4">
                        <p><strong>รหัส:</strong></p>
                      </div>
                      <div class="col-8">
                        <input type="text" class="form-control" value="<?= str_pad($row->course_id, 6, '0', STR_PAD_LEFT); ?>" readonly> 
                      </div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-4">
                        <p><strong>ชื่อคอร์ส:</strong></p>
                      </div>
                      <div class="col-8">
                        <input type="text" class="form-control" value="<?= $row->course_name ?>" readonly>
                      </div>
                    </div>
                    <div class="row mt-3">
                      <div class="col-4">
                        <p><strong>รายละเอียดคอร์ส:</strong></p>
                      </div>
                      <div class="col-8">
                        <textarea class="form-control" readonly><?= $row->course_detail ?></textarea>                     
                      </div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-4">
                        <p><strong>ประเภท:</strong></p>
                      </div>
                      <div class="col-8">
                        <select class="form-select" disabled> 
                          <option value="<?= $row->course_type_id ?>" selected><?= $course_type_name ?></option>
                        </select>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-4">
                        <p><strong>จำนวน:</strong></p>
                      </div>
                      <div class="col-6">
                        <input type="number" class="form-control" value="<?= $row->course_amount ?>" readonly>
                      </div>
                      <div class="col-2">
                        <p>ครั้ง</p> 
                      </div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-4">
                        <p><strong>ราคา:</strong></p>
                      </div>
                      <div class="col-6">
                        <input type="number" class="form-control" value="<?= $row->course_price ?>" readonly>
                      </div>
                      <div class="col-2">
                        <p>บาท</p> 
                      </div>
                    </div>

                    <div class="row mb-3">
                      <div class="col-4">
                        <p><strong>เริ่ม:</strong></p>
                      </div>
                      <div class="col-8">
                        <input type="text" class="form-control date-mask" value="<?php 
                          $timestamp = strtotime($row->course_start); 
                          echo date('d/m/Y', $timestamp + 543 * 365 * 24 * 60 * 60); 
                        ?>" readonly>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-4">
                        <p><strong>สิ้นสุด:</strong></p>
                      </div>
                      <div class="col-8">
                        <input type="text" class="form-control date-mask" value="<?php 
                          $timestamp = strtotime($row->course_end); 
                          echo date('d/m/Y', $timestamp + 543 * 365 * 24 * 60 * 60); 
                        ?>" readonly>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <div class="col-4">
                        <p><strong>สถานะ:</strong></p>
                      </div>
                      <div class="col-8 text-start">
                          <?php if ($row->course_status == 1): ?>
                              <span class="badge bg-success">พร้อมใช้งาน</span>
                          <?php else: ?>
                              <span class="badge bg-danger">ไม่พร้อมใช้งาน</span>
                          <?php endif ?>
                      </div>
                    </div>
                    <div class="row mt-3">
                      <div class="col-4">
                        <p><strong>หมายเหตุ:</strong></p>
                      </div>
                      <div class="col-8">
                        <textarea class="form-control" readonly><?= $row->course_note ?></textarea>                     
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

<div class="layout-overlay layout-menu-toggle"></div>

<div class="drag-target"></div>

<script src="../assets/vendor/libs/jquery/jquery.js"></script>
<script src="../assets/vendor/libs/popper/popper.js"></script>
<script src="../assets/vendor/js/bootstrap.js"></script>
<script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
<script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="../assets/vendor/libs/hammer/hammer.js"></script>

<script src="../assets/vendor/js/menu.js"></script>
<script src="../assets/js/main.js"></script>

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
  // ... (โค้ด JavaScript อื่นๆ) ...
</body>
</html>