<?php
session_start();
require_once '../../config/db_connection.php';
require_once '../../includes/header.php';

// Get all categories for filter dropdown
$stmt = $conn->prepare("SELECT CategoryID, CategoryName FROM Categories ORDER BY CategoryName");
$stmt->execute();
$categories = $stmt->get_result();
$stmt->close();

// Build the base query
$sql = "SELECT p.ProductID, p.ProductName, p.ImagePath, p.Price, p.Unit, p.StockQuantity, 
               IFNULL(c.CategoryName, 'Chưa phân loại') AS CategoryName 
        FROM Products p 
        LEFT JOIN Categories c ON p.CategoryID = c.CategoryID";

// Initialize prepared statement parameters
$params = [];
$types = "";

// Add search condition if search term is provided
if (isset($_GET['search_term']) && !empty($_GET['search_term'])) {
    $search_term = trim($_GET['search_term']);
    $sql .= " WHERE p.ProductName LIKE CONCAT('%', ?, '%')";
    $params[] = $search_term;
    $types .= "s";
}

// Add category filter if selected
if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
    if (strpos($sql, 'WHERE') !== false) {
        $sql .= " AND p.CategoryID = ?";
    } else {
        $sql .= " WHERE p.CategoryID = ?";
    }
    $params[] = $_GET['category_id'];
    $types .= "i";
}

// Add order by
$sql .= " ORDER BY p.CreatedAt DESC";

// Pagination logic
$items_per_page = 12; // Tăng số sản phẩm mỗi trang
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Count total products for pagination
$count_sql = "SELECT COUNT(*) as total FROM Products p LEFT JOIN Categories c ON p.CategoryID = c.CategoryID";
if (isset($_GET['search_term']) && !empty($_GET['search_term'])) {
    $count_sql .= " WHERE p.ProductName LIKE CONCAT('%', ?, '%')";
}
if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
    if (strpos($count_sql, 'WHERE') !== false) {
        $count_sql .= " AND p.CategoryID = ?";
    } else {
        $count_sql .= " WHERE p.CategoryID = ?";
    }
}

$stmt = $conn->prepare($count_sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_result = $stmt->get_result();
$total_products = 0;
if ($row = $total_result->fetch_assoc()) {
    $total_products = (int)$row['total'];
}
$total_pages = ceil($total_products / $items_per_page);

// Add limit and offset to main query
$sql .= " LIMIT ? OFFSET ?";
$params[] = $items_per_page;
$params[] = $offset;
$types .= "ii";

// Execute main query
$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

// Helper function to get proper image URL
function getImageUrl($path) {
    if (empty($path)) {
        return '../../assets/img/no-image.jpg';
    }
    
    // If path starts with http:// or https://, it's already a complete URL
    if (preg_match('/^https?:\/\//', $path)) {
        return $path;
    }
    
    // Remove any leading slash for consistency
    $path = ltrim($path, '/');
    
    // If path starts with assets/, add only ../../ 
    if (strpos($path, 'assets/') === 0) {
        return '../../' . $path;
    }
    
    // Otherwise, assume it's a relative path from the project root
    return '../../' . $path;
}
?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-primary fw-bold">Danh sách Sản phẩm</h2>
            <p class="text-muted">Tìm kiếm và lựa chọn sản phẩm phù hợp với nhu cầu của bạn</p>
        </div>
    </div>
    
    <!-- Search and Filter Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" name="search_term" 
                               placeholder="Tìm kiếm sản phẩm..." 
                               value="<?php echo htmlspecialchars($_GET['search_term'] ?? ''); ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search me-1"></i>Tìm kiếm
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-tag"></i>
                        </span>
                        <select class="form-select" name="category_id" onchange="this.form.submit()">
                            <option value="">Tất cả danh mục</option>
                            <?php while ($category = $categories->fetch_assoc()): ?>
                                <option value="<?php echo $category['CategoryID']; ?>" 
                                    <?php echo (isset($_GET['category_id']) && $_GET['category_id'] == $category['CategoryID']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['CategoryName']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <?php if (isset($_GET['page'])): ?>
                    <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>">
                <?php endif; ?>
                <div class="col-md-2">
                    <a href="product.php" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-1"></i>Xóa bộ lọc
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Products Display -->
    <div class="row g-4">
        <?php if ($products->num_rows > 0): ?>
            <?php while ($product = $products->fetch_assoc()): ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 product-card border-0 shadow-sm">
                        <div class="position-relative">
                            <img src="<?php echo getImageUrl($product['ImagePath']); ?>" 
                                 class="card-img-top product-image" 
                                 alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
                            <?php if ($product['StockQuantity'] <= 0): ?>
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-danger">Hết hàng</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-truncate" title="<?php echo htmlspecialchars($product['ProductName']); ?>">
                                <?php echo htmlspecialchars($product['ProductName']); ?>
                            </h5>
                            <p class="card-text text-danger fw-bold mb-2">
                                <?php echo number_format($product['Price'], 0, ',', '.'); ?>đ
                                <small class="text-muted fw-normal">/ <?php echo htmlspecialchars($product['Unit']); ?></small>
                            </p>
                            <p class="card-text mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-tag me-1"></i><?php echo htmlspecialchars($product['CategoryName']); ?>
                                </small>
                            </p>
                            <div class="mt-auto">
                                <a href="detail.php?id=<?php echo $product['ProductID']; ?>" 
                                   class="btn btn-outline-primary w-100">
                                    <i class="bi bi-eye me-1"></i>Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    <i class="bi bi-info-circle me-2"></i>
                    Không tìm thấy sản phẩm nào phù hợp với tiêu chí tìm kiếm.
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <nav aria-label="Page navigation" class="mt-5">
        <ul class="pagination justify-content-center">
            <?php if ($current_page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?><?php echo isset($_GET['search_term']) ? '&search_term=' . urlencode($_GET['search_term']) : ''; ?><?php echo isset($_GET['category_id']) ? '&category_id=' . $_GET['category_id'] : ''; ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            <?php endif; ?>
            
            <?php
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $start_page + 4);
            if ($end_page - $start_page < 4) {
                $start_page = max(1, $end_page - 4);
            }
            
            for ($i = $start_page; $i <= $end_page; $i++): 
            ?>
                <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo isset($_GET['search_term']) ? '&search_term=' . urlencode($_GET['search_term']) : ''; ?><?php echo isset($_GET['category_id']) ? '&category_id=' . $_GET['category_id'] : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php endfor; ?>
            
            <?php if ($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?><?php echo isset($_GET['search_term']) ? '&search_term=' . urlencode($_GET['search_term']) : ''; ?><?php echo isset($_GET['category_id']) ? '&category_id=' . $_GET['category_id'] : ''; ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<style>
.product-card {
    transition: all 0.3s ease;
    border-radius: 10px;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}

.product-image {
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.card-title {
    font-size: 1.1rem;
    line-height: 1.4;
    margin-bottom: 0.5rem;
}

.pagination .page-link {
    color: #0d6efd;
    border: none;
    margin: 0 2px;
    border-radius: 5px;
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    color: white;
}

.pagination .page-link:hover {
    background-color: #e9ecef;
    color: #0d6efd;
}

.input-group-text {
    border: none;
    background-color: transparent;
}

.form-control:focus, .form-select:focus {
    box-shadow: none;
    border-color: #0d6efd;
}
</style>

<?php
require_once '../../includes/footer.php';
$conn->close();
?> 