<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Quản lý danh mục';

// Include auth check
require_once '../includes/auth_check.php';

// Include database connection
require_once '../../config/db_connection.php';

// Truy vấn danh mục
$query = "SELECT * FROM Categories ORDER BY CategoryID";
$result = $conn->query($query);

// Include header và sidebar
include_once '../includes/header_admin.php';
include_once '../includes/sidebar_admin.php';
?>

<div class="container-fluid mt-4">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-tags me-2"></i>Danh sách danh mục</h5>
            <a href="add_category.php" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Thêm danh mục mới
            </a>
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
                            <th>Tên danh mục</th>
                            <th>Mô tả</th>
                            <th>Số sản phẩm</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) { 
                                // Xác định đường dẫn ảnh
                                $imagePath = !empty($row['ImagePath']) ? $row['ImagePath'] : 'assets/img/no-category-image.jpg';
                                
                                // Đếm số sản phẩm trong danh mục
                                $countQuery = "SELECT COUNT(*) as total FROM Products WHERE CategoryID = " . $row['CategoryID'];
                                $countResult = $conn->query($countQuery);
                                $productCount = 0;
                                if ($countResult && $countResult->num_rows > 0) {
                                    $productCount = $countResult->fetch_assoc()['total'];
                                }
                        ?>
                        <tr>
                            <td><?php echo $row['CategoryID']; ?></td>
                            <td>
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($row['CategoryName']); ?>" 
                                     class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                            </td>
                            <td><?php echo htmlspecialchars($row['CategoryName']); ?></td>
                            <td>
                                <?php 
                                    $description = $row['Description'] ?? '';
                                    echo !empty($description) ? htmlspecialchars(substr($description, 0, 100)) . (strlen($description) > 100 ? '...' : '') : 'Không có mô tả';
                                ?>
                            </td>
                            <td><span class="badge bg-info"><?php echo $productCount; ?></span></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="edit_category.php?id=<?php echo $row['CategoryID']; ?>" class="btn btn-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" 
                                            onclick="confirmDelete(<?php echo $row['CategoryID']; ?>, '<?php echo htmlspecialchars($row['CategoryName']); ?>', <?php echo $productCount; ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else { 
                        ?>
                        <tr>
                            <td colspan="6" class="text-center">Không có danh mục nào</td>
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
                <p>Bạn có chắc chắn muốn xóa danh mục <span id="category-name"></span>?</p>
                <div id="warning-products" class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Danh mục này đang có <span id="product-count"></span> sản phẩm. Xóa danh mục có thể ảnh hưởng đến các sản phẩm này!
                </div>
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
    function confirmDelete(categoryId, categoryName, productCount) {
        document.getElementById('category-name').textContent = categoryName;
        document.getElementById('delete-link').href = 'delete_category.php?id=' + categoryId;
        
        // Hiển thị cảnh báo nếu danh mục có sản phẩm
        const warningElement = document.getElementById('warning-products');
        const productCountElement = document.getElementById('product-count');
        
        if (productCount > 0) {
            productCountElement.textContent = productCount;
            warningElement.style.display = 'block';
        } else {
            warningElement.style.display = 'none';
        }
        
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>

<?php
// Include footer
include_once '../includes/footer_admin.php';
$conn->close();
?> 