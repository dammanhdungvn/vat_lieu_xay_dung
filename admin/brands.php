<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Quản lý thương hiệu';

// Include auth check
require_once 'includes/auth_check.php';

// Include database connection
require_once '../config/db_connection.php';

// Xử lý xóa thương hiệu
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $brand_id = $_GET['delete'];
    
    // Kiểm tra xem thương hiệu có tồn tại không
    $check_query = "SELECT * FROM Brands WHERE BrandID = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $brand_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $brand = $result->fetch_assoc();
        
        // Xóa logo nếu có
        if (!empty($brand['LogoPath']) && file_exists("../" . $brand['LogoPath'])) {
            unlink("../" . $brand['LogoPath']);
        }
        
        // Xóa thương hiệu
        $delete_query = "DELETE FROM Brands WHERE BrandID = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $brand_id);
        
        if ($delete_stmt->execute()) {
            $_SESSION['success_message'] = "Đã xóa thương hiệu thành công!";
        } else {
            $_SESSION['error_message'] = "Lỗi khi xóa thương hiệu: " . $conn->error;
        }
    } else {
        $_SESSION['error_message'] = "Không tìm thấy thương hiệu!";
    }
    
    header("Location: brands.php");
    exit();
}

// Lấy danh sách thương hiệu
$query = "SELECT * FROM Brands ORDER BY BrandName ASC";
$result = $conn->query($query);

// Include header và sidebar
include_once 'includes/header_admin.php';
include_once 'includes/sidebar_admin.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-primary m-0 fw-semibold">Quản lý thương hiệu</h4>
        <a href="brands/add.php" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Thêm thương hiệu mới
        </a>
    </div>

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

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Logo</th>
                            <th>Tên thương hiệu</th>
                            <th>Mô tả</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($brand = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $brand['BrandID']; ?></td>
                                    <td>
                                        <?php if (!empty($brand['LogoPath'])): ?>
                                            <img src="../<?php echo htmlspecialchars($brand['LogoPath']); ?>" 
                                                 alt="<?php echo htmlspecialchars($brand['BrandName']); ?>" 
                                                 class="img-thumbnail" style="max-width: 50px;">
                                        <?php else: ?>
                                            <span class="text-muted">Không có logo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($brand['BrandName']); ?></td>
                                    <td>
                                        <?php 
                                        if (!empty($brand['Description'])) {
                                            echo htmlspecialchars(substr($brand['Description'], 0, 100)) . 
                                                 (strlen($brand['Description']) > 100 ? '...' : '');
                                        } else {
                                            echo '<span class="text-muted">Không có mô tả</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="brands/view.php?id=<?php echo $brand['BrandID']; ?>" 
                                               class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="brands/edit.php?id=<?php echo $brand['BrandID']; ?>" 
                                               class="btn btn-sm btn-warning" title="Chỉnh sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="brands.php?delete=<?php echo $brand['BrandID']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa thương hiệu này?')"
                                               title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Không có thương hiệu nào</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once 'includes/footer_admin.php';
$conn->close();
?> 