<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['users_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['users_id'];

// Fetch user data
$query = "SELECT * FROM customer WHERE cus_id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_object($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cus_id_card_number = $_POST['cus_id_card_number'];
    $cus_birthday = $_POST['cus_birthday'];
    $cus_firstname = $_POST['cus_firstname'];
    $cus_lastname = $_POST['cus_lastname'];
    $cus_title = $_POST['cus_title'];
    $cus_gender = $_POST['cus_gender'];
    $cus_nickname = $_POST['cus_nickname'];
    $cus_email = $_POST['cus_email'];
    $cus_blood = $_POST['cus_blood'];
    $cus_tel = $_POST['cus_tel'];
    $cus_drugallergy = $_POST['cus_drugallergy'];
    $cus_congenital = $_POST['cus_congenital'];
    $cus_remark = $_POST['cus_remark'];
    $cus_address = $_POST['cus_address'];
    $cus_district = $_POST['cus_district'];
    $cus_city = $_POST['cus_city'];
    $cus_province = $_POST['cus_province'];
    $cus_postal_code = $_POST['cus_postal_code'];

    $update_query = "UPDATE customer SET 
        cus_id_card_number = '$cus_id_card_number',
        cus_birthday = '$cus_birthday',
        cus_firstname = '$cus_firstname',
        cus_lastname = '$cus_lastname',
        cus_title = '$cus_title',
        cus_gender = '$cus_gender',
        cus_nickname = '$cus_nickname',
        cus_email = '$cus_email',
        cus_blood = '$cus_blood',
        cus_tel = '$cus_tel',
        cus_drugallergy = '$cus_drugallergy',
        cus_congenital = '$cus_congenital',
        cus_remark = '$cus_remark',
        cus_address = '$cus_address',
        cus_district = '$cus_district',
        cus_city = '$cus_city',
        cus_province = '$cus_province',
        cus_postal_code = '$cus_postal_code'
        WHERE cus_id = '$user_id'";

    $update_result = mysqli_query($conn, $update_query);

    if ($update_result) {
        $_SESSION['msg_ok'] = "Profile updated successfully.";
    } else {
        $_SESSION['msg_error'] = "Error updating profile: " . mysqli_error($conn);
    }

    header('Location: user-profile.php');
    exit;
}
?>

<!doctype html>

<html
  lang="en"
  class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../../assets/"
  data-template="vertical-menu-template-no-customizer-starter"
  data-style="light">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>User Profile - D Care Clinic System</title>

    <meta name="description" content="" />

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
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        
        <?php include 'menu.php'; ?>

        <!-- Layout container -->
        <div class="layout-page">
          
          <?php include 'navbar.php'; ?>

          <!-- Content wrapper -->
          <div class="content-wrapper">
