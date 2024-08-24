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
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .course-image:hover {
            transform: scale(1.03);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            padding: 20px;
        }
        .card-body {
            padding: 30px;
        }
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        .course-quick-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .course-quick-info .info-item {
            background-color: #f0f4f8;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }
        .course-quick-info .info-item label {
            display: block;
            margin-bottom: 5px;
            color: #718096;
            font-size: 0.9rem;
        }
        .course-quick-info .info-item .value {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2d3748;
        }

        .course-details-modern {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            padding: 25px;
            margin-top: 20px;
        }
        .course-details-modern .detail-item {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
        }
        .course-details-modern .detail-item:last-child {
            border-bottom: none;
        }
        .course-details-modern label {
            display: block;
            font-weight: 600;
            color: #344767;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .course-details-modern .detail-content {
            font-size: 1rem;
            color: #2d3748;
            background-color: #f8f9fa;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        .course-details-modern textarea.detail-content {
            min-height: 100px;
            resize: vertical;
        }
        .course-details-modern .badge-status {
            display: inline-block;
            padding: 8px 12px;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 20px;
            text-transform: uppercase;
        }
        .course-details-modern .badge-status.active {
            background-color: #48bb78;
            color: white;
        }
        .course-details-modern .badge-status.inactive {
            background-color: #f56565;
            color: white;
        }
        .course-details-modern .date-info {
            display: flex;
            justify-content: space-between;
            gap: 15px;
        }
        .course-details-modern .date-info .date-item {
            flex: 1;
        }
        @media (max-width: 768px) {
            .course-details-modern .date-info {
                flex-direction: column;
            }
        }
        .modal-content {
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  }
  
  .modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
  }
  
  .modal-title {
    color: #495057;
    font-weight: 600;
  }
  
  .form-label {
    font-weight: 500;
    color: #495057;
  }
  
  .form-select, .form-control {
    border-radius: 8px;
    border: 1px solid #ced4da;
    padding: 10px 15px;
  }
  
  .input-group .form-select {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
  }
  
  .btn-primary {
    background-color: #007bff;
    border-color: #007bff;
  }
  
  .btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
  }

