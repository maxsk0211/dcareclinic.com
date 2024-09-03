<?php 
// escape ตัวแปรเพื่อป้องกัน SQL injection
$user_id = mysqli_real_escape_string($conn, $_SESSION['users_id']);

// สร้างคำสั่ง SQL โดยใช้ mysqli_real_escape_string
$sql_users = "SELECT * FROM customer WHERE cus_id = '$user_id'";

// ดำเนินการ query
$result_users = $conn->query($sql_users);
$row_users=mysqli_fetch_object($result_users);

if ($row_users->line_user_id!=null) {
    $profile_pic=$row_users->line_picture_url;
    $profile_name=$row_users->line_display_name;
}else{
    $profile_pic="../img/customer/".$row_users->cus_image;
    $profile_name=$row_users->cus_firstname." ".$row_users->cus_lastname;
}

 ?>


<!-- Navbar -->

          <nav
            class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
            id="layout-navbar">
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                <i class="ri-menu-fill ri-22px"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      <img src="<?= $profile_pic; ?>" alt class="rounded-circle" />
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-2">
                            <div class="avatar avatar-online">
                              <img src="<?= $profile_pic; ?>" alt class="rounded-circle" />
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-medium d-block"><?= $profile_name; ?></span>
                            <small class="text-muted">Line</small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>

                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>

                      <a class="dropdown-item btn btn-danger mx-2" href="../logout.php">
                        <i class="ri-shut-down-line me-3"></i>
                        <span class="align-middle">Log Out</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ User -->
              </ul>
            </div>
          </nav>

          <!-- / Navbar -->