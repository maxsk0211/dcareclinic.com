<style>
  /* เพิ่ม CSS เพื่อแก้ปัญหา */
  .service-item {
    transition: opacity 0.3s, transform 0.3s;
  }
  
  .service-item.filtered-out {
    display: none !important;
  }
  
  .service-item.filtered-in {
    display: block !important;
  }
  
  /* สไตล์สำหรับข้อความไม่พบบริการ */
  .no-services-alert {
    padding: 20px;
    margin: 30px auto;
    max-width: 600px;
    animation: fadeIn 0.5s;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>

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
      <div class="btn-group category-filter" role="group" aria-label="Service categories">
        <button type="button" class="btn btn-primary active" data-filter="all">ทั้งหมด</button>
        <?php
        // ดึงข้อมูลหมวดหมู่จากตาราง frontend_categories
        $sql_cat = "SELECT id, name, slug FROM frontend_categories WHERE status = 1 ORDER BY display_order ASC, name ASC";
        $result_cat = $conn->query($sql_cat);
        
        if ($result_cat && $result_cat->num_rows > 0) {
          while ($row_cat = $result_cat->fetch_assoc()) {
            // เก็บค่า slug เพื่อใช้ในการตรวจสอบจำนวนบริการในแต่ละหมวดหมู่
            $category_slugs[$row_cat['id']] = $row_cat['slug'];
            echo '<button type="button" class="btn btn-outline-primary" data-filter="'.$row_cat['id'].'" data-slug="'.$row_cat['slug'].'">'.$row_cat['name'].'</button>';
          }
        }
        ?>
      </div>
    </div>

    <div class="row g-4 services-container">
<?php
  // ดึงข้อมูลบริการจากตาราง frontend_services
  $sql_services = "SELECT fs.*, c.course_name, c.course_pic, c.course_detail, fc.name AS category_name, fc.slug AS category_slug, fc.id AS category_id
                   FROM frontend_services fs
                   JOIN course c ON fs.course_id = c.course_id
                   JOIN frontend_categories fc ON fs.frontend_category_id = fc.id
                   WHERE fs.status = 1
                   ORDER BY fs.is_featured DESC, fs.display_order ASC";
  $result_services = $conn->query($sql_services);
  
  // นับจำนวนบริการในแต่ละหมวดหมู่
  $category_counts = [];
  
  if ($result_services && $result_services->num_rows > 0) {
    while ($row = $result_services->fetch_assoc()) {
      // นับจำนวนบริการในแต่ละหมวดหมู่
      if (!isset($category_counts[$row['category_id']])) {
        $category_counts[$row['category_id']] = 0;
      }
      $category_counts[$row['category_id']]++;
      
      // ข้อมูลอื่นๆ เหมือนเดิม
      $price = $row['custom_price'] ?? $row['course_price'];
      $original_price = $row['custom_original_price'] ?? null;
      $imgPath = $row['image_path'] ?? $row['course_pic'] ?? 'course.png';
      $description = $row['custom_description'] ?? $row['course_detail'];
  ?>
  <div class="col-lg-4 col-md-6 service-item" data-category="<?php echo $row['category_id']; ?>" data-category-slug="<?php echo $row['category_slug']; ?>">
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
<div class="d-grid gap-2 mt-4">
  <a href="https://dcareclinic.com/packages.php" class=" btn btn-primary">ดูทั้งหมเ</a>
</div>

<!-- เพิ่มข้อมูลจำนวนบริการในแต่ละหมวดหมู่สำหรับการ debug -->
<div id="category-data" class="d-none" 
     data-counts='<?php echo json_encode($category_counts); ?>'></div>
</section>

<script>
// ตรวจสอบว่า jQuery พร้อมใช้งานหรือไม่
if (typeof jQuery === 'undefined') {
  document.write('<script src="https://code.jquery.com/jquery-3.6.0.min.js"><\/script>');
}

document.addEventListener('DOMContentLoaded', function() {
  function checkJQuery() {
    if (window.jQuery) {
      setTimeout(initializeServices, 100);
    } else {
      setTimeout(checkJQuery, 50);
    }
  }

  checkJQuery();
});

function initializeServices() {
  console.log("Initializing services version 3...");
  
  // ขั้นตอนแรก - กำจัด inline style และรีเซ็ตทุกอย่าง
  $('.service-item').each(function() {
    // ลบ inline style display:none ที่อาจมีอยู่
    $(this).removeAttr('style');
    
    // เพิ่ม class filtered-in เริ่มต้น (แสดงทุกรายการ)
    $(this).addClass('filtered-in').removeClass('filtered-out');
    
    // ลบ transform และ opacity ที่อาจถูกกำหนด
    $(this).find('.card').removeAttr('style');
  });
  
  // ตั้งค่าตัวกรองเริ่มต้นให้เป็น "ทั้งหมด"
  $('.category-filter button[data-filter="all"]').addClass('active');
  $('.category-filter button[data-filter!="all"]').removeClass('active');
  
  // กรองบริการตามหมวดหมู่
  $('.category-filter button').on('click', function() {
    const categoryId = $(this).data('filter');
    const categorySlug = $(this).data('slug');
    
    console.log("Filter selected - ID:", categoryId, "Slug:", categorySlug);
    
    // เปลี่ยนปุ่มที่เลือก
    $('.category-filter button').removeClass('active');
    $(this).addClass('active');
    
    // ลบข้อความแจ้งเตือนเดิม (ถ้ามี)
    $('#no-services-message').remove();
    
    if (categoryId === 'all') {
      // กรณีแสดงทั้งหมด
      $('.service-item').each(function() {
        $(this).removeClass('filtered-out').addClass('filtered-in');
        // สำคัญ: ต้องลบ inline style
        $(this).removeAttr('style').css('display', 'block');
      });
    } else {
      // กรณีกรองตามหมวดหมู่
      $('.service-item').each(function() {
        const itemCategoryId = $(this).data('category');
        
        // debug log
        console.log("Checking item:", itemCategoryId, "against filter:", categoryId);
        
        if (itemCategoryId == categoryId) {
          // รายการที่ตรงกับตัวกรอง
          $(this).removeClass('filtered-out').addClass('filtered-in');
          // สำคัญ: ต้องกำหนด display:block ทับ inline style
          $(this).removeAttr('style').css('display', 'block');
          console.log("Showing item:", itemCategoryId);
        } else {
          // รายการที่ไม่ตรงกับตัวกรอง
          $(this).removeClass('filtered-in').addClass('filtered-out');
          // สำคัญ: ต้องกำหนด display:none ทับ inline style
          $(this).removeAttr('style').css('display', 'none');
          console.log("Hiding item:", itemCategoryId);
        }
      });
      
      // ตรวจสอบว่ามีรายการที่ตรงกับตัวกรองหรือไม่
      const visibleItems = $('.service-item.filtered-in').length;
      console.log("Visible items after filter:", visibleItems);
      
      // ถ้าไม่พบรายการที่ตรงกับตัวกรอง
      if (visibleItems === 0) {
        // เพิ่มข้อความแจ้งเตือน
        $('.services-container').append(`
          <div id="no-services-message" class="col-12">
            <div class="alert alert-info no-services-alert text-center">
              <i class="fas fa-info-circle fa-lg me-2"></i>
              ขออภัย ไม่พบบริการในหมวดหมู่ "${$(this).text()}"
            </div>
          </div>
        `);
      }
    }
    
    // เพิ่มแอนิเมชันให้กับรายการที่แสดง
    $('.service-item.filtered-in').each(function(i) {
      $(this).css('opacity', '0').css('transform', 'translateY(20px)');
      setTimeout(() => {
        $(this).css('opacity', '1').css('transform', 'translateY(0)');
      }, i * 100);
    });
  });
  
  // ตรวจสอบ URL parameter เพื่อให้กรองตาม category ที่กำหนด
  function getUrlParameter(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
  }
  
  const categoryParam = getUrlParameter('category');
  if (categoryParam) {
    // ถ้ามี category ใน URL ให้คลิกที่ปุ่มตัวกรองนั้น
    $(`.category-filter button[data-filter="${categoryParam}"]`).trigger('click');
  } else {
    // ถ้าไม่มี category ใน URL ให้คลิกที่ปุ่ม "ทั้งหมด"
    $(`.category-filter button[data-filter="all"]`).trigger('click');
  }
}
</script>