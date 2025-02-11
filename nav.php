<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <!-- Brand Logo -->
    <div class="navbar-brand app-brand demo">
        <a href="index.html" class="app-brand-link">
            <span class="app-brand-logo demo">
                <img src="img/pr/logo.jpg" alt="Logo" width="40px" height="40px" class="rounded-circle" />
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">D Care Clinic</span>
        </a>
    </div>

    <!-- Hamburger for mobile -->
    <button class="navbar-toggler border-0 p-2 mx-5" type="button" data-bs-toggle="collapse" 
            data-bs-target="#navbar-collapse" aria-controls="navbar-collapse" 
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Collapsible wrapper -->
    <div class="collapse navbar-collapse" id="navbar-collapse">
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- Search - visible only on desktop -->
            <li class="nav-item d-none d-md-block">
                <a class="nav-link search-toggler" href="javascript:void(0);">
                    <i class="ti ti-search ti-md"></i>
                    <span class="d-none d-md-inline-block text-muted">ค้นหา</span>
                </a>
            </li>
            
            <!-- Main navigation items -->
            <li class="nav-item">
                <a class="nav-link py-3" href="#services">บริการ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-3" href="#promotion">โปรโมชั่น</a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-3" href="#doctors">แพทย์ผู้เชี่ยวชาญ</a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-3" href="#contact">ติดต่อเรา</a>
            </li>
            
            <!-- Login Button -->
            <li class="nav-item ms-xl-3">
                <a class="btn btn-primary btn-sm waves-effect waves-light" href="login.php">
                    <i class="ti ti-calendar-plus me-1"></i>
                    Login
                </a>
            </li>
        </ul>
    </div>
</nav>