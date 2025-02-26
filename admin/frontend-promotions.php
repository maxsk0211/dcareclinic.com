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

    <title>จัดการโปรโมชั่นบนเว็บไซต์หลัก | dcareclinic.com</title>

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

    <!-- datatables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">

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
      
      .promotion-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 5;
      }
      
      .countdown-display {
        font-size: 0.8rem;
        letter-spacing: 0.5px;
      }
      
      .status-active {
        background-color: #28a745;
      }
      
      .status-upcoming {
        background-color: #17a2b8;
      }
      
      .status-expired {
        background-color: #6c757d;
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
              <!-- Promotions List -->
              <div class="card">
                <div class="card-header border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h5 class="card-title mb-0 text-white">โปรโมชั่นบนเว็บไซต์หลัก</h5>
                      <small class="text-white">จัดการโปรโมชั่นที่แสดงให้ลูกค้าเห็นบนหน้าเว็บไซต์หลัก</small>
                    </div>
                    <div class="d-flex gap-2">
                      <a href="frontend-services.php" class="btn btn-warning">
                        <i class="ri-service-line me-1"></i> จัดการคอร์ส
                      </a>
                      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPromotionModal">
                        <i class="ri-add-line me-1"></i> เพิ่มโปรโมชั่น
                      </button>
                    </div>
                  </div>
                </div>
                
                <!-- ส่วนช่องค้นหาและตัวกรอง -->
                <div class="card-body border-bottom pt-3 pb-3">
                  <div class="row g-3">
                    <div class="col-md-4">
                      <label class="form-label">ค้นหาโปรโมชั่น</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="ri-search-line"></i></span>
                        <input type="text" class="form-control" id="searchPromotion" placeholder="ชื่อโปรโมชั่น...">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label">สถานะโปรโมชั่น</label>
                      <select class="form-select" id="filterStatus">
                        <option value="">ทั้งหมด</option>
                        <option value="active">กำลังใช้งาน</option>
                        <option value="upcoming">กำลังจะมาถึง</option>
                        <option value="expired">หมดอายุ</option>
                        <option value="disabled">ปิดใช้งาน</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label">การแสดงผล</label>
                      <select class="form-select" id="filterFeatured">
                        <option value="">ทั้งหมด</option>
                        <option value="1">โปรโมชั่นแนะนำ</option>
                        <option value="0">โปรโมชั่นทั่วไป</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">&nbsp;</label>
                      <button type="button" class="btn btn-outline-secondary w-100" id="clearFilters">
                        <i class="ri-filter-off-line me-1"></i> ล้างตัวกรอง
                      </button>
                    </div>
                  </div>
                </div>
                
                <!-- Dashboard สรุปข้อมูล -->
                <div class="card-body border-bottom pb-0">
                  <div class="row g-3">
                    <?php
                    // คำนวณสถิติโปรโมชั่น
                    $today = date('Y-m-d');
                    
                    // โปรโมชั่นทั้งหมด
                    $sql_total = "SELECT COUNT(*) as total FROM frontend_promotions";
                    $result_total = $conn->query($sql_total);
                    $total = $result_total->fetch_assoc()['total'] ?? 0;
                    
                    // โปรโมชั่นที่กำลังใช้งาน
                    $sql_active = "SELECT COUNT(*) as active FROM frontend_promotions WHERE status = 1 AND start_date <= '$today' AND end_date >= '$today'";
                    $result_active = $conn->query($sql_active);
                    $active = $result_active->fetch_assoc()['active'] ?? 0;
                    
                    // โปรโมชั่นที่กำลังจะมาถึง
                    $sql_upcoming = "SELECT COUNT(*) as upcoming FROM frontend_promotions WHERE status = 1 AND start_date > '$today'";
                    $result_upcoming = $conn->query($sql_upcoming);
                    $upcoming = $result_upcoming->fetch_assoc()['upcoming'] ?? 0;
                    
                    // โปรโมชั่นที่หมดอายุ
                    $sql_expired = "SELECT COUNT(*) as expired FROM frontend_promotions WHERE status = 1 AND end_date < '$today'";
                    $result_expired = $conn->query($sql_expired);
                    $expired = $result_expired->fetch_assoc()['expired'] ?? 0;
                    
                    // โปรโมชั่นแนะนำ
                    $sql_featured = "SELECT COUNT(*) as featured FROM frontend_promotions WHERE is_featured = 1";
                    $result_featured = $conn->query($sql_featured);
                    $featured = $result_featured->fetch_assoc()['featured'] ?? 0;
                    ?>
                    
                    <div class="col-md-3">
                      <div class="card bg-primary bg-opacity-10 border-0">
                        <div class="card-body">
                          <div class="d-flex align-items-center">
                            <div class="avatar">
                              <div class="avatar-initial bg-primary rounded">
                                <i class="ri-price-tag-3-line fs-4"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                              <h5 class="mb-0"><?php echo $total; ?></h5>
                              <small>โปรโมชั่นทั้งหมด</small>
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
                                <i class="ri-calendar-check-line fs-4"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                              <h5 class="mb-0"><?php echo $active; ?></h5>
                              <small>กำลังใช้งาน</small>
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
                                <i class="ri-calendar-line fs-4"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                              <h5 class="mb-0"><?php echo $upcoming; ?></h5>
                              <small>กำลังจะมาถึง</small>
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
                              <h5 class="mb-0"><?php echo $featured; ?></h5>
                              <small>โปรโมชั่นแนะนำ</small>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card-body">
                  <div class="card-datatable table-responsive">
                    <table id="promotionsTable" class="table table-hover border-top">
                      <thead class="table-light">
					    <tr>
					      <th width="60" class="text-center">รูปภาพ</th>
					      <th>ชื่อโปรโมชั่น</th>
					      <th>คอร์ส</th>
					      <th width="120" class="text-end">ราคา (บาท)</th>
					      <th width="120" class="text-center">ระยะเวลา</th>
					      <th width="100" class="text-center">สถานะ</th>
					      <th width="150" class="text-center">จัดการ</th>
					    </tr>
                      </thead>
                      <tbody>
                        <?php
                        $sql = "SELECT fp.*, c.course_name, c.course_price, c.course_pic 
                                FROM frontend_promotions fp
                                JOIN course c ON fp.course_id = c.course_id
                                ORDER BY fp.display_order ASC, fp.start_date DESC";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                          while ($row = $result->fetch_assoc()) {
                            // กำหนดรูปภาพ
                            $imgPath = $row['image_path'] ?? $row['course_pic'] ?? 'promotion.png';
                            $imgUrl = "../img/" . ($row['image_path'] ? "promotion/{$imgPath}" : "course/{$imgPath}");
                            
                            // คำนวณส่วนลด
                            $discount = '';
                            if (!empty($row['discount_percent'])) {
                              $discount = ' <span class="badge bg-danger">ลด '.$row['discount_percent'].'%</span>';
                            } elseif (!empty($row['original_price']) && !empty($row['promotion_price'])) {
                              $discount_val = (($row['original_price'] - $row['promotion_price']) / $row['original_price']) * 100;
                              $discount = ' <span class="badge bg-danger">ลด '.round($discount_val, 0).'%</span>';
                            }
                            
                            // กำหนดสถานะโปรโมชั่น
                            $now = strtotime(date('Y-m-d'));
                            $start = strtotime($row['start_date']);
                            $end = strtotime($row['end_date']);
                            
                            $status = 'disabled';
                            $statusText = 'ปิดใช้งาน';
                            $statusClass = 'bg-secondary';
                            
                            if ($row['status'] == 1) {
                              if ($now < $start) {
                                $status = 'upcoming';
                                $statusText = 'กำลังจะมาถึง';
                                $statusClass = 'bg-info';
                              } elseif ($now > $end) {
                                $status = 'expired';
                                $statusText = 'หมดอายุ';
                                $statusClass = 'bg-secondary';
                              } else {
                                $status = 'active';
                                $statusText = 'กำลังใช้งาน';
                                $statusClass = 'bg-success';
                              }
                            }
                            
                            // คำนวณวันที่แสดงผล
                            $date_display = '';
                            $date_format = 'd/m/Y';
                            if ($status == 'active' || $status == 'upcoming') {
                              $date_display = 'ถึง '.date($date_format, $end);
                            } elseif ($status == 'expired') {
                              $date_display = 'สิ้นสุด '.date($date_format, $end);
                            } else {
                              $date_display = date($date_format, $start).' - '.date($date_format, $end);
                            }
                        ?>
                        <tr data-id="<?php echo $row['id']; ?>" data-status="<?php echo $status; ?>" data-featured="<?php echo $row['is_featured']; ?>">
                          <td class="text-center position-relative">
                            <img src="<?php echo $imgUrl; ?>" class="rounded img-thumbnail thumbnail-preview cursor-pointer view-image" alt="<?php echo $row['title']; ?>" data-bs-toggle="tooltip" title="คลิกเพื่อดูภาพขนาดใหญ่">
                            <?php if ($row['badge_text']) { ?>
                              <span class="badge bg-warning promotion-badge"><?php echo $row['badge_text']; ?></span>
                            <?php } ?>
                          </td>
                          <td>
                            <h6 class="mb-0"><?php echo $row['title']; ?></h6>
                            <small class="text-muted d-block">
                              <?php if ($row['is_featured'] == 1) { ?>
                                <span class="badge bg-primary me-1">แนะนำ</span>
                              <?php } ?>
                              <span>ลำดับแสดง: <?php echo $row['display_order']; ?></span>
                            </small>
                          </td>
                          <td><?php echo $row['course_name']; ?></td>
                          <td class="text-end">
                            <?php if ($row['original_price']) { ?>
                              <span class="text-decoration-line-through text-muted"><?php echo number_format($row['original_price'], 0); ?></span><br>
                            <?php } ?>
                            <span class="fw-bold text-danger"><?php echo number_format($row['promotion_price'], 0); ?></span>
                            <?php echo $discount; ?>
                          </td>
                          <td class="text-center">
                            <div class="d-flex flex-column">
                              <span class="small"><?php echo $date_display; ?></span>
                              <?php if ($status == 'active') { ?>
                                <span class="countdown-display" data-end="<?php echo $row['end_date']; ?>">
                                  เหลือ <span class="days">0</span> วัน
                                </span>
                              <?php } ?>
                            </div>
                          </td>
                          <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                          </td>
                          <td>
                            <div class="d-flex justify-content-center gap-2">
                              <button type="button" class="btn btn-sm btn-primary quick-view-btn" data-id="<?php echo $row['id']; ?>" data-bs-toggle="tooltip" title="ดูรายละเอียด">
                                <i class="ri-eye-line"></i>
                              </button>
                              <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $row['id']; ?>" data-bs-toggle="tooltip" title="แก้ไขโปรโมชั่น">
                                <i class="ri-edit-line"></i>
                              </button>
                              <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                  <i class="ri-settings-3-line"></i>
                                </button>
                                <ul class="dropdown-menu">
                                  <li>
                                    <a class="dropdown-item toggle-status-btn" href="javascript:void(0);" data-id="<?php echo $row['id']; ?>" data-current="<?php echo $row['status']; ?>">
                                      <?php echo ($row['status'] == 1) ? '<i class="ri-eye-off-line me-2"></i>ปิดใช้งาน' : '<i class="ri-eye-line me-2"></i>เปิดใช้งาน'; ?>
                                    </a>
                                  </li>
                                  <li>
                                    <a class="dropdown-item toggle-featured-btn" href="javascript:void(0);" data-id="<?php echo $row['id']; ?>" data-current="<?php echo $row['is_featured']; ?>">
                                      <?php echo ($row['is_featured'] == 1) ? '<i class="ri-star-line me-2"></i>ยกเลิกการแนะนำ' : '<i class="ri-star-fill me-2"></i>ตั้งเป็นโปรโมชั่นแนะนำ'; ?>
                                    </a>
                                  </li>
                                  <li><hr class="dropdown-divider"></li>
                                  <li>
                                    <a class="dropdown-item text-danger delete-btn" href="javascript:void(0);" data-id="<?php echo $row['id']; ?>">
                                      <i class="ri-delete-bin-line me-2"></i>ลบโปรโมชั่น
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
                          <td colspan="7" class="text-center py-3">
                            <img src="../assets/img/illustrations/page-misc-empty-state.png" alt="No data" class="mb-2" width="80">
                            <p class="mb-0">ไม่พบข้อมูลโปรโมชั่น</p>
                            <small class="text-muted">เริ่มสร้างโปรโมชั่นใหม่โดยคลิกที่ปุ่ม "เพิ่มโปรโมชั่น"</small>
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
            </div>
            <!-- / Content -->

            <!-- Modal เพื่อแสดงรูปภาพขนาดใหญ่ -->
            <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                  <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="imagePreviewTitle">รูปภาพโปรโมชั่น</h5>
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

            <!-- Modal ดูรายละเอียดโปรโมชั่นแบบเร็ว -->
            <div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header border-bottom">
                    <h5 class="modal-title">รายละเอียดโปรโมชั่น</h5>
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
                    <button type="button" class="btn btn-primary" id="quickViewEditBtn">แก้ไขโปรโมชั่น</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal เพิ่มโปรโมชั่นใหม่ -->
            <div class="modal fade" id="addPromotionModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <form class="modal-content" id="addPromotionForm">
                  <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="ri-add-line me-2"></i>เพิ่มโปรโมชั่นใหม่</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="alert alert-info">
                      <div class="d-flex gap-2">
                        <i class="ri-information-line fs-5"></i>
                        <div>
                          <strong>คำแนะนำ:</strong> เลือกคอร์สและกำหนดรายละเอียดโปรโมชั่นที่ต้องการแสดงบนเว็บไซต์ โปรโมชั่นจะแสดงในช่วงวันที่กำหนดเท่านั้น
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
                        <label class="form-label required">ชื่อโปรโมชั่น</label>
                        <input type="text" class="form-control" name="title" placeholder="ชื่อโปรโมชั่นที่โดดเด่น" required>
                      </div>
                      
                      <div class="col-md-6 mb-3">
                        <label class="form-label">ราคาปกติ (บาท)</label>
                        <input type="number" class="form-control" name="original_price" id="add_original_price" step="1" min="0" placeholder="ราคาปกติ">
                        <small class="text-muted" id="original_price_note">ถ้าว่างไว้จะใช้ราคาจากคอร์ส</small>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label required">ราคาโปรโมชั่น (บาท)</label>
                        <input type="number" class="form-control" name="promotion_price" id="add_promotion_price" step="1" min="0" placeholder="ราคาโปรโมชั่น" required>
                      </div>
                      
                      <div class="col-md-4 mb-3">
                        <label class="form-label">เปอร์เซ็นต์ส่วนลด (%)</label>
                        <input type="number" class="form-control" name="discount_percent" id="add_discount_percent" step="0.01" min="0" max="100" placeholder="คำนวณอัตโนมัติ">
                        <small class="text-muted">ปล่อยว่างเพื่อคำนวณอัตโนมัติ</small>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label class="form-label">ข้อความป้ายกำกับ</label>
                        <input type="text" class="form-control" name="badge_text" placeholder="เช่น HOT DEAL, สิ้นสุดเร็วๆนี้">
                      </div>
                      <div class="col-md-4 mb-3">
                        <label class="form-label">ลำดับการแสดงผล</label>
                        <input type="number" class="form-control" name="display_order" value="0" min="0">
                      </div>
                      
                      <div class="col-md-6 mb-3">
                        <label class="form-label required">วันเริ่มต้น</label>
                        <input type="date" class="form-control" name="start_date" required>
                      </div>
                      <div class="col-md-6 mb-3">
                        <label class="form-label required">วันสิ้นสุด</label>
                        <input type="date" class="form-control" name="end_date" required>
                      </div>
                      
                      <div class="col-12 mb-3">
                        <label class="form-label d-block">สถานะและตัวเลือก</label>
                        <div class="form-check form-switch form-check-inline mt-2">
                          <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1">
                          <label class="form-check-label" for="is_featured">แสดงในส่วน Featured</label>
                        </div>
                        <div class="form-check form-switch form-check-inline">
                          <input class="form-check-input" type="checkbox" name="status" id="status_active" value="1" checked>
                          <label class="form-check-label" for="status_active">เปิดใช้งาน</label>
                        </div>
                      </div>
                      
                      <div class="col-12 mb-3">
                        <label class="form-label">รายละเอียดโปรโมชั่น</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="รายละเอียดเพิ่มเติมเกี่ยวกับโปรโมชั่น"></textarea>
                      </div>
                      <div class="col-12 mb-3">
                        <label class="form-label">คุณสมบัติเพิ่มเติม (แยกแต่ละบรรทัด)</label>
                        <textarea class="form-control" name="features" rows="3" placeholder="เช่น:
รับฟรี! บริการนวดหน้า
รับรองผลลัพธ์ 100%
จองคิววันนี้รับส่วนลดเพิ่ม 5%"></textarea>
                      </div>
                      <div class="col-12 mb-3">
                        <label class="form-label">รูปภาพ</label>
                        <input type="file" class="form-control" name="image" id="add_promotion_image" accept="image/*">
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
                      บันทึกโปรโมชั่น
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Modal แก้ไขโปรโมชั่น -->
            <div class="modal fade" id="editPromotionModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <form class="modal-content" id="editPromotionForm">
                  <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="ri-edit-line me-2"></i>แก้ไขโปรโมชั่น</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <!-- ฟอร์มแก้ไขจะถูกโหลดผ่าน AJAX -->
                    <div id="editPromotionFormContent" class="text-center">
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
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

 <script>
$(document).ready(function() {
  // ป้องกันการ initialize DataTable ซ้ำซ้อน
  if ($.fn.DataTable.isDataTable('#promotionsTable')) {
    $('#promotionsTable').DataTable().destroy();
  }

  // ตรวจสอบว่าตารางมีข้อมูลก่อนเริ่ม DataTable
  if ($('#promotionsTable tbody tr').length > 0 && $('#promotionsTable tbody tr td').length > 1) {
    try {
      const promotionsTable = $('#promotionsTable').DataTable({
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
                exportOptions: { columns: [1, 2, 3, 4, 5] }
              },
              {
                extend: 'pdf',
                text: '<i class="ri-file-pdf-line me-1"></i>PDF',
                className: 'dropdown-item',
                exportOptions: { columns: [1, 2, 3, 4, 5] }
              },
              {
                extend: 'print',
                text: '<i class="ri-printer-line me-1"></i>พิมพ์',
                className: 'dropdown-item',
                exportOptions: { columns: [1, 2, 3, 4, 5] }
              }
            ]
          }
        ],
        pageLength: 10,
        ordering: true,
        order: [[4, 'asc']], // เรียงตามวันที่สิ้นสุด
        responsive: true,
        columnDefs: [
          { orderable: false, targets: [0, 6] } // ไม่ให้เรียงตามคอลัมน์รูปภาพและปุ่มจัดการ
        ]
      });
  
      // อัพเดทนับถอยหลัง
      function updateCountdowns() {
        $('.countdown-display').each(function() {
          const endDate = $(this).data('end');
          const endTime = new Date(endDate).getTime();
          const now = new Date().getTime();
          
          const distance = endTime - now;
          
          if (distance < 0) {
            $(this).html('<span class="text-danger">หมดอายุแล้ว</span>');
            return;
          }
          
          const days = Math.floor(distance / (1000 * 60 * 60 * 24));
          const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          
          $(this).html(`เหลือ <span class="days">${days}</span> วัน ${hours} ชม.`);
        });
      }
      
      // อัพเดทนับถอยหลังทุกนาที
      updateCountdowns();
      setInterval(updateCountdowns, 60000);
      
      // ฟังก์ชันกรองตามสถานะโปรโมชั่น
      function applyFilters() {
        // ล้าง search function ที่อาจมีอยู่เดิม
        $.fn.dataTable.ext.search.pop();
        
        // สร้าง function ใหม่สำหรับกรอง
        $.fn.dataTable.ext.search.push(
          function(settings, data, dataIndex) {
            const $row = $(promotionsTable.row(dataIndex).node());
            const statusFilter = $('#filterStatus').val();
            const featuredFilter = $('#filterFeatured').val();
            
            let showRow = true;
            
            // กรองตามสถานะโปรโมชั่น
            if (statusFilter && $row.data('status') !== statusFilter) {
              showRow = false;
            }
            
            // กรองตามการเป็นโปรโมชั่นแนะนำ
            if (featuredFilter !== '' && $row.data('featured') != featuredFilter) {
              showRow = false;
            }
            
            return showRow;
          }
        );
        
        // วาดตารางใหม่พร้อมตัวกรอง
        promotionsTable.draw();
      }
      
      // เมื่อมีการเปลี่ยนแปลงตัวกรอง
      $('#filterStatus, #filterFeatured').change(function() {
        applyFilters();
      });
      
      // ปุ่มล้างตัวกรอง
      $('#clearFilters').click(function() {
        $('#filterStatus').val('');
        $('#filterFeatured').val('');
        $('#searchPromotion').val('');
        promotionsTable.search('').draw();
        
        // ล้าง search function
        $.fn.dataTable.ext.search.pop();
        promotionsTable.draw();
      });
      
      // ค้นหาทั่วไป
      $('#searchPromotion').on('keyup', function() {
        promotionsTable.search(this.value).draw();
      });
    } catch (error) {
      console.error("DataTables initialization error:", error);
    }
  } else {
    console.log("Table has no data or invalid structure, skipping DataTable initialization");
  }
  
  // แสดงรูปภาพขนาดใหญ่เมื่อคลิกที่รูปภาพขนาดเล็ก
  $(document).on('click', '.view-image', function() {
    const imgSrc = $(this).attr('src');
    const promotionName = $(this).attr('alt');
    
    $('#imagePreviewTitle').text('รูปภาพโปรโมชั่น: ' + promotionName);
    $('#largeImage').attr('src', imgSrc);
    $('#imagePreviewModal').modal('show');
  });
  
  // เพิ่มการพรีวิวรูปภาพสำหรับอัปโหลด
  $(document).on('change', '#addPromotionForm [name="image"], #editPromotionForm [name="image"]', function() {
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
  
  // ฟังก์ชันสำหรับโหลดรายละเอียดคอร์สตอนเลือก
  $(document).on('change', '#add_course_id', function() {
    const courseId = $(this).val();
    if (!courseId) {
      $('#course_preview').addClass('d-none');
      return;
    }
    
    // ตั้งค่าราคาปกติตามคอร์ส
    const coursePrice = $('option:selected', this).data('price');
    $('#add_original_price').val(coursePrice);
    $('#original_price_note').text(`ราคาปกติจากคอร์สที่เลือก: ${number_format(coursePrice, 0)} บาท`);
    
    // โหลดข้อมูลคอร์สผ่าน AJAX
    $.ajax({
      url: 'api/frontend-promotion-api.php?action=get_course_details',
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
            $('#course_image').css('background-image', `url(../img/course/${course.course_pic})`);
          } else {
            $('#course_image').css('background-image', 'url(../img/course/course.png)');
          }
        }
      }
    });
  });
  
  // คำนวณส่วนลดอัตโนมัติเมื่อเปลี่ยนราคา
  $(document).on('input', '#add_original_price, #add_promotion_price', function() {
    const originalPrice = parseFloat($('#add_original_price').val()) || 0;
    const promotionPrice = parseFloat($('#add_promotion_price').val()) || 0;
    
    if (originalPrice > 0 && promotionPrice > 0 && originalPrice >= promotionPrice) {
      const discount = ((originalPrice - promotionPrice) / originalPrice) * 100;
      $('#add_discount_percent').val(discount.toFixed(2));
    } else {
      $('#add_discount_percent').val('');
    }
  });
  
  // ปรับราคาอัตโนมัติเมื่อเปลี่ยนเปอร์เซ็นต์ส่วนลด
  $(document).on('input', '#add_discount_percent', function() {
    const originalPrice = parseFloat($('#add_original_price').val()) || 0;
    const discountPercent = parseFloat($(this).val()) || 0;
    
    if (originalPrice > 0 && discountPercent >= 0 && discountPercent <= 100) {
      const promotionPrice = originalPrice - (originalPrice * (discountPercent / 100));
      $('#add_promotion_price').val(promotionPrice.toFixed(0));
    }
  });
  
  // ดูรายละเอียดโปรโมชั่นแบบเร็ว
  $(document).on('click', '.quick-view-btn', function() {
    const id = $(this).data('id');
    
    // ตั้งค่า ID สำหรับปุ่มแก้ไขในโหมดดูเร็ว
    $('#quickViewEditBtn').data('id', id);
    
    // โหลดข้อมูลโปรโมชั่นผ่าน AJAX
    $.ajax({
      url: 'api/frontend-promotion-api.php?action=quick_view',
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
  $(document).on('click', '#quickViewEditBtn', function() {
    const id = $(this).data('id');
    // ปิด Modal ดูเร็ว
    $('#quickViewModal').modal('hide');
    // เรียกฟังก์ชันแก้ไข
    $('.edit-btn[data-id="' + id + '"]').click();
  });
  
  // Edit Button Click
  $(document).on('click', '.edit-btn', function() {
    const id = $(this).data('id');
    
    // โหลดข้อมูลโปรโมชั่นผ่าน AJAX
    $.ajax({
      url: 'api/frontend-promotion-api.php?action=get',
      type: 'GET',
      data: { id: id },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // เติมข้อมูลลงในฟอร์มแก้ไข
          $('#editPromotionFormContent').html(response.html);
          $('#editPromotionModal').modal('show');
          
          // เรียกใช้ฟังก์ชันแสดงพรีวิวรูปภาพปัจจุบัน
          initEditImagePreview();
          
          // เพิ่มการคำนวณส่วนลดอัตโนมัติเมื่อเปลี่ยนราคา
          $('[name="original_price"], [name="promotion_price"]').on('input', function() {
            const originalPrice = parseFloat($('[name="original_price"]').val()) || 0;
            const promotionPrice = parseFloat($('[name="promotion_price"]').val()) || 0;
            
            if (originalPrice > 0 && promotionPrice > 0 && originalPrice >= promotionPrice) {
              const discount = ((originalPrice - promotionPrice) / originalPrice) * 100;
              $('[name="discount_percent"]').val(discount.toFixed(2));
            } else {
              $('[name="discount_percent"]').val('');
            }
          });
          
          // ปรับราคาอัตโนมัติเมื่อเปลี่ยนเปอร์เซ็นต์ส่วนลด
          $('[name="discount_percent"]').on('input', function() {
            const originalPrice = parseFloat($('[name="original_price"]').val()) || 0;
            const discountPercent = parseFloat($(this).val()) || 0;
            
            if (originalPrice > 0 && discountPercent >= 0 && discountPercent <= 100) {
              const promotionPrice = originalPrice - (originalPrice * (discountPercent / 100));
              $('[name="promotion_price"]').val(promotionPrice.toFixed(0));
            }
          });
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
  
  // แสดงพรีวิวรูปภาพปัจจุบันในโหมดแก้ไข
  function initEditImagePreview() {
    // เพิ่มพรีวิวสำหรับรูปภาพปัจจุบัน
    const currentImagePath = $('#current_image_path').val();
    const coursePic = $('#course_pic').val();
    
    if (currentImagePath || coursePic) {
      const imgPath = currentImagePath || coursePic;
      const imgUrl = "../img/" + (currentImagePath ? "promotion/" : "course/") + imgPath;
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
  
  // Delete Button Click
  $(document).on('click', '.delete-btn', function() {
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
          url: 'api/frontend-promotion-api.php?action=delete',
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
  
  // เปลี่ยนสถานะการแสดงผล
  $(document).on('click', '.toggle-status-btn', function() {
    const id = $(this).data('id');
    const currentStatus = $(this).data('current');
    const newStatus = currentStatus == 1 ? 0 : 1;
    const statusLabel = newStatus == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
    
    // แสดง SweetAlert2 เพื่อยืนยันการเปลี่ยนสถานะ
    Swal.fire({
      title: `${statusLabel}โปรโมชั่นนี้?`,
      text: currentStatus == 1 ? "โปรโมชั่นนี้จะไม่แสดงบนเว็บไซต์หลัก" : "โปรโมชั่นนี้จะแสดงบนเว็บไซต์หลัก",
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
          url: 'api/frontend-promotion-api.php?action=toggle_status',
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
  
  // เปลี่ยนสถานะโปรโมชั่นแนะนำ
  $(document).on('click', '.toggle-featured-btn', function() {
    const id = $(this).data('id');
    const currentFeatured = $(this).data('current');
    const newFeatured = currentFeatured == 1 ? 0 : 1;
    const featuredLabel = newFeatured == 1 ? 'ตั้งเป็นโปรโมชั่นแนะนำ' : 'ยกเลิกการแนะนำ';
    
    Swal.fire({
      title: `${featuredLabel}?`,
      text: currentFeatured == 1 ? "โปรโมชั่นนี้จะไม่แสดงเป็นโปรโมชั่นแนะนำบนเว็บไซต์หลัก" : "โปรโมชั่นนี้จะแสดงเป็นโปรโมชั่นแนะนำบนเว็บไซต์หลัก",
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
          url: 'api/frontend-promotion-api.php?action=toggle_featured',
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
  
  // ฟังก์ชันสำหรับการส่งฟอร์มเพิ่มโปรโมชั่น
  $('#addPromotionForm').submit(function(e) {
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
      url: 'api/frontend-promotion-api.php?action=add',
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
            $('#addPromotionForm')[0].reset();
            $('#addPromotionModal').modal('hide');
            
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
  
  // ฟังก์ชันสำหรับการส่งฟอร์มแก้ไขโปรโมชั่น
  $('#editPromotionForm').submit(function(e) {
    e.preventDefault();
    
    // แสดง loading
    const submitBtn = $(this).find('button[type="submit"]');
    const spinner = submitBtn.find('.spinner-border');
    submitBtn.prop('disabled', true);
    spinner.removeClass('d-none');
    
    // สร้าง FormData สำหรับส่งข้อมูลรวมไฟล์
    const formData = new FormData(this);
    
    // ส่งข้อมูลผ่าน AJAX
    $.ajax({
      url: 'api/frontend-promotion-api.php?action=update',
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
            $('#editPromotionModal').modal('hide');
            
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
  
  // เพิ่ม tooltip
  $('[data-bs-toggle="tooltip"]').tooltip();
  
  // ฟังก์ชัน Helper สำหรับแปลงตัวเลขเป็นรูปแบบสกุลเงิน
  function number_format(number, decimals = 0) {
    return new Intl.NumberFormat('th-TH', {
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals
    }).format(number);
  }
});
</script>
  </body>
</html>