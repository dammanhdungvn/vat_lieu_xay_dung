<?php
// Start the session
session_start();

// Set page title - must be before header include
$admin_page_title = 'Bảng điều khiển';

// Include authentication check
require_once 'includes/auth_check.php';

// Include database connection
require_once '../config/db_connection.php';

// Get statistics
// Products count
$products_query = "SELECT COUNT(*) as total FROM Products";
$products_result = $conn->query($products_query);
$products_count = ($products_result && $products_result->num_rows > 0) 
    ? $products_result->fetch_assoc()['total'] 
    : 0;

// Orders count
$orders_query = "SELECT COUNT(*) as total FROM Orders";
$orders_result = $conn->query($orders_query);
$orders_count = ($orders_result && $orders_result->num_rows > 0) 
    ? $orders_result->fetch_assoc()['total'] 
    : 0;

// New orders count
$new_orders_query = "SELECT COUNT(*) as total FROM Orders WHERE Status = 'Mới'";
$new_orders_result = $conn->query($new_orders_query);
$new_orders_count = ($new_orders_result && $new_orders_result->num_rows > 0) 
    ? $new_orders_result->fetch_assoc()['total'] 
    : 0;

// Users count
$users_query = "SELECT COUNT(*) as total FROM Users";
$users_result = $conn->query($users_query);
$users_count = ($users_result && $users_result->num_rows > 0) 
    ? $users_result->fetch_assoc()['total'] 
    : 0;

// Include header and sidebar
include 'includes/header_admin.php';
include 'includes/sidebar_admin.php';
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h1 class="card-title">
                        <i class="bi bi-speedometer2 me-2"></i>Bảng điều khiển
                    </h1>
                    <h5 class="text-muted">
                        Chào mừng trở lại, <?php echo htmlspecialchars($_SESSION['admin_fullname'] ?? $_SESSION['admin_username']); ?>!
                    </h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Sản phẩm</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $products_count; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-box fs-2 text-primary"></i>
                        </div>
                    </div>
                </div>
                                <div class="card-footer bg-light">                    <a href="products/index.php" class="text-decoration-none">Quản lý sản phẩm <i class="bi bi-arrow-right"></i></a>                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Đơn hàng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $orders_count; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-bag-check fs-2 text-success"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <a href="orders/index.php" class="text-decoration-none">Quản lý đơn hàng <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- New Orders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Đơn hàng mới</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $new_orders_count; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-bell fs-2 text-warning"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <a href="orders/index.php?status=Mới" class="text-decoration-none">Xem đơn hàng mới <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Khách hàng</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $users_count; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fs-2 text-info"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <a href="users_management/index.php" class="text-decoration-none">Quản lý khách hàng <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Truy cập nhanh</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="products/add.php" class="btn btn-primary w-100">                                <i class="bi bi-plus-circle me-2"></i>Thêm sản phẩm mới                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="categories_management/index.php" class="btn btn-secondary w-100">                                <i class="bi bi-tags me-2"></i>Quản lý danh mục                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="inventory.php" class="btn btn-success w-100">
                                <i class="bi bi-clipboard-check me-2"></i>Kiểm tra kho
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="settings.php" class="btn btn-dark w-100">
                                <i class="bi bi-gear me-2"></i>Cài đặt hệ thống
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Đơn hàng gần đây</h5>
                    <a href="orders/index.php" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get 5 most recent orders
                                $recent_orders_query = "SELECT o.OrderID, o.OrderDate, o.TotalAmount, o.Status, 
                                                          u.FullName as CustomerName 
                                                       FROM Orders o 
                                                       JOIN Users u ON o.UserID = u.UserID 
                                                       ORDER BY o.OrderDate DESC 
                                                       LIMIT 5";
                                $recent_orders_result = $conn->query($recent_orders_query);
                                
                                if ($recent_orders_result && $recent_orders_result->num_rows > 0) {
                                    while ($order = $recent_orders_result->fetch_assoc()) {
                                        $status_class = '';
                                        switch ($order['Status']) {
                                            case 'Mới': $status_class = 'badge bg-warning'; break;
                                            case 'Đang xử lý': $status_class = 'badge bg-info'; break;
                                            case 'Đang giao': $status_class = 'badge bg-primary'; break;
                                            case 'Hoàn thành': $status_class = 'badge bg-success'; break;
                                            case 'Hủy': $status_class = 'badge bg-danger'; break;
                                            default: $status_class = 'badge bg-secondary';
                                        }
                                        ?>
                                        <tr>
                                            <td>#<?php echo $order['OrderID']; ?></td>
                                            <td><?php echo htmlspecialchars($order['CustomerName']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($order['OrderDate'])); ?></td>
                                            <td><?php echo number_format($order['TotalAmount'], 0, ',', '.'); ?>đ</td>
                                            <td><span class="<?php echo $status_class; ?>"><?php echo $order['Status']; ?></span></td>
                                            <td>
                                                <a href="orders/view_order.php?id=<?php echo $order['OrderID']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="6" class="text-center">Không có đơn hàng nào</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include 'includes/footer_admin.php';
?> 