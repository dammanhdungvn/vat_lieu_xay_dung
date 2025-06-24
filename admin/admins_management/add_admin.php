<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Thêm tài khoản Admin mới';

// Include auth check
require_once '../includes/auth_check.php';

// Kiểm tra xem người dùng có phải là SuperAdmin hay không
if (!isset($_SESSION['admin_role_name']) || $_SESSION['admin_role_name'] !== 'superadmin') {
    $_SESSION['admin_error_message'] = 'Bạn không có quyền truy cập trang này!';
    header('Location: ../dashboard.php');
    exit;
}

// Include database connection
require_once '../../config/db_connection.php';

// Lấy danh sách vai trò
$roles_query = "SELECT * FROM Roles ORDER BY RoleName";
$roles_result = $conn->query($roles_query);

// Include header và sidebar
include_once '../includes/header_admin.php';
include_once '../includes/sidebar_admin.php';
?>

<div class="container-fluid mt-4">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Thêm tài khoản Admin mới</h5>
            <a href="index.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['admin_error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['admin_error_message']; 
                    unset($_SESSION['admin_error_message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form action="process_add_admin.php" method="post" class="needs-validation" novalidate>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div class="invalid-feedback">Vui lòng nhập tên đăng nhập.</div>
                        <div class="form-text">Tên đăng nhập phải là duy nhất và không chứa khoảng trắng.</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback">Vui lòng nhập mật khẩu.</div>
                        <div class="form-text">Mật khẩu nên có ít nhất 8 ký tự và bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.</div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fullname" class="form-label">Tên đầy đủ</label>
                        <input type="text" class="form-control" id="fullname" name="fullname">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                        <div class="invalid-feedback">Vui lòng nhập một địa chỉ email hợp lệ.</div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="role" class="form-label">Vai trò <span class="text-danger">*</span></label>
                        <select class="form-select" id="role" name="role_id" required>
                            <option value="">-- Chọn vai trò --</option>
                            <?php 
                            if ($roles_result && $roles_result->num_rows > 0) {
                                while ($role = $roles_result->fetch_assoc()) {
                                    echo '<option value="' . $role['RoleID'] . '">' . htmlspecialchars($role['RoleName']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Vui lòng chọn vai trò.</div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">
                                Kích hoạt tài khoản
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Lưu Admin
                    </button>
                    <a href="index.php" class="btn btn-secondary ms-2">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Form validation
    (function() {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>

<?php
// Include footer
include_once '../includes/footer_admin.php';
$conn->close();
?> 