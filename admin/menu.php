      <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
        <div class="container-xxl d-flex h-100">
          <ul class="menu-inner">
            <!-- Page -->

            <li class="menu-item">
              <div class="text-center mt-2 mb-5 d-block d-xl-none"> <span class="alert bg-primary text-white p-3" role="alert">สาขา : <?php echo $row_branch->branch_name; ?></span> </div>
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
                <li class="menu-item">
                  <a href="dashboards-crm.html" class="menu-link">
                    <i class="menu-icon tf-icons ri-donut-chart-fill"></i>
                    <div data-i18n="CRM">CRM</div>
                  </a>
                </li>
              </ul>
            </li>

            <li class="menu-item">
              <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon ri-book-open-line"></i>
                <div data-i18n="Dashboards">คอร์ส</div>
              </a>
              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="course.php" class="menu-link" >
                    <i class="menu-icon tf-icons ri-shopping-cart-2-line"></i>
                    <div data-i18n="eCommerce">จัดการคอร์ส</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="type-course.php" class="menu-link">
                    <i class="menu-icon tf-icons ri-donut-chart-fill"></i>
                    <div data-i18n="CRM">ประเภทคอร์ส</div>
                  </a>
                </li>
              </ul>
            </li>

            <li class="menu-item">
              <a href="javascript:void(0)" class="menu-link menu-toggle">
                <i class="menu-icon ri-server-line"></i>
                <div data-i18n="Dashboards">สต๊อกคลินิค</div>
              </a>
                <ul class="menu-sub">
                  <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                      <i class="menu-icon tf-icons ri-shopping-cart-2-line"></i>
                      <div data-i18n="eCommerce">ยา</div>
                    </a>
                    <ul class="menu-sub">
                      <li class="menu-item">
                        <a href="drug.php" class="menu-link">
                          <i class="menu-icon tf-icons ri-circle-fill"></i>
                          <div data-i18n="Dashboard">จัดการยา</div>
                        </a>
                      </li>
                      <li class="menu-item">
                        <a href="drug-type.php" class="menu-link">
                          <i class="menu-icon tf-icons ri-circle-fill"></i>
                          <div data-i18n="Dashboard">จัดการประเภทยา</div>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                      <i class="menu-icon tf-icons ri-shopping-cart-2-line"></i>
                      <div data-i18n="eCommerce">อุปกรณ์</div>
                    </a>
                    <ul class="menu-sub">
                      <li class="menu-item">
                        <a href="accessories.php" class="menu-link">
                          <i class="menu-icon tf-icons ri-circle-fill"></i>
                          <div data-i18n="Dashboard">จัดการอุปกรณ์</div>
                        </a>
                      </li>
                      <li class="menu-item">
                        <a href="acc-type.php" class="menu-link">
                          <i class="menu-icon tf-icons ri-circle-fill"></i>
                          <div data-i18n="Dashboard">จัดการประเภทอุปกรณ์</div>
                        </a>
                      </li>
                    </ul>
                  </li>



              <ul class="menu-item">
                <li class="menu-item">
                  <a href="tool.php" class="menu-link">
                    <i class="menu-icon tf-icons ri-donut-chart-fill"></i>
                    <div data-i18n="CRM">จัดการเครื่องมือ</div>
                  </a>
                </li>
              </ul>
            </ul>
            </li>

            <li class="menu-item">
              <a href="page-2.html" class="menu-link">
                <i class="menu-icon tf-icons ri-file-line"></i>
                <div data-i18n="Page 2">Page 2</div>
              </a>
            </li>
          </ul>
        </div>
      </aside>  