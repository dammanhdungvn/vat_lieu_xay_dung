<?php
session_start();
$page_title = 'Chi tiết đơn hàng - VLXD Online';
require_once '../../config/db_connection.php';
require_once '../../utils/helpers.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Lấy UserID từ session
$user_id = $_SESSION['user_id'];

// Kiểm tra ID đơn hàng
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = 'ID đơn hàng không hợp lệ.';
    header('Location: my_account.php?page=order_history');
    exit();
}

$order_id = (int)$_GET['id'];

// Lấy thông tin đơn hàng và kiểm tra quyền truy cập
$stmt = $conn->prepare("SELECT o.*, DATE_FORMAT(o.OrderDate, '%d/%m/%Y %H:%i') AS FormattedDate 
                        FROM Orders o 
                        WHERE o.OrderID = ? AND o.UserID = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra nếu đơn hàng không tồn tại hoặc không thuộc về người dùng này
if ($result->num_rows === 0) {
    $_SESSION['error_message'] = 'Đơn hàng không tồn tại hoặc bạn không có quyền truy cập.';
    header('Location: my_account.php?page=order_history');
    exit();
}

$order = $result->fetch_assoc();
$stmt->close();

// Lấy chi tiết đơn hàng
$stmt = $conn->prepare("SELECT oi.*, p.ProductName, p.Unit, p.ImagePath 
                        FROM OrderItems oi 
                        JOIN Products p ON oi.ProductID = p.ProductID 
                        WHERE oi.OrderID = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result();
$stmt->close();

include_once '../../includes/header.php';
?>

<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Chi tiết đơn hàng #<?php echo $order_id; ?></h2>
        <a href="my_account.php?page=order_history" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i>Quay lại danh sách đơn hàng
        </a>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Thông tin đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($order['CustomerName']); ?></p>
                            <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($order['CustomerPhone']); ?></p>
                            <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['CustomerAddress']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Ngày đặt:</strong> <?php echo $order['FormattedDate']; ?></p>
                            <p>
                                <strong>Trạng thái:</strong> 
                                <span class="badge <?php echo getStatusBadgeClass($order['Status']); ?>">
                                    <?php echo htmlspecialchars($order['Status']); ?>
                                </span>
                            </p>
                            <p><strong>Phương thức thanh toán:</strong> Thanh toán khi nhận hàng (COD)</p>
                        </div>
                    </div>
                    
                    <?php if (!empty($order['Notes'])): ?>
                    <div class="mb-3">
                        <p><strong>Ghi chú:</strong> <?php echo nl2br(htmlspecialchars($order['Notes'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Danh sách sản phẩm</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Hình ảnh</th>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Đơn giá</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($item = $order_items->fetch_assoc()): 
                                    $subtotal = $item['PriceAtOrder'] * $item['Quantity'];
                                ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo getImagePath($item['ImagePath']); ?>" class="img-thumbnail" 
                                             style="max-height: 50px;" alt="<?php echo htmlspecialchars($item['ProductName']); ?>">
                                    </td>
                                    <td>
                                        <a href="../../views/product/detail.php?id=<?php echo $item['ProductID']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($item['ProductName']); ?>
                                        </a>
                                    </td>
                                    <td class="text-center"><?php echo format_price($item['PriceAtOrder']); ?> vnđ</td>
                                    <td class="text-center">
                                        <?php echo $item['Quantity']; ?> <?php echo htmlspecialchars($item['Unit'] ?? 'Sản phẩm'); ?>
                                    </td>
                                    <td class="text-end"><?php echo format_price($subtotal); ?> vnđ</td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Tổng tiền hàng:</th>
                                    <th class="text-end text-danger"><?php echo format_price($order['TotalAmount']); ?> vnđ</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Trạng thái đơn hàng</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Đơn hàng đã đặt
                            <span class="badge bg-success rounded-pill"><i class="bi bi-check-lg"></i></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Đang xử lý
                            <span class="badge <?php echo $order['Status'] != 'Mới' ? 'bg-success' : 'bg-secondary'; ?> rounded-pill">
                                <?php echo $order['Status'] != 'Mới' ? '<i class="bi bi-check-lg"></i>' : ''; ?>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Đang giao hàng
                            <span class="badge <?php echo in_array($order['Status'], ['Đang giao hàng', 'Đã giao hàng']) ? 'bg-success' : 'bg-secondary'; ?> rounded-pill">
                                <?php echo in_array($order['Status'], ['Đang giao hàng', 'Đã giao hàng']) ? '<i class="bi bi-check-lg"></i>' : ''; ?>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Hoàn thành
                            <span class="badge <?php echo $order['Status'] == 'Đã giao hàng' ? 'bg-success' : 'bg-secondary'; ?> rounded-pill">
                                <?php echo $order['Status'] == 'Đã giao hàng' ? '<i class="bi bi-check-lg"></i>' : ''; ?>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Tổng kết đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tổng tiền hàng:</span>
                        <span><?php echo format_price($order['TotalAmount']); ?> vnđ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển:</span>
                        <span>0 vnđ</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Tổng thanh toán:</span>
                        <span class="text-danger"><?php echo format_price($order['TotalAmount']); ?> vnđ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Helper function for image path
function getImagePath($path) {
    if (empty($path)) {
        return '../../assets/img/product-placeholder.png';
    }
    
    if (preg_match('/^https?:\/\//', $path)) {
        return $path;
    }
    
    $path = ltrim($path, '/');
    
    if (strpos($path, 'assets/') === 0) {
        return '../../' . $path;
    }
    
    return '../../' . $path;
}

// Helper function to get badge class based on order status
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'Mới':
            return 'bg-info';
        case 'Đang xử lý':
            return 'bg-primary';
        case 'Đang giao hàng':
            return 'bg-warning';
        case 'Đã giao hàng':
            return 'bg-success';
        case 'Đã hủy':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

include_once '../../includes/footer.php';
?> 