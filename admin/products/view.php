<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Chi tiết sản phẩm';

// Include auth check
require_once '../includes/auth_check.php';

// Include database connection
require_once '../../config/db_connection.php';

// Kiểm tra ID sản phẩm
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['admin_error_message'] = "ID sản phẩm không hợp lệ!";
    header("Location: index.php");
    exit;
}

$product_id = (int)$_GET['id'];

// Lấy thông tin sản phẩm
$query = "SELECT p.*, c.CategoryName 
          FROM Products p 
          LEFT JOIN Categories c ON p.CategoryID = c.CategoryID 
          WHERE p.ProductID = $product_id";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    $_SESSION['admin_error_message'] = "Không tìm thấy sản phẩm!";
    header("Location: index.php");
    exit;
}

$product = $result->fetch_assoc();

// Include header và sidebar
include_once '../includes/header_admin.php';
include_once '../includes/sidebar_admin.php';
?>

<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-eye me-2"></i>Chi tiết sản phẩm</h5>
            <a href="index.php" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Quay lại
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center mb-4">
                        <?php 
                        if (!empty($product['ImagePath'])) {
                            if (strpos($product['ImagePath'], 'http') === 0) {
                                // Nếu là URL từ internet
                                $imagePath = $product['ImagePath'];
                            } else {
                                // Nếu là đường dẫn local
                                $base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/hoan';
                                $imagePath = $base_url . '/' . ltrim($product['ImagePath'], '/');
                            }
                        ?>
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                 alt="<?php echo htmlspecialchars($product['ProductName']); ?>" 
                                 class="img-fluid rounded" style="max-height: 300px;">
                        <?php } else { 
                            $base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/hoan';
                            $imagePath = $base_url . '/assets/img/no-image.jpg';
                        ?>
                            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="No image" class="img-fluid rounded">
                        <?php } ?>
                    </div>
                </div>
                <div class="col-md-8">
                    <table class="table">
                        <tr>
                            <th style="width: 200px;">Tên sản phẩm:</th>
                            <td><?php echo htmlspecialchars($product['ProductName']); ?></td>
                        </tr>
                        <tr>
                            <th>Danh mục:</th>
                            <td><?php echo htmlspecialchars($product['CategoryName']); ?></td>
                        </tr>
                        <tr>
                            <th>Giá:</th>
                            <td><?php echo number_format($product['Price'], 0, ',', '.'); ?>đ</td>
                        </tr>
                        <tr>
                            <th>Số lượng trong kho:</th>
                            <td><?php echo number_format($product['StockQuantity']); ?> <?php echo htmlspecialchars($product['Unit']); ?></td>
                        </tr>
                        <tr>
                            <th>Đơn vị tính:</th>
                            <td><?php echo htmlspecialchars($product['Unit']); ?></td>
                        </tr>
                        <tr>
                            <th>Ngày tạo:</th>
                            <td><?php echo date('d/m/Y H:i', strtotime($product['CreatedAt'])); ?></td>
                        </tr>
                        <tr>
                            <th>Mô tả:</th>
                            <td><?php echo nl2br(htmlspecialchars($product['Description'])); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once '../includes/footer_admin.php';
$conn->close();
?> 