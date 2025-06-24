<?php
session_start();
$page_title = 'Đặt hàng - VLXD Online';
require_once '../../config/db_connection.php';
require_once '../../utils/helpers.php';

// Kiểm tra giỏ hàng
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['cart_message'] = 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm vào giỏ hàng trước khi đặt hàng.';
    header('Location: ../cart/view.php');
    exit();
}

// Lấy thông tin người dùng nếu đã đăng nhập
$customer_name = '';
$customer_phone = '';
$customer_address = '';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT FullName, Address FROM Users WHERE UserID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $customer_name = $user['FullName'];
        $customer_address = $user['Address'] ?? '';
    }
    
    $stmt->close();
}

// Tính tổng tiền
$total_amount = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

include_once '../../includes/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">Thông tin Đặt hàng</h2>
    
    <?php
    // Hiển thị thông báo lỗi nếu có
    if (isset($_SESSION['checkout_error'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['checkout_error'] . '</div>';
        unset($_SESSION['checkout_error']);
    }
    ?>
    
    <div class="row">
        <!-- Cột trái: Form thông tin giao hàng -->
        <div class="col-md-7">
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-4">Thông tin giao hàng</h4>
                    
                    <form method="POST" action="../../controllers/order/process_checkout.php">
                        <div class="mb-3">
                            <label for="customer_name" class="form-label">Họ tên người nhận <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($customer_name); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="customer_phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer_phone" name="customer_phone" value="<?php echo htmlspecialchars($customer_phone); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="customer_address" class="form-label">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="customer_address" name="customer_address" rows="3" required><?php echo htmlspecialchars($customer_address); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-4 p-3 bg-light rounded">
                            <strong>Phương thức thanh toán:</strong> Thanh toán khi nhận hàng (COD)
                        </div>
                        
                        <button type="submit" name="place_order_submit" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-bag-check me-2"></i>Xác nhận Đặt hàng
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Cột phải: Tóm tắt đơn hàng -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tóm tắt đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">SL</th>
                                    <th class="text-end">Giá</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['cart'] as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                                        <td class="text-end"><?php echo format_price($item['price'] * $item['quantity']); ?> vnđ</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Tổng tiền hàng:</th>
                                    <th class="text-end text-danger"><?php echo format_price($total_amount); ?> vnđ</th>
                                </tr>
                                <tr>
                                    <th colspan="2" class="text-end">Phí vận chuyển:</th>
                                    <th class="text-end">0 vnđ</th>
                                </tr>
                                <tr class="table-primary">
                                    <th colspan="2" class="text-end">Tổng thanh toán:</th>
                                    <th class="text-end text-danger fs-5"><?php echo format_price($total_amount); ?> vnđ</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <a href="../cart/view.php" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-left me-2"></i>Quay lại giỏ hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once '../../includes/footer.php';
?> 