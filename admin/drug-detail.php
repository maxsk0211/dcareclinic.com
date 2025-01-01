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
/*        border: none;*/
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
/*        border-bottom: none;*/
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
    .drug-info-section {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .drug-info-title {
        color: #4e73df;
        font-weight: 600;
        margin-bottom: 15px;
        border-bottom: 2px solid #4e73df;
        padding-bottom: 5px;
    }
    .drug-info-item {
        margin-bottom: 15px;
    }
    .drug-info-label {
        font-weight: 600;
        color: #495057;
    }
    .drug-info-value {
        color: #6c757d;
    }
    .drug-image {
        max-width: 200px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    .drug-image:hover {
        transform: scale(1.05);
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
    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid #ced4da;
        padding: 10px 15px;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: 0 0 0 0.2rem rgba(78,115,223,0.25);
        border-color: #4e73df;
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
    <div class="card border-2 border-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title text-white">รายละเอียดยา</h5>
            <a href="drug.php" class="btn btn-secondary">
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
    $formattedId = 'D-' . str_pad($idString, 6, '0', STR_PAD_LEFT);

    return $formattedId;
}
 ?>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="drug-info-section">
                        <h6 class="drug-info-title">ข้อมูลทั่วไป</h6>
                        <div class="drug-info-item">
                            <span class="drug-info-label">รหัสยา:</span>
                            <span class="drug-info-value"><?php echo formatId($drug->drug_id); ?></span>
                        </div>
                        <div class="drug-info-item">
                            <span class="drug-info-label">ชื่อยา:</span>
                            <span class="drug-info-value"><?php echo $drug->drug_name; ?></span>
                        </div>
                        <div class="drug-info-item">
                            <span class="drug-info-label">ประเภท:</span>
                            <span class="drug-info-value"><?php echo $drug->drug_type_name; ?></span>
                        </div>
                        <div class="drug-info-item">
                            <span class="drug-info-label">สถานะ:</span>
                            <?php if ($drug->drug_status == 1): ?>
                                <span class="badge badge-success">พร้อมใช้งาน</span>
                            <?php else: ?>
                                <span class="badge badge-danger">ไม่พร้อมใช้งาน</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!empty($drug->drug_pic)): ?>
                        <img src="../img/drug/<?php echo $drug->drug_pic; ?>" alt="รูปภาพยา" class="drug-image img-fluid mt-3">
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <div class="drug-info-section">
                        <h6 class="drug-info-title">รายละเอียดยา</h6>
                        <div class="drug-info-item">
                            <span class="drug-info-label">คุณสมบัติ:</span>
                            <span class="drug-info-value"><?php echo $drug->drug_properties; ?></span>
                        </div>
                        <div class="drug-info-item">
                            <span class="drug-info-label">วิธีใช้:</span>
                            <span class="drug-info-value"><?php echo $drug->drug_advice; ?></span>
                        </div>
                        <div class="drug-info-item">
                            <span class="drug-info-label">ข้อควรระวัง:</span>
                            <span class="drug-info-value"><?php echo $drug->drug_warning; ?></span>
                        </div>
                    </div>
                    <div class="drug-info-section">
                        <h6 class="drug-info-title">ข้อมูลคงคลัง</h6>
                        <div class="drug-info-item">
                            <span class="drug-info-label">จำนวนคงเหลือ:</span>
                            <span class="drug-info-value"><?php echo $drug->drug_amount." ".$drug->unit_name; ?></span>
                        </div>
                        <div class="drug-info-item">
                            <span class="drug-info-label">ราคาต้นทุน/<?= $drug->unit_name?>:</span>
                            <span class="drug-info-value"><?php echo number_format($drug->drug_cost, 2); ?> บาท</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

            <div class="mt-4">

<!-- Modal เพิ่มสต็อค -->
<div class="modal fade" id="addStockModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">
                    <i class="ri-medicine-bottle-line me-1"></i> เพิ่มสต็อคยา
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addStockForm" action="sql/stock-insert.php" method="post">
                    <!-- ข้อมูลยา -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>รหัสยา:</strong> <?php echo formatId($drug->drug_id); ?></p>
                                    <p class="mb-1"><strong>ชื่อยา:</strong> <?php echo $drug->drug_name; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>หน่วยนับ:</strong> <?php echo $drug->unit_name; ?></p>
                                    <p class="mb-1"><strong>คงเหลือปัจจุบัน:</strong> <?php echo number_format($drug->drug_amount); ?> <?php echo $drug->unit_name; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- แบบฟอร์มเพิ่มสต็อค -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">วันที่ทำรายการ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="transaction_date" name="transaction_date" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">จำนวนรับเข้า <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="quantity" name="quantity" 
                                           min="0.01" step="0.01" required
                                           placeholder="ระบุจำนวน">
                                    <span class="input-group-text"><?php echo $drug->unit_name; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">ราคาต้นทุนต่อหน่วย <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="cost_per_unit" name="cost_per_unit"
                                           min="0.01" step="0.01" required
                                           placeholder="ระบุราคาต้นทุน">
                                    <span class="input-group-text">บาท</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">วันหมดอายุ</label>
                                <input type="text" class="form-control date-mask" id="expiry_date" name="expiry_date" 
                                       placeholder="วว/ดด/ปปปป">
                                <div class="form-text">ระบุในรูปแบบ วัน/เดือน/ปี พ.ศ.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"
                                 placeholder="ระบุรายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
                    </div>

                    <input type="hidden" name="users_id" value="<?php echo $_SESSION['users_id']; ?>">
                    <input type="hidden" name="stock_type" value="drug">
                    <input type="hidden" name="related_id" value="<?php echo $_GET['drug_id']; ?>">
                    <input type="hidden" name="branch_id" value="<?php echo $_SESSION['branch_id']; ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i> ยกเลิก
                </button>
                <button type="button" class="btn btn-primary" onclick="validateStockForm()">
                    <i class="ri-save-line me-1"></i> บันทึกข้อมูล
                </button>
            </div>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">เพิ่มสต็อก</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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



<div class="card mt-4 border-2 border-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title text-white mb-0">
            <i class="ri-history-line me-1"></i> ประวัติรายการเข้า-ออก
        </h5>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addStockModal">
            <i class="ri-add-line me-1"></i> เพิ่มสต็อค
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="stockHistoryTable">
                <thead class="table-primary">
                    <tr>
                        <th>วันที่-เวลา</th>
                        <th>ประเภทรายการ</th>
                        <th class="text-end">จำนวน</th>
                        <th>หน่วยนับ</th>
                        <th class="text-end">ราคา/หน่วย</th>
                        <th class="text-end">มูลค่ารวม</th>
                        <th>วันหมดอายุ</th>
                        <th>ผู้ทำรายการ</th>
                        <th>หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stock_sql = "SELECT st.*, u.users_fname, u.users_lname,
                                  d.drug_name, dt.drug_type_name, unit.unit_name,
                                  CASE 
                                      WHEN st.notes LIKE '%ORDER%' THEN 'ใช้ในคอร์ส'
                                      WHEN st.notes LIKE '%คืนสต็อก%' THEN 'คืนสต็อก'
                                      WHEN st.quantity > 0 THEN 'รับเข้า'
                                      ELSE 'เบิกออก'
                                  END as transaction_type_name,
                                  CASE 
                                      WHEN st.quantity > 0 THEN st.quantity
                                      ELSE ABS(st.quantity)
                                  END as display_quantity,
                                  (ABS(st.quantity) * st.cost_per_unit) as total_value
                                  FROM stock_transactions st
                                  LEFT JOIN users u ON st.users_id = u.users_id
                                  LEFT JOIN drug d ON st.related_id = d.drug_id
                                  LEFT JOIN drug_type dt ON d.drug_type_id = dt.drug_type_id
                                  LEFT JOIN unit ON d.drug_unit_id = unit.unit_id
                                  WHERE st.stock_type = 'drug' 
                                  AND st.related_id = ? 
                                  AND st.status = 1
                                  ORDER BY st.transaction_date DESC";
                              
                    $stmt = $conn->prepare($stock_sql);
                    $stmt->bind_param("i", $_GET['drug_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_object()) {
                                // กำหนดสีและ class ตามประเภทรายการ
                                $badgeClass = '';
                                switch($row->transaction_type_name) {
                                    case 'รับเข้า':
                                        $badgeClass = 'bg-success';
                                        break;
                                    case 'เบิกออก':
                                        $badgeClass = 'bg-danger';
                                        break;
                                    case 'ใช้ในคอร์ส':
                                        $badgeClass = 'bg-info';
                                        break;
                                    case 'คืนสต็อก':
                                        $badgeClass = 'bg-warning';
                                        break;
                                }
                                
                                echo "<tr>";
                                echo "<td>" . date('d/m/Y H:i', strtotime($row->transaction_date)) . "</td>";
                                echo "<td><span class='badge {$badgeClass}'>{$row->transaction_type_name}</span></td>";
                                echo "<td class='text-end'>" . number_format($row->display_quantity, 2) . "</td>";
                                echo "<td>{$row->unit_name}</td>";
                                echo "<td class='text-end'>" . number_format($row->cost_per_unit, 2) . "</td>";
                                echo "<td class='text-end'>" . number_format($row->total_value, 2) . "</td>";
                                echo "<td>" . ($row->expiry_date ? date('d/m/Y', strtotime($row->expiry_date)) : '-') . "</td>";
                                echo "<td>{$row->users_fname} {$row->users_lname}</td>";
                                echo "<td>";
                                // ตัดคำว่า "ORDER-" ออกจาก notes ถ้ามี
                                echo $row->notes ? str_replace('ORDER-', 'รหัสการสั่งซื้อ: ', $row->notes) : '-';
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9' class='text-center'>ไม่พบข้อมูลรายการ</td></tr>";
                        }
                    ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="2" class="text-end">รวมทั้งหมด:</th>
                        <th class="text-end" id="totalQuantity">0</th>
                        <th colspan="2" class="text-end">มูลค่ารวม:</th>
                        <th class="text-end" id="totalValue">0</th>
                        <th colspan="3"></th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-end">รับเข้า:</th>
                        <th class="text-end" id="totalIn">0</th>
                        <th colspan="2" class="text-end">มูลค่ารับเข้า:</th>
                        <th class="text-end" id="totalInValue">0</th>
                        <th colspan="3"></th>
                    </tr>
                    <tr>
                        <th colspan="2" class="text-end">เบิกออก:</th>
                        <th class="text-end" id="totalOut">0</th>
                        <th colspan="2" class="text-end">มูลค่าเบิกออก:</th>
                        <th class="text-end" id="totalOutValue">0</th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
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
    <!-- <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js" /> -->
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
// ตั้งค่าวันที่ปัจจุบัน
$(document).ready(function() {
    let totalIn = 0;
    let totalOut = 0;
    let totalInValue = 0;
    let totalOutValue = 0;

    $('#stockHistoryTable tbody tr').each(function() {
        const type = $(this).find('td:eq(1) .badge').text();
        const quantity = parseFloat($(this).find('td:eq(2)').text().replace(/,/g, ''));
        const value = parseFloat($(this).find('td:eq(5)').text().replace(/,/g, ''));

        if (type === 'รับเข้า' || type === 'คืนสต็อก') {
            totalIn += quantity;
            totalInValue += value;
        } else {
            totalOut += quantity;
            totalOutValue += value;
        }
    });

    $('#totalQuantity').text(number_format(totalIn - totalOut, 2));
    $('#totalValue').text(number_format(totalInValue - totalOutValue, 2));
    $('#totalIn').text(number_format(totalIn, 2));
    $('#totalInValue').text(number_format(totalInValue, 2));
    $('#totalOut').text(number_format(totalOut, 2));
    $('#totalOutValue').text(number_format(totalOutValue, 2));

    // Format วันที่ปัจจุบัน
    const now = new Date();
    const currentDateTime = now.toLocaleString('th-TH', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });
    $('#transaction_date').val(currentDateTime);

    // ตั้งค่า DataTable
    $('#stockHistoryTable').DataTable({
        order: [[0, 'desc']], // เรียงตามวันที่ล่าสุด
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "ทั้งหมด"]],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json'
        }
    });
});

