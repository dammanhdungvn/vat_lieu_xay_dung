<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Chi tiết đánh giá';

// Include auth check
require_once '../includes/auth_check.php';

// Include database connection
require_once '../../config/db_connection.php';

// Kiểm tra ID đánh giá
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID đánh giá không hợp lệ!";
    header("Location: /hoan/admin/reviews.php");
    exit();
}

$reviewId = (int)$_GET['id'];

// Truy vấn thông tin đánh giá
$query = "SELECT r.*, p.ProductName, p.ImagePath, u.FullName, u.Email 
          FROM Reviews r 
          JOIN Products p ON r.ProductID = p.ProductID 
          JOIN Users u ON r.UserID = u.UserID 
          WHERE r.ReviewID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $reviewId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Không tìm thấy đánh giá!";
    header("Location: /hoan/admin/reviews.php");
    exit();
}

$review = $result->fetch_assoc();

// Include header và sidebar
include_once '../includes/header_admin.php';
include_once '../includes/sidebar_admin.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="text-primary m-0 fw-semibold">Chi tiết đánh giá</h5>
                    <a href="/hoan/admin/reviews.php" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Quay lại
                    </a>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php 
                            echo $_SESSION['error_message']; 
                            unset($_SESSION['error_message']);
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <?php if (!empty($review['ImagePath'])): ?>
                                <img src="/hoan/<?php echo htmlspecialchars($review['ImagePath']); ?>" 
                                     alt="<?php echo htmlspecialchars($review['ProductName']); ?>" 
                                     class="img-fluid rounded">
                            <?php else: ?>
                                <div class="bg-light rounded p-4 text-center">
                                    <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-2 mb-0">Không có hình ảnh</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <h4><?php echo htmlspecialchars($review['ProductName']); ?></h4>
                            <p class="text-muted mb-2">
                                <i class="bi bi-person me-1"></i>
                                <?php echo htmlspecialchars($review['FullName']); ?> 
                                (<?php echo htmlspecialchars($review['Email']); ?>)
                            </p>
                            <p class="text-muted mb-2">
                                <i class="bi bi-calendar me-1"></i>
                                <?php echo date('d/m/Y H:i', strtotime($review['ReviewDate'])); ?>
                            </p>
                            <div class="mb-2">
                                <?php
                                $rating = $review['Rating'];
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $rating) {
                                        echo '<i class="bi bi-star-fill text-warning"></i>';
                                    } else {
                                        echo '<i class="bi bi-star text-warning"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <div class="mb-3">
                                <strong>Trạng thái:</strong>
                                <form method="POST" action="/hoan/admin/reviews.php" class="d-inline">
                                    <input type="hidden" name="review_id" value="<?php echo $review['ReviewID']; ?>">
                                    <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $review['Status'] == 'pending' ? 'selected' : ''; ?>>Chờ duyệt</option>
                                        <option value="approved" <?php echo $review['Status'] == 'approved' ? 'selected' : ''; ?>>Đã duyệt</option>
                                        <option value="rejected" <?php echo $review['Status'] == 'rejected' ? 'selected' : ''; ?>>Từ chối</option>
                                    </select>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Bình luận</h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($review['Comment'])): ?>
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($review['Comment'])); ?></p>
                            <?php else: ?>
                                <p class="text-muted mb-0">Không có bình luận</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="/hoan/admin/reviews.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Quay lại
                        </a>
                        <a href="/hoan/admin/reviews.php?delete=<?php echo $review['ReviewID']; ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?')">
                            <i class="bi bi-trash me-1"></i> Xóa đánh giá
                        </a>
                    </div>
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