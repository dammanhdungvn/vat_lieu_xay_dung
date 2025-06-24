<?php
// Bắt đầu session
session_start();

// Thiết lập tiêu đề trang
$admin_page_title = 'Quản lý tài khoản Admin';

// Include auth check
require_once '../includes/auth_check.php';

// Kiểm tra xem người dùng có phải là SuperAdmin hay không
if (!isset($_SESSION['admin_role_name']) || $_SESSION['admin_role_name'] !== 'superadmin') {
    $_SESSION['admin_error_message'] = 'Bạn không có quyền truy cập trang này!';
    header('Location: ../dashboard.php');
    exit;
}

// Include database connection
require_once '../../config/db_connection.php';

// Truy vấn danh sách admin
$query = "SELECT a.AdminID, a.Username, a.FullName, a.Email, a.IsActive, r.RoleName
          FROM Admins a
          LEFT JOIN Roles r ON a.RoleID = r.RoleID
          ORDER BY a.AdminID";
$result = $conn->query($query);

// Include header và sidebar
include_once '../includes/header_admin.php';
include_once '../includes/sidebar_admin.php';
?>

<div class="container-fluid mt-4">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Danh sách tài khoản Admin</h5>
            <a href="add_admin.php" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Thêm Admin mới
            </a>
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
                            <th>Tên đăng nhập</th>
                            <th>Tên đầy đủ</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) { 
                                $isCurrentUser = (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $row['AdminID']);
                        ?>
                        <tr>
                            <td><?php echo $row['AdminID']; ?></td>
                            <td><?php echo htmlspecialchars($row['Username']); ?></td>
                            <td><?php echo htmlspecialchars($row['FullName'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['Email'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge <?php echo $row['RoleName'] == 'superadmin' ? 'bg-danger' : 'bg-primary'; ?>">
                                    <?php echo htmlspecialchars($row['RoleName']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if($row['IsActive'] == 1): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Bị khóa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="edit_admin.php?id=<?php echo $row['AdminID']; ?>" class="btn btn-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    
                                    <?php if (!$isCurrentUser): ?>
                                        <?php if($row['IsActive'] == 1): ?>
                                            <a href="toggle_admin_status.php?id=<?php echo $row['AdminID']; ?>&current_status=<?php echo $row['IsActive']; ?>" class="btn btn-warning" onclick="return confirm('Bạn có chắc chắn muốn khóa tài khoản admin này?')">
                                                <i class="bi bi-lock"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="toggle_admin_status.php?id=<?php echo $row['AdminID']; ?>&current_status=<?php echo $row['IsActive']; ?>" class="btn btn-success" onclick="return confirm('Bạn có chắc chắn muốn kích hoạt tài khoản admin này?')">
                                                <i class="bi bi-unlock"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else { 
                        ?>
                        <tr>
                            <td colspan="7" class="text-center">Không có tài khoản admin nào</td>
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