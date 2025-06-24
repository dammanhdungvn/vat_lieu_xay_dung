<?php
// Bắt đầu session
session_start();

// Include auth check
require_once '../includes/auth_check.php';

// Include database connection
require_once '../../config/db_connection.php';

// Kiểm tra có ID được truyền vào không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "ID danh mục không hợp lệ!";
    header("Location: index.php");
    exit();
}

$categoryId = intval($_GET['id']);

// Kiểm tra xem danh mục có tồn tại không
$checkCategoryQuery = "SELECT * FROM Categories WHERE CategoryID = ?";
$stmt = $conn->prepare($checkCategoryQuery);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$categoryResult = $stmt->get_result();

if ($categoryResult->num_rows === 0) {
    $_SESSION['error_message'] = "Danh mục không tồn tại!";
    header("Location: index.php");
    exit();
}

$category = $categoryResult->fetch_assoc();

// Kiểm tra xem danh mục có sản phẩm không
$checkProductsQuery = "SELECT COUNT(*) as total FROM Products WHERE CategoryID = ?";
$stmt = $conn->prepare($checkProductsQuery);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$productsResult = $stmt->get_result();
$productsCount = $productsResult->fetch_assoc()['total'];

if ($productsCount > 0) {
    // Có hai lựa chọn:
    // 1. Không cho phép xóa danh mục có sản phẩm
    // $_SESSION['error_message'] = "Không thể xóa danh mục '{$category['CategoryName']}' vì có {$productsCount} sản phẩm liên kết!";
    // header("Location: index.php");
    // exit();
    
    // 2. Hoặc cập nhật CategoryID của các sản phẩm thành NULL
    $updateProductsQuery = "UPDATE Products SET CategoryID = NULL WHERE CategoryID = ?";
    $stmt = $conn->prepare($updateProductsQuery);
    $stmt->bind_param("i", $categoryId);
    
    if (!$stmt->execute()) {
        $_SESSION['error_message'] = "Lỗi khi cập nhật sản phẩm: " . $conn->error;
        header("Location: index.php");
        exit();
    }
}

// Lấy đường dẫn ảnh danh mục (nếu có) để xóa
$imagePath = $category['ImagePath'];

// Xóa danh mục
$deleteCategoryQuery = "DELETE FROM Categories WHERE CategoryID = ?";
$stmt = $conn->prepare($deleteCategoryQuery);
$stmt->bind_param("i", $categoryId);

if ($stmt->execute()) {
    // Xóa file ảnh nếu có
    if (!empty($imagePath) && file_exists("../../" . $imagePath)) {
        unlink("../../" . $imagePath);
    }
    
    $_SESSION['success_message'] = "Đã xóa danh mục '{$category['CategoryName']}' thành công!";
    
    // Thêm thông báo nếu có sản phẩm bị ảnh hưởng
    if ($productsCount > 0) {
        $_SESSION['success_message'] .= " {$productsCount} sản phẩm đã được bỏ danh mục.";
    }
} else {
    $_SESSION['error_message'] = "Lỗi khi xóa danh mục: " . $conn->error;
}

// Chuyển hướng về trang danh sách
header("Location: index.php");
exit();

$stmt->close();
$conn->close();
?> 