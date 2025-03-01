<?php
require 'dbcon.php';

// ตรวจสอบว่ามี ID ของโปรโมชั่น
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// ดึงข้อมูลโปรโมชั่น
$sql = "SELECT fp.*, c.course_name, c.course_pic, c.course_detail 
        FROM frontend_promotions fp
        JOIN course c ON fp.course_id = c.course_id
        WHERE fp.id = ? AND fp.status = 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header('Location: index.php');
    exit;
}

$promotion = $result->fetch_assoc();

// ตรวจสอบว่าโปรโมชั่นยังไม่หมดอายุ
$today = date('Y-m-d');
$is_active = ($promotion['start_date'] <= $today && $promotion['end_date'] >= $today);

// คำนวณส่วนลด
$discount_percent = $promotion['discount_percent'];
if (empty($discount_percent) && !empty($promotion['original_price']) && !empty($promotion['promotion_price'])) {
    $discount_percent = (($promotion['original_price'] - $promotion['promotion_price']) / $promotion['original_price']) * 100;
    $discount_percent = round($discount_percent, 0);
}

// กำหนดรูปภาพ
$imgPath = $promotion['image_path'] ?? $promotion['course_pic'] ?? 'promotion.png';
$imgUrl = "img/" . ($promotion['image_path'] ? "promotion/{$imgPath}" : "course/{$imgPath}");

// ดึงโปรโมชั่นที่เกี่ยวข้อง
$sql_related = "SELECT fp.*, c.course_name, c.course_pic 
                FROM frontend_promotions fp
                JOIN course c ON fp.course_id = c.course_id
                WHERE fp.id != ? AND fp.status = 1
                AND fp.start_date <= ? AND fp.end_date >= ?
                ORDER BY fp.is_featured DESC, RAND()
                LIMIT 3";

$stmt_related = $conn->prepare($sql_related);
$stmt_related->bind_param("iss", $id, $today, $today);
$stmt_related->execute();
$result_related = $stmt_related->get_result();

// คำนวณวันที่เหลือ
$end_date = strtotime($promotion['end_date']);
$now = strtotime('now');
$days_remaining = ceil(($end_date - $now) / (60 * 60 * 24));
$hours_remaining = ceil(($end_date - $now) / (60 * 60)) % 24;
$minutes_remaining = ceil(($end_date - $now) / 60) % 60;

// เตรียมคุณสมบัติพิเศษ
$features = [];
if (!empty($promotion['features'])) {
    $features = array_filter(array_map('trim', explode("\n", $promotion['features'])));
}

$page_title = $promotion['title'] . " - D Care Clinic";

?>
<!DOCTYPE html>
<html lang="th" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>การรักษาฝ้า กระ และจุดด่างดำ: วิธีการที่ได้ผลจริง - D Care Clinic</title>

    <!-- SEO Tags -->
    <meta name="description" content="เรียนรู้วิธีการรักษาฝ้า กระ และจุดด่างดำด้วยเทคโนโลยีทันสมัย เลือกวิธีที่เหมาะกับคุณจากผู้เชี่ยวชาญ D Care Clinic" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="img/pr/logo.jpg" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="assets/vendor/fonts/flag-icons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="assets/css/demo.css" />
    <link rel="stylesheet" href="navbar-styles.css" />
    <link rel="stylesheet" href="footer-styles.css" />

<style>
/* Hero Banner */
.promotion-hero {
    position: relative;
    padding: 160px 0 80px;
    background: linear-gradient(135deg, rgba(255,143,177,0.1) 0%, rgba(255,91,148,0.1) 100%);
    overflow: hidden;
    margin-bottom: 0;
}

.promotion-hero::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100px;
    background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, #fff 100%);
}

