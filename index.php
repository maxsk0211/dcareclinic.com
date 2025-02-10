<!DOCTYPE html>
<html lang="th" class="light-style layout-navbar-fixed layout-menu-fixed">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>D Care Clinic</title>

    <meta name="description" content="D Care Clinic - ศูนย์รวมความงามครบวงจร" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="img/pr/logo.jpg" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="../assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="../assets/vendor/fonts/tabler-icons.css" />
    <link rel="stylesheet" href="../assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/node-waves/node-waves.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/swiper/swiper.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/toastr/toastr.css" />

    <!-- Custom CSS -->
    <style>
/* ===============================================
   Global Styles
   =============================================== */
body {
    font-family: 'Prompt', sans-serif;
}

/* ===============================================
   Hero Section Styles
   =============================================== */
.hero-section {
    height: 100vh;
    overflow: hidden;
    background-color: #000;
    position: relative;
}

.hero-swiper {
    width: 100%;
    height: 100%;
}

.hero-slide {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
}

/* Hero Content and Overlay */
.hero-content-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(120deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.6) 50%, rgba(0,0,0,0.4) 100%);
    display: flex;
    align-items: center;
}

.hero-text-content {
    position: relative;
    z-index: 2;
}

/* Hero Typography */
.hero-subtitle-top {
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 3px;
    color: #fff;
    opacity: 0.9;
    font-weight: 400;
}

