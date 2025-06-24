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
    header("Location: add_category.php");
    exit();
}

// Lấy dữ liệu từ form
$categoryName = $conn->real_escape_string(trim($_POST['categoryName']));
$description = $conn->real_escape_string(trim($_POST['description'] ?? ''));
$imageType = isset($_POST['image_type']) ? $_POST['image_type'] : 'url';

// Validate dữ liệu
if (empty($categoryName)) {
    $_SESSION['error_message'] = "Tên danh mục không được để trống!";
    header("Location: add_category.php");
    exit();
}

// Kiểm tra tên danh mục đã tồn tại chưa
$checkQuery = "SELECT CategoryID FROM Categories WHERE CategoryName = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("s", $categoryName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error_message'] = "Tên danh mục '{$categoryName}' đã tồn tại!";
    header("Location: add_category.php");
    exit();
}

// Xử lý ảnh dựa trên loại input được chọn
$imagePath = '';

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
        header("Location: add_category.php");
        exit();
    }
    
    // Kiểm tra kích thước file (max 5MB)
    if ($_FILES['imagePath']['size'] > 5 * 1024 * 1024) {
        $_SESSION['error_message'] = "Kích thước file không được vượt quá 5MB!";
        header("Location: add_category.php");
        exit();
    }
    
    // Tạo tên file mới để tránh trùng lặp
    $newFileName = 'category_' . time() . '_' . mt_rand(1000, 9999) . '.' . $fileExt;
    $targetFilePath = $targetDir . $newFileName;
    
    // Upload file
    if (move_uploaded_file($_FILES['imagePath']['tmp_name'], $targetFilePath)) {
        $imagePath = 'uploads/categories/' . $newFileName;
    } else {
        $_SESSION['error_message'] = "Có lỗi xảy ra khi upload file!";
        header("Location: add_category.php");
        exit();
    }
}

// Thêm danh mục vào database
$insertQuery = "INSERT INTO Categories (CategoryName, Description, ImagePath) VALUES (?, ?, ?)";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("sss", $categoryName, $description, $imagePath);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Đã thêm danh mục '{$categoryName}' thành công!";
    header("Location: index.php");
    exit();
} else {
    $_SESSION['error_message'] = "Lỗi khi thêm danh mục: " . $conn->error;
    header("Location: add_category.php");
    exit();
}

$stmt->close();
$conn->close();
?> 