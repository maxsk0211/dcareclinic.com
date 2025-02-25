<?php 
  session_start();
  
  include 'chk-session.php';
  require '../dbcon.php';
  // เพิ่มฟังก์ชันนี้ก่อนส่วนของ HTML
function formatHN($id) {
    return 'HN-' . str_pad($id, 5, '0', STR_PAD_LEFT);
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

    <title>จัดการลูกค้า | dcareclinic.com</title>

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
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/> -->
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

  .btn-primary {
    background-color: #4e73df;
    border: none;
    transition: all 0.3s ease;
  }

  .btn-primary:hover {
    background-color: #2e59d9;
    transform: translateY(-2px);
  }

  .table {
    border-collapse: separate;
    border-spacing: 0 15px;
  }

  .table thead th {
    border-bottom: none;
    background-color: #f1f3f9;
    color: #4e73df;
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 1px;
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

  .table td, .table th {
    vertical-align: middle;
    border: none;
    padding: 15px;
  }

  .badge {
    padding: 8px 12px;
    font-size: 0.8rem;
    border-radius: 30px;
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

  .form-control, .form-select {
    border-radius: 10px;
    border: 1px solid #e0e0e0;
    padding: 12px 15px;
  }

  .form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.25);
    border-color: #4e73df;
  }
    /* เพิ่มต่อจาก CSS เดิม */
    .clickable-row {
        transition: background-color 0.3s;
    }
    
    .clickable-row:hover {
        background-color: rgba(78,115,223,0.1) !important;
    }
    
    .clickable-row td:not(:first-child) {
        cursor: pointer;
    }
    /* เพิ่มต่อจาก style เดิม */
.img-upload-preview {
    max-width: 150px;
    height: auto;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    margin-bottom: 10px;
}

.img-upload-container {
    position: relative;
    margin-bottom: 15px;
}

.img-upload-container .remove-image {
    position: absolute;
    top: -10px;
    right: -10px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    padding: 5px;
    cursor: pointer;
    font-size: 12px;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
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
             <?php include 'menu.php'; ?>
          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Menu -->

           

            <!-- / Menu -->

            <!-- Content -->

           <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y  ">

              <!-- Users List Table -->
              <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between">
                  <h5 class="card-title mb-0 alert alert-info">ข้อมูลลูกค้าในระบบทั้งหมด</h5>
                  
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                      <i class="ri-user-add-line me-1"></i> เพิ่มผู้ใช้งาน
                    </button>
                </div>
                <div class="text-end my-2">
                    <button type="button" class="btn btn-danger" onclick="showHistory()">
                        <i class="ri-history-line me-2"></i> ประวัติการแก้ไข
                    </button>
                </div>
                <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="addCustomerModalLabel">เพิ่มข้อมูลลูกค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm" method="post" action="sql/customer-insert.php" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_id_card_number" class="form-label">เลขบัตรประจำตัวประชาชน:</label>
                                <input type="text" class="form-control border-primary" id="cus_id_card_number" name="cus_id_card_number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_birthday" class="form-label">วัน/เดือน/ปี เกิด (พ.ศ.):</label>
                                <input type="text" class="form-control border-primary date-mask" id="cus_birthday" name="cus_birthday" placeholder=" DD/MM/YYYY">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_firstname" class="form-label">ชื่อ:</label>
                                <input type="text" class="form-control border-primary" id="cus_firstname" name="cus_firstname" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_lastname" class="form-label">นามสกุล:</label>
                                <input type="text" class="form-control border-primary" id="cus_lastname" name="cus_lastname" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_title" class="form-label">คำนำหน้าชื่อ:</label>
                                <select class="form-select border-primary" id="cus_title" name="cus_title" required>
                                    <option value="" selected disabled>โปรดเลือก</option>
                                    <option value="นาย">นาย</option>
                                    <option value="นาง">นาง</option>
                                    <option value="นางสาว">นางสาว</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_gender" class="form-label">เพศ:</label>
                                <select class="form-select border-primary" id="cus_gender" name="cus_gender" required>
                                    <option value="" selected disabled>โปรดเลือก</option>
                                    <option value="ชาย">ชาย</option>
                                    <option value="หญิง">หญิง</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_nickname" class="form-label">ชื่อเล่น:</label>
                                <input type="text" class="form-control border-primary" id="cus_nickname" name="cus_nickname">
                            </div>
                        </div>
<!--                         <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_line_id" class="form-label">Line ID:</label>
                                <input type="text" class="form-control border-primary" id="cus_line_id" name="cus_line_id">
                            </div>
                        </div> -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_email" class="form-label">อีเมล:</label>
                                <input type="email" class="form-control border-primary" id="cus_email" name="cus_email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_blood" class="form-label">กรุ๊ปเลือด:</label>
                                <select class="form-select border-primary" id="cus_blood" name="cus_blood" required>
                                    <option value="" selected disabled>โปรดเลือก</option>
<!--                                     <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option> -->

                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="O">O</option>
                                    <option value="AB">AB</option>
                                    <option value="N/A">N/A</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_tel" class="form-label">หมายเลขโทรศัพท์:</label>
                                <input type="tel" class="form-control border-primary" id="cus_tel" name="cus_tel" required>
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_drugallergy" class="form-label">ประวัติการแพ้ยา:</label>
                                <input type="text" class="form-control border-primary" id="cus_drugallergy" name="cus_drugallergy">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_congenital" class="form-label">โรคประจำตัว:</label>
                                <textarea class="form-control border-primary" id="cus_congenital" name="cus_congenital"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="occupation" class="form-label">อาชีพ:</label>
                                <input type="text" name="occupation" id="occupation" class="form-control border-primary">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="height" class="form-label">ส่วนสูง:</label>
                                <input type="text" name="height" id="height" class="form-control border-primary">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="weight" class="form-label">น้ำหนัก:</label>
                                <input type="text" name="weight" id="weight" class="form-control border-primary">
                            </div>
                        </div>

<!--                    <div class="col-12">
                            <div class="mb-3">
                                <label for="cus_remark" class="form-label">หมายเหตุ:</label>
                                <textarea class="form-control border-primary" id="cus_remark" name="cus_remark"></textarea>
                            </div>
                        </div> -->
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="cus_address" class="form-label">ที่อยู่:</label>
                                <textarea class="form-control border-primary" id="cus_address" name="cus_address"></textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cus_district" class="form-label">ตำบล:</label>
                                <input type="text" class="form-control border-primary" id="cus_district" name="cus_district">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cus_city" class="form-label">อำเภอ:</label>
                                <input type="text" class="form-control border-primary" id="cus_city" name="cus_city">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cus_province" class="form-label">จังหวัด:</label>
                                <input type="text" class="form-control border-primary" id="cus_province" name="cus_province" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_postal_code" class="form-label">รหัสไปรษณีย์:</label>
                                <input type="text" class="form-control border-primary" id="cus_postal_code" name="cus_postal_code">
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="emergency_name" class="form-label">ชื่อผู้ติดต่อฉุกเฉิน:</label>
                                <input type="text" class="form-control border-primary" id="emergency_name" name="emergency_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="emergency_tel" class="form-label">เบอร์โทรศัพท์:</label>
                                <input type="text" class="form-control border-primary" id="emergency_tel" name="emergency_tel">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="emergency_note" class="form-label">หมายเหตุ:</label>
                                <textarea class="form-control border-primary" id="emergency_note" name="emergency_note"></textarea>
                            </div>
                        </div>
                        <!-- เพิ่มช่องอัปโหลดรูป -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_image" class="form-label">รูปภาพ:</label>
                                <input type="file" class="form-control border-primary" id="cus_image" name="cus_image" accept="image/*">
                                <small class="text-muted">อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG และ GIF</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


                
    <div class="container">
                <div class="card-datatable table-responsive">
    <table id="customersTable" class="table table-striped table-bordered table-hover table-primary">
    <thead>
        <tr class="">
            <th class="text-center">คำสั่ง</th>
            <th class="text-center">HN</th>  <!-- เปลี่ยนจาก # เป็น HN -->
            <th>เลขบัตรประชาชน</th>
            <th>ชื่อ - นามสกุล</th>
            <th>วันเกิด</th>
            <th>เพศ</th>
            <th>ชื่อเล่น</th>
            <th>อีเมล์</th>
            <th>โทร</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql_show_customers = "SELECT * FROM `customer` ORDER BY `customer`.`cus_id` ASC";
        $result_show_customers = $conn->query($sql_show_customers);
        while ($row = $result_show_customers->fetch_object()) {
        ?>
        <tr class="clickable-row" style="cursor: pointer;">
            <td class="text-center" onclick="event.stopPropagation();">
                <a href="#" class="text-warning" data-bs-toggle="modal" data-bs-target="#editCustomerModal<?= $row->cus_id ?>">
                    <i class="ri-edit-box-line"></i>
                </a>
                <a href="" class="text-danger" onClick="confirmDelete('sql/customer-delete.php?id=<?php echo $row->cus_id; ?>'); return false;">
                    <i class="ri-delete-bin-6-line"></i>
                </a>
            </td>
            <td class="text-center fw-bold text-primary"><?= formatHN($row->cus_id) ?></td>
            <td><?= $row->cus_id_card_number ?></td>
            <td><?= $row->cus_title.$row->cus_firstname." ".$row->cus_lastname ?></td>
            <td><?= $row->cus_birthday ?></td>
            <td><?= $row->cus_gender ?></td>
            <td><?= $row->cus_nickname ?></td>
            <td><?= $row->cus_email ?></td>
            <td><?= $row->cus_tel ?></td>
        </tr>

         <div class="modal fade" id="editCustomerModal<?= $row->cus_id ?>" tabindex="-1" aria-labelledby="editCustomerModalLabel<?= $row->cus_id ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-white" id="editCustomerModalLabel<?= $row->cus_id ?>">แก้ไขข้อมูลลูกค้า</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editCustomerForm<?= $row->cus_id ?>" method="post" action="sql/customer-update.php" enctype="multipart/form-data">
                            <input type="hidden" name="cus_id" value="<?= $row->cus_id ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_id_card_number" class="form-label">เลขบัตรประจำตัวประชาชน:</label>
                                        <input type="text" class="form-control border-primary" id="cus_id_card_number" name="cus_id_card_number" value="<?= $row->cus_id_card_number ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_birthday" class="form-label border-primary">วัน/เดือน/ปี เกิด (พ.ศ.):</label>
                                        <div class="input-group date" id="cus_birthday_datepicker<?= $row->cus_id ?>" data-provide="datepicker" data-date-language="th" data-date-format="dd/mm/yyyy">
                                            <input type="text" class="form-control date-mask border-primary" id="cus_birthday" name="cus_birthday" 
                                                   value="<?php 
                                                        // แปลงวันที่จากฐานข้อมูล (YYYY-MM-DD) เป็น DateTime object
                                                        $date = new DateTime($row->cus_birthday); 
                                                        // บวก 543 ปีเข้าไปใน DateTime object
                                                        $date->modify('+543 year'); 
                                                        // จัดรูปแบบเป็น dd/mm/YYYY (พ.ศ.)
                                                        echo $date->format('d/m/Y'); 
                                                   ?>" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="ri-calendar-2-line"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_firstname" class="form-label">ชื่อ:</label>
                                        <input type="text" class="form-control border-primary" id="cus_firstname" name="cus_firstname" value="<?= $row->cus_firstname ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_lastname" class="form-label">นามสกุล:</label>
                                        <input type="text" class="form-control border-primary" id="cus_lastname" name="cus_lastname" value="<?= $row->cus_lastname ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_title" class="form-label">คำนำหน้าชื่อ:</label>
                                        <select class="form-select border-primary" id="cus_title" name="cus_title" required>
                                            <option value="นาย" <?= ($row->cus_title == 'นาย') ? 'selected' : '' ?>>นาย</option>
                                            <option value="นาง" <?= ($row->cus_title == 'นาง') ? 'selected' : '' ?>>นาง</option>
                                            <option value="นางสาว" <?= ($row->cus_title == 'นางสาว') ? 'selected' : '' ?>>นางสาว</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_gender" class="form-label">เพศ:</label>
                                        <select class="form-select border-primary" id="cus_gender" name="cus_gender" required>
                                            <option value="ชาย" <?= ($row->cus_gender == 'ชาย') ? 'selected' : '' ?>>ชาย</option>
                                            <option value="หญิง" <?= ($row->cus_gender == 'หญิง') ? 'selected' : '' ?>>หญิง</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_nickname" class="form-label">ชื่อเล่น:</label>
                                        <input type="text" class="form-control border-primary" id="cus_nickname" name="cus_nickname" value="<?= $row->cus_nickname ?>">
                                    </div>
                                </div>
<!--                                 <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_line_id" class="form-label">Line ID:</label>
                                        <input type="text" class="form-control" id="cus_line_id" name="cus_line_id" value="<?= $row->cus_line_id ?>">
                                    </div>
                                </div> -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_email" class="form-label">อีเมล:</label>
                                        <input type="email" class="form-control border-primary" id="cus_email" name="cus_email" value="<?= $row->cus_email ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_blood" class="form-label">กรุ๊ปเลือด:</label>
                                        <select class="form-select border-primary" id="cus_blood" name="cus_blood">
                                            <option value="" disabled selected>โปรดเลือก</option>


                                            <option value="A" <?= ($row->cus_blood == 'A') ? 'selected' : '' ?>>A</option>
                                            <option value="B" <?= ($row->cus_blood == 'B') ? 'selected' : '' ?>>B</option>
                                            <option value="O" <?= ($row->cus_blood == 'O') ? 'selected' : '' ?>>O</option>
                                            <option value="AB" <?= ($row->cus_blood == 'AB') ? 'selected' : '' ?>>AB</option>
                                            <option value="N/A" <?= ($row->cus_blood == 'N/A') ? 'selected' : '' ?>>N/A</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_tel" class="form-label">หมายเลขโทรศัพท์:</label>
                                        <input type="tel" class="form-control border-primary" id="cus_tel" name="cus_tel" value="<?= $row->cus_tel ?>" required>
                                    </div>
                                </div>
                                 <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_drugallergy" class="form-label">ประวัติการแพ้ยา:</label>
                                        <input type="text" class="form-control border-primary" id="cus_drugallergy" name="cus_drugallergy" value="<?= $row->cus_drugallergy ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_congenital" class="form-label">โรคประจำตัว:</label>
                                        <textarea class="form-control border-primary" id="cus_congenital" name="cus_congenital"><?= $row->cus_congenital ?></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="occupation" class="form-label">อาชีพ:</label>
                                        <input type="text" name="occupation" id="occupation" class="form-control border-primary" value="<?= $row->occupation ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="height" class="form-label">ส่วนสูง:</label>
                                        <input type="text" name="height" id="height" class="form-control border-primary" value="<?= $row->height ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="weight" class="form-label">น้ำหนัก:</label>
                                        <input type="text" name="weight" id="weight" class="form-control border-primary" value="<?= $row->weight ?>">
                                    </div>
                                </div>
<!--                            <div class="col-12">
                                    <div class="mb-3">
                                        <label for="cus_remark" class="form-label">หมายเหตุ:</label>
                                        <textarea class="form-control" id="cus_remark" name="cus_remark"><?= $row->cus_remark ?></textarea>
                                    </div>
                                </div> -->
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="cus_address" class="form-label">ที่อยู่:</label>
                                        <textarea class="form-control border-primary" id="cus_address" name="cus_address" ><?= $row->cus_address ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cus_district" class="form-label">ตำบล:</label>
                                        <input type="text" class="form-control border-primary" id="cus_district" name="cus_district" value="<?= $row->cus_district ?>" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cus_city" class="form-label">อำเภอ:</label>
                                        <input type="text" class="form-control border-primary" id="cus_city" name="cus_city" value="<?= $row->cus_city ?>" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cus_province" class="form-label">จังหวัด:</label>
                                        <input type="text" class="form-control border-primary" id="cus_province" name="cus_province" value="<?= $row->cus_province ?>" >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_postal_code" class="form-label">รหัสไปรษณีย์:</label>
                                        <input type="text" class="form-control border-primary" id="cus_postal_code" name="cus_postal_code" value="<?= $row->cus_postal_code ?>" >
                                    </div>
                                </div>
                                <hr>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="emergency_name" class="form-label">ชื่อผู้ติดต่อฉุกเฉิน:</label>
                                        <input type="text" class="form-control border-primary" id="emergency_name" name="emergency_name" value="<?= $row->emergency_name ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="emergency_tel" class="form-label">เบอร์โทรศัพท์:</label>
                                        <input type="text" class="form-control border-primary" id="emergency_tel" name="emergency_tel" value="<?= $row->emergency_tel ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="emergency_note" class="form-label">หมายเหตุ:</label>
                                        <textarea class="form-control border-primary" id="emergency_note" name="emergency_note"><?= $row->emergency_note ?></textarea>
                                    </div>
                                </div>
                                <!-- เพิ่มช่องอัปโหลดรูปและแสดงรูปปัจจุบัน -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_image" class="form-label">รูปภาพ:</label>
                                        <div class="mb-2">
                                            <img src="../../img/customer/<?= $row->cus_image ?>" alt="รูปลูกค้า" class="img-thumbnail" style="max-width: 150px;">
                                        </div>
                                        <input type="file" class="form-control border-primary" id="cus_image" name="cus_image" accept="image/*">
                                        <small class="text-muted">อัปโหลดรูปใหม่เพื่อเปลี่ยนรูปภาพ หรือเว้นว่างไว้เพื่อใช้รูปเดิม</small>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" class="btn btn-primary" form="editCustomerForm<?= $row->cus_id ?>">บันทึก</button>
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


<!-- Modal แสดงประวัติการเปลี่ยนแปลง -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">ประวัติการเปลี่ยนแปลงข้อมูลลูกค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <select id="actionFilter" class="form-select">
                        <option value="">แสดงทั้งหมด</option>
                        <option value="create">เพิ่มข้อมูล</option>
                        <option value="update">แก้ไขข้อมูล</option>
                        <option value="delete">ลบข้อมูล</option>
                    </select>
                </div>
                <div class="table-responsive">
                    <table id="historyTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>วันที่-เวลา</th>
                                <th>การดำเนินการ</th>
                                <th>รหัสลูกค้า/ชื่อ</th>
                                <th>รายละเอียด</th>
                                <th>ผู้ดำเนินการ</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            <!-- ข้อมูลจะถูกเพิ่มด้วย JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Core JS -->
    <!-- sweet Alerts 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <!-- <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js" /> -->
    <!-- build:js assets/vendor/js/core.js -->
    <!-- <script src="../assets/vendor/libs/jquery/jquery.js"></script> -->
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
<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>





    <script type="text/javascript">
        // เพิ่มฟังก์ชันสำหรับแสดง modal ประวัติ
function showHistory() {
    const tableBody = $('#historyTableBody');
    tableBody.html('<tr><td colspan="5" class="text-center"><div class="spinner-border text-primary"></div></td></tr>');
    
    $('#historyModal').modal('show');
    loadHistory();
}

function loadHistory(action = '') {
    $.ajax({
        url: 'sql/get-customer-history.php',
        type: 'GET',
        data: { action: action },
        success: function(response) {
            if (response.success) {
                updateHistoryTable(response.data);
            } else {
                $('#historyTableBody').html(`
                    <tr>
                        <td colspan="5" class="text-center text-danger">
                            ${response.message || 'เกิดข้อผิดพลาดในการโหลดข้อมูล'}
                        </td>
                    </tr>
                `);
            }
        },
        error: function() {
            $('#historyTableBody').html(`
                <tr>
                    <td colspan="5" class="text-center text-danger">
                        ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้
                    </td>
                </tr>
            `);
        }
    });
}

function updateHistoryTable(data) {
    const tableBody = $('#historyTableBody');
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
        let customerInfo = '';

        if (item.action === 'update' && item.details.changes) {
            detailsHtml = '<ul class="mb-0">';
            Object.entries(item.details.changes).forEach(([field, change]) => {
                detailsHtml += `<li><strong>${field}:</strong> ${change.from} ➜ ${change.to}</li>`;
            });
            detailsHtml += '</ul>';
            customerInfo = `HN-${String(item.entity_id).padStart(5, '0')} ${item.details.customer_name || ''}`;
        } else if (item.action === 'delete') {
            detailsHtml = `<strong>เหตุผล:</strong> ${item.details.reason || ''}`;
            if (item.details.deleted_data) {
                customerInfo = `HN-${String(item.entity_id).padStart(5, '0')} ${item.details.deleted_data.customer_name || ''}`;
            }
        } else if (item.action === 'create') {
            customerInfo = `HN-${String(item.entity_id).padStart(5, '0')} ${item.details.customer_name || ''}`;
            detailsHtml = '<strong>เพิ่มลูกค้าใหม่</strong>';
        }

        tableBody.append(`
            <tr>
                <td>${item.created_at}</td>
                <td>${actionMap[item.action]}</td>
                <td>${customerInfo}</td>
                <td>${detailsHtml}</td>
                <td>${item.users_fname} ${item.users_lname}</td>
            </tr>
        `);
    });
}

// Event listener สำหรับ filter
$('#actionFilter').change(function() {
    loadHistory($(this).val());
});

// Initialize DataTable for history
$('#historyTable').DataTable({
    "language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
    },
    "order": [[0, "desc"]], // เรียงตามวันที่ล่าสุด
    "pageLength": 10
});
        // เพิ่มต่อจาก script เดิม
