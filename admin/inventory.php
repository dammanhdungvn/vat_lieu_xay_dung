<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Quản lý kho hàng';

// Include authentication check
require_once 'includes/auth_check.php';

// Include database connection
require_once '../config/db_connection.php';

// Xử lý cập nhật số lượng nếu có
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $new_quantity = (int)$_POST['quantity'];
    
    $update_query = "UPDATE Products SET StockQuantity = $new_quantity WHERE ProductID = $product_id";
    
    if ($conn->query($update_query) === TRUE) {
        $_SESSION['admin_success_message'] = "Đã cập nhật số lượng thành công!";
    } else {
        $_SESSION['admin_error_message'] = "Lỗi khi cập nhật: " . $conn->error;
    }
    
    // Chuyển hướng để tránh gửi lại form khi refresh
    header("Location: inventory.php");
    exit;
}

// Lọc sản phẩm
$filter_condition = '';
$category_filter = '';
$search_term = '';
$stock_filter = '';

// Lọc theo danh mục
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category_id = (int)$_GET['category'];
    $category_filter = $category_id;
    $filter_condition .= ($filter_condition ? ' AND ' : ' WHERE ') . "p.CategoryID = $category_id";
}

// Lọc theo từ khóa tìm kiếm
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $search_term = $search;
    $filter_condition .= ($filter_condition ? ' AND ' : ' WHERE ') . "(p.ProductName LIKE '%$search%')";
}

// Lọc theo tồn kho
if (isset($_GET['stock_status']) && !empty($_GET['stock_status'])) {
    $stock_status = $_GET['stock_status'];
    $stock_filter = $stock_status;
    
    switch ($stock_status) {
        case 'out_of_stock':
            $filter_condition .= ($filter_condition ? ' AND ' : ' WHERE ') . "p.StockQuantity = 0";
            break;
        case 'low_stock':
            $filter_condition .= ($filter_condition ? ' AND ' : ' WHERE ') . "p.StockQuantity > 0 AND p.StockQuantity <= 10";
            break;
        case 'in_stock':
            $filter_condition .= ($filter_condition ? ' AND ' : ' WHERE ') . "p.StockQuantity > 10";
            break;
    }
}

// Truy vấn danh sách sản phẩm với thông tin danh mục
$query = "SELECT p.ProductID, p.ProductName, p.Price, p.StockQuantity, p.Unit, c.CategoryName 
          FROM Products p
          LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
          $filter_condition
          ORDER BY p.ProductName";
$result = $conn->query($query);

// Lấy danh sách danh mục cho bộ lọc
$categories_query = "SELECT CategoryID, CategoryName FROM Categories ORDER BY CategoryName";
$categories_result = $conn->query($categories_query);

// Include header và sidebar
include_once 'includes/header_admin.php';
include_once 'includes/sidebar_admin.php';
?>

<div class="container-fluid mt-4">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-box2-fill me-2"></i>Quản lý kho hàng</h5>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['admin_success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['admin_success_message']; 
                    unset($_SESSION['admin_success_message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['admin_error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['admin_error_message']; 
                    unset($_SESSION['admin_error_message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <!-- Bộ lọc sản phẩm -->
            <div class="row mb-4">
                <div class="col-12">
                    <form method="get" action="" class="row g-3">
                        <div class="col-md-3">
                            <label for="category" class="form-label">Danh mục</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Tất cả danh mục</option>
                                <?php 
                                if ($categories_result && $categories_result->num_rows > 0) {
                                    while($category = $categories_result->fetch_assoc()) {
                                        $selected = ($category_filter == $category['CategoryID']) ? 'selected' : '';
                                        echo '<option value="' . $category['CategoryID'] . '" ' . $selected . '>' . htmlspecialchars($category['CategoryName']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="stock_status" class="form-label">Tình trạng tồn kho</label>
                            <select class="form-select" id="stock_status" name="stock_status">
                                <option value="">Tất cả</option>
                                <option value="in_stock" <?php echo ($stock_filter == 'in_stock') ? 'selected' : ''; ?>>Còn hàng (>10)</option>
                                <option value="low_stock" <?php echo ($stock_filter == 'low_stock') ? 'selected' : ''; ?>>Sắp hết (1-10)</option>
                                <option value="out_of_stock" <?php echo ($stock_filter == 'out_of_stock') ? 'selected' : ''; ?>>Hết hàng (0)</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Tìm theo tên hoặc SKU" value="<?php echo htmlspecialchars($search_term); ?>">
                        </div>
                        
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">Lọc</button>
                            <a href="inventory.php" class="btn btn-secondary">Đặt lại</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Bảng danh sách sản phẩm -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tên sản phẩm</th>
                            <th>SKU</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) { 
                                // Xác định trạng thái tồn kho
                                $stock_class = '';
                                $stock_text = '';
                                
                                if ($row['StockQuantity'] <= 0) {
                                    $stock_class = 'bg-danger';
                                    $stock_text = 'Hết hàng';
                                } elseif ($row['StockQuantity'] <= 10) {
                                    $stock_class = 'bg-warning';
                                    $stock_text = 'Sắp hết';
                                } else {
                                    $stock_class = 'bg-success';
                                    $stock_text = 'Còn hàng';
                                }
                        ?>
                        <tr>
                            <td><?php echo $row['ProductID']; ?></td>
                            <td><?php echo htmlspecialchars($row['ProductName']); ?></td>
                            <td><?php echo htmlspecialchars($row['Unit']); ?></td>
                            <td><?php echo htmlspecialchars($row['CategoryName']); ?></td>
                            <td><?php echo number_format($row['Price'], 0, ',', '.'); ?>đ</td>
                            <td>
                                <form method="post" class="quantity-form" id="form-<?php echo $row['ProductID']; ?>">
                                    <div class="input-group" style="width: 150px;">
                                        <input type="hidden" name="product_id" value="<?php echo $row['ProductID']; ?>">
                                        <input type="number" class="form-control form-control-sm quantity-input" name="quantity" value="<?php echo $row['StockQuantity']; ?>" min="0">
                                        <button type="submit" name="update_quantity" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </div>
                                </form>
                            </td>
                            <td>
                                <?php if($row['StockQuantity'] > 0): ?>
                                    <span class="badge bg-success">Còn hàng</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Hết hàng</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="products/edit.php?id=<?php echo $row['ProductID']; ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Sửa
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

<?php
// Include footer
include_once 'includes/footer_admin.php';
$conn->close();
?> 