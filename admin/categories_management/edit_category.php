<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Chỉnh sửa danh mục';

// Include auth check
require_once '../includes/auth_check.php';

// Include database connection
require_once '../../config/db_connection.php';

// Kiểm tra có ID được truyền vào không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "ID danh mục không hợp lệ!";
    header("Location: index.php");
    exit();
}

$categoryId = intval($_GET['id']);

// Truy vấn lấy thông tin danh mục
$query = "SELECT * FROM Categories WHERE CategoryID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra danh mục có tồn tại không
if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Danh mục không tồn tại!";
    header("Location: index.php");
    exit();
}

// Lấy dữ liệu danh mục
$category = $result->fetch_assoc();

// Include header và sidebar
include_once '../includes/header_admin.php';
include_once '../includes/sidebar_admin.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Chỉnh sửa danh mục</h5>
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
                    
                    <form action="process_edit_category.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="categoryId" value="<?php echo $categoryId; ?>">
                        
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="categoryName" name="categoryName" value="<?php echo htmlspecialchars($category['CategoryName']); ?>" required>
                            <div class="form-text">Tên danh mục phải là duy nhất và không được trùng lặp.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($category['Description'] ?? ''); ?></textarea>
                            <div class="form-text">Mô tả chi tiết về danh mục sản phẩm.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="imagePath" class="form-label">Ảnh danh mục</label>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="image_type" id="image_url" value="url" 
                                           <?php echo (strpos($category['ImagePath'], 'http') === 0) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="image_url">
                                        Nhập đường dẫn ảnh
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="image_type" id="image_upload" value="upload"
                                           <?php echo (strpos($category['ImagePath'], 'http') !== 0) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="image_upload">
                                        Upload file ảnh
                                    </label>
                                </div>
                            </div>
                            
                            <div id="url_input" class="mb-3" style="display: <?php echo (strpos($category['ImagePath'], 'http') === 0) ? 'block' : 'none'; ?>;">
                                <input type="url" class="form-control" id="image_url_input" name="image_url" 
                                       value="<?php echo htmlspecialchars($category['ImagePath']); ?>" 
                                       placeholder="Nhập đường dẫn ảnh">
                            </div>
                            
                            <div id="file_input" class="mb-3" style="display: <?php echo (strpos($category['ImagePath'], 'http') !== 0) ? 'block' : 'none'; ?>;">
                                <input type="file" class="form-control" id="imagePath" name="imagePath" accept="image/*">
                            </div>
                            
                            <div class="mt-3" id="image-preview-container">
                                <?php 
                                if (!empty($category['ImagePath'])) {
                                    if (strpos($category['ImagePath'], 'http') === 0) {
                                        // Nếu là URL từ internet
                                        $imagePath = $category['ImagePath'];
                                    } else {
                                        // Nếu là đường dẫn local
                                        $base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/hoan';
                                        $imagePath = $base_url . '/' . ltrim($category['ImagePath'], '/');
                                    }
                                ?>
                                    <img id="image-preview" src="<?php echo htmlspecialchars($imagePath); ?>" 
                                         alt="Xem trước hình ảnh" class="img-thumbnail" style="max-height: 150px;">
                                <?php } else { ?>
                                    <img id="image-preview" src="" alt="Xem trước hình ảnh" class="img-thumbnail" style="max-height: 150px; display: none;">
                                <?php } ?>
                            </div>
                            <div class="form-text">Chọn ảnh đại diện cho danh mục (JPG, PNG, GIF). Để trống nếu không muốn thay đổi.</div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Cập nhật danh mục
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Hướng dẫn</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Tên danh mục</strong>: Bắt buộc, không được trùng lặp với danh mục khác.
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Mô tả</strong>: Tùy chọn, mô tả chi tiết về danh mục để giúp khách hàng hiểu rõ hơn.
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <strong>Ảnh danh mục</strong>: Tùy chọn, ảnh đại diện cho danh mục trên trang web. Để trống nếu muốn giữ ảnh cũ.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Script chuyển đổi giữa nhập URL và upload file
document.querySelectorAll('input[name="image_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const urlInput = document.getElementById('url_input');
        const fileInput = document.getElementById('file_input');
        const imagePreview = document.getElementById('image-preview');
        
        if (this.value === 'url') {
            urlInput.style.display = 'block';
            fileInput.style.display = 'none';
            // Cập nhật preview khi nhập URL
            document.getElementById('image_url_input').addEventListener('input', function() {
                imagePreview.src = this.value;
                imagePreview.style.display = this.value ? 'block' : 'none';
            });
        } else {
            urlInput.style.display = 'none';
            fileInput.style.display = 'block';
        }
    });
});

// Script hiển thị xem trước hình ảnh khi upload file
document.getElementById('imagePath').addEventListener('change', function() {
    const file = this.files[0];
    const previewImg = document.getElementById('image-preview');
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
        }
        
        reader.readAsDataURL(file);
    } else {
        previewImg.style.display = 'none';
    }
});
</script>

<?php
// Include footer
include_once '../includes/footer_admin.php';
$stmt->close();
$conn->close();
?> 