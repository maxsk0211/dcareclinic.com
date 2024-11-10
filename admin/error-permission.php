<?php 
  session_start();
 ?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ไม่มีสิทธิ์เข้าถึง | D-Care Clinic</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="../assets/vendor/fonts/remixicon/remixicon.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
        }

        .error-card {
            max-width: 500px;
            width: 90%;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            padding: 2rem;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }

        .error-icon {
            font-size: 5rem;
            color: #dc3545;
            margin-bottom: 1rem;
            animation: bounce 2s infinite;
        }

        .error-title {
            color: #2c3e50;
            font-size: 2rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .error-message {
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .error-user-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn-back {
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn-contact {
            background-color: #6c757d;
            color: white;
        }

        .btn-contact:hover {
            background-color: #5a6268;
            color: white;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }

        .error-details {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #dee2e6;
        }

        .error-code {
            background: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-family: monospace;
            color: #dc3545;
            display: inline-block;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <i class="ri-error-warning-line error-icon"></i>
            <h1 class="error-title">ไม่มีสิทธิ์เข้าถึง</h1>
            <p class="error-message">
                ขออภัย คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้<br>
                กรุณาติดต่อผู้ดูแลระบบเพื่อขอสิทธิ์ในการเข้าถึง
            </p>

            <div class="error-user-info">
                <p><strong>ผู้ใช้:</strong> <?php echo $_SESSION['users_fname'] . ' ' . $_SESSION['users_lname']; ?></p>
                <p><strong>ตำแหน่ง:</strong> 
                    <?php 
                        $position_names = [
                            1 => 'ผู้ดูแลระบบ',
                            2 => 'ผู้จัดการคลินิก',
                            3 => 'หมอ',
                            4 => 'พยาบาล',
                            5 => 'พนักงานต้อนรับ'
                        ];
                        echo $position_names[$_SESSION['position_id']] ?? 'ไม่ระบุ';
                    ?>
                </p>
                <p><strong>หน้าที่พยายามเข้าถึง:</strong> <?php echo $_SESSION['attempted_page'] ?? 'ไม่ทราบ'; ?></p>
            </div>

            <div class="error-actions">
                <a href="index.php" class="btn btn-primary btn-back">
                    <i class="ri-home-line me-2"></i>กลับหน้าหลัก
                </a>
                <button onclick="window.history.back()" class="btn btn-contact btn-back">
                    <i class="ri-arrow-left-line me-2"></i>กลับหน้าก่อนหน้า
                </button>
            </div>

            <div class="error-details">
                <p>หากคุณเชื่อว่านี่เป็นข้อผิดพลาด กรุณาติดต่อผู้ดูแลระบบ</p>
                <p>รหัสข้อผิดพลาด: <span class="error-code">ERR_403_FORBIDDEN</span></p>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
</body>
</html>