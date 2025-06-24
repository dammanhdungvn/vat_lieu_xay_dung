<?php
// Bắt đầu session
session_start();

// Include auth check
require_once '../includes/auth_check.php';

// Include database connection
require_once '../../config/db_connection.php';

// Kiểm tra phương thức request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = "Phương thức không được hỗ trợ!";
    header("Location: index.php");
    exit();
}

// Lấy dữ liệu từ form
$categoryId = isset($_POST['categoryId']) ? intval($_POST['categoryId']) : 0;
$categoryName = $conn->real_escape_string(trim($_POST['categoryName']));
$description = $conn->real_escape_string(trim($_POST['description'] ?? ''));
$displayOrder = isset($_POST['displayOrder']) ? 1 : 0;
$imageType = isset($_POST['image_type']) ? $_POST['image_type'] : 'url';

// Validate dữ liệu
if (empty($categoryId)) {
    $_SESSION['error_message'] = "ID danh mục không hợp lệ!";
    header("Location: index.php");
    exit();
}

if (empty($categoryName)) {
    $_SESSION['error_message'] = "Tên danh mục không được để trống!";
    header("Location: edit_category.php?id=" . $categoryId);
    exit();
}

// Kiểm tra tên danh mục đã tồn tại chưa (trừ chính nó)
$checkQuery = "SELECT CategoryID FROM Categories WHERE CategoryName = ? AND CategoryID != ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("si", $categoryName, $categoryId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error_message'] = "Tên danh mục '{$categoryName}' đã tồn tại!";
    header("Location: edit_category.php?id=" . $categoryId);
    exit();
}

// Lấy thông tin danh mục hiện tại
$getCurrentQuery = "SELECT ImagePath FROM Categories WHERE CategoryID = ?";
$stmt = $conn->prepare($getCurrentQuery);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$currentResult = $stmt->get_result();

if ($currentResult->num_rows === 0) {
    $_SESSION['error_message'] = "Danh mục không tồn tại!";
    header("Location: index.php");
    exit();
}

$currentCategory = $currentResult->fetch_assoc();
$oldImagePath = $currentCategory['ImagePath'];

// Xử lý ảnh dựa trên loại input được chọn
$imagePath = $oldImagePath; // Giữ nguyên ảnh cũ nếu không có thay đổi

if ($imageType === 'url' && !empty($_POST['image_url'])) {
    // Nếu người dùng nhập URL
    $imagePath = $conn->real_escape_string(trim($_POST['image_url']));
} elseif ($imageType === 'upload' && isset($_FILES['imagePath']) && $_FILES['imagePath']['error'] == 0) {
    // Thư mục lưu ảnh
    $targetDir = "../../uploads/categories/";
    
    // Tạo thư mục nếu chưa tồn tại
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Lấy thông tin file
    $fileName = basename($_FILES['imagePath']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Kiểm tra định dạng file
    $allowedExts = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($fileExt, $allowedExts)) {
        $_SESSION['error_message'] = "Chỉ chấp nhận file ảnh định dạng JPG, JPEG, PNG, GIF!";
        header("Location: edit_category.php?id=" . $categoryId);
        exit();
    }
    
    // Kiểm tra kích thước file (max 5MB)
    if ($_FILES['imagePath']['size'] > 5 * 1024 * 1024) {
        $_SESSION['error_message'] = "Kích thước file không được vượt quá 5MB!";
        header("Location: edit_category.php?id=" . $categoryId);
        exit();
    }
    
    // Tạo tên file mới để tránh trùng lặp
    $newFileName = 'category_' . time() . '_' . mt_rand(1000, 9999) . '.' . $fileExt;
    $targetFilePath = $targetDir . $newFileName;
    
    // Upload file
    if (move_uploaded_file($_FILES['imagePath']['tmp_name'], $targetFilePath)) {
        $imagePath = 'uploads/categories/' . $newFileName;
        
        // Xóa ảnh cũ nếu có và không phải là URL
        if (!empty($oldImagePath) && 
            strpos($oldImagePath, 'http') !== 0 && 
            file_exists("../../" . $oldImagePath)) {
            unlink("../../" . $oldImagePath);
        }
    } else {
        $_SESSION['error_message'] = "Có lỗi xảy ra khi upload file!";
        header("Location: edit_category.php?id=" . $categoryId);
        exit();
    }
}

// Cập nhật danh mục
$query = "UPDATE Categories SET 
          CategoryName = ?,
          Description = ?,
          ImagePath = ?
          WHERE CategoryID = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("sssi", $categoryName, $description, $imagePath, $categoryId);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Đã cập nhật danh mục '{$categoryName}' thành công!";
    header("Location: index.php");
    exit();
} else {
    $_SESSION['error_message'] = "Lỗi khi cập nhật danh mục: " . $conn->error;
    header("Location: edit_category.php?id=" . $categoryId);
    exit();
}

$stmt->close();
$conn->close();
?> 