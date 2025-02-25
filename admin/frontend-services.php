<?php 
  session_start();
  
  include 'chk-session.php';
  require '../dbcon.php';
?>

<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>จัดการบริการบนเว็บไซต์หลัก | dcareclinic.com</title>

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
      
      .thumbnail-preview {
        max-width: 80px;
        max-height: 80px;
        border-radius: 5px;
        object-fit: cover;
      }
    </style>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
      <div class="layout-container">
        <?php include 'navbar.php'; ?>

        <!-- Layout container -->
        <div class="layout-page">
         

          <!-- Content wrapper -->
          <div class="content-wrapper"> 
          	<?php include 'menu.php'; ?>
            <!-- Content -->
            <div class="container-xxl flex-grow-1 container-p-y">
              <!-- Services List -->
              <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between">
                  <h5 class="card-title mb-0">บริการที่แสดงบนเว็บไซต์หลัก</h5>
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                    <i class="ri-add-line me-1"></i> เพิ่มบริการ
                  </button>
                </div>
                <div class="card-datatable table-responsive">
                  <table id="servicesTable" class="table table-striped table-bordered">
                    <thead>
                      <tr>
                        <th width="60" class="text-center">รูปภาพ</th>
                        <th>ชื่อบริการ</th>
                        <th>หมวดหมู่</th>
                        <th width="100" class="text-center">ราคา (บาท)</th>
                        <th width="80" class="text-center">ป้ายกำกับ</th>
                        <th width="80" class="text-center">ลำดับ</th>
                        <th width="100" class="text-center">สถานะ</th>
                        <th width="120" class="text-center">จัดการ</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $sql = "SELECT fs.*, c.course_name, c.course_price, c.course_pic, fc.name as category_name 
                              FROM frontend_services fs
                              JOIN course c ON fs.course_id = c.course_id
                              JOIN frontend_categories fc ON fs.frontend_category_id = fc.id
                              ORDER BY fs.display_order ASC, c.course_name ASC";
                      $result = $conn->query($sql);
                      
                      if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                          // ใช้ราคาที่กำหนดในบริการหน้าเว็บหลัก หรือราคาจากคอร์ส
                          $price = $row['custom_price'] ?? $row['course_price'];
                          
                          // ใช้รูปภาพที่กำหนดสำหรับหน้าเว็บหลัก หรือรูปภาพจากคอร์ส
                          $imgPath = $row['image_path'] ?? $row['course_pic'] ?? 'course.png';
                      ?>
                      <tr>
                        <td class="text-center">
                          <img src="../../img/course/<?php echo $imgPath; ?>" class="thumbnail-preview" alt="<?php echo $row['course_name']; ?>">
                        </td>
                        <td><?php echo $row['course_name']; ?></td>
                        <td><?php echo $row['category_name']; ?></td>
                        <td class="text-end"><?php echo number_format($price, 2); ?></td>
                        <td class="text-center">
                          <?php if ($row['badge_text']) { ?>
                            <span class="badge bg-info"><?php echo $row['badge_text']; ?></span>
                          <?php } ?>
                        </td>
                        <td class="text-center"><?php echo $row['display_order']; ?></td>
                        <td class="text-center">
                          <?php if ($row['status'] == 1) { ?>
                            <span class="badge bg-success">แสดง</span>
                          <?php } else { ?>
                            <span class="badge bg-secondary">ซ่อน</span>
                          <?php } ?>
                        </td>
                        <td class="text-center">
                          <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $row['id']; ?>">
                            <i class="ri-edit-line"></i>
                          </button>
                          <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $row['id']; ?>">
                            <i class="ri-delete-bin-line"></i>
                          </button>
                        </td>
                      </tr>
                      <?php
                        }
                      } else {
                      ?>
                      <tr>
                        <td colspan="8" class="text-center">ไม่พบข้อมูลบริการ</td>
                      </tr>
                      <?php
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <!-- / Content -->

            <!-- Add Service Modal -->
            <div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <form class="modal-content" id="addServiceForm" action="sql/frontend-service-add.php" method="post" enctype="multipart/form-data">
                  <div class="modal-header">
                    <h5 class="modal-title">เพิ่มบริการใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label class="form-label">เลือกคอร์ส</label>
                        <select class="form-select" name="course_id" required>
                          <option value="">-- กรุณาเลือกคอร์ส --</option>
                          <?php
                          $sql_course = "SELECT course_id, course_name, course_price FROM course WHERE course_status = 1 ORDER BY course_name";
                          $result_course = $conn->query($sql_course);
                          
                          if ($result_course->num_rows > 0) {
                            while ($row_course = $result_course->fetch_assoc()) {
                              echo '<option value="'.$row_course['course_id'].'" data-price="'.$row_course['course_price'].'">'.$row_course['course_name'].' - '.number_format($row_course['course_price'], 2).' บาท</option>';
                            }
                          }
                          ?>
                        </select>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">หมวดหมู่</label>
                        <select class="form-select" name="frontend_category_id" required>
                          <option value="">-- กรุณาเลือกหมวดหมู่ --</option>
                          <?php
                          $sql_cat = "SELECT id, name FROM frontend_categories WHERE status = 1 ORDER BY display_order, name";
                          $result_cat = $conn->query($sql_cat);
                          
                          if ($result_cat->num_rows > 0) {
                            while ($row_cat = $result_cat->fetch_assoc()) {
                              echo '<option value="'.$row_cat['id'].'">'.$row_cat['name'].'</option>';
                            }
                          }
                          ?>
                        </select>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">ราคาพิเศษ (บาท)</label>
                        <input type="number" class="form-control" name="custom_price" step="0.01" placeholder="ถ้าว่างไว้จะใช้ราคาจากคอร์ส">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">ราคาปกติสำหรับแสดงส่วนลด (บาท)</label>
                        <input type="number" class="form-control" name="custom_original_price" step="0.01" placeholder="ถ้าต้องการแสดงส่วนลด">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">ข้อความป้ายกำกับ</label>
                        <input type="text" class="form-control" name="badge_text" placeholder="เช่น ขายดี, แนะนำ">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">ระยะเวลาต่อครั้ง (นาที)</label>
                        <input type="number" class="form-control" name="session_duration" placeholder="เช่น 60, 90">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label">ลำดับการแสดงผล</label>
                        <input type="number" class="form-control" name="display_order" value="0" min="0">
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label d-block">สถานะและตัวเลือก</label>
                        <div class="form-check form-check-inline mt-2">
                          <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1">
                          <label class="form-check-label" for="is_featured">แสดงในส่วน Featured</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="status" id="status_show" value="1" checked>
                          <label class="form-check-label" for="status_show">แสดง</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="status" id="status_hide" value="0">
                          <label class="form-check-label" for="status_hide">ซ่อน</label>
                        </div>
                      </div>
                      <div class="col-12 mb-3">
                        <label class="form-label">คำอธิบายพิเศษ</label>
                        <textarea class="form-control" name="custom_description" rows="3" placeholder="ถ้าว่างไว้จะใช้คำอธิบายจากคอร์ส"></textarea>
                      </div>
                      <div class="col-12 mb-3">
                        <label class="form-label">คุณสมบัติพิเศษเพิ่มเติม (แยกแต่ละบรรทัด)</label>
                        <textarea class="form-control" name="additional_features" rows="3" placeholder="เช่น Deep Cleansing&#10;Gentle Exfoliation&#10;Face Massage"></textarea>
                      </div>
                      <div class="col-12 mb-3">
                        <label class="form-label">รูปภาพ (ถ้าต้องการใช้รูปใหม่)</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <small class="text-muted">ถ้าไม่อัปโหลดจะใช้รูปจากคอร์ส</small>
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

            <!-- Edit Service Modal จะเพิ่มเมื่อมีการคลิกแก้ไข -->
            <div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <form class="modal-content" id="editServiceForm" action="sql/frontend-service-update.php" method="post" enctype="multipart/form-data">
                  <div class="modal-header">
                    <h5 class="modal-title">แก้ไขบริการ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <!-- ฟอร์มแก้ไขจะถูกโหลดผ่าน AJAX -->
                    <div id="editServiceFormContent" class="text-center">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
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

            <!-- Footer -->
            <?php include 'footer.php'; ?>
            <!-- / Footer -->
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout container -->
      </div>
    </div>
    <!-- / Layout wrapper -->

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

    <script>
      $(document).ready(function() {
        // Initialize DataTable
        $('#servicesTable').DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
          },
          "pageLength": 10,
          "ordering": true
        });

        // Course selection changes default price
        $('[name="course_id"]').change(function() {
          var selectedOption = $(this).find('option:selected');
          var price = selectedOption.data('price');
          if (price) {
            $('[name="custom_price"]').attr('placeholder', 'ราคาจากคอร์ส: ' + price.toFixed(2) + ' บาท');
          }
        });

        // Edit Button Click
        $('.edit-btn').click(function() {
          var id = $(this).data('id');
          
          // โหลดข้อมูลบริการผ่าน AJAX
          $.ajax({
            url: 'sql/get-frontend-service.php',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                // เติมข้อมูลลงในฟอร์มแก้ไข
                $('#editServiceFormContent').html(response.html);
                $('#editServiceModal').modal('show');
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'ผิดพลาด',
                  text: response.message || 'ไม่สามารถโหลดข้อมูลได้',
                  customClass: {
                    confirmButton: 'btn btn-danger'
                  },
                  buttonsStyling: false
                });
              }
            },
            error: function() {
              Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                customClass: {
                  confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
              });
            }
          });
        });

        // Delete Button Click
        $('.delete-btn').click(function() {
          var id = $(this).data('id');
          
          Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "คุณจะไม่สามารถเรียกคืนข้อมูลนี้ได้!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ใช่, ลบเลย',
            cancelButtonText: 'ยกเลิก',
            customClass: {
              confirmButton: 'btn btn-danger me-3',
              cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.href = 'sql/frontend-service-delete.php?id=' + id;
            }
          });
        });

        // Form Validation Messages
        <?php if(isset($_SESSION['msg_ok'])){ ?>
          Swal.fire({
            icon: 'success',
            title: 'สำเร็จ',
            text: '<?php echo $_SESSION['msg_ok']; ?>',
            customClass: {
              confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
          });
        <?php unset($_SESSION['msg_ok']); } ?>

        <?php if(isset($_SESSION['msg_error'])){ ?>
          Swal.fire({
            icon: 'error',
            title: 'ผิดพลาด',
            text: '<?php echo $_SESSION['msg_error']; ?>',
            customClass: {
              confirmButton: 'btn btn-danger'
            },
            buttonsStyling: false
          });
        <?php unset($_SESSION['msg_error']); } ?>
      });
    </script>
  </body>
</html>