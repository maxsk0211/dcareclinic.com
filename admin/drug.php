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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css"> 
    <!-- Chart.js สำหรับแสดงกราฟ -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>

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
/*    border: none;*/
    border-radius: 15px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    overflow: hidden;
  }

  .card:hover {
    box-shadow: 0 0 30px rgba(0,0,0,0.15);
  }

  .card-header {
    background-color: #4e73df;
    color: white;
/*    border-bottom: none;*/
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

  .btn-info:hover {
    background-color: #2a9aab;
    border-color: #2a9aab;
  }

  .btn-warning {
    background-color: #f6c23e;
    border-color: #f6c23e;
    color: #333;
  }

  .btn-warning:hover {
    background-color: #dda20a;
    border-color: #dda20a;
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
    vertical-align: middle;
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
  .stock-summary-card {
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 15px;
  }

  .stock-summary-card h6 {
      margin-bottom: 10px;
      font-size: 0.9rem;
  }

  .stock-summary-card h3 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: bold;
  }

  #stockReportTable th {
      background-color: #4e73df;
      color: white;
  }

  .export-btn {
      min-width: 120px;
  }

  .filter-section {
      background-color: #f8f9fa;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
  }

  #stockValueChart {
      min-height: 300px;
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
              <div class="card border-2 border-primary">
                <div class="card mb-4">
                  <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="card-title mb-0 text-white">ข้อมูลยาในระบบทั้งหมด</h5>
                      <div>
                          <button type="button" class="btn btn-danger me-2" onclick="showDrugHistory()">
                              <i class="ri-history-line me-1"></i> ประวัติการเปลี่ยนแปลง
                          </button>
                          <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#addDrugModal">
                              <i class="ri-medicine-bottle-line me-1"></i> เพิ่มยา
                          </button>
                          <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                              <i class="ri-scales-line me-1"></i> จัดการหน่วยนับ
                          </button>
                      </div>
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
<div class="modal fade" id="addDrugModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white">เพิ่มยาใหม่</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="sql/drug-insert.php" method="post" enctype="multipart/form-data">
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
                <label for="drug_pic" class="form-label">รูปภาพ:</label>
                <input type="file" class="form-control" id="drug_pic" name="drug_pic">
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
        <h5 class="modal-title text-white" id="addUnitModalLabel ">จัดการหน่วยนับ</h5>
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


<div class="card">
  <div class="card-body">
    <div class="text-end mb-2">
        <button type="button" class="btn btn-primary me-2" onclick="showStockReport()">
            <i class="ri-file-list-3-line me-1"></i> รายงานสต็อค
        </button>
        <button type="button" class="btn btn-info" onclick="showTransactionReport()">
            <i class="ri-exchange-line me-1"></i> รายงานการเบิกยา
        </button>
    </div>
    <div class="table-responsive">
      <table id="drugTable" class="table table-hover">
        <thead>
          <tr>
            <th>รหัสยา</th>
            <th>ชื่อยา</th>
            <th>สาขา</th>
            <th>ประเภทยา</th>
            <th>จำนวนคงเหลือ/หน่วยนับ</th>
            <th>สถานะ</th>
            <th class="text-center">จัดการ</th>
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
                    <td><a href="drug-detail.php?drug_id=<?= $row->drug_id?>"><?php echo formatId($row->drug_id); ?></a></td>
                    <td><a href="drug-detail.php?drug_id=<?= $row->drug_id?>"><?php echo $row->drug_name; ?></a></td>
                    <td><a href="drug-detail.php?drug_id=<?= $row->drug_id?>"><?php echo $row->branch_name; ?></a></td>
                    <td><a href="drug-detail.php?drug_id=<?= $row->drug_id?>"><?php echo $row->drug_type_name; ?></a></td>
                    <td><a href="drug-detail.php?drug_id=<?= $row->drug_id?>"><?php echo $row->drug_amount." ".$row->unit_name; ?></a></td>
                    <td>
                      <?php if ($row->drug_status == 1): ?>
                        <span class="badge badge-success">พร้อมใช้งาน</span>
                      <?php else: ?>
                        <span class="badge badge-danger">ไม่พร้อมใช้งาน</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center">
                      <a href="#" class="text-primary me-2" data-bs-toggle="modal" data-bs-target="#editDrugModal<?php echo $row->drug_id; ?>">
                        <i class="ri-edit-line"></i>
                      </a>
                      <a href="#" class="text-danger" 
                         onClick="confirmDelete(<?php echo $row->drug_id; ?>); return false;"
                         data-id="<?php echo $row->drug_id; ?>">
                          <i class="ri-delete-bin-6-line"></i>
                      </a>
                    </td>
                </tr>


                <!-- update  modal -->
