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

    <title>จัดการ Hero Slides | dcareclinic.com</title>

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

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/sweetalert2/sweetalert2.css" />

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
    
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
      
      .hero-card {
        transition: all 0.3s ease;
        cursor: pointer;
      }
      
      .hero-card:hover {
        transform: translateY(-5px);
      }
      
      .hero-card .card-img-top {
        height: 180px;
        object-fit: cover;
      }
      
      .hero-card .badge-container {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
      }
      
      .preview-wrapper {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
      }
      
      .preview-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all 0.3s ease;
      }
      
      .preview-wrapper:hover .preview-overlay {
        opacity: 1;
      }
      
      .inactive-slide {
        position: relative;
      }
      
      .inactive-slide::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.6);
        border-radius: 10px;
      }
      
      .inactive-badge {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 5;
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
              <!-- Hero Slides List -->
              <div class="card">
                <div class="card-header border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h5 class="card-title mb-0 text-white">Hero Slides</h5>
                      <small class="text-white">จัดการสไลด์หลักบนหน้าแรกของเว็บไซต์</small>
                    </div>
                    <div class="d-flex gap-2">
                      <a href="../index.php" class="btn btn-light" target="_blank">
                        <i class="ri-eye-line me-1"></i> ดูหน้าเว็บไซต์
                      </a>
                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHeroSlideModal">
                        <i class="ri-add-line me-1"></i> เพิ่มสไลด์ใหม่
                      </button>
                    </div>
                  </div>
                </div>
                
                <div class="card-body">
                  <div class="row g-4">
                    <?php
                    // ดึงข้อมูล Hero Slides
                    $sql = "SELECT * FROM frontend_hero_slides ORDER BY display_order ASC";
                    $result = $conn->query($sql);
                    
                    if ($result && $result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                        // แปลง JSON กลับเป็น array
                        $stats = json_decode($row['stats_json'], true) ?: [];
                        $features = json_decode($row['features_json'], true) ?: [];
                        $buttons = json_decode($row['buttons_json'], true) ?: [];
                        
                        // รูปภาพพื้นหลัง
                        $bgImage = !empty($row['background_image']) ? "../" . $row['background_image'] : "../img/pr/pic5.1.png";
                        
                        // นับจำนวนองค์ประกอบ
                        $statCount = count($stats);
                        $featureCount = count($features);
                        $buttonCount = count($buttons);
                    ?>
                    <div class="col-md-6 col-lg-4">
                      <div class="card hero-card h-100 <?php echo $row['is_active'] ? '' : 'inactive-slide'; ?>" 
                           data-id="<?php echo $row['id']; ?>"
                           data-bs-toggle="modal" 
                           data-bs-target="#editHeroSlideModal">
                        <?php if ($row['is_active'] == 0) { ?>
                          <div class="inactive-badge">
                            <span class="badge bg-danger fs-6 px-3 py-2">ไม่แสดงผล</span>
                          </div>
                        <?php } ?>
                        <div class="badge-container">
                          <span class="badge bg-primary">ลำดับ <?php echo $row['display_order']; ?></span>
                        </div>
                        <div class="preview-wrapper">
                          <img src="<?php echo $bgImage; ?>" class="card-img-top" alt="<?php echo $row['title']; ?>">
                          <div class="preview-overlay">
                            <button class="btn btn-light btn-icon me-2 view-btn" data-id="<?php echo $row['id']; ?>" data-bs-toggle="tooltip" title="ดูตัวอย่าง">
                              <i class="ri-eye-line"></i>
                            </button>
                            <button class="btn btn-warning btn-icon me-2 edit-btn" data-id="<?php echo $row['id']; ?>" data-bs-toggle="tooltip" title="แก้ไข">
                              <i class="ri-edit-line"></i>
                            </button>
                            <button class="btn btn-danger btn-icon delete-btn" data-id="<?php echo $row['id']; ?>" data-bs-toggle="tooltip" title="ลบ">
                              <i class="ri-delete-bin-line"></i>
                            </button>
                          </div>
                        </div>
                        <div class="card-body">
                          <h6 class="card-title">
                            <?php echo $row['title']; ?>
                            <?php if (!empty($row['title_highlight'])) echo ' <span class="text-primary">' . $row['title_highlight'] . '</span>'; ?>
                          </h6>
                          <?php if (!empty($row['subtitle'])) { ?>
                            <p class="card-text text-muted mb-2"><?php echo $row['subtitle']; ?></p>
                          <?php } ?>
                          <?php if (!empty($row['description'])) { ?>
                            <p class="card-text small mb-3"><?php echo substr(strip_tags($row['description']), 0, 80); ?><?php if (strlen($row['description']) > 80) echo '...'; ?></p>
                          <?php } ?>
                          <div class="d-flex flex-wrap gap-2 mt-3">
                            <?php if ($statCount > 0) { ?>
                              <span class="badge bg-info">
                                <i class="ri-bar-chart-line me-1"></i> <?php echo $statCount; ?> สถิติ
                              </span>
                            <?php } ?>
                            <?php if ($featureCount > 0) { ?>
                              <span class="badge bg-success">
                                <i class="ri-list-check me-1"></i> <?php echo $featureCount; ?> คุณสมบัติ
                              </span>
                            <?php } ?>
                            <?php if ($buttonCount > 0) { ?>
                              <span class="badge bg-warning">
                                <i class="ri-cursor-line me-1"></i> <?php echo $buttonCount; ?> ปุ่ม
                              </span>
                            <?php } ?>
                          </div>
                        </div>
                        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                          <div class="form-check form-switch">
                            <input class="form-check-input toggle-status" type="checkbox" id="status_<?php echo $row['id']; ?>" 
                                  data-id="<?php echo $row['id']; ?>" <?php echo $row['is_active'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="status_<?php echo $row['id']; ?>">
                              <?php echo $row['is_active'] ? 'แสดงผล' : 'ไม่แสดงผล'; ?>
                            </label>
                          </div>
                          <small class="text-muted">แก้ไขล่าสุด: <?php echo date('d/m/Y', strtotime($row['updated_at'])); ?></small>
                        </div>
                      </div>
                    </div>
                    <?php
                      }
                    } else {
                      // ถ้าไม่มีข้อมูล Hero Slides
                    ?>
                    <div class="col-12 text-center p-5">
                      <img src="../assets/img/illustrations/empty-state.png" alt="ไม่มีข้อมูล" class="img-fluid mb-3" style="max-width: 200px;">
                      <h5>ยังไม่มีข้อมูล Hero Slides</h5>
                      <p class="text-muted">เริ่มสร้าง Hero Slides แรกของคุณโดยคลิกที่ปุ่ม "เพิ่มสไลด์ใหม่"</p>
                      <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addHeroSlideModal">
                        <i class="ri-add-line me-1"></i> เพิ่มสไลด์ใหม่
                      </button>
                    </div>
                    <?php
                    }
                    ?>
                  </div>
                </div>
              </div>
            </div>
            <!-- / Content -->

            <!-- Modal เพิ่ม Hero Slide ใหม่ -->
            <div class="modal fade" id="addHeroSlideModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <form class="modal-content" id="addHeroSlideForm" enctype="multipart/form-data">
                  <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="ri-add-line me-2"></i>เพิ่ม Hero Slide ใหม่</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="alert alert-info">
                      <div class="d-flex gap-2">
                        <i class="ri-information-line fs-5"></i>
                        <div>
                          <strong>คำแนะนำ:</strong> กรอกข้อมูลให้ครบถ้วนเพื่อสร้าง Hero Slide ที่สมบูรณ์ ไม่จำเป็นต้องกรอกทุกช่อง เลือกเฉพาะองค์ประกอบที่ต้องการ
                        </div>
                      </div>
                    </div>

                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label">หัวข้อย่อย (Subtitle)</label>
                        <input type="text" class="form-control" name="subtitle" placeholder="เช่น Welcome to D Care Clinic">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label required">หัวข้อหลัก (Title)</label>
                        <input type="text" class="form-control" name="title" placeholder="เช่น ค้นพบความงาม" required>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">ส่วนที่เน้นในหัวข้อหลัก (Highlight)</label>
                        <input type="text" class="form-control" name="title_highlight" placeholder="เช่น ที่เป็นตัวคุณ">
                        <small class="text-muted">จะแสดงเป็นสีไล่ระดับ (Gradient)</small>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">ลำดับการแสดงผล</label>
                        <input type="number" class="form-control" name="display_order" value="0" min="0">
                      </div>
                      <div class="col-12">
                        <label class="form-label">คำอธิบาย</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="รายละเอียดเพิ่มเติม"></textarea>
                        <small class="text-muted">สามารถใช้ HTML tag &lt;br&gt; เพื่อขึ้นบรรทัดใหม่ได้</small>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">รูปภาพพื้นหลัง</label>
                        <input type="file" class="form-control" name="background_image" accept="image/*">
                        <small class="text-muted">ขนาดแนะนำ 1920x1080 พิกเซล</small>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">รูปภาพด้านขวา (Hero)</label>
                        <input type="file" class="form-control" name="hero_image" accept="image/*">
                        <small class="text-muted">ขนาดแนะนำ 600x800 พิกเซล</small>
                      </div>
                      <div class="col-12">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" name="is_active" id="add_is_active" value="1" checked>
                          <label class="form-check-label" for="add_is_active">เปิดใช้งาน (แสดงบนหน้าเว็บไซต์)</label>
                        </div>
                      </div>
                      
                      <!-- ส่วนจัดการสถิติ (Stats) -->
                      <div class="col-12 mt-4">
                        <h6>สถิติ (Stats)</h6>
                        <div id="add-stats-container" class="mb-2">
                          <!-- จะเพิ่มด้วย JavaScript -->
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-stat-btn">
                          <i class="ri-add-line"></i> เพิ่มสถิติ
                        </button>
                      </div>
                      
                      <!-- ส่วนจัดการคุณสมบัติ (Features) -->
                      <div class="col-12 mt-4">
                        <h6>คุณสมบัติ (Features)</h6>
                        <div id="add-features-container" class="mb-2">
                          <!-- จะเพิ่มด้วย JavaScript -->
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-feature-btn">
                          <i class="ri-add-line"></i> เพิ่มคุณสมบัติ
                        </button>
                      </div>
                      
                      <!-- ส่วนจัดการปุ่ม (Buttons) -->
                      <div class="col-12 mt-4">
                        <h6>ปุ่ม (Buttons)</h6>
                        <div id="add-buttons-container" class="mb-2">
                          <!-- จะเพิ่มด้วย JavaScript -->
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-button-btn">
                          <i class="ri-add-line"></i> เพิ่มปุ่ม
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                      <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                      บันทึก
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Modal แก้ไข Hero Slide -->
            <div class="modal fade" id="editHeroSlideModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <form class="modal-content" id="editHeroSlideForm" enctype="multipart/form-data">
                  <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="ri-edit-line me-2"></i>แก้ไข Hero Slide</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div id="editHeroSlideContent" class="text-center">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning">
                      <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                      บันทึกการแก้ไข
                    </button>
                  </div>
                </form>
              </div>
            </div>
            
            <!-- Modal Preview Hero Slide -->
            <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">ตัวอย่าง Hero Slide</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body p-0">
                    <div class="preview-iframe-container">
                      <iframe id="preview-iframe" src="" width="100%" height="600" frameborder="0"></iframe>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                  </div>
                </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
      $(document).ready(function() {
        // Tooltip
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // ป้องกันการกดทับซ้อนบน Card
        $('.hero-card').on('click', function(e) {
          e.stopPropagation();
          const id = $(this).data('id');
          loadHeroSlide(id);
        });
        
        // ปุ่มแก้ไข
        $('.edit-btn').on('click', function(e) {
          e.stopPropagation();
          const id = $(this).data('id');
          loadHeroSlide(id);
        });
        
        // ปุ่มดูตัวอย่าง
        $('.view-btn').on('click', function(e) {
          e.stopPropagation();
          const id = $(this).data('id');
          
          // โหลดตัวอย่างใน iframe
          $('#preview-iframe').attr('src', '../index.php?preview_hero=' + id);
          $('#previewModal').modal('show');
        });
        
        // ปุ่มลบ
        $('.delete-btn').on('click', function(e) {
          e.stopPropagation();
          const id = $(this).data('id');
          
          Swal.fire({
            title: 'คุณแน่ใจหรือไม่?',
            text: "การลบ Hero Slide ไม่สามารถเรียกคืนได้!",
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
              deleteHeroSlide(id);
            }
          });
        });
        
        // Toggle Status Switch
        $('.toggle-status').on('change', function(e) {
          e.stopPropagation();
          const id = $(this).data('id');
          const status = $(this).is(':checked') ? 1 : 0;
          
          toggleStatus(id, status);
        });
        
        // ปุ่มเพิ่มสถิติ
        $('#add-stat-btn').on('click', function() {
          addStatRow('add');
        });
        
        // ปุ่มเพิ่มคุณสมบัติ
        $('#add-feature-btn').on('click', function() {
          addFeatureRow('add');
        });
        
        // ปุ่มเพิ่มปุ่ม
        $('#add-button-btn').on('click', function() {
          addButtonRow('add');
        });
        
        // ฟอร์มเพิ่ม Hero Slide
        $('#addHeroSlideForm').on('submit', function(e) {
          e.preventDefault();
          
          // แสดงไอคอนโหลด
          const submitBtn = $(this).find('button[type="submit"]');
          const spinner = submitBtn.find('.spinner-border');
          submitBtn.prop('disabled', true);
          spinner.removeClass('d-none');
          
          // สร้าง FormData
          const formData = new FormData(this);
          
          // ส่งคำขอ AJAX
          $.ajax({
            url: 'api/frontend-hero-api.php?action=add',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                Swal.fire({
                  icon: 'success',
                  title: 'สำเร็จ!',
                  text: response.message,
                  customClass: {
                    confirmButton: 'btn btn-success'
                  },
                  buttonsStyling: false
                }).then(() => {
                  location.reload();
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'เกิดข้อผิดพลาด',
                  text: response.message,
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
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                customClass: {
                  confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
              });
            },
            complete: function() {
              submitBtn.prop('disabled', false);
              spinner.addClass('d-none');
            }
          });
        });
        
        // ฟอร์มแก้ไข Hero Slide
        $('#editHeroSlideForm').on('submit', function(e) {
          e.preventDefault();
          
          // แสดงไอคอนโหลด
          const submitBtn = $(this).find('button[type="submit"]');
          const spinner = submitBtn.find('.spinner-border');
          submitBtn.prop('disabled', true);
          spinner.removeClass('d-none');
          
          // สร้าง FormData
          const formData = new FormData(this);
          
          // ส่งคำขอ AJAX
          $.ajax({
            url: 'api/frontend-hero-api.php?action=update',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                Swal.fire({
                  icon: 'success',
                  title: 'สำเร็จ!',
                  text: response.message,
                  customClass: {
                    confirmButton: 'btn btn-success'
                  },
                  buttonsStyling: false
                }).then(() => {
                  location.reload();
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'เกิดข้อผิดพลาด',
                  text: response.message,
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
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                customClass: {
                  confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
              });
            },
            complete: function() {
              submitBtn.prop('disabled', false);
              spinner.addClass('d-none');
            }
          });
        });
        
        // เมื่อปิด Modal เพิ่ม Hero Slide
        $('#addHeroSlideModal').on('hidden.bs.modal', function() {
          $('#addHeroSlideForm')[0].reset();
          $('#add-stats-container, #add-features-container, #add-buttons-container').empty();
        });
        
        // ฟังก์ชันโหลดข้อมูล Hero Slide สำหรับการแก้ไข
        function loadHeroSlide(id) {
          $('#editHeroSlideContent').html('<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>');
          $('#editHeroSlideModal').modal('show');
          
          $.ajax({
            url: 'api/frontend-hero-api.php?action=get',
            type: 'GET',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                $('#editHeroSlideContent').html(response.html);
              } else {
                $('#editHeroSlideContent').html('<div class="alert alert-danger">' + response.message + '</div>');
              }
            },
            error: function() {
              $('#editHeroSlideContent').html('<div class="alert alert-danger">ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้</div>');
            }
          });
        }
        
        // ฟังก์ชันลบ Hero Slide
        function deleteHeroSlide(id) {
          $.ajax({
            url: 'api/frontend-hero-api.php?action=delete',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                Swal.fire({
                  icon: 'success',
                  title: 'สำเร็จ!',
                  text: response.message,
                  customClass: {
                    confirmButton: 'btn btn-success'
                  },
                  buttonsStyling: false
                }).then(() => {
                  location.reload();
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'เกิดข้อผิดพลาด',
                  text: response.message,
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
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                customClass: {
                  confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
              });
            }
          });
        }
        
        // ฟังก์ชันเปลี่ยนสถานะ Hero Slide
        function toggleStatus(id, status) {
          $.ajax({
            url: 'api/frontend-hero-api.php?action=toggle_status',
            type: 'POST',
            data: {
              id: id,
              status: status
            },
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                Swal.fire({
                  icon: 'success',
                  title: 'สำเร็จ!',
                  text: response.message,
                  customClass: {
                    confirmButton: 'btn btn-success'
                  },
                  buttonsStyling: false,
                  timer: 1500
                }).then(() => {
                  location.reload();
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'เกิดข้อผิดพลาด',
                  text: response.message,
                  customClass: {
                    confirmButton: 'btn btn-danger'
                  },
                  buttonsStyling: false
                }).then(() => {
                  location.reload();
                });
              }
            },
            error: function() {
              Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                customClass: {
                  confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
              }).then(() => {
                location.reload();
              });
            }
          });
        }
        
        // ฟังก์ชันเพิ่มแถวสถิติ
        function addStatRow(prefix) {
          const container = document.getElementById(prefix + '-stats-container');
          const newRow = document.createElement('div');
          newRow.className = 'row mb-2 stat-row';
          newRow.innerHTML = `
            <div class="col-md-3">
              <input type="text" class="form-control" name="stats[label][]" placeholder="ชื่อสถิติ">
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control" name="stats[value][]" placeholder="ค่า">
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control" name="stats[suffix][]" placeholder="หน่วย/คำต่อท้าย">
            </div>
            <div class="col-md-3">
              <button type="button" class="btn btn-outline-danger btn-sm remove-row">ลบ</button>
            </div>
          `;
          container.appendChild(newRow);
          
          // เพิ่ม Event Listener สำหรับปุ่มลบ
          newRow.querySelector('.remove-row').addEventListener('click', function() {
            container.removeChild(newRow);
          });
        }
        
        // ฟังก์ชันเพิ่มแถวคุณสมบัติ
        function addFeatureRow(prefix) {
          const container = document.getElementById(prefix + '-features-container');
          const newRow = document.createElement('div');
          newRow.className = 'row mb-2 feature-row';
          newRow.innerHTML = `
            <div class="col-md-4">
              <input type="text" class="form-control" name="features[text][]" placeholder="ข้อความ">
            </div>
            <div class="col-md-4">
              <input type="text" class="form-control" name="features[icon][]" placeholder="ไอคอน">
            </div>
            <div class="col-md-4">
              <button type="button" class="btn btn-outline-danger btn-sm remove-row">ลบ</button>
            </div>
          `;
          container.appendChild(newRow);
          
          // เพิ่ม Event Listener สำหรับปุ่มลบ
          newRow.querySelector('.remove-row').addEventListener('click', function() {
            container.removeChild(newRow);
          });
        }
        
        // ฟังก์ชันเพิ่มแถวปุ่ม
        function addButtonRow(prefix) {
          const container = document.getElementById(prefix + '-buttons-container');
          const newRow = document.createElement('div');
          newRow.className = 'row mb-2 button-row';
          newRow.innerHTML = `
            <div class="col-md-3">
              <input type="text" class="form-control" name="buttons[text][]" placeholder="ข้อความ">
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control" name="buttons[icon][]" placeholder="ไอคอน">
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control" name="buttons[url][]" placeholder="URL">
            </div>
            <div class="col-md-2">
              <select class="form-select" name="buttons[type][]">
                <option value="primary">หลัก</option>
                <option value="secondary">รอง</option>
              </select>
            </div>
            <div class="col-md-1">
              <button type="button" class="btn btn-outline-danger btn-sm remove-row">ลบ</button>
            </div>
          `;
          container.appendChild(newRow);
          
          // เพิ่ม Event Listener สำหรับปุ่มลบ
          newRow.querySelector('.remove-row').addEventListener('click', function() {
            container.removeChild(newRow);
          });
        }
      });
    </script>
  </body>
</html>