<!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">Profile</h4>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <h5 class="card-header">Profile Details</h5>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="cus_id_card_number" class="form-label  text-danger">หมายเลขบัตรประชาชน</label>
                                    <input class="form-control" type="text" id="cus_id_card_number" name="cus_id_card_number" value="<?php echo htmlspecialchars($user->cus_id_card_number); ?>" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="cus_birthday" class="form-label text-danger">วันเกิด</label>
                                    <input class="form-control" type="date" id="cus_birthday" name="cus_birthday" value="<?php echo htmlspecialchars($user->cus_birthday); ?>" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="cus_firstname" class="form-label text-danger">ชื่อ</label>
                                    <input class="form-control" type="text" id="cus_firstname" name="cus_firstname" value="<?php echo htmlspecialchars($user->cus_firstname); ?>" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="cus_lastname" class="form-label text-danger">นามสกุล</label>
                                    <input class="form-control" type="text" id="cus_lastname" name="cus_lastname" value="<?php echo htmlspecialchars($user->cus_lastname); ?>" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="cus_title" class="form-label text-danger">คำนำหน้า</label>
                                    <select class="form-select" id="cus_title" name="cus_title" required>
                                        <option value="" disabled selected>โปรดเลือก</option>
                                        <option value="นาย" <?= ($user->cus_title == 'นาย') ? 'selected' : '' ?>>นาย</option>
                                        <option value="นาง" <?= ($user->cus_title == 'นาง') ? 'selected' : '' ?>>นาง</option>
                                        <option value="นางสาว" <?= ($user->cus_title == 'นางสาว') ? 'selected' : '' ?>>นางสาว</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="cus_gender" class="form-label text-danger">เพศ</label>
                                    <select class="form-select" id="cus_gender" name="cus_gender" required>
                                        <option value="" disabled selected>โปรดเลือก</option>
                                        <option value="ชาย" <?= ($user->cus_gender == 'ชาย') ? 'selected' : '' ?>>ชาย</option>
                                        <option value="หญิง" <?= ($user->cus_gender == 'หญิง') ? 'selected' : '' ?>>หญิง</option>
                                    </select>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="cus_tel" class="form-label">เบอร์โทร</label>
                                    <input class="form-control" type="text" id="cus_tel" name="cus_tel" value="<?php echo htmlspecialchars($user->cus_tel); ?>" required />
                                </div>
                                <button class="btn btn-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseWidthExample" aria-expanded="false" aria-controls="collapseWidthExample">
                                ข้อมูลเพิ่มเติม (ไม่บังคับ)
                                </button>
                                <div style="min-height: 120px;">
                                  <div class="collapse collapse-horizontal" id="collapseWidthExample">
                                    <div class="row">
                                        
                                        <div class="mb-3 col-md-6">
                                            <label for="cus_nickname" class="form-label">ชื่อเล่น</label>
                                            <input class="form-control" type="text" id="cus_nickname" name="cus_nickname" value="<?php echo htmlspecialchars($user->cus_nickname); ?>" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="cus_email" class="form-label">Email</label>
                                            <input class="form-control" type="email" id="cus_email" name="cus_email" value="<?php echo htmlspecialchars($user->cus_email); ?>" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="cus_blood" class="form-label">กรุ๊ปเลือด</label>
                                            <select class="form-select" id="cus_blood" name="cus_blood">
                                                <option value="" disabled selected>โปรดเลือก</option>
                                                <option value="A+" <?= ($user->cus_blood == 'A+') ? 'selected' : '' ?>>A+</option>
                                                <option value="A-" <?= ($user->cus_blood == 'A-') ? 'selected' : '' ?>>A-</option>
                                                <option value="B+" <?= ($user->cus_blood == 'B+') ? 'selected' : '' ?>>B+</option>
                                                <option value="B-" <?= ($user->cus_blood == 'B-') ? 'selected' : '' ?>>B-</option>
                                                <option value="O+" <?= ($user->cus_blood == 'O+') ? 'selected' : '' ?>>O+</option>
                                                <option value="O-" <?= ($user->cus_blood == 'O-') ? 'selected' : '' ?>>O-</option>
                                                <option value="AB+" <?= ($user->cus_blood == 'AB+') ? 'selected' : '' ?>>AB+</option>
                                                <option value="AB-" <?= ($user->cus_blood == 'AB-') ? 'selected' : '' ?>>AB-</option>
                                            </select>
                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <label for="cus_drugallergy" class="form-label">ประวัติการแพ้ยา</label>
                                            <textarea class="form-control" id="cus_drugallergy" name="cus_drugallergy"><?php echo htmlspecialchars($user->cus_drugallergy); ?></textarea>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="cus_congenital" class="form-label">โรคประจำตัว</label>
                                            <textarea class="form-control" id="cus_congenital" name="cus_congenital"><?php echo htmlspecialchars($user->cus_congenital); ?></textarea>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label for="cus_remark" class="form-label">หมายเหตุ</label>
                                            <textarea class="form-control" id="cus_remark" name="cus_remark"><?php echo htmlspecialchars($user->cus_remark); ?></textarea>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label for="cus_address" class="form-label">ที่อยู่</label>
                                            <input class="form-control" type="text" id="cus_address" name="cus_address" value="<?php echo htmlspecialchars($user->cus_address); ?>" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="cus_district" class="form-label">ตำบล</label>
                                            <input class="form-control" type="text" id="cus_district" name="cus_district" value="<?php echo htmlspecialchars($user->cus_district); ?>"  />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="cus_city" class="form-label">อำเภอ</label>
                                            <input class="form-control" type="text" id="cus_city" name="cus_city" value="<?php echo htmlspecialchars($user->cus_city); ?>"  />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="cus_province" class="form-label">จังหวัด</label>
                                            <input class="form-control" type="text" id="cus_province" name="cus_province" value="<?php echo htmlspecialchars($user->cus_province); ?>"  />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="cus_postal_code" class="form-label">รหัสไปรษณีย์</label>
                                            <input class="form-control" type="text" id="cus_postal_code" name="cus_postal_code" value="<?php echo htmlspecialchars($user->cus_postal_code); ?>"  />
                                        </div>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Save changes</button>
                                <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    <!-- / Content -->

            <?php   include 'footer.php'; ?>

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

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
    <script>
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

    // msg info 
    <?php if(isset($_SESSION['msg_info'])){ ?>
      Swal.fire({
         icon: 'info',
         title: 'แจ้งเตือน!!',
         text: '<?php echo $_SESSION['msg_info']; ?>',
         customClass: {
              confirmButton: 'btn btn-primary waves-effect waves-light'
            },
         buttonsStyling: false

      })
    <?php unset($_SESSION['msg_info']); } ?>
        
    </script>
  </body>
</html>
