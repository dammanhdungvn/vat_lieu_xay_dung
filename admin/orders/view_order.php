<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Chi tiết đơn hàng';

// Include auth check
require_once '../includes/auth_check.php';

// Include database connection
require_once '../../config/db_connection.php';

// Kiểm tra ID đơn hàng từ URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['admin_error_message'] = 'ID đơn hàng không hợp lệ!';
    header("Location: index.php");
    exit;
}

$orderId = intval($_GET['id']);

// Truy vấn thông tin đơn hàng
$orderQuery = "SELECT o.*, u.FullName as CustomerName, u.Email, u.PhoneNumber, o.CustomerAddress 
               FROM Orders o 
               LEFT JOIN Users u ON o.UserID = u.UserID 
               WHERE o.OrderID = $orderId";
$orderResult = $conn->query($orderQuery);

// Kiểm tra đơn hàng tồn tại
if (!$orderResult || $orderResult->num_rows === 0) {
    $_SESSION['admin_error_message'] = "Không tìm thấy đơn hàng với ID: $orderId";
    header("Location: index.php");
    exit;
}

$orderInfo = $orderResult->fetch_assoc();

// Truy vấn chi tiết sản phẩm trong đơn hàng
$orderItemsQuery = "SELECT oi.*, p.ProductName, p.ImagePath 
                    FROM OrderItems oi 
                    LEFT JOIN Products p ON oi.ProductID = p.ProductID 
                    WHERE oi.OrderID = $orderId";
$orderItemsResult = $conn->query($orderItemsQuery);

// Include header và sidebar
include_once '../includes/header_admin.php';
include_once '../includes/sidebar_admin.php';
?>

<div class="container-fluid mt-4">
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
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Thông tin đơn hàng #<?php echo $orderId; ?></h5>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Quay lại danh sách
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Thông tin khách hàng</h6>
                            <hr>
                            <p><strong>Tên khách hàng:</strong> <?php echo htmlspecialchars($orderInfo['CustomerName'] ?? 'N/A'); ?></p>
                            <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($orderInfo['PhoneNumber'] ?? 'N/A'); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($orderInfo['Email'] ?? 'N/A'); ?></p>
                            <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($orderInfo['CustomerAddress'] ?? 'N/A'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Thông tin đơn hàng</h6>
                            <hr>
                            <p><strong>Mã đơn hàng:</strong> #<?php echo $orderId; ?></p>
                            <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($orderInfo['OrderDate'])); ?></p>
                            <p><strong>Tổng tiền:</strong> <?php echo number_format($orderInfo['TotalAmount'], 0, ',', '.'); ?>đ</p>
                            <p>
                                <strong>Trạng thái:</strong> 
                                <?php 
                                $status_class = '';
                                switch ($orderInfo['Status']) {
                                    case 'Mới': $status_class = 'badge bg-warning'; break;
                                    case 'Đang xử lý': $status_class = 'badge bg-info'; break;
                                    case 'Đang giao': $status_class = 'badge bg-primary'; break;
                                    case 'Hoàn thành': $status_class = 'badge bg-success'; break;
                                    case 'Đã hủy': $status_class = 'badge bg-danger'; break;
                                    default: $status_class = 'badge bg-secondary';
                                }
                                ?>
                                <span class="<?php echo $status_class; ?>"><?php echo $orderInfo['Status']; ?></span>
                            </p>
                            <?php if (!empty($orderInfo['Notes'])): ?>
                            <p><strong>Ghi chú:</strong> <?php echo nl2br(htmlspecialchars($orderInfo['Notes'])); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-box me-2"></i>Sản phẩm đã đặt</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($orderItemsResult && $orderItemsResult->num_rows > 0) {
                                    $totalAmount = 0;
                                    while ($item = $orderItemsResult->fetch_assoc()) {
                                        $subtotal = $item['Quantity'] * $item['PriceAtOrder'];
                                        $totalAmount += $subtotal;
                                        
                                        // Xác định đường dẫn ảnh
                                        $imagePath = !empty($item['ImagePath']) ? $item['ImagePath'] : 'assets/img/no-image.jpg';
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($item['ProductName']); ?>" 
                                             class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    </td>
                                    <td><?php echo htmlspecialchars($item['ProductName']); ?></td>
                                    <td><?php echo number_format($item['PriceAtOrder'], 0, ',', '.'); ?>đ</td>
                                    <td><?php echo $item['Quantity']; ?></td>
                                    <td><?php echo number_format($item['PriceAtOrder'] * $item['Quantity'], 0, ',', '.'); ?>đ</td>
                                </tr>
                                <?php
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center">Không có sản phẩm nào trong đơn hàng này</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end fw-bold">Tổng cộng:</td>
                                    <td class="fw-bold"><?php echo number_format($orderInfo['TotalAmount'], 0, ',', '.'); ?>đ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Cập nhật trạng thái đơn hàng</h5>
                </div>
                <div class="card-body">
                    <form action="update_order_status.php" method="post">
                        <input type="hidden" name="orderid" value="<?php echo $orderId; ?>">
                        
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Mới" <?php echo ($orderInfo['Status'] == 'Mới') ? 'selected' : ''; ?>>Mới</option>
                                <option value="Đang xử lý" <?php echo ($orderInfo['Status'] == 'Đang xử lý') ? 'selected' : ''; ?>>Đang xử lý</option>
                                <option value="Đang giao" <?php echo ($orderInfo['Status'] == 'Đang giao') ? 'selected' : ''; ?>>Đang giao</option>
                                <option value="Hoàn thành" <?php echo ($orderInfo['Status'] == 'Hoàn thành') ? 'selected' : ''; ?>>Hoàn thành</option>
                                <option value="Đã hủy" <?php echo ($orderInfo['Status'] == 'Đã hủy') ? 'selected' : ''; ?>>Đã hủy</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Cập nhật trạng thái
                        </button>
                    </form>
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