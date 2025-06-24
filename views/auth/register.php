<?php
session_start();
$page_title = 'Đăng ký Tài khoản - VLXD Online';
include_once '../../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Đăng ký Tài khoản</h4>
                </div>
                <div class="card-body">
                    <?php if(isset($_SESSION['error_register'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php 
                                echo htmlspecialchars($_SESSION['error_register']);
                                unset($_SESSION['error_register']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['success_register'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php 
                                echo htmlspecialchars($_SESSION['success_register']);
                                unset($_SESSION['success_register']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="../../controllers/auth/register.php">
                        <div class="mb-3">
                            <label for="FullName" class="form-label">Họ và tên</label>
                            <input type="text" name="FullName" id="FullName" class="form-control" required 
                                value="<?php echo htmlspecialchars($_SESSION['form_data_register']['FullName'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="Email" class="form-label">Email</label>
                            <input type="email" name="Email" id="Email" class="form-control" required placeholder="vidu@email.com" 
                                value="<?php echo htmlspecialchars($_SESSION['form_data_register']['Email'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="PhoneNumber" class="form-label">Số điện thoại</label>
                            <input type="tel" name="PhoneNumber" id="PhoneNumber" class="form-control" 
                                value="<?php echo htmlspecialchars($_SESSION['form_data_register']['PhoneNumber'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Nhập lại mật khẩu</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Clear form data after displaying
if (isset($_SESSION['form_data_register'])) {
    unset($_SESSION['form_data_register']);
}

include_once '../../includes/footer.php'; 
?> 