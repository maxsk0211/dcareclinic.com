<?php
session_start();
include 'chk-session.php';
require '../dbcon.php';

if (isset($_GET['tool_id'])) {
    $tool_id = mysqli_real_escape_string($conn, $_GET['tool_id']);
    $sql = "SELECT t.*,  b.branch_name, u.unit_name 
            FROM tool t
            LEFT JOIN branch b ON t.branch_id = b.branch_id
            LEFT JOIN unit u ON t.tool_unit_id = u.unit_id
            WHERE t.tool_id = '$tool_id'";
    $result = mysqli_query($conn, $sql);
    $tool = mysqli_fetch_object($result);

    if (!$tool) {
        $_SESSION['msg_error'] = "ไม่พบข้อมูลเครื่องมือ1";
        header("Location: tool.php");
        exit();
    }
} else {
    $_SESSION['msg_error'] = "ไม่ได้ระบุรหัสเครื่องมือ2";
    header("Location: tool.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดเครื่องมือ | <?php echo $tool->tool_name; ?></title>

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
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 0 30px rgba(0,0,0,0.15);
    }
    .card-header {
        background-color: #4e73df;
        color: white;
        border-bottom: none;
        padding: 20px 25px;
    }
    .card-title {
        margin-bottom: 0;
        font-weight: 600;
        font-size: 1.25rem;
    }
    .card-body {
        padding: 30px;
    }
    .tool-info-section {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .tool-info-title {
        color: #4e73df;
        font-weight: 600;
        margin-bottom: 15px;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 5px;
    }
    .tool-info-item {
        margin-bottom: 15px;
    }
    .tool-info-label {
        font-weight: 600;
        color: #495057;
    }
    .tool-info-value {
        color: #6c757d;
    }
    .badge {
        padding: 8px 12px;
        font-size: 0.85rem;
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
    .table {
        border-collapse: separate;
        border-spacing: 0 10px;
    }
    .table thead th {
        background-color: #4e73df;
        color: white;
        border: none;
        padding: 15px;
        font-weight: 600;
    }
    .table tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .table tbody tr:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .table td {
        border: none;
        padding: 15px;
        vertical-align: middle;
    }
    .modal-content {
        border-radius: 15px;
        overflow: hidden;
    }
    .modal-header {
        background-color: #4e73df;
        color: white;
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
        padding: 10px 15px;
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
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title text-white">รายละเอียดเครื่องมือแพทย์</h5>
            <a href="tool.php" class="btn btn-secondary">
                <i class="ri-arrow-left-line me-1"></i> ย้อนกลับ
            </a>
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
    $formattedId = 'TOOL-' . str_pad($idString, 6, '0', STR_PAD_LEFT);

    return $formattedId;
}

 ?>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="tool-info-section">
                        <h6 class="tool-info-title">ข้อมูลทั่วไป</h6>
                        <div class="tool-info-item">
                            <span class="tool-info-label">รหัสเครื่องมือ:</span>
                            <span class="tool-info-value"><?php echo formatId($tool->tool_id); ?></span>
                        </div>
                        <div class="tool-info-item">
                            <span class="tool-info-label">ชื่อเครื่องมือ:</span>
                            <span class="tool-info-value"><?php echo $tool->tool_name; ?></span>
                        </div>
                        <div class="tool-info-item">
                            <span class="tool-info-label">สถานะ:</span>
                            <?php if ($tool->tool_status == 1): ?>
                                <span class="badge badge-success">พร้อมใช้งาน</span>
                            <?php else: ?>
                                <span class="badge badge-danger">ไม่พร้อมใช้งาน</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="tool-info-section">
                        <h6 class="tool-info-title">รายละเอียดเครื่องมือ</h6>
                        <div class="tool-info-item">
                            <span class="tool-info-label">รายละเอียด:</span>
                            <span class="tool-info-value"><?php echo $tool->tool_detail; ?></span>
                        </div>
                    </div>
                    <div class="tool-info-section">
                        <h6 class="tool-info-title">ข้อมูลคงคลัง</h6>
                        <div class="tool-info-item">
                            <span class="tool-info-label">จำนวนคงเหลือ:</span>
                            <span class="tool-info-value"><?php echo $tool->tool_amount." ".$tool->unit_name; ?></span>
                        </div>
                        <div class="tool-info-item">
                            <span class="tool-info-label">ราคาต้นทุน/<?= $tool->unit_name?>:</span>
                            <span class="tool-info-value"><?php echo number_format($tool->tool_cost, 2); ?> บาท</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



            <div class="mt-4">

                <!-- Modal -->
<div class="modal fade" id="addStockModal" tabindex="-1" aria-labelledby="addStockModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-white" id="addStockModalLabel">เพิ่มสต๊อก</h5>
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
                <input type="text" class="form-control text-danger fw-bold" value="<?php echo formatId($tool->tool_id)." - ".$tool->tool_name; ?>">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="quantity" class="form-label">จำนวนรับเข้า (<?= $tool->unit_name ?>)</label>
              <div class="input-group">
                <input type="number" class="form-control" id="quantity" name="quantity" step="0.01" required>
                <span class="input-group-text"><?= $tool->unit_name ?></span>
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
          <input type="hidden" name="stock_type" value="tool">
          <input type="hidden" name="related_id" value="<?= $_GET['tool_id']; ?>">
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
        <h5 class="modal-title text-white" id="exampleModalLabel">ยืนยันข้อมูล</h5>
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


<?php
// ... (โค้ดอื่นๆ ที่มีอยู่แล้ว) ...

// เพิ่มโค้ดนี้หลังจากส่วนที่แสดงรายละเอียดยา
$stock_sql = "SELECT st.*, u.users_fname, u.users_lname 
              FROM stock_transactions st
              JOIN users u ON st.users_id = u.users_id
              WHERE st.stock_type = 'tool' AND st.related_id = '$tool_id'
              ORDER BY st.transaction_date DESC";
$stock_result = mysqli_query($conn, $stock_sql);


    ?>
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title text-white">ข้อมูลสต็อก</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStockModal">
            <i class="ri-add-line me-1"></i> เพิ่มสต็อก
        </button>
    </div>
    <div class="card-body">
        <?php 

// ตรวจสอบว่ามีข้อมูลสต็อกหรือไม่
if (mysqli_num_rows($stock_result) > 0) {
         ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>วันที่ทำรายการ</th>
                        <th>ผู้ทำรายการ</th>
                        <th>จำนวน</th>
                        <th>ต้นทุนต่อหน่วย</th>
                        <th>วันหมดอายุ</th>
                        <th>หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($stock = mysqli_fetch_object($stock_result)) { ?>
                    <tr>
                        <td><?php echo date('d/m/Y H:i', strtotime($stock->transaction_date)); ?></td>
                        <td><?php echo $stock->users_fname . ' ' . $stock->users_lname; ?></td>
                        <td><?php echo number_format($stock->quantity, 2); ?></td>
                        <td><?php echo number_format($stock->cost_per_unit, 2)." บาท"; ?></td>
                        <td><?php echo ($stock->expiry_date) ? date('d/m/Y', strtotime($stock->expiry_date)) : 'ไม่ระบุ'; ?></td>
                        <td><?php echo $stock->notes; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
            <?php
} else {
    echo "<p>ไม่พบข้อมูลการทำรายการสต็อก</p>";
}

// ... (โค้ดอื่นๆ ที่มีอยู่แล้ว) ...
?>
    </div>
</div>

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