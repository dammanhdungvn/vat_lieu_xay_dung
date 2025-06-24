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
    header("Location: add.php");
    exit();
}

// Lấy dữ liệu từ form
$brandName = $conn->real_escape_string(trim($_POST['brandName']));
$description = $conn->real_escape_string(trim($_POST['description'] ?? ''));
$imageType = isset($_POST['image_type']) ? $_POST['image_type'] : 'url';

// Validate dữ liệu
if (empty($brandName)) {
    $_SESSION['error_message'] = "Tên thương hiệu không được để trống!";
    header("Location: add.php");
    exit();
}

// Kiểm tra tên thương hiệu đã tồn tại chưa
$checkQuery = "SELECT BrandID FROM Brands WHERE BrandName = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("s", $brandName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error_message'] = "Tên thương hiệu '{$brandName}' đã tồn tại!";
    header("Location: add.php");
    exit();
}

// Xử lý ảnh dựa trên loại input được chọn
$logoPath = '';

if ($imageType === 'url' && !empty($_POST['image_url'])) {
    // Nếu người dùng nhập URL
    $logoPath = $conn->real_escape_string(trim($_POST['image_url']));
} elseif ($imageType === 'upload' && isset($_FILES['logoPath']) && $_FILES['logoPath']['error'] == 0) {
    // Thư mục lưu ảnh
    $targetDir = "../../uploads/brands/";
    
    // Tạo thư mục nếu chưa tồn tại
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Lấy thông tin file
    $fileName = basename($_FILES['logoPath']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    // Kiểm tra định dạng file
    $allowedExts = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($fileExt, $allowedExts)) {
        $_SESSION['error_message'] = "Chỉ chấp nhận file ảnh định dạng JPG, JPEG, PNG, GIF!";
        header("Location: add.php");
        exit();
    }
    
    // Kiểm tra kích thước file (max 5MB)
    if ($_FILES['logoPath']['size'] > 5 * 1024 * 1024) {
        $_SESSION['error_message'] = "Kích thước file không được vượt quá 5MB!";
        header("Location: add.php");
        exit();
    }
    
    // Tạo tên file mới để tránh trùng lặp
    $newFileName = 'brand_' . time() . '_' . mt_rand(1000, 9999) . '.' . $fileExt;
    $targetFilePath = $targetDir . $newFileName;
    
    // Upload file
    if (move_uploaded_file($_FILES['logoPath']['tmp_name'], $targetFilePath)) {
        $logoPath = 'uploads/brands/' . $newFileName;
    } else {
        $_SESSION['error_message'] = "Có lỗi xảy ra khi upload file!";
        header("Location: add.php");
        exit();
    }
}

// Thêm thương hiệu vào database
$query = "INSERT INTO Brands (BrandName, Description, LogoPath) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $brandName, $description, $logoPath);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Đã thêm thương hiệu '{$brandName}' thành công!";
    header("Location: /hoan/admin/brands.php");
    exit();
} else {
    $_SESSION['error_message'] = "Lỗi khi thêm thương hiệu: " . $conn->error;
    header("Location: add.php");
    exit();
}

$stmt->close();
$conn->close();
?> 