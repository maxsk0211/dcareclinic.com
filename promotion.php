<style>
  /* สไตล์สำหรับส่วนโปรโมชั่น */
  .promotion-section {
    padding: 20px 0;
  }
  
  .promotion-card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
  }
  
  .promotion-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
  }
  
  .premium-card {
    border: 2px solid rgba(var(--bs-primary-rgb), 0.2);
  }
  
  .promotion-image img {
    height: 100%;
    object-fit: cover;
    border-top-right-radius: 15px;
    border-bottom-right-radius: 15px;
  }
  
  .promotion-title {
    font-weight: bold;
    color: var(--bs-primary);
  }
  
  .countdown-timer {
    background-color: rgba(var(--bs-danger-rgb), 0.1);
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: 500;
    color: var(--bs-danger);
    font-size: 0.85rem;
  }
  
  .countdown-timer .days,
  .countdown-timer .hours,
  .countdown-timer .minutes {
    font-weight: bold;
  }
  
  .promotion-price .original-price {
    font-size: 1rem;
    color: var(--bs-gray);
  }
  
  .promotion-price .current-price {
    font-size: 1.5rem;
    color: var(--bs-primary);
  }
  
  .discount-badge {
    background-color: var(--bs-danger);
    color: white;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: bold;
  }
  
  .promotion-features {
    margin-top: 1rem;
  }
  
  .feature-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
  }
  
  .feature-item i {
    color: var(--bs-success);
    margin-right: 0.5rem;
  }
  
  .promotion-actions {
    margin-top: 1.5rem;
  }
</style>

