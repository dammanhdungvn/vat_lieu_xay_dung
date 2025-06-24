<?php
session_start();
$page_title = 'Chi tiết Sản phẩm - VLXD Online';
require_once '../../config/db_connection.php';
require_once '../../utils/helpers.php';

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || $_GET['id'] <= 0) {
    $_SESSION['error_message'] = 'ID sản phẩm không hợp lệ.';
    header('Location: list.php');
    exit();
}

$product_id = (int)$_GET['id'];

// Get product details
$sql = "SELECT p.ProductID, p.ProductName, p.Description, p.ImagePath, p.Price, p.Unit, p.StockQuantity, 
               IFNULL(c.CategoryName, 'Chưa phân loại') AS CategoryName
        FROM Products p 
        LEFT JOIN Categories c ON p.CategoryID = c.CategoryID
        WHERE p.ProductID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if product exists
if ($result->num_rows === 0) {
    $_SESSION['error_message'] = 'Sản phẩm không tồn tại.';
    header('Location: list.php');
    exit();
}

// Get product data
$product = $result->fetch_assoc();
$stmt->close();

// Update page title
$page_title = $product['ProductName'] . ' - VLXD Online';

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

<div class="container mt-5">
    <!-- Product Information -->
    <div class="row">
        <!-- Product Image -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <img src="<?php echo getImageUrl($product['ImagePath']); ?>" 
                     class="card-img-top img-fluid" alt="<?php echo htmlspecialchars($product['ProductName']); ?>">
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="col-md-6">
            <h2 class="mb-3"><?php echo htmlspecialchars($product['ProductName']); ?></h2>
            <h4 class="text-danger fw-bold mb-3"><?php echo format_price($product['Price']); ?> / <?php echo htmlspecialchars($product['Unit']); ?></h4>
            
            <div class="mb-3">
                <p><strong>Danh mục:</strong> <?php echo htmlspecialchars($product['CategoryName']); ?></p>
                
                <?php if ($product['StockQuantity'] > 0): ?>
                    <p class="text-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Còn hàng</strong> (<?php echo $product['StockQuantity']; ?> <?php echo htmlspecialchars($product['Unit']); ?>)
                    </p>
                <?php else: ?>
                    <p class="text-danger">
                        <i class="bi bi-x-circle-fill me-2"></i>
                        <strong>Hết hàng</strong>
                    </p>
                <?php endif; ?>
            </div>
            
            <!-- Add to Cart Form -->
            <form method="POST" action="../../controllers/cart/add.php" class="mb-3">
                <input type="hidden" name="product_id" value="<?php echo $product['ProductID']; ?>">
                
                <div class="mb-3">
                    <label for="quantity" class="form-label">Số lượng:</label>
                    <div class="input-group">
                        <button type="button" class="btn btn-outline-secondary" id="decrease-quantity" <?php echo $product['StockQuantity'] <= 0 ? 'disabled' : ''; ?>>
                            <i class="bi bi-dash"></i>
                        </button>
                        <input type="number" id="quantity" name="quantity" class="form-control text-center" value="1" 
                               min="1" <?php echo $product['StockQuantity'] > 0 ? 'max="' . $product['StockQuantity'] . '"' : ''; ?> 
                               <?php echo $product['StockQuantity'] <= 0 ? 'disabled' : ''; ?>>
                        <button type="button" class="btn btn-outline-secondary" id="increase-quantity" <?php echo $product['StockQuantity'] <= 0 ? 'disabled' : ''; ?>>
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-success btn-lg w-100" <?php echo $product['StockQuantity'] <= 0 ? 'disabled' : ''; ?>>
                    <i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ hàng
                </button>
            </form>
            
            <!-- Share Buttons (Optional) -->
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary">
                    <i class="bi bi-facebook me-1"></i>Chia sẻ
                </button>
                <button class="btn btn-outline-info">
                    <i class="bi bi-twitter me-1"></i>Tweet
                </button>
            </div>
        </div>
    </div>
    
    <!-- Product Description -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Mô tả sản phẩm</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($product['Description'])): ?>
                        <div class="product-description">
                            <?php echo nl2br(htmlspecialchars($product['Description'])); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Chưa có mô tả cho sản phẩm này.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const decreaseBtn = document.getElementById('decrease-quantity');
    const increaseBtn = document.getElementById('increase-quantity');
    const maxStock = <?php echo $product['StockQuantity']; ?>;
    
    // Function to update quantity
    function updateQuantity(newValue) {
        // Ensure quantity is not less than 1
        if (newValue < 1) newValue = 1;
        // Ensure quantity doesn't exceed stock
        if (newValue > maxStock) newValue = maxStock;
        
        quantityInput.value = newValue;
    }
    
    // Decrease quantity button
    decreaseBtn.addEventListener('click', function() {
        const currentValue = parseInt(quantityInput.value) || 1;
        updateQuantity(currentValue - 1);
    });
    
    // Increase quantity button
    increaseBtn.addEventListener('click', function() {
        const currentValue = parseInt(quantityInput.value) || 1;
        updateQuantity(currentValue + 1);
    });
    
    // Validate when user manually inputs a value
    quantityInput.addEventListener('change', function() {
        const currentValue = parseInt(quantityInput.value) || 1;
        updateQuantity(currentValue);
    });
});
</script>

<?php
$conn->close();
include_once '../../includes/footer.php';
?> 