<?php
session_start();
$page_title = 'Giỏ hàng - VLXD Online';
require_once '../../config/db_connection.php';
require_once '../../utils/helpers.php';

// Kiểm tra và cập nhật đơn vị cho các sản phẩm trong giỏ hàng
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $updated = false;
    
    foreach ($_SESSION['cart'] as $key => $item) {
        if (!isset($item['unit']) || empty($item['unit'])) {
            // Lấy đơn vị từ database
            $sql = "SELECT Unit FROM Products WHERE ProductID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $item['product_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();
                $_SESSION['cart'][$key]['unit'] = $product['Unit'];
                $updated = true;
            }
            
            $stmt->close();
        }
    }
}

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
    <h2 class="mb-4">Giỏ hàng của bạn</h2>
    
    <?php
    // Display cart message if it exists
    if (isset($_SESSION['cart_message'])) {
        echo '<div class="alert alert-info">' . $_SESSION['cart_message'] . '</div>';
        unset($_SESSION['cart_message']);
    }
    ?>
    
    <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
        <div class="alert alert-info">
            <p class="mb-0">Giỏ hàng của bạn đang trống.</p>
        </div>
        <a href="../../views/product/list.php" class="btn btn-primary">Tiếp tục mua sắm</a>
    <?php else: ?>
        <form method="POST" action="../../controllers/cart/process.php?action=update_multiple">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">Hình ảnh</th>
                                    <th>Sản phẩm</th>
                                    <th style="width: 120px;" class="text-center">Đơn giá</th>
                                    <th style="width: 150px;" class="text-center">Số lượng</th>
                                    <th style="width: 150px;" class="text-end">Thành tiền</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total = 0;
                                foreach($_SESSION['cart'] as $index => $item): 
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $total += $subtotal;
                                ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo getImageUrl($item['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-thumbnail" style="max-height: 70px;">
                                        </td>
                                        <td>
                                            <a href="../../views/product/detail.php?id=<?php echo $item['product_id']; ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($item['name']); ?>
                                            </a>
                                            <div class="small text-muted">Đơn vị: <?php echo isset($item['unit']) ? htmlspecialchars($item['unit']) : 'Sản phẩm'; ?></div>
                                        </td>
                                        <td class="text-center"><?php echo format_price($item['price']); ?> </td>
                                        <td class="text-center">
                                            <input type="number" name="quantity[<?php echo $item['product_id']; ?>]" 
                                                   value="<?php echo $item['quantity']; ?>" min="1" 
                                                   class="form-control form-control-sm" style="width: 70px; margin: 0 auto;">
                                        </td>
                                        <td class="text-end fw-bold"><?php echo format_price($subtotal); ?> </td>
                                        <td>
                                            <a href="../../controllers/cart/process.php?action=remove&product_id=<?php echo $item['product_id']; ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Tổng cộng:</td>
                                    <td class="text-end text-danger fw-bold fs-5"><?php echo format_price($total); ?> </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-3">
                        <a href="../../views/product/list.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-2"></i>Tiếp tục mua sắm
                        </a>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-info">
                                <i class="bi bi-arrow-repeat me-2"></i>Cập nhật giỏ hàng
                            </button>
                            <a href="../../controllers/cart/process.php?action=clear" class="btn btn-outline-danger" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm trong giỏ hàng?')">
                                <i class="bi bi-trash me-2"></i>Xóa giỏ hàng
                            </a>
                            <a href="../../views/order/checkout.php" class="btn btn-success">
                                <i class="bi bi-bag-check me-2"></i>Tiến hành đặt hàng
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php
include_once '../../includes/footer.php';
?> 