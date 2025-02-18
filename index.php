<!DOCTYPE html>
<html lang="th" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter">
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
    <!-- <link rel="stylesheet" href="../assets/vendor/fonts/tabler-icons.css" /> -->
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <!-- Custom CSS -->
    <style>
/* Global Styles */
body {
    font-family: 'Prompt', sans-serif;
}

/* ===============================================
   Navbar Styles
   =============================================== */
.layout-navbar {
    position: fixed;
    top: 0;
    right: 0;
    left: 0;
    z-index: 1050;
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.navbar-brand {
    position: relative;
    z-index: 1051;
}

.app-brand-logo {
    border-radius: 8px;
    transition: transform 0.3s ease;
}

/*.navbar-brand img {
    transition: all 0.3s ease;
}*/

.app-brand-text {
    color: #333;
    font-size: 1.2rem;
    transition: color 0.3s ease;
}

.navbar-toggler {
    padding: 0.5rem;
    margin-right: 1rem;
    border-radius: 8px;
    background: rgba(147, 51, 234, 0.1);
    transition: all 0.3s ease;
}

.navbar-toggler:hover {
    background: rgba(147, 51, 234, 0.2);
}

.navbar-toggler:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.25);
}

.navbar-toggler[aria-expanded="true"] {
    background-color: rgba(147, 51, 234, 0.2);
}

.navbar-toggler[aria-expanded="true"] i {
    transform: rotate(90deg);
}

.navbar-toggler i {
    font-size: 1.5rem;
    color: #333;
    transition: transform 0.3s ease;
}

.navbar-nav .nav-link {
    color: #666;
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: all 0.3s ease;
    position: relative;
}


.navbar-nav .nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #ff8fb1, #ff5b94);
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.navbar-nav .nav-link:hover {
    color: #ff5b94;
}

.navbar-nav .nav-link:hover::after {
    width: 100%;
}

.login-btn {
    background: linear-gradient(120deg, #ff8fb1, #ff5b94);
    color: white !important;
    border-radius: 50px;
    padding: 0.5rem 1.5rem !important;
    transition: all 0.3s ease;
}

.login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 91, 148, 0.3);
}



/* Mobile Styles */
@media (max-width: 991.98px) {
    .navbar-collapse {
        position: absolute;
        top: 100%;  /* แสดงถัดจาก navbar */
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        padding: 1rem;
        border-radius: 0 0 16px 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateY(-20px);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .navbar-collapse.show {
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
    }

    /* Close Button Styles */
    .btn-close {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        z-index: 1051;
        display: none;
    }
    .navbar-nav {
        padding: 0.5rem 0;
    }

    .navbar-nav .nav-link {
        padding: 0.8rem 1.5rem;
        text-align: center;
    }

    .navbar-collapse.show .btn-close {
        display: block;
    }

    /* Toggle Button Styles */
    .navbar-toggler {
        z-index: 1052;
    }

    .navbar-toggler[aria-expanded="true"] {
        color: #ff5b94;
    }

    .navbar-toggler[aria-expanded="true"] i {
        transform: rotate(90deg);
    }

    .login-btn {
        margin: 0.5rem 1.5rem;
        width: auto;
        text-align: center;
    }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.navbar-nav .nav-link {
    animation: fadeIn 0.3s ease forwards;
    animation-delay: calc(var(--item-index) * 0.1s);
}







/* ===============================================
   Hero Section Styles
   =============================================== */
.hero-section {
    position: relative;
    height: 80vh;
    overflow: hidden;
    margin-top: 70px;
}

.hero-swiper,
.swiper-wrapper,
.swiper-slide {
    height: 100%;
}

.swiper-slide {
    position: relative;
}

/* Background Layer */
.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    z-index: 1;
}

/* Gradient Overlay */
.overlay-gradient {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, 
        rgba(0,0,0,0.8) 0%, 
        rgba(0,0,0,0.6) 50%, 
        rgba(0,0,0,0.4) 100%
    );
    z-index: 2;
}

/* Content Wrapper */
.hero-content-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    z-index: 3;
    display: flex;
    align-items: center;
}

/* Text Content */
.hero-text-content {
    color: #fff;
    opacity: 0;
    transform: translateY(20px);
    transition: all 1s ease;
}

.hero-text-content.active {
    opacity: 1;
    transform: translateY(0);
}