function number_format(number, decimals = 0) {
    return number.toLocaleString('th-TH', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

// ตรวจสอบข้อมูลก่อนบันทึก
function validateStockForm() {
    const form = document.getElementById('addStockForm');
    const quantity = parseFloat($('#quantity').val());
    const costPerUnit = parseFloat($('#cost_per_unit').val());

    if (!quantity || quantity <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาระบุจำนวน',
            text: 'จำนวนต้องมากกว่า 0'
        });
        return false;
    }

    if (!costPerUnit || costPerUnit <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'กรุณาระบุราคาต้นทุน',
            text: 'ราคาต้นทุนต้องมากกว่า 0'
        });
        return false;
    }

    // แสดง Modal ยืนยันการบันทึก
    Swal.fire({
        title: 'ยืนยันการบันทึก',
        html: `
            <div class="text-start">
                <p class="mb-2"><strong>จำนวนรับเข้า:</strong> ${quantity.toLocaleString()} ${$('#unit_name').text()}</p>
                <p class="mb-2"><strong>ราคาต้นทุน:</strong> ${costPerUnit.toLocaleString()} บาท/หน่วย</p>
                <p class="mb-0"><strong>มูลค่ารวม:</strong> ${(quantity * costPerUnit).toLocaleString()} บาท</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}
    </script>
</body>
</html>