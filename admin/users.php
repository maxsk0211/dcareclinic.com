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

    <title>จัดการผู้ใช้งาน | dcareclinic.com</title>

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



<style>
    body {
        background-color: #f8f9fa;
    }
    .container-xxl {
        animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
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
    .card-title {
        margin-bottom: 0;
        font-weight: 600;
        font-size: 1.25rem;
    }
    .card-body {
        padding: 30px;
    }
    .btn {
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
        transform: translateY(-2px);
    }
    .table-responsive {
        border-radius: 15px;
        overflow: hidden;
    }
    .table {
        margin-bottom: 0;
    }
    .table thead th {
        background-color: #4e73df;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        padding: 15px;
    }
    .table tbody tr {
        transition: all 0.3s ease;
    }
    .table tbody tr:nth-of-type(even) {
        background-color: #f8f9fa;
    }
    .table tbody tr:hover {
        background-color: #e8eaf6;
        transform: scale(1.01);
    }
    .table td {
        vertical-align: middle;
        border: none;
        padding: 15px;
    }
    .badge {
        padding: 8px 12px;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 30px;
    }
    .badge-success {
        background-color: #1cc88a;
        color: white;
    }
    .badge-danger {
        background-color: #e74a3b;
        color: white;
    }
    .text-primary, .text-danger {
        transition: all 0.3s ease;
    }
    .text-primary:hover, .text-danger:hover {
        opacity: 0.8;
        transform: scale(1.1);
    }
    .modal-content {
        border-radius: 15px;
        box-shadow: 0 0 30px rgba(0,0,0,0.1);
    }
    .modal-header {
        background-color: #4e73df;
        color: white;
        border-radius: 15px 15px 0 0;
    }
    .modal-title {
        font-weight: 600;
    }
    .modal-footer {
        border-top: none;
    }
    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #ced4da;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.25);
        border-color: #4e73df;
    }
