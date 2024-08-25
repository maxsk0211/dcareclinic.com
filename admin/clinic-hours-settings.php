<?php
session_start();

include 'chk-session.php'; // ตรวจสอบสิทธิ์การเข้าถึงสำหรับ admin
require '../dbcon.php'; // เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลเวลาทำการปัจจุบันจากฐานข้อมูล
$sql = "SELECT * FROM clinic_hours ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";
$result = $conn->query($sql);
$clinic_hours = $result->fetch_all(MYSQLI_ASSOC);

$daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

// ถ้ามีการส่งข้อมูลจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // อัปเดตข้อมูลเวลาทำการในฐานข้อมูล
    foreach ($_POST['day_of_week'] as $day => $data) {
        $start_time = $data['start_time'];
        $end_time = $data['end_time'];
        $is_closed = isset($data['is_closed']) ? 1 : 0;

        $update_sql = "UPDATE clinic_hours SET start_time = ?, end_time = ?, is_closed = ? WHERE day_of_week = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssss", $start_time, $end_time, $is_closed, $day);

        if (!$stmt->execute()) {
            error_log("SQL Error: " . $stmt->error);
            $_SESSION['msg_error'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
        } else {
            $_SESSION['msg_ok'] = "บันทึกข้อมูลเวลาทำการสำเร็จ";
        }
        $stmt->close();
    }

    header("Location: clinic-hours-settings.php"); // refresh หน้าเพื่อแสดงข้อมูลใหม่
    exit();
}
?>

<!doctype html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter" data-style="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>ตั้งค่าเวลาทำการคลินิก | dcareclinic.com</title> 

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
    <script src="../assets/js/config.js"></script>
    
    <!-- sweet Alerts 2 -->
    <link rel="stylesheet" href="../assets/vendor/libs/animate-css/animate.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/sweetalert2/sweetalert2.css" />

    <!-- datatables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.3/css/dataTables.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.1.1/css/buttons.dataTables.css"> 

</head>

<body>

    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">
            <?php include 'navbar.php'; ?>

            <div class="layout-page">
                <div class="content-wrapper">
                    <?php include 'menu.php'; ?>

                    <div class="container-xxl flex-grow-1 container-p-y">

                        <div class="container mt-4">
                            <h2>ตั้งค่าเวลาทำการคลินิก</h2>

                            <form method="post" action="clinic-hours-settings.php">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>วัน</th>
                                            <th>เวลาเปิด</th>
                                            <th>เวลาปิด</th>
                                            <th>ปิดทำการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        foreach ($clinic_hours as $hour): 
                                        ?>
                                        <tr>
                                            <td><?php echo $hour['day_of_week']; ?></td>
                                            <td>
                                                <input type="hidden" name="day_of_week[<?php echo $hour['day_of_week']; ?>][day]" value="<?php echo $hour['day_of_week']; ?>">
                                                <input type="time" class="form-control" name="day_of_week[<?php echo $hour['day_of_week']; ?>][start_time]" value="<?php echo $hour['start_time']; ?>" step="60"> 
                                            </td>
                                            <td>
                                                <input type="time" class="form-control" name="day_of_week[<?php echo $hour['day_of_week']; ?>][end_time]" value="<?php echo $hour['end_time']; ?>" step="60">
                                            </td>
                                            <td>
                                                <input type="checkbox" name="day_of_week[<?php echo $hour['day_of_week']; ?>][is_closed]" <?php echo $hour['is_closed'] == 1 ? 'checked' : ''; ?>>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <button type="submit" class="btn btn-primary">บันทึก</button>
                            </form>
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
    <!-- sweet Alerts 2 -->
    <script src="../assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
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
    // บังคับให้ input time แสดงผลเป็น 24 ชั่วโมง
    const timeInputs = document.querySelectorAll('input[type="time"]');
    timeInputs.forEach(input => {
        input.setAttribute('step', '60'); // เพิ่ม step="60" หากยังไม่มี
    });

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

    // msg error
    <?php if(isset($_SESSION['msg_error'])){ ?>
        Swal.fire({
            icon: 'error',
            title: 'ข้อผิดพลาด!',
            text: '<?php echo $_SESSION['msg_error']; ?>',
            customClass: {
                confirmButton: 'btn btn-primary waves-effect waves-light'
            },
            buttonsStyling: false
        })
    <?php unset($_SESSION['msg_error']); } ?>
    </script>

</body>
</html>