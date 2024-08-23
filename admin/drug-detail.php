<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

if (isset($_GET['drug_id'])) {
    $drug_id = mysqli_real_escape_string($conn, $_GET['drug_id']);
    $sql = "SELECT d.*, dt.drug_type_name, b.branch_name, u.unit_name 
            FROM drug d
            LEFT JOIN drug_type dt ON d.drug_type_id = dt.drug_type_id
            LEFT JOIN branch b ON d.branch_id = b.branch_id
            LEFT JOIN unit u ON d.drug_unit_id = u.unit_id
            WHERE d.drug_id = '$drug_id'";
    $result = mysqli_query($conn, $sql);
    $drug = mysqli_fetch_object($result);

    if (!$drug) {
        $_SESSION['msg_error'] = "ไม่พบข้อมูลยา";
        header("Location: drug.php");
        exit();
    }
} else {
    $_SESSION['msg_error'] = "ไม่ได้ระบุรหัสยา";
    header("Location: drug.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดยา | <?php echo $drug->drug_name; ?></title>

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
        body { background-color: #f8f9fa; }
        .drug-detail-card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }
        .drug-info-label {
            font-weight: bold;
            color: #6c757d;
        }
        .drug-info-value {
            font-weight: normal;
            color: #212529;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 5px 10px;
        }
        .detail-section {
            background-color: #f1f3f5;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .stock-table {
            font-size: 0.9rem;
        }
        .swal2-container {
          z-index: 1091 !important; /* หรือค่าที่สูงกว่า z-index ของ modal */
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

    <div class="container mt-4">
        <div class="drug-detail-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">คลังยา ประจำสาขา</h2>
                <a href="drug.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> ย้อนกลับ</a>
            </div>
<?php 

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
    $formattedId = 'D-' . str_pad($idString, 6, '0', STR_PAD_LEFT);

    return $formattedId;
}

 ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-section">
                        <p><span class="drug-info-label">รหัส:</span> <span class="drug-info-value"><?php echo formatId($drug->drug_id); ?></span></p>
                        <p><span class="drug-info-label">ชื่อ:</span> <span class="drug-info-value"><?php echo $drug->drug_name; ?></span></p>
                        <p><span class="drug-info-label">กลุ่ม:</span> <span class="drug-info-value"><?php echo $drug->drug_type_name; ?></span></p>
                        <p><span class="drug-info-label">ประเภท:</span> <span class="drug-info-value"><?php echo $drug->drug_properties; ?></span></p>
                        <p><span class="drug-info-label">หมวดหมู่:</span> <span class="drug-info-value"><?php echo $drug->drug_advice; ?></span></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-section">
                        <p><span class="drug-info-label">หน่วยนับ:</span> <span class="drug-info-value"><?php echo $drug->unit_name; ?></span></p>
                        <p><span class="drug-info-label">ราคาต้นทุน:</span> <span class="drug-info-value"><?php echo number_format($drug->drug_cost, 2); ?> บาท</span></p>
                        <p>
                            <span class="drug-info-label">สถานะ:</span>
                            <span class="badge <?php echo $drug->drug_status == 1 ? 'bg-success' : 'bg-danger'; ?> status-badge">
                                <?php echo $drug->drug_status == 1 ? 'พร้อมใช้งาน' : 'ไม่พร้อมใช้งาน'; ?>
                            </span>
                        </p>
                        <p><span class="drug-info-label">รูปภาพ:</span><br>
                            <?php if (!empty($drug->drug_pic)): ?>
                                <img src="../img/drug/<?php echo $drug->drug_pic; ?>" alt="รูปภาพยา" style="max-width: 100px; max-height: 100px;">
                            <?php else: ?>
                                ไม่มีรูปภาพ
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="detail-section">
                        <h5>วิธีใช้</h5>
                        <p><?php echo $drug->drug_advice; ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-section">
                        <h5>ข้อควรระวัง</h5>
                        <p><?php echo $drug->drug_warning; ?></p>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <div class="d-flex justify-content-between">
                    <h5>ข้อมูลสต็อก</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">เพิ่มสต๊อก</button>
                </div>
                <!-- Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addStockModalLabel">เพิ่มสต๊อก</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addStockForm" action="sql/stock-insert.php" method="post">
          <div class="row mb-3">
            <div class="col-md-6">
               <label for="transaction_date" class="form-label">วันที่ทำรายการ</label>
                <input type="text" class="form-control " id="transaction_date" name="transaction_date" readonly >
            </div>
            <div class="col-md-6">
                <div class="form-label">รหัส-ขื่อยา</div>
                <input type="text" class="form-control text-danger fw-bold" value="<?php echo formatId($drug->drug_id)." - ".$drug->drug_name; ?>">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="quantity" class="form-label">จำนวนรับเข้า (<?= $drug->unit_name ?>)</label>
              <div class="input-group">
                <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" required>
                <span class="input-group-text"><?= $drug->unit_name ?></span>
               </div>
            </div>
          
            <div class="col-md-6">
              <label for="cost_per_unit" class="form-label">ต้นทุนต่อหน่วย</label>
              <div class="input-group">
                <input type="number" class="form-control" id="cost_per_unit" name="cost_per_unit" step="0.01" required>
                <span class="input-group-text">บาท</span>
              </div> 
            </div>
           </div>          
          <div class="row mb-3">

            <div class="col-md-6">
              <label for="expiry_date" class="form-label">วันหมดอายุ (พ.ศ.)</label>
              <input type="text" class="form-control date-mask" id="expiry_date" name="expiry_date" placeholder="dd/mm/yyyy">
            </div>
          </div>
          <div class="mb-3">
            <label for="notes" class="form-label">หมายเหตุ</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
          </div>
          <input type="hidden" name="users_id" value="<?= $_SESSION['users_id']; ?>">
          <input type="hidden" name="stock_type" value="drug">
          <input type="hidden" name="related_id" value="<?= $_GET['drug_id']; ?>">
          <input type="hidden" name="branch_id" value="<?= $_SESSION['branch_id']; ?>">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
        <button class="btn btn-primary" data-bs-target="#exampleModal" data-bs-toggle="modal" data-bs-dismiss="modal">บันทึกข้อมูล</button>
      </div>
    </div>
  </div>
</div>



<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">ยืนยันข้อมูล</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          </button>
      </div>
      <div class="modal-body">
        <div class="text-center text-danger h2">การบันทึกข้อมูลไม่สามารถแก้ไขหรือลบได้!</div>
        <div class="text-center text-danger h4">ท่านต้องการยืนยันข้อมูลหรือไม่!</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-target="#addStockModal" data-bs-toggle="modal" data-bs-dismiss="modal">กลับ</button>
        <button type="button" class="btn btn-danger" onclick="submitAddStock()">ยืนยัน</button>
      </div>
    </div>
  </div>
</div>


                <table class="table table-striped table-bordered stock-table">
                    <thead>
                        <tr>
                            <th>ลำดับ</th>
                            <th>รหัสเบิกจ่าย</th>
                            <th>วันที่รับเข้า</th>
                            <th>ผู้รับเข้า</th>
                            <th>จำนวน (<?php echo $drug->unit_name; ?>)</th>
                            <th>วันหมดอายุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- ตัวอย่างข้อมูล - ควรดึงจากฐานข้อมูลจริง -->
                        <tr>
                            <td>1</td>
                            <td>6</td>
                            <td>31/08/2022</td>
                            <td>นาย ก</td>
                            <td>4,000.00</td>
                            <td>28/02/2025</td>
                        </tr>
                        <!-- เพิ่มแถวตามข้อมูลจริง -->
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js"></script>
    <script src="../assets/vendor/libs/cleavejs/cleave.js"></script>
    <script src="../assets/vendor/libs/cleavejs/cleave-phone.js"></script>

    <script type="text/javascript">
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
    </script>

    <script>
$(document).ready(function() {
    $('#drugTable').DataTable({
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

        //date input
        $(".date-mask").each(function() {
            new Cleave(this, { // ใช้ 'this' เพื่ออ้างอิงถึง element ปัจจุบันใน loop
                date: true,
                delimiter: "/",
                datePattern: ["d", "m", "Y"]
            });
        });


// Get the current date and time
const currentDate = new Date();

// Convert the year to the Buddhist Era
const thaiYear = currentDate.getFullYear() + 543;

// Format the date and time
const formattedDateTime = currentDate.toLocaleString('th-TH', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false // Use 24-hour format
}).replace(/\//g, '/'); // Replace '/' with '-'

// Set the value of the input field
document.getElementById('transaction_date').value = formattedDateTime;
});
function submitAddStock() {
    var form = document.getElementById('addStockForm');
    if (form.checkValidity()) {
        form.submit();
    } else {
        // แสดงข้อความแจ้งเตือนถ้าข้อมูลไม่ครบ
          Swal.fire({
            title: 'แจ้งเตือน!',
            text: ' กรุณากรอกข้อมูลให้ครบ!',
            icon: 'error',
            customClass: {
              confirmButton: 'btn btn-primary waves-effect waves-light'
            },
            buttonsStyling: false
          })
    }
}
    </script>
</body>
</html>