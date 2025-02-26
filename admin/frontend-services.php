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
              <!-- แก้ไขส่วน Card Header -->
              <div class="card">
                <div class="card-header border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h5 class="card-title mb-0 text-white">คอร์สที่แสดงบนเว็บไซต์หลัก</h5>
                      <small class=" text-white">จัดการคอร์สที่แสดงให้ลูกค้าเห็นบนหน้าเว็บไซต์หลัก</small>
                    </div>
                    <div class="d-flex gap-2 ">
                      <a href="frontend-categories.php" class="btn btn-warning">
                        <i class="ri-folder-line me-1"></i> จัดการหมวดหมู่
                      </a>
                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                        <i class="ri-add-line me-1"></i> เพิ่มคอร์ส
                      </button>
                    </div>
                  </div>
                </div>
                
                <!-- แก้ไขส่วนช่องค้นหาและตัวกรอง -->
                <br>
                <div class="card-body border-bottom">
                  <div class="row g-3">
                    
                    <div class="col-md-4">
                      
                      <label class="form-label">ค้นหาคอร์ส</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="ri-search-line"></i></span>
                        <input type="text" class="form-control" id="searchService" placeholder="ชื่อคอร์ส...">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label">กรองตามหมวดหมู่</label>
                      <select class="form-select" id="filterCategory">
                        <option value="">ทุกหมวดหมู่</option>
                        <?php
                        $sql_filter_cat = "SELECT id, name FROM frontend_categories WHERE status = 1 ORDER BY display_order, name";
                        $result_filter_cat = $conn->query($sql_filter_cat);
                        
                        if ($result_filter_cat->num_rows > 0) {
                          while ($row_filter_cat = $result_filter_cat->fetch_assoc()) {
                            echo '<option value="'.$row_filter_cat['id'].'">'.$row_filter_cat['name'].'</option>';
                          }
                        }
                        ?>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label">สถานะการแสดง</label>
                      <select class="form-select" id="filterStatus">
                        <option value="">ทั้งหมด</option>
                        <option value="1">แสดง</option>
                        <option value="0">ซ่อน</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">คอร์สแนะนำ</label>
                      <select class="form-select" id="filterFeatured">
                        <option value="">ทั้งหมด</option>
                        <option value="1">คอร์สแนะนำ</option>
                        <option value="0">ทั่วไป</option>
                      </select>
                    </div>
                  </div>
                </div>
                
                <!-- แก้ไขส่วน Dashboard สรุปข้อมูล -->
                <div class="card-body border-bottom pb-0">
                  <div class="row g-3">
                    <div class="col-md-3">
                      <div class="card bg-primary bg-opacity-10 border-0">
                        <div class="card-body">
                          <div class="d-flex align-items-center">
                            <div class="avatar">
                              <div class="avatar-initial bg-primary rounded">
                                <i class="ri-service-line fs-4"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                              <h5 class="mb-0 text-white" id="totalServices">0</h5>
                              <small class="text-white">คอร์สทั้งหมด</small>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card bg-success bg-opacity-10 border-0">
                        <div class="card-body">
                          <div class="d-flex align-items-center">
                            <div class="avatar">
                              <div class="avatar-initial bg-success rounded">
                                <i class="ri-eye-line fs-4"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                              <h5 class="mb-0" id="activeServices">0</h5>
                              <small>คอร์สที่แสดงอยู่</small>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card bg-warning bg-opacity-10 border-0">
                        <div class="card-body">
                          <div class="d-flex align-items-center">
                            <div class="avatar">
                              <div class="avatar-initial bg-warning rounded">
                                <i class="ri-star-line fs-4"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                              <h5 class="mb-0" id="featuredServices">0</h5>
                              <small>คอร์สแนะนำ</small>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card bg-info bg-opacity-10 border-0">
                        <div class="card-body">
                          <div class="d-flex align-items-center">
                            <div class="avatar">
                              <div class="avatar-initial bg-info rounded">
                                <i class="ri-folder-line fs-4"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                              <h5 class="mb-0" id="totalCategories">0</h5>
                              <small>หมวดหมู่</small>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              <div class="card-body">
                <div class="card-datatable table-responsive">
                <table id="servicesTable" class="table table-hover border-top">
                  <thead class="table-light">
                    <tr>
                      <th width="60" class="text-center">รูปภาพ</th>
                      <th>ชื่อคอร์ส</th>
                      <th>หมวดหมู่</th>
                      <th width="100" class="text-end">ราคา (บาท)</th>
                      <th width="150" class="text-center">สถานะ</th>
                      <th width="150" class="text-center">จัดการ</th>
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
                    
                    $totalCount = 0;
                    $activeCount = 0;
                    $featuredCount = 0;
                    
                    if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                        // คำนวณสถิติ
                        $totalCount++;
                        if ($row['status'] == 1) $activeCount++;
                        if ($row['is_featured'] == 1) $featuredCount++;
                        
                        // ใช้ราคาที่กำหนดในคอร์สหน้าเว็บหลัก หรือราคาจากคอร์ส
                        $price = $row['custom_price'] ?? $row['course_price'];
                        $originalPrice = $row['custom_original_price'] ?? null;
                        
                        // ใช้รูปภาพที่กำหนดสำหรับหน้าเว็บหลัก หรือรูปภาพจากคอร์ส
                        $imgPath = $row['image_path'] ?? $row['course_pic'] ?? 'course.png';
                    ?>
                    <tr data-id="<?php echo $row['id']; ?>" data-category="<?php echo $row['frontend_category_id']; ?>" data-status="<?php echo $row['status']; ?>" data-featured="<?php echo $row['is_featured']; ?>">
                      <td class="text-center">
                        <img src="../../img/course/<?php echo $imgPath; ?>" class="rounded img-thumbnail thumbnail-preview cursor-pointer view-image" alt="<?php echo $row['course_name']; ?>" data-bs-toggle="tooltip" title="คลิกเพื่อดูภาพขนาดใหญ่">
                      </td>
                      <td>
                        <h6 class="mb-0"><?php echo $row['course_name']; ?></h6>
                        <small class="text-muted d-block">
                          <?php if ($row['badge_text']) { ?>
                            <span class="badge bg-label-info"><?php echo $row['badge_text']; ?></span>
                          <?php } ?>
                          <span class="ms-1">ลำดับแสดง: <?php echo $row['display_order']; ?></span>
                        </small>
                      </td>
                      <td><?php echo $row['category_name']; ?></td>
                      <td class="text-end">
                        <?php if ($originalPrice) { ?>
                          <span class="text-decoration-line-through text-muted"><?php echo number_format($originalPrice, 0); ?></span><br>
                        <?php } ?>
                        <span class="fw-bold"><?php echo number_format($price, 0); ?></span>
                      </td>
                      <td class="text-center">
                        <div class="d-flex flex-column gap-1 align-items-center">
                          <?php if ($row['status'] == 1) { ?>
                            <span class="badge bg-success">แสดงอยู่</span>
                          <?php } else { ?>
                            <span class="badge bg-secondary">ซ่อนอยู่</span>
                          <?php } ?>
                          
                          <?php if ($row['is_featured'] == 1) { ?>
                            <span class="badge bg-warning">คอร์สแนะนำ</span>
                          <?php } ?>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex justify-content-center gap-2">
                          <button type="button" class="btn btn-sm btn-primary quick-view-btn" data-id="<?php echo $row['id']; ?>" data-bs-toggle="tooltip" title="ดูรายละเอียด">
                            <i class="ri-eye-line"></i>
                          </button>
                          <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $row['id']; ?>" data-bs-toggle="tooltip" title="แก้ไขคอร์ส">
                            <i class="ri-edit-line"></i>
                          </button>
                          <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                              <i class="ri-settings-3-line"></i>
                            </button>
                            <ul class="dropdown-menu">
                              <li>
                                <a class="dropdown-item toggle-status-btn" href="javascript:void(0);" data-id="<?php echo $row['id']; ?>" data-current="<?php echo $row['status']; ?>">
                                  <?php echo ($row['status'] == 1) ? '<i class="ri-eye-off-line me-2"></i>ซ่อนคอร์ส' : '<i class="ri-eye-line me-2"></i>แสดงคอร์ส'; ?>
                                </a>
                              </li>
                              <li>
                                <a class="dropdown-item toggle-featured-btn" href="javascript:void(0);" data-id="<?php echo $row['id']; ?>" data-current="<?php echo $row['is_featured']; ?>">
                                  <?php echo ($row['is_featured'] == 1) ? '<i class="ri-star-line me-2"></i>ยกเลิกการแนะนำ' : '<i class="ri-star-fill me-2"></i>ตั้งเป็นคอร์สแนะนำ'; ?>
                                </a>
                              </li>
                              <li><hr class="dropdown-divider"></li>
                              <li>
                                <a class="dropdown-item text-danger delete-btn" href="javascript:void(0);" data-id="<?php echo $row['id']; ?>">
                                  <i class="ri-delete-bin-line me-2"></i>ลบคอร์ส
                                </a>
                              </li>
                            </ul>
                          </div>
                        </div>
                      </td>
                    </tr>
                    <?php
                      }
                    } else {
                    ?>
                    <tr>
                      <td colspan="6" class="text-center py-3">
                        <img src="../assets/img/illustrations/page-misc-empty-state.png" alt="No data" class="mb-2" width="80">
                        <p class="mb-0">ไม่พบข้อมูลคอร์ส</p>
                        <small class="text-muted">เริ่มสร้างคอร์สใหม่โดยคลิกที่ปุ่ม "เพิ่มคอร์ส"</small>
                      </td>
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


<!-- Modal เพื่อแสดงรูปภาพขนาดใหญ่ -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header border-bottom">
        <h5 class="modal-title" id="imagePreviewTitle">รูปภาพคอร์ส</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="largeImage" src="" alt="รูปภาพขนาดใหญ่" class="img-fluid">
      </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal สำหรับดูข้อมูลรายละเอียดคอร์สแบบเร็ว -->
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-bottom">
        <h5 class="modal-title">รายละเอียดคอร์ส</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="quickViewContent">
        <!-- จะแสดงข้อมูลที่โหลดผ่าน AJAX -->
        <div class="text-center">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
      <div class="modal-footer border-top">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
        <button type="button" class="btn btn-primary" id="quickViewEditBtn">แก้ไขคอร์ส</button>
      </div>
    </div>
  </div>
</div>
 <!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" id="addServiceForm">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="ri-add-line me-2"></i>เพิ่มคอร์สใหม่</h5>
        <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <div class="d-flex gap-2">
            <i class="ri-information-line fs-5"></i>
            <div>
              <strong>คำแนะนำ:</strong> เลือกคอร์สจากระบบและหมวดหมู่ที่ต้องการแสดงบนเว็บไซต์ คุณสามารถกำหนดราคาและรายละเอียดพิเศษได้เพื่อการแสดงผลที่แตกต่างจากข้อมูลคอร์สเดิม
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label required">เลือกคอร์ส</label>
            <select class="form-select" name="course_id" id="add_course_id" required>
              <option value="">-- กรุณาเลือกคอร์ส --</option>
              <?php
              $sql_course = "SELECT course_id, course_name, course_price FROM course WHERE course_status = 1 ORDER BY course_name";
              $result_course = $conn->query($sql_course);
              
              if ($result_course->num_rows > 0) {
                while ($row_course = $result_course->fetch_assoc()) {
                  echo '<option value="'.$row_course['course_id'].'" data-price="'.$row_course['course_price'].'">'.$row_course['course_name'].' - '.number_format($row_course['course_price'], 0).' บาท</option>';
                }
              }
              ?>
            </select>
            <div id="course_preview" class="mt-2 d-none">
              <div class="border rounded p-2 bg-light">
                <div class="d-flex align-items-center">
                  <div id="course_image" class="me-2" style="width:50px; height:50px; background-size:cover; background-position:center; border-radius:4px;"></div>
                  <div>
                    <div id="course_name" class="fw-bold"></div>
                    <div id="course_price" class="small text-primary"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label required">หมวดหมู่</label>
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
          
          <div class="col-12">
            <hr class="my-3">
            <h6 class="text-uppercase text-primary small mb-3">รายละเอียดการแสดงผล</h6>
          </div>
          
          <div class="col-md-6 mb-3">
            <label class="form-label">ราคาพิเศษ (บาท)</label>
            <div class="input-group">
              <input type="number" class="form-control" name="custom_price" id="add_custom_price" step="1" min="0" placeholder="ราคาพิเศษ">
              <span class="input-group-text">บาท</span>
            </div>
            <small class="text-muted" id="original_price_note">ถ้าว่างไว้จะใช้ราคาจากคอร์ส</small>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">ราคาปกติสำหรับแสดงส่วนลด (บาท)</label>
            <div class="input-group">
              <input type="number" class="form-control" name="custom_original_price" step="1" min="0" placeholder="ราคาปกติ">
              <span class="input-group-text">บาท</span>
            </div>
            <small class="text-muted">ใช้เพื่อแสดงราคาขีดฆ่า</small>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">ข้อความป้ายกำกับ</label>
            <input type="text" class="form-control" name="badge_text" placeholder="เช่น ขายดี, แนะนำ" maxlength="20">
            <small class="text-muted">ป้ายที่จะแสดงมุมบนขวาของคอร์ส</small>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">ระยะเวลาต่อครั้ง (นาที)</label>
            <div class="input-group">
              <input type="number" class="form-control" name="session_duration" placeholder="60" min="0" step="5">
              <span class="input-group-text">นาที</span>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">ลำดับการแสดงผล</label>
            <input type="number" class="form-control" name="display_order" value="0" min="0">
            <small class="text-muted">เลขน้อยจะแสดงก่อน</small>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label d-block">สถานะและตัวเลือก</label>
            <div class="form-check form-switch form-check-inline mt-2">
              <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1">
              <label class="form-check-label" for="is_featured">แสดงในส่วน Featured</label>
            </div>
            <div class="form-check form-switch form-check-inline">
              <input class="form-check-input" type="checkbox" name="status" id="status_active" value="1" checked>
              <label class="form-check-label" for="status_active">แสดงบนเว็บไซต์</label>
            </div>
          </div>
          <div class="col-12 mb-3">
            <label class="form-label">คำอธิบายพิเศษ</label>
            <textarea class="form-control" name="custom_description" rows="3" placeholder="ถ้าว่างไว้จะใช้คำอธิบายจากคอร์ส"></textarea>
            <div id="course_description_preview" class="mt-1 small text-muted d-none">
              <div class="d-flex">
                <div class="me-1"><i class="ri-file-text-line"></i></div>
                <div>คำอธิบายจากคอร์ส: <span id="course_description_text"></span></div>
              </div>
            </div>
          </div>
          <div class="col-12 mb-3">
            <label class="form-label">คุณสมบัติพิเศษเพิ่มเติม (แยกแต่ละบรรทัด)</label>
            <textarea class="form-control" name="additional_features" rows="3" placeholder="เช่น:
Deep Cleansing ทำความสะอาดล้ำลึก
Gentle Exfoliation ผลัดเซลล์ผิวอ่อนโยน
Face Massage นวดหน้าด้วยเทคนิคเฉพาะ"></textarea>
            <small class="text-muted">จะแสดงเป็นรายการบนหน้าคอร์ส</small>
          </div>
          <div class="col-12 mb-3">
            <label class="form-label">รูปภาพ</label>
            <input type="file" class="form-control" name="image" id="add_service_image" accept="image/*">
            <div class="d-flex align-items-center mt-2">
              <div class="me-2"><i class="ri-information-line text-primary"></i></div>
              <small class="text-muted">ถ้าไม่อัปโหลดจะใช้รูปจากคอร์ส ขนาดที่แนะนำ 600x400 พิกเซล</small>
            </div>
            <div class="image-preview-container mt-2"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
        <button type="submit" class="btn btn-primary">
          <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
          บันทึกคอร์ส
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Service Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" id="editServiceForm">
      <div class="modal-header bg-warning">
        <h5 class="modal-title"><i class="ri-edit-line me-2"></i>แก้ไขคอร์ส</h5>
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
        <button type="submit" class="btn btn-warning">
          <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
          บันทึกการแก้ไข
        </button>
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
  // ตัวแปรเก็บสถิติ
  const stats = {
    total: <?php echo $totalCount ?? 0; ?>,
    active: <?php echo $activeCount ?? 0; ?>,
    featured: <?php echo $featuredCount ?? 0; ?>,
    categories: <?php echo $result_filter_cat->num_rows ?? 0; ?>
  };
  
  // แสดงสถิติใน Dashboard
  $('#totalServices').text(stats.total);
  $('#activeServices').text(stats.active);
  $('#featuredServices').text(stats.featured);
  $('#totalCategories').text(stats.categories);
  
  // Initialize DataTable with enhanced features
  const servicesTable = $('#servicesTable').DataTable({
    language: {
      url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
    },
    dom: '<"d-flex justify-content-between align-items-center header-actions mx-2 row mt-75"' +
         '<"col-sm-12 col-lg-4 d-flex justify-content-center justify-content-lg-start" l>' +
         '<"col-sm-12 col-lg-8 ps-xl-75 ps-0"<"dt-action-buttons d-flex align-items-center justify-content-center justify-content-lg-end flex-lg-nowrap flex-wrap"<"me-1"f>B>>' +
         '>t' +
         '<"d-flex justify-content-between mx-2 row mb-1"' +
         '<"col-sm-12 col-md-6"i>' +
         '<"col-sm-12 col-md-6"p>' +
         '>',
    buttons: [
      {
        extend: 'collection',
        className: 'btn btn-outline-secondary dropdown-toggle me-2',
        text: '<i class="ri-download-line me-1"></i>ส่งออก',
        buttons: [
          {
            extend: 'excel',
            text: '<i class="ri-file-excel-line me-1"></i>Excel',
            className: 'dropdown-item',
            exportOptions: { columns: [1, 2, 3, 4] }
          },
          {
            extend: 'pdf',
            text: '<i class="ri-file-pdf-line me-1"></i>PDF',
            className: 'dropdown-item',
            exportOptions: { columns: [1, 2, 3, 4] }
          },
          {
            extend: 'print',
            text: '<i class="ri-printer-line me-1"></i>พิมพ์',
            className: 'dropdown-item',
            exportOptions: { columns: [1, 2, 3, 4] }
          }
        ]
      }
    ],
    pageLength: 10,
    ordering: true,
    order: [[1, 'asc']], // เรียงตามชื่อคอร์สเริ่มต้น
    responsive: true,
    columnDefs: [
      { orderable: false, targets: [0, 5] } // ไม่ให้เรียงตามคอลัมน์รูปภาพและปุ่มจัดการ
    ]
  });
  
  // ทำงานกับฟิลเตอร์
  function applyFilters() {
    $.fn.dataTable.ext.search.push(
      function(settings, data, dataIndex) {
        const $row = $(servicesTable.row(dataIndex).node());
        const categoryId = $('#filterCategory').val();
        const status = $('#filterStatus').val();
        const featured = $('#filterFeatured').val();
        
        let showRow = true;
        
        // กรองตามหมวดหมู่
        if (categoryId && $row.data('category') != categoryId) {
          showRow = false;
        }
        
        // กรองตามสถานะการแสดง
        if (status !== '' && $row.data('status') != status) {
          showRow = false;
        }
        
        // กรองตามการเป็นคอร์สแนะนำ
        if (featured !== '' && $row.data('featured') != featured) {
          showRow = false;
        }
        
        return showRow;
      }
    );
    
    servicesTable.draw();
    
    // ล้างฟังก์ชันค้นหาที่เพิ่มไว้เพื่อไม่ให้สะสม
    $.fn.dataTable.ext.search.pop();
  }
  
  // เมื่อมีการเปลี่ยนแปลงตัวกรอง
  $('#filterCategory, #filterStatus, #filterFeatured').change(function() {
    applyFilters();
  });
  
  // ค้นหาทั่วไป
  $('#searchService').on('keyup', function() {
    servicesTable.search(this.value).draw();
  });
  
  // แสดงรูปภาพขนาดใหญ่เมื่อคลิกที่รูปภาพขนาดเล็ก
  $('.view-image').click(function() {
    const imgSrc = $(this).attr('src');
    const serviceName = $(this).attr('alt');
    
    $('#imagePreviewTitle').text('รูปภาพคอร์ส: ' + serviceName);
    $('#largeImage').attr('src', imgSrc);
    $('#imagePreviewModal').modal('show');
  });
  
  // แสดงหรือซ่อนคอร์สแบบรวดเร็ว
  $('.toggle-status-btn').click(function() {
    const id = $(this).data('id');
    const currentStatus = $(this).data('current');
    const newStatus = currentStatus == 1 ? 0 : 1;
    const statusLabel = newStatus == 1 ? 'แสดง' : 'ซ่อน';
    
    // แสดง SweetAlert2 เพื่อยืนยันการเปลี่ยนสถานะ
    Swal.fire({
      title: `${statusLabel}คอร์สนี้?`,
      text: currentStatus == 1 ? "คอร์สนี้จะไม่แสดงบนเว็บไซต์หลัก" : "คอร์สนี้จะแสดงบนเว็บไซต์หลัก",
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'ใช่, เปลี่ยนสถานะ',
      cancelButtonText: 'ยกเลิก',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-outline-secondary'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) {
        // ส่งคำขอผ่าน AJAX เพื่อเปลี่ยนสถานะ
        $.ajax({
          url: 'api/frontend-service-api.php?action=toggle_status',
          type: 'POST',
          data: { id: id, status: newStatus },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              // แสดงข้อความสำเร็จและรีโหลดหน้า
              Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
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
              // แสดงข้อความผิดพลาด
              Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด',
                text: response.message || 'ไม่สามารถเปลี่ยนสถานะได้',
                customClass: {
                  confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
              });
            }
          },
          error: function() {
            // แสดงข้อความเมื่อเกิดข้อผิดพลาดในการเชื่อมต่อ
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
      }
    });
  });
  
  // เปลี่ยนสถานะคอร์สแนะนำแบบรวดเร็ว
  $('.toggle-featured-btn').click(function() {
    const id = $(this).data('id');
    const currentFeatured = $(this).data('current');
    const newFeatured = currentFeatured == 1 ? 0 : 1;
    const featuredLabel = newFeatured == 1 ? 'ตั้งเป็นคอร์สแนะนำ' : 'ยกเลิกการแนะนำ';
    
    Swal.fire({
      title: `${featuredLabel}?`,
      text: currentFeatured == 1 ? "คอร์สนี้จะไม่แสดงเป็นคอร์สแนะนำบนเว็บไซต์หลัก" : "คอร์สนี้จะแสดงเป็นคอร์สแนะนำบนเว็บไซต์หลัก",
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'ใช่, ดำเนินการ',
      cancelButtonText: 'ยกเลิก',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-outline-secondary'
      },
      buttonsStyling: false
    }).then((result) => {
      if (result.isConfirmed) {
        // ส่งคำขอผ่าน AJAX เพื่อเปลี่ยนสถานะ
        $.ajax({
          url: 'api/frontend-service-api.php?action=toggle_featured',
          type: 'POST',
          data: { id: id, featured: newFeatured },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              // แสดงข้อความสำเร็จและรีโหลดหน้า
              Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
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
              // แสดงข้อความผิดพลาด
              Swal.fire({
                icon: 'error',
                title: 'ผิดพลาด',
                text: response.message || 'ไม่สามารถเปลี่ยนสถานะได้',
                customClass: {
                  confirmButton: 'btn btn-danger'
                },
                buttonsStyling: false
              });
            }
          },
          error: function() {
            // แสดงข้อความเมื่อเกิดข้อผิดพลาดในการเชื่อมต่อ
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
      }
    });
  });
  
  // ดูรายละเอียดคอร์สแบบเร็ว
  $('.quick-view-btn').click(function() {
    const id = $(this).data('id');
    
    // ตั้งค่า ID สำหรับปุ่มแก้ไขในโหมดดูเร็ว
    $('#quickViewEditBtn').data('id', id);
    
    // โหลดข้อมูลคอร์สผ่าน AJAX
    $.ajax({
      url: 'api/frontend-service-api.php?action=quick_view',
      type: 'GET',
      data: { id: id },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // แสดงข้อมูลในหน้าต่าง Quick View
          $('#quickViewContent').html(response.html);
          $('#quickViewModal').modal('show');
        } else {
          // แสดงข้อความผิดพลาด
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
        // แสดงข้อความเมื่อเกิดข้อผิดพลาดในการเชื่อมต่อ
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
  
  // จัดการปุ่มแก้ไขในโหมดดูเร็ว
  $('#quickViewEditBtn').click(function() {
    const id = $(this).data('id');
    // ปิด Modal ดูเร็ว
    $('#quickViewModal').modal('hide');
    // เรียกฟังก์ชันแก้ไข
    $('.edit-btn[data-id="' + id + '"]').click();
  });
  
  // เพิ่มการพรีวิวรูปภาพสำหรับอัปโหลด
  $('#addServiceForm [name="image"], #editServiceForm [name="image"]').change(function() {
    const fileInput = this;
    const previewContainer = $(this).siblings('.image-preview-container');
    
    // สร้าง container สำหรับพรีวิว ถ้ายังไม่มี
    if (previewContainer.length === 0) {
      $(this).after('<div class="image-preview-container mt-2"></div>');
    }
    
    const container = $(this).siblings('.image-preview-container');
    
    if (fileInput.files && fileInput.files[0]) {
      const reader = new FileReader();
      
      reader.onload = function(e) {
        container.html(`
          <div class="position-relative">
            <img src="${e.target.result}" alt="รูปพรีวิว" class="img-thumbnail" style="max-height: 150px">
            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 clear-preview">
              <i class="ri-close-line"></i>
            </button>
          </div>
        `);
        
        // เพิ่มการจัดการปุ่มลบพรีวิว
        $('.clear-preview').click(function(e) {
          e.preventDefault();
          container.empty();
          fileInput.value = '';
        });
      }
      
      reader.readAsDataURL(fileInput.files[0]);
    } else {
      container.empty();
    }
  });
  
  // แสดงพรีวิวรูปภาพปัจจุบันในโหมดแก้ไข
  function initEditImagePreview() {
    // เพิ่มพรีวิวสำหรับรูปภาพปัจจุบัน
    const currentImagePath = $('#edit_current_image_path').val();
    const coursePic = $('#edit_course_pic').val();
    
    if (currentImagePath || coursePic) {
      const imgPath = currentImagePath || coursePic;
      const imgUrl = `../../img/course/${imgPath}`;
      const imgSource = currentImagePath ? 'รูปภาพที่กำหนดเฉพาะ' : 'รูปภาพจากคอร์ส';
      
      $('#edit_image_preview').html(`
        <div class="alert alert-info mt-2 mb-0">
          <div class="d-flex align-items-center">
            <img src="${imgUrl}" alt="รูปภาพปัจจุบัน" class="img-thumbnail me-3" style="max-height: 80px">
            <div>
              <strong>รูปภาพปัจจุบัน (${imgSource})</strong><br>
              <small>อัปโหลดรูปใหม่เพื่อเปลี่ยน หรือปล่อยว่างไว้เพื่อใช้รูปเดิม</small>
            </div>
          </div>
        </div>
      `);
    }
  }
  
  // Edit Button Click
  $('.edit-btn').click(function() {
    const id = $(this).data('id');
    
    // โหลดข้อมูลคอร์สผ่าน AJAX
    $.ajax({
      url: 'api/frontend-service-api.php?action=get',
      type: 'GET',
      data: { id: id },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // เติมข้อมูลลงในฟอร์มแก้ไข
          $('#editServiceFormContent').html(response.html);
          $('#editServiceModal').modal('show');
          
          // เรียกใช้ฟังก์ชันแสดงพรีวิวรูปภาพปัจจุบัน
          initEditImagePreview();
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
    const id = $(this).data('id');
    
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
        // ส่ง AJAX request เพื่อลบ
        $.ajax({
          url: 'api/frontend-service-api.php?action=delete',
          type: 'POST',
          data: { id: id },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              Swal.fire({
                icon: 'success',
                title: 'สำเร็จ',
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
                title: 'ผิดพลาด',
                text: response.message || 'ไม่สามารถลบข้อมูลได้',
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
      }
    });
  });
  
  // เพิ่ม tooltip
  $('[data-bs-toggle="tooltip"]').tooltip();
});

// เพิ่มเข้าไปในส่วน script ที่มีอยู่แล้ว

// ฟังก์ชันสำหรับโหลดรายละเอียดคอร์สตอนเลือก
$('#add_course_id').change(function() {
  const courseId = $(this).val();
  if (!courseId) {
    $('#course_preview').addClass('d-none');
    $('#course_description_preview').addClass('d-none');
    return;
  }
  
  // โหลดข้อมูลคอร์สผ่าน AJAX
  $.ajax({
    url: 'api/frontend-service-api.php?action=get_course_details',
    type: 'GET',
    data: { course_id: courseId },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        const course = response.data;
        
        // แสดงพรีวิวคอร์ส
        $('#course_preview').removeClass('d-none');
        $('#course_name').text(course.course_name);
        $('#course_price').text(number_format(course.course_price, 0) + ' บาท');
        
        // กำหนดรูปภาพ
        if (course.course_pic) {
          $('#course_image').css('background-image', `url(../../img/course/${course.course_pic})`);
        } else {
          $('#course_image').css('background-image', 'url(../../img/course/course.png)');
        }
        
        // แสดงคำอธิบาย
        if (course.course_detail) {
          $('#course_description_preview').removeClass('d-none');
          $('#course_description_text').text(truncateText(course.course_detail, 200));
        } else {
          $('#course_description_preview').addClass('d-none');
        }
        
        // อัพเดทหมายเหตุราคา
        $('#original_price_note').text(`ถ้าว่างไว้จะใช้ราคาจากคอร์ส (${number_format(course.course_price, 0)} บาท)`);
      }
    }
  });
});

// ฟังก์ชันสำหรับการส่งฟอร์มเพิ่มคอร์ส
$('#addServiceForm').submit(function(e) {
  e.preventDefault();
  
  // แสดง loading
  const submitBtn = $(this).find('button[type="submit"]');
  const spinner = submitBtn.find('.spinner-border');
  submitBtn.prop('disabled', true);
  spinner.removeClass('d-none');
  
  // สร้าง FormData สำหรับส่งข้อมูลรวมไฟล์
  const formData = new FormData(this);
  
  // แปลงค่าชุดการแสดงผลที่ใช้ checkbox
  if (!$('#status_active').prop('checked')) {
    formData.set('status', '0');
  }
  
  // ส่งข้อมูลผ่าน AJAX
  $.ajax({
    url: 'api/frontend-service-api.php?action=add',
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
          confirmButtonText: 'ตกลง',
          customClass: {
            confirmButton: 'btn btn-success'
          },
          buttonsStyling: false
        }).then(() => {
          // รีเซ็ตฟอร์มและปิด Modal
          $('#addServiceForm')[0].reset();
          $('#addServiceModal').modal('hide');
          
          // รีโหลดหน้าเพื่อแสดงข้อมูลใหม่
          location.reload();
        });
      } else {
        // แสดงข้อความผิดพลาด
        Swal.fire({
          icon: 'error',
          title: 'เกิดข้อผิดพลาด',
          text: response.message,
          confirmButtonText: 'ตกลง',
          customClass: {
            confirmButton: 'btn btn-danger'
          },
          buttonsStyling: false
        });
      }
    },
    error: function(xhr, status, error) {
      // แสดงข้อความผิดพลาดกรณีไม่สามารถเชื่อมต่อได้
      Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด',
        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
        confirmButtonText: 'ตกลง',
        customClass: {
          confirmButton: 'btn btn-danger'
        },
        buttonsStyling: false
      });
    },
    complete: function() {
      // ซ่อน loading
      submitBtn.prop('disabled', false);
      spinner.addClass('d-none');
    }
  });
});

