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
          $formattedId = 'C-' . str_pad($idString, 6, '0', STR_PAD_LEFT);

          return $formattedId;
      }


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
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>รายละเอียดคอร์ส | dcareclinic.com</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/sweetalert2/sweetalert2.css" />

    <!-- Page CSS -->
    <style>
        .course-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .course-details .form-control, .course-details .form-select {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
        }
        .course-details label {
            font-weight: 600;
            color: #566a7f;
        }
    </style>

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
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
                                <h4 class="mb-0">รายละเอียดคอร์ส</h4>
                                <a href="course.php" class="btn btn-secondary">
                                    <i class="ri-arrow-left-line me-1"></i> ย้อนกลับ
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-5 d-flex justify-content-center align-items-start">
                                        <img src="../../img/course/<?= $row->course_pic ?>" alt="รูปภาพคอร์ส" class="course-image">
                                    </div>
                                    <div class="col-md-7 course-details">
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <label class="form-label">รหัส</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" value="<?= formatId($row->course_id); ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <label class="form-label">ชื่อคอร์ส</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" value="<?= $row->course_name ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <label class="form-label">รายละเอียดคอร์ส</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <textarea class="form-control" rows="3" readonly><?= $row->course_detail ?></textarea>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <label class="form-label">ประเภท</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" value="<?= $course_type_name ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <label class="form-label">จำนวน</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" value="<?= $row->course_amount ?>" readonly>
                                                    <span class="input-group-text">ครั้ง</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <label class="form-label">ราคา</label>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" value="<?= $row->course_price ?>" readonly>
                                                    <span class="input-group-text">บาท</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <label class="form-label">วันที่เริ่ม</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" value="<?= date('d/m/Y', strtotime($row->course_start) + 543 * 365 * 24 * 60 * 60) ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <label class="form-label">วันที่สิ้นสุด</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" value="<?= date('d/m/Y', strtotime($row->course_end) + 543 * 365 * 24 * 60 * 60) ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <label class="form-label">สถานะ</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <?php if ($row->course_status == 1): ?>
                                                    <span class="badge bg-success">พร้อมใช้งาน</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">ไม่พร้อมใช้งาน</span>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-4">
                                                <label class="form-label">หมายเหตุ</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <textarea class="form-control" rows="3" readonly><?= $row->course_note ?></textarea>
                                            </div>
                                        </div>
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

    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="../assets/vendor/libs/cleavejs/cleave.js"></script>
    <script src="../assets/vendor/libs/cleavejs/cleave-phone.js"></script>









    <script>

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
          $('#coursesTable').DataTable({
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
</body>
</html>