<section id="promotion" class="promotion-section mb-5">
    <!-- Section Header -->
    <div class="text-center mb-5">
        <span class="badge bg-primary bg-opacity-10 text-primary mb-2">Special Offers</span>
        <h2 class="display-6 fw-bold mb-3">โปรโมชั่นสุดพิเศษ</h2>
        <p class="text-muted col-md-8 mx-auto">
            ยกระดับความงามของคุณด้วยโปรโมชั่นสุดคุ้มจาก D Care Clinic 
            พร้อมมอบประสบการณ์การดูแลผิวระดับพรีเมียม
        </p>
    </div>

    <!-- Featured Promotion Carousel -->
    <div class="featured-promotion mb-5">
        <div class="swiper promotion-swiper">
            <div class="swiper-wrapper">
                <?php
                // ดึงข้อมูลโปรโมชั่นแนะนำ
                $today = date('Y-m-d');
                $sql_featured = "SELECT fp.*, c.course_name, c.course_pic 
                                FROM frontend_promotions fp
                                JOIN course c ON fp.course_id = c.course_id
                                WHERE fp.is_featured = 1 
                                AND fp.status = 1 
                                AND fp.start_date <= '$today' 
                                AND fp.end_date >= '$today'
                                ORDER BY fp.display_order ASC, fp.start_date DESC
                                LIMIT 5";
                $result_featured = $conn->query($sql_featured);
                
                if ($result_featured && $result_featured->num_rows > 0) {
                    while ($row = $result_featured->fetch_assoc()) {
                        // กำหนดรูปภาพ
                        $imgPath = $row['image_path'] ?? $row['course_pic'] ?? 'promotion.png';
                        $imgUrl = "img/" . ($row['image_path'] ? "promotion/{$imgPath}" : "course/{$imgPath}");
                        
                        // คำนวณเปอร์เซ็นต์ส่วนลด
                        $discount_display = '';
                        if (!empty($row['discount_percent'])) {
                            $discount_display = '<span class="discount-badge ms-2">-' . round($row['discount_percent']) . '%</span>';
                        }
                        
                        // คำนวณวันที่เหลือ
                        $end_date = strtotime($row['end_date']);
                        $now = strtotime('now');
                        $days_remaining = round(($end_date - $now) / (60 * 60 * 24));
                        $hours_remaining = round(($end_date - $now) / (60 * 60)) % 24;
                        $minutes_remaining = round(($end_date - $now) / 60) % 60;
                ?>
                <!-- Slide -->
                <div class="swiper-slide">
                    <div class="card promotion-card premium-card border-0">
                        <div class="row g-0 align-items-center">
                            <div class="col-lg-6">
                                <div class="promotion-content p-4 p-lg-5">
                                    <div class="d-flex align-items-center mb-3">
                                        <?php if (!empty($row['badge_text'])) { ?>
                                            <span class="badge bg-danger p-2 me-2"><?php echo $row['badge_text']; ?></span>
                                        <?php } ?>
                                        <div class="countdown-timer" data-end="<?php echo $row['end_date']; ?>">
                                            <i class="fas fa-clock me-2"></i>
                                            <span class="days"><?php echo $days_remaining; ?></span> วัน
                                            <span class="hours"><?php echo $hours_remaining; ?></span> ชั่วโมง
                                            <span class="minutes"><?php echo $minutes_remaining; ?></span> นาที
                                        </div>
                                    </div>
                                    <h3 class="promotion-title mb-3"><?php echo $row['title']; ?></h3>
                                    <p class="promotion-description mb-4">
                                        <?php echo !empty($row['description']) ? $row['description'] : $row['course_name']; ?>
                                    </p>
                                    <div class="promotion-price mb-4 ">
                                        <?php if (!empty($row['original_price'])) { ?>
                                            <div class="original-price">
                                                <span class="text-muted text-decoration-line-through"><?php echo number_format($row['original_price']); ?>฿</span>
                                            </div>
                                        <?php } ?>
                                        <div class="current-price">
                                            <span class="display-4 fw-bold text-primary"><?php echo number_format($row['promotion_price']); ?>฿</span>
                                            <?php echo $discount_display; ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($row['features'])) { ?>
                                        <div class="promotion-features mb-4">
                                            <?php
                                            $features = explode("\n", $row['features']);
                                            foreach ($features as $feature) {
                                                if (trim($feature)) {
                                                    echo '<div class="feature-item">
                                                            <i class="fas fa-check-circle text-success"></i>
                                                            <span class="text-black">'.trim($feature).'</span>
                                                        </div>';
                                                }
                                            }
                                            ?>
                                        </div>
                                    <?php } ?>
                                    <div class="promotion-actions">
                                        <a href="booking.php?promotion=<?php echo $row['id']; ?>" class="btn btn-primary btn-lg me-2">
                                            <i class="fas fa-calendar-plus me-2"></i>จองทันที
                                        </a>
                                        <a href="promotion-detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-lg">
                                            <i class="fas fa-info-circle me-2"></i>รายละเอียด
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="promotion-image">
                                    <img src="<?php echo $imgUrl; ?>" alt="<?php echo $row['title']; ?>" class="img-fluid rounded-end">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    // ถ้าไม่มีโปรโมชั่นแนะนำ
                    echo '<div class="swiper-slide">
                            <div class="card promotion-card premium-card border-0">
                                <div class="card-body text-center p-5">
                                    <i class="fas fa-gift fs-1 text-primary mb-3"></i>
                                    <h3 class="promotion-title mb-3">โปรโมชั่นเร็วๆ นี้</h3>
                                    <p class="promotion-description mb-4">
                                        ติดตามโปรโมชั่นพิเศษของเราเร็วๆ นี้ เพื่อรับข้อเสนอสุดพิเศษจาก D Care Clinic
                                    </p>
                                </div>
                            </div>
                        </div>';
                }
                ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
    
    <!-- Regular Promotions Grid -->
    <div class="container">
        <div class="mb-4">
            <h3 class="h4 mb-0">โปรโมชั่นทั้งหมด</h3>
            <p class="text-muted">เลือกโปรโมชั่นที่ตรงความต้องการของคุณ</p>
        </div>
        
        <div class="row g-4">
            <?php
            // ดึงข้อมูลโปรโมชั่นทั้งหมดที่ไม่ใช่แนะนำ
            $sql_promotions = "SELECT fp.*, c.course_name, c.course_pic 
                            FROM frontend_promotions fp
                            JOIN course c ON fp.course_id = c.course_id
                            WHERE fp.status = 1 
                            AND fp.start_date <= '$today' 
                            AND fp.end_date >= '$today'
                            AND fp.is_featured = 0
                            ORDER BY fp.display_order ASC, fp.start_date DESC";
            $result_promotions = $conn->query($sql_promotions);
            
            if ($result_promotions && $result_promotions->num_rows > 0) {
                while ($row = $result_promotions->fetch_assoc()) {
                    // กำหนดรูปภาพ
                    $imgPath = $row['image_path'] ?? $row['course_pic'] ?? 'promotion.png';
                    $imgUrl = "img/" . ($row['image_path'] ? "promotion/{$imgPath}" : "course/{$imgPath}");
                    
                    // คำนวณวันที่เหลือ
                    $end_date = strtotime($row['end_date']);
                    $now = strtotime('now');
                    $days_remaining = round(($end_date - $now) / (60 * 60 * 24));
            ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card promotion-card h-100">
                        <div class="position-relative">
                            <img src="<?php echo $imgUrl; ?>" alt="<?php echo $row['title']; ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <?php if (!empty($row['badge_text'])) { ?>
                                <span class="badge bg-danger position-absolute top-0 end-0 m-3"><?php echo $row['badge_text']; ?></span>
                            <?php } ?>
                            <?php if ($days_remaining <= 7) { ?>
                                <div class="countdown-timer position-absolute bottom-0 start-0 m-3">
                                    <i class="fas fa-hourglass-half me-1"></i> เหลือเวลาอีก <?php echo $days_remaining; ?> วัน
                                </div>
                            <?php } ?>
                        </div>
                        <div class="card-body">
                            <h4 class="card-title"><?php echo $row['title']; ?></h4>
                            <p class="card-text small"><?php echo $row['course_name']; ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center my-3">
                                <div>
                                    <?php if (!empty($row['original_price'])) { ?>
                                        <span class="text-decoration-line-through text-muted me-2"><?php echo number_format($row['original_price']); ?>฿</span>
                                    <?php } ?>
                                    <span class="fs-4 fw-bold text-primary"><?php echo number_format($row['promotion_price']); ?>฿</span>
                                </div>
                                <?php if (!empty($row['discount_percent'])) { ?>
                                    <span class="badge bg-danger">ลด <?php echo round($row['discount_percent']); ?>%</span>
                                <?php } ?>
                            </div>
                            
                            <?php if (!empty($row['features'])) { 
                                $features = explode("\n", $row['features']);
                                $top_features = array_slice($features, 0, 2); // แสดงเฉพาะ 2 รายการแรก
                            ?>
                                <div class="small mb-3">
                                    <?php 
                                    foreach ($top_features as $feature) {
                                        if (trim($feature)) {
                                            echo '<div class="d-flex align-items-center mb-1">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    <span>'.trim($feature).'</span>
                                                </div>';
                                        }
                                    }
                                    
                                    // ถ้ามีรายการเพิ่มเติม
                                    if (count($features) > 2) {
                                        echo '<div class="text-primary small">+ อีก '.(count($features) - 2).' รายการ</div>';
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                            
                            <div class="d-grid gap-2">
                                <a href="promotion-detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-info-circle me-1"></i> รายละเอียด
                                </a>
                                <a href="booking.php?promotion=<?php echo $row['id']; ?>" class="btn btn-primary">
                                    <i class="fas fa-calendar-plus me-1"></i> จองตอนนี้
                                </a>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i> โปรโมชั่นถึง <?php echo date('d/m/Y', strtotime($row['end_date'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php
                }
            } else {
                // ถ้าไม่มีโปรโมชั่นทั่วไป
                echo '<div class="col-12">
                        <div class="alert alert-info text-center py-5">
                            <i class="fas fa-info-circle fs-3 mb-3"></i>
                            <h4>ไม่พบโปรโมชั่นในขณะนี้</h4>
                            <p>กรุณาติดตามโปรโมชั่นใหม่ของเราเร็วๆ นี้</p>
                        </div>
                    </div>';
            }
            ?>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // อัพเดทนับถอยหลัง
  function updateCountdowns() {
    document.querySelectorAll('.countdown-timer').forEach(function(timer) {
      const endDate = timer.getAttribute('data-end');
      const endTime = new Date(endDate).getTime();
      const now = new Date().getTime();
      
      const distance = endTime - now;
      
      if (distance < 0) {
        timer.innerHTML = '<i class="fas fa-hourglass-end me-2"></i> หมดเวลาแล้ว';
        return;
      }
      
      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      
      const daysElement = timer.querySelector('.days');
      const hoursElement = timer.querySelector('.hours');
      const minutesElement = timer.querySelector('.minutes');
      
      if (daysElement) daysElement.textContent = days;
      if (hoursElement) hoursElement.textContent = hours;
      if (minutesElement) minutesElement.textContent = minutes;
    });
  }
  
  // อัพเดทนับถอยหลังทุกนาที
  updateCountdowns();
  setInterval(updateCountdowns, 60000);
  
  // Initial Swiper
  const swiper = new Swiper('.promotion-swiper', {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    autoplay: {
      delay: 5000,
      disableOnInteraction: false,
    },
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });
});
</script>