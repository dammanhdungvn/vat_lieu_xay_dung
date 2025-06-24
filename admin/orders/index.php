<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Quản lý đơn hàng';

// Include auth check
require_once '../includes/auth_check.php';

// Include database connection
require_once '../../config/db_connection.php';

// Lọc theo trạng thái nếu có
$status_filter = '';
$filter_condition = '';
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    $status_filter = $status;
    $filter_condition = " WHERE o.Status = '$status'";
}

// Truy vấn đơn hàng với JOIN Users để lấy tên khách hàng
$query = "SELECT o.OrderID, o.OrderDate, o.TotalAmount, o.Status, 
                 u.FullName as CustomerName
          FROM Orders o 
          LEFT JOIN Users u ON o.UserID = u.UserID
          $filter_condition
          ORDER BY o.OrderDate DESC";
$result = $conn->query($query);

// Lấy danh sách các trạng thái đơn hàng hiện có
$status_query = "SELECT DISTINCT Status FROM Orders ORDER BY Status";
$status_result = $conn->query($status_query);
$statuses = [];
if ($status_result && $status_result->num_rows > 0) {
    while ($status_row = $status_result->fetch_assoc()) {
        $statuses[] = $status_row['Status'];
    }
}

// Include header và sidebar
include_once '../includes/header_admin.php';
include_once '../includes/sidebar_admin.php';
?>

<div class="container-fluid mt-4">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0 me-2"><i class="bi bi-cart me-2"></i>Danh sách đơn hàng</h5>
            
            <!-- Form lọc đơn hàng -->
            <form method="get" action="" class="d-flex align-items-center flex-wrap">
                <div class="input-group me-2 mb-2 mb-md-0" style="max-width: 300px;">
                    <label class="input-group-text" for="status">Trạng thái</label>
                    <select class="form-select" id="status" name="status" onchange="this.form.submit()">
                        <option value="">Tất cả</option>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?php echo htmlspecialchars($status); ?>" <?php echo ($status_filter == $status) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($status); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
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
            
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Tên khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) { 
                                // Xác định màu cho trạng thái
                                $status_class = '';
                                switch ($row['Status']) {
                                    case 'Mới': $status_class = 'badge bg-warning'; break;
                                    case 'Đang xử lý': $status_class = 'badge bg-info'; break;
                                    case 'Đang giao': $status_class = 'badge bg-primary'; break;
                                    case 'Hoàn thành': $status_class = 'badge bg-success'; break;
                                    case 'Đã hủy': $status_class = 'badge bg-danger'; break;
                                    default: $status_class = 'badge bg-secondary';
                                }
                        ?>
                        <tr>
                            <td>#<?php echo $row['OrderID']; ?></td>
                            <td><?php echo htmlspecialchars($row['CustomerName']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($row['OrderDate'])); ?></td>
                            <td><?php echo number_format($row['TotalAmount'], 0, ',', '.'); ?>đ</td>
                            <td>
                                <span class="<?php echo $status_class; ?>"><?php echo $row['Status']; ?></span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="view_order.php?id=<?php echo $row['OrderID']; ?>" class="btn btn-primary">
                                        <i class="bi bi-eye"></i> Xem chi tiết
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else { 
                        ?>
                        <tr>
                            <td colspan="6" class="text-center">Không có đơn hàng nào</td>
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
include_once '../includes/footer_admin.php';
$conn->close();
?> 