// ฟังก์ชันสำหรับการส่งฟอร์มแก้ไขคอร์ส
$('#editServiceForm').submit(function(e) {
  e.preventDefault();
  
  // แสดง loading
  const submitBtn = $(this).find('button[type="submit"]');
  const spinner = submitBtn.find('.spinner-border');
  submitBtn.prop('disabled', true);
  spinner.removeClass('d-none');
  
  // สร้าง FormData สำหรับส่งข้อมูลรวมไฟล์
  const formData = new FormData(this);
  
  // แก้ไขค่า status ให้ถูกต้อง
  if (document.getElementById('edit_status_active').checked) {
    formData.set('status', '1');
  } else {
    formData.set('status', '0');
  }
  
  // ส่งข้อมูลผ่าน AJAX
  $.ajax({
    url: 'api/frontend-service-api.php?action=update',
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
          confirmButtonText: 'ตกลง',
          customClass: {
            confirmButton: 'btn btn-success'
          },
          buttonsStyling: false
        }).then(() => {
          // ปิด Modal
          $('#editServiceModal').modal('hide');
          
          // รีโหลดหน้าเพื่อแสดงข้อมูลใหม่
          location.reload();
        });
      } else {
        // แสดงข้อความผิดพลาด
        Swal.fire({
          icon: 'error',
          title: 'เกิดข้อผิดพลาด',
          text: response.message,
          confirmButtonText: 'ตกลง',
          customClass: {
            confirmButton: 'btn btn-danger'
          },
          buttonsStyling: false
        });
      }
    },
    error: function(xhr, status, error) {
      // แสดงข้อความผิดพลาดกรณีไม่สามารถเชื่อมต่อได้
      Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด',
        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
        confirmButtonText: 'ตกลง',
        customClass: {
          confirmButton: 'btn btn-danger'
        },
        buttonsStyling: false
      });
    },
    complete: function() {
      // ซ่อน loading
      submitBtn.prop('disabled', false);
      spinner.addClass('d-none');
    }
  });
});

// ฟังก์ชัน Helper สำหรับตัดข้อความที่ยาวเกินไป
function truncateText(text, maxLength) {
  if (!text) return '';
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
}

// ฟังก์ชัน Helper สำหรับแปลงตัวเลขเป็นรูปแบบสกุลเงิน
function number_format(number, decimals = 0) {
  return new Intl.NumberFormat('th-TH', {
    minimumFractionDigits: decimals,
    maximumFractionDigits: decimals
  }).format(number);
}
</script>
  </body>
</html>