.floating-circle {
    position: absolute;
    border-radius: 50%;
    background: linear-gradient(120deg, #ff8fb1, #ff5b94);
    opacity: 0.1;
    animation: float 20s infinite ease-in-out;
}

.circle-1 {
    width: 300px;
    height: 300px;
    top: -150px;
    left: -150px;
}

.circle-2 {
    width: 200px;
    height: 200px;
    top: 40%;
    right: -100px;
    animation-delay: -5s;
}

.circle-3 {
    width: 150px;
    height: 150px;
    bottom: -75px;
    left: 20%;
    animation-delay: -10s;
}

@keyframes float {
    0%, 100% {
        transform: translate(0, 0) rotate(0deg);
    }
    33% {
        transform: translate(30px, -30px) rotate(5deg);
    }
    66% {
        transform: translate(-20px, 20px) rotate(-5deg);
    }
}

/* Breadcrumb */
.breadcrumb {
    background: transparent;
    padding: 0;
    margin-bottom: 2rem;
}

.breadcrumb-item a {
    color: #666;
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb-item a:hover {
    color: #ff5b94;
}

.breadcrumb-item.active {
    color: #ff5b94;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: '→';
    color: #999;
}

/* Main Promotion Card */
.promotion-main-card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    background: white;
    margin-bottom: 2rem;
    border: none;
    transition: all 0.3s ease;
}

.promotion-main-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
}

.promotion-image-container {
    position: relative;
    height: 400px;
    overflow: hidden;
}

.promotion-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.promotion-main-card:hover .promotion-image-container img {
    transform: scale(1.05);
}

.image-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 150px;
    background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%);
}

.badge-container {
    position: absolute;
    top: 20px;
    right: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    z-index: 3;
}

.promo-badge {
    padding: 8px 15px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    animation: fadeInRight 0.5s ease forwards;
}

.countdown-badge {
    position: absolute;
    bottom: 20px;
    left: 20px;
    background: rgba(255, 255, 255, 0.9);
    color: #ff5b94;
    padding: 8px 15px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    z-index: 2;
}

.promotion-title {
    font-size: 2.2rem;
    font-weight: 700;
    background: linear-gradient(120deg, #ff5b94, #ff8fb1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 0.5rem;
    position: relative;
    display: inline-block;
}

.promotion-title::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 50px;
    height: 3px;
    background: linear-gradient(90deg, #ff8fb1, #ff5b94);
    border-radius: 3px;
}

.course-name {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 1.5rem;
}

/* Price Section */
.price-section {
    background: rgba(255, 91, 148, 0.05);
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 2rem;
}

.price-section .row {
    align-items: center;
}

.price-tag {
    display: flex;
    align-items: center;
    gap: 10px;
}

.original-price {
    font-size: 1.2rem;
    color: #999;
    text-decoration: line-through;
}

.current-price {
    font-size: 2.5rem;
    font-weight: 800;
    color: #ff5b94;
    line-height: 1;
}

.discount-badge {
    background: #FF3B6F;
    color: white;
    padding: 5px 10px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
}

.date-info {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.95rem;
}

.date-info i {
    color: #ff5b94;
}

/* Countdown Timer */
.countdown-timer-large {
    background: #fff;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(255, 91, 148, 0.1);
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.countdown-timer-large::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, rgba(255,143,177,0.05), rgba(255,91,148,0.05), rgba(255,143,177,0.05));
    transform: rotate(45deg);
    z-index: 0;
}

.countdown-title {
    color: #ff5b94;
    font-weight: 600;
    margin-bottom: 1rem;
    text-align: center;
    position: relative;
    z-index: 1;
}

.countdown-timer-large .time-blocks {
    display: flex;
    justify-content: center;
    gap: 15px;
    position: relative;
    z-index: 1;
}

.time-block {
    width: 100px;
    text-align: center;
}

