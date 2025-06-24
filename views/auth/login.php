<?php
session_start();
$page_title = 'Đăng nhập - VLXD Online';
include_once '../../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Đăng nhập Tài khoản</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($_SESSION['error_login'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php 
                                echo htmlspecialchars($_SESSION['error_login']);
                                unset($_SESSION['error_login']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['success_login'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php 
                                echo htmlspecialchars($_SESSION['success_login']);
                                unset($_SESSION['success_login']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="../../controllers/auth/login.php">
                        <div class="mb-3">
                            <label for="Email" class="form-label">Email</label>
                            <input type="email" name="Email" id="Email" class="form-control" required placeholder="vidu@email.com" 
                                value="<?php echo htmlspecialchars($_SESSION['form_data_login']['Email'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Clear form data after displaying
if (isset($_SESSION['form_data_login'])) {
    unset($_SESSION['form_data_login']);
}

include_once '../../includes/footer.php'; 
?> 