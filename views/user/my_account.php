<?php
session_start();
$page_title = 'Tài khoản của tôi - VLXD Online';
require_once '../../config/db_connection.php';
require_once '../../utils/helpers.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

// Lấy UserID từ session
$user_id = $_SESSION['user_id'];

// Xác định trang hiện tại (mặc định là profile)
$current_page = isset($_GET['page']) ? $_GET['page'] : 'profile';

// Tiêu đề trang dựa trên section
$section_titles = [
    'profile' => 'Thông tin tài khoản',
    'order_history' => 'Lịch sử đơn hàng',
    'change_password' => 'Đổi mật khẩu'
];

// Hiển thị header
include_once '../../includes/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4">Tài khoản của tôi</h2>
    
    <?php
    // Hiển thị thông báo nếu có
    if (isset($_SESSION['account_message'])) {
        $message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'info';
        echo '<div class="alert alert-' . $message_type . '">' . $_SESSION['account_message'] . '</div>';
        unset($_SESSION['account_message']);
        unset($_SESSION['message_type']);
    }
    ?>
    
    <div class="row">
        <!-- Sidebar/Menu -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Menu tài khoản</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="my_account.php?page=profile" 
                       class="list-group-item list-group-item-action <?php echo $current_page == 'profile' ? 'active' : ''; ?>">
                       <i class="bi bi-person me-2"></i>Thông tin tài khoản
                    </a>
                    <a href="my_account.php?page=order_history" 
                       class="list-group-item list-group-item-action <?php echo $current_page == 'order_history' ? 'active' : ''; ?>">
                       <i class="bi bi-clock-history me-2"></i>Lịch sử đơn hàng
                    </a>
                    <a href="my_account.php?page=change_password" 
                       class="list-group-item list-group-item-action <?php echo $current_page == 'change_password' ? 'active' : ''; ?>">
                       <i class="bi bi-key me-2"></i>Đổi mật khẩu
                    </a>
                    <a href="../../logout.php" class="list-group-item list-group-item-action text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Nội dung chính -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><?php echo $section_titles[$current_page] ?? 'Tài khoản'; ?></h5>
                </div>
                <div class="card-body">
                    <?php
                    // Hiển thị nội dung tương ứng với trang được chọn
                    switch ($current_page) {
                        case 'profile':
                            // Lấy thông tin người dùng
                            $stmt = $conn->prepare("SELECT FullName, Email, Address FROM Users WHERE UserID = ?");
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $user = $result->fetch_assoc();
                            $stmt->close();
                            ?>
                            <div class="row">
                                <div class="col-md-8">
                                    <table class="table table-striped">
                                        <tr>
                                            <th style="width: 30%">Họ và tên:</th>
                                            <td><?php echo htmlspecialchars($user['FullName']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td><?php echo htmlspecialchars($user['Email']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Địa chỉ:</th>
                                            <td><?php echo !empty($user['Address']) ? htmlspecialchars($user['Address']) : '<em>Chưa cập nhật</em>'; ?></td>
                                        </tr>
                                    </table>
                                    <a href="edit_profile.php" class="btn btn-primary">
                                        <i class="bi bi-pencil-square me-2"></i>Chỉnh sửa thông tin
                                    </a>
                                </div>
                            </div>
                            <?php
                            break;
                        
                        case 'order_history':
                            // Lấy lịch sử đơn hàng
                            $stmt = $conn->prepare("SELECT OrderID, DATE_FORMAT(OrderDate, '%d/%m/%Y %H:%i') AS FormattedDate, 
                                                  TotalAmount, Status FROM Orders WHERE UserID = ? ORDER BY OrderDate DESC");
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $orders = $stmt->get_result();
                            $stmt->close();
                            
                            if ($orders->num_rows > 0):
                            ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Mã đơn hàng</th>
                                                <th>Ngày đặt</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($order = $orders->fetch_assoc()): ?>
                                                <tr>
                                                    <td>#<?php echo $order['OrderID']; ?></td>
                                                    <td><?php echo $order['FormattedDate']; ?></td>
                                                    <td><?php echo format_price($order['TotalAmount']); ?> vnđ</td>
                                                    <td>
                                                        <span class="badge <?php echo getStatusBadgeClass($order['Status']); ?>">
                                                            <?php echo htmlspecialchars($order['Status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="order_detail.php?id=<?php echo $order['OrderID']; ?>" class="btn btn-sm btn-outline-primary">
                                                            Chi tiết
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <p class="mb-0">Bạn chưa có đơn hàng nào.</p>
                                </div>
                                <a href="../../views/product/list.php" class="btn btn-primary">Mua sắm ngay</a>
                            <?php endif;
                            break;
                        
                        case 'change_password':
                            ?>
                            <form action="../../controllers/user/process_change_password.php" method="POST" class="col-md-8">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Mật khẩu mới <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    <div class="form-text">Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Nhập lại mật khẩu mới <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Đổi mật khẩu
                                </button>
                            </form>
                            <?php
                            break;
                        
                        default:
                            echo '<div class="alert alert-warning">Trang không tồn tại.</div>';
                            break;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
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