.hero-subtitle-top {
    font-size: 1.1rem;
    text-transform: uppercase;
    letter-spacing: 4px;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
    margin-bottom: 1.5rem;
    display: inline-block;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.hero-subtitle-top::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 50px;
    height: 2px;
    background: linear-gradient(90deg, rgba(255,255,255,0), #fff, rgba(255,255,255,0));
}

.hero-title {
    font-size: 4rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    color: #FFFFFF;
    text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
}

.gradient-text {
    background: linear-gradient(120deg, #ff8fb1, #ff5b94);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 800;
}

.hero-subtitle {
    font-size: 1.25rem;
    line-height: 1.8;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 2rem;
    text-shadow: 1px 1px 4px rgba(0,0,0,0.2);
}

.hero-title .gradient-text {
    background: linear-gradient(120deg, #ff8fb1, #ff5b94, #ff8fb1);
    background-size: 200% auto;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradient 3s ease infinite;
    text-shadow: none;
    font-weight: 900;
}

@keyframes gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Hero Buttons */
.hero-buttons {
    display: flex;
    gap: 1rem;
    margin-bottom: 3rem;
}

.btn-gradient {
    background: linear-gradient(120deg, #ff8fb1, #ff5b94);
    border: none;
    color: white;
    position: relative;
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
    gap: 2rem;
}

.stat-item {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Hero Image */
.hero-image-wrapper {
    position: relative;
    height: 80%;
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

/* Floating Elements */
.floating-elements {
    position: absolute;
    width: 100%;
    height: 100%;
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

/* Treatment Features */
.treatment-features {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 2rem;
}

.feature-item {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    color: #FFFFFF;
}

/* ===============================================
   Services Section Styles
   =============================================== */
.service-card {
    transition: transform 0.3s ease;
    height: 100%;
    overflow: hidden;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.service-card:hover {
    transform: translateY(-5px);
}

.service-card .card-img-top {
    height: 200px;
    object-fit: cover;
}

.service-card .card-body {
    padding: 2rem;
}

.service-card .card-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

/* ===============================================
   Promotion Section Styles
   =============================================== */
.promotion-section .card {
    overflow: hidden;
    border: none;
    border-radius: 15px;
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
    width: 150px;
    height: 150px;
    object-fit: cover;
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
    border-radius: 15px;
    overflow: hidden;
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

/* Form Styles */
.form-control, .form-select {
    border-radius: 10px;
    padding: 0.75rem 1rem;
    border: 1px solid rgba(0, 0, 0, 0.1);
}

.form-control:focus, .form-select:focus {
    border-color: #ff8fb1;
    box-shadow: 0 0 0 0.2rem rgba(255, 143, 177, 0.25);
}

/* ===============================================
   Footer Styles
   =============================================== */
.footer {
    background: #fff;
    border-top: 1px solid #eee;
    padding: 2rem 0;
}

.footer .btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.footer .btn-icon:hover {
    background: rgba(255, 143, 177, 0.1);
    color: #ff8fb1;
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

.fade-in {
    animation: fadeIn 1s ease forwards;
}

.slide-up {
    animation: slideUp 0.8s ease forwards;
}

/*@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}*/

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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
   Responsive Styles
   =============================================== */
/* Desktop Styles */
@media (min-width: 1200px) {
/*    .navbar-nav .nav-link {
        position: relative;
        margin: 0 0.5rem;
    }*/

    .navbar-nav .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: 0;
        left: 50%;
        background: #ff8fb1;
        transition: all 0.3s ease;
        transform: translateX(-50%);
    }

    .navbar-nav .nav-link:hover::after {
        width: 100%;
    }
}

/* Tablet Styles (ต่อ) */
@media (max-width: 991.98px) {
    /* ปรับ Overlay ให้เข้มขึ้น */
    .overlay-gradient {
        background: linear-gradient(to bottom,
            rgba(0, 0, 0, 0.2) 0%,
            rgba(0, 0, 0, 0.7) 35%,
            rgba(0, 0, 0, 0.85) 100%
        );
    }

    /* ปรับปรุงการแสดงผลข้อความ */
    .hero-text-content {
        text-align: left;
        padding: 0 1rem;
    }

    .hero-subtitle-top {
        font-size: 1rem;
        letter-spacing: 3px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        color: rgba(255, 255, 255, 0.9);
    }

    .hero-title {
        font-size: 3.5rem;
        line-height: 1.3;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        margin-bottom: 1rem;
    }

    .gradient-text {
        text-shadow: none;
        background: linear-gradient(120deg, #ff8fb1, #ff5b94);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-subtitle {
        font-size: 1.1rem;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.95);
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        margin-bottom: 2rem;
    }

    /* ปรับปรุงปุ่ม */
    .hero-buttons {
        gap: 1rem;
        margin-bottom: 2.5rem;
    }

    .btn-gradient {
        width: 100%;
        padding: 1rem 1.5rem;
        font-weight: 600;
        background: linear-gradient(120deg, #ff8fb1, #ff5b94);
        border: none;
        box-shadow: 0 4px 15px rgba(255, 143, 177, 0.3);
    }

    .btn-outline-light {
        width: 100%;
        padding: 1rem 1.5rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* ปรับปรุง Stats */
    .hero-stats {
/*        display: grid;*/
        grid-template-columns: repeat(1, 1fr);
        gap: 1rem;
        padding: 0 1rem;
    }

    .stat-item {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        padding: 1.25rem;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(120deg, #fff, #ff8fb1);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
    }

    .text-gradient {
        background: linear-gradient(120deg, #ff8fb1, #ff5b94);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
    }

    .stat-label {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1rem;
        font-weight: 500;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    /* ปรับปรุง Feature Items */
    .treatment-features {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1rem;
        padding: 0 1rem;
        margin-top: 2rem;
    }

    .feature-item {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .feature-item i {
        font-size: 1.5rem;
        color: #ff8fb1;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .feature-item span {
        color: rgba(255, 255, 255, 0.95);
        font-size: 1rem;
        font-weight: 500;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    /* Swiper Pagination */
    .swiper-pagination {
        bottom: 20px !important;
    }

    .swiper-pagination-bullet {
        width: 8px;
        height: 8px;
        background: rgba(255, 255, 255, 0.5);
    }

    .swiper-pagination-bullet-active {
        background: #ff8fb1;
        transform: scale(1.2);
    }
}

/* Mobile Styles */
@media (max-width: 767.98px) {
    .hero-content-overlay {
        padding: 3.5rem 0;
    }

    .hero-title {
        font-size: 2.8rem;  /* ลดจาก 4rem */
        line-height: 1.3;
        margin-bottom: 1rem;
    }

    .hero-subtitle-top {
        font-size: 0.9rem;  /* ลดจาก 1.1rem */
        letter-spacing: 2px;
    }

/*    .hero-buttons {
        flex-direction: column;
    }*/

    .hero-buttons .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }

    .treatment-features {
        flex-direction: column;
        align-items: center;
    }

    .feature-item {
        width: 100%;
        justify-content: center;
    }

    .swiper-button-next,
    .swiper-button-prev {
        width: 40px;
        height: 40px;
        display: none;
    }

    .swiper-button-next:after,
    .swiper-button-prev:after {
        font-size: 1.2rem;
    }

    .promotion-section .card-body {
        padding: 1.5rem;
    }
}

/* Small Mobile Styles */
@media (max-width: 575.98px) {
    .hero-section {
        margin-top: 60px;
    }

    .hero-content-overlay {
        padding: 3rem 0;
    }

    .hero-title {
        font-size: 2.4rem;
    }

    .hero-subtitle-top {
        font-size: 0.8rem;
        letter-spacing: 2px;
    }

    .hero-subtitle {
        font-size: 1rem;
        line-height: 1.6;
    }

    .hero-stats {
        margin-top: 2rem;
    }

    .stat-item {
        flex: 1 1 100%;
    }

    .stat-number {
        font-size: 1.5rem;
    }

    .floating-circle {
        display: none;
    }

    .service-card .card-img-top {
        height: 160px;
    }

    .service-card .card-body {
        padding: 1.5rem;
    }

    .footer {
        text-align: center;
    }

    .footer .d-flex {
        justify-content: center;
    }

    .footer-container {
        flex-direction: column;
    }

    .footer .btn-icon {
        margin: 0.5rem;
    }
}

@media (max-width: 375px) {
    .hero-title {
        font-size: 2rem;
    }

    .hero-subtitle {
        font-size: 0.9rem;
    }

    .stat-number {
        font-size: 2rem;
    }

    .feature-item {
        padding: 0.75rem 1rem;
    }
}

/* Utility Classes */
.text-gradient {
    background: linear-gradient(120deg, #ff8fb1, #ff5b94);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.bg-gradient {
    background: linear-gradient(120deg, #ff8fb1, #ff5b94);
}

.backdrop-blur {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #ff8fb1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #ff5b94;
}

/* Focus Styles */
:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(255, 143, 177, 0.25);
}

/* Touch Device Optimizations */
@media (hover: none) {
    .btn-gradient:hover {
        transform: none;
    }

    .service-card:hover {
        transform: none;
    }

    .doctors-section .card:hover .rounded-circle {
        transform: none;
    }
}





/* Services Section Styles */
.services-section {
    padding: 80px 0;
    background: linear-gradient(180deg, rgba(255,255,255,0) 0%, rgba(247,250,255,0.5) 100%);
}

/* Service Card Styles */
.service-card {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
}

.service-card:hover {
    transform: translateY(-10px) scale(1.01);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

/* Card Image Wrapper */
.card-image-wrapper {
    position: relative;
    overflow: hidden;
}

.card-image-wrapper img {
    transition: all 0.5s ease;
}

.service-card:hover .card-image-wrapper img {
    transform: scale(1.1);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.5) 100%);
    opacity: 0;
    transition: all 0.3s ease;
}

.service-card:hover .image-overlay {
    opacity: 1;
}

/* Badges */
.popular-badge, .discount-badge, .new-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    padding: 8px 15px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    z-index: 2;
}

.popular-badge {
    background: linear-gradient(45deg, #FFD700, #FFA500);
    color: #fff;
}

.discount-badge {
    background: linear-gradient(45deg, #FF6B6B, #FF8787);
    color: #fff;
}

.new-badge {
    background: linear-gradient(45deg, #69db7c, #37b24d);
    color: #fff;
}

/* Service Icon */
.service-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(45deg, #ff8fb1, #ff5b94);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    margin-top: -50px;
    position: relative;
    z-index: 2;
    box-shadow: 0 10px 20px rgba(255, 91, 148, 0.2);
}

.service-icon i {
    font-size: 24px;
    color: #fff;
}

/* Service Features */
.service-features {
    display: flex;
    gap: 15px;
    margin: 20px 0;
}

.feature {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: #525252;
    background: #fff;
    padding: 8px 12px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.feature i {
    color: #ff5b94;
    font-size: 0.8rem;
}

/* Service Price */
.service-price {
    font-size: 1.1rem;
    color: #525252;
    margin-bottom: 15px;
    font-weight: 500;
}

.service-price .price {
    font-size: 1.4rem;
    font-weight: 700;
    color: #ff5b94;
}

/* Service Rating */
.service-rating {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.stars {
    color: #ffd700;
}

.rating-count {
    font-size: 0.9rem;
    color: #666;
}

/* Card Actions */
.card-actions {
    display: flex;
    gap: 10px;
}

.card-actions .btn {
    flex: 1;
    border-radius: 12px;
    padding: 10px 20px;
}

/* Category Navigation */
.category-nav .btn-group {
    background: #fff;
    padding: 5px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
}

.category-nav .btn {
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 500;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .service-card {
        margin-bottom: 20px;
    }

    .card-actions {
        flex-direction: column;
    }

    .service-features {
        flex-direction: column;
        gap: 10px;
    }
}

/* Animation for service cards */
@keyframes cardFloat {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes iconPulse {
    0% {
        transform: scale(1);
        box-shadow: 0 10px 20px rgba(255, 91, 148, 0.2);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 15px 30px rgba(255, 91, 148, 0.3);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 10px 20px rgba(255, 91, 148, 0.2);
    }
}

@keyframes gradientShift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.service-card {
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 0.6s ease forwards;
}

.service-card:nth-child(1) { animation-delay: 0.2s; }
.service-card:nth-child(2) { animation-delay: 0.4s; }
.service-card:nth-child(3) { animation-delay: 0.6s; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Enhanced Hover Effects */
.service-card:hover .service-icon {
    animation: iconPulse 1.5s infinite;
}

.service-card:hover .card-title {
    background: linear-gradient(120deg, #ff8fb1, #ff5b94, #ff8fb1);
    background-size: 200% 100%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradientShift 2s linear infinite;
}

/* Glass Morphism Effects */
.service-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 20px;
    background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.1),
        rgba(255, 255, 255, 0.05)
    );
    opacity: 0;
    transition: opacity 0.3s ease;
}

.service-card:hover::before {
    opacity: 1;
}

/* Enhanced Button Effects */
.card-actions .btn {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.card-actions .btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
}

.card-actions .btn:hover::before {
    width: 300px;
    height: 300px;
}

/* Enhanced Feature Items */
.feature {
    padding: 8px 15px;
    background: rgba(255, 91, 148, 0.05);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.feature:hover {
    background: rgba(255, 91, 148, 0.1);
    transform: translateX(5px);
}

/* Price Tag Animation */
.service-price .price {
    position: relative;
    display: inline-block;
}

.service-price .price::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, #ff8fb1, #ff5b94);
    transform: scaleX(0);
    transform-origin: right;
    transition: transform 0.3s ease;
}

.service-card:hover .service-price .price::after {
    transform: scaleX(1);
    transform-origin: left;
}

/* Rating Stars Animation */
.stars i {
    transition: all 0.3s ease;
    transform-origin: center;
}

.service-card:hover .stars i {
    animation: starPulse 1s ease infinite;
}

@keyframes starPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

.stars i:nth-child(1) { animation-delay: 0.1s; }
.stars i:nth-child(2) { animation-delay: 0.2s; }
.stars i:nth-child(3) { animation-delay: 0.3s; }
.stars i:nth-child(4) { animation-delay: 0.4s; }
.stars i:nth-child(5) { animation-delay: 0.5s; }

/* Category Navigation Enhancement */
.category-nav .btn {
    position: relative;
    overflow: hidden;
    z-index: 1;
}

.category-nav .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        rgba(255, 255, 255, 0.1),
        rgba(255, 255, 255, 0.2),
        rgba(255, 255, 255, 0.1)
    );
    transition: left 0.6s ease;
    z-index: -1;
}

.category-nav .btn:hover::before {
    left: 100%;
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    .service-card {
        background: rgba(255, 255, 255, 0.05);
    }
    
    .card-body {
        background: rgba(255, 255, 255, 0.02);
    }
    
    .feature {
        background: rgba(255, 255, 255, 0.05);
    }
    
    .service-price {
        color: rgba(255, 255, 255, 0.9);
    }
    
    .feature {
        color: rgba(255, 255, 255, 0.7);
    }
}


/* Promotion Section Styles */
.promotion-section {
    position: relative;
    overflow: hidden;
}

/* Featured Promotion Styles */
.promotion-card {
    background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.95) 100%);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.promotion-card.premium-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border: 1px solid rgba(255,91,148,0.1);
}

.promotion-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 30px 60px rgba(0,0,0,0.15);
}

/* Countdown Timer */
.countdown-timer {
    background: rgba(255,91,148,0.1);
    padding: 8px 15px;
    border-radius: 50px;
    font-size: 0.9rem;
    color: #ff5b94;
}

.countdown-timer span {
    font-weight: 600;
    margin: 0 2px;
}

/* Promotion Content */
.promotion-title {
    font-size: 2rem;
    font-weight: 700;
    background: linear-gradient(120deg, #ff5b94, #ff8fb1);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1rem;
}

.promotion-description {
    color: #666;
    line-height: 1.6;
}

/* Price Styling */
.promotion-price {
    position: relative;
    margin: 2rem 0;
    padding-left: 1rem; /* เพิ่มระยะห่างด้านซ้ายเพื่อไม่ให้ถูก swiper button บัง */
}

.original-price {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    opacity: 0.6;
}

.current-price {
    display: flex;
    align-items: center;
}

.current-price .price {
    font-size: 3.5rem; /* เพิ่มขนาดตัวเลข */
    font-weight: 700;
    color: #5E5CEF; /* สีตามในภาพ */
    margin-right: 1rem;
}

.discount-badge {
    background: linear-gradient(120deg, #ff5b94, #ff8fb1);
    color: white;
    padding: 5px 10px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
}

/* Features List */
.promotion-features {
    display: grid;
    gap: 1rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 15px;
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.feature-item:hover {
    transform: translateX(5px);
    background: rgba(255,91,148,0.1);
}

.feature-item i {
    font-size: 1.2rem;
}

/* Image Section */
.promotion-image {
    position: relative;
    height: 100%;
}

.promotion-image img {
    height: 100%;
    object-fit: cover;
}

.trusted-badge {
    position: absolute;
    bottom: 20px;
    right: 20px;
    background: white;
    padding: 15px;
    border-radius: 15px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    text-align: center;
}

/* Swiper Navigation */
.promotion-swiper .swiper-button-prev,
.promotion-swiper .swiper-button-next {
    width: 40px;
    height: 40px;
    background: white;
    border-radius: 50%;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    top: 45%; /* ปรับตำแหน่งให้อยู่กึ่งกลางของ slide */
}

.promotion-swiper .swiper-button-prev {
    left: -10px; /* ย้ายปุ่มซ้ายออกนอก content */
}

.promotion-swiper .swiper-button-next {
    right: -10ยป; /* ย้ายปุ่มขวาออกนอก content */
}

.promotion-swiper .swiper-button-prev:after,
.promotion-swiper .swiper-button-next:after {
    font-size: 1rem;
    color: #5E5CEF;
}

.promotion-swiper .swiper-pagination-bullet {
    width: 10px;
    height: 10px;
    background: #ff5b94;
}

/* Responsive Adjustments */
@media (max-width: 991.98px) {
    .promotion-title {
        font-size: 1.75rem;
    }
    
    .promotion-content {
        text-align: center;
    }
    
    .feature-item {
        justify-content: center;
    }
    
    .promotion-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .promotion-actions .btn {
        width: 100%;
    }
}

@media (max-width: 767.98px) {
    .promotion-title {
        font-size: 1.5rem;
    }
    
    .current-price .display-4 {
        font-size: 2rem;
    }
}









/* Doctors Section Styles */
.doctors-section {
    padding: 80px 0;
    background: linear-gradient(180deg, rgba(255,255,255,0) 0%, rgba(247,250,255,0.5) 100%);
}

/* Hero Section */
.doctors-hero {
    position: relative;
    padding: 60px 0;
    background: linear-gradient(45deg, rgba(255,143,177,0.1) 0%, rgba(255,91,148,0.1) 100%);
    border-radius: 30px;
    overflow: hidden;
}

.doctors-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('/api/placeholder/1200/400') center/cover no-repeat;
    opacity: 0.1;
}

/* Doctor Card Styles */
.doctor-card {
    perspective: 1500px;
    height: 600px;
}

.doctor-card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transition: transform 0.8s;
    transform-style: preserve-3d;
}

.doctor-card:hover .doctor-card-inner {
    transform: rotateY(180deg);
}

.doctor-card-front,
.doctor-card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    border-radius: 20px;
    overflow: hidden;
}

.doctor-card-front {
    background: white;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.doctor-card-back {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    transform: rotateY(180deg);
    padding: 30px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Doctor Image Styles */
.doctor-image-wrapper {
    position: relative;
    width: 100%;
    padding-top: 100%;
    overflow: hidden;
}

.doctor-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.doctor-card:hover .doctor-image {
    transform: scale(1.05);
}

/* Certification Badge */
.certification-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255,255,255,0.9);
    padding: 8px 15px;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    color: #ff5b94;
    display: flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 5px 15px rgba(255,91,148,0.2);
}

/* Doctor Info Styles */
.doctor-info {
    padding: 25px;
}

.doctor-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
}

.doctor-specialty {
    color: #666;
    font-size: 1rem;
    margin-bottom: 15px;
}

.doctor-credentials {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 15px;
}

.credential-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: #666;
}

/* Expertise Tags */
.expertise-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 15px;
}

.tag {
    background: rgba(255,91,148,0.1);
    color: #ff5b94;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Achievements Styles */
.achievements {
    flex: 1;
}

.achievement-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
}

.achievement-list {
    list-style: none;
    padding: 0;
    margin: 0 0 20px 0;
}

.achievement-list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 15px;
    color: #666;
    font-size: 0.95rem;
}

.achievement-list li i {
    color: #ff5b94;
    margin-top: 4px;
}

/* Certification Logos */
.certification-list {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.cert-logo {
    height: 40px;
    object-fit: contain;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.cert-logo:hover {
    opacity: 1;
}

/* Social Links */
.social-links {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 20px;
}

.social-link {
    width: 40px;
    height: 40px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ff5b94;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.social-link:hover {
    background: #ff5b94;
    color: white;
    transform: translateY(-3px);
}

/* Animations */
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

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes shine {
    0% {
        background-position: -100% 50%;
    }
    100% {
        background-position: 200% 50%;
    }
}

.doctor-card {
    opacity: 0;
    animation: fadeInUp 0.6s ease forwards;
}

.doctor-card:nth-child(1) { animation-delay: 0.2s; }
.doctor-card:nth-child(2) { animation-delay: 0.4s; }
.doctor-card:nth-child(3) { animation-delay: 0.6s; }

.certification-badge {
    animation: float 3s ease-in-out infinite;
}

/* Glass Morphism Effects */
.doctor-card-front::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        135deg,
        rgba(255, 255, 255, 0.1),
        rgba(255, 255, 255, 0.05)
    );
    z-index: 1;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.doctor-card:hover .doctor-card-front::before {
    opacity: 1;
}

/* Enhanced Hover Effects */
.doctor-name {
    position: relative;
    display: inline-block;
}

.doctor-name::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, #ff8fb1, #ff5b94);
    transform: scaleX(0);
    transform-origin: right;
    transition: transform 0.3s ease;
}

.doctor-card:hover .doctor-name::after {
    transform: scaleX(1);
    transform-origin: left;
}

.tag {
    position: relative;
    overflow: hidden;
    background: linear-gradient(90deg, 
        rgba(255,91,148,0.1), 
        rgba(255,143,177,0.1), 
        rgba(255,91,148,0.1)
    );
    background-size: 200% 100%;
}

.tag:hover {
    animation: shine 1.5s infinite;
}

/* Responsive Styles */
@media (max-width: 991.98px) {
    .doctor-card {
        height: 550px;
    }

    .doctor-info {
        padding: 20px;
    }

    .doctor-name {
        font-size: 1.3rem;
    }

    .expertise-tags {
        gap: 6px;
    }

    .tag {
        padding: 4px 10px;
        font-size: 0.75rem;
    }
}

@media (max-width: 767.98px) {
    .doctors-hero {
        padding: 40px 0;
    }

    .doctor-card {
        height: 500px;
        margin-bottom: 30px;
    }

    .achievement-list li {
        font-size: 0.9rem;
    }

    .certification-list {
        flex-wrap: wrap;
        justify-content: center;
    }

    .social-links {
        margin-top: 15px;
    }
}

@media (max-width: 575.98px) {
    .doctor-card {
        height: 450px;
    }

    .doctor-info {
        padding: 15px;
    }

    .doctor-credentials {
        margin-bottom: 10px;
    }

    .credential-item {
        font-size: 0.8rem;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    .doctor-card-front,
    .doctor-card-back {
        background: rgba(255, 255, 255, 0.05);
    }
    
    .doctor-name {
        color: rgba(255, 255, 255, 0.9);
    }
    
    .doctor-specialty,
    .credential-item,
    .achievement-list li {
        color: rgba(255, 255, 255, 0.7);
    }
    
    .certification-badge {
        background: rgba(255, 255, 255, 0.1);
    }
    
    .social-link {
        background: rgba(255, 255, 255, 0.1);
    }
}

/* Print Styles */
@media print {
    .doctor-card {
        break-inside: avoid;
        page-break-inside: avoid;
    }

    .doctor-card-inner {
        transform: none !important;
    }

    .doctor-card-back {
        display: none;
    }

    .certification-badge,
    .social-links {
        display: none;
    }
}

/* Accessibility Improvements */
.doctor-card-inner:focus {
    outline: 3px solid #ff5b94;
    outline-offset: 3px;
}

@media (prefers-reduced-motion: reduce) {
    .doctor-card-inner {
        transition: none;
    }
    
    .doctor-card:hover .doctor-card-inner {
        transform: none;
    }
    
    .certification-badge {
        animation: none;
    }
    
    .tag:hover {
        animation: none;
    }
}






/* Contact Section Styles */
.contact-section {
    background: linear-gradient(180deg, rgba(255,255,255,0) 0%, rgba(247,250,255,0.5) 100%);
}

/* Hero Section */
.contact-hero {
    padding: 60px 0;
    background: linear-gradient(45deg, rgba(255,143,177,0.1) 0%, rgba(255,91,148,0.1) 100%);
    border-radius: 30px;
    margin-bottom: 50px;
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

/* Branch Navigation */
.branch-navigation {
    padding: 20px 0;
}

.branch-tabs {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 15px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

.branch-tab {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: #ffffff;
    border: 1px solid rgba(82, 82, 255, 0.2);
    border-radius: 50px;
    color: #666;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.branch-tab i {
    color: #5252ff;
    font-size: 1rem;
}

.branch-tab:hover {
    background: rgba(82, 82, 255, 0.05);
    transform: translateY(-2px);
}

.branch-tab.active {
    background: #5252ff;
    color: white;
    border-color: transparent;
}

.branch-tab.active i {
    color: white;
}

/* Branch Cards */
.branch-card {
    border-radius: 15px;
    transition: all 0.3s ease;
    background: white;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
}

.branch-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 91, 148, 0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.branch-info .info-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.branch-info .info-item i {
    margin-top: 4px;
}

.branch-info .info-item p {
    color: #666;
    font-size: 0.95rem;
    line-height: 1.5;
}

/* Map Card */
.map-card {
    border-radius: 15px;
    overflow: hidden;
    position: relative;
    min-height: 600px;
}

.branch-map {
    width: 100%;
    height: 600px;
}

.branch-map iframe {
    width: 100%;
    height: 100%;
    border: 0;
}

.map-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.map-message {
    padding: 30px;
}

.map-message i {
    font-size: 3rem;
    color: #5252ff;
    margin-bottom: 15px;
}

.map-message p {
    color: #666;
    font-size: 1.1rem;
    margin: 0;
}

/* Badges */
.badge {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-weight: 500;
}

/* Animations */
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

/* Responsive Styles */
@media (max-width: 991.98px) {
    .branch-navigation {
        overflow-x: auto;
        white-space: nowrap;
        padding-bottom: 1rem;
    }
    
    .branch-tabs {
        flex-wrap: nowrap;
        justify-content: flex-start;
        padding: 0;
        margin: 0;
    }
    
    .branch-tab {
        padding: 8px 16px;
    }
    
    .map-card {
        margin-top: 2rem;
        min-height: 400px;
    }
    
    .branch-map {
        height: 400px;
    }
}

@media (max-width: 767.98px) {
    .contact-hero {
        padding: 40px 0;
    }
    
    .floating-circle {
        display: none;
    }
    
    .branch-card {
        margin-bottom: 1rem;
    }
}

/* Utility Classes */
.text-gradient {
    background: linear-gradient(120deg, #ff8fb1, #ff5b94);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.bg-gradient {
    background: linear-gradient(120deg, #ff8fb1, #ff5b94);
}

/* Accessibility Improvements */
@media (prefers-reduced-motion: reduce) {
    .floating-circle {
        animation: none;
    }
    
    .hover-lift:hover {
        transform: none;
    }
}





/* Enhanced Footer Styles */
.enhanced-footer {
    position: relative;
    background: linear-gradient(135deg, #ffffff 0%, #fff5f8 100%);
    font-family: 'Prompt', sans-serif;
}

/* Main Footer Section */
.footer-main {
    position: relative;
    padding: 4rem 0;
    overflow: hidden;
}

.footer-main::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ff8fb1' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.5;
}

/* Footer Brand */
.footer-logo {
    height: 60px;
    width: auto;
    margin-bottom: 1.5rem;
    border-radius: 10px;
}

.footer-about {
    color: #666;
    line-height: 1.6;
    font-size: 0.95rem;
}

/* Certification Badges */
.certification-badges {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.cert-badge {
    height: 40px;
    width: auto;
    opacity: 0.8;
    transition: all 0.3s ease;
}

.cert-badge:hover {
    opacity: 1;
    transform: translateY(-2px);
}

/* Footer Titles */
.footer-title {
    color: #333;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    position: relative;
}

.footer-title::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 40px;
    height: 2px;
    background: linear-gradient(90deg, #ff8fb1, #ff5b94);
    border-radius: 2px;
}

/* Footer Links */
.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 0.8rem;
}

.footer-links a {
    color: #666;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
}

.footer-links a::before {
    content: '';
    width: 0;
    height: 1px;
    background: linear-gradient(90deg, #ff8fb1, #ff5b94);
    margin-right: 0;
    transition: all 0.3s ease;
}

.footer-links a:hover {
    color: #ff5b94;
    transform: translateX(5px);
}

.footer-links a:hover::before {
    width: 15px;
    margin-right: 10px;
}

/* Contact Information */
.footer-contact {
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.contact-item i {
    color: #ff5b94;
    font-size: 1.2rem;
    margin-top: 0.2rem;
}

.contact-info {
    display: flex;
    flex-direction: column;
}

.contact-info .label {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 0.2rem;
}

.contact-info .value {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.contact-info a.value:hover {
    color: #ff5b94;
}

/* Footer Bottom Bar */
.footer-bottom {
    background: white;
    padding: 1.5rem 0;
    border-top: 1px solid rgba(0,0,0,0.05);
}

.footer-bottom-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.copyright {
    color: #666;
    font-size: 0.9rem;
}

.social-links {
    display: flex;
    gap: 1rem;
}

.social-link {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 50%;
    color: #666;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.social-link:hover {
    background: linear-gradient(135deg, #ff8fb1, #ff5b94);
    color: white;
    transform: translateY(-2px);
}

/* Back to Top Button */
.back-to-top {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 40px;
    height: 40px;
    background: white;
    border: none;
    border-radius: 50%;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    color: #666;
    font-size: 1.2rem;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 999;
}

.back-to-top.show {
    opacity: 1;
    visibility: visible;
}

.back-to-top:hover {
    background: linear-gradient(135deg, #ff8fb1, #ff5b94);
    color: white;
    transform: translateY(-3px);
}

/* Cookie Consent Banner */
.cookie-consent {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    padding: 1rem;
    z-index: 1000;
    transform: translateY(100%);
    transition: transform 0.3s ease;
}

.cookie-consent.show {
    transform: translateY(0);
}

.cookie-content {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    max-width: 1200px;
    margin: 0 auto;
}

.cookie-content p {
    margin: 0;
    font-size: 0.9rem;
    color: #666;
}

/* Responsive Styles */
@media (max-width: 991.98px) {
    .footer-main {
        padding: 3rem 0;
    }
    
    .footer-bottom-content {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}

@media (max-width: 767.98px) {
    .certification-badges {
        justify-content: center;
    }
    
    .footer-title {
        margin-top: 2rem;
    }
    
    .contact-item {
        align-items: flex-start;
    }
    
    .cookie-content {
        flex-direction: column;
        text-align: center;
    }
}

@media (max-width: 575.98px) {
    .footer-main {
        padding: 2rem 0;
    }
    
    .back-to-top {
        bottom: 20px;
        right: 20px;
    }
}
    </style>

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!-- <script src="../assets/vendor/js/template-customizer.js"></script> -->
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
                        <?php include 'service.php'; ?>

                        <!-- Promotion Section -->
                        <?php include 'promotion.php'; ?>

                        <!-- Doctors Section -->
                        <?php include 'doctors.php'; ?>

                        <!-- Contact Section -->
                        <?php include 'contact.php'; ?>

                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <?php include 'footer.php'; ?>
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

document.addEventListener('DOMContentLoaded', function() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    // Reset toggle state when collapse is hidden
    navbarCollapse.addEventListener('hidden.bs.collapse', function () {
        navbarToggler.setAttribute('aria-expanded', 'false');
    });

    // Set toggle state when collapse is shown
    navbarCollapse.addEventListener('shown.bs.collapse', function () {
        navbarToggler.setAttribute('aria-expanded', 'true');
    });

    // Handle clicks outside of navbar
    document.addEventListener('click', function(e) {
        if (!navbarCollapse.contains(e.target) && !navbarToggler.contains(e.target)) {
            navbarCollapse.classList.remove('show');
            navbarToggler.setAttribute('aria-expanded', 'false');
        }
    });
});


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
            prevEl: '.swiper-button-prev',
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
                
                if (content) {
                    content.style.opacity = '0';
                    content.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        content.style.transition = 'all 1s ease';
                        content.style.opacity = '1';
                        content.style.transform = 'translateY(0)';
                    }, 300);
                }
            },
            init: function() {
                // Animate first slide
                const firstSlide = this.slides[0];
                const content = firstSlide.querySelector('.hero-text-content');
                
                if (content) {
                    content.classList.add('fade-in');
                }
            }
        }
    });

    // Add parallax effect to floating circles
    document.addEventListener('mousemove', function(e) {
        if (window.innerWidth > 991) {
            const circles = document.querySelectorAll('.floating-circle');
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;

            circles.forEach((circle, index) => {
                const speed = (index + 1) * 20;
                const x = (mouseX - 0.5) * speed;
                const y = (mouseY - 0.5) * speed;
                circle.style.transform = `translate(${x}px, ${y}px)`;
            });
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    // Initialize Intersection Observer for animation
    const options = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, options);

    // Observe all service cards
    document.querySelectorAll('.service-card').forEach(card => {
        observer.observe(card);
    });

    // Category filter functionality
    const categoryButtons = document.querySelectorAll('.category-nav .btn');
    const serviceCards = document.querySelectorAll('.service-card');

    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');

            // Get category from button text
            const category = this.textContent.toLowerCase();

            // Filter cards
            serviceCards.forEach(card => {
                const cardCategory = card.dataset.category;
                if (category === 'ทั้งหมด' || cardCategory === category) {
                    card.closest('.col-lg-4').style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                } else {
                    card.closest('.col-lg-4').style.display = 'none';
                }
            });
        });
    });

    // Add hover effect for service cards
    serviceCards.forEach(card => {
        card.addEventListener('mousemove', function(e) {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
        });

        card.addEventListener('mouseleave', function() {
            card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
        });
    });

    // Add smooth scroll for buttons
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add loading animation for images
    document.querySelectorAll('.card-img-top').forEach(img => {
        img.addEventListener('load', function() {
            this.classList.add('loaded');
        });
    });
});


document.addEventListener('DOMContentLoaded', function() {
    // Initialize Maps
    const branchMapUrls = {
        'bangsan': 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2755.601840505429!2d100.92019789999999!3d13.2802328!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3102b52cf93566eb%3A0x73ccb3023b7fb473!2sD%20Care%20Clinic%20Bang%20Saen!5e1!3m2!1sth!2sth!4v1739707083989!5m2!1sth!2sth',

        'rangsit': 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2746.725196558729!2d100.6151524!3d14.040845200000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30e280034747f35b%3A0x75e94e699bf7a558!2sD%20Care%20Clinic!5e1!3m2!1sth!2sth!4v1739707126741!5m2!1sth!2sth',

        'lumlukka': 'https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d343.5034801021313!2d100.6559295!3d13.9317762!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x311d7db6fdfe1145%3A0xf611bcdc643dd776!2zSmlmZnkgKOC4iOC4tOC4n-C4n-C4teC5iCkg4Liq4Liy4LiC4Liy4Lib4LiX4Li44Lih4LiY4Liy4LiZ4Li1LeC4peC4s-C4peC4ueC4geC4geC4siDguITguKXguK3guIcgMg!5e1!3m2!1sth!2sth!4v1739707179157!5m2!1sth!2sth',

        'ramkhamhaeng': 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2749.659393865025!2d100.70543070000001!3d13.7939758!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x311d630049ac4d9d%3A0x6d631c831f086b72!2z4Lib4LiV4LiXLuC4iOC4tOC4n-C4n-C4teC5iCDguIHguKPguLjguIfguYDguJfguJ4t4Liq4Li44LiC4Liy4Lig4Li04Lia4Liy4LilIDMgKOC4oeC4tOC4quC4l-C4teC4mSk!5e1!3m2!1sth!2sth!4v1739707224329!5m2!1sth!2sth',

        'ekkamai': 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2749.470414038151!2d100.62019599999999!3d13.810005999999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x311d6277e4e73039%3A0xbe6f98039cf5ae5!2z4Lib4Lix4LmK4Lih4LiZ4LmJ4Liz4Lih4Lix4LiZIOC5geC4peC4sCDguK3guLXguKfguLUg4Lib4LiV4LiXLuC4geC4o-C4uOC4h-C5gOC4l-C4ni3guKPguLLguKHguK3guLTguJnguJfguKPguLIgMQ!5e1!3m2!1sth!2sth!4v1739707259817!5m2!1sth!2sth'
    };

        // Branch Navigation
    const branchTabs = document.querySelectorAll('.branch-tab');
    const branchCards = document.querySelectorAll('.branch-card');
    const branchMap = document.getElementById('branchMap');
    const defaultMapMessage = document.getElementById('defaultMapMessage');

    // ฟังก์ชันแยกสำหรับอัพเดทแผนที่
    function updateMap(branchKey) {
        if (branchKey && branchMapUrls[branchKey]) {
            defaultMapMessage.style.display = 'none';
            branchMap.innerHTML = `<iframe src="${branchMapUrls[branchKey]}" 
                width="100%" height="100%" style="border:0;" 
                allowfullscreen="" loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade"></iframe>`;
        }
    }

    branchTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            branchTabs.forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
            
            const selectedBranch = this.dataset.branch;
            updateMap(selectedBranch);
            
            // Scroll to branch card เฉพาะเมื่อมีการคลิกเท่านั้น
            const branchCard = document.querySelector(`.branch-card[data-branch="${selectedBranch}"]`);
            if (branchCard && !this.classList.contains('initial-load')) {
                branchCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });

    // Initialize map with first branch (without scrolling)
    const firstBranchTab = document.querySelector('.branch-tab');
    if (firstBranchTab) {
        firstBranchTab.classList.add('initial-load');
        const firstBranchKey = firstBranchTab.dataset.branch;
        updateMap(firstBranchKey);
        firstBranchTab.classList.add('active');
        firstBranchTab.classList.remove('initial-load');
    }

    // Animate on scroll code (คงเดิม)
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.branch-card, .map-card').forEach(el => {
        observer.observe(el);
    });

    // Hover effect code (คงเดิม)
    document.querySelectorAll('.branch-card').forEach(card => {
        card.addEventListener('mousemove', function(e) {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
        });

        card.addEventListener('mouseleave', function() {
            card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale3d(1, 1, 1)';
        });
    });
});


// Back to Top Button Functionality
document.addEventListener('DOMContentLoaded', function() {
    const backToTopButton = document.getElementById('backToTop');
    
    // แสดง/ซ่อนปุ่ม Back to Top เมื่อเลื่อนหน้า
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopButton.classList.add('show');
        } else {
            backToTopButton.classList.remove('show');
        }
    });
    
    // เมื่อคลิกปุ่ม Back to Top
    backToTopButton.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Cookie Consent Functionality
    const cookieConsent = document.getElementById('cookieConsent');
    const acceptCookiesButton = document.getElementById('acceptCookies');
    
    // ตรวจสอบว่าผู้ใช้เคยยอมรับคุกกี้หรือไม่
    if (!localStorage.getItem('cookiesAccepted')) {
        setTimeout(() => {
            cookieConsent.classList.add('show');
        }, 2000);
    }
    
    // เมื่อคลิกยอมรับคุกกี้
    acceptCookiesButton.addEventListener('click', function() {
        localStorage.setItem('cookiesAccepted', 'true');
        cookieConsent.classList.remove('show');
    });

    // Hover effect สำหรับ certification badges
    const certBadges = document.querySelectorAll('.cert-badge');
    certBadges.forEach(badge => {
        badge.addEventListener('mouseover', function() {
            this.style.transform = 'translateY(-5px) scale(1.05)';
        });
        
        badge.addEventListener('mouseout', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Smooth scroll สำหรับลิงก์ใน footer
    document.querySelectorAll('.footer-links a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Animation สำหรับ contact items เมื่อ hover
    const contactItems = document.querySelectorAll('.contact-item');
    contactItems.forEach(item => {
        item.addEventListener('mouseover', function() {
            this.querySelector('i').style.transform = 'scale(1.2)';
        });
        
        item.addEventListener('mouseout', function() {
            this.querySelector('i').style.transform = 'scale(1)';
        });
    });

    // Loading animation สำหรับโลโก้
    const footerLogo = document.querySelector('.footer-logo');
    if (footerLogo) {
        footerLogo.addEventListener('load', function() {
            this.style.animation = 'fadeIn 0.5s ease-in-out';
        });
    }
});
    </script>
</body>