<div class="modal fade" id="editDrugModal<?php echo $row->drug_id; ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white">แก้ไขข้อมูลยา</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="sql/drug-update.php" method="post" enctype="multipart/form-data">
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
                <label for="drug_pic" class="form-label">รูปภาพ:</label>
                <input type="file" class="form-control" id="drug_pic" name="drug_pic">
                <?php if (!empty($row->drug_pic)): ?>
                  <img src="../../img/drug/<?= $row->drug_pic ?>" alt="รูปภาพยา" width="100">
                <?php endif; ?>
                <br>
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


<!-- เพิ่มที่ส่วนท้ายของหน้า ก่อนปิด body -->
<div class="modal fade" id="drugHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ประวัติการเปลี่ยนแปลงข้อมูลยา</h5>
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
                                <th>วันที่</th>
                                <th>การกระทำ</th>
                                <th>ชื่อยา</th>
                                <th>รายละเอียด</th>
                                <th>ผู้ดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody id="drugHistoryTableBody">
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
                <h5 class="modal-title text-white">รายงานสต็อคยาคงเหลือ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- ส่วนฟิลเตอร์ -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label class="form-label">ประเภทยา</label>
                        <select class="form-select" id="drugTypeFilter">
                            <option value="">ทั้งหมด</option>
                            <!-- จะเพิ่ม options ด้วย JavaScript -->
                        </select>
                    </div>
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

                <!-- ตารางแสดงข้อมูล -->
                <div class="table-responsive">
                    <table id="stockReportTable" class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>รหัสยา</th>
                                <th>ชื่อยา</th>
                                <th>ประเภทยา</th>
                                <th>คงเหลือ</th>
                                <th>หน่วยนับ</th>
                                <th>ต้นทุน/หน่วย</th>
                                <th>มูลค่ารวม</th>
                                <th>สถานะ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- จะเพิ่มข้อมูลด้วย JavaScript -->
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

<!-- Modal สำหรับรายงานการเบิกยา -->
<div class="modal fade" id="transactionReportModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">รายงานการเบิกยาเข้า-ออก</h5>
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
                    <div class="col-md-3">
                        <label class="form-label">ประเภทยา</label>
                        <select class="form-select" id="transDrugType">
                            <option value="">ทั้งหมด</option>
                            <!-- จะเพิ่ม options ด้วย JavaScript -->
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

                <!-- ตารางแสดงข้อมูล -->
                <div class="table-responsive">
                    <table id="transactionTable" class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>วันที่-เวลา</th>
                                <th>รหัสยา</th>
                                <th>ชื่อยา</th>
                                <th>ประเภทยา</th>
                                <th>ประเภทรายการ</th>
                                <th>จำนวน</th>
                                <th>หน่วยนับ</th>
                                <th>ราคา/หน่วย</th>
                                <th>มูลค่ารวม</th>
                                <th>ผู้ดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- จะเพิ่มข้อมูลด้วย JavaScript -->
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
    <!--/ Layout wrapper -->

    <!-- Core JS -->
    <!-- sweet Alerts 2 -->
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
    <script src="../assets/vendor/libs/cleavejs/cleave.js"></script>
    <script src="../assets/vendor/libs/cleavejs/cleave-phone.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>













    <script type="text/javascript">
      // ฟังก์ชันแสดง Modal รายงาน
function showStockReport() {
    loadDrugTypes();
    loadStockData();
    $('#stockReportModal').modal('show');
}

