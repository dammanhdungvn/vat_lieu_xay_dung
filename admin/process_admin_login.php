<?php
// Bắt đầu phiên làm việc
session_start();

// Include kết nối database
require_once '../config/db_connection.php';

// Kiểm tra method của request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Nếu không phải POST, chuyển hướng về trang đăng nhập
    header('Location: admin_login.php');
    exit();
}

// Lấy username và password từ POST data
$username = $_POST['Username'] ?? '';
$password = $_POST['password'] ?? '';

// Validate input không được trống
if (empty($username) || empty($password)) {
    $_SESSION['admin_error_login'] = 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.';
    header('Location: admin_login.php');
    exit();
}

// Chuẩn bị câu truy vấn SQL sử dụng prepared statement
$query = "SELECT a.AdminID, a.Username, a.FullName, a.PasswordHash, a.IsActive, r.RoleName 
          FROM Admins a 
          JOIN Roles r ON a.RoleID = r.RoleID 
          WHERE a.Username = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

// Nếu không tìm thấy tài khoản admin
if ($result->num_rows === 0) {
    $_SESSION['admin_error_login'] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
    header('Location: admin_login.php');
    exit();
}

// Lấy thông tin admin
$admin_row = $result->fetch_assoc();

// Kiểm tra tài khoản có bị khóa không
if (!$admin_row['IsActive']) {
    $_SESSION['admin_error_login'] = 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.';
    header('Location: admin_login.php');
    exit();
}

// Xác thực mật khẩu
if (password_verify($password, $admin_row['PasswordHash'])) {
    // Mật khẩu đúng, lưu thông tin vào session
    $_SESSION['admin_id'] = $admin_row['AdminID'];
    $_SESSION['admin_username'] = $admin_row['Username'];
    $_SESSION['admin_fullname'] = $admin_row['FullName'];
    $_SESSION['admin_role_name'] = $admin_row['RoleName'];
    
    // Xóa các thông báo lỗi nếu có
    if (isset($_SESSION['admin_error_login'])) {
        unset($_SESSION['admin_error_login']);
    }
    
    if (isset($_SESSION['redirect_message_admin'])) {
        unset($_SESSION['redirect_message_admin']);
    }
    
    // Chuyển hướng đến trang dashboard
    header('Location: dashboard.php');
    exit();
} else {
    // Mật khẩu sai
    $_SESSION['admin_error_login'] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
    header('Location: admin_login.php');
    exit();
}

// Đóng statement và kết nối
$stmt->close();
$conn->close(); 