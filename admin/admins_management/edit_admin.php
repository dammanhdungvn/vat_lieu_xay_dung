<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Chỉnh sửa tài khoản Admin';

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

// Kiểm tra ID admin từ URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['admin_error_message'] = 'ID admin không hợp lệ!';
    header("Location: index.php");
    exit;
}

$adminId = intval($_GET['id']);

// Truy vấn thông tin admin
$adminQuery = "SELECT * FROM Admins WHERE AdminID = $adminId";
$adminResult = $conn->query($adminQuery);

// Kiểm tra admin tồn tại
if (!$adminResult || $adminResult->num_rows === 0) {
    $_SESSION['admin_error_message'] = "Không tìm thấy admin với ID: $adminId";
    header("Location: index.php");
    exit;
}

$adminData = $adminResult->fetch_assoc();

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
            <h5 class="mb-0"><i class="bi bi-person-gear me-2"></i>Chỉnh sửa tài khoản Admin</h5>
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
            
            <form action="process_edit_admin.php" method="post" class="needs-validation" novalidate>
                <input type="hidden" name="admin_id" value="<?php echo $adminId; ?>">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="username" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($adminData['Username']); ?>" readonly>
                        <div class="form-text">Tên đăng nhập không thể thay đổi.</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="password" class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <div class="form-text">Để trống nếu không muốn thay đổi mật khẩu.</div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fullname" class="form-label">Tên đầy đủ</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($adminData['FullName'] ?? ''); ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($adminData['Email'] ?? ''); ?>">
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
                                    $selected = ($role['RoleID'] == $adminData['RoleID']) ? 'selected' : '';
                                    echo '<option value="' . $role['RoleID'] . '" ' . $selected . '>' . htmlspecialchars($role['RoleName']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Vui lòng chọn vai trò.</div>
                    </div>
                    
                    <?php 
                    // Xác định xem admin hiện tại có phải là admin đang đăng nhập hay không
                    $isCurrentUser = (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $adminId);
                    ?>
                    
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                   <?php echo ($adminData['IsActive'] == 1) ? 'checked' : ''; ?>
                                   <?php echo ($isCurrentUser) ? 'disabled' : ''; ?>>
                            <label class="form-check-label" for="is_active">
                                Kích hoạt tài khoản
                            </label>
                            <?php if ($isCurrentUser): ?>
                                <input type="hidden" name="is_active" value="1">
                                <div class="form-text text-muted">Không thể vô hiệu hóa tài khoản của chính mình.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Cập nhật Admin
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