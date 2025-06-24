<?php
// Note: session is already started in dashboard.php or other main files
// require database connection
require_once __DIR__ . '/../../config/db_connection.php';

// Check if admin_page_title is not set, set default
if (!isset($admin_page_title)) {
    $admin_page_title = 'Quản trị hệ thống';
}

// Kiểm tra trạng thái sidebar từ cookie
$sidebarCollapsed = isset($_COOKIE['sidebar_collapsed']) && $_COOKIE['sidebar_collapsed'] === 'true';

// Kiểm tra dark mode từ cookie
$darkMode = isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($admin_page_title); ?> - VLXD Online</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="../../assets/images/favicon.ico" type="image/x-icon">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS for admin panel -->
    <style>
        :root {
            /* Primary colors */
            --primary-color: #4361ee;
            --primary-light: #4895ef;
            --primary-dark: #3f37c9;
            --secondary-color: #4f5d75;
            
            /* Text colors */
            --text-primary: #2b2d42;
            --text-secondary: #6c757d;
            --text-muted: #8d99ae;
            --text-light: #f8f9fa;
            
            /* Background colors */
            --bg-primary: #f8f9fc;
            --bg-secondary: #f1f3f9;
            --bg-light: #ffffff;
            --bg-dark: #212529;
            
            /* Status colors */
            --success: #10b981;
            --info: #3b82f6;
            --warning: #f59e0b;
            --danger: #ef4444;
            
            /* Structure variables */
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 70px;
            --topbar-height: 70px;
            --card-shadow: 0 0.25rem 1rem rgba(47, 65, 146, 0.08);
            --transition-speed: 0.3s;
            
            /* Border radius */
            --border-radius-sm: 0.25rem;
            --border-radius-md: 0.5rem;
            --border-radius-lg: 0.75rem;
            --border-radius-xl: 1rem;
        }
        
        /* Dark Mode Variables */
        .dark-mode {
            --text-primary: #f0f0f0;
            --text-secondary: #c0c0c0;
            --text-muted: #b0b0b0;
            
            --bg-primary: #121212;
            --bg-secondary: #1e1e1e;
            --bg-light: #2a2a2a;
            --bg-dark: #000000;
            
            --card-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.35);
            
            color-scheme: dark;
        }
        
        /* Global Styles */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-weight: 400;
            line-height: 1.6;
            transition: background-color var(--transition-speed), color var(--transition-speed);
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
            padding: 0;
            margin: 0;
        }
        
        /* Dark Mode Specific Styles */
        .dark-mode .card,
        .dark-mode .modal-content,
        .dark-mode .dropdown-menu {
            background-color: var(--bg-light);
            border-color: rgba(255, 255, 255, 0.08);
        }
        
        .dark-mode .card-header {
            background-color: var(--bg-light);
            border-color: rgba(255, 255, 255, 0.08);
        }
        
        .dark-mode .table {
            color: var(--text-secondary);
        }
        
        .dark-mode .table-striped>tbody>tr:nth-of-type(odd)>* {
            background-color: rgba(255, 255, 255, 0.02);
        }
        
        .dark-mode .nav-link,
        .dark-mode .modal-header,
        .dark-mode .dropdown-item,
        .dark-mode .form-control,
        .dark-mode .form-select {
            color: var(--text-secondary);
        }
        
        .dark-mode .table-dark {
            background-color: var(--bg-dark);
        }
        
        .dark-mode .form-control,
        .dark-mode .form-select {
            background-color: var(--bg-secondary);
            border-color: rgba(255, 255, 255, 0.08);
        }
        
        .dark-mode .form-control:focus,
        .dark-mode .form-select:focus {
            background-color: var(--bg-secondary);
            border-color: rgba(255, 255, 255, 0.2);
            color: var(--text-primary);
        }
        
        .dark-mode .border-bottom,
        .dark-mode .border-top,
        .dark-mode .border-start,
        .dark-mode .border-end {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        
        /* Wrapper and layout */
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
            padding: 0;
            margin: 0;
            background-color: var(--bg-primary);
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
            color: #fff;
            z-index: 999;
            transition: transform var(--transition-speed);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.2) transparent;
        }
        
        .dark-mode .sidebar {
            background: linear-gradient(135deg, #1a1a30 0%, #2d2d50 100%);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }
        
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
        }
        
        .sidebar.hidden {
            transform: translateX(-100%);
        }
        
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            height: var(--topbar-height);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-brand-icon {
            font-size: 1.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: var(--border-radius-md);
            margin-right: 0.75rem;
            transition: all var(--transition-speed);
        }
        
        .sidebar-brand-text {
            white-space: nowrap;
            transition: opacity var(--transition-speed);
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin: 0.75rem 1rem;
        }
        
        .sidebar-heading {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.6);
            white-space: nowrap;
            letter-spacing: 0.05em;
            margin-top: 0.5rem;
        }
        
        .nav-item {
            position: relative;
            margin-bottom: 0.25rem;
            padding: 0 0.75rem;
        }
        
        .nav-link {
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            transition: all 0.15s;
            white-space: nowrap;
            border-radius: var(--border-radius-md);
        }
        
        .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.15);
            font-weight: 500;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.07);
        }
        
        .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            transition: margin var(--transition-speed);
            min-width: 20px;
            text-align: center;
        }
        
        .sidebar.hidden .nav-link i {
            margin-right: 0;
            font-size: 1.2rem;
            min-width: 100%;
        }
        
        .sidebar.hidden .sidebar-brand-text,
        .sidebar.hidden .sidebar-heading,
        .sidebar.hidden .nav-link span,
        .sidebar.hidden .sidebar-footer span,
        .sidebar.hidden .sidebar-divider {
            opacity: 0;
            display: none;
        }
        
        /* Content Area */
        .content-wrapper {
            flex: 1;
            width: 100%;
            transition: margin-left var(--transition-speed);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            padding-top: 0;
            margin-top: 0;
            overflow-x: hidden;
        }
        
        .content-wrapper.sidebar-collapsed {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        /* Topbar */
        .topbar {
            height: var(--topbar-height);
            background-color: var(--bg-light);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            padding: 0 1.75rem;
            z-index: 98;
            transition: all var(--transition-speed);
            width: 100%;
            position: relative;
            top: 0;
            margin-bottom: 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .dark-mode .topbar {
            background-color: var(--bg-light);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-toggle {
            background: transparent;
            border: none;
            color: var(--primary-color);
            font-size: 1.5rem;
            line-height: 0;
            padding: 0.375rem;
            border-radius: var(--border-radius-md);
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
        }
        
        .dark-mode .sidebar-toggle {
            color: var(--primary-light);
        }
        
        .sidebar-toggle:hover {
            background: rgba(67, 97, 238, 0.08);
        }
        
        /* Float sidebar toggle button */
        .sidebar-toggle-float {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
            width: 45px;
            height: 45px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: none;
            justify-content: center;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }
        
        .sidebar-toggle-float:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }
        
        @media (max-width: 768px) {
            .sidebar-toggle-float {
                display: flex;
            }
        }
        
        .topbar-divider {
            width: 0;
            border-right: 1px solid rgba(0, 0, 0, 0.1);
            height: calc(var(--topbar-height) - 2rem);
            margin: 0 1rem;
        }
        
        .dark-mode .topbar-divider {
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        .topbar-navbar {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-grow: 1;
        }
        
        .topbar-nav-item {
            position: relative;
            margin: 0 5px;
        }
        
        .topbar-nav-link {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem;
            border-radius: 50%;
            color: var(--text-secondary);
            transition: all 0.2s;
            text-decoration: none;
            width: 36px;
            height: 36px;
            aspect-ratio: 1/1;
            flex-shrink: 0;
        }
        
        .topbar-nav-link i {
            font-size: 1.1rem;
            line-height: 1;
        }
        
        .topbar-nav-link:hover {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
        }
        
        .notification-counter {
            position: absolute;
            top: 0;
            right: 0;
            width: 18px;
            height: 18px;
            background-color: var(--danger);
            color: white;
            font-size: 0.7rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--bg-light);
            z-index: 1;
        }
        
        .dropdown-menu {
            padding: 0.5rem 0;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
        }
        
        .avatar-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: white;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            font-weight: 600;
            overflow: hidden;
            aspect-ratio: 1/1;
            flex-shrink: 0;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .dropdown-menu .avatar-placeholder {
            margin: 0 auto;
        }
        
        /* Overlay for sidebar */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }
        
        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body class="<?php echo $darkMode ? 'dark-mode' : ''; ?>">
    <!-- Main page wrapper -->
    <div class="wrapper">
        <!-- Sidebar will be included separately -->
        
        <!-- Content Wrapper -->
        <div class="content-wrapper <?php echo $sidebarCollapsed ? 'sidebar-collapsed' : ''; ?>">
            <!-- Topbar -->
            <nav class="topbar">
                <!-- Sidebar Toggle (Topbar) -->
                <button type="button" class="sidebar-toggle" id="sidebarToggle">
                    <i class="bi <?php echo $sidebarCollapsed ? 'bi-arrow-right-square-fill' : 'bi-arrow-left-square-fill'; ?>"></i>
                </button>
                
                <a href="dashboard.php" class="d-none d-md-inline-block text-decoration-none ms-3">
                    <h5 class="text-primary m-0 fw-semibold"><?php echo htmlspecialchars($admin_page_title); ?></h5>
                </a>
                
                <!-- Topbar Navbar -->
                <ul class="topbar-navbar list-unstyled mb-0">
                    <!-- Topbar Search -->
                    <li class="topbar-nav-item d-none d-md-block">
                        <form class="d-flex me-3">
                            <input class="form-control me-2" type="search" placeholder="Tìm kiếm..." aria-label="Search">
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                        </form>
                    </li>
                    
                    <div class="topbar-divider d-none d-md-block"></div>
                    
                    <!-- Dark Mode Toggle -->
                    <li class="topbar-nav-item mx-1">
                        <button class="topbar-nav-link btn" id="darkModeToggle" title="Chế độ tối/sáng">
                            <i class="bi <?php echo $darkMode ? 'bi-sun-fill' : 'bi-moon-fill'; ?>"></i>
                        </button>
                    </li>
                    
                    <!-- Alerts / Notifications -->
                    <li class="topbar-nav-item mx-2 dropdown no-arrow">
                        <a class="topbar-nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell-fill"></i>
                            <span class="notification-counter">3</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="alertsDropdown" style="min-width: 18rem;">
                            <h6 class="dropdown-header border-bottom">Thông báo</h6>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="me-3">
                                    <div class="icon-circle bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                        <i class="bi bi-cart"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-muted">12/05/2023</div>
                                    <span>Đơn hàng mới #1234 đã được đặt</span>
                                </div>
                            </a>
                            <a class="dropdown-item text-center small text-primary" href="#">Xem tất cả thông báo</a>
                        </div>
                    </li>
                    
                    <!-- Messages -->
                    <li class="topbar-nav-item mx-2 dropdown no-arrow">
                        <a class="topbar-nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-envelope-fill"></i>
                            <span class="notification-counter">2</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="messagesDropdown" style="min-width: 18rem;">
                            <h6 class="dropdown-header border-bottom">Tin nhắn</h6>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <div class="me-3">
                                    <div class="avatar-placeholder">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-muted">Nguyễn Văn A</div>
                                    <span>Tôi cần hỗ trợ về đơn hàng...</span>
                                </div>
                            </a>
                            <a class="dropdown-item text-center small text-primary" href="#">Xem tất cả tin nhắn</a>
                        </div>
                    </li>
                    
                    <div class="topbar-divider"></div>
                    
                    <!-- User Information -->
                    <li class="topbar-nav-item dropdown no-arrow">
                        <a class="topbar-nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="avatar-placeholder" style="width: 36px; height: 36px; background: linear-gradient(45deg, #4361ee, #3a0ca3); font-size: 1.1rem; font-weight: 600;">
                                <?php 
                                $userInitial = mb_substr($_SESSION['admin_fullname'] ?? $_SESSION['admin_username'] ?? 'A', 0, 1, 'UTF-8');
                                echo strtoupper($userInitial); 
                                ?>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                            <div class="dropdown-item text-center border-bottom pb-3">
                                <div class="avatar-placeholder mx-auto mb-2" style="width: 60px; height: 60px; background: linear-gradient(45deg, #4361ee, #3a0ca3); font-size: 1.8rem; font-weight: 600;">
                                    <?php echo strtoupper($userInitial); ?>
                                </div>
                                <div class="fw-bold">
                                    <?php echo htmlspecialchars($_SESSION['admin_fullname'] ?? $_SESSION['admin_username'] ?? 'Admin'); ?>
                                </div>
                                <div class="small text-muted">Quản trị viên</div>
                            </div>
                            <a class="dropdown-item" href="profile.php">
                                <i class="bi bi-person-fill me-2 text-gray-400"></i>
                                Hồ sơ
                            </a>
                            <a class="dropdown-item" href="settings.php">
                                <i class="bi bi-gear-fill me-2 text-gray-400"></i>
                                Cài đặt
                            </a>
                            <a class="dropdown-item" href="../index.php" target="_blank">
                                <i class="bi bi-shop me-2 text-gray-400"></i>
                                Xem cửa hàng
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="admin_logout.php">
                                <i class="bi bi-box-arrow-right me-2 text-gray-400"></i>
                                Đăng xuất
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            
            <!-- Floating sidebar toggle button for mobile -->
            <button type="button" class="sidebar-toggle-float" id="sidebarToggleFloat">
                <i class="bi bi-list"></i>
            </button>
            
            <!-- Sidebar overlay for mobile -->
            <div class="sidebar-overlay" id="sidebarOverlay"></div>
            
            <!-- Begin Page Content -->
            <div class="main-content">
</body>
</html>
 