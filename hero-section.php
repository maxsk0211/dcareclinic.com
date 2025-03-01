<div class="hero-section" id="hero-section">
    <div class="swiper hero-swiper">
        <div class="swiper-wrapper">
            <?php
            // ดึงข้อมูล Hero Slides จากฐานข้อมูล
            $sql = "SELECT * FROM frontend_hero_slides WHERE is_active = 1 ORDER BY display_order ASC";
            $result = $conn->query($sql);
            
            // ถ้าไม่มีข้อมูลใน database ให้ใช้ค่า default
            if ($result && $result->num_rows > 0) {
                while ($slide = $result->fetch_assoc()) {
                    // แปลง JSON เป็น array
                    $stats = json_decode($slide['stats_json'], true) ?: [];
                    $features = json_decode($slide['features_json'], true) ?: [];
                    $buttons = json_decode($slide['buttons_json'], true) ?: [];
                    
                    // กำหนดรูปภาพ default ถ้าไม่มี
                    $bgImage = !empty($slide['background_image']) ? $slide['background_image'] : 'img/pr/pic5.1.png';
                    $heroImage = !empty($slide['hero_image']) ? $slide['hero_image'] : 'img/pr/pic4.png';
            ?>
            <!-- Slide -->
            <div class="swiper-slide">
                <!-- Background Layer -->
                <div class="hero-background" style="background-image: url('<?php echo $bgImage; ?>')"></div>
                
                <!-- Gradient Overlay -->
                <div class="overlay-gradient"></div>
                
                <!-- Content Wrapper -->
                <div class="hero-content-wrapper">
                    <div class="container-xxl">
                        <!-- Floating Elements -->
                        <div class="floating-elements">
                            <div class="floating-circle circle-1"></div>
                            <div class="floating-circle circle-2"></div>
                            <div class="floating-circle circle-3"></div>
                        </div>
                        
                        <div class="row align-items-center">
                            <!-- Text Content -->
                            <div class="col-lg-6">
                                <div class="hero-text-content" data-swiper-animation="animate__fadeInUp">
                                    <?php if (!empty($slide['subtitle'])) { ?>
                                    <h5 class="hero-subtitle-top"><?php echo $slide['subtitle']; ?></h5>
                                    <?php } ?>
                                    
                                    <h1 class="hero-title">
                                        <?php echo $slide['title']; ?><br>
                                        <?php if (!empty($slide['title_highlight'])) { ?>
                                        <span class="gradient-text"><?php echo $slide['title_highlight']; ?></span>
                                        <?php } ?>
                                    </h1>
                                    
                                    <?php if (!empty($slide['description'])) { ?>
                                    <p class="hero-subtitle"><?php echo $slide['description']; ?></p>
                                    <?php } ?>
                                    
                                    <?php if (!empty($buttons)) { ?>
                                    <div class="hero-buttons">
                                        <?php foreach ($buttons as $button) { 
                                            $btnClass = ($button['type'] === 'primary') ? 'btn-gradient' : 'btn-outline-light';
                                            $icon = !empty($button['icon']) ? '<i class="fas fa-'.$button['icon'].' me-2"></i>' : '';
                                        ?>
                                        <button class="btn <?php echo $btnClass; ?> btn-lg" onclick="location.href='<?php echo $button['url']; ?>'">
                                            <?php echo $icon . $button['text']; ?>
                                        </button>
                                        <?php } ?>
                                    </div>
                                    <?php } ?>
                                    
                                    <?php if (!empty($stats)) { ?>
                                    <div class="hero-stats">
                                        <?php foreach ($stats as $stat) { ?>
                                        <div class="stat-item">
                                            <div class="stat-number"><?php echo $stat['value']; ?><span class="text-gradient"><?php echo $stat['suffix']; ?></span></div>
                                            <div class="stat-label"><?php echo $stat['label']; ?></div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <?php } ?>
                                    
                                    <?php if (!empty($features)) { ?>
                                    <div class="treatment-features">
                                        <?php foreach ($features as $feature) { 
                                            $featureIcon = !empty($feature['icon']) ? '<i class="fas fa-'.$feature['icon'].' text-gradient"></i>' : '';
                                        ?>
                                        <div class="feature-item">
                                            <?php echo $featureIcon; ?>
                                            <span><?php echo $feature['text']; ?></span>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            
                            <!-- Image Content (Desktop Only) -->
                            <?php if (!empty($heroImage)) { ?>
                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="hero-image-wrapper">
                                    <img src="<?php echo $heroImage; ?>" alt="<?php echo $slide['title']; ?>" class="hero-image">
                                    <?php if (!empty($slide['subtitle']) && strpos(strtolower($slide['subtitle']), 'welcome') !== false) { ?>
                                    <div class="experience-badge">
                                        <div class="badge-content">
                                            <span class="years">15</span>
                                            <span class="text">Years of<br>Excellence</span>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
                // ถ้าไม่มีข้อมูลในฐานข้อมูล ให้แสดงค่าเริ่มต้น
            ?>
            <!-- Default Slide 1 -->
            <div class="swiper-slide">
                <!-- Background Layer -->
                <div class="hero-background" style="background-image: url('img/pr/pic5.1.png')"></div>
                
                <!-- Gradient Overlay -->
                <div class="overlay-gradient"></div>
                
                <!-- Content Wrapper -->
                <div class="hero-content-wrapper">
                    <div class="container-xxl">
                        <!-- Floating Elements -->
                        <div class="floating-elements">
                            <div class="floating-circle circle-1"></div>
                            <div class="floating-circle circle-2"></div>
                            <div class="floating-circle circle-3"></div>
                        </div>
                        
                        <div class="row align-items-center">
                            <!-- Text Content -->
                            <div class="col-lg-6">
                                <div class="hero-text-content" data-swiper-animation="animate__fadeInUp">
                                    <h5 class="hero-subtitle-top">Welcome to D Care Clinic</h5>
                                    <h1 class="hero-title">
                                        ค้นพบความงาม<br>
                                        <span class="gradient-text">ที่เป็นตัวคุณ</span>
                                    </h1>
                                    <p class="hero-subtitle">
                                        ด้วยทีมแพทย์ผู้เชี่ยวชาญและนวัตกรรมความงามระดับโลก<br>
                                        เราพร้อมดูแลคุณด้วยมาตรฐานระดับพรีเมียม
                                    </p>
                                    <div class="hero-buttons">
                                        <button class="btn btn-gradient btn-lg">
                                            <i class="fas fa-calendar-plus me-2"></i>จองคิวทันที
                                        </button>
                                        <button class="btn btn-outline-light btn-lg">
                                            <i class="fas fa-arrow-right me-2"></i>ดูบริการทั้งหมด
                                        </button>
                                    </div>
                                    <div class="hero-stats">
                                        <div class="stat-item">
                                            <div class="stat-number">15<span class="text-gradient">+</span></div>
                                            <div class="stat-label">ปีแห่งประสบการณ์</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number">50<span class="text-gradient">K+</span></div>
                                            <div class="stat-label">ลูกค้าที่ไว้วางใจ</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number">98<span class="text-gradient">%</span></div>
                                            <div class="stat-label">ความพึงพอใจ</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Image Content (Desktop Only) -->
                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="hero-image-wrapper">
                                    <img src="img/pr/pic4.png" alt="Beauty Treatment" class="hero-image">
                                    <div class="experience-badge">
                                        <div class="badge-content">
                                            <span class="years">15</span>
                                            <span class="text">Years of<br>Excellence</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Default Slide 2 -->
            <div class="swiper-slide">
                <!-- Background Layer -->
                <div class="hero-background" style="background-image: url('img/pr/pic6.1.png')"></div>
                
                <!-- Gradient Overlay -->
                <div class="overlay-gradient"></div>
                
                <!-- Content Wrapper -->
                <div class="hero-content-wrapper">
                    <div class="container-xxl">
                        <!-- Floating Elements -->
                        <div class="floating-elements">
                            <div class="floating-circle circle-1"></div>
                            <div class="floating-circle circle-2"></div>
                            <div class="floating-circle circle-3"></div>
                        </div>
                        
                        <div class="row align-items-center">
                            <!-- Text Content -->
                            <div class="col-lg-6">
                                <div class="hero-text-content" data-swiper-animation="animate__fadeInUp">
                                    <h5 class="hero-subtitle-top">Innovation & Technology</h5>
                                    <h1 class="hero-title">
                                        นวัตกรรมความงาม<br>
                                        <span class="gradient-text">ระดับพรีเมียม</span>
                                    </h1>
                                    <p class="hero-subtitle">
                                        ยกระดับผิวพรรณของคุณด้วยเทคโนโลยีล่าสุด<br>
                                        จากผู้เชี่ยวชาญด้านความงามระดับแนวหน้า
                                    </p>
                                    <div class="treatment-features">
                                        <div class="feature-item">
                                            <i class="fas fa-certificate text-gradient"></i>
                                            <span>มาตรฐานระดับสากล</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-heart text-gradient"></i>
                                            <span>ดูแลด้วยใจ</span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-thumbs-up text-gradient"></i>
                                            <span>ผลลัพธ์ที่ดีที่สุด</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Image Content (Desktop Only) -->
                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="hero-image-wrapper">
                                    <img src="img/pr/pic3.png" alt="Innovation" class="hero-image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        
        <!-- Navigation -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        
        <!-- Pagination -->
        <div class="swiper-pagination"></div>
    </div>
</div>