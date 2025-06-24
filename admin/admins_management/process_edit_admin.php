<?php
// Bắt đầu session
session_start();

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

// Kiểm tra phương thức request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['admin_error_message'] = 'Phương thức không hợp lệ!';
    header('Location: index.php');
    exit;
}

// Lấy dữ liệu form
$adminId = isset($_POST['admin_id']) ? intval($_POST['admin_id']) : 0;
$fullName = isset($_POST['fullname']) ? trim($_POST['fullname']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$roleId = isset($_POST['role_id']) ? intval($_POST['role_id']) : 0;
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$isActive = isset($_POST['is_active']) ? 1 : 0;

// Validate dữ liệu
if ($adminId <= 0) {
    $_SESSION['admin_error_message'] = 'ID admin không hợp lệ!';
    header('Location: index.php');
    exit;
}

if ($roleId <= 0) {
    $_SESSION['admin_error_message'] = 'Vai trò không hợp lệ!';
    header('Location: edit_admin.php?id=' . $adminId);
    exit;
}

// Xác minh admin tồn tại
$checkQuery = "SELECT AdminID, Username FROM Admins WHERE AdminID = $adminId";
$checkResult = $conn->query($checkQuery);

if (!$checkResult || $checkResult->num_rows === 0) {
    $_SESSION['admin_error_message'] = "Không tìm thấy admin với ID: $adminId";
    header('Location: index.php');
    exit;
}

$adminData = $checkResult->fetch_assoc();

// Validate email nếu có
if (!empty($email)) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['admin_error_message'] = 'Địa chỉ email không hợp lệ!';
        header('Location: edit_admin.php?id=' . $adminId);
        exit;
    }
    
    // Kiểm tra email đã tồn tại hay chưa (trừ admin hiện tại)
    $emailCheckQuery = "SELECT AdminID FROM Admins WHERE Email = '$email' AND AdminID != $adminId";
    $emailCheckResult = $conn->query($emailCheckQuery);
    
    if ($emailCheckResult && $emailCheckResult->num_rows > 0) {
        $_SESSION['admin_error_message'] = 'Email này đã được sử dụng bởi người dùng khác!';
        header('Location: edit_admin.php?id=' . $adminId);
        exit;
    }
}

// Cấu trúc câu lệnh UPDATE
$updateQuery = "UPDATE Admins SET 
                FullName = '" . $conn->real_escape_string($fullName) . "',
                Email = '" . $conn->real_escape_string($email) . "',
                RoleID = $roleId,
                IsActive = $isActive";

// Nếu có mật khẩu mới thì cập nhật
if (!empty($password)) {
    // Băm mật khẩu với password_hash
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $updateQuery .= ", Password = '" . $conn->real_escape_string($hashedPassword) . "'";
}

// Hoàn thành câu lệnh UPDATE
$updateQuery .= " WHERE AdminID = $adminId";

// Thực thi câu lệnh UPDATE
$updateResult = $conn->query($updateQuery);

if ($updateResult) {
    $_SESSION['admin_success_message'] = "Đã cập nhật thành công thông tin Admin: " . $adminData['Username'];
    
    // Nếu admin đang cập nhật chính tài khoản của mình, cập nhật thông tin phiên đăng nhập
    if (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $adminId) {
        // Cập nhật thông tin phiên đăng nhập nếu cần
        if (!empty($fullName)) {
            $_SESSION['admin_fullname'] = $fullName;
        }
    }
} else {
    $_SESSION['admin_error_message'] = "Lỗi khi cập nhật Admin: " . $conn->error;
}

// Đóng kết nối và chuyển hướng
$conn->close();
header('Location: index.php');
exit; 