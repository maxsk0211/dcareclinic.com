<!DOCTYPE html>
<html lang="th" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>แพ็คเกจและราคา - D Care Clinic</title>

    <!-- SEO Tags -->
    <meta name="description" content="แพ็คเกจการบริการและราคาที่คุ้มค่าจาก D Care Clinic เลือกแพ็คเกจที่เหมาะกับความต้องการของผิวคุณ พร้อมโปรโมชั่นพิเศษ" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="img/pr/logo.jpg" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="../assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="../assets/vendor/fonts/flag-icons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" />
    <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-default.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <link rel="stylesheet" href="navbar-styles.css" />
    <link rel="stylesheet" href="footer-styles.css" />

    <style>
        /* Hero Section */
        .packages-hero {
            position: relative;
            padding: 180px 0 120px;
            background-color: #f5f5f9;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ff8fb1' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            margin-bottom: 0;
            overflow: hidden;
        }

        .packages-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,143,177,0.2) 0%, rgba(255,91,148,0.2) 100%);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: #333;
            text-shadow: 0 2px 15px rgba(0,0,0,0.05);
            position: relative;
            display: inline-block;
        }

        .hero-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, transparent, #ff5b94, transparent);
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: #555;
            max-width: 700px;
            margin: 0 auto 2rem;
            line-height: 1.6;
        }

        /* Filter Section */
        .filter-section {
            background: white;
            padding: 30px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            position: relative;
            z-index: 10;
            border-radius: 0 0 20px 20px;
            margin-bottom: 30px;
        }

        .filter-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .category-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .filter-btn {
            background: transparent;
            border: 1px solid rgba(0,0,0,0.08);
            color: #555;
            font-size: 0.9rem;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            background: rgba(255,91,148,0.05);
            color: #ff5b94;
            border-color: rgba(255,91,148,0.2);
            transform: translateY(-2px);
        }

        .filter-btn.active {
            background: linear-gradient(120deg, #ff8fb1, #ff5b94);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(255,91,148,0.2);
        }

        .price-toggle-wrapper {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 8px;
            padding: 6px;
            border: 1px solid rgba(0,0,0,0.08);
        }

        .price-toggle-option {
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .price-toggle-option.active {
            background: linear-gradient(120deg, #ff8fb1, #ff5b94);
            color: white;
            box-shadow: 0 4px 10px rgba(255,91,148,0.2);
        }

        /* Packages Grid */
        .packages-grid {
            padding: 30px 0 60px;
            background: #f8f9fa;
        }

        .package-card {
            display: flex;
            flex-direction: column;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            height: 100%;
            border: 1px solid rgba(0,0,0,0.04);
        }

        .package-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .package-card::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(255,91,148,0.2);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }

        .package-card:hover::after {
            opacity: 1;
        }

        .package-badge {
            position: absolute;
            top: 15px;
            right: 0;
            background: #ff5b94;
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 6px 15px 6px 10px;
            border-radius: 4px 0 0 4px;
            z-index: 1;
            box-shadow: 0 4px 12px rgba(255,91,148,0.3);
        }

        .package-badge::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 10px;
            height: 10px;
            border-bottom-left-radius: 10px;
            background: #ff5b94;
            box-shadow: 2px 2px 0 white;
            transform: translateY(-10px);
        }

        .package-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .package-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.8s ease;
        }

        .package-card:hover .package-image img {
            transform: scale(1.05);
        }

        .package-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.2), transparent);
        }

        .package-content {
            padding: 24px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .package-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #333;
            line-height: 1.3;
        }

        .package-description {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .package-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed rgba(0,0,0,0.1);
        }

        .package-duration {
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            color: #666;
        }

        .package-duration i {
            margin-right: 5px;
            color: #ff5b94;
        }

        .package-rating {
            display: flex;
            align-items: center;
        }

        .package-rating .stars {
            color: #ffc107;
            margin-right: 5px;
        }

        .rating-count {
            font-size: 0.85rem;
            color: #666;
        }

        .package-pricing {
            margin-bottom: 20px;
        }

        .price-tag {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }

        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 1rem;
        }

        .current-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ff5b94;
            text-shadow: 0 1px 2px rgba(255,91,148,0.1);
        }

        .discount-badge {
            background: #FF3B6F;
            color: white;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .price-note {
            font-size: 0.85rem;
            color: #666;
        }

        .package-features {
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: #555;
        }

        .feature-item i {
            color: #ff5b94;
            margin-top: 4px;
            font-size: 0.8rem;
        }

        .package-actions {
            margin-top: auto;
            display: flex;
            gap: 10px;
        }

        .btn-book-now {
            flex: 3;
            background: linear-gradient(120deg, #ff8fb1, #ff5b94);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-book-now:hover {
            box-shadow: 0 8px 20px rgba(255,91,148,0.3);
            transform: translateY(-2px);
        }

        .btn-details {
            flex: 1;
            background: transparent;
            color: #666;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-details:hover {
            background: rgba(0,0,0,0.05);
            transform: translateY(-2px);
        }

        /* Special Offers */
        .special-offers {
            padding: 100px 0;
            background-color: white;
            position: relative;
            overflow: hidden;
        }

        .special-offers::before {
            content: "";
            position: absolute;
            width: 800px;
            height: 800px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255,143,177,0.1), rgba(255,91,148,0.1));
            top: -400px;
            left: -200px;
            z-index: 1;
        }

        .special-offers::after {
            content: "";
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255,143,177,0.1), rgba(255,91,148,0.1));
            bottom: -300px;
            right: -200px;
            z-index: 1;
        }

        .special-offers-content {
            position: relative;
            z-index: 2;
        }

        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-tag {
            display: inline-block;
            padding: 6px 15px;
            background: linear-gradient(120deg, rgba(255,143,177,0.2), rgba(255,91,148,0.2));
            color: #ff5b94;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 20px;
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #333;
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #ff8fb1, #ff5b94);
            border-radius: 2px;
        }

        .section-subtitle {
            font-size: 1.1rem;
            color: #666;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .featured-offer {
            background: white;
            border-radius: 16px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.08);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
        }

        .featured-offer::before {
            content: "";
            position: absolute;
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, rgba(255,143,177,0.3), rgba(255,91,148,0.3));
            border-radius: 50%;
            top: -100px;
            right: -100px;
            z-index: 1;
        }

        .featured-offer::after {
            content: "";
            position: absolute;
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, rgba(255,143,177,0.2), rgba(255,91,148,0.2));
            border-radius: 50%;
            bottom: -75px;
            left: -75px;
            z-index: 1;
        }

        .featured-content {
            position: relative;
            z-index: 2;
            padding: 40px;
        }

        .featured-label {
            display: inline-block;
            padding: 6px 15px;
            background: #ff5b94;
            color: white;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 20px;
            margin-bottom: 20px;
        }

        .featured-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .featured-description {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .featured-pricing {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .featured-original {
            text-decoration: line-through;
            color: #999;
            font-size: 1.4rem;
        }

        .featured-current {
            font-size: 2.5rem;
            font-weight: 800;
            color: #ff5b94;
        }

        .featured-discount {
            background: #FF3B6F;
            color: white;
            padding: 5px 10px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .featured-features {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }

        .featured-feature {
            flex: 1 1 calc(50% - 20px);
            min-width: 200px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 15px;
            background: rgba(255,91,148,0.05);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .featured-feature:hover {
            background: rgba(255,91,148,0.1);
            transform: translateY(-3px);
        }

        .featured-feature i {
            color: #ff5b94;
            font-size: 1.5rem;
            margin-top: 2px;
        }

        .feature-text {
            flex: 1;
        }

        .feature-text h4 {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .feature-text p {
            font-size: 0.9rem;
            color: #666;
            line-height: 1.5;
        }

        .featured-action {
            text-align: center;
        }

        .btn-get-offer {
            background: linear-gradient(120deg, #ff8fb1, #ff5b94);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .btn-get-offer:hover {
            box-shadow: 0 10px 25px rgba(255,91,148,0.3);
            transform: translateY(-3px);
        }

        .featured-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0 16px 16px 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .offer-timer {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .timer-item {
            flex: 1;
            background: white;
            border-radius: 10px;
            padding: 15px 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .timer-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ff5b94;
            margin-bottom: 5px;
        }

        .timer-label {
            font-size: 0.85rem;
            color: #666;
        }

        /* More Offers */
        .more-offers {
            margin-top: 60px;
        }

        .more-offers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        .mini-offer {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            display: flex;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .mini-offer:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }

        .mini-offer-image {
            width: 130px;
            overflow: hidden;
        }

        .mini-offer-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .mini-offer-content {
            flex: 1;
            padding: 20px;
        }

        .mini-offer-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .mini-offer-price {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .mini-old-price {
            text-decoration: line-through;
            color: #999;
            font-size: 0.9rem;
        }

        .mini-new-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: #ff5b94;
        }

        .mini-discount {
            background: #FF3B6F;
            color: white;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .mini-features {
            margin-bottom: 15px;
        }

        .mini-feature-item {
            display: flex;
            align-items: flex-start;
            gap: 5px;
            margin-bottom: 5px;
            font-size: 0.85rem;
            color: #666;
        }

        .mini-feature-item i {
            color: #ff5b94;
            font-size: 0.8rem;
            margin-top: 3px;
        }

        .mini-offer-action .btn-view-details {
            width: 100%;
            background: rgba(255,91,148,0.1);
            color: #ff5b94;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .mini-offer-action .btn-view-details:hover {
            background: rgba(255,91,148,0.2);
        }

        /* CTA Section */
        .cta-section {
            padding: 80px 0;
            background: #f5f5f9;
            position: relative;
            overflow: hidden;
        }

        .cta-section::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ff8fb1' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 1;
        }

        .cta-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .cta-title {
            font-size: 2.8rem;
            font-weight: 800;
            color: #333;
            margin-bottom: 20px;
        }

        .cta-description {
            font-size: 1.2rem;
            color: #666;
            max-width: 800px;
            margin: 0 auto 40px;
            line-height: 1.6;
        }

        .btn-cta {
            background: linear-gradient(120deg, #ff8fb1, #ff5b94);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 18px 40px;
            font-size: 1.2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-cta:hover {
            box-shadow: 0 12px 30px rgba(255,91,148,0.3);
            transform: translateY(-3px);
        }

        /* Floating Elements */
        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 1;
        }

        .floating-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255,143,177,0.3), rgba(255,91,148,0.3));
            opacity: 0.2;
            animation: float 15s infinite ease-in-out;
        }

        .circle-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
        }

        .circle-2 {
            width: 200px;
            height: 200px;
            top: 40%;
            right: -50px;
            animation-delay: -3s;
        }

        .circle-3 {
            width: 150px;
            height: 150px;
            bottom: -50px;
            left: 30%;
            animation-delay: -6s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            33% {
                transform: translate(20px, -30px) rotate(8deg);
            }
            66% {
                transform: translate(-15px, 15px) rotate(-8deg);
            }
        }

        /* Shimmer Effect */
        .shimmer {
            position: relative;
            overflow: hidden;
        }

        .shimmer::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.3) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: rotate(30deg);
            animation: shimmer 3s infinite;
            pointer-events: none;
        }

        @keyframes shimmer {
            0% { transform: translate(-100%, -100%) rotate(30deg); }
            100% { transform: translate(100%, 100%) rotate(30deg); }
        }

        /* Responsive Styles */
        @media (max-width: 1199.98px) {
            .hero-title {
                font-size: 3.5rem;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .featured-title {
                font-size: 1.8rem;
            }
            
            .featured-content {
                padding: 30px;
            }
            
            .featured-current {
                font-size: 2.2rem;
            }
        }

        @media (max-width: 991.98px) {
            .packages-hero {
                padding: 150px 0 100px;
            }
            
            .hero-title {
                font-size: 3rem;
            }
            
            .filter-container {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .category-filter {
                overflow-x: auto;
                max-width: 100%;
                padding-bottom: 10px;
                justify-content: flex-start;
            }
            
            .featured-offer {
                margin-bottom: 30px;
            }
            
            .featured-feature {
                flex: 1 1 100%;
            }
            
            .cta-title {
                font-size: 2.4rem;
            }
        }

        @media (max-width: 767.98px) {
            .packages-hero {
                padding: 120px 0 80px;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .featured-image {
                height: 300px;
            }
            
            .featured-image img {
                border-radius: 0 0 16px 16px;
            }
            
            .more-offers-grid {
                grid-template-columns: 1fr;
            }
            
            .mini-offer {
                max-width: 100%;
            }
            
            .cta-title {
                font-size: 2rem;
            }
            
            .cta-description {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 575.98px) {
            .packages-hero {
                padding: 100px 0 60px;
            }
            
            .hero-title {
                font-size: 2.2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
            
            .package-image {
                height: 180px;
            }
            
            .package-card {
                max-width: 100%;
            }
            
            .featured-content {
                padding: 20px;
            }
            
            .featured-title {
                font-size: 1.5rem;
            }
            
            .featured-pricing {
                flex-wrap: wrap;
            }
            
            .featured-current {
                font-size: 1.8rem;
            }
            
            .timer-number {
                font-size: 1.5rem;
            }
            
            .mini-offer-image {
                width: 100px;
            }
            
            .mini-offer-title {
                font-size: 1rem;
            }
            
            .mini-new-price {
                font-size: 1.2rem;
            }
            
            .btn-cta {
                padding: 15px 30px;
                font-size: 1.1rem;
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

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <!-- Hero Section -->
                    <section class="packages-hero">
                        <div class="floating-elements">
                            <div class="floating-circle circle-1"></div>
                            <div class="floating-circle circle-2"></div>
                            <div class="floating-circle circle-3"></div>
                        </div>
                        <div class="container">
                            <div class="hero-content">
                                <h1 class="hero-title">เลือกแพ็คเกจที่ใช่สำหรับคุณ</h1>
                                <p class="hero-subtitle">
                                    ค้นพบแพ็คเกจและบริการที่ออกแบบมาเพื่อความงามของคุณโดยเฉพาะ 
                                    ด้วยทีมแพทย์ผู้เชี่ยวชาญและเทคโนโลยีที่ทันสมัย พร้อมโปรโมชั่นพิเศษมากมาย
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Filter Section -->
                    <section class="filter-section">
                        <div class="container">
                            <div class="filter-container">
                                <div class="category-filter">
                                    <button class="filter-btn active">ทั้งหมด</button>
                                    <button class="filter-btn">ทรีตเมนต์ผิวหน้า</button>
                                    <button class="filter-btn">เลเซอร์</button>
                                    <button class="filter-btn">แอนตี้เอจจิ้ง</button>
                                    <button class="filter-btn">ทรีตเมนต์ผิวกาย</button>
                                    <button class="filter-btn">แพ็คเกจพิเศษ</button>
                                </div>
                                <div class="price-toggle-wrapper">
                                    <div class="price-toggle-option active">รายครั้ง</div>
                                    <div class="price-toggle-option">คอร์ส (ประหยัดกว่า)</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Packages Grid -->
                    <section class="packages-grid">
                        <div class="container">
                            <div class="row g-4">
                                <!-- Package Card 1 -->
                                <div class="col-lg-4 col-md-6" data-category="facial">
                                    <div class="package-card">
                                        <div class="package-badge">ขายดี</div>
                                        <div class="package-image">
                                            <img src="/api/placeholder/600/400" alt="Basic Facial Treatment">
                                        </div>
                                        <div class="package-content">
                                            <h3 class="package-title">Basic Facial Treatment</h3>
                                            <p class="package-description">
                                                ทรีตเมนต์พื้นฐานสำหรับการทำความสะอาดและฟื้นฟูผิวหน้า
                                            </p>
                                            <div class="package-meta">
                                                <div class="package-duration">
                                                    <i class="fas fa-clock"></i>
                                                    <span>60 นาที</span>
                                                </div>
                                                <div class="package-rating">
                                                    <div class="stars">
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star-half-alt"></i>
                                                    </div>
                                                    <span class="rating-count">(128)</span>
                                                </div>
                                            </div>
                                            <div class="package-pricing">
                                                <div class="price-tag">
                                                    <div class="original-price">2,500฿</div>
                                                    <div class="current-price">1,900฿</div>
                                                    <div class="discount-badge">-24%</div>
                                                </div>
                                                <div class="price-note">ต่อครั้ง (ประหยัด 600฿)</div>
                                            </div>
                                            <div class="package-features">
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Deep Cleansing ทำความสะอาดผิวอย่างล้ำลึก</span>
                                                </div>
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Gentle Exfoliation ผลัดเซลล์ผิวอย่างอ่อนโยน</span>
                                                </div>
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Face Massage นวดหน้าด้วยเทคนิคเฉพาะ</span>
                                                </div>
                                            </div>
                                            <div class="package-actions">
                                                <button class="btn-book-now shimmer">
                                                    <i class="fas fa-calendar-check me-2"></i>จองเลย
                                                </button>
                                                <button class="btn-details">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Package Card 2 -->
                                <div class="col-lg-4 col-md-6" data-category="facial">
                                    <div class="package-card">
                                        <div class="package-badge">แนะนำ</div>
                                        <div class="package-image">
                                            <img src="/api/placeholder/600/400" alt="Premium Facial Treatment">
                                        </div>
                                        <div class="package-content">
                                            <h3 class="package-title">Premium Facial Treatment</h3>
                                            <p class="package-description">
                                                ทรีตเมนต์ระดับพรีเมียมปรับสภาพผิวและแก้ไขปัญหาผิวเฉพาะจุด
                                            </p>
                                            <div class="package-meta">
                                                <div class="package-duration">
                                                    <i class="fas fa-clock"></i>
                                                    <span>90 นาที</span>
                                                </div>
                                                <div class="package-rating">
                                                    <div class="stars">
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                    </div>
                                                    <span class="rating-count">(245)</span>
                                                </div>
                                            </div>
                                            <div class="package-pricing">
                                                <div class="price-tag">
                                                    <div class="original-price">3,500฿</div>
                                                    <div class="current-price">2,900฿</div>
                                                    <div class="discount-badge">-17%</div>
                                                </div>
                                                <div class="price-note">ต่อครั้ง (ประหยัด 600฿)</div>
                                            </div>
                                            <div class="package-features">
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Advanced Cleansing ทำความสะอาดล้ำลึกพิเศษ</span>
                                                </div>
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Professional Exfoliation ผลัดเซลล์ผิวระดับมืออาชีพ</span>
                                                </div>
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Ultrasonic Technology เทคโนโลยีอัลตร้าโซนิค</span>
                                                </div>
                                            </div>
                                            <div class="package-actions">
                                                <button class="btn-book-now shimmer">
                                                    <i class="fas fa-calendar-check me-2"></i>จองเลย
                                                </button>
                                                <button class="btn-details">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Package Card 3 -->
                                <div class="col-lg-4 col-md-6" data-category="laser">
                                    <div class="package-card">
                                        <div class="package-image">
                                            <img src="/api/placeholder/600/400" alt="Laser Treatment">
                                        </div>
                                        <div class="package-content">
                                            <h3 class="package-title">Laser Treatment</h3>
                                            <p class="package-description">
                                                เลเซอร์บำบัดผิวที่ช่วยลดเลือนริ้วรอย จุดด่างดำ และปรับสภาพผิว
                                            </p>
                                            <div class="package-meta">
                                                <div class="package-duration">
                                                    <i class="fas fa-clock"></i>
                                                    <span>60 นาที</span>
                                                </div>
                                                <div class="package-rating">
                                                    <div class="stars">
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star-half-alt"></i>
                                                    </div>
                                                    <span class="rating-count">(197)</span>
                                                </div>
                                            </div>
                                            <div class="package-pricing">
                                                <div class="price-tag">
                                                    <div class="original-price">4,500฿</div>
                                                    <div class="current-price">3,900฿</div>
                                                    <div class="discount-badge">-13%</div>
                                                </div>
                                                <div class="price-note">ต่อครั้ง (ประหยัด 600฿)</div>
                                            </div>
                                            <div class="package-features">
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Laser Technology เทคโนโลยีเลเซอร์ล่าสุด</span>
                                                </div>
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Skin Analysis วิเคราะห์สภาพผิวก่อนทำ</span>
                                                </div>
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Targeted Treatment รักษาเฉพาะจุด</span>
                                                </div>
                                            </div>
                                            <div class="package-actions">
                                                <button class="btn-book-now shimmer">
                                                    <i class="fas fa-calendar-check me-2"></i>จองเลย
                                                </button>
                                                <button class="btn-details">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Package Card 4 -->
                                <div class="col-lg-4 col-md-6" data-category="anti-aging">
                                    <div class="package-card">
                                        <div class="package-badge">พรีเมียม</div>
                                        <div class="package-image">
                                            <img src="/api/placeholder/600/400" alt="Anti-Aging Treatment">
                                        </div>
                                        <div class="package-content">
                                            <h3 class="package-title">Anti-Aging Treatment</h3>
                                            <p class="package-description">
                                                แพ็คเกจชะลอวัยด้วยเทคโนโลยีและนวัตกรรมล่าสุด
                                            </p>
                                            <div class="package-meta">
                                                <div class="package-duration">
                                                    <i class="fas fa-clock"></i>
                                                    <span>90 นาที</span>
                                                </div>
                                                <div class="package-rating">
                                                    <div class="stars">
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                    </div>
                                                    <span class="rating-count">(162)</span>
                                                </div>
                                            </div>
                                            <div class="package-pricing">
                                                <div class="price-tag">
                                                    <div class="original-price">6,500฿</div>
                                                    <div class="current-price">5,500฿</div>
                                                    <div class="discount-badge">-15%</div>
                                                </div>
                                                <div class="price-note">ต่อครั้ง (ประหยัด 1,000฿)</div>
                                            </div>
                                            <div class="package-features">
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>HIFU Technology เทคโนโลยี HIFU</span>
                                                </div>
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Radio Frequency เทคโนโลยี RF</span>
                                                </div>
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Collagen Boost กระตุ้นคอลลาเจน</span>
                                                </div>
                                            </div>
                                            <div class="package-actions">
                                                <button class="btn-book-now shimmer">
                                                    <i class="fas fa-calendar-check me-2"></i>จองเลย
                                                </button>
                                                <button class="btn-details">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Package Card 5 -->
                                <div class="col-lg-4 col-md-6" data-category="brightening">
                                    <div class="package-card">
                                        <div class="package-image">
                                            <img src="/api/placeholder/600/400" alt="Brightening Treatment">
                                        </div>
                                        <div class="package-content">
                                            <h3 class="package-title">Brightening Treatment</h3>
                                            <p class="package-description">
                                                ทรีตเมนต์ผิวขาวกระจ่างใส ลดเลือนฝ้า กระ และจุดด่างดำ
                                            </p>
                                            <div class="package-meta">
                                                <div class="package-duration">
                                                    <i class="fas fa-clock"></i>
                                                    <span>75 นาที</span>
                                                </div>
                                                <div class="package-rating">
                                                    <div class="stars">
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                    </div>
                                                    <span class="rating-count">(206)</span>
                                                </div>
                                            </div>
                                            <div class="package-pricing">
                                                <div class="price-tag">
                                                    <div class="original-price">4,000฿</div>
                                                    <div class="current-price">3,500฿</div>
                                                    <div class="discount-badge">-13%</div>
                                                </div>
                                                <div class="price-note">ต่อครั้ง (ประหยัด 500฿)</div>
                                            </div>
                                            <div class="package-features">
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Vitamin C Infusion วิตามินซีเข้มข้น</span>
                                                </div>
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Whitening Booster ตัวเร่งผิวขาว</span>
                                                </div>
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Intensive Mask มาส์กเข้มข้น</span>
                                                </div>
                                            </div>
                                            <div class="package-actions">
                                                <button class="btn-book-now shimmer">
                                                    <i class="fas fa-calendar-check me-2"></i>จองเลย
                                                </button>
                                                <button class="btn-details">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Package Card 6 -->
                                <div class="col-lg-4 col-md-6" data-category="body">
                                    <div class="package-card">
                                        <div class="package-image">
                                            <img src="/api/placeholder/600/400" alt="Body Treatment">
                                        </div>
                                        <div class="package-content">
                                            <h3 class="package-title">Body Treatment</h3>
                                            <p class="package-description">
                                                ทรีตเมนต์ผิวกายเพื่อผิวเนียนนุ่ม กระชับ และเปล่งปลั่ง
                                            </p>
                                            <div class="package-meta">
                                                <div class="package-duration">
                                                    <i class="fas fa-clock"></i>
                                                    <span>120 นาที</span>
                                                </div>
                                                <div class="package-rating">
                                                    <div class="stars">
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star"></i>
                                                        <i class="fas fa-star-half-alt"></i>
                                                    </div>
                                                    <span class="rating-count">(184)</span>
                                                </div>
                                            </div>
                                            <div class="package-pricing">
                                                <div class="price-tag">
                                                    <div class="original-price">5,000฿</div>
                                                    <div class="current-price">4,200฿</div>
                                                    <div class="discount-badge">-16%</div>
                                                </div>
                                                <div class="price-note">ต่อครั้ง (ประหยัด 800฿)</div>
                                            </div>
                                            <div class="package-features">
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Body Scrub ขัดผิวทั้งตัว</span>
                                                </div>
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Body Wrap พอกผิวด้วยสารสกัดพิเศษ</span>
                                                </div>
                                                <div class="feature-item">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span>Slimming Massage นวดกระชับสัดส่วน</span>
                                                </div>
                                            </div>
                                            <div class="package-actions">
                                                <button class="btn-book-now shimmer">
                                                    <i class="fas fa-calendar-check me-2"></i>จองเลย
                                                </button>
                                                <button class="btn-details">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Special Offers Section -->
                    <section class="special-offers">
                        <div class="container">
                            <div class="special-offers-content">
                                <div class="section-header">
                                    <div class="section-tag">Limited Time Offer</div>
                                    <h2 class="section-title">โปรโมชั่นสุดพิเศษประจำเดือน</h2>
                                    <p class="section-subtitle">
                                        แพ็คเกจสุดพิเศษเฉพาะช่วงเวลาจำกัด เพื่อให้คุณได้ดูแลผิวในราคาที่คุ้มค่ายิ่งขึ้น
                                    </p>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="featured-offer">
                                            <div class="row g-0">
                                                <div class="col-lg-7">
                                                    <div class="featured-content">
                                                        <div class="featured-label">Exclusive Deal</div>
                                                        <h3 class="featured-title">Premium Beauty Package</h3>
                                                        <p class="featured-description">
                                                            แพ็คเกจความงามระดับพรีเมียมที่รวมทรีตเมนต์ยอดนิยมไว้ในแพ็คเกจเดียว
                                                            ครบทุกขั้นตอนการดูแลผิวหน้าและผิวกาย พร้อมผลิตภัณฑ์บำรุงผิวมูลค่ากว่า 2,500 บาท
                                                        </p>

                                                        <div class="offer-timer">
                                                            <div class="timer-item">
                                                                <div class="timer-number" id="days">15</div>
                                                                <div class="timer-label">วัน</div>
                                                            </div>
                                                            <div class="timer-item">
                                                                <div class="timer-number" id="hours">08</div>
                                                                <div class="timer-label">ชั่วโมง</div>
                                                            </div>
                                                            <div class="timer-item">
                                                                <div class="timer-number" id="minutes">45</div>
                                                                <div class="timer-label">นาที</div>
                                                            </div>
                                                            <div class="timer-item">
                                                                <div class="timer-number" id="seconds">30</div>
                                                                <div class="timer-label">วินาที</div>
                                                            </div>
                                                        </div>

                                                        <div class="featured-pricing">
                                                            <div class="featured-original">28,000฿</div>
                                                            <div class="featured-current">18,900฿</div>
                                                            <div class="featured-discount">ประหยัด 33%</div>
                                                        </div>

                                                        <div class="featured-features">
                                                            <div class="featured-feature">
                                                                <i class="fas fa-gem"></i>
                                                                <div class="feature-text">
                                                                    <h4>Premium Facial 3 ครั้ง</h4>
                                                                    <p>ทรีตเมนต์ผิวหน้าระดับพรีเมียม 3 ครั้ง</p>
                                                                </div>
                                                            </div>
                                                            <div class="featured-feature">
                                                                <i class="fas fa-laser"></i>
                                                                <div class="feature-text">
                                                                    <h4>Laser Treatment 2 ครั้ง</h4>
                                                                    <p>ทรีตเมนต์เลเซอร์ 2 ครั้ง ตามสภาพผิว</p>
                                                                </div>
                                                            </div>
                                                            <div class="featured-feature">
                                                                <i class="fas fa-air-freshener"></i>
                                                                <div class="feature-text">
                                                                    <h4>Body Treatment 1 ครั้ง</h4>
                                                                    <p>ทรีตเมนต์ผิวกาย 1 ครั้ง</p>
                                                                </div>
                                                            </div>
                                                            <div class="featured-feature">
                                                                <i class="fas fa-gift"></i>
                                                                <div class="feature-text">
                                                                    <h4>ชุดผลิตภัณฑ์บำรุงผิว</h4>
                                                                    <p>ชุดผลิตภัณฑ์บำรุงผิวมูลค่า 2,500 บาท</p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="featured-action">
                                                            <button class="btn-get-offer">
                                                                <i class="fas fa-tag me-2"></i>รับข้อเสนอพิเศษ
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-5">
                                                    <div class="featured-image">
                                                        <img src="/api/placeholder/600/800" alt="Premium Beauty Package">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="more-offers">
                                    <div class="more-offers-grid">
                                        <!-- Mini Offer 1 -->
                                        <div class="mini-offer">
                                            <div class="mini-offer-image">
                                                <img src="/api/placeholder/300/300" alt="Facial Package">
                                            </div>
                                            <div class="mini-offer-content">
                                                <h4 class="mini-offer-title">Facial Package Deal</h4>
                                                <div class="mini-offer-price">
                                                    <div class="mini-old-price">12,500฿</div>
                                                    <div class="mini-new-price">8,900฿</div>
                                                    <div class="mini-discount">-29%</div>
                                                </div>
                                                <div class="mini-features">
                                                    <div class="mini-feature-item">
                                                        <i class="fas fa-check-circle"></i>
                                                        <span>Premium Facial 5 ครั้ง</span>
                                                    </div>
                                                    <div class="mini-feature-item">
                                                        <i class="fas fa-check-circle"></i>
                                                        <span>ฟรี! Hydrating Mask 1 ครั้ง</span>
                                                    </div>
                                                </div>
                                                <div class="mini-offer-action">
                                                    <button class="btn-view-details">ดูรายละเอียด</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Mini Offer 2 -->
                                        <div class="mini-offer">
                                            <div class="mini-offer-image">
                                                <img src="/api/placeholder/300/300" alt="Laser Package">
                                            </div>
                                            <div class="mini-offer-content">
                                                <h4 class="mini-offer-title">Laser Special Deal</h4>
                                                <div class="mini-offer-price">
                                                    <div class="mini-old-price">18,000฿</div>
                                                    <div class="mini-new-price">12,900฿</div>
                                                    <div class="mini-discount">-28%</div>
                                                </div>
                                                <div class="mini-features">
                                                    <div class="mini-feature-item">
                                                        <i class="fas fa-check-circle"></i>
                                                        <span>Laser Treatment 5 ครั้ง</span>
                                                    </div>
                                                    <div class="mini-feature-item">
                                                        <i class="fas fa-check-circle"></i>
                                                        <span>ฟรี! Sun Protection SPF 50+</span>
                                                    </div>
                                                </div>
                                                <div class="mini-offer-action">
                                                    <button class="btn-view-details">ดูรายละเอียด</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Mini Offer 3 -->
                                        <div class="mini-offer">
                                            <div class="mini-offer-image">
                                                <img src="/api/placeholder/300/300" alt="Anti-Aging Package">
                                            </div>
                                            <div class="mini-offer-content">
                                                <h4 class="mini-offer-title">Anti-Aging Package</h4>
                                                <div class="mini-offer-price">
                                                    <div class="mini-old-price">25,000฿</div>
                                                    <div class="mini-new-price">19,900฿</div>
                                                    <div class="mini-discount">-20%</div>
                                                </div>
                                                <div class="mini-features">
                                                    <div class="mini-feature-item">
                                                        <i class="fas fa-check-circle"></i>
                                                        <span>HIFU Treatment 3 ครั้ง</span>
                                                    </div>
                                                    <div class="mini-feature-item">
                                                        <i class="fas fa-check-circle"></i>
                                                        <span>RF Treatment 2 ครั้ง</span>
                                                    </div>
                                                </div>
                                                <div class="mini-offer-action">
                                                    <button class="btn-view-details">ดูรายละเอียด</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- CTA Section -->
                    <section class="cta-section">
                        <div class="container">
                            <div class="cta-content">
                                <h2 class="cta-title">ยกระดับการดูแลผิวของคุณ</h2>
                                <p class="cta-description">
                                    ปรึกษาผู้เชี่ยวชาญเพื่อรับคำแนะนำและวางแผนการดูแลผิวที่เหมาะกับคุณ
                                    พร้อมรับสิทธิพิเศษมากมาย
                                </p>
                                <button class="btn-cta">
                                    <i class="fas fa-calendar-alt me-2"></i>จองปรึกษาฟรี
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Footer -->
                    <?php include 'footer.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    
    <script>
        // Category Filter
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                const category = this.textContent.toLowerCase();
                const cards = document.querySelectorAll('.col-lg-4[data-category]');
                
                if (category === 'ทั้งหมด') {
                    cards.forEach(card => {
                        card.style.display = 'block';
                    });
                } else {
                    cards.forEach(card => {
                        if (card.dataset.category === category) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                }
            });
        });

        // Price Toggle
        document.querySelectorAll('.price-toggle-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.price-toggle-option').forEach(o => o.classList.remove('active'));
                this.classList.add('active');
                
                // Logic for toggling between single and course prices
                const isCourse = this.textContent.includes('คอร์ส');
                const cards = document.querySelectorAll('.package-card');
                
                cards.forEach(card => {
                    const currentPrice = card.querySelector('.current-price');
                    const originalPrice = card.querySelector('.original-price');
                    const priceNote = card.querySelector('.price-note');
                    
                    if (isCourse) {
                        // Switch to course pricing (example: 20% discount)
                        const singlePrice = parseInt(currentPrice.textContent.replace('฿', '').replace(',', ''));
                        const coursePrice = Math.round(singlePrice * 0.8 * 5); // 20% discount for 5 sessions
                        const originalCoursePrice = parseInt(originalPrice.textContent.replace('฿', '').replace(',', '')) * 5;
                        
                        currentPrice.textContent = coursePrice.toLocaleString() + '฿';
                        originalPrice.textContent = originalCoursePrice.toLocaleString() + '฿';
                        priceNote.textContent = 'คอร์ส 5 ครั้ง (ประหยัด ' + Math.round(originalCoursePrice - coursePrice).toLocaleString() + '฿)';
                    } else {
                        // Back to single session pricing (simplified - in real app would need to store original values)
                        location.reload(); // Simple way to reset prices
                    }
                });
            });
        });

        // Countdown Timer
        function updateCountdown() {
            // Set the target date (15 days from now for demo)
            const now = new Date();
            const target = new Date();
            target.setDate(target.getDate() + 15);
            
            const diff = target - now;
            
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            document.getElementById('days').textContent = days.toString().padStart(2, '0');
            document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
            document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
        }
        
        // Update countdown every second
        setInterval(updateCountdown, 1000);
        updateCountdown();

        // Intersection Observer for Animation
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            // Observe all package cards
            document.querySelectorAll('.package-card, .mini-offer, .featured-offer').forEach(card => {
                observer.observe(card);
            });
        });
    </script>
</body>
</html>