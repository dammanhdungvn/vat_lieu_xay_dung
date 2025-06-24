<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Quản lý sản phẩm';

// Include auth check
require_once '../includes/auth_check.php';

// Include database connection
require_once '../../config/db_connection.php';

// Truy vấn sản phẩm với JOIN Categories
$query = "SELECT p.*, c.CategoryName 
          FROM Products p 
          LEFT JOIN Categories c ON p.CategoryID = c.CategoryID 
          ORDER BY p.ProductID DESC";
$result = $conn->query($query);

// Include header và sidebar
include_once '../includes/header_admin.php';
include_once '../includes/sidebar_admin.php';
?>

<style>
    .card-header h5 {
        pointer-events: none; /* Vô hiệu hóa sự kiện click */
        cursor: default; /* Đổi con trỏ chuột thành mặc định */
    }
    .page-title {
        pointer-events: none !important; /* Vô hiệu hóa sự kiện click */
        cursor: default !important; /* Đổi con trỏ chuột thành mặc định */
        user-select: none; /* Ngăn chọn text */
    }
</style>

<div class="container-fluid mt-4">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="text-primary m-0 fw-semibold page-title">Quản lý sản phẩm</h5>
            </div>
            <div>
                <a href="/hoan/admin/dashboard.php" class="btn btn-secondary me-2">
                    <i class="bi bi-arrow-left me-1"></i>Quay lại
                </a>
                <a href="add.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Thêm sản phẩm mới
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Tồn kho</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) { 
                                // Xác định đường dẫn ảnh
                                $imagePath = '';
                                if (!empty($row['ImagePath'])) {
                                    if (strpos($row['ImagePath'], 'http') === 0) {
                                        // Nếu là URL từ internet
                                        $imagePath = $row['ImagePath'];
                                    } else {
                                        // Nếu là đường dẫn local
                                        $base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/hoan';
                                        $imagePath = $base_url . '/' . ltrim($row['ImagePath'], '/');
                                    }
                                } else {
                                    $base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/hoan';
                                    $imagePath = $base_url . '/assets/img/no-image.jpg';
                                }
                        ?>
                        <tr>
                            <td><?php echo $row['ProductID']; ?></td>
                            <td>
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($row['ProductName']); ?>" 
                                     class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
                            <td><?php echo htmlspecialchars($row['CategoryName'] ?? 'Không có danh mục'); ?></td>
                            <td><?php echo number_format($row['Price'], 0, ',', '.'); ?>đ</td>
                            <td><?php echo $row['StockQuantity'] . ' ' . $row['Unit']; ?></td>
                            <td>
                                <?php if($row['StockQuantity'] > 0): ?>
                                    <span class="badge bg-success">Còn hàng</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Hết hàng</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="/hoan/admin/products/view.php?id=<?php echo $row['ProductID']; ?>" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="/hoan/admin/products/edit.php?id=<?php echo $row['ProductID']; ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/hoan/admin/products/delete.php?id=<?php echo $row['ProductID']; ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else { 
                        ?>
                        <tr>
                            <td colspan="8" class="text-center">Không có sản phẩm nào</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal xác nhận xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa sản phẩm <span id="product-name"></span>?</p>
                <p class="text-danger">Lưu ý: Hành động này không thể hoàn tác!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <a href="#" id="delete-link" class="btn btn-danger">Xóa</a>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(productId, productName) {
        document.getElementById('product-name').textContent = productName;
        document.getElementById('delete-link').href = 'delete_product.php?id=' + productId;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>

<?php
// Include footer
include_once '../includes/footer_admin.php';
$conn->close();
?> 