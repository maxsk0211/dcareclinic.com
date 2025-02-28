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

    <title>จัดการข้อมูลแพทย์บนเว็บไซต์หลัก | dcareclinic.com</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet" />

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
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #f0f0f0;
      }
      
      .doctor-badge {
        position: absolute;
        top: 0;
        right: 0;
        z-index: 5;
      }
      
      .status-active {
        background-color: #28a745;
      }
      
      .status-inactive {
        background-color: #6c757d;
      }
      
      .doctor-specialty {
        font-size: 0.85rem;
        color: #6c757d;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        max-height: 40px;
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
              <!-- Doctors List -->
              <div class="card">
                <div class="card-header border-bottom">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h5 class="card-title mb-0 text-white">ข้อมูลแพทย์บนเว็บไซต์หลัก</h5>
                      <small class="text-white">จัดการข้อมูลแพทย์ที่แสดงให้ลูกค้าเห็นบนหน้าเว็บไซต์หลัก</small>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                      <i class="ri-add-line me-1"></i> เพิ่มแพทย์ใหม่
                    </button>
                  </div>
                </div>
                
                <!-- ส่วนช่องค้นหาและตัวกรอง -->
                <div class="card-body border-bottom pt-3 pb-3">
                  <div class="row g-3">
                    <div class="col-md-5">
                      <label class="form-label">ค้นหาแพทย์</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="ri-search-line"></i></span>
                        <input type="text" class="form-control" id="searchDoctor" placeholder="ชื่อ, สกุล, ความเชี่ยวชาญ...">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label">สถานะ</label>
                      <select class="form-select" id="filterStatus">
                        <option value="">ทั้งหมด</option>
                        <option value="1">เปิดใช้งาน</option>
                        <option value="0">ปิดใช้งาน</option>
                      </select>
                    </div>
                    <div class="col-md-2">
                      <label class="form-label">แพทย์แนะนำ</label>
                      <select class="form-select" id="filterFeatured">
                        <option value="">ทั้งหมด</option>
                        <option value="1">แพทย์แนะนำ</option>
                        <option value="0">แพทย์ทั่วไป</option>
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
                    // คำนวณสถิติแพทย์
                    
                    // แพทย์ทั้งหมด
                    $sql_total = "SELECT COUNT(*) as total FROM frontend_doctors";
                    $result_total = $conn->query($sql_total);
                    $total = $result_total->fetch_assoc()['total'] ?? 0;
                    
                    // แพทย์ที่เปิดใช้งาน
                    $sql_active = "SELECT COUNT(*) as active FROM frontend_doctors WHERE status = 1";
                    $result_active = $conn->query($sql_active);
                    $active = $result_active->fetch_assoc()['active'] ?? 0;
                    
                    // แพทย์แนะนำ
                    $sql_featured = "SELECT COUNT(*) as featured FROM frontend_doctors WHERE is_featured = 1";
                    $result_featured = $conn->query($sql_featured);
                    $featured = $result_featured->fetch_assoc()['featured'] ?? 0;
                    ?>
                    
                    <div class="col-md-4">
                      <div class="card bg-primary bg-opacity-10 border-0">
                        <div class="card-body">
                          <div class="d-flex align-items-center">
                            <div class="avatar">
                              <div class="avatar-initial bg-primary rounded">
                                <i class="ri-user-star-line fs-4"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                              <h5 class="mb-0"><?php echo $total; ?></h5>
                              <small>แพทย์ทั้งหมด</small>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="card bg-success bg-opacity-10 border-0">
                        <div class="card-body">
                          <div class="d-flex align-items-center">
                            <div class="avatar">
                              <div class="avatar-initial bg-success rounded">
                                <i class="ri-user-follow-line fs-4"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                              <h5 class="mb-0"><?php echo $active; ?></h5>
                              <small>แพทย์ที่เปิดใช้งาน</small>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
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
                              <small>แพทย์แนะนำ</small>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card-body">
                  <div class="card-datatable table-responsive">
                    <table id="doctorsTable" class="table table-hover border-top">
                      <thead class="table-light">
                        <tr>
                          <th width="60" class="text-center">รูปภาพ</th>
                          <th>ชื่อแพทย์</th>
                          <th>ความเชี่ยวชาญ</th>
                          <th>สถาบันการศึกษา</th>
                          <th width="80" class="text-center">ลำดับแสดง</th>
                          <th width="100" class="text-center">สถานะ</th>
                          <th width="150" class="text-center">จัดการ</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $sql = "SELECT * FROM frontend_doctors ORDER BY display_order ASC, first_name ASC";
                        $result = $conn->query($sql);
                        
                        if ($result && $result->num_rows > 0) {
                          while ($row = $result->fetch_assoc()) {
                            // กำหนดรูปภาพ
                            $imgPath = $row['image_path'] ?? 'doctor.jpg';
                            $imgUrl = "../img/doctors/{$imgPath}";
                            
                            // กำหนดสถานะ
                            $statusClass = $row['status'] == 1 ? 'bg-success' : 'bg-secondary';
                            $statusText = $row['status'] == 1 ? 'เปิดใช้งาน' : 'ปิดใช้งาน';
                        ?>
                        <tr data-id="<?php echo $row['id']; ?>" data-status="<?php echo $row['status']; ?>" data-featured="<?php echo $row['is_featured']; ?>">
                          <td class="text-center position-relative">
                            <img src="<?php echo $imgUrl; ?>" class="thumbnail-preview cursor-pointer view-image" alt="<?php echo $row['title'].$row['first_name'].' '.$row['last_name']; ?>" data-bs-toggle="tooltip" title="คลิกเพื่อดูภาพขนาดใหญ่">
                            <?php if ($row['is_featured'] == 1) { ?>
                              <span class="badge bg-warning doctor-badge"><i class="ri-star-fill"></i></span>
                            <?php } ?>
                          </td>
                          <td>
                            <h6 class="mb-0"><?php echo $row['title'].$row['first_name'].' '.$row['last_name']; ?></h6>
                            <?php if ($row['nickname']) { ?>
                              <small class="text-muted d-block">หมอ<?php echo $row['nickname']; ?></small>
                            <?php } ?>
                          </td>
                          <td>
                            <span class="doctor-specialty"><?php echo $row['specialty']; ?></span>
                          </td>
                          <td><?php echo $row['education'] ?? '-'; ?></td>
                          <td class="text-center"><?php echo $row['display_order']; ?></td>
                          <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                          </td>
                          <td>
                            <div class="d-flex justify-content-center gap-2">
                              <button type="button" class="btn btn-sm btn-primary quick-view-btn" data-id="<?php echo $row['id']; ?>" data-bs-toggle="tooltip" title="ดูรายละเอียด">
                                <i class="ri-eye-line"></i>
                              </button>
                              <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $row['id']; ?>" data-bs-toggle="tooltip" title="แก้ไขข้อมูล">
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
                                      <?php echo ($row['is_featured'] == 1) ? '<i class="ri-star-line me-2"></i>ยกเลิกการแนะนำ' : '<i class="ri-star-fill me-2"></i>ตั้งเป็นแพทย์แนะนำ'; ?>
                                    </a>
                                  </li>
                                  <li><hr class="dropdown-divider"></li>
                                  <li>
                                    <a class="dropdown-item text-danger delete-btn" href="javascript:void(0);" data-id="<?php echo $row['id']; ?>">
                                      <i class="ri-delete-bin-line me-2"></i>ลบข้อมูลแพทย์
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
                            <p class="mb-0">ไม่พบข้อมูลแพทย์</p>
                            <small class="text-muted">เริ่มเพิ่มข้อมูลแพทย์โดยคลิกที่ปุ่ม "เพิ่มแพทย์ใหม่"</small>
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
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header border-bottom">
                    <h5 class="modal-title" id="imagePreviewTitle">รูปภาพแพทย์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body text-center">
                    <img id="largeImage" src="" alt="รูปภาพขนาดใหญ่" class="img-fluid rounded">
                  </div>
                  <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal ดูรายละเอียดแพทย์แบบเร็ว -->
            <div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header border-bottom">
                    <h5 class="modal-title">รายละเอียดแพทย์</h5>
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
                    <button type="button" class="btn btn-primary" id="quickViewEditBtn">แก้ไขข้อมูล</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal เพิ่มแพทย์ใหม่ -->
            <div class="modal fade" id="addDoctorModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <form class="modal-content" id="addDoctorForm" enctype="multipart/form-data">
                  <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="ri-user-add-line me-2"></i>เพิ่มแพทย์ใหม่</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="alert alert-info">
                      <div class="d-flex gap-2">
                        <i class="ri-information-line fs-5"></i>
                        <div>
                          <strong>คำแนะนำ:</strong> กรอกข้อมูลแพทย์เพื่อแสดงบนเว็บไซต์ ข้อมูลที่มีเครื่องหมาย * จำเป็นต้องกรอก
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-3 mb-3">
                        <label class="form-label required">คำนำหน้า</label>
                        <select class="form-select" name="title" required>
                          <option value="นพ.">นพ.</option>
                          <option value="พญ.">พญ.</option>
                          <option value="ทพ.">ทพ.</option>
                          <option value="ทพญ.">ทพญ.</option>
                          <option value="ดร.">ดร.</option>
                          <option value="ศ.นพ.">ศ.นพ.</option>
                          <option value="ศ.พญ.">ศ.พญ.</option>
                          <option value="รศ.นพ.">รศ.นพ.</option>
                          <option value="รศ.พญ.">รศ.พญ.</option>
                          <option value="ผศ.นพ.">ผศ.นพ.</option>
                          <option value="ผศ.พญ.">ผศ.พญ.</option>
                        </select>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label class="form-label required">ชื่อ</label>
                        <input type="text" class="form-control" name="first_name" required>
                      </div>
                      <div class="col-md-5 mb-3">
                        <label class="form-label required">นามสกุล</label>
                        <input type="text" class="form-control" name="last_name" required>
                      </div>
                      <div class="col-md-4 mb-3">
                        <label class="form-label">ชื่อเล่น</label>
                        <input type="text" class="form-control" name="nickname" placeholder="ชื่อเล่นที่ใช้เรียก">
                      </div>
                      <div class="col-md-8 mb-3">
                        <label class="form-label required">ความเชี่ยวชาญ</label>
                        <input type="text" class="form-control" name="specialty" placeholder="เช่น ผู้เชี่ยวชาญด้านเลเซอร์และผิวพรรณ" required>
                      </div>
                      
                      <div class="col-md-12 mb-3">
                        <label class="form-label">สถาบันการศึกษา</label>
                        <input type="text" class="form-control" name="education" placeholder="เช่น จุฬาลงกรณ์มหาวิทยาลัย">
                      </div>
                      
                      <div class="col-md-4 mb-3">
                        <label class="form-label">ลำดับการแสดงผล</label>
                        <input type="number" class="form-control" name="display_order" value="0" min="0">
                      </div>
                      <div class="col-md-8 mb-3">
                        <label class="form-label d-block">สถานะและตัวเลือก</label>
                        <div class="form-check form-switch form-check-inline mt-2">
                          <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1">
                          <label class="form-check-label" for="is_featured">แพทย์แนะนำ</label>
                        </div>
                        <div class="form-check form-switch form-check-inline">
                          <input class="form-check-input" type="checkbox" name="status" id="status_active" value="1" checked>
                          <label class="form-check-label" for="status_active">เปิดใช้งาน</label>
                        </div>
                      </div>
                      
                      <div class="col-12 mb-3">
                        <label class="form-label">ใบรับรองและวุฒิบัตร</label>
                        <input type="text" class="form-control" name="certification" placeholder="เช่น Board Certified, American Board, ฯลฯ">
                      </div>
                      
                      <div class="col-12 mb-3">
                        <label class="form-label">ผลงานและประสบการณ์ (แยกแต่ละบรรทัด)</label>
                        <textarea class="form-control" name="achievements" rows="3" placeholder="เช่น:
ประสบการณ์การรักษามากกว่า 10,000 เคส
วิทยากรด้านความงามระดับนานาชาติ
ผู้เชี่ยวชาญด้านเทคโนโลยีเลเซอร์รุ่นใหม่"></textarea>
                      </div>
                      <div class="col-12 mb-3">
                        <label class="form-label">คุณสมบัติเพิ่มเติม (แยกแต่ละบรรทัด)</label>
                        <textarea class="form-control" name="additional_features" rows="3" placeholder="เช่น:
เชี่ยวชาญด้านการรักษาริ้วรอย
เชี่ยวชาญการรักษาสิวและรอยแผลเป็น
เชี่ยวชาญการฟื้นฟูผิวหน้าด้วยเทคโนโลยีล่าสุด"></textarea>
                      </div>
                      <div class="col-12 mb-3">
                        <label class="form-label">รูปภาพ</label>
                        <input type="file" class="form-control" name="image" id="add_doctor_image" accept="image/*">
                        <div class="d-flex align-items-center mt-2">
                          <div class="me-2"><i class="ri-information-line text-primary"></i></div>
                          <small class="text-muted">ขนาดที่แนะนำ 400x400 พิกเซล (สัดส่วน 1:1)</small>
                        </div>
                        <div class="image-preview-container mt-2"></div>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                      <span class="spinner-border spinner-border-sm d-none me-1" role="status" aria-hidden="true"></span>
                      บันทึกข้อมูล
                    </button>
                  </div>
                </form>
              </div>
            </div>

            <!-- Modal แก้ไขข้อมูลแพทย์ -->
            <div class="modal fade" id="editDoctorModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg">
                <form class="modal-content" id="editDoctorForm" enctype="multipart/form-data">
                  <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="ri-edit-line me-2"></i>แก้ไขข้อมูลแพทย์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <!-- ฟอร์มแก้ไขจะถูกโหลดผ่าน AJAX -->
                    <div id="editDoctorFormContent" class="text-center">
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
  if ($.fn.DataTable.isDataTable('#doctorsTable')) {
    $('#doctorsTable').DataTable().destroy();
  }

  // ตรวจสอบว่าตารางมีข้อมูลก่อนเริ่ม DataTable
  if ($('#doctorsTable tbody tr').length > 0 && $('#doctorsTable tbody tr td').length > 1) {
    try {
      const doctorsTable = $('#doctorsTable').DataTable({
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
        order: [[4, 'asc']], // เรียงตามลำดับการแสดงผล
        responsive: true,
        columnDefs: [
          { orderable: false, targets: [0, 6] } // ไม่ให้เรียงตามคอลัมน์รูปภาพและปุ่มจัดการ
        ]
      });
      
      // ฟังก์ชันกรองตามสถานะและแพทย์แนะนำ
      function applyFilters() {
        // ล้าง search function ที่อาจมีอยู่เดิม
        $.fn.dataTable.ext.search.pop();
        
        // สร้าง function ใหม่สำหรับกรอง
        $.fn.dataTable.ext.search.push(
          function(settings, data, dataIndex) {
            const $row = $(doctorsTable.row(dataIndex).node());
            const statusFilter = $('#filterStatus').val();
            const featuredFilter = $('#filterFeatured').val();
            
            let showRow = true;
            
            // กรองตามสถานะ
            if (statusFilter && $row.data('status') != statusFilter) {
              showRow = false;
            }
            
            // กรองตามการเป็นแพทย์แนะนำ
            if (featuredFilter !== '' && $row.data('featured') != featuredFilter) {
              showRow = false;
            }
            
            return showRow;
          }
        );
        
        // วาดตารางใหม่พร้อมตัวกรอง
        doctorsTable.draw();
      }
      
      // เมื่อมีการเปลี่ยนแปลงตัวกรอง
      $('#filterStatus, #filterFeatured').change(function() {
        applyFilters();
      });
      
      // ปุ่มล้างตัวกรอง
      $('#clearFilters').click(function() {
        $('#filterStatus').val('');
        $('#filterFeatured').val('');
        $('#searchDoctor').val('');
        doctorsTable.search('').draw();
        
        // ล้าง search function
        $.fn.dataTable.ext.search.pop();
        doctorsTable.draw();
      });
      
      // ค้นหาทั่วไป
      $('#searchDoctor').on('keyup', function() {
        doctorsTable.search(this.value).draw();
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
    const doctorName = $(this).attr('alt');
    
    $('#imagePreviewTitle').text('รูปภาพแพทย์: ' + doctorName);
    $('#largeImage').attr('src', imgSrc);
    $('#imagePreviewModal').modal('show');
  });
  
  // เพิ่มการพรีวิวรูปภาพสำหรับอัปโหลด
  $(document).on('change', '#addDoctorForm [name="image"], #editDoctorForm [name="image"]', function() {
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
            <img src="${e.target.result}" alt="รูปพรีวิว" class="img-thumbnail" style="max-height: 150px; max-width: 150px; border-radius: 50%;">
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
  
  // ดูรายละเอียดแพทย์แบบเร็ว
  $(document).on('click', '.quick-view-btn', function() {
    const id = $(this).data('id');
    
    // ตั้งค่า ID สำหรับปุ่มแก้ไขในโหมดดูเร็ว
    $('#quickViewEditBtn').data('id', id);
    
    // โหลดข้อมูลแพทย์ผ่าน AJAX
    $.ajax({
      url: 'api/frontend-doctor-api.php?action=quick_view',
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
    
    // โหลดข้อมูลแพทย์ผ่าน AJAX
    $.ajax({
      url: 'api/frontend-doctor-api.php?action=get',
      type: 'GET',
      data: { id: id },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // เติมข้อมูลลงในฟอร์มแก้ไข
          $('#editDoctorFormContent').html(response.html);
          $('#editDoctorModal').modal('show');
          
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
  
  // แสดงพรีวิวรูปภาพปัจจุบันในโหมดแก้ไข
  function initEditImagePreview() {
    // เพิ่มพรีวิวสำหรับรูปภาพปัจจุบัน
    const currentImagePath = $('#current_image_path').val();
    
    if (currentImagePath) {
      const imgUrl = "../img/doctors/" + currentImagePath;
      
      $('#edit_image_preview').html(`
        <div class="alert alert-info mt-2 mb-0">
          <div class="d-flex align-items-center">
            <div style="width: 80px; height: 80px; overflow: hidden; border-radius: 50%; margin-right: 15px;">
              <img src="${imgUrl}" alt="รูปภาพปัจจุบัน" class="img-fluid" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div>
              <strong>รูปภาพปัจจุบัน</strong><br>
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
          url: 'api/frontend-doctor-api.php?action=delete',
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
      title: `${statusLabel}แพทย์ท่านนี้?`,
      text: currentStatus == 1 ? "แพทย์ท่านนี้จะไม่แสดงบนเว็บไซต์หลัก" : "แพทย์ท่านนี้จะแสดงบนเว็บไซต์หลัก",
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
          url: 'api/frontend-doctor-api.php?action=toggle_status',
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
  
  // เปลี่ยนสถานะแพทย์แนะนำ
  $(document).on('click', '.toggle-featured-btn', function() {
    const id = $(this).data('id');
    const currentFeatured = $(this).data('current');
    const newFeatured = currentFeatured == 1 ? 0 : 1;
    const featuredLabel = newFeatured == 1 ? 'ตั้งเป็นแพทย์แนะนำ' : 'ยกเลิกการแนะนำ';
    
    Swal.fire({
      title: `${featuredLabel}?`,
      text: currentFeatured == 1 ? "แพทย์ท่านนี้จะไม่แสดงเป็นแพทย์แนะนำบนเว็บไซต์หลัก" : "แพทย์ท่านนี้จะแสดงเป็นแพทย์แนะนำบนเว็บไซต์หลัก",
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
          url: 'api/frontend-doctor-api.php?action=toggle_featured',
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
  
  // ฟังก์ชันสำหรับการส่งฟอร์มเพิ่มแพทย์
  $('#addDoctorForm').submit(function(e) {
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
      url: 'api/frontend-doctor-api.php?action=add',
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
            $('#addDoctorForm')[0].reset();
            $('#addDoctorModal').modal('hide');
            
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
  
  // ฟังก์ชันสำหรับการส่งฟอร์มแก้ไขแพทย์
  $('#editDoctorForm').submit(function(e) {
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
      url: 'api/frontend-doctor-api.php?action=update',
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
            $('#editDoctorModal').modal('hide');
            
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
});
    </script>
  </body>
</html>