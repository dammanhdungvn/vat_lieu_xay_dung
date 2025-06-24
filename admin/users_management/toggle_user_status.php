<?php
// Bắt đầu session
session_start();

// Include auth check
require_once '../includes/auth_check.php';

// Include database connection
require_once '../../config/db_connection.php';

// Kiểm tra nếu tham số id và current_status tồn tại
if (!isset($_GET['id']) || !isset($_GET['current_status'])) {
    $_SESSION['admin_error_message'] = 'Tham số không hợp lệ!';
    header("Location: index.php");
    exit;
}

// Lấy UserID và trạng thái hiện tại
$userID = intval($_GET['id']);
$currentStatus = intval($_GET['current_status']);

// Validate dữ liệu
if ($userID <= 0) {
    $_SESSION['admin_error_message'] = 'ID người dùng không hợp lệ!';
    header("Location: index.php");
    exit;
}

// Xác minh người dùng tồn tại
$checkQuery = "SELECT UserID, FullName FROM Users WHERE UserID = $userID";
$checkResult = $conn->query($checkQuery);

if (!$checkResult || $checkResult->num_rows === 0) {
    $_SESSION['admin_error_message'] = "Không tìm thấy người dùng với ID: $userID";
    header("Location: index.php");
    exit;
}

$userData = $checkResult->fetch_assoc();
$userName = $userData['FullName'];

// Trạng thái mới là ngược lại của trạng thái hiện tại
$newStatus = $currentStatus == 1 ? 0 : 1;

// Cập nhật trạng thái người dùng
$updateQuery = "UPDATE Users SET IsActive = $newStatus WHERE UserID = $userID";
$updateResult = $conn->query($updateQuery);

if ($updateResult) {
    if ($newStatus == 1) {
        $_SESSION['admin_success_message'] = "Đã kích hoạt tài khoản người dùng: $userName";
    } else {
        $_SESSION['admin_success_message'] = "Đã khóa tài khoản người dùng: $userName";
    }
} else {
    $_SESSION['admin_error_message'] = "Lỗi khi cập nhật trạng thái: " . $conn->error;
}

// Đóng kết nối và chuyển hướng về trang danh sách
$conn->close();
header("Location: index.php");
exit; 