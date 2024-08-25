  <?php 
      // สาขา
      $branch_id=$_SESSION['branch_id'];
      $sql_branch = "SELECT * FROM branch where branch.branch_id='$branch_id'";
      $result_branch = $conn->query($sql_branch);
      $row_branch = $result_branch->fetch_object();

   ?>

<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="container-xxl">
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-6">
      <a href="index.php" class="app-brand-link gap-2 d-flex align-items-center">
        <span class="app-brand-logo">
          <span style="color: var(--bs-primary)">
            <img src="../img/d.png" width="40px" height="40px">
          </span>
        </span>
        <span class="app-brand-text text-heading fw-semibold d-sm-block">Care Clinic System</span>
        <div class="ms-auto d-none d-xl-block"> <span class="alert bg-primary text-white p-3" role="alert">สาขา : <?php echo $row_branch->branch_name; ?></span> </div>
      </a>

        


      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
        <i class="ri-close-fill align-middle"></i>
      </a>
    </div>

    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
      <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
        <i class="ri-menu-fill ri-22px"></i>
      </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
      <ul class="navbar-nav flex-row align-items-center ms-auto">
        <li class="nav-item">
          <div class="ms-auto"><span class="alert alert-solid-info  p-3" role="alert"><?php echo $_SESSION['users_fname']." ".$_SESSION['users_lname']; ?></span> </div>
        </li>
        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="avatar avatar-online">
              <img src="../assets/img/avatars/2.png" alt class="rounded-circle" />
            </div>
          </a>

          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="#">
                <div class="d-flex">
                  <div class="flex-shrink-0 me-2">
                    <div class="avatar avatar-online">
                      <img src="../assets/img/avatars/2.png" alt class="rounded-circle" />
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <span class="fw-medium d-block"><?php echo $_SESSION['users_fname']." ".$_SESSION['users_lname']; ?></span>
                    <small class="text-muted"><?php echo $_SESSION['position_name']; ?></small>
                  </div>
                </div>
              </a>
            </li>
            <li>
              <div class="dropdown-divider"></div>
            </li>
            <li>
              <a class="dropdown-item" href="#">
                <i class="ri-user-3-line me-3"></i><span class="align-middle">My Profile</span>
              </a>
            </li>
            <?php if($_SESSION['position_id']==1){ ?>
            <li>
              <a class="dropdown-item" href="branch.php">
                <i class="ri-settings-4-line me-3"></i><span class="align-middle">จัดการสาขา</span>
              </a>
            </li>
            <?php } ?>
            <?php if($_SESSION['position_id']==1 or $_SESSION['position_id']==2){ ?>
            <li>
              <a class="dropdown-item" href="clinic-settings.php">
                <i class="ri-settings-4-line me-3"></i><span class="align-middle">คั้งค่าสาขา</span>
              </a>
            </li>
            <?php } ?>

            <?php if($_SESSION['position_id']==1 or $_SESSION['position_id']==2){ ?>
            <li>
              <a class="dropdown-item" href="users.php">
                <i class="ri-settings-4-line me-3"></i><span class="align-middle">จัดการพนังงาน</span>
              </a>
            </li>
            <?php } ?>
            <li>
              <div class="dropdown-divider"></div>
            </li>
            <?php if($_SESSION['position_id']==1){ ?>
            <li>
              <a class="dropdown-item" href="index.php?branch_out=1">
                <i class="ri-shut-down-line me-3"></i>
                <span class="align-middle">ออกจากสาขา</span>
              </a>
            </li>
              <?php } ?>


            <li>
              <div class="d-grid px-4 pt-2 pb-1">
                <a class="btn btn-sm btn-danger d-flex" href="../logout.php">
                  <small class="align-middle">Logout</small>
                  <i class="ri-logout-box-r-line ms-2 ri-16px"></i>
                </a>
              </div>
            </li>
          </ul>
        </li>
        <!--/ User -->
      </ul>
    </div>
  </div>
</nav>