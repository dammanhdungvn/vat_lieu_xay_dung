<?php
// Include database connection
require_once '../../config/db_connection.php';

// SQL để tạo bảng Brands
$sql = "CREATE TABLE IF NOT EXISTS Brands (
    BrandID INT PRIMARY KEY AUTO_INCREMENT,
    BrandName VARCHAR(255) NOT NULL UNIQUE,
    Description TEXT,
    LogoPath VARCHAR(255)
)";

// Thực thi câu lệnh SQL
if ($conn->query($sql) === TRUE) {
    echo "Bảng Brands đã được tạo thành công!";
} else {
    echo "Lỗi khi tạo bảng: " . $conn->error;
}

$conn->close();
?> 