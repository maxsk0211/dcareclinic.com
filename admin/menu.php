      <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
        <div class="container-xxl d-flex h-100">
          <ul class="menu-inner">
            <li class="menu-item">
              <div class="text-center mt-2 mb-5 d-block d-xl-none"> <span class="alert bg-primary text-white p-3" role="alert">สาขา : <?php echo $row_branch->branch_name; ?></span> </div>
            </li>
            <!-- Page -->
            <?php if (checkPagePermission('queue-management.php')) { ?>
            <li class="menu-item">
                <a href="queue-management.php" class="menu-link">
                    <i class="menu-icon tf-icons ri-calendar-todo-fill"></i>
                    <div data-i18n="จัดการคิว">จัดการคิว</div>
                </a>
            </li>
          <?php } ?>
          <?php if (checkPagePermission('booking.php')) { ?>
            <li class="menu-item">
              <a href="booking.php" class="menu-link">
                <i class="menu-icon tf-icons ri-file-line"></i>
                <div data-i18n="Page 2">การจองคอร์ส</div>
              </a>
            </li>
          <?php } ?>
          <?php if (checkPagePermission('order-list.php')) { ?>
            <li class="menu-item">
              <a href="order-list.php" class="menu-link">
                <i class="menu-icon tf-icons ri-file-line"></i>
                <div data-i18n="Page 2">จัดการใบสั่งซื้อ</div>
              </a>
<!--               <ul class="menu-sub">
                <li class="menu-item">
                  <a href="order-list-to-day.php" class="menu-link">
                    <i class="menu-icon tf-icons ri-file-line"></i>
                    <div data-i18n="Page 2">จัดการใบสั่งซื้อประจำวัน</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="order-list.php" class="menu-link">
                    <i class="menu-icon tf-icons ri-file-line"></i>
                    <div data-i18n="Page 2">จัดการใบสั่งซื้อทั้งหมด</div>
                  </a>
                </li>
              </ul> -->
            </li>
            <?php } ?>
            <?php if (checkPagePermission('customer.php')) { ?>
            <li class="menu-item">
              <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon ri-group-fill"></i>
                <div data-i18n="Dashboards">ลูกค้า</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="customer.php" class="menu-link" >
                    <i class="menu-icon tf-icons ri-shopping-cart-2-line"></i>
                    <div data-i18n="eCommerce">ข้อมูลลูกค้า</div>
                  </a>
                </li>
<!--                 <li class="menu-item">
                  <a href="dashboards-crm.html" class="menu-link">
                    <i class="menu-icon tf-icons ri-donut-chart-fill"></i>
                    <div data-i18n="CRM">CRM</div>
                  </a>
                </li> -->
              </ul>
            </li>
            <?php } ?>
            <?php if (checkPagePermission('course.php') or checkPagePermission('type-course.php')) { ?>
            <li class="menu-item">
              <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon ri-book-open-line"></i>
                <div data-i18n="Dashboards">คอร์ส</div>
              </a>
              <ul class="menu-sub">
                <?php if (checkPagePermission('course.php')) { ?>
                <li class="menu-item">
                  <a href="course.php" class="menu-link" >
                    <i class="menu-icon tf-icons ri-shopping-cart-2-line"></i>
                    <div data-i18n="eCommerce">จัดการคอร์ส</div>
                  </a>
                </li>
                <?php } ?>
                <?php if (checkPagePermission('type-course.php')) { ?>
                <li class="menu-item">
                  <a href="type-course.php" class="menu-link">
                    <i class="menu-icon tf-icons ri-donut-chart-fill"></i>
                    <div data-i18n="CRM">ประเภทคอร์ส</div>
                  </a>
                </li>
                <?php } ?>
              </ul>
            </li>
            <?php } ?>
            <?php if (checkPagePermission('drug.php') or checkPagePermission('drug-type.php') or checkPagePermission('accessories.php') or checkPagePermission('acc-type.php') or checkPagePermission('tool.php')) { ?>
            <li class="menu-item">
              <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon ri-server-line"></i>
                <div data-i18n="Dashboards">สต๊อกคลินิค</div>
              </a>
                <ul class="menu-sub">
                  <?php if (checkPagePermission('drug.php') or checkPagePermission('drug-type.php')) { ?>
                  <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                      <i class="menu-icon tf-icons ri-shopping-cart-2-line"></i>
                      <div data-i18n="eCommerce">ยา</div>
                    </a>
                    <ul class="menu-sub">
                      <?php if (checkPagePermission('drug.php')) { ?>
                      <li class="menu-item">
                        <a href="drug.php" class="menu-link">
                          <i class="menu-icon tf-icons ri-circle-fill"></i>
                          <div data-i18n="Dashboard">จัดการยา</div>
                        </a>
                      </li>
                      <?php } ?>
                      <?php if (checkPagePermission('drug-type.php')) { ?>
                      <li class="menu-item">
                        <a href="drug-type.php" class="menu-link">
                          <i class="menu-icon tf-icons ri-circle-fill"></i>
                          <div data-i18n="Dashboard">จัดการประเภทยา</div>
                        </a>
                      </li>
                      <?php } ?>
                    </ul>
                  </li>
                  <?php } ?>
                  <?php if (checkPagePermission('accessories.php') or checkPagePermission('acc-type.php')) { ?>
                  <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                      <i class="menu-icon tf-icons ri-shopping-cart-2-line"></i>
                      <div data-i18n="eCommerce">อุปกรณ์</div>
                    </a>
                    <ul class="menu-sub">
                      <?php if (checkPagePermission('accessories.php')) { ?>
                      <li class="menu-item">
                        <a href="accessories.php" class="menu-link">
                          <i class="menu-icon tf-icons ri-circle-fill"></i>
                          <div data-i18n="Dashboard">จัดการอุปกรณ์</div>
                        </a>
                      </li>
                      <?php } ?>
                      <?php if (checkPagePermission('acc-type.php')) { ?>
                      <li class="menu-item">
                        <a href="acc-type.php" class="menu-link">
                          <i class="menu-icon tf-icons ri-circle-fill"></i>
                          <div data-i18n="Dashboard">จัดการประเภทอุปกรณ์</div>
                        </a>
                      </li>
                      <?php } ?>
                    </ul>
                  </li>
                  <?php } ?>
                  <?php if (checkPagePermission('tool.php')) { ?>
                  <ul class="menu-item">
                    <li class="menu-item">
                      <a href="tool.php" class="menu-link">
                        <i class="menu-icon tf-icons ri-donut-chart-fill"></i>
                        <div data-i18n="CRM">จัดการเครื่องมือ</div>
                      </a>
                    </li>
                  </ul>
                  <?php } ?>
                </ul>
            </li>
            <?php } ?>
            <?php if (checkPagePermission('df.php')) { ?>
            <li class="menu-item">
              <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon ri-group-fill"></i>
                <div data-i18n="Dashboards">สรุปรายรับ - รายจ่าย</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="df.php" class="menu-link" >
                    <i class="menu-icon tf-icons ri-shopping-cart-2-line"></i>
                    <div data-i18n="eCommerce">สรุปค่า Doctor fee (DF.)</div>
                  </a>
                </li>
<!--                 <li class="menu-item">
                  <a href="dashboards-crm.html" class="menu-link">
                    <i class="menu-icon tf-icons ri-donut-chart-fill"></i>
                    <div data-i18n="CRM">CRM</div>
                  </a>
                </li> -->
              </ul>
            </li>
            <?php } ?>
            <?php if (checkPagePermission('manage-rooms.php') or checkPagePermission('room-occupancy-calendar.php')) { ?>
            <li class="menu-item">
              <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ri-calendar-todo-fill"></i>
                <div data-i18n="Dashboards">จัดการห้อง</div>
              </a>
              <ul class="menu-sub">
                <?php if (checkPagePermission('manage-rooms.php')) { ?>
                <li class="menu-item">
                  <a href="manage-rooms.php" class="menu-link" >
                    <i class="menu-icon tf-icons ri-shopping-cart-2-line"></i>
                    <div data-i18n="eCommerce">จัดการห้องประจำวัน</div>
                  </a>
                </li>
                <?php } ?>
                <?php if (checkPagePermission('room-occupancy-calendar.php')) { ?>
                <li class="menu-item">
                  <a href="room-occupancy-calendar.php" class="menu-link">
                    <i class="menu-icon tf-icons ri-donut-chart-fill"></i>
                    <div data-i18n="CRM">ปฏิทินการใช้งานห้อง</div>
                  </a>
                </li>
                <?php } ?>
              </ul>
            </li>
            <?php } ?>
            <?php if (checkPagePermission('gift-vouchers.php')) { ?>
            <li class="menu-item">
              <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon ri-group-fill"></i>
                <div data-i18n="Dashboards">บัตรกำนัล</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="gift-vouchers.php" class="menu-link" >
                    <i class="menu-icon tf-icons ri-shopping-cart-2-line"></i>
                    <div data-i18n="eCommerce">จัดการบัตรกำนัล</div>
                  </a>
                </li>
<!--                 <li class="menu-item">
                  <a href="dashboards-crm.html" class="menu-link">
                    <i class="menu-icon tf-icons ri-donut-chart-fill"></i>
                    <div data-i18n="CRM">CRM</div>
                  </a>
                </li> -->
              </ul>
            </li>
            <?php } ?>

          <?php if (checkPagePermission('frontend-management.php')) { ?>
          <li class="menu-item">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
              <i class="menu-icon ri-global-line"></i>
              <div data-i18n="Dashboards">จัดการข้อมูลเว็บไซต์หลัก</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item">
                <a href="frontend-categories.php" class="menu-link">
                  <i class="menu-icon tf-icons ri-folder-fill"></i>
                  <div data-i18n="eCommerce">หมวดหมู่บริการ</div>
                </a>
              </li>
              <li class="menu-item">
                <a href="frontend-services.php" class="menu-link">
                  <i class="menu-icon tf-icons ri-service-line"></i>
                  <div data-i18n="CRM">บริการที่แสดงบนเว็บไซต์</div>
                </a>
              </li>
            </ul>
          </li>
          <?php } ?>


          </ul>
        </div>
      </aside>  