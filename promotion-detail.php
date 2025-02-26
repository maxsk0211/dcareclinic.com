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

include 'header.php';
?>

<section class="py-5">
    <div class="container">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">หน้าแรก</a></li>
                <li class="breadcrumb-item"><a href="index.php#promotion">โปรโมชั่น</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $promotion['title']; ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8">
                <!-- Main Promotion Card -->
                <div class="card promotion-card premium-card border-0 mb-4">
                    <div class="card-body p-0">
                        <div class="position-relative">
                            <img src="<?php echo $imgUrl; ?>" alt="<?php echo $promotion['title']; ?>" class="img-fluid w-100" style="max-height: 400px; object-fit: cover;">
                            
                            <?php if (!empty($promotion['badge_text'])) { ?>
                                <span class="badge bg-danger position-absolute top-0 end-0 m-3 p-2 fs-6"><?php echo $promotion['badge_text']; ?></span>
                            <?php } ?>
                            
                            <?php if ($is_active && $days_remaining <= 7) { ?>
                                <div class="countdown-timer position-absolute bottom-0 start-0 m-3 p-2 fs-6">
                                    <i class="fas fa-clock me-1"></i> เหลือเวลาอีก <?php echo $days_remaining; ?> วัน
                                </div>
                            <?php } ?>
                        </div>
                        
                        <div class="p-4">
                            <div class="mb-4">
                                <h1 class="h2 promotion-title"><?php echo $promotion['title']; ?></h1>
                                <p class="text-muted mb-0"><?php echo $promotion['course_name']; ?></p>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h4 class="h5 text-primary">ราคา</h4>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($promotion['original_price'])) { ?>
                                                <span class="text-decoration-line-through text-muted me-2"><?php echo number_format($promotion['original_price']); ?>฿</span>
                                            <?php } ?>
                                            <span class="fs-2 fw-bold text-primary"><?php echo number_format($promotion['promotion_price']); ?>฿</span>
                                            
                                            <?php if (!empty($discount_percent)) { ?>
                                                <span class="badge bg-danger ms-2 p-2">ลด <?php echo $discount_percent; ?>%</span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h4 class="h5 text-primary">ระยะเวลาโปรโมชั่น</h4>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar-alt text-primary me-2"></i>
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
                                <div class="countdown-timer-large bg-light p-3 rounded mb-4 text-center">
                                    <h4 class="h5 text-danger mb-2">
                                        <i class="fas fa-hourglass-half me-2"></i> โปรโมชั่นนี้จะสิ้นสุดใน
                                    </h4>
                                    <div class="row justify-content-center g-0">
                                        <div class="col-3 col-md-2">
                                            <div class="bg-white rounded p-2 m-1 shadow-sm">
                                                <div class="display-4 fw-bold text-danger days"><?php echo $days_remaining; ?></div>
                                                <div class="small">วัน</div>
                                            </div>
                                        </div>
                                        <div class="col-3 col-md-2">
                                            <div class="bg-white rounded p-2 m-1 shadow-sm">
                                                <div class="display-4 fw-bold text-danger hours"><?php echo $hours_remaining; ?></div>
                                                <div class="small">ชั่วโมง</div>
                                            </div>
                                        </div>
                                        <div class="col-3 col-md-2">
                                            <div class="bg-white rounded p-2 m-1 shadow-sm">
                                                <div class="display-4 fw-bold text-danger minutes"><?php echo $minutes_remaining; ?></div>
                                                <div class="small">นาที</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            
                            <div class="mb-4">
                                <h4 class="h5 text-primary">รายละเอียด</h4>
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
                                <div class="mb-4">
                                    <h4 class="h5 text-primary">คุณสมบัติพิเศษ</h4>
                                    <div class="row">
                                        <?php foreach ($features as $feature) { ?>
                                            <div class="col-md-6">
                                                <div class="feature-item mb-2">
                                                    <i class="fas fa-check-circle text-success fs-5"></i>
                                                    <span class="ms-2"><?php echo $feature; ?></span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            
                            <div class="promotion-actions mt-4">
                                <?php if ($is_active) { ?>
                                    <a href="booking.php?promotion=<?php echo $promotion['id']; ?>" class="btn btn-primary btn-lg">
                                        <i class="fas fa-calendar-plus me-2"></i>จองทันที
                                    </a>
                                <?php } else { ?>
                                    <button class="btn btn-secondary btn-lg" disabled>
                                        <i class="fas fa-calendar-times me-2"></i>โปรโมชั่นนี้สิ้นสุดแล้ว
                                    </button>
                                <?php } ?>
                                
                                <a href="index.php#promotion" class="btn btn-outline-primary btn-lg ms-2">
                                    <i class="fas fa-tags me-2"></i>ดูโปรโมชั่นอื่น
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Contact Info -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-phone-alt me-2"></i>สอบถามข้อมูลเพิ่มเติม</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-sm bg-primary-subtle me-3">
                                <i class="fas fa-phone-alt text-primary"></i>
                            </div>
                            <div>
                                <div class="small text-muted">โทรศัพท์</div>
                                <div class="fw-bold">081-234-5678</div>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar avatar-sm bg-primary-subtle me-3">
                                <i class="fas fa-line text-primary"></i>
                            </div>
                            <div>
                                <div class="small text-muted">Line ID</div>
                                <div class="fw-bold">@dcareclinic</div>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm bg-primary-subtle me-3">
                                <i class="fas fa-clock text-primary"></i>
                            </div>
                            <div>
                                <div class="small text-muted">เวลาทำการ</div>
                                <div class="fw-bold">เปิดทุกวัน 10:00 - 20:00 น.</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Related Promotions -->
                <?php if ($result_related && $result_related->num_rows > 0) { ?>
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-tags me-2"></i>โปรโมชั่นที่คุณอาจสนใจ</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php
                            while ($related = $result_related->fetch_assoc()) {
                                // กำหนดรูปภาพ
                                $relImgPath = $related['image_path'] ?? $related['course_pic'] ?? 'promotion.png';
                                $relImgUrl = "img/" . ($related['image_path'] ? "promotion/{$relImgPath}" : "course/{$relImgPath}");
                            ?>
                                <div class="d-flex p-3 border-bottom">
                                    <img src="<?php echo $relImgUrl; ?>" alt="<?php echo $related['title']; ?>" class="me-3" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px;">
                                    <div>
                                        <h6 class="mb-1"><?php echo $related['title']; ?></h6>
                                        <div class="d-flex align-items-center mb-1">
                                            <?php if (!empty($related['original_price'])) { ?>
                                                <span class="text-decoration-line-through text-muted me-2 small"><?php echo number_format($related['original_price']); ?>฿</span>
                                            <?php } ?>
                                            <span class="fw-bold text-primary"><?php echo number_format($related['promotion_price']); ?>฿</span>
                                        </div>
                                        <a href="promotion-detail.php?id=<?php echo $related['id']; ?>" class="btn btn-sm btn-outline-primary">รายละเอียด</a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // อัพเดทนับถอยหลัง
  function updateCountdown() {
    const days = document.querySelector('.countdown-timer-large .days');
    const hours = document.querySelector('.countdown-timer-large .hours');
    const minutes = document.querySelector('.countdown-timer-large .minutes');
    
    if (!days || !hours || !minutes) return;
    
    const endDate = new Date('<?php echo $promotion['end_date']; ?>').getTime();
    const now = new Date().getTime();
    
    const distance = endDate - now;
    
    if (distance < 0) {
      document.querySelector('.countdown-timer-large').innerHTML = '<div class="alert alert-secondary">โปรโมชั่นนี้สิ้นสุดแล้ว</div>';
      return;
    }
    
    const daysValue = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hoursValue = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutesValue = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    
    days.textContent = daysValue;
    hours.textContent = hoursValue;
    minutes.textContent = minutesValue;
  }
  
  // อัพเดทนับถอยหลังทุกนาที
  <?php if ($is_active) { ?>
  updateCountdown();
  setInterval(updateCountdown, 60000);
  <?php } ?>
});
</script>

<?php include 'footer.php'; ?>