.time-value {
    background: linear-gradient(135deg, #ff8fb1, #ff5b94);
    color: white;
    font-size: 2.5rem;
    font-weight: 700;
    padding: 15px 5px;
    border-radius: 10px;
    box-shadow: 0 10px 20px rgba(255, 91, 148, 0.2);
    margin-bottom: 10px;
    line-height: 1;
}

.time-label {
    font-size: 0.85rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Description Section */
.description-section {
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1rem;
    position: relative;
    padding-left: 15px;
}

.section-title::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(135deg, #ff8fb1, #ff5b94);
    border-radius: 4px;
}

.promotion-description {
    color: #666;
    line-height: 1.8;
}

/* Features */
.features-section {
    margin-bottom: 2rem;
}

.features-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.feature-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 15px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.feature-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

.feature-icon {
    color: #ff5b94;
    font-size: 1.2rem;
    margin-top: 3px;
}

/* Action Buttons */
.promotion-actions {
    display: flex;
    gap: 15px;
}

.btn-book-now {
    flex: 2;
    background: linear-gradient(120deg, #ff8fb1, #ff5b94);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(255, 91, 148, 0.2);
}

.btn-book-now:hover, .btn-book-now:focus {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(255, 91, 148, 0.3);
    color: white;
}

.btn-book-now.disabled {
    background: #999;
    pointer-events: none;
}

.btn-other-promos {
    flex: 1;
    background: transparent;
    color: #666;
    border: 1px solid rgba(0, 0, 0, 0.1);
    padding: 12px 20px;
    border-radius: 50px;
    font-size: 1.1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
}

.btn-other-promos:hover, .btn-other-promos:focus {
    background: rgba(0, 0, 0, 0.05);
    transform: translateY(-3px);
    color: #333;
}

/* Sidebar */
.sidebar-card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    background: white;
    margin-bottom: 2rem;
    border: none;
}

.sidebar-card .card-header {
    background: #ff5b94;
    color: white;
    padding: 15px 20px;
    border-bottom: none;
}

.sidebar-card .card-header h5 {
    margin: 0;
    font-weight: 600;
}

.contact-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.contact-icon {
    width: 40px;
    height: 40px;
    background: rgba(255, 91, 148, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.contact-icon i {
    color: #ff5b94;
    font-size: 1.2rem;
}

.contact-info .label {
    color: #999;
    font-size: 0.8rem;
    margin-bottom: 0.2rem;
}

.contact-info .value {
    color: #333;
    font-weight: 600;
}

/* Related Promotions */
.related-promo {
    display: flex;
    padding: 15px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.related-promo:last-child {
    border-bottom: none;
}

.related-promo:hover {
    background: rgba(255, 91, 148, 0.05);
}

.related-image {
    width: 80px;
    height: 80px;
    overflow: hidden;
    border-radius: 10px;
    margin-right: 15px;
    flex-shrink: 0;
}

.related-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.related-promo:hover .related-image img {
    transform: scale(1.1);
}

.related-info {
    flex: 1;
}

.related-title {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
}

.related-price {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 10px;
}

.related-original {
    font-size: 0.85rem;
    color: #999;
    text-decoration: line-through;
}

.related-current {
    font-size: 1.1rem;
    font-weight: 700;
    color: #ff5b94;
}

/* Animations */
@keyframes fadeInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeInUp {
    opacity: 0;
    animation: fadeInUp 0.5s ease forwards;
}

/* Responsive Styles */
@media (max-width: 991.98px) {
    .promotion-image-container {
        height: 300px;
    }
    
    .promotion-title {
        font-size: 1.8rem;
    }
    
    .current-price {
        font-size: 2rem;
    }
    
    .time-block {
        width: 80px;
    }
    
    .time-value {
        font-size: 2rem;
    }
}

@media (max-width: 767.98px) {
    .promotion-hero {
        padding: 120px 0 60px;
    }
    
    .promotion-image-container {
        height: 250px;
    }
    
    .badge-container {
        top: 10px;
        right: 10px;
    }
    
    .countdown-badge {
        bottom: 10px;
        left: 10px;
        padding: 5px 10px;
        font-size: 0.8rem;
    }
    
    .promotion-title {
        font-size: 1.5rem;
    }
    
    .price-tag {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .current-price {
        font-size: 1.8rem;
    }
    
    .discount-badge {
        font-size: 0.8rem;
    }
    
    .time-blocks {
        flex-wrap: wrap;
    }
    
    .time-block {
        width: 70px;
    }
    
    .time-value {
        font-size: 1.8rem;
        padding: 10px 5px;
    }
    
    .promotion-actions {
        flex-direction: column;
    }
    
    .btn-book-now, .btn-other-promos {
        width: 100%;
    }
}

@media (max-width: 575.98px) {
    .promotion-hero {
        padding: 100px 0 50px;
    }
    
    .promotion-image-container {
        height: 200px;
    }
    
    .promotion-title {
        font-size: 1.3rem;
    }
    
    .features-container {
        grid-template-columns: 1fr;
    }
    
    .feature-item {
        padding: 12px;
    }
    
    .time-block {
        width: 60px;
    }
    
    .time-value {
        font-size: 1.5rem;
        padding: 8px 5px;
    }
    
    .time-label {
        font-size: 0.75rem;
    }
}
</style>


</head>

<body>


        <div class="layout-wrapper layout-content-navbar layout-without-menu">
        <div class="layout-container">
            <div class="layout-page">
                <!-- Navbar -->
                <?php include 'nav.php'; ?>

<!-- Hero Banner -->
<div class="promotion-hero">
    <div class="floating-elements">
        <div class="floating-circle circle-1"></div>
        <div class="floating-circle circle-2"></div>
        <div class="floating-circle circle-3"></div>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">หน้าแรก</a></li>
                <li class="breadcrumb-item"><a href="index.php#promotion">โปรโมชั่น</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $promotion['title']; ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8">
                <!-- Main Promotion Card -->
                <div class="promotion-main-card animate-fadeInUp" style="animation-delay: 0.1s;">
                    <div class="promotion-image-container">
                        <img src="<?php echo $imgUrl; ?>" alt="<?php echo $promotion['title']; ?>" class="img-fluid">
                        <div class="image-overlay"></div>
                        
                        <div class="badge-container">
                            <?php if (!empty($promotion['badge_text'])) { ?>
                                <div class="promo-badge bg-danger text-white" style="animation-delay: 0.2s;">
                                    <i class="fas fa-fire-alt me-1"></i><?php echo $promotion['badge_text']; ?>
                                </div>
                            <?php } ?>
                            
                            <?php if (!$is_active) { ?>
                                <div class="promo-badge bg-secondary text-white" style="animation-delay: 0.3s;">
                                    <i class="fas fa-clock me-1"></i>สิ้นสุดแล้ว
                                </div>
                            <?php } ?>
                        </div>
                        
                        <?php if ($is_active && $days_remaining <= 7) { ?>
                            <div class="countdown-badge">
                                <i class="fas fa-stopwatch"></i> เหลืออีก <?php echo $days_remaining; ?> วัน
                            </div>
                        <?php } ?>
                    </div>
                    
                    <div class="p-4">
                        <div class="mb-4 animate-fadeInUp" style="animation-delay: 0.2s;">
                            <h1 class="promotion-title"><?php echo $promotion['title']; ?></h1>
                            <p class="course-name"><?php echo $promotion['course_name']; ?></p>
                        </div>
                        
                        <div class="price-section animate-fadeInUp" style="animation-delay: 0.3s;">
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <h4 class="section-title">ราคา</h4>
                                    <div class="price-tag">
                                        <?php if (!empty($promotion['original_price'])) { ?>
                                            <div class="original-price"><?php echo number_format($promotion['original_price']); ?>฿</div>
                                        <?php } ?>
                                        <div class="current-price"><?php echo number_format($promotion['promotion_price']); ?>฿</div>
                                        
                                        <?php if (!empty($discount_percent)) { ?>
                                            <div class="discount-badge">ลด <?php echo $discount_percent; ?>%</div>
                                        <?php } ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h4 class="section-title">ระยะเวลา</h4>
                                    <div class="date-info">
                                        <i class="fas fa-calendar-alt"></i>
                                        <div>
                                            <?php echo date('d/m/Y', strtotime($promotion['start_date'])); ?> - <?php echo date('d/m/Y', strtotime($promotion['end_date'])); ?>
                                            <?php if (!$is_active) { ?>
                                                <span class="badge bg-secondary ms-2">สิ้นสุดแล้ว</span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if ($is_active) { ?>
                            <div class="countdown-timer-large animate-fadeInUp" style="animation-delay: 0.4s;">
                                <h4 class="countdown-title">
                                    <i class="fas fa-hourglass-half me-2"></i>โปรโมชั่นนี้จะสิ้นสุดใน
                                </h4>
                                <div class="time-blocks">
                                    <div class="time-block">
                                        <div class="time-value days"><?php echo $days_remaining; ?></div>
                                        <div class="time-label">วัน</div>
                                    </div>
                                    <div class="time-block">
                                        <div class="time-value hours"><?php echo $hours_remaining; ?></div>
                                        <div class="time-label">ชั่วโมง</div>
                                    </div>
                                    <div class="time-block">
                                        <div class="time-value minutes"><?php echo $minutes_remaining; ?></div>
                                        <div class="time-label">นาที</div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        
                        <div class="description-section animate-fadeInUp" style="animation-delay: 0.5s;">
                            <h4 class="section-title">รายละเอียด</h4>
                            <div class="promotion-description">
                                <?php 
                                if (!empty($promotion['description'])) {
                                    echo nl2br($promotion['description']);
                                } else {
                                    echo nl2br($promotion['course_detail']);
                                }
                                ?>
                            </div>
                        </div>
                        
                        <?php if (!empty($features)) { ?>
                            <div class="features-section animate-fadeInUp" style="animation-delay: 0.6s;">
                                <h4 class="section-title">คุณสมบัติพิเศษ</h4>
                                <div class="features-container">
                                    <?php foreach ($features as $index => $feature) { ?>
                                        <div class="feature-item" style="animation-delay: <?php echo (0.7 + $index * 0.1); ?>s;">
                                            <div class="feature-icon">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                            <div><?php echo $feature; ?></div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        
                        <div class="promotion-actions mt-4 animate-fadeInUp" style="animation-delay: 0.8s;">
                            <?php if ($is_active) { ?>
                                <a href="https://line.me/R/ti/p/@bsl3458m" target="_blank" class="btn btn-book-now">
                                    <i class="fas fa-calendar-plus"></i>จองทันที
                                </a>
                            <?php } else { ?>
                                <button class="btn btn-book-now disabled">
                                    <i class="fas fa-calendar-times"></i>โปรโมชั่นนี้สิ้นสุดแล้ว
                                </button>
                            <?php } ?>
                            
                            <a href="index.php#promotion" class="btn btn-other-promos">
                                <i class="fas fa-tags"></i>ดูโปรโมชั่นอื่น
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Contact Info -->
                <div class="sidebar-card animate-fadeInUp" style="animation-delay: 0.3s;">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-headset me-2"></i>
                        <h5 class="mb-0">สอบถามข้อมูลเพิ่มเติม</h5>
                    </div>
                    <div class="card-body">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="contact-info">
                                <div class="label">โทรศัพท์</div>
                                <div class="value">099-287-1289</div>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fab fa-line"></i>
                            </div>
                            <div class="contact-info">
                                <div class="label">Line ID</div>
                                <div class="value"><a href="https://line.me/R/ti/p/@bsl3458m" target="_blank">@dcareclinic</a></div>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="contact-info">
                                <div class="label">เวลาทำการ</div>
                                <div class="value">เปิดทุกวัน 13:00 - 21:00 น.</div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="tel:0812345678" class="btn btn-outline-primary btn-sm me-2">
                                <i class="fas fa-phone-alt me-1"></i>โทรเลย
                            </a>
                            <a href="https://line.me/R/ti/p/@bsl3458m" target="_blank" class="btn btn-success btn-sm">
                                <i class="fab fa-line me-1"></i>แอดไลน์
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Related Promotions -->
                <?php if ($result_related && $result_related->num_rows > 0) { ?>
                    <div class="sidebar-card animate-fadeInUp" style="animation-delay: 0.4s;">
                        <div class="card-header d-flex align-items-center">
                            <i class="fas fa-tags me-2"></i>
                            <h5 class="mb-0">โปรโมชั่นที่คุณอาจสนใจ</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php
                            $delay = 0.5;
                            while ($related = $result_related->fetch_assoc()) {
                                // กำหนดรูปภาพ
                                $relImgPath = $related['image_path'] ?? $related['course_pic'] ?? 'promotion.png';
                                $relImgUrl = "img/" . ($related['image_path'] ? "promotion/{$relImgPath}" : "course/{$relImgPath}");
                            ?>
                                <div class="related-promo animate-fadeInUp" style="animation-delay: <?php echo $delay; ?>s;">
                                    <div class="related-image">
                                        <img src="<?php echo $relImgUrl; ?>" alt="<?php echo $related['title']; ?>">
                                    </div>
                                    <div class="related-info">
                                        <h6 class="related-title"><?php echo $related['title']; ?></h6>
                                        <div class="related-price">
                                            <?php if (!empty($related['original_price'])) { ?>
                                                <span class="related-original"><?php echo number_format($related['original_price']); ?>฿</span>
                                            <?php } ?>
                                            <span class="related-current"><?php echo number_format($related['promotion_price']); ?>฿</span>
                                        </div>
                                        <a href="promotion-detail.php?id=<?php echo $related['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-info-circle me-1"></i>รายละเอียด
                                        </a>
                                    </div>
                                </div>
                            <?php 
                                $delay += 0.1;
                            } 
                            ?>
                        </div>
                    </div>
                <?php } ?>
                
                <!-- CTA Card -->
                <div class="sidebar-card animate-fadeInUp" style="animation-delay: 0.6s;">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-gift text-primary" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="mb-3">รับโปรโมชั่นพิเศษเพิ่มเติม</h5>
                        <p class="text-muted mb-3">สมัครรับข่าวสารและโปรโมชั่นพิเศษจาก D Care Clinic ส่งตรงถึงคุณ</p>
                        <a href="#" class="btn btn-primary w-100">
                            <i class="fas fa-envelope me-2"></i>สมัครรับข่าวสาร
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


</div>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // อนิเมชั่นเมื่อเลื่อนหน้าจอมาถึงองค์ประกอบ
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.animate-fadeInUp');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const screenPosition = window.innerHeight;
            
            if (elementPosition < screenPosition) {
                element.style.animation = 'fadeInUp 0.5s ease forwards';
                element.style.animationDelay = element.style.getPropertyValue('animation-delay');
            }
        });
    };
    
    window.addEventListener('scroll', animateOnScroll);
    animateOnScroll(); // เรียกใช้ทันทีเพื่อตรวจสอบองค์ประกอบที่มองเห็นได้ทันที
    
    // อัพเดทนับถอยหลัง
    function updateCountdown() {
        const daysElement = document.querySelector('.time-value.days');
        const hoursElement = document.querySelector('.time-value.hours');
        const minutesElement = document.querySelector('.time-value.minutes');
        
        if (!daysElement || !hoursElement || !minutesElement) return;
        
        const endDate = new Date('<?php echo $promotion['end_date']; ?>').getTime();
        const now = new Date().getTime();
        
        const distance = endDate - now;
        
        if (distance < 0) {
            if (document.querySelector('.countdown-timer-large')) {
                document.querySelector('.countdown-timer-large').innerHTML = '<div class="alert alert-secondary">โปรโมชั่นนี้สิ้นสุดแล้ว</div>';
            }
            return;
        }
        
        const daysValue = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hoursValue = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutesValue = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        
        daysElement.textContent = daysValue;
        hoursElement.textContent = hoursValue;
        minutesElement.textContent = minutesValue;
    }
    
    // อัพเดทนับถอยหลังทุกนาที
    <?php if ($is_active) { ?>
    updateCountdown();
    setInterval(updateCountdown, 60000);
    <?php } ?>
    
    // เลื่อนไปยังบริเวณที่เหมาะสมเมื่อโหลดหน้า
    setTimeout(() => {
        window.scrollTo({
            top: document.querySelector('.promotion-hero').offsetHeight - 100,
            behavior: 'smooth'
        });
    }, 500);
    
    // เพิ่มเอฟเฟกต์ hover ให้กับปุ่ม
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>

<?php include 'footer.php'; ?>