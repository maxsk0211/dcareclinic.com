<!DOCTYPE html>
<html lang="th" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="horizontal-menu-template-no-customizer-starter">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>บทความความรู้ - D Care Clinic</title>

    <meta name="description" content="บทความความรู้เกี่ยวกับการดูแลผิวพรรณและความงาม จาก D Care Clinic" />
    
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
        /* Articles Section Styles */
        .articles-hero {
            position: relative;
            padding: 100px 0 60px;
            background: linear-gradient(45deg, rgba(255,143,177,0.1) 0%, rgba(255,91,148,0.1) 100%);
            border-radius: 0 0 30px 30px;
            overflow: hidden;
        }

        .category-filter {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin: 2rem 0;
            padding: 0 15px;
        }

        .filter-btn {
            padding: 8px 20px;
            border-radius: 50px;
            border: 1px solid rgba(255,91,148,0.2);
            background: white;
            color: #666;
            transition: all 0.3s ease;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: linear-gradient(120deg, #ff8fb1, #ff5b94);
            color: white;
            transform: translateY(-2px);
        }

        .article-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .article-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .article-image-wrapper {
            position: relative;
            padding-top: 60%;
            overflow: hidden;
        }

        .article-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .article-card:hover .article-image {
            transform: scale(1.05);
        }

        .article-category {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 5px 15px;
            border-radius: 50px;
            background: rgba(255,255,255,0.9);
            color: #ff5b94;
            font-size: 0.8rem;
            font-weight: 500;
            backdrop-filter: blur(5px);
        }

        .article-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
            font-size: 0.85rem;
            color: #666;
        }

        .article-meta i {
            color: #ff5b94;
        }

        .article-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            line-height: 1.4;
            color: #333;
        }

        .article-excerpt {
            color: #666;
            margin-bottom: 1.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .read-more {
            color: #ff5b94;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: gap 0.3s ease;
        }

        .read-more:hover {
            gap: 10px;
        }

        .article-stats {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0,0,0,0.1);
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
            font-size: 0.85rem;
        }

        @media (max-width: 991.98px) {
            .articles-hero {
                padding: 80px 0 40px;
            }
            
            .category-filter {
                overflow-x: auto;
                justify-content: flex-start;
                padding-bottom: 10px;
            }
            
            .article-title {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 767.98px) {
            .articles-hero {
                padding: 60px 0 30px;
            }
            
            .article-card {
                margin-bottom: 20px;
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
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Articles Hero Section -->
                        <section class="articles-hero text-center">
                            <div class="container">
                                <span class="badge bg-primary bg-opacity-10 text-primary mb-3 px-3 py-2">Knowledge Hub</span>
                                <h1 class="display-5 fw-bold mb-3">บทความความรู้</h1>
                                <p class="text-muted col-lg-8 mx-auto">
                                    รวมบทความที่น่าสนใจเกี่ยวกับการดูแลผิวพรรณและความงาม จากผู้เชี่ยวชาญ D Care Clinic
                                </p>
                            </div>
                        </section>

                        <!-- Category Filter -->
                        <div class="category-filter">
                            <button class="filter-btn active">ทั้งหมด</button>
                            <button class="filter-btn">การดูแลผิวหน้า</button>
                            <button class="filter-btn">ทรีตเมนต์</button>
                            <button class="filter-btn">เลเซอร์</button>
                            <button class="filter-btn">สุขภาพผิว</button>
                            <button class="filter-btn">ความงาม</button>
                        </div>

                        <!-- Articles Grid -->
                        <div class="row g-4">
                            <!-- Article Card 1 -->
                            <div class="col-lg-4 col-md-6">
                                <article class="card article-card">
                                    <div class="article-image-wrapper">
                                        <img src="img/pr/1/1.png" class="article-image" alt="ผิวที่ต้องการทรีตเมนต์">
                                        <span class="article-category">การดูแลผิวหน้า</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="article-meta">
                                            <span><i class="fas fa-calendar"></i> 18 กุมภาพันธ์ 2025</span>
                                            <span><i class="fas fa-user"></i> Dr. สมหญิง</span>
                                        </div>
                                        <h3 class="article-title">
                                            5 สัญญาณที่บอกว่าผิวของคุณต้องการทรีตเมนต์ด่วน
                                        </h3>
                                        <p class="article-excerpt">
                                            หลายคนอาจไม่รู้ว่าผิวกำลังส่งสัญญาณขอความช่วยเหลือ การสังเกตสัญญาณเหล่านี้ตั้งแต่เนิ่นๆ จะช่วยป้องกันปัญหาผิวที่อาจลุกลามได้...
                                        </p>
                                        <a href="article-detail1.php" class="read-more">
                                            อ่านเพิ่มเติม <i class="fas fa-arrow-right"></i>
                                        </a>
                                        <div class="article-stats">
                                            <div class="stat-item">
                                                <i class="fas fa-eye"></i>
                                                <span>2.5K views</span>
                                            </div>
                                            <div class="stat-item">
                                                <i class="fas fa-share-alt"></i>
                                                <span>Share</span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <article class="card article-card">
                                    <div class="article-image-wrapper">
                                        <img src="img/pr/2/1.png" class="article-image" alt="ผิวที่ต้องการทรีตเมนต์">
                                        <span class="article-category">การดูแลผิวหน้า</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="article-meta">
                                            <span><i class="fas fa-calendar"></i> 18 กุมภาพันธ์ 2025</span>
                                            <span><i class="fas fa-user"></i> Dr. สมหญิง</span>
                                        </div>
                                        <h3 class="article-title">
                                            เทรนด์การดูแลผิวปี 2025: นวัตกรรมใหม่ที่คุณไม่ควรพลาด
                                        </h3>
                                        <p class="article-excerpt">
                                            ปี 2025 มาพร้อมกับนวัตกรรมการดูแลผิวที่ล้ำสมัยและน่าตื่นเต้น ด้วยการผสมผสานระหว่างเทคโนโลยีขั้นสูง
                                            และความเข้าใจในธรรมชาติของผิวที่มากขึ้น...
                                        </p>
                                        <a href="article-detail2.php" class="read-more">
                                            อ่านเพิ่มเติม <i class="fas fa-arrow-right"></i>
                                        </a>
                                        <div class="article-stats">
                                            <div class="stat-item">
                                                <i class="fas fa-eye"></i>
                                                <span>2.5K views</span>
                                            </div>
                                            <div class="stat-item">
                                                <i class="fas fa-share-alt"></i>
                                                <span>Share</span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <article class="card article-card">
                                    <div class="article-image-wrapper">
                                        <img src="img/pr/3/1.png" class="article-image" alt="ผิวที่ต้องการทรีตเมนต์">
                                        <span class="article-category">การดูแลผิวหน้า</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="article-meta">
                                            <span><i class="fas fa-calendar"></i> 18 กุมภาพันธ์ 2025</span>
                                            <span><i class="fas fa-user"></i> Dr. สมหญิง</span>
                                        </div>
                                        <h3 class="article-title">
                                            วิธีเลือกคลินิกความงามอย่างชาญฉลาด: 7 สิ่งที่ต้องพิจารณา
                                        </h3>
                                        <p class="article-excerpt">
                                            การเลือกคลินิกความงามที่เหมาะสมเป็นก้าวสำคัญในการดูแลผิวพรรณของคุณ การตัดสินใจที่ถูกต้องไม่เพียงช่วยให้คุณได้รับผลลัพธ์ที่ต้องการ...
                                        </p>
                                        <a href="article-detail3.php" class="read-more">
                                            อ่านเพิ่มเติม <i class="fas fa-arrow-right"></i>
                                        </a>
                                        <div class="article-stats">
                                            <div class="stat-item">
                                                <i class="fas fa-eye"></i>
                                                <span>2.5K views</span>
                                            </div>
                                            <div class="stat-item">
                                                <i class="fas fa-share-alt"></i>
                                                <span>Share</span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <article class="card article-card">
                                    <div class="article-image-wrapper">
                                        <img src="img/pr/3/1.png" class="article-image" alt="ผิวที่ต้องการทรีตเมนต์">
                                        <span class="article-category">การดูแลผิวหน้า</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="article-meta">
                                            <span><i class="fas fa-calendar"></i> 18 กุมภาพันธ์ 2025</span>
                                            <span><i class="fas fa-user"></i> Dr. สมหญิง</span>
                                        </div>
                                        <h3 class="article-title">
                                            เส้นทางสู่ผิวสวย: แผนการดูแลผิวตามช่วงวัย
                                        </h3>
                                        <p class="article-excerpt">
                                            ผิวพรรณของเราเปลี่ยนแปลงไปตามช่วงวัย การดูแลผิวที่ถูกต้องและเหมาะสมกับอายุ
                                            จึงเป็นกุญแจสำคัญสู่ผิวสวยสุขภาพดีที่ยั่งยืน มาทำความเข้าใจการดูแลผิวที่เหมาะสม
                                            สำหรับแต่ละช่วงวัยกัน...
                                        </p>
                                        <a href="article-detail4.php" class="read-more">
                                            อ่านเพิ่มเติม <i class="fas fa-arrow-right"></i>
                                        </a>
                                        <div class="article-stats">
                                            <div class="stat-item">
                                                <i class="fas fa-eye"></i>
                                                <span>2.5K views</span>
                                            </div>
                                            <div class="stat-item">
                                                <i class="fas fa-share-alt"></i>
                                                <span>Share</span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                            <div class="col-lg-4 col-md-6">
                                <article class="card article-card">
                                    <div class="article-image-wrapper">
                                        <img src="img/pr/3/1.png" class="article-image" alt="ผิวที่ต้องการทรีตเมนต์">
                                        <span class="article-category">การดูแลผิวหน้า</span>
                                    </div>
                                    <div class="card-body">
                                        <div class="article-meta">
                                            <span><i class="fas fa-calendar"></i> 18 กุมภาพันธ์ 2025</span>
                                            <span><i class="fas fa-user"></i> Dr. สมหญิง</span>
                                        </div>
                                        <h3 class="article-title">
                                           ทำความรู้จักกับเลเซอร์บำบัดผิว: ทางเลือกที่เหมาะกับคุณ
                                        </h3>
                                        <p class="article-excerpt">
                                            การเลือกคลินิกความงามที่เหมาะสมเป็นก้าวสำคัญในการดูแลผิวพรรณของคุณ การตัดสินใจที่ถูกต้องไม่เพียงช่วยให้คุณได้รับผลลัพธ์ที่ต้องการ...
                                        </p>
                                        <a href="article-detail5.php" class="read-more">
                                            อ่านเพิ่มเติม <i class="fas fa-arrow-right"></i>
                                        </a>
                                        <div class="article-stats">
                                            <div class="stat-item">
                                                <i class="fas fa-eye"></i>
                                                <span>2.5K views</span>
                                            </div>
                                            <div class="stat-item">
                                                <i class="fas fa-share-alt"></i>
                                                <span>Share</span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>

                        </div>
                    </div>

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
    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>
    <script>
        // Category Filter
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Intersection Observer for animation
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.article-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            observer.observe(card);
        });
    </script>
</body>
</html>