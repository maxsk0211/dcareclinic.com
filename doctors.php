<section id="doctors" class="doctors-section mb-4">
    <!-- Hero Section -->
    <div class="doctors-hero mb-5">
        <div class="doctors-hero-content text-center">
            <span class="badge bg-primary bg-opacity-10 text-primary mb-2">Our Specialists</span>
            <h2 class="display-6 fw-bold mb-3">แพทย์ผู้เชี่ยวชาญ</h2>
            <p class="text-muted col-md-8 mx-auto">
                ทีมแพทย์ผู้เชี่ยวชาญของเรา พร้อมดูแลคุณด้วยประสบการณ์กว่า 15 ปี 
                และการรับรองจากสถาบันชั้นนำระดับประเทศ
            </p>
        </div>
    </div>

    <!-- Doctors Grid -->
    <div class="row g-4">
        <?php
        // ดึงข้อมูลแพทย์จากฐานข้อมูล
        $sql = "SELECT * FROM frontend_doctors WHERE status = 1 ORDER BY display_order ASC, first_name ASC";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // กำหนดรูปภาพ
                $imgPath = $row['image_path'] ?? 'doctor.jpg';
                $imgUrl = "img/doctors/{$imgPath}";
                
                // แปลงข้อความที่มีการขึ้นบรรทัดใหม่ให้เป็น array
                $achievements = explode("\n", $row['achievements'] ?? '');
                $additional_features = explode("\n", $row['additional_features'] ?? '');
        ?>
        <!-- Doctor Card -->
        <div class="col-lg-4 col-md-6">
            <div class="doctor-card">
                <div class="doctor-card-inner">
                    <!-- Front Side -->
                    <div class="doctor-card-front">
                        <div class="doctor-image-wrapper">
                            <img src="<?php echo $imgUrl; ?>" class="doctor-image" alt="<?php echo $row['title'] . $row['first_name'] . ' ' . $row['last_name']; ?>">
                            <div class="certification-badge">
                                <i class="fas fa-certificate"></i>
                                <?php echo $row['certification'] ?? 'Certified Expert'; ?>
                            </div>
                        </div>
                        <div class="doctor-info">
                            <h5 class="doctor-name text-black"><?php echo $row['title'] . $row['first_name'] . ' ' . $row['last_name']; ?></h5>
                            <p class="doctor-specialty text-black"><?php echo $row['specialty']; ?></p>
                            <div class="doctor-credentials">
                                <?php if ($row['education']) { ?>
                                <span class="credential-item text-black">
                                    <i class="fas fa-graduation-cap"></i> <?php echo $row['education']; ?>
                                </span>
                                <?php } ?>
                                <?php if ($row['certification']) { ?>
                                <span class="credential-item text-black">
                                    <i class="fas fa-star"></i> <?php echo $row['certification']; ?>
                                </span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Back Side -->
                    <div class="doctor-card-back">
                        <div class="achievements">
                            <h6 class="achievement-title">ผลงานและความเชี่ยวชาญ</h6>
                            <?php if (!empty($achievements) && $achievements[0] !== '') { ?>
                            <ul class="achievement-list">
                                <?php foreach ($achievements as $achievement) { 
                                    if (trim($achievement) !== '') { ?>
                                <li class="text-black">
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo trim($achievement); ?>
                                </li>
                                <?php } } ?>
                            </ul>
                            <?php } ?>
                            
                            <?php if (!empty($additional_features) && $additional_features[0] !== '') { ?>
                            <h6 class="achievement-title mt-4">ความเชี่ยวชาญพิเศษ</h6>
                            <ul class="achievement-list">
                                <?php foreach ($additional_features as $feature) { 
                                    if (trim($feature) !== '') { ?>
                                <li class="text-black">
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo trim($feature); ?>
                                </li>
                                <?php } } ?>
                            </ul>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
            }
        } else {
            // ถ้าไม่มีข้อมูลแพทย์
            echo '<div class="col-12 text-center py-5">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle fs-3 mb-3 d-block"></i>
                        <h4>ยังไม่มีข้อมูลแพทย์</h4>
                        <p class="mb-0">ขออภัยในความไม่สะดวก เรากำลังอัพเดทข้อมูลแพทย์ผู้เชี่ยวชาญของเรา</p>
                    </div>
                </div>';
        }
        ?>
    </div>
</section>