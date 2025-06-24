<?php
// Bắt đầu session
session_start();

// Include auth check
require_once '../includes/auth_check.php';

// Include database connection
require_once '../../config/db_connection.php';

// Kiểm tra ID sản phẩm
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['admin_error_message'] = "ID sản phẩm không hợp lệ!";
    header("Location: /hoan/admin/dashboard.php");
    exit;
}

$product_id = (int)$_GET['id'];

// Kiểm tra chi tiết về đơn hàng liên quan
$check_query = "SELECT oi.ProductID, oi.OrderID, o.Status 
                FROM OrderItems oi 
                JOIN Orders o ON oi.OrderID = o.OrderID 
                WHERE oi.ProductID = $product_id";
$check_result = $conn->query($check_query);

if ($check_result->num_rows > 0) {
    // Nếu sản phẩm đã được sử dụng trong đơn hàng
    $order_details = [];
    while ($row = $check_result->fetch_assoc()) {
        $order_details[] = "Đơn hàng #" . $row['OrderID'] . " (Trạng thái: " . $row['Status'] . ")";
    }
    
    // Cập nhật số lượng về 0
    $update_query = "UPDATE Products SET StockQuantity = 0 WHERE ProductID = $product_id";
    if ($conn->query($update_query) === TRUE) {
        $_SESSION['admin_success_message'] = "Sản phẩm đã được đánh dấu là hết hàng vì đang được sử dụng trong các đơn hàng: " . implode(", ", $order_details);
    } else {
        $_SESSION['admin_error_message'] = "Lỗi khi cập nhật trạng thái sản phẩm: " . $conn->error;
    }
} else {
    // Nếu sản phẩm chưa được sử dụng trong đơn hàng, có thể xóa hoàn toàn
    // Lấy đường dẫn ảnh trước khi xóa
    $image_query = "SELECT ImagePath FROM Products WHERE ProductID = $product_id";
    $image_result = $conn->query($image_query);
    $product = $image_result->fetch_assoc();
    
    // Xóa sản phẩm
    $delete_query = "DELETE FROM Products WHERE ProductID = $product_id";
    if ($conn->query($delete_query) === TRUE) {
        // Xóa file ảnh nếu tồn tại và không phải là URL
        if (!empty($product['ImagePath']) && 
            strpos($product['ImagePath'], 'http') !== 0 && 
            file_exists('../../' . $product['ImagePath'])) {
            unlink('../../' . $product['ImagePath']);
        }
        $_SESSION['admin_success_message'] = "Sản phẩm đã được xóa thành công!";
    } else {
        $_SESSION['admin_error_message'] = "Lỗi khi xóa sản phẩm: " . $conn->error;
    }
}

// Chuyển hướng về trang danh sách sản phẩm
header("Location: /hoan/admin/dashboard.php");
exit;
?> 