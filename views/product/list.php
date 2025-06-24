<?php
session_start();
$page_title = 'Danh sách Sản phẩm - VLXD Online';
require_once '../../config/db_connection.php';
require_once '../../utils/helpers.php';

// Get all categories for filter dropdown
$stmt = $conn->prepare("SELECT CategoryID, CategoryName FROM Categories ORDER BY CategoryName");
$stmt->execute();
$categories = $stmt->get_result();
$stmt->close();

// Build the base query
$sql = "SELECT p.ProductID, p.ProductName, p.ImagePath, p.Price, p.Unit, IFNULL(c.CategoryName, 'Chưa phân loại') AS CategoryName 
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
$items_per_page = 8;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Count total products for pagination
$count_sql = str_replace("SELECT p.ProductID, p.ProductName, p.ImagePath, p.Price, p.Unit, IFNULL(c.CategoryName, 'Chưa phân loại') AS CategoryName", "SELECT COUNT(*) AS total", $sql);
$stmt = $conn->prepare($count_sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total_result = $stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_products = $total_row['total'];
$total_pages = ceil($total_products / $items_per_page);
$stmt->close();

// Add limit and offset for pagination
$sql .= " LIMIT ? OFFSET ?";
$params[] = $items_per_page;
$params[] = $offset;
$types .= "ii";

// Execute the query with pagination
$stmt = $conn->prepare($sql);
if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

// Include the header
include_once '../../includes/header.php';

// Helper function to get proper image URL
function getImageUrl($path) {
    if (empty($path)) {
        return '../../assets/img/product-placeholder.png';
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
    <h2 class="mb-4">Danh sách Sản phẩm</h2>
    
    <!-- Search and Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search_term" placeholder="Tìm kiếm sản phẩm..." 
                               value="<?php echo htmlspecialchars($_GET['search_term'] ?? ''); ?>">
                        <button class="btn btn-outline-primary" type="submit">Tìm kiếm</button>
                    </div>
                </div>
                <div class="col-md-4">
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
                <?php if (isset($_GET['page'])): ?>
                    <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>">
                <?php endif; ?>
                <div class="col-md-2">
                    <a href="list.php" class="btn btn-outline-secondary w-100">Xóa bộ lọc</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Products Display -->
    <div class="row">
        <?php if ($products->num_rows > 0): ?>
            <?php while ($product = $products->fetch_assoc()): ?>
                <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 product-card">
                        <img src="<?php echo getImageUrl($product['ImagePath']); ?>" 
                             class="card-img-top product-image" alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['ProductName']); ?></h5>
                            <p class="card-text text-danger fw-bold"><?php echo format_price($product['Price']); ?> / <?php echo htmlspecialchars($product['Unit']); ?></p>
                            <p class="card-text"><small class="text-muted"><?php echo htmlspecialchars($product['CategoryName']); ?></small></p>
                            <div class="mt-auto">
                                <a href="detail.php?id=<?php echo $product['ProductID']; ?>" class="btn btn-outline-primary w-100">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p class="alert alert-info">Không tìm thấy sản phẩm nào.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($current_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $current_page - 1; 
                            echo isset($_GET['search_term']) ? '&search_term=' . urlencode($_GET['search_term']) : '';
                            echo isset($_GET['category_id']) ? '&category_id=' . urlencode($_GET['category_id']) : '';
                        ?>">Trước</a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i;
                            echo isset($_GET['search_term']) ? '&search_term=' . urlencode($_GET['search_term']) : '';
                            echo isset($_GET['category_id']) ? '&category_id=' . urlencode($_GET['category_id']) : '';
                        ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $current_page + 1;
                            echo isset($_GET['search_term']) ? '&search_term=' . urlencode($_GET['search_term']) : '';
                            echo isset($_GET['category_id']) ? '&category_id=' . urlencode($_GET['category_id']) : '';
                        ?>">Tiếp</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php
$stmt->close();
$conn->close();
include_once '../../includes/footer.php';
?> 