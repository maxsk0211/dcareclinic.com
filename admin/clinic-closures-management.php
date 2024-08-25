<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

// ดึงข้อมูลวันที่ปิดทำการจากฐานข้อมูล
$sql = "SELECT * FROM clinic_closures ORDER BY closure_date DESC";
$result = $conn->query($sql);
$clinic_closures = $result->fetch_all(MYSQLI_ASSOC);

// ฟังก์ชันสำหรับจัดการการเพิ่มหรือแก้ไขข้อมูล
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $closure_date = $_POST['closure_date'];
    $reason = $_POST['reason'];

    // แปลงวันที่จาก พ.ศ. เป็น ค.ศ.
    $closure_date_obj = DateTime::createFromFormat('d/m/Y', $closure_date);
    if ($closure_date_obj) {
        $closure_date_obj->modify('-543 years');
        $closure_date_sql = $closure_date_obj->format('Y-m-d');
    } else {
        $_SESSION['msg_error'] = "รูปแบบวันที่ปิดทำการไม่ถูกต้อง";
        header("Location: clinic-closures-management.php");
        exit();
    }

    if (isset($_POST['closure_id'])) {
        // แก้ไขข้อมูล
        $closure_id = $_POST['closure_id'];
        
        // ตรวจสอบว่าวันที่นี้มีอยู่แล้วหรือไม่ (ยกเว้นรายการที่กำลังแก้ไข)
        $check_sql = "SELECT id FROM clinic_closures WHERE closure_date = ? AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $closure_date_sql, $closure_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $_SESSION['msg_error'] = "วันที่ปิดทำการนี้มีอยู่ในระบบแล้ว";
            header("Location: clinic-closures-management.php");
            exit();
        }
        
        $sql = "UPDATE clinic_closures SET closure_date = ?, reason = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $closure_date_sql, $reason, $closure_id);
    } else {
        // เพิ่มข้อมูลใหม่
        // ตรวจสอบว่าวันที่นี้มีอยู่แล้วหรือไม่
        $check_sql = "SELECT id FROM clinic_closures WHERE closure_date = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $closure_date_sql);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $_SESSION['msg_error'] = "วันที่ปิดทำการนี้มีอยู่ในระบบแล้ว";
            header("Location: clinic-closures-management.php");
            exit();
        }
        
        $sql = "INSERT INTO clinic_closures (closure_date, reason) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $closure_date_sql, $reason);
    }

    if ($stmt->execute()) {
        $_SESSION['msg_ok'] = "บันทึกข้อมูลสำเร็จ";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
    }
    $stmt->close();
    header("Location: clinic-closures-management.php");
    exit();
}

// ลบข้อมูล
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM clinic_closures WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $_SESSION['msg_ok'] = "ลบข้อมูลสำเร็จ";
    } else {
        $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . $stmt->error;
    }
    $stmt->close();
    header("Location: clinic-closures-management.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>จัดการวันที่ปิดทำการคลินิก | dcareclinic.com</title>

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

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
    
    <!-- sweet Alerts 2 -->
    <link rel="stylesheet" href="../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/sweetalert2/sweetalert2.css" />
<style>
    .table {
        font-size: 16px; /* เพิ่มขนาดฟอนต์พื้นฐาน */
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 18px; /* เพิ่มขนาดฟอนต์สำหรับหัวตาราง */
    }
    .table td {
        vertical-align: middle;
        padding: 12px 15px; /* เพิ่ม padding เพื่อให้เซลล์ใหญ่ขึ้น */
    }
    .table .fw-bold {
        font-weight: 600 !important;
    }
    .table .text-warning:hover, .table .text-danger:hover {
        opacity: 0.7;
    }
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid rgba(0,0,0,.125);
        padding: 20px 25px; /* เพิ่ม padding สำหรับ card header */
    }
    .card-body {
        padding: 25px; /* เพิ่ม padding สำหรับ card body */
    }
    .btn {
        font-size: 16px; /* เพิ่มขนาดฟอนต์สำหรับปุ่ม */
        padding: 10px 20px; /* ปรับขนาดปุ่ม */
    }
    .ri-lg {
        font-size: 1.5em; /* เพิ่มขนาดไอคอน */
    }
</style>
</head>

<body>
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">
            <?php include 'navbar.php'; ?>

            <div class="layout-page">
                <div class="content-wrapper">
                    <?php include 'menu.php'; ?>

                    <div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">จัดการวันที่ปิดทำการคลินิก</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClosureModal">
                        <i class="ri-add-line me-1"></i> เพิ่มวันที่ปิดทำการ
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" >ลำดับ</th>
                                    <th>วันที่ปิดทำการ (พ.ศ.)</th>
                                    <th>เหตุผล</th>
                                    <th class="text-center" >การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                foreach ($clinic_closures as $closure): 
                                    $closure_date_obj = new DateTime($closure['closure_date']);
                                    $closure_date_obj->modify('+543 years');
                                    $closure_date_thai = $closure_date_obj->format('d/m/Y');
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td class="fw-bold"><?php echo $closure_date_thai; ?></td>
                                    <td><?php echo $closure['reason']; ?></td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editClosureModal<?php echo $closure['id']; ?>" title="แก้ไข">
                                            <i class="ri-edit-box-line ri-lg"></i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-danger" onClick="confirmDelete('clinic-closures-management.php?delete_id=<?php echo $closure['id']; ?>'); return false;" title="ลบ">
                                            <i class="ri-delete-bin-6-line ri-lg"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if (empty($clinic_closures)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="ri-information-line ri-3x mb-3"></i>
                        <p class="fs-5">ไม่พบข้อมูลวันที่ปิดทำการ</p>
                    </div>
                    <?php endif; ?>
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

    <!-- Modal สำหรับเพิ่มข้อมูล -->
    <div class="modal fade" id="addClosureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มวันที่ปิดทำการ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="closure_date" class="form-label">วันที่ปิดทำการ (พ.ศ.)</label>
                            <input type="text" class="form-control date-mask" id="closure_date" name="closure_date" required placeholder="dd/mm/yyyy">
                        </div>
                        <div class="mb-3">
                            <label for="reason" class="form-label">เหตุผล</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal สำหรับแก้ไขข้อมูล -->
    <?php foreach ($clinic_closures as $closure): 
    $closure_date_obj = new DateTime($closure['closure_date']);
    $closure_date_obj->modify('+543 years');
    $closure_date_thai = $closure_date_obj->format('d/m/Y');
?>
<div class="modal fade" id="editClosureModal<?php echo $closure['id']; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">แก้ไขวันที่ปิดทำการ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="post">
                    <input type="hidden" name="closure_id" value="<?php echo $closure['id']; ?>">
                    <div class="mb-3">
                        <label for="closure_date" class="form-label">วันที่ปิดทำการ (พ.ศ.)</label>
                        <input type="text" class="form-control date-mask" id="closure_date" name="closure_date" 
                               value="<?php echo $closure_date_thai; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">เหตุผล</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required><?php echo $closure['reason']; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

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

        document.addEventListener('DOMContentLoaded', function() {
            var dateInputs = document.querySelectorAll('.date-mask');
            dateInputs.forEach(function(input) {
                new Cleave(input, {
                    date: true,
                    delimiter: '/',
                    datePattern: ['d', 'm', 'Y']
                });
            });
        });

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
    </script>
</body>
</html>