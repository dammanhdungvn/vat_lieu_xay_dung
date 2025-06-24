<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Quản lý đánh giá';

// Include auth check
require_once 'includes/auth_check.php';

// Include database connection
require_once '../config/db_connection.php';

// Xử lý cập nhật trạng thái đánh giá
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id']) && isset($_POST['status'])) {
    $review_id = $_POST['review_id'];
    $status = $_POST['status'];
    
    $query = "UPDATE Reviews SET Status = ? WHERE ReviewID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $review_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Đã cập nhật trạng thái đánh giá thành công!";
    } else {
        $_SESSION['error_message'] = "Lỗi khi cập nhật trạng thái: " . $conn->error;
    }
    
    header("Location: reviews.php");
    exit();
}

// Xử lý xóa đánh giá
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $review_id = $_GET['delete'];
    
    $query = "DELETE FROM Reviews WHERE ReviewID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $review_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Đã xóa đánh giá thành công!";
    } else {
        $_SESSION['error_message'] = "Lỗi khi xóa đánh giá: " . $conn->error;
    }
    
    header("Location: reviews.php");
    exit();
}

// Lấy danh sách đánh giá
$query = "SELECT r.*, p.ProductName, u.FullName, u.Email 
          FROM Reviews r 
          JOIN Products p ON r.ProductID = p.ProductID 
          JOIN Users u ON r.UserID = u.UserID 
          ORDER BY r.ReviewDate DESC";
$result = $conn->query($query);

// Include header và sidebar
include_once 'includes/header_admin.php';
include_once 'includes/sidebar_admin.php';
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="text-primary m-0 fw-semibold">Quản lý đánh giá</h4>
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
                            <th>Sản phẩm</th>
                            <th>Người đánh giá</th>
                            <th>Đánh giá</th>
                            <th>Bình luận</th>
                            <th>Ngày đánh giá</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($review = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $review['ReviewID']; ?></td>
                                    <td><?php echo htmlspecialchars($review['ProductName']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($review['FullName']); ?>
                                        <br>
                                        <small class="text-muted"><?php echo htmlspecialchars($review['Email']); ?></small>
                                    </td>
                                    <td>
                                        <div class="text-warning">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="bi bi-star<?php echo $i <= $review['Rating'] ? '-fill' : ''; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                        if (!empty($review['Comment'])) {
                                            echo htmlspecialchars(substr($review['Comment'], 0, 100)) . 
                                                 (strlen($review['Comment']) > 100 ? '...' : '');
                                        } else {
                                            echo '<span class="text-muted">Không có bình luận</span>';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($review['ReviewDate'])); ?></td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="review_id" value="<?php echo $review['ReviewID']; ?>">
                                            <select name="status" class="form-select form-select-sm" 
                                                    onchange="this.form.submit()" 
                                                    style="width: auto;">
                                                <option value="pending" <?php echo $review['Status'] === 'pending' ? 'selected' : ''; ?>>
                                                    Chờ duyệt
                                                </option>
                                                <option value="approved" <?php echo $review['Status'] === 'approved' ? 'selected' : ''; ?>>
                                                    Đã duyệt
                                                </option>
                                                <option value="rejected" <?php echo $review['Status'] === 'rejected' ? 'selected' : ''; ?>>
                                                    Từ chối
                                                </option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="reviews/view.php?id=<?php echo $review['ReviewID']; ?>" 
                                               class="btn btn-sm btn-info" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="reviews.php?delete=<?php echo $review['ReviewID']; ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?')"
                                               title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Không có đánh giá nào</td>
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