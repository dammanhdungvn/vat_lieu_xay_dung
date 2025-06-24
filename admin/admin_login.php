<?php
session_start();

// Kiểm tra xem admin đã đăng nhập chưa
if (isset($_SESSION['admin_id'])) {
    // Nếu đã đăng nhập rồi, chuyển hướng đến trang dashboard
    header('Location: dashboard.php');
    exit();
}

$page_title = 'Đăng nhập Quản trị';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #343a40;
            color: white;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card login-card">
                    <div class="card-header text-center py-3">
                        <i class="bi bi-shield-lock me-2"></i>Đăng nhập Trang Quản Trị
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($_SESSION['admin_error_login'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php 
                                echo $_SESSION['admin_error_login']; 
                                unset($_SESSION['admin_error_login']);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['redirect_message_admin'])): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <?php 
                                echo $_SESSION['redirect_message_admin']; 
                                unset($_SESSION['redirect_message_admin']);
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="process_admin_login.php">
                            <div class="mb-3">
                                <label for="username" class="form-label">Tên đăng nhập</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" id="username" name="Username" class="form-control" placeholder="Nhập tên đăng nhập" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="../index.php" class="text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i>Quay lại trang chính
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 