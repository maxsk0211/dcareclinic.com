<?php 
  session_start();
  
  include 'chk-session.php';
  require '../dbcon.php';
  // เพิ่มฟังก์ชันนี้ก่อนส่วนของ HTML
function formatHN($id) {
    return 'HN-' . str_pad($id, 5, '0', STR_PAD_LEFT);
}
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

    <title>จัดการลูกค้า | dcareclinic.com</title>

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

  .btn-primary {
    background-color: #4e73df;
    border: none;
    transition: all 0.3s ease;
  }

  .btn-primary:hover {
    background-color: #2e59d9;
    transform: translateY(-2px);
  }

  .table {
    border-collapse: separate;
    border-spacing: 0 15px;
  }

  .table thead th {
    border-bottom: none;
    background-color: #f1f3f9;
    color: #4e73df;
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 1px;
  }

  .table tbody tr {
    background-color: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
  }

  .table tbody tr:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  }

  .table td, .table th {
    vertical-align: middle;
    border: none;
    padding: 15px;
  }

  .badge {
    padding: 8px 12px;
    font-size: 0.8rem;
    border-radius: 30px;
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

  .form-control, .form-select {
    border-radius: 10px;
    border: 1px solid #e0e0e0;
    padding: 12px 15px;
  }

  .form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.25);
    border-color: #4e73df;
  }
    /* เพิ่มต่อจาก CSS เดิม */
    .clickable-row {
        transition: background-color 0.3s;
    }
    
    .clickable-row:hover {
        background-color: rgba(78,115,223,0.1) !important;
    }
    
    .clickable-row td:not(:first-child) {
        cursor: pointer;
    }
    /* เพิ่มต่อจาก style เดิม */
.img-upload-preview {
    max-width: 150px;
    height: auto;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    margin-bottom: 10px;
}

.img-upload-container {
    position: relative;
    margin-bottom: 15px;
}

.img-upload-container .remove-image {
    position: absolute;
    top: -10px;
    right: -10px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    padding: 5px;
    cursor: pointer;
    font-size: 12px;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
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


            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title text-white">ประวัติการเปลี่ยนแปลงข้อมูลลูกค้า</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="logsTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>วันที่</th>
                                        <th>ผู้ดำเนินการ</th>
                                        <th>การกระทำ</th>
                                        <th>รหัสลูกค้า</th>
                                        <th>รายละเอียด</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT l.*, u.users_fname, u.users_lname 
                                           FROM activity_logs l 
                                           JOIN users u ON l.user_id = u.users_id 
                                           WHERE l.entity_type = 'customer' 
                                           ORDER BY l.created_at DESC";
                                    $result = $conn->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        $details = json_decode($row['details'], true);
                                    ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i:s', strtotime($row['created_at'])) ?></td>
                                        <td><?= $row['users_fname'] . ' ' . $row['users_lname'] ?></td>
                                        <td><?= $row['action'] ?></td>
                                        <td><?= formatHN($row['entity_id']) ?></td>
                                        <td>
                                            <?php
                                            if ($row['action'] == 'delete') {
                                                echo "เหตุผล: " . ($details['reason'] ?? 'ไม่ระบุ');
                                            } else {
                                                // แสดงข้อมูลที่มีการเปลี่ยนแปลง
                                                if (isset($details['changes'])) {
                                                    foreach ($details['changes'] as $field => $change) {
                                                        echo "$field: {$change['old']} -> {$change['new']}<br>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

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
            $('#logsTable').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 25,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json"
                }
            });
        });
    </script>



  </body>
</html>