.table {
    width: 100%;
    margin-bottom: 1rem;
    color: #212529;
    vertical-align: top;
    border-color: #dee2e6;
}
.table > :not(caption) > * > * {
    padding: 0.5rem 0.5rem;
    background-color: var(--bs-table-bg);
    border-bottom-width: 1px;
    box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
}
.table-bordered > :not(caption) > * {
    border-width: 1px 0;
}
.table-bordered > :not(caption) > * > * {
    border-width: 0 1px;
}
.table-hover > tbody > tr:hover > * {
    --bs-table-accent-bg: rgba(0, 0, 0, 0.075);
    color: var(--bs-table-hover-color);
}
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 0.25rem;
}
.card-header {
    background-color: rgba(0, 0, 0, 0.03);
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}
.card-title {
    margin-bottom: 0;
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
                                    <div class="col-md-7">
                                        <div class="course-quick-info">
                                            <div class="info-item">
                                                <label>รหัส</label>
                                                <div class="value"><?= formatId($row->course_id); ?></div>
                                            </div>
                                            <div class="info-item">
                                                <label>ประเภท</label>
                                                <div class="value"><?= $course_type_name ?></div>
                                            </div>
                                            <div class="info-item">
                                                <label>จำนวน</label>
                                                <div class="value"><?= $row->course_amount ?> ครั้ง</div>
                                            </div>
                                            <div class="info-item">
                                                <label>ราคา</label>
                                                <div class="value"><?= number_format($row->course_price) ?> บาท</div>
                                            </div>
                                        </div>

                                        <div class="course-details-modern">
                                            <div class="detail-item">
                                                <label>ชื่อคอร์ส</label>
                                                <div class="detail-content"><?= $row->course_name ?></div>
                                            </div>
                                            <div class="row">
                                                <div class="detail-item col-md-6">
                                                    <label>รายละเอียดคอร์ส</label>
                                                    <textarea class="detail-content" readonly><?= $row->course_detail ?></textarea>
                                                </div>
                                                <div class="detail-item col-md-6">
                                                    <label>หมายเหตุ</label>
                                                    <textarea class="detail-content" readonly><?= $row->course_note ?></textarea>
                                                </div>
                                            </div>
                                            <div class="detail-item">
                                                <label>ระยะเวลาคอร์ส</label>
                                                <div class="date-info">
                                                    <div class="date-item">
                                                        <label>วันที่เริ่ม</label>
                                                        <div class="detail-content"><?= date('d/m/Y', strtotime($row->course_start) + 543 * 365 * 24 * 60 * 60) ?></div>
                                                    </div>
                                                    <div class="date-item">
                                                        <label>วันที่สิ้นสุด</label>
                                                        <div class="detail-content"><?= date('d/m/Y', strtotime($row->course_end) + 543 * 365 * 24 * 60 * 60) ?></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="detail-item">
                                                <label>สถานะ</label>
                                                <div>
                                                    <?php if ($row->course_status == 1): ?>
                                                        <span class="badge-status active">พร้อมใช้งาน</span>
                                                    <?php else: ?>
                                                        <span class="badge-status inactive">ไม่พร้อมใช้งาน</span>
                                                    <?php endif ?>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>

                                <!-- modal -->
<?php
// ดึงข้อมูลยา
$course_id = $_GET['id'];
$drug_sql = "SELECT d.drug_id as id, d.drug_name as name, u.unit_name as unit FROM drug d LEFT JOIN unit u ON d.drug_unit_id = u.unit_id WHERE d.drug_status = 1";
$drug_result = $conn->query($drug_sql);
$drugs = $drug_result->fetch_all(MYSQLI_ASSOC);

// ดึงข้อมูลเครื่องมือ
$tool_sql = "SELECT t.tool_id as id, t.tool_name as name, u.unit_name as unit FROM tool t LEFT JOIN unit u ON t.tool_unit_id = u.unit_id WHERE t.tool_status = 1";
$tool_result = $conn->query($tool_sql);
$tools = $tool_result->fetch_all(MYSQLI_ASSOC);

// ดึงข้อมูลอุปกรณ์
$accessory_sql = "SELECT a.acc_id as id, a.acc_name as name, u.unit_name as unit FROM accessories a LEFT JOIN unit u ON a.acc_unit_id = u.unit_id WHERE a.acc_status = 1";
$accessory_result = $conn->query($accessory_sql);
$accessories = $accessory_result->fetch_all(MYSQLI_ASSOC);
?>
<div class="modal fade" id="addResourceModal" tabindex="-1" aria-labelledby="addResourceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addResourceModalLabel">เพิ่มทรัพยากรสำหรับคอร์ส</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addResourceForm" action="sql/course-resources-insert.php" method="POST">
          <input type="hidden" name="course_id" id="course_id" value="<?php echo isset($course_id) ? $course_id : ''; ?>" required>
          <div class="mb-3">
            <label for="resourceType" class="form-label">ประเภททรัพยากร</label>
            <select class="form-select" id="resourceType" name="resource_type" required>
              <option value="">เลือกประเภท</option>
              <option value="drug">ยา</option>
              <option value="tool">เครื่องมือ</option>
              <option value="accessory">อุปกรณ์</option>
            </select>
          </div>
          
          <div class="mb-3">
            <label for="resourceName" class="form-label">ชื่อทรัพยากร</label>
            <select class="form-select" id="resourceName" name="resource_id" required>
              <option value="">เลือกทรัพยากร</option>
            </select>
          </div>
          
          <div class="mb-3">
            <label for="quantity" class="form-label">จำนวน</label>
            <div class="input-group">
              <input type="number" class="form-control" id="quantity" name="quantity" required min="0" step="0.01">
              <span class="input-group-text" id="unitLabel"></span>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
        <button type="submit" class="btn btn-primary"  form="addResourceForm"  data-course-id="<?php echo $course_id; ?>">เพิ่มทรัพยากร</button>
      </div>
    </div>
  </div>
</div>
                                <!-- modal -->
<?php
// ดึงข้อมูล course_resources (ส่วนนี้ยังคงเดิม)
$resources_sql = "SELECT cr.*,
                        CASE
                            WHEN cr.resource_type = 'drug' THEN d.drug_name
                            WHEN cr.resource_type = 'tool' THEN t.tool_name
                            WHEN cr.resource_type = 'accessory' THEN a.acc_name
                        END AS resource_name,
                        CASE
                            WHEN cr.resource_type = 'drug' THEN d.drug_cost
                            WHEN cr.resource_type = 'tool' THEN t.tool_cost
                            WHEN cr.resource_type = 'accessory' THEN a.acc_cost
                        END AS unit_cost,
                        CASE
                            WHEN cr.resource_type = 'drug' THEN u1.unit_name
                            WHEN cr.resource_type = 'tool' THEN u2.unit_name
                            WHEN cr.resource_type = 'accessory' THEN u3.unit_name
                        END AS unit_name
                        FROM course_resources cr
                        LEFT JOIN drug d ON cr.resource_type = 'drug' AND cr.resource_id = d.drug_id
                        LEFT JOIN tool t ON cr.resource_type = 'tool' AND cr.resource_id = t.tool_id
                        LEFT JOIN accessories a ON cr.resource_type = 'accessory' AND cr.resource_id = a.acc_id
                        LEFT JOIN unit u1 ON d.drug_unit_id = u1.unit_id
                        LEFT JOIN unit u2 ON t.tool_unit_id = u2.unit_id
                        LEFT JOIN unit u3 ON a.acc_unit_id = u3.unit_id
                        WHERE cr.course_id = ?";

$stmt = $conn->prepare($resources_sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

$total_cost = 0;
$resources = [];

// เปลี่ยนจาก fetch_assoc เป็น fetch_object
while ($row = $result->fetch_object()) {
    $row->total_cost = $row->quantity * $row->unit_cost;
    $total_cost += $row->total_cost;
    $resources[] = $row;
}
$stmt->close();
?>

<div class="card mt-4">
    <div class="card-header">
        <div class="d-flex justify-content-between">
            <h5 class="card-title">ทรัพยากรที่ใช้ในคอร์ส</h5>
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#addResourceModal">เพิ่มทรัพยากรสำหรับคอร์ส</button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ประเภท</th>
                        <th>ชื่อทรัพยากร</th>
                        <th>จำนวน</th>
                        <th>หน่วยนับ</th>
                        <th>ต้นทุนต่อหน่วย</th>
                        <th>ต้นทุนรวม</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resources as $resource): ?>
                    <tr>
                        <td><?php echo ucfirst($resource->resource_type); ?></td>
                        <td><?php echo $resource->resource_name; ?></td>
                        <td><?php echo $resource->quantity; ?></td>
                        <td><?php echo $resource->unit_name; ?></td>
                        <td><?php echo number_format($resource->unit_cost, 2); ?> บาท</td>
                        <td><?php echo number_format($resource->total_cost, 2); ?> บาท</td>
                        <td>
                            <a href="" class="text-danger" onClick="confirmDelete('sql/course-resource-delete.php?id=<?php echo $resource->resource_id; ?>&course_id=<?php echo $course_id; ?>'); return false;"><i class="ri-delete-bin-6-line"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-primary">
                        <th colspan="5" class="text-end">ต้นทุนรวมทั้งหมด:</th>
                        <th><?php echo number_format($total_cost, 2); ?> บาท</th>
                    </tr>
                </tfoot>
            </table>
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


document.addEventListener('DOMContentLoaded', function() {
  const resourceType = document.getElementById('resourceType');
  const resourceName = document.getElementById('resourceName');
  const unitLabel = document.getElementById('unitLabel');

  // ข้อมูลทรัพยากรแต่ละประเภท
  const resources = {
    drug: <?php echo json_encode($drugs); ?>,
    tool: <?php echo json_encode($tools); ?>,
    accessory: <?php echo json_encode($accessories); ?>
  };

  resourceType.addEventListener('change', function() {
    const selectedType = this.value;
    resourceName.innerHTML = '<option value="">เลือกทรัพยากร</option>';
    unitLabel.textContent = '';
    
    if (selectedType && resources[selectedType]) {
      resources[selectedType].forEach(item => {
        const option = new Option(item.name, item.id);
        resourceName.add(option);
      });
    }
  });

  resourceName.addEventListener('change', function() {
    const selectedType = resourceType.value;
    const selectedId = this.value;
    
    if (selectedType && selectedId) {
      const selectedResource = resources[selectedType].find(item => item.id == selectedId);
      if (selectedResource) {
        unitLabel.textContent = selectedResource.unit;
      }
    }
  });

    // เมื่อ modal เปิด ให้เซ็ต course_id
    $('#addResourceModal').on('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const courseId = button.getAttribute('data-course-id');
      if (courseId) {
        document.getElementById('course_id').value = courseId;
      } else {
        console.error('ไม่พบ course_id');
      }
    });
});
</script>





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





    </script>
</body>
</html>