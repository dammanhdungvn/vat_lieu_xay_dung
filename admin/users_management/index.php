<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Quản lý người dùng';

// Include auth check
require_once '../includes/auth_check.php';

// Include database connection
require_once '../../config/db_connection.php';

// Truy vấn danh sách người dùng
$query = "SELECT UserID, FullName, Email, Address, CreatedAt, IsActive 
          FROM Users 
          ORDER BY CreatedAt DESC";
$result = $conn->query($query);

// Include header và sidebar
include_once '../includes/header_admin.php';
include_once '../includes/sidebar_admin.php';
?>

<div class="container-fluid mt-4">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-people me-2"></i>Danh sách người dùng</h5>
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
                            <th>ID</th>
                            <th>Tên đầy đủ</th>
                            <th>Email</th>
                            <th>Địa chỉ</th>
                            <th>Ngày tạo</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) { 
                        ?>
                        <tr>
                            <td><?php echo $row['UserID']; ?></td>
                            <td><?php echo htmlspecialchars($row['FullName']); ?></td>
                            <td><?php echo htmlspecialchars($row['Email']); ?></td>
                            <td><?php echo htmlspecialchars(substr($row['Address'] ?? 'N/A', 0, 30)) . (strlen($row['Address'] ?? '') > 30 ? '...' : ''); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['CreatedAt'])); ?></td>
                            <td>
                                <?php if($row['IsActive'] == 1): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Bị khóa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if($row['IsActive'] == 1): ?>
                                        <a href="toggle_user_status.php?id=<?php echo $row['UserID']; ?>&current_status=<?php echo $row['IsActive']; ?>" class="btn btn-warning" onclick="return confirm('Bạn có chắc chắn muốn khóa người dùng này?')">
                                            <i class="bi bi-lock"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="toggle_user_status.php?id=<?php echo $row['UserID']; ?>&current_status=<?php echo $row['IsActive']; ?>" class="btn btn-success" onclick="return confirm('Bạn có chắc chắn muốn kích hoạt người dùng này?')">
                                            <i class="bi bi-unlock"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else { 
                        ?>
                        <tr>
                            <td colspan="7" class="text-center">Không có người dùng nào</td>
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