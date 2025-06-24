<?php
// Include file kiểm tra đăng nhập
require_once 'auth_check.php';

// Lấy tên file hiện tại để xác định menu nào đang active
$current_file = basename($_SERVER['PHP_SELF']);
$current_folder = dirname($_SERVER['PHP_SELF']);
$current_path = $current_folder . '/' . $current_file;

// Các đường dẫn menu và title tương ứng
$menu_items = [
    'dashboard.php' => ['title' => 'Tổng quan', 'icon' => 'bi-speedometer2'],
    'categories_management/index.php' => ['title' => 'Quản lý Danh mục', 'icon' => 'bi-tag'],
    'products/index.php' => ['title' => 'Quản lý Sản phẩm', 'icon' => 'bi-box-seam'],
    'orders/index.php' => ['title' => 'Quản lý Đơn hàng', 'icon' => 'bi-cart'],
    'users_management/index.php' => ['title' => 'Quản lý Khách hàng', 'icon' => 'bi-people']
];

// Thêm menu quản lý admin nếu là superadmin
if (isset($_SESSION['admin_role_name']) && $_SESSION['admin_role_name'] == 'superadmin') {
    $menu_items['admins_management/index.php'] = ['title' => 'Quản lý Tài khoản Admin', 'icon' => 'bi-person-badge'];
}

// Helper function để kiểm tra menu đang active
function isMenuActive($current_path, $menu_link) {
    // Kiểm tra đúng file
    if (strpos($current_path, $menu_link) !== false) {
        return true;
    }
    
    // Kiểm tra thư mục (ví dụ: products/edit.php cũng được coi là thuộc menu products)
    $menu_folder = dirname($menu_link);
    if ($menu_folder != '.' && strpos($current_path, $menu_folder) !== false) {
        return true;
    }
    
    return false;
}
?>

<!-- Sidebar -->
<div class="sidebar <?php echo $sidebarCollapsed ? 'collapsed' : ''; ?>">
    <!-- Sidebar - Brand -->
    <div class="sidebar-brand d-flex align-items-center justify-content-start">
        <div class="sidebar-brand-icon">
            <i class="bi bi-building-fill"></i>
        </div>
        <div class="sidebar-brand-text mx-2">VLXD Online</div>
    </div>
    
    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    
    <!-- Nav Item - Dashboard -->
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="/hoan/admin/dashboard.php">
                <i class="bi bi-speedometer2"></i>
                <span>Bảng điều khiển</span>
            </a>
        </li>
        
        <!-- Divider -->
        <hr class="sidebar-divider">
        
        <!-- Heading -->
        <div class="sidebar-heading">
            Quản lý sản phẩm
        </div>
        
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' && strpos($_SERVER['PHP_SELF'], '/products/') !== false ? 'active' : ''; ?>" href="/hoan/admin/products/index.php">
                <i class="bi bi-box-seam"></i>
                <span>Sản phẩm</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' && strpos($_SERVER['PHP_SELF'], '/categories_management/') !== false ? 'active' : ''; ?>" href="/hoan/admin/categories_management/index.php">
                <i class="bi bi-tags"></i>
                <span>Danh mục</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'brands.php' ? 'active' : ''; ?>" href="/hoan/admin/brands.php">
                <i class="bi bi-award"></i>
                <span>Thương hiệu</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'inventory.php' ? 'active' : ''; ?>" href="/hoan/admin/inventory.php">
                <i class="bi bi-box2-fill"></i>
                <span>Quản lý kho</span>
            </a>
        </li>
        
        <!-- Divider -->
        <hr class="sidebar-divider">
        
        <!-- Heading -->
        <div class="sidebar-heading">
            Quản lý đơn hàng
        </div>
        
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' || strpos($_SERVER['PHP_SELF'], '/orders/') !== false ? 'active' : ''; ?>" href="/hoan/admin/orders/index.php">
                <i class="bi bi-cart-check"></i>
                <span>Đơn hàng</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : ''; ?>" href="/hoan/admin/reviews.php">
                <i class="bi bi-star-fill"></i>
                <span>Đánh giá</span>
            </a>
        </li>
        
        <!-- Divider -->
        <hr class="sidebar-divider">
        
        <!-- Heading -->
        <div class="sidebar-heading">
            Quản lý người dùng
        </div>
        
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' && strpos($_SERVER['PHP_SELF'], '/users_management/') !== false ? 'active' : ''; ?>" href="/hoan/admin/users_management/index.php">
                <i class="bi bi-people"></i>
                <span>Khách hàng</span>
            </a>
        </li>
        
        <?php if (isset($_SESSION['admin_role_name']) && $_SESSION['admin_role_name'] == 'superadmin'): ?>
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' && strpos($_SERVER['PHP_SELF'], '/admins_management/') !== false ? 'active' : ''; ?>" href="/hoan/admin/admins_management/index.php">
                <i class="bi bi-person-badge"></i>
                <span>Quản lý Admin</span>
            </a>
        </li>
        <?php endif; ?>
        
        <!-- Divider -->
        <hr class="sidebar-divider">
        
        <!-- Heading -->
        <div class="sidebar-heading">
            Hệ thống
        </div>
        
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="/hoan/admin/settings.php">
                <i class="bi bi-gear"></i>
                <span>Cài đặt</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="/hoan/index.php" target="_blank">
                <i class="bi bi-shop"></i>
                <span>Xem cửa hàng</span>
            </a>
        </li>
        
        <li class="nav-item mt-3">
            <a class="nav-link text-danger" href="/hoan/admin/admin_logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Đăng xuất</span>
            </a>
        </li>
    </ul>
