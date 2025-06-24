<?php
// Bắt đầu session
session_start();

// Include auth check
require_once '../includes/auth_check.php';

// Include database connection
require_once '../../config/db_connection.php';

// Kiểm tra nếu là phương thức POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['admin_error_message'] = 'Phương thức không hợp lệ!';
    header("Location: index.php");
    exit;
}

// Lấy dữ liệu từ form
$orderId = isset($_POST['orderid']) ? intval($_POST['orderid']) : 0;
$newStatus = isset($_POST['status']) ? $conn->real_escape_string(trim($_POST['status'])) : '';

// Validate dữ liệu
if ($orderId <= 0) {
    $_SESSION['admin_error_message'] = 'ID đơn hàng không hợp lệ!';
    header("Location: index.php");
    exit;
}

if (empty($newStatus)) {
    $_SESSION['admin_error_message'] = 'Trạng thái không được để trống!';
    header("Location: view_order.php?id=$orderId");
    exit;
}

// Xác minh đơn hàng tồn tại
$checkQuery = "SELECT OrderID FROM Orders WHERE OrderID = $orderId";
$checkResult = $conn->query($checkQuery);

if (!$checkResult || $checkResult->num_rows === 0) {
    $_SESSION['admin_error_message'] = "Không tìm thấy đơn hàng với ID: $orderId";
    header("Location: index.php");
    exit;
}

// Validate trạng thái hợp lệ
$validStatuses = ['Mới', 'Đang xử lý', 'Đang giao', 'Hoàn thành', 'Đã hủy'];
if (!in_array($newStatus, $validStatuses)) {
    $_SESSION['admin_error_message'] = 'Trạng thái không hợp lệ!';
    header("Location: view_order.php?id=$orderId");
    exit;
}

// Cập nhật trạng thái đơn hàng
$updateQuery = "UPDATE Orders SET Status = '$newStatus' WHERE OrderID = $orderId";
$updateResult = $conn->query($updateQuery);

if ($updateResult) {
    $_SESSION['admin_success_message'] = "Đã cập nhật trạng thái đơn hàng thành '$newStatus'";
} else {
    $_SESSION['admin_error_message'] = "Lỗi khi cập nhật trạng thái: " . $conn->error;
}

// Đóng kết nối và chuyển hướng lại trang chi tiết
$conn->close();
header("Location: view_order.php?id=$orderId");
exit; 