</style>
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
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-white">ข้อมูลผู้ใช้งานในระบบทั้งหมด</h5>
                        <div>
                            <button type="button" class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#managePositionModal">
                                <i class="ri-settings-line me-1"></i> จัดการตำแหน่ง
                            </button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="ri-user-add-line me-1"></i> เพิ่มผู้ใช้งาน
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-white">เพิ่มผู้ใช้งาน</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="sql/users-insert.php" method="post">
                                    <?php if ($_SESSION['position_id']==1): ?>
                                      <?php 

                                      $sql_branch = "SELECT branch_id, branch_name FROM branch";
                                      $result_branch = $conn->query($sql_branch);
                                      
                                       ?>
                                       <label class="form-label" for="branchDropdown">เลือกสาขา</label>
                                       <select class="form-select border-primary mb-3" id="branchDropdown" required name="branch_id">
                                        <option disabled selected value="" >โปรดเลือก</option>
                                        <?php while ($row_branch = $result_branch->fetch_object()) {
                                          echo "<option value='" . $row_branch->branch_id . "'>" . $row_branch->branch_name . "</option>";
                                        } ?>
                                      </select>
                                    <?php else :  ?>

                                       <div class="alert alert-solid-danger text-center h3">ชื่อสาขา : <?php echo $row_branch->branch_name; ?></div>
                                       <input type="hidden" name="branch_id" value="<?php echo $row_branch->branch_id; ?>">

                                    <?php endif ?>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="users_username" class="form-label">ชื่อผู้ใช้งาน</label>
                                            <input type="text" class="form-control" id="users_username" name="users_username" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="users_password" class="form-label">รหัสผ่าน</label>
                                            <input type="password" class="form-control" id="users_password" name="users_password" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="users_fname" class="form-label">ชื่อ</label>
                                            <input type="text" class="form-control" id="users_fname" name="users_fname" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="users_lname" class="form-label">นามสกุล</label>
                                            <input type="text" class="form-control" id="users_lname" name="users_lname" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="users_nickname" class="form-label">ชื่อเล่น</label>
                                            <input type="text" class="form-control" id="users_nickname" name="users_nickname" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="users_tel" class="form-label">เบอร์โทร</label>
                                            <input type="tel" class="form-control" id="users_tel" name="users_tel" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                              <script>
                                                function createLabelAndInput() {
                                                    var dropdown = document.getElementById("position");
                                                    var container = document.getElementById("inputContainer");
                                                    var branchDropdown = document.getElementById("branchDropdown"); // เพิ่มการอ้างอิงถึง dropdown สาขา
                                                    
                                                    var selectedValue = dropdown.value;
                                                    
                                                    // จัดการการแสดง/ซ่อนช่องใบประกอบวิชาชีพ
                                                    if (selectedValue === "3" || selectedValue === "4") {
                                                        if (container) {
                                                            var label = document.createElement("label");
                                                            label.textContent = "ใบประกอบวิชาชีพ";
                                                            label.setAttribute("for", "license");
                                                            label.classList.add("form-label"); 

                                                            var input = document.createElement("input");
                                                            input.type = "text";
                                                            input.classList.add("form-control");
                                                            input.classList.add("border-primary");
                                                            input.id = "license";
                                                            input.name = "users_license";
                                                            input.value = "<?php if(isset($_SESSION['chk_users_license'])){echo $_SESSION['chk_users_license'];} ?>";

                                                            container.innerHTML = ""; 
                                                            container.appendChild(label);
                                                            container.appendChild(input);
                                                        }
                                                    } else {
                                                        if (container) {
                                                            container.innerHTML = "";
                                                        }
                                                    }

                                                    // จัดการการแสดง/ซ่อน dropdown สาขา
                                                    if (branchDropdown) {
                                                        if (selectedValue === "1") { // ถ้าเลือกผู้ดูแลระบบ
                                                            branchDropdown.disabled = true;
                                                            branchDropdown.value = ""; // เคลียร์ค่าที่เลือก
                                                            // เพิ่ม input hidden เพื่อส่งค่าว่าง
                                                            var hiddenInput = document.createElement("input");
                                                            hiddenInput.type = "hidden";
                                                            hiddenInput.name = "branch_id";
                                                            hiddenInput.value = "0";
                                                            branchDropdown.parentNode.appendChild(hiddenInput);
                                                        } else {
                                                            branchDropdown.disabled = false;
                                                            // ลบ input hidden ถ้ามี
                                                            var hiddenInput = branchDropdown.parentNode.querySelector('input[type="hidden"][name="branch_id"]');
                                                            if (hiddenInput) {
                                                                hiddenInput.remove();
                                                            }
                                                        }
                                                    }
                                                }
                                                </script>
                                            <label for="position_id" class="form-label">ตำแหน่ง</label>
                                            <?php 
                                             if($_SESSION['position_id']==1){
                                                 $sql_position="SELECT * FROM position";
                                             }else{
                                                $sql_position="SELECT * FROM position WHERE position_name!='ผู้ดูแลระบบ'and position_name!='ผู้จัดการคลินิก'";
                                             }
                                            $result_position=mysqli_query($conn,$sql_position);

                                             ?>
                                              <select id="position" name="position_id" required class="form-select border-primary" onchange="createLabelAndInput()">
                                                <option value="" disabled selected>โปรดเลือก</option>
                                            <?php while ($row_position=mysqli_fetch_object($result_position)) { ?>
                                                <option value="<?php echo $row_position->position_id; ?>"><?php echo $row_position->position_name; ?></option>
                                            <?php } ?>
                                              </select>
                                        </div>
                                        <div class="col-md-6" id="inputContainer">
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                        <button type="submit" class="btn btn-primary">บันทึก</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="usersTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">คำสั่ง</th>
                                        <th class="text-center">#</th>
                                        <th>ผู้ใช้งาน</th>
                                        <th>ชื่อ - นามสกุล</th>
                                        <th>ชื่อเล่น</th>
                                        <th>เบอร์โทร</th>
                                        <th>ตำแหน่ง</th>
                                        <th>ใบอนุญาต</th>
                                        <th>สาขา</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i=1;

                                    $position_id=$_SESSION['position_id'];
                                    $branch_id=$_SESSION['branch_id'];
                                    if($position_id==1){
                                        $sql_show_users="SELECT * FROM `users` ORDER BY `users`.`users_id` ASC";
                                    }elseif($position_id==2){
                                      $sql_show_users="SELECT * FROM `users` WHERE branch_id='$branch_id' ORDER BY `users`.`users_id` ASC";
                                    }
                                    $result_show_users=$conn->query($sql_show_users);
                                    while ($row_show_users = $result_show_users->fetch_object()) {
                                    ?>
                                    <tr <?php if($row_show_users->position_id==1){ ?>class="table-danger"<?php }elseif($row_show_users->position_id==2){ ?>class="table-warning"<?php } ?>>
                                <td class="text-center">
                                    <?php if($row_show_users->users_id!=1): ?>
                                    <a href="#" class="text-warning" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row_show_users->users_id; ?>"><i class="ri-edit-box-line"></i></a>
                                    <a href="" class="text-danger" onClick="confirmDelete('sql/user-delete.php?id=<?php echo $row_show_users->users_id; ?>'); return false;"><i class="ri-delete-bin-6-line"></i></a>
                                    <?php endif ?>
                                </td>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td><?php echo $row_show_users->users_username; ?></td>
                                <td><?php echo $row_show_users->users_fname." ".$row_show_users->users_lname; ?></td>
                                <td><?php echo $row_show_users->users_nickname; ?></td>
                                <td><?php echo $row_show_users->users_tel; ?></td>
                                <td>
                                    <?php
                                    $position_id = $row_show_users->position_id;
                                    $sql_position = "SELECT position_name FROM position WHERE position_id = $position_id";
                                    $result_position = $conn->query($sql_position);
                                    $position_name = $result_position->fetch_object()->position_name;
                                    ?>
                                    <?php echo $position_name; ?>
                                </td>
                                <td><?php echo $row_show_users->users_license; ?></td>
                                <td>
                                    <?php
                                    $branch_id = $row_show_users->branch_id;
                                    if ($branch_id) {  // เพิ่มเงื่อนไขตรวจสอบ branch_id
                                        $sql_branch = "SELECT branch_name FROM branch WHERE branch_id = $branch_id";
                                        $result_branch = $conn->query($sql_branch);
                                        if ($result_branch && $result_branch->num_rows > 0) {  // เพิ่มการตรวจสอบผลลัพธ์
                                            $branch_name = $result_branch->fetch_object()->branch_name;
                                            echo $branch_name;
                                        } else {
                                            echo "-";
                                        }
                                    } else {
                                        echo "ทั้งหมด";  // กรณีไม่มี branch_id (เช่น admin)
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                  <?php if ($row_show_users->users_status==1): ?>
                                    <span class="badge bg-success">พร้อมใช้งาน</span>
                                  <?php else: ?>
                                    <span class="badge bg-danger">ไม่พร้อมใช้งาน</span>
                                  <?php endif ?>
                            </tr>

                    <div class="modal fade" id="editModal<?php echo $row_show_users->users_id; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $row_show_users->users_id; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-white" id="editModalLabel<?php echo $row_show_users->users_id; ?>">แก้ไขข้อมูลพนักงาน</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="editForm<?php  echo $row_show_users->users_id; ?>" method="post" action="sql/user-update.php"> 
                                        <input type="hidden" name="users_id" value="<?php echo $row_show_users->users_id; ?>">
                                        <div class="row">
                                          <div class="col-md-6">
                                            <div class="mb-3">
                                              <label for="users_username" class="form-label">Username:</label>
                                              <input type="text" class="form-control" name="users_username" value="<?php echo $row_show_users->users_username; ?>" required readonly>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="users_password" class="form-label">Password:</label>
                                                <input type="password" class="form-control" name="users_password" value="<?php echo $row_show_users->users_password; ?>" required>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="users_fname" class="form-label">ชื่อ:</label>
                                                <input type="text" class="form-control" name="users_fname" value="<?php echo $row_show_users->users_fname; ?>" required>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="users_lname" class="form-label">นามสกุล:</label>
                                                <input type="text" class="form-control" name="users_lname" value="<?php echo $row_show_users->users_lname; ?>" required>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="users_nickname" class="form-label">ชื่อเล่น:</label>
                                                <input type="text" class="form-control"  name="users_nickname" value="<?php echo $row_show_users->users_nickname; ?>" required>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="users_tel" class="form-label">เบอร์โทร:</label>
                                                <input type="tel" class="form-control"  name="users_tel" value="<?php echo $row_show_users->users_tel; ?>" required>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="position_id" class="form-label">ตำแหน่ง:</label>
                                                <select class="form-select" name="position_id" required>
                                                    <?php
                                                     if($position_id==1){
                                                         $sql_position="SELECT * FROM position";
                                                     }else{
                                                        $sql_position="SELECT * FROM position WHERE position_name!='ผู้ดูแลระบบ'and position_name!='ผู้จัดการคลินิก'";
                                                     }
                                                    $result_position=mysqli_query($conn,$sql_position);
                                                    $result_position = $conn->query($sql_position);
                                                    while ($row_position = $result_position->fetch_object()) {
                                                        $selected = ($row_position->position_id == $row_show_users->position_id) ? 'selected' : '';
                                                        echo "<option value='" . $row_position->position_id . "' $selected>" . $row_position->position_name . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="users_license" class="form-label">ใบอนุญาต:</label>
                                                <input type="text" class="form-control" name="users_license" value="<?php echo $row_show_users->users_license; ?>">
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="branch_id" class="form-label">สาขา:</label>
                                                <select class="form-select" name="branch_id" required>
                                                    <?php
                                                    $branch_id=$_SESSION['branch_id'];
                                                     if($position_id==1){
                                                        $sql_branch = "SELECT * FROM branch";
                                                      }else{
                                                        $sql_branch = "SELECT * FROM branch where branch_id='$branch_id'";
                                                      }

                                                    $result_branch = $conn->query($sql_branch);
                                                    while ($row_branch = $result_branch->fetch_object()) {
                                                        $selected = ($row_branch->branch_id == $row_show_users->branch_id) ? 'selected' : '';
                                                        echo "<option value='" . $row_branch->branch_id . "' $selected>" . $row_branch->branch_name . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                          </div>
                                          <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="users_status" class="form-label">สถานะ:</label>
                                                <select class="form-select" name="users_status" required>
                                                    <option value="1" <?php if ($row_show_users->users_status == 1) echo 'selected'; ?>>พร้อมใช้งาน</option>
                                                    <option value="0" <?php if ($row_show_users->users_status == 0) echo 'selected'; ?>>ไม่พร้อมใช้งาน</option>
                                                </select>
                                            </div>
                                          </div>

                                        </div>


                                      </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                        <button  type="submit" class="btn btn-primary" form="editForm<?php echo $row_show_users->users_id; ?>">บันทึก</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php } ?>
                </tbody>
            </table>
        </div>
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


<!-- Modal จัดการตำแหน่ง -->
<div class="modal fade" id="managePositionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white">จัดการตำแหน่ง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- ปุ่มเพิ่มตำแหน่งใหม่ -->
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addPositionModal">
                    <i class="ri-add-line me-1"></i> เพิ่มตำแหน่งใหม่
                </button>
                
                <!-- ตารางแสดงตำแหน่งทั้งหมด -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ชื่อตำแหน่ง</th>
                                <th class="text-center">คำสั่ง</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM position ORDER BY position_id ASC";
                            $result = $conn->query($sql);
                            $i = 1;
                            while($row = $result->fetch_object()) {
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $row->position_name; ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm" 
                                            onclick="editPosition(<?php echo $row->position_id; ?>, '<?php echo $row->position_name; ?>')"
                                            <?php if($row->position_id <= 2) echo 'disabled'; ?>>
                                        <i class="ri-edit-line"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="deletePosition(<?php echo $row->position_id; ?>)"
                                            <?php if($row->position_id <= 2) echo 'disabled'; ?>>
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal เพิ่มตำแหน่งใหม่ -->
<div class="modal fade" id="addPositionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">เพิ่มตำแหน่งใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addPositionForm" action="sql/position-insert.php" method="post">
                    <div class="mb-3">
                        <label for="position_name" class="form-label">ชื่อตำแหน่ง:</label>
                        <input type="text" class="form-control" id="position_name" name="position_name" required>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal แก้ไขตำแหน่ง -->
<div class="modal fade" id="editPositionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">แก้ไขตำแหน่ง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editPositionForm" action="sql/position-update.php" method="post">
                    <input type="hidden" id="edit_position_id" name="position_id">
                    <div class="mb-3">
                        <label for="edit_position_name" class="form-label">ชื่อตำแหน่ง:</label>
                        <input type="text" class="form-control" id="edit_position_name" name="position_name" required>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-warning">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


    <!-- Core JS -->
    <!-- sweet Alerts 2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script> -->
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js"></script>



    <script type="text/javascript">

function editPosition(id, name) {
    document.getElementById('edit_position_id').value = id;
    document.getElementById('edit_position_name').value = name;
    
    // ปิด Modal จัดการตำแหน่ง
    $('#managePositionModal').modal('hide');
    // เปิด Modal แก้ไขตำแหน่ง
    $('#editPositionModal').modal('show');
}

function deletePosition(id) {
    Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "การลบตำแหน่งนี้จะไม่สามารถกู้คืนได้!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ใช่, ลบตำแหน่ง!',
        cancelButtonText: 'ยกเลิก',
        customClass: {
            confirmButton: 'btn btn-danger me-1',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'sql/position-delete.php?id=' + id;
        }
    });
}

// Event listener เมื่อปิด Modal แก้ไข ให้เปิด Modal จัดการกลับมา
$('#editPositionModal').on('hidden.bs.modal', function () {
    $('#managePositionModal').modal('show');
});

// Event listener เมื่อปิด Modal เพิ่ม ให้เปิด Modal จัดการกลับมา
$('#addPositionModal').on('hidden.bs.modal', function () {
    $('#managePositionModal').modal('show');
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
    $('#usersTable').DataTable({ 

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
