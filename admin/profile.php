<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Hồ sơ cá nhân';

// Include auth check
require_once 'includes/auth_check.php';

// Include database connection
require_once '../config/db_connection.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Vui lòng đăng nhập để truy cập trang này.";
    header("Location: login.php");
    exit();
}

// Lấy thông tin người dùng hiện tại
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM Users WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Kiểm tra nếu không tìm thấy thông tin người dùng
if (!$user) {
    $_SESSION['error_message'] = "Không tìm thấy thông tin người dùng.";
    header("Location: login.php");
    exit();
}

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $success = true;
    $error = '';

    // Kiểm tra mật khẩu hiện tại nếu có thay đổi mật khẩu
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $success = false;
            $error = "Vui lòng nhập mật khẩu hiện tại";
        } elseif (!password_verify($current_password, $user['Password'])) {
            $success = false;
            $error = "Mật khẩu hiện tại không đúng";
        } elseif ($new_password !== $confirm_password) {
            $success = false;
            $error = "Mật khẩu mới không khớp";
        }
    }

    if ($success) {
        // Cập nhật thông tin cơ bản
        $query = "UPDATE Users SET FullName = ?, Email = ?, Phone = ? WHERE UserID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $fullname, $email, $phone, $user_id);
        
        if (!$stmt->execute()) {
            $success = false;
            $error = "Lỗi khi cập nhật thông tin: " . $conn->error;
        }

        // Cập nhật mật khẩu nếu có
        if ($success && !empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "UPDATE Users SET Password = ? WHERE UserID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $hashed_password, $user_id);
            
            if (!$stmt->execute()) {
                $success = false;
                $error = "Lỗi khi cập nhật mật khẩu: " . $conn->error;
            }
        }

        if ($success) {
            $_SESSION['success_message'] = "Đã cập nhật thông tin thành công!";
            header("Location: profile.php");
            exit();
        }
    }

    if (!$success) {
        $_SESSION['error_message'] = $error;
    }
}

// Include header và sidebar
include_once 'includes/header_admin.php';
include_once 'includes/sidebar_admin.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="text-primary m-0 fw-semibold">Hồ sơ cá nhân</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php 
                            echo $_SESSION['success_message']; 
                            unset($_SESSION['success_message']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php 
                            echo $_SESSION['error_message']; 
                            unset($_SESSION['error_message']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập</label>
                                    <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['Username'] ?? ''); ?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label for="fullname" class="form-label">Họ và tên</label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" 
                                           value="<?php echo htmlspecialchars($user['FullName'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($user['Email'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($user['Phone'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password">
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Mật khẩu mới</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password">
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Cập nhật thông tin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer_admin.php';
$conn->close();
?> 