</div>

<!-- Nút toggle sidebar -->
<button type="button" class="sidebar-toggle" id="sidebarToggle" title="Thu gọn/Mở rộng">
    <i class="bi <?php echo $sidebarCollapsed ? 'bi-chevron-right' : 'bi-chevron-left'; ?>"></i>
</button>

<style>
    /* Additional styles for sidebar */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: var(--sidebar-width);
        z-index: 999;
        transition: all 0.3s;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        background: linear-gradient(135deg, #3a1c71 0%, #4361ee 100%);
        color: #fff;
        padding-bottom: 20px;
    }
    
    .sidebar.collapsed {
        width: var(--sidebar-collapsed-width);
    }
    
    .sidebar-brand {
        padding: 15px 20px;
        height: 70px;
        display: flex;
        align-items: center;
        background: rgba(0, 0, 0, 0.1);
        margin-bottom: 10px;
    }
    
    .sidebar.collapsed .sidebar-brand-text {
        display: none;
    }
    
    .sidebar.collapsed .sidebar-heading {
        display: none;
    }
    
    .sidebar.collapsed .nav-link span {
        display: none;
    }
    
    .nav-item {
        margin: 4px 8px;
    }
    
    .nav-link {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        border-radius: 8px;
        transition: all 0.2s;
        color: rgba(255, 255, 255, 0.8);
        white-space: nowrap;
        overflow: hidden;
    }
    
    .nav-link:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
    }
    
    .nav-link.active {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        font-weight: 500;
    }
    
    .nav-link i {
        min-width: 24px;
        font-size: 1.1rem;
        margin-right: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .sidebar-divider {
        margin: 15px 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-heading {
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0 15px;
        margin: 10px 0 5px;
    }
    
    /* Fix sidebar toggle positioning */
    #sidebarToggle {
        position: fixed;
        bottom: 20px;
        left: calc(var(--sidebar-width) - 20px);
        z-index: 1000;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: white;
        color: #4361ee;
        border: none;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }
    
    .sidebar.collapsed + #sidebarToggle {
        left: calc(var(--sidebar-collapsed-width) - 20px);
    }
    
    .content-wrapper {
        margin-left: var(--sidebar-width);
        transition: all 0.3s;
    }
    
    .content-wrapper.sidebar-collapsed {
        margin-left: var(--sidebar-collapsed-width);
    }
    
    @media (max-width: 768px) {
        .sidebar {
            left: -280px;
        }
        
        .sidebar.show {
            left: 0;
        }
        
        .content-wrapper {
            margin-left: 0;
        }
        
        #sidebarToggle {
            display: none;
        }
    }
</style> 