// โหลดข้อมูลประเภทยา
function loadDrugTypes() {
    $.ajax({
        url: 'sql/get-drug-types.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success && Array.isArray(response.data)) {
                const select = $('#drugTypeFilter');
                select.empty().append('<option value="">ทั้งหมด</option>');
                // เปลี่ยนการเข้าถึง property ให้ตรงกับ API response
                response.data.forEach(type => {
                    select.append(`<option value="${type.drug_type_id}">${type.drug_type_name}</option>`);
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading drug types:', error);
        }
    });
}

// โหลดข้อมูลสต็อค
function loadStockData() {
    const filters = {
        stock_type: 'drug',
        typeFilter: $('#drugTypeFilter').val(), // เปลี่ยนเป็น typeFilter ตามที่ backend รับ
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
                <td colspan="8" class="text-center">ไม่พบข้อมูล</td>
            </tr>
        `);
        return;
    }

    items.forEach(item => {
        // ฟังก์ชันจัดรูปแบบรหัสยา
        const formatDrugId = (id) => {
            return `D-${String(id).padStart(6, '0')}`;
        };

        // ปรับการแสดงผลประเภทยา
        const typeName = item.type_name || item.drug_type_name || 'ไม่ระบุประเภท';
        const amount = parseFloat(item.amount || item.drug_amount || 0);
        const cost = parseFloat(item.cost || item.drug_cost || 0);
        const totalValue = amount * cost;

        const row = `
            <tr>
                <td>${formatDrugId(item.drug_id)}</td>
                <td>${item.drug_name}</td>
                <td>${typeName}</td>
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

// ฟังก์ชันแสดงสถานะ (ไม่ต้องเปลี่ยนแปลง)
function getStatusBadge(amount) {
    if (amount <= 0) {
        return '<span class="badge bg-danger">หมด</span>';
    } else if (amount < 10) {
        return '<span class="badge bg-warning">ต่ำกว่าเกณฑ์</span>';
    }
    return '<span class="badge bg-success">ปกติ</span>';
}

// Event Listeners
$('#drugTypeFilter, #stockStatusFilter').change(loadStockData);
$('#startDate, #endDate').change(loadStockData);

function showDrugHistory() {
    // แสดง loading
    const tableBody = $('#drugHistoryTableBody');
    tableBody.html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary"></div></td></tr>');
    
    $('#drugHistoryModal').modal('show');

    // ดึงข้อมูลประวัติ
    loadDrugHistory();
}

function loadDrugHistory(action = '') {
    $.ajax({
        url: 'sql/get-drug-history.php',
        type: 'GET',
        data: { action: action },
        success: function(response) {
            if (response.success) {
                updateDrugHistoryTable(response.data);
            } else {
                $('#drugHistoryTableBody').html(`
                    <tr>
                        <td colspan="5" class="text-center text-danger">
                            ${response.message || 'เกิดข้อผิดพลาดในการโหลดข้อมูล'}
                        </td>
                    </tr>
                `);
            }
        },
        error: function() {
            $('#drugHistoryTableBody').html(`
                <tr>
                    <td colspan="5" class="text-center text-danger">
                        ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้
                    </td>
                </tr>
            `);
        }
    });
}

function updateDrugHistoryTable(data) {
    const tableBody = $('#drugHistoryTableBody');
    tableBody.empty();

    if (!data || data.length === 0) {
        tableBody.html(`
            <tr>
                <td colspan="5" class="text-center">ไม่พบประวัติการเปลี่ยนแปลง</td>
            </tr>
        `);
        return;
    }

    // ฟังก์ชันช่วยจัดรูปแบบรหัสยา
    const formatDrugId = (id) => {
        return id ? `D-${String(id).padStart(6, '0')}` : '';
    };

    data.forEach(item => {
        const actionMap = {
            'create': '<span class="badge bg-success">เพิ่มข้อมูล</span>',
            'update': '<span class="badge bg-warning">แก้ไขข้อมูล</span>',
            'delete': '<span class="badge bg-danger">ลบข้อมูล</span>'
        };

        let detailsHtml = '';
        let drugInfo = '';

        // จัดการแสดงข้อมูลตามประเภทการกระทำ
        if (item.action === 'update' && item.details.changes) {
            detailsHtml = '<ul class="mb-0">';
            Object.entries(item.details.changes).forEach(([field, change]) => {
                detailsHtml += `<li><strong>${field}:</strong> ${change.from} ➜ ${change.to}</li>`;
            });
            detailsHtml += '</ul>';
            drugInfo = `${formatDrugId(item.entity_id)} ${item.details.drug_name || ''}`;

        } else if (item.action === 'delete') {
            detailsHtml = `<strong>เหตุผล:</strong> ${item.details.reason || ''}`;
            if (item.details.deleted_data) {
                drugInfo = `${formatDrugId(item.entity_id)} ${item.details.deleted_data.drug_name || ''}`;
            }

        } else if (item.action === 'create') {
            drugInfo = `${formatDrugId(item.entity_id)} ${item.details.drug_name || ''}`;
            detailsHtml = '<strong>เพิ่มยาใหม่</strong>';
            if (item.details.properties) {
                detailsHtml += `<br>คุณสมบัติ: ${item.details.properties}`;
            }
            if (item.details.advice) {
                detailsHtml += `<br>คำแนะนำ: ${item.details.advice}`;
            }
        }

        tableBody.append(`
            <tr>
                <td>${item.created_at}</td>
                <td>${actionMap[item.action]}</td>
                <td>${drugInfo}</td>
                <td>${detailsHtml}</td>
                <td>${item.users_fname} ${item.users_lname}</td>
            </tr>
        `);
    });
}

// Event listener สำหรับ filter
$('#actionFilter').change(function() {
    loadDrugHistory($(this).val());
});
 
// ฟังก์ชัน Export Excel
async function exportToExcel() {
    try {
        const filters = {
            startDate: $('#startDate').val(),
            endDate: $('#endDate').val(),
            drugType: $('#drugTypeFilter').val(),
            stockStatus: $('#stockStatusFilter').val()
        };

        // ดึงข้อมูลล่าสุด
        const response = await $.ajax({
            url: 'sql/get-stock-report.php',
            type: 'GET',
            data: filters
        });

        if (!response.success) {
            throw new Error(response.message || 'Failed to fetch data');
        }

        // สร้าง Workbook
        const wb = XLSX.utils.book_new();
        
        // สร้าง Worksheet สำหรับสรุป
        const summaryData = [
            ['รายงานสต็อคยาคงเหลือ'],
            ['วันที่ออกรายงาน:', new Date().toLocaleString('th-TH')],
            [''],
            ['สรุปภาพรวม'],
            ['จำนวนรายการทั้งหมด:', response.summary.totalItems],
            ['มูลค่ารวม:', response.summary.totalValue.toLocaleString('th-TH', {style: 'currency', currency: 'THB'})],
            ['รายการต่ำกว่าเกณฑ์:', response.summary.lowStockItems],
            ['รายการที่หมด:', response.summary.outOfStockItems]
        ];
        
        const wsSummary = XLSX.utils.aoa_to_sheet(summaryData);
        XLSX.utils.book_append_sheet(wb, wsSummary, "สรุป");

        // สร้าง Worksheet สำหรับรายละเอียด
        const headers = [
            'รหัสยา',
            'ชื่อยา',
            'ประเภทยา',
            'คงเหลือ',
            'หน่วยนับ',
            'ต้นทุน/หน่วย',
            'มูลค่ารวม',
            'สถานะ'
        ];

        const wsData = [headers];
        
        response.items.forEach(item => {
            let status = '';
            const amount = parseFloat(item.amount || item.drug_amount || 0);
            const cost = parseFloat(item.cost || item.drug_cost || 0);
            const totalValue = amount * cost;
            // กำหนดสถานะ
            if (amount <= 0) status = 'หมด';
            else if (amount < 10) status = 'ต่ำกว่าเกณฑ์';
            else status = 'ปกติ';

            // ฟังก์ชันจัดรูปแบบรหัสยา
            const formatDrugId = (id) => {
                return `D-${String(id).padStart(6, '0')}`;
            };

            // จัดการข้อมูลประเภทยา
            const typeName = item.type_name || item.drug_type_name || 'ไม่ระบุประเภท';

            wsData.push([
                formatDrugId(item.drug_id),
                item.drug_name,
                typeName,
                amount,
                item.unit_name,
                cost,
                totalValue,
                status
            ]);
        });

        const wsDetails = XLSX.utils.aoa_to_sheet(wsData);
        XLSX.utils.book_append_sheet(wb, wsDetails, "รายละเอียด");

        // กำหนดความกว้างคอลัมน์
        const wscols = [
            {wch: 10}, // รหัสยา
            {wch: 30}, // ชื่อยา
            {wch: 20}, // ประเภทยา
            {wch: 10}, // คงเหลือ
            {wch: 10}, // หน่วยนับ
            {wch: 15}, // ต้นทุน/หน่วย
            {wch: 15}, // มูลค่ารวม
            {wch: 15}  // สถานะ
        ];
        wsDetails['!cols'] = wscols;

        // Export ไฟล์
        const fileName = `stock_report_${new Date().toISOString().slice(0, 10)}.xlsx`;
        XLSX.writeFile(wb, fileName);

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: error.message
        });
    }
}


async function exportToPDF() {
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

        // สร้างเนื้อหา HTML
        const currentDate = new Date().toLocaleDateString('th-TH', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        // สร้าง Header
        const content = `
            <div style="font-family: 'Sarabun', sans-serif;">
                <!-- ส่วนหัว -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <h2 style="margin: 0;">รายงานสต็อคยาคงเหลือ</h2>
                    <p style="margin: 5px 0;">วันที่ออกรายงาน: ${currentDate}</p>
                </div>

                <!-- เงื่อนไขการกรอง -->
                <div style="margin-bottom: 15px; font-size: 14px;">
                    <p>เงื่อนไขการกรอง:</p>
                    <table style="width: 100%; margin-bottom: 10px;">
                        <tr>
                            <td>ประเภทยา: ${$('#drugTypeFilter option:selected').text()}</td>
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
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">รหัสยา</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">ชื่อยา</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">ประเภทยา</th>
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
                                    <td style="border: 1px solid #ddd; padding: 8px;">${$(row).find('td').eq(2).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${$(row).find('td').eq(3).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${$(row).find('td').eq(5).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${$(row).find('td').eq(6).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">
                                        ${getStatusForPDF($(row).find('td').eq(7).text())}
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

        // กำหนดค่า options สำหรับ html2pdf
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


      // ลบข้อมูล
function confirmDelete(drugId) {
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

            // ส่งข้อมูลแบบ FormData
            const formData = new FormData();
            formData.append('drug_id', drugId);
            formData.append('password', password);
            formData.append('reason', reason);

            return fetch('sql/drug-delete.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .catch(error => {
                console.error('Error:', error);
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
                    text: 'ลบข้อมูลยาเรียบร้อยแล้ว',
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

// ฟังก์ชันแสดง Modal รายงานการเบิกยา
function showTransactionReport() {
    loadDrugTypes(); // ใช้ฟังก์ชันเดิมสำหรับโหลดประเภทยา
    loadTransactionData();
    $('#transactionReportModal').modal('show');
}

// ฟังก์ชันโหลดข้อมูลการเบิกยา
function loadTransactionData() {
    const filters = {
        startDate: $('#transStartDate').val(),
        endDate: $('#transEndDate').val(),
        transactionType: $('#transactionType').val(),
        drugType: $('#transDrugType').val(),
        stock_type: 'drug'  // เพิ่มพารามิเตอร์ stock_type
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

// อัพเดทข้อมูลสรุป
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



// อัพเดทการแสดงผลในตาราง
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
        // กำหนดสีและ class ตามประเภทรายการ
        let badgeClass = '';
        switch(item.transaction_type_name) {
            case 'รับเข้า':
                badgeClass = 'bg-success';
                break;
            case 'เบิกออก':
                badgeClass = 'bg-danger';
                break;
            case 'ใช้ในคอร์ส':
                badgeClass = 'bg-info';
                break;
            case 'คืนสต็อก':
                badgeClass = 'bg-warning';
                break;
        }

        const row = `
            <tr>
                <td>${formatDateTime(item.transaction_date)}</td>
                <td>${formatDrugId(item.related_id)}</td>
                <td>${item.item_name}</td>
                <td>${item.type_name}</td>
                <td class="text-center">
                    <span class="badge ${badgeClass}">${item.transaction_type_name}</span>
                </td>
                <td class="text-end">${formatNumber(item.display_quantity)}</td>
                <td>${item.unit_name}</td>
                <td class="text-end">${formatNumber(item.cost_per_unit, 2)}</td>
                <td class="text-end">${formatNumber(item.total_value, 2)}</td>
                <td>${item.users_fname} ${item.users_lname}</td>
                <td>${item.notes}</td>
            </tr>
        `;
        tbody.append(row);
    });
}

// Export Excel
async function exportTransactionToExcel() {
    try {
        const filters = {
            startDate: $('#transStartDate').val(),
            endDate: $('#transEndDate').val(),
            transactionType: $('#transactionType').val(),
            drugType: $('#transDrugType').val(),
            stock_type: 'drug'
        };

        const response = await $.ajax({
            url: 'sql/get-transaction-report.php',
            type: 'GET',
            data: filters
        });

        if (!response.success) {
            throw new Error(response.message || 'Failed to fetch data');
        }

        // สร้าง Workbook
        const wb = XLSX.utils.book_new();
        
        // สร้าง Worksheet สำหรับสรุป
        const summaryData = [
            ['รายงานการเบิกยาเข้า-ออก'],
            ['วันที่ออกรายงาน:', new Date().toLocaleString('th-TH')],
            ['เงื่อนไขการค้นหา:'],
            ['วันที่เริ่มต้น:', $('#transStartDate').val() || '-'],
            ['วันที่สิ้นสุด:', $('#transEndDate').val() || '-'],
            ['ประเภทรายการ:', $('#transactionType option:selected').text()],
            ['ประเภทยา:', $('#transDrugType option:selected').text()],
            [''],
            ['สรุปภาพรวม'],
            ['จำนวนรายการทั้งหมด:', response.summary.totalTransactions],
            ['มูลค่ารวมรับเข้า:', response.summary.totalInValue.toLocaleString('th-TH', {style: 'currency', currency: 'THB'})],
            ['มูลค่ายาใช้ในคอร์ส:', response.summary.totalOutValue.toLocaleString('th-TH', {style: 'currency', currency: 'THB'})],
            ['มูลค่าคงเหลือ:', response.summary.netValue.toLocaleString('th-TH', {style: 'currency', currency: 'THB'})]
        ];
        
        const wsSummary = XLSX.utils.aoa_to_sheet(summaryData);
        XLSX.utils.book_append_sheet(wb, wsSummary, "สรุป");

        // สร้าง Worksheet สำหรับรายละเอียด
        const headers = [
            'วันที่-เวลา',
            'รหัสยา',
            'ชื่อยา',
            'ประเภทยา',
            'ประเภทรายการ',
            'จำนวน',
            'หน่วยนับ',
            'ราคา/หน่วย',
            'มูลค่ารวม',
            'ผู้ดำเนินการ',
            'หมายเหตุ'
        ];

        const wsData = [headers];
        
        response.items.forEach(item => {
            // แปลงรหัสยาให้อยู่ในรูปแบบ D-000001
            const formattedDrugId = `D-${String(item.related_id).padStart(6, '0')}`;
            
            wsData.push([
                new Date(item.transaction_date).toLocaleString('th-TH'),
                formattedDrugId,
                item.item_name,
                item.type_name,
                item.transaction_type_name,
                item.display_quantity,
                item.unit_name,
                item.cost_per_unit,
                item.total_value,
                `${item.users_fname} ${item.users_lname}`,
                item.notes
            ]);
        });

        const wsDetails = XLSX.utils.aoa_to_sheet(wsData);

        // กำหนดความกว้างคอลัมน์
        const wscols = [
            {wch: 20}, // วันที่-เวลา
            {wch: 10}, // รหัสยา
            {wch: 30}, // ชื่อยา
            {wch: 20}, // ประเภทยา
            {wch: 15}, // ประเภทรายการ
            {wch: 10}, // จำนวน
            {wch: 10}, // หน่วยนับ
            {wch: 12}, // ราคา/หน่วย
            {wch: 12}, // มูลค่ารวม
            {wch: 20}, // ผู้ดำเนินการ
            {wch: 30}  // หมายเหตุ
        ];
        wsDetails['!cols'] = wscols;

        XLSX.utils.book_append_sheet(wb, wsDetails, "รายการเบิกยา");

        // Export ไฟล์
        const fileName = `transaction_report_${new Date().toISOString().slice(0, 10)}.xlsx`;
        XLSX.writeFile(wb, fileName);

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: error.message
        });
    }
}

// Export PDF
async function exportTransactionToPDF() {
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
                    <h2 style="margin: 0;">รายงานการเบิกยาเข้า-ออก</h2>
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
                            <td>ประเภทยา: ${$('#transDrugType option:selected').text()}</td>
                        </tr>
                    </table>
                </div>

                <!-- ส่วนสรุป -->
                <div style="margin-bottom: 20px;">
                    <h3 style="margin-bottom: 10px;">สรุปภาพรวม</h3>
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">จำนวนรายการทั้งหมด:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><strong>${$('#totalTransactions').text()}</strong></td>
                            <td style="padding: 8px; border: 1px solid #ddd;">มูลค่าคงเหลือ:</td>
                            <td style="padding: 8px; border: 1px solid #ddd;"><strong>${$('#netValue').text()}</strong></td>
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
                    <h3 style="margin-bottom: 10px;">รายการเบิกยา</h3>
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <thead>
                            <tr style="background-color: #4e73df; color: white;">
                                <th style="border: 1px solid #ddd; padding: 8px;">วันที่-เวลา</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">รหัสยา</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">ชื่อยา</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">ประเภท</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">ประเภทรายการ</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">จำนวน</th>
                                <th style="border: 1px solid #ddd; padding: 8px;">หน่วยนับ</th>
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
                                    <td style="border: 1px solid #ddd; padding: 8px;">${$(row).find('td').eq(3).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">${$(row).find('td').eq(4).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${$(row).find('td').eq(5).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">${$(row).find('td').eq(6).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${$(row).find('td').eq(7).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">${$(row).find('td').eq(8).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">${$(row).find('td').eq(9).text()}</td>
                                    <td style="border: 1px solid #ddd; padding: 8px;">${$(row).find('td').eq(10).text()}</td>
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

        const pdf = await html2pdf().set(opt).from(content).save();
        
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

// ฟังก์ชันช่วยจัดรูปแบบ
function formatDateTime(dateTimeStr) {
    const date = new Date(dateTimeStr);
    return date.toLocaleString('th-TH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatDrugId(id) {
    return `D-${String(id).padStart(6, '0')}`;
}

// อัพเดท Event Listeners
$('#transactionType, #transDrugType, #transStartDate, #transEndDate').change(function() {
    loadTransactionData();
});

// เพิ่มคอลัมน์หมายเหตุในตาราง
// Event Listeners
$(document).ready(function() {
    // โหลดข้อมูลเริ่มต้น
    loadDrugTypes();
    loadStockData();

    // เพิ่ม event listeners สำหรับ filters
    $('#drugTypeFilter, #stockStatusFilter').change(function() {
        loadStockData();
    });
});

// ฟังก์ชันจัดรูปแบบเงิน
function formatCurrency(amount) {
    return amount.toLocaleString('th-TH', {
        style: 'currency',
        currency: 'THB',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// ฟังก์ชันจัดรูปแบบตัวเลข
function formatNumber(number) {
    return number.toLocaleString('th-TH', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}
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
