<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Thêm sản phẩm mới';

// Include auth check
require_once '../includes/auth_check.php';

// Include database connection
require_once '../../config/db_connection.php';

// Lấy danh sách danh mục cho dropdown
$categories_query = "SELECT CategoryID, CategoryName FROM Categories ORDER BY CategoryName";
$categories_result = $conn->query($categories_query);

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $name = $conn->real_escape_string($_POST['name']);
    $category_id = (int)$_POST['category_id'];
    $price = (float)$_POST['price'];
    $description = $conn->real_escape_string($_POST['description']);
    $quantity = (int)$_POST['quantity'];
    $unit = $conn->real_escape_string($_POST['unit']);
    $status = isset($_POST['status']) ? 1 : 0;
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Xử lý upload hình ảnh
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Nếu người dùng upload file
        $upload_dir = '../../assets/images/products/';
        
        // Tạo thư mục nếu không tồn tại
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Lấy thông tin file
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Tạo tên file mới để tránh trùng lặp
        $new_file_name = 'product_' . time() . '_' . uniqid() . '.' . $file_ext;
        $full_path = $upload_dir . $new_file_name;
        
        // Kiểm tra định dạng file
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_ext)) {
            // Di chuyển file tạm vào thư mục đích
            if (move_uploaded_file($file_tmp, $full_path)) {
                $image_path = 'assets/images/products/' . $new_file_name;
            } else {
                $_SESSION['admin_error_message'] = "Không thể upload hình ảnh!";
            }
        } else {
            $_SESSION['admin_error_message'] = "Định dạng file không được hỗ trợ!";
        }
    } elseif (!empty($_POST['image_url'])) {
        // Nếu người dùng nhập URL
        $image_path = $conn->real_escape_string($_POST['image_url']);
    }
    
    // Thêm sản phẩm vào database
    $query = "INSERT INTO Products (ProductName, CategoryID, Price, Description, ImagePath, StockQuantity, Unit, CreatedAt) 
              VALUES ('$name', $category_id, $price, '$description', '$image_path', $quantity, '$unit', NOW())";
    
    if ($conn->query($query) === TRUE) {
        $_SESSION['admin_success_message'] = "Thêm sản phẩm mới thành công!";
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['admin_error_message'] = "Lỗi: " . $conn->error;
    }
}

// Include header và sidebar
include_once '../includes/header_admin.php';
include_once '../includes/sidebar_admin.php';
?>

<div class="container-fluid mt-4">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Thêm sản phẩm mới</h5>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['admin_error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['admin_error_message']; 
                    unset($_SESSION['admin_error_message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php 
                                if ($categories_result && $categories_result->num_rows > 0) {
                                    while($category = $categories_result->fetch_assoc()) {
                                        echo '<option value="' . $category['CategoryID'] . '">' . htmlspecialchars($category['CategoryName']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label">Giá <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="price" name="price" min="0" step="1000" required>
                                <span class="input-group-text">đ</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh sản phẩm</label>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="image_type" id="image_url" value="url" checked>
                                    <label class="form-check-label" for="image_url">
                                        Nhập đường dẫn ảnh
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="image_type" id="image_upload" value="upload">
                                    <label class="form-check-label" for="image_upload">
                                        Upload file ảnh
                                    </label>
                                </div>
                            </div>
                            
                            <div id="url_input" class="mb-3">
                                <input type="url" class="form-control" id="image_url_input" name="image_url" placeholder="Nhập đường dẫn ảnh">
                            </div>
                            
                            <div id="file_input" class="mb-3" style="display: none;">
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>
                            
                            <div class="mt-3" id="image-preview-container">
                                <img id="image-preview" src="" alt="Xem trước hình ảnh" style="max-width: 100%; display: none;">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Số lượng trong kho <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="0" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="unit" class="form-label">Đơn vị tính <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="unit" name="unit" required>
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <a href="index.php" class="btn btn-secondary me-2">Hủy</a>
                    <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
                </div>
            </form>
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
document.getElementById('image').addEventListener('change', function() {
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
$conn->close();
?> 