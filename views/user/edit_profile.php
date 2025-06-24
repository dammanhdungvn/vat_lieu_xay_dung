<?php
session_start();
require_once '../../config/db_connection.php';
require_once '../../includes/header.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Lấy thông tin người dùng
$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header('Location: ../auth/login.php');
    exit();
}

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Kiểm tra email trùng lặp
    if ($email !== $user['Email']) {
        $check_email = $conn->prepare("SELECT UserID FROM Users WHERE Email = ? AND UserID != ?");
        $check_email->bind_param("si", $email, $user_id);
        $check_email->execute();
        if ($check_email->get_result()->num_rows > 0) {
            $error_message = "Email này đã được sử dụng bởi tài khoản khác.";
        }
    }

    // Kiểm tra số điện thoại trùng lặp
    if ($phone !== $user['PhoneNumber']) {
        $check_phone = $conn->prepare("SELECT UserID FROM Users WHERE PhoneNumber = ? AND UserID != ?");
        $check_phone->bind_param("si", $phone, $user_id);
        $check_phone->execute();
        if ($check_phone->get_result()->num_rows > 0) {
            $error_message = "Số điện thoại này đã được sử dụng bởi tài khoản khác.";
        }
    }

    if (empty($error_message)) {
        // Nếu có thay đổi mật khẩu
        if (!empty($current_password)) {
            if (password_verify($current_password, $user['PasswordHash'])) {
                if (empty($new_password)) {
                    $error_message = "Vui lòng nhập mật khẩu mới.";
                } elseif ($new_password !== $confirm_password) {
                    $error_message = "Mật khẩu mới không khớp.";
                } elseif (strlen($new_password) < 6) {
                    $error_message = "Mật khẩu mới phải có ít nhất 6 ký tự.";
                } else {
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE Users SET FullName = ?, Email = ?, PhoneNumber = ?, Address = ?, PasswordHash = ? WHERE UserID = ?");
                    $stmt->bind_param("sssssi", $full_name, $email, $phone, $address, $password_hash, $user_id);
                }
            } else {
                $error_message = "Mật khẩu hiện tại không đúng.";
            }
        } else {
            // Cập nhật thông tin cơ bản
            $stmt = $conn->prepare("UPDATE Users SET FullName = ?, Email = ?, PhoneNumber = ?, Address = ? WHERE UserID = ?");
            $stmt->bind_param("ssssi", $full_name, $email, $phone, $address, $user_id);
        }

        if (empty($error_message)) {
            if ($stmt->execute()) {
                $success_message = "Cập nhật thông tin thành công!";
                // Cập nhật lại thông tin người dùng
                $stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
            } else {
                $error_message = "Có lỗi xảy ra khi cập nhật thông tin.";
            }
        }
    }
}
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Chỉnh sửa thông tin cá nhân</h4>
                </div>
                <div class="card-body">
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i><?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle me-2"></i><?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?php echo htmlspecialchars($user['FullName']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['Email']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['PhoneNumber'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['Address'] ?? ''); ?></textarea>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3">Thay đổi mật khẩu</h5>
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

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Cập nhật thông tin
                            </button>
                            <a href="../user/profile.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}
.card-header {
    border-radius: 10px 10px 0 0 !important;
}
.form-control:focus {
    box-shadow: none;
    border-color: #0d6efd;
}
.btn {
    padding: 0.5rem 1rem;
}
</style>

<?php
require_once '../../includes/footer.php';
$conn->close();
?> 