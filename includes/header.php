<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db_connection.php';
require_once __DIR__ . '/../config/constants.php';

// Sử dụng URL tuyệt đối từ hằng số SITE_URL
$baseUrl = SITE_URL;

// Xác định đường dẫn tuyệt đối từ thư mục gốc của trang web
$documentRoot = $_SERVER['DOCUMENT_ROOT'];
$currentPath = $_SERVER['SCRIPT_FILENAME'];

// Tính toán đường dẫn tương đối từ gốc của trang web
$relativeToRoot = str_replace($documentRoot, '', $currentPath);
$folderDepth = substr_count(dirname($relativeToRoot), '/');

// Xác định basePath dựa trên độ sâu của thư mục
if ($folderDepth == 1) { // Trang chủ (index.php)
    $basePath = './';
} elseif ($folderDepth == 2) { // Trang cấp 1 (views/about.php)
    $basePath = SITE_URL . '/';
} else { // Trang cấp 2 trở lên (views/product/list.php)
    $basePath = SITE_URL . '/';
}

// Debug hiển thị các đường dẫn (có thể xóa sau)
// echo '<!-- Current: ' . $currentPath . ' | Root: ' . $documentRoot . ' | Depth: ' . $folderDepth . ' | Base: ' . $basePath . ' -->';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS (if any) -->
    <link href="<?= $baseUrl ?>/assets/css/style.css" rel="stylesheet">
    <title><?php echo $page_title ?? 'VLXD Online'; ?></title>
</head>
<body class="d-flex flex-column min-vh-100">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= $baseUrl ?>/index.php">VLXD Online</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/views/product/list.php">Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/views/about.php">Giới thiệu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/views/contact.php">Liên hệ</a>
                    </li>
                </ul>
                
                <form class="d-flex me-2" action="<?= $baseUrl ?>/views/product/list.php" method="GET">
                    <input class="form-control me-2" type="search" name="search_term" placeholder="Tìm sản phẩm..." aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Tìm</button>
                </form>
                
                <div class="d-flex align-items-center">
                    <a href="<?= $baseUrl ?>/views/cart/view.php" class="me-3 position-relative">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                        <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo count($_SESSION['cart']); ?>
                        </span>
                        <?php endif; ?>
                    </a>

                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="dropdown">
                            <button class="btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Xin chào, <?php echo htmlspecialchars($_SESSION['user_fullname']); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/views/user/my_account.php">Tài khoản của tôi</a></li>
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/views/user/my_account.php?page=order_history">Đơn hàng của tôi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= $baseUrl ?>/controllers/auth/logout.php">Đăng xuất</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="<?= $baseUrl ?>/views/auth/login.php" class="btn btn-outline-primary me-2">Đăng nhập</a>
                        <a href="<?= $baseUrl ?>/views/auth/register.php" class="btn btn-primary">Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container mt-4">
        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php 
                echo htmlspecialchars($_SESSION['success_message']);
                unset($_SESSION['success_message']); 
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                echo htmlspecialchars($_SESSION['error_message']);
                unset($_SESSION['error_message']); 
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    </div> 