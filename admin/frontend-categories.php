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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>จัดการคอร์สบนเว็บไซต์หลัก | dcareclinic.com</title>

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
              <!-- Categories List -->
              <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between">
                  <h5 class="card-title mb-0 text-white">หมวดหมู่คอร์สบนเว็บไซต์หลัก</h5>
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="ri-add-line me-1"></i> เพิ่มหมวดหมู่
                  </button>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="categoriesTable" class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th width="60" class="text-center">ลำดับ</th>
                          <th>ชื่อหมวดหมู่</th>
                          <th>ประเภทคอร์สอ้างอิง</th>
                          <th>Slug</th>
                          <th width="100" class="text-center">ลำดับแสดงผล</th>
                          <th width="100" class="text-center">สถานะ</th>
                          <th width="120" class="text-center">จัดการ</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        // ปรับปรุง SQL ให้ join กับตาราง course_type เพื่อแสดงชื่อประเภทคอร์ส
                        $sql = "SELECT fc.*, ct.course_type_name 
                                FROM frontend_categories fc
                                LEFT JOIN course_type ct ON fc.course_type_id = ct.course_type_id
                                ORDER BY fc.display_order ASC, fc.name ASC";
                        
                        $result = $conn->query($sql);
                        
                        // ตรวจสอบความผิดพลาดของ query
                        if ($result === false) {
                          // กรณี query ผิดพลาด
                          echo '<tr><td colspan="7" class="text-center text-danger">เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $conn->error . '</td></tr>';
                        } else {
                          // query สำเร็จ ตรวจสอบจำนวนแถว
                          if ($result->num_rows > 0) {
                            $i = 1;
                            while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr id="category-row-<?php echo $row['id']; ?>">
                          <td class="text-center"><?php echo $i++; ?></td>
                          <td><?php echo htmlspecialchars($row['name']); ?></td>
                          <td><?php echo $row['course_type_name'] ? htmlspecialchars($row['course_type_name']) : '<span class="text-muted">ไม่มี</span>'; ?></td>
                          <td><?php echo htmlspecialchars($row['slug']); ?></td>
                          <td class="text-center"><?php echo $row['display_order']; ?></td>
                          <td class="text-center">
                            <?php if ($row['status'] == 1) { ?>
                              <span class="badge bg-success">แสดง</span>
                            <?php } else { ?>
                              <span class="badge bg-secondary">ซ่อน</span>
                            <?php } ?>
                          </td>
                          <td class="text-center">
                            <button type="button" class="btn btn-sm btn-warning edit-btn" 
                                    data-id="<?php echo $row['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                    data-slug="<?php echo htmlspecialchars($row['slug']); ?>"
                                    data-course-type="<?php echo $row['course_type_id']; ?>"
                                    data-order="<?php echo $row['display_order']; ?>"
                                    data-status="<?php echo $row['status']; ?>">
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
                            // ไม่พบข้อมูล
                            echo '<tr><td colspan="7" class="text-center">ไม่พบข้อมูลหมวดหมู่</td></tr>';
                          }
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>

                </div>
              </div>
            </div>
            <!-- / Content -->

            <!-- Add Category Modal -->
            <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <form class="modal-content" id="addCategoryForm">
                  <div class="modal-header">
                    <h5 class="modal-title">เพิ่มหมวดหมู่คอร์สใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="row">
                      <div class="col-12 mb-3">
                        <label class="form-label">ชื่อหมวดหมู่</label>
                        <input type="text" class="form-control" name="name" required>
                      </div>
                      <div class="col-12 mb-3">
                        <label class="form-label">ประเภทคอร์สอ้างอิง (ถ้ามี)</label>
                        <select class="form-select" name="course_type_id">
                          <option value="">-- ไม่อ้างอิงประเภทคอร์ส --</option>
                          <?php
                          // ดึงข้อมูลประเภทคอร์ส
                          $sql_course_type = "SELECT course_type_id, course_type_name FROM course_type WHERE course_type_status = 1 ORDER BY course_type_name";
                          $result_course_type = $conn->query($sql_course_type);
                          
                          if ($result_course_type && $result_course_type->num_rows > 0) {
                            while ($row_course_type = $result_course_type->fetch_assoc()) {
                              echo '<option value="'.$row_course_type['course_type_id'].'">'.htmlspecialchars($row_course_type['course_type_name']).'</option>';
                            }
                          }
                          ?>
                        </select>
                        <small class="text-muted">หากเลือกประเภทคอร์ส หมวดหมู่นี้จะแสดงเฉพาะคอร์สในประเภทที่เลือกเท่านั้น</small>
                      </div>
                      <div class="col-12 mb-3">
                        <label class="form-label">Slug (สำหรับ URL)</label>
                        <input type="text" class="form-control" name="slug" required>
                        <small class="text-muted">ใช้ตัวอักษรภาษาอังกฤษพิมพ์เล็ก ตัวเลข และเครื่องหมาย - เท่านั้น</small>
                      </div>
                      <div class="col-12 mb-3">
                        <label class="form-label">ลำดับการแสดงผล</label>
                        <input type="number" class="form-control" name="display_order" value="0" min="0">
                      </div>
                      <div class="col-12">
                        <label class="form-label d-block">สถานะ</label>
                        <div class="form-check form-check-inline mt-2">
                          <input class="form-check-input" type="radio" name="status" id="status_show" value="1" checked>
                          <label class="form-check-label" for="status_show">แสดง</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="status" id="status_hide" value="0">
                          <label class="form-check-label" for="status_hide">ซ่อน</label>
                        </div>
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

            <!-- Edit Category Modal -->
            <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog">
                <form class="modal-content" id="editCategoryForm">
                  <div class="modal-header">
                    <h5 class="modal-title">แก้ไขหมวดหมู่คอร์ส</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row">
                      <div class="col-12 mb-3">
                        <label class="form-label">ชื่อหมวดหมู่</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                      </div>
                      <div class="col-12 mb-3">
                        <label class="form-label">ประเภทคอร์สอ้างอิง (ถ้ามี)</label>
                        <select class="form-select" name="course_type_id" id="edit_course_type_id">
                          <option value="">-- ไม่อ้างอิงประเภทคอร์ส --</option>
                          <?php
                          // รีเซ็ตตัวชี้ตำแหน่งของผลลัพธ์
                          if ($result_course_type) {
                            $result_course_type->data_seek(0);
                            while ($row_course_type = $result_course_type->fetch_assoc()) {
                              echo '<option value="'.$row_course_type['course_type_id'].'">'.htmlspecialchars($row_course_type['course_type_name']).'</option>';
                            }
                          }
                          ?>
                        </select>
                        <small class="text-muted">หากเลือกประเภทคอร์ส หมวดหมู่นี้จะแสดงเฉพาะคอร์สในประเภทที่เลือกเท่านั้น</small>
                      </div>
                      <div class="col-12 mb-3">
                        <label class="form-label">Slug (สำหรับ URL)</label>
                        <input type="text" class="form-control" name="slug" id="edit_slug" required>
                        <small class="text-muted">ใช้ตัวอักษรภาษาอังกฤษพิมพ์เล็ก ตัวเลข และเครื่องหมาย - เท่านั้น</small>
                      </div>
                      <div class="col-12 mb-3">
                        <label class="form-label">ลำดับการแสดงผล</label>
                        <input type="number" class="form-control" name="display_order" id="edit_display_order" min="0">
                      </div>
                      <div class="col-12">
                        <label class="form-label d-block">สถานะ</label>
                        <div class="form-check form-check-inline mt-2">
                          <input class="form-check-input" type="radio" name="status" id="edit_status_show" value="1">
                          <label class="form-check-label" for="edit_status_show">แสดง</label>
                        </div>
                        <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="status" id="edit_status_hide" value="0">
                          <label class="form-check-label" for="edit_status_hide">ซ่อน</label>
                        </div>
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
        // ตรวจสอบว่ามีตารางก่อนที่จะ initialize DataTables
        if ($("#categoriesTable").length > 0 && $("tr", "#categoriesTable tbody").length > 0) {
          try {
            const categoriesTable = $('#categoriesTable').DataTable({
              "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
              },
              "pageLength": 10,
              "ordering": true
            });
          } catch (e) {
            console.error("DataTables initialization error:", e);
          }
        }

        // Auto generate slug
        $('[name="name"]').on('input', function() {
          var slug = $(this).val()
            .toLowerCase()
            .replace(/\s+/g, '-') // Replace spaces with -
            .replace(/[^\w\-]+/g, '') // Remove all non-word chars
            .replace(/\-\-+/g, '-') // Replace multiple - with single -
            .replace(/^-+/, '') // Trim - from start of text
            .replace(/-+$/, ''); // Trim - from end of text
          $('[name="slug"]').val(slug);
        });

        // Edit Button Click
        $('.edit-btn').click(function() {
          var id = $(this).data('id');
          var name = $(this).data('name');
          var slug = $(this).data('slug');
          var courseType = $(this).data('course-type');
          var order = $(this).data('order');
          var status = $(this).data('status');

          $('#edit_id').val(id);
          $('#edit_name').val(name);
          $('#edit_slug').val(slug);
          $('#edit_course_type_id').val(courseType);
          $('#edit_display_order').val(order);
          
          if(status == 1) {
            $('#edit_status_show').prop('checked', true);
          } else {
            $('#edit_status_hide').prop('checked', true);
          }
          
          $('#editCategoryModal').modal('show');
        });

        // Add Category Form Submit
        $('#addCategoryForm').submit(function(e) {
          e.preventDefault();
          
          const formData = new FormData(this);
          
          // แสดง loading indicator
          const submitBtn = $(this).find('button[type="submit"]');
          const originalBtnText = submitBtn.html();
          submitBtn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> กำลังบันทึก...');
          submitBtn.prop('disabled', true);
          
          $.ajax({
            url: 'api/frontend-category-api.php?action=add',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                // แสดงข้อความสำเร็จ
                Swal.fire({
                  icon: 'success',
                  title: 'สำเร็จ',
                  text: response.message,
                  customClass: {
                    confirmButton: 'btn btn-primary'
                  },
                  buttonsStyling: false
                }).then((result) => {
                  // รีโหลดหน้าเพื่อแสดงข้อมูลล่าสุด
                  location.reload();
                });
              } else {
                // แสดงข้อความผิดพลาด
                Swal.fire({
                  icon: 'error',
                  title: 'ผิดพลาด',
                  text: response.message,
                  customClass: {
                    confirmButton: 'btn btn-danger'
                  },
                  buttonsStyling: false
                });
              }
            },
            error: function(xhr, status, error) {
              // แสดงข้อความผิดพลาดจากเซิร์ฟเวอร์
              Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด',
                text: 'เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์',
                customClass: {
                  confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
              });
            },
            complete: function() {
              // คืนค่าปุ่มกลับเป็นปกติ
              submitBtn.html(originalBtnText);
              submitBtn.prop('disabled', false);
            }
          });
        });

        // Edit Category Form Submit
        $('#editCategoryForm').submit(function(e) {
          e.preventDefault();
          
          const formData = new FormData(this);
          
          // แสดง loading indicator
          const submitBtn = $(this).find('button[type="submit"]');
          const originalBtnText = submitBtn.html();
          submitBtn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> กำลังบันทึก...');
          submitBtn.prop('disabled', true);
          
          $.ajax({
            url: 'api/frontend-category-api.php?action=update',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                // แสดงข้อความสำเร็จ
                Swal.fire({
                  icon: 'success',
                  title: 'สำเร็จ',
                  text: response.message,
                  customClass: {
                    confirmButton: 'btn btn-primary'
                  },
                  buttonsStyling: false
                }).then((result) => {
                  // รีโหลดหน้าเพื่อแสดงข้อมูลล่าสุด
                  location.reload();
                });
              } else {
                // แสดงข้อความผิดพลาด
                Swal.fire({
                  icon: 'error',
                  title: 'ผิดพลาด',
                  text: response.message,
                  customClass: {
                    confirmButton: 'btn btn-danger'
                  },
                  buttonsStyling: false
                });
              }
            },
            error: function(xhr, status, error) {
              // แสดงข้อความผิดพลาดจากเซิร์ฟเวอร์
              Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด',
                text: 'เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์',
                customClass: {
                  confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
              });
            },
            complete: function() {
              // คืนค่าปุ่มกลับเป็นปกติ
              submitBtn.html(originalBtnText);
              submitBtn.prop('disabled', false);
            }
          });
        });

        // Delete Button Click
        $('.delete-btn').click(function() {
          var id = $(this).data('id');
          
          Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "การลบหมวดหมู่นี้จะมีผลกับคอร์สที่อยู่ในหมวดหมู่นี้",
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
              // ส่งคำขอลบผ่าน AJAX
              $.ajax({
                url: 'api/frontend-category-api.php?action=delete',
                type: 'POST',
                data: { id: id },
                dataType: 'json',
                success: function(response) {
                  if (response.success) {
                    // ลบแถวออกจากตาราง
                    $('#category-row-' + id).remove();
                    
                    // แสดงข้อความสำเร็จ
                    Swal.fire({
                      icon: 'success',
                      title: 'สำเร็จ',
                      text: response.message,
                      customClass: {
                        confirmButton: 'btn btn-primary'
                      },
                      buttonsStyling: false,
                      timer: 1500
                    });
                  } else {
                    // แสดงข้อความผิดพลาด
                    Swal.fire({
                      icon: 'error',
                      title: 'ผิดพลาด',
                      text: response.message,
                      customClass: {
                        confirmButton: 'btn btn-danger'
                      },
                      buttonsStyling: false
                    });
                  }
                },
                error: function(xhr, status, error) {
                  // แสดงข้อความผิดพลาดจากเซิร์ฟเวอร์
                  Swal.fire({
                    icon: 'error',
                    title: 'ผิดพลาด',
                    text: 'เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์',
                    customClass: {
                      confirmButton: 'btn btn-danger'
                    },
                    buttonsStyling: false
                  });
                }
              });
            }
          });
        });
      });
    </script>
  </body>
</html>