function validateImage(input) {
    const file = input.files[0];
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

    if (file) {
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'ไฟล์ไม่ถูกต้อง',
                text: 'กรุณาเลือกไฟล์รูปภาพที่มีนามสกุล .jpg, .jpeg, .png หรือ .gif เท่านั้น'
            });
            input.value = '';
            return false;
        }

        if (file.size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'ไฟล์มีขนาดใหญ่เกินไป',
                text: 'กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน 5MB'
            });
            input.value = '';
            return false;
        }

        // แสดงตัวอย่างรูปภาพ
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = input.closest('.mb-3').querySelector('img');
            if (preview) {
                preview.src = e.target.result;
            }
        }
        reader.readAsDataURL(file);
    }
    return true;
}

// เพิ่ม event listener สำหรับการตรวจสอบไฟล์
document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('change', function() {
        validateImage(this);
    });
});
        //date input
        new Cleave(".date-mask", {
          date: true,
          delimiter: "/",
          datePattern: ["d", "m", "Y"]
        });

    // คงการตั้งค่า DataTable เดิม
    $('#customersTable').DataTable({ 
        "pageLength": 25,
        "order": [[1, "desc"]], // เรียงตาม HN
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
        }
    });

        // เพิ่ม event handler สำหรับการคลิกแถว
    $('#customersTable tbody').on('click', 'tr', function(e) {
        // ถ้าคลิกที่ปุ่มในคอลัมน์แรก จะไม่นำทางไปหน้า detail
        if (!$(e.target).closest('td:first-child').length) {
            var customerId = $(this).find('td:nth-child(2)').text().replace('HN-', '');
            window.location.href = 'customer-detail.php?id=' + parseInt(customerId);
        }
    });

      // ลบข้อมูล
function confirmDelete(url) {
    Swal.fire({
        title: 'ยืนยันการลบข้อมูล',
        html: `
            <form id="deleteForm">
                <div class="mb-3">
                    <label class="form-label">กรุณากรอกรหัสผ่านเพื่อยืนยัน:</label>
                    <input type="password" class="form-control" id="confirmPassword" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">เหตุผลในการลบ:</label>
                    <textarea class="form-control" id="deleteReason"></textarea>
                </div>
            </form>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ยืนยันการลบ',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        preConfirm: () => {
            const password = document.getElementById('confirmPassword').value;
            const reason = document.getElementById('deleteReason').value;
            if (!password) {
                Swal.showValidationMessage('กรุณากรอกรหัสผ่าน');
                return false;
            }
            return { password, reason };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;

            const passwordInput = document.createElement('input');
            passwordInput.type = 'hidden';
            passwordInput.name = 'password';
            passwordInput.value = result.value.password;
            form.appendChild(passwordInput);

            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'delete_reason';
            reasonInput.value = result.value.reason;
            form.appendChild(reasonInput);

            document.body.appendChild(form);
            form.submit();
        }
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