.hero-title {
    font-size: 4rem;
    font-weight: 700;
    line-height: 1.2;
    color: #fff;
    margin-bottom: 1.5rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.gradient-text {
    background: linear-gradient(120deg, #ff8fb1, #ff5b94);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: 1.25rem;
    color: rgba(255, 255, 255, 0.9);
    line-height: 1.8;
    margin-bottom: 2rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
}

/* Hero Buttons */
.btn-gradient {
    background: linear-gradient(120deg, #ff8fb1, #ff5b94);
    border: none;
    color: white;
    position: relative;
    z-index: 1;
    overflow: hidden;
    transition: all 0.3s ease;
}

.btn-gradient:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 143, 177, 0.4);
}

/* Hero Stats */
.hero-stats {
    display: flex;
    gap: 2.5rem;
    margin-top: 3rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #fff;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: rgba(255, 255, 255, 0.9);
    font-size: 0.9rem;
}

/* Hero Image */
.hero-image-wrap {
    position: relative;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-image {
    width: 100%;
    max-width: 500px;
    height: auto;
    border-radius: 30px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease;
}

/* Floating Elements */
.floating-elements {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    pointer-events: none;
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

/* Experience Badge */
.experience-badge {
    position: absolute;
    bottom: 40px;
    right: 40px;
    width: 120px;
    height: 120px;
    background: linear-gradient(120deg, #ff8fb1, #ff5b94);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 20px rgba(255, 143, 177, 0.3);
}

.badge-content {
    text-align: center;
    color: white;
}

.badge-content .years {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    display: block;
}

.badge-content .text {
    font-size: 0.8rem;
    opacity: 0.9;
}

/* Treatment Features */
.treatment-features {
    display: flex;
    gap: 2rem;
    margin-top: 2rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
}

.feature-item i {
    font-size: 1.5rem;
}

/* ===============================================
   Services Section Styles
   =============================================== */
.service-card {
    transition: transform 0.3s ease;
    height: 100%;
}

.service-card:hover {
    transform: translateY(-5px);
}

.service-card .card-img-top {
    height: 200px;
    object-fit: cover;
}

/* ===============================================
   Promotion Section Styles
   =============================================== */
.promotion-section .card {
    overflow: hidden;
}

.promotion-section .img-fluid {
    transition: transform 0.3s ease;
}

.promotion-section .img-fluid:hover {
    transform: scale(1.05);
}

/* ===============================================
   Doctors Section Styles
   =============================================== */
.doctors-section .rounded-circle {
    border: 5px solid rgba(255, 143, 177, 0.2);
    transition: transform 0.3s ease;
}

.doctors-section .card:hover .rounded-circle {
    transform: scale(1.05);
}

.doctors-section .btn-icon {
    width: 36px;
    height: 36px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ===============================================
   Contact Section Styles
   =============================================== */
.contact-section .card {
    height: 100%;
}

.contact-info .ti {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255, 143, 177, 0.1);
    color: #ff8fb1;
}

/* ===============================================
   Swiper Navigation Styles
   =============================================== */
.swiper-button-next,
.swiper-button-prev {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 50%;
    color: white;
    transition: all 0.3s ease;
}

.swiper-button-next:hover,
.swiper-button-prev:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

.swiper-button-next:after,
.swiper-button-prev:after {
    font-size: 1.5rem;
}

/* Swiper Pagination */
.swiper-pagination-bullet {
    width: 10px;
    height: 10px;
    background: rgba(255, 255, 255, 0.5);
    opacity: 0.7;
}

.swiper-pagination-bullet-active {
    background: #ff8fb1;
    transform: scale(1.2);
}

/* ===============================================
   Footer Styles
   =============================================== */
.footer {
    background: #fff;
    border-top: 1px solid #eee;
}

.footer .btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ===============================================
   Animations
   =============================================== */
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

/* ===============================================
   Responsive Styles
   =============================================== */
@media (max-width: 1200px) {
    .hero-title {
        font-size: 3.5rem;
    }
}

@media (max-width: 992px) {
    .hero-section {
        height: auto;
        min-height: 100vh;
    }

    .hero-title {
        font-size: 2.8rem;
    }

    .hero-subtitle {
        font-size: 1.1rem;
    }

    .hero-stats {
        flex-wrap: wrap;
        gap: 1rem;
    }

    .stat-item {
        flex: 1 1 calc(50% - 1rem);
    }

    .service-card .card-img-top {
        height: 180px;
    }

    .experience-badge {
        width: 100px;
        height: 100px;
        bottom: 20px;
        right: 20px;
    }

    .badge-content .years {
        font-size: 1.5rem;
    }

    .badge-content .text {
        font-size: 0.7rem;
    }

    .treatment-features {
        flex-direction: column;
        gap: 1rem;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }

    .hero-subtitle-top {
        font-size: 0.9rem;
        letter-spacing: 2px;
    }

    .hero-buttons {
        flex-direction: column;
    }

    .hero-buttons .btn {
        width: 100%;
    }

    .stat-item {
        flex: 1 1 100%;
    }

    .swiper-button-next,
    .swiper-button-prev {
        width: 40px;
        height: 40px;
    }

    .swiper-button-next:after,
    .swiper-button-prev:after {
        font-size: 1.2rem;
    }

    .promotion-section .card-body {
        padding: 1.5rem;
    }
    
    .doctors-section .card {
        margin-bottom: 2rem;
    }
}

@media (max-width: 576px) {
    .hero-title {
        font-size: 2rem;
    }

    .hero-subtitle {
        font-size: 1rem;
    }

    .hero-stats {
        margin-top: 2rem;
    }

    .stat-number {
        font-size: 2rem;
    }

    .floating-circle {
        opacity: 0.05;
    }

    .service-card .card-img-top {
        height: 160px;
    }
    
    .footer {
        text-align: center;
    }
    
    .footer .d-flex {
        justify-content: center;
    }
}
    </style>

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/vendor/js/template-customizer.js"></script>
    <script src="../assets/js/config.js"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar layout-without-menu">
        <div class="layout-container">
            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <?php include 'nav.php'; ?>
                <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Hero Section -->
                        <?php include 'hero-section.php'; ?>

                        <!-- Services Section -->
                        <section id="services" class="mb-4">
                            <div class="text-center mb-4">
                                <h2 class="display-6 fw-bold">บริการของเรา</h2>
                                <p class="text-muted">เราให้บริการด้านความงามครบวงจร ด้วยมาตรฐานระดับสากล</p>
                            </div>

                            <div class="row g-4">
                                <!-- Service Card 1 -->
                                <div class="col-md-4">
                                    <div class="card service-card h-100">
                                        <img src="/api/placeholder/400/300" class="card-img-top" alt="Facial Treatment">
                                        <div class="card-body">
                                            <h5 class="card-title">ทรีตเมนต์ผิวหน้า</h5>
                                            <p class="card-text">ปรนนิบัติผิวหน้าด้วยทรีตเมนต์เฉพาะบุคคล</p>
                                            <a href="javascript:void(0)" class="btn btn-primary waves-effect">อ่านเพิ่มเติม</a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Service Card 2 -->
                                <div class="col-md-4">
                                    <div class="card service-card h-100">
                                        <img src="/api/placeholder/400/300" class="card-img-top" alt="Laser Treatment">
                                        <div class="card-body">
                                            <h5 class="card-title">เลเซอร์ผิวหน้า</h5>
                                            <p class="card-text">เทคโนโลยีเลเซอร์ล่าสุดเพื่อผิวกระจ่างใส</p>
                                            <a href="javascript:void(0)" class="btn btn-primary waves-effect">อ่านเพิ่มเติม</a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Service Card 3 -->
                                <div class="col-md-4">
                                    <div class="card service-card h-100">
                                        <img src="/api/placeholder/400/300" class="card-img-top" alt="Body Treatment">
                                        <div class="card-body">
                                            <h5 class="card-title">ทรีตเมนต์ผิวกาย</h5>
                                            <p class="card-text">บำรุงผิวกายให้เปล่งปลั่งอย่างเป็นธรรมชาติ</p>
                                            <a href="javascript:void(0)" class="btn btn-primary waves-effect">อ่านเพิ่มเติม</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Promotion Section -->
                        <section id="promotion" class="mb-4">
                            <div class="card bg-primary">
                                <div class="row g-0">
                                    <div class="col-md-6 p-4 p-xl-5">
                                        <h2 class="text-white mb-4">โปรโมชั่นพิเศษประจำเดือน</h2>
                                        <div class="mb-4">
                                            <ul class="list-unstyled">
                                                <li class="d-flex align-items-center mb-3">
                                                    <i class="ti ti-check-circle text-white me-2"></i>
                                                    <span class="text-white">ส่วนลด 30% สำหรับทรีตเมนต์ใหม่</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-3">
                                                    <i class="ti ti-check-circle text-white me-2"></i>
                                                    <span class="text-white">ฟรี! คอร์สบำรุงผิวหน้า</span>
                                                </li>
                                                <li class="d-flex align-items-center mb-3">
                                                    <i class="ti ti-check-circle text-white me-2"></i>
                                                    <span class="text-white">รับคะแนนสะสม 2 เท่า</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <button class="btn btn-white waves-effect">จองโปรโมชั่นนี้</button>
                                    </div>
                                    <div class="col-md-6">
                                        <img src="/api/placeholder/600/400" alt="Promotion" class="img-fluid h-100 object-fit-cover">
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Doctors Section -->
                        <section id="doctors" class="mb-4">
                            <div class="text-center mb-4">
                                <h2 class="display-6 fw-bold">แพทย์ผู้เชี่ยวชาญ</h2>
                                <p class="text-muted">ทีมแพทย์มากประสบการณ์พร้อมดูแลคุณ</p>
                            </div>

                            <div class="row g-4">
                                <!-- Doctor 1 -->
                                <div class="col-md-4">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <img src="/api/placeholder/150/150" class="rounded-circle mb-3" alt="Doctor 1">
                                            <h5 class="card-title">พญ. สมหญิง รักษาดี</h5>
                                            <p class="card-text text-muted">ผู้เชี่ยวชาญด้านเลเซอร์</p>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-icon btn-outline-primary">
                                                    <i class="ti ti-brand-facebook"></i>
                                                </button>
                                                <button class="btn btn-icon btn-outline-primary">
                                                    <i class="ti ti-brand-instagram"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Doctor 3 -->
                                <div class="col-md-4">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <img src="/api/placeholder/150/150" class="rounded-circle mb-3" alt="Doctor 3">
                                            <h5 class="card-title">พญ. สมศรี มั่นใจ</h5>
                                            <p class="card-text text-muted">ผู้เชี่ยวชาญด้านผิวกาย</p>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-icon btn-outline-primary">
                                                    <i class="ti ti-brand-facebook"></i>
                                                </button>
                                                <button class="btn btn-icon btn-outline-primary">
                                                    <i class="ti ti-brand-instagram"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <!-- Contact Section -->
                        <section id="contact" class="mb-4">
                            <div class="row">
                                <!-- Contact Info -->
                                <div class="col-lg-4 col-md-6 mb-4">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">ติดต่อเรา</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex mb-3">
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-map-pin ti-md text-primary me-3"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">ที่อยู่</h6>
                                                    <p class="mb-0">123 ถนนสุขุมวิท แขวงคลองเตย<br>เขตคลองเตย กรุงเทพฯ 10110</p>
                                                </div>
                                            </div>
                                            <div class="d-flex mb-3">
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-phone ti-md text-primary me-3"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">โทรศัพท์</h6>
                                                    <p class="mb-0">02-123-4567</p>
                                                </div>
                                            </div>
                                            <div class="d-flex mb-3">
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-clock ti-md text-primary me-3"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">เวลาทำการ</h6>
                                                    <p class="mb-0">ทุกวัน 10:00 - 20:00 น.</p>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <i class="ti ti-mail ti-md text-primary me-3"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">อีเมล</h6>
                                                    <p class="mb-0">info@beautycare.com</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Appointment Form -->
                                <div class="col-lg-8 col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">จองคิวปรึกษา</h5>
                                        </div>
                                        <div class="card-body">
                                            <form id="appointmentForm" class="row g-3">
                                                <!-- Name -->
                                                <div class="col-md-6">
                                                    <label class="form-label" for="name">ชื่อ-นามสกุล</label>
                                                    <input type="text" class="form-control" id="name" placeholder="กรอกชื่อ-นามสกุล">
                                                </div>

                                                <!-- Phone -->
                                                <div class="col-md-6">
                                                    <label class="form-label" for="phone">เบอร์โทรศัพท์</label>
                                                    <input type="tel" class="form-control" id="phone" placeholder="กรอกเบอร์โทรศัพท์">
                                                </div>

                                                <!-- Email -->
                                                <div class="col-md-6">
                                                    <label class="form-label" for="email">อีเมล</label>
                                                    <input type="email" class="form-control" id="email" placeholder="กรอกอีเมล">
                                                </div>

                                                <!-- Service -->
                                                <div class="col-md-6">
                                                    <label class="form-label" for="service">บริการที่สนใจ</label>
                                                    <select class="form-select" id="service">
                                                        <option value="">เลือกบริการ</option>
                                                        <option value="facial">ทรีตเมนต์ผิวหน้า</option>
                                                        <option value="laser">เลเซอร์ผิวหน้า</option>
                                                        <option value="body">ทรีตเมนต์ผิวกาย</option>
                                                    </select>
                                                </div>

                                                <!-- Date -->
                                                <div class="col-md-6">
                                                    <label class="form-label" for="date">วันที่ต้องการจอง</label>
                                                    <input type="date" class="form-control" id="date">
                                                </div>

                                                <!-- Time -->
                                                <div class="col-md-6">
                                                    <label class="form-label" for="time">เวลาที่ต้องการจอง</label>
                                                    <select class="form-select" id="time">
                                                        <option value="">เลือกเวลา</option>
                                                        <option value="10:00">10:00</option>
                                                        <option value="11:00">11:00</option>
                                                        <option value="13:00">13:00</option>
                                                        <option value="14:00">14:00</option>
                                                        <option value="15:00">15:00</option>
                                                        <option value="16:00">16:00</option>
                                                        <option value="17:00">17:00</option>
                                                        <option value="18:00">18:00</option>
                                                    </select>
                                                </div>

                                                <!-- Message -->
                                                <div class="col-12">
                                                    <label class="form-label" for="message">ข้อความเพิ่มเติม</label>
                                                    <textarea class="form-control" id="message" rows="3" placeholder="กรอกข้อความเพิ่มเติม (ถ้ามี)"></textarea>
                                                </div>

                                                <!-- Submit Button -->
                                                <div class="col-12">
                                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                                        <i class="ti ti-send me-1"></i> ส่งข้อมูลการจองคิว
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div class="footer-container d-flex align-items-center justify-content-between py-3 flex-md-row flex-column">
                                <div class="text-center text-md-start mb-3 mb-md-0">
                                    © 2024 Beauty Care Clinic. All rights reserved.
                                </div>
                                <div class="d-flex">
                                    <a href="javascript:void(0)" class="btn btn-icon btn-sm btn-text-secondary rounded-pill me-2">
                                        <i class="ti ti-brand-facebook"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-icon btn-sm btn-text-secondary rounded-pill me-2">
                                        <i class="ti ti-brand-instagram"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-icon btn-sm btn-text-secondary rounded-pill">
                                        <i class="ti ti-brand-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </footer>
                    <!-- / Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout container -->
        </div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../assets/vendor/libs/node-waves/node-waves.js"></script>
    <script src="../assets/vendor/libs/hammer/hammer.js"></script>
    <script src="../assets/vendor/js/menu.js"></script>

    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/swiper/swiper.js"></script>
    <script src="../assets/vendor/libs/toastr/toastr.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Custom JS -->
    <script>
        // Form Submit Handler
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Swiper
    const swiper = new Swiper('.hero-swiper', {
        loop: true,
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        speed: 1500,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            previousEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        on: {
            slideChange: function() {
                // Reset and play animations
                const activeSlide = this.slides[this.activeIndex];
                const content = activeSlide.querySelector('.hero-text-content');
                content.style.opacity = '0';
                content.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    content.style.transition = 'all 1s ease';
                    content.style.opacity = '1';
                    content.style.transform = 'translateY(0)';
                }, 300);
            }
        }
    });

    // Parallax effect for floating circles
    document.addEventListener('mousemove', function(e) {
        const circles = document.querySelectorAll('.floating-circle');
        const mouseX = e.clientX / window.innerWidth;
        const mouseY = e.clientY / window.innerHeight;

        circles.forEach((circle, index) => {
            const speed = (index + 1) * 20;
            const x = (mouseX - 0.5) * speed;
            const y = (mouseY - 0.5) * speed;
            circle.style.transform = `translate(${x}px, ${y}px)`;
        });
    });

    // Smooth reveal for stats
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('stat-reveal');
            }
        });
    }, {
        threshold: 0.5
    });

    document.querySelectorAll('.stat-item').forEach(stat => {
        observer.observe(stat);
    });
});
    </script>
</body>