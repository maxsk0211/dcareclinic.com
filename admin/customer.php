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
                  <h5 class="card-title mb-0 alert alert-info">ข้อมูลลูกค้าในระบบทั้งหมด</h5>
                  <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addCustomerModal">เพิ่มลูกค้า</button>
                </div>
                <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCustomerModalLabel">เพิ่มข้อมูลลูกค้า</h5>
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_line_id" class="form-label">Line ID:</label>
                                <input type="text" class="form-control border-primary" id="cus_line_id" name="cus_line_id">
                            </div>
                        </div>
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
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
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
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="cus_remark" class="form-label">หมายเหตุ:</label>
                                <textarea class="form-control border-primary" id="cus_remark" name="cus_remark"></textarea>
                            </div>
                        </div>
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cus_image" class="form-label">รูปภาพ:</label>
                                <input type="file" class="form-control border-primary" id="cus_image" name="cus_image">
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


                

                <div class="card-datatable table-responsive">
    <table id="customersTable" class="table table-striped table-bordered table-hover table-primary">
    <thead>
        <tr class="">
            <th class="text-center">คำสั่ง</th>
            <th class="text-center">#</th>
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
        $i = 1;
        $sql_show_customers = "SELECT * FROM `customer` ORDER BY `customer`.`cus_id` ASC";
        $result_show_customers = $conn->query($sql_show_customers);
        while ($row = $result_show_customers->fetch_object()) {
        ?>
        <tr>
            <td class="text-center">
                <a href="#" class="text-warning" data-bs-toggle="modal" data-bs-target="#editCustomerModal<?= $row->cus_id ?>"><i class="ri-edit-box-line"></i></a>
                    <a href="" class="text-danger" onClick="confirmDelete('sql/customer-delete.php?id=<?php echo $row->cus_id; ?>'); return false;"><i class="ri-delete-bin-6-line"></i></a>
            </td>
            <td class="text-center"><?= $i++ ?></td>
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
                        <h5 class="modal-title" id="editCustomerModalLabel<?= $row->cus_id ?>">แก้ไขข้อมูลลูกค้า</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editCustomerForm<?= $row->cus_id ?>" method="post" action="sql/customer-update.php" enctype="multipart/form-data">
                            <input type="hidden" name="cus_id" value="<?= $row->cus_id ?>">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_id_card_number" class="form-label">เลขบัตรประจำตัวประชาชน:</label>
                                        <input type="text" class="form-control" id="cus_id_card_number" name="cus_id_card_number" value="<?= $row->cus_id_card_number ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_birthday" class="form-label">วัน/เดือน/ปี เกิด (พ.ศ.):</label>
                                        <div class="input-group date" id="cus_birthday_datepicker<?= $row->cus_id ?>" data-provide="datepicker" data-date-language="th" data-date-format="dd/mm/yyyy">
                                            <input type="text" class="form-control date-mask" id="cus_birthday" name="cus_birthday" 
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
                                        <input type="text" class="form-control" id="cus_firstname" name="cus_firstname" value="<?= $row->cus_firstname ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_lastname" class="form-label">นามสกุล:</label>
                                        <input type="text" class="form-control" id="cus_lastname" name="cus_lastname" value="<?= $row->cus_lastname ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_title" class="form-label">คำนำหน้าชื่อ:</label>
                                        <select class="form-select" id="cus_title" name="cus_title" required>
                                            <option value="นาย" <?= ($row->cus_title == 'นาย') ? 'selected' : '' ?>>นาย</option>
                                            <option value="นาง" <?= ($row->cus_title == 'นาง') ? 'selected' : '' ?>>นาง</option>
                                            <option value="นางสาว" <?= ($row->cus_title == 'นางสาว') ? 'selected' : '' ?>>นางสาว</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_gender" class="form-label">เพศ:</label>
                                        <select class="form-select" id="cus_gender" name="cus_gender" required>
                                            <option value="ชาย" <?= ($row->cus_gender == 'ชาย') ? 'selected' : '' ?>>ชาย</option>
                                            <option value="หญิง" <?= ($row->cus_gender == 'หญิง') ? 'selected' : '' ?>>หญิง</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_nickname" class="form-label">ชื่อเล่น:</label>
                                        <input type="text" class="form-control" id="cus_nickname" name="cus_nickname" value="<?= $row->cus_nickname ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_line_id" class="form-label">Line ID:</label>
                                        <input type="text" class="form-control" id="cus_line_id" name="cus_line_id" value="<?= $row->cus_line_id ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_email" class="form-label">อีเมล:</label>
                                        <input type="email" class="form-control" id="cus_email" name="cus_email" value="<?= $row->cus_email ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_blood" class="form-label">กรุ๊ปเลือด:</label>
                                        <select class="form-select" id="cus_blood" name="cus_blood">
                                            <option value="" disabled>โปรดเลือก</option>
                                            <option value="A+" <?= ($row->cus_blood == 'A+') ? 'selected' : '' ?>>A+</option>
                                            <option value="A-" <?= ($row->cus_blood == 'A-') ? 'selected' : '' ?>>A-</option>
                                            <option value="B+" <?= ($row->cus_blood == 'B+') ? 'selected' : '' ?>>B+</option>
                                            <option value="B-" <?= ($row->cus_blood == 'B-') ? 'selected' : '' ?>>B-</option>
                                            <option value="O+" <?= ($row->cus_blood == 'O+') ? 'selected' : '' ?>>O+</option>
                                            <option value="O-" <?= ($row->cus_blood == 'O-') ? 'selected' : '' ?>>O-</option>
                                            <option value="AB+" <?= ($row->cus_blood == 'AB+') ? 'selected' : '' ?>>AB+</option>
                                            <option value="AB-" <?= ($row->cus_blood == 'AB-') ? 'selected' : '' ?>>AB-</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_tel" class="form-label">หมายเลขโทรศัพท์:</label>
                                        <input type="tel" class="form-control" id="cus_tel" name="cus_tel" value="<?= $row->cus_tel ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_drugallergy" class="form-label">ประวัติการแพ้ยา:</label>
                                        <input type="text" class="form-control" id="cus_drugallergy" name="cus_drugallergy" value="<?= $row->cus_drugallergy ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_congenital" class="form-label">โรคประจำตัว:</label>
                                        <textarea class="form-control" id="cus_congenital" name="cus_congenital"><?= $row->cus_congenital ?></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="cus_remark" class="form-label">หมายเหตุ:</label>
                                        <textarea class="form-control" id="cus_remark" name="cus_remark"><?= $row->cus_remark ?></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="cus_address" class="form-label">ที่อยู่:</label>
                                        <textarea class="form-control" id="cus_address" name="cus_address" ><?= $row->cus_address ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cus_district" class="form-label">ตำบล:</label>
                                        <input type="text" class="form-control" id="cus_district" name="cus_district" value="<?= $row->cus_district ?>" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cus_city" class="form-label">อำเภอ:</label>
                                        <input type="text" class="form-control" id="cus_city" name="cus_city" value="<?= $row->cus_city ?>" >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="cus_province" class="form-label">จังหวัด:</label>
                                        <input type="text" class="form-control" id="cus_province" name="cus_province" value="<?= $row->cus_province ?>" >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_postal_code" class="form-label">รหัสไปรษณีย์:</label>
                                        <input type="text" class="form-control" id="cus_postal_code" name="cus_postal_code" value="<?= $row->cus_postal_code ?>" >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cus_image" class="form-label">รูปภาพ:</label>
                                        <input type="file" class="form-control" id="cus_image" name="cus_image">
                                        <?php if (!empty($row->cus_image)): ?>
                                            <img src="../img/customer/<?= $row->cus_image ?>" alt="รูปภาพลูกค้า" width="100">
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit"   
 class="btn btn-primary" form="editCustomerForm<?= $row->cus_id ?>">บันทึก</button>
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
        //date input
        new Cleave(".date-mask", {
          date: true,
          delimiter: "/",
          datePattern: ["d", "m", "Y"]
        });



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
    $('#customersTable').DataTable({ 

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
