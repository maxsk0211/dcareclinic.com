<section id="services" class="services-section mb-4">
    <!-- Section Header -->
    <div class="text-center mb-5">
        <span class="badge bg-primary bg-opacity-10 text-primary mb-2">Our Services</span>
        <h2 class="display-6 fw-bold mb-3">บริการของเรา</h2>
        <p class="text-muted col-md-8 mx-auto">เราให้บริการด้านความงามครบวงจร ด้วยมาตรฐานระดับสากล 
            โดยทีมแพทย์ผู้เชี่ยวชาญและเทคโนโลยีทันสมัย</p>
    </div>

    <!-- Category Navigation -->
    <div class="category-nav text-center mb-4">
      <div class="btn-group" role="group" aria-label="Service categories">
        <button type="button" class="btn btn-primary active" data-filter="all">ทั้งหมด</button>
        <?php
        // ดึงข้อมูลหมวดหมู่จากตาราง frontend_categories
        $sql_cat = "SELECT id, name, slug FROM frontend_categories WHERE status = 1 ORDER BY display_order ASC, name ASC";
        $result_cat = $conn->query($sql_cat);
        
        if ($result_cat && $result_cat->num_rows > 0) {
          while ($row_cat = $result_cat->fetch_assoc()) {
            echo '<button type="button" class="btn btn-outline-primary" data-filter="'.$row_cat['slug'].'">'.$row_cat['name'].'</button>';
          }
        }
        ?>
      </div>
    </div>

    <div class="row g-4 services-container">
  <?php
  // ดึงข้อมูลบริการจากตาราง frontend_services
  $sql_services = "SELECT fs.*, c.course_name, c.course_pic, c.course_detail, fc.name AS category_name, fc.slug AS category_slug
                   FROM frontend_services fs
                   JOIN course c ON fs.course_id = c.course_id
                   JOIN frontend_categories fc ON fs.frontend_category_id = fc.id
                   WHERE fs.status = 1
                   ORDER BY fs.is_featured DESC, fs.display_order ASC";
  $result_services = $conn->query($sql_services);
  
  if ($result_services && $result_services->num_rows > 0) {
    while ($row = $result_services->fetch_assoc()) {
      // ใช้ราคาที่กำหนดในบริการหน้าเว็บหลัก หรือราคาจากคอร์ส
      $price = $row['custom_price'] ?? $row['course_price'];
      $original_price = $row['custom_original_price'] ?? null;
      
      // ใช้รูปภาพที่กำหนดสำหรับหน้าเว็บหลัก หรือรูปภาพจากคอร์ส
      $imgPath = $row['image_path'] ?? $row['course_pic'] ?? 'course.png';
      
      // ใช้คำอธิบายพิเศษหรือคำอธิบายจากคอร์ส
      $description = $row['custom_description'] ?? $row['course_detail'];
  ?>
  <div class="col-lg-4 col-md-6 service-item" data-category="<?php echo $row['category_slug']; ?>">
    <div class="card service-card h-100">
      <div class="card-image-wrapper">
        <?php if ($row['badge_text']) { ?>
          <div class="popular-badge">
            <i class="fas fa-crown"></i> <?php echo $row['badge_text']; ?>
          </div>
        <?php } ?>
        <img src="img/course/<?php echo $imgPath; ?>" class="card-img-top" alt="<?php echo $row['course_name']; ?>">
        <div class="image-overlay"></div>
      </div>
      <div class="card-body">
        <div class="service-icon">
          <i class="fas fa-spa"></i>
        </div>
        <h5 class="card-title"><?php echo $row['course_name']; ?></h5>
        <p class="card-text"><?php echo mb_substr($description, 0, 100) . (mb_strlen($description) > 100 ? '...' : ''); ?></p>
        
        <?php if ($row['session_duration'] || $row['additional_features']) { ?>
        <div class="service-features">
          <?php if ($row['session_duration']) { ?>
          <div class="feature">
            <i class="fas fa-clock"></i>
            <span class="text-black"><?php echo $row['session_duration']; ?> นาที</span>
          </div>
          <?php } ?>
          
          <?php 
          if ($row['additional_features']) {
            $features = explode("\n", $row['additional_features']);
            foreach ($features as $feature) {
              if (trim($feature)) {
                echo '<div class="feature"><i class="fas fa-check"></i><span class="text-black">'.trim($feature).'</span></div>';
              }
            }
          }
          ?>
        </div>
        <?php } ?>

        <div class="service-price text-black">
          เริ่มต้น 
          <?php if ($original_price) { ?>
            <span class="original-price text-decoration-line-through"><?php echo number_format($original_price, 0); ?>฿</span>
          <?php } ?>
          <span class="price"><?php echo number_format($price, 0); ?>฿</span>
        </div>

        <div class="card-actions">
          <a href="service-detail.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary waves-effect">
            <i class="fas fa-info-circle me-1"></i>รายละเอียด
          </a>
          <a href="booking.php?service=<?php echo $row['id']; ?>" class="btn btn-primary waves-effect">
            <i class="fas fa-calendar-plus me-1"></i>จองคิว
          </a>
        </div>
      </div>
    </div>
  </div>
  <?php
    }
  } else {
    echo '<div class="col-12 text-center"><p>ไม่พบข้อมูลบริการ</p></div>';
  }
  ?>
</div>
</section>


<script>
  $(document).ready(function() {
    // กรองบริการตามหมวดหมู่
    $('.category-nav button').click(function() {
      const filter = $(this).data('filter');
      
      // เปลี่ยนปุ่มที่เลือก
      $('.category-nav button').removeClass('active');
      $(this).addClass('active');
      
      if (filter === 'all') {
        // แสดงทั้งหมด
        $('.service-item').show();
      } else {
        // ซ่อนทั้งหมดแล้วแสดงเฉพาะที่ตรงกับเงื่อนไข
        $('.service-item').hide();
        $('.service-item[data-category="' + filter + '"]').show();
      }
    });
  });
</script>
