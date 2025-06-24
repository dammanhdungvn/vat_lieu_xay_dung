<?php
session_start();
require_once '../config/db_connection.php';

// Flag để kiểm tra nếu đã có admin nào chưa
$has_admins = false;
$success_message = '';
$error_message = '';

// Kiểm tra bảng Roles đã tồn tại chưa
$check_roles_table = "SHOW TABLES LIKE 'Roles'";
$roles_exists = $conn->query($check_roles_table)->num_rows > 0;

// Kiểm tra bảng Admins đã tồn tại chưa
$check_admins_table = "SHOW TABLES LIKE 'Admins'";
$admins_exists = $conn->query($check_admins_table)->num_rows > 0;

// Tạo bảng Roles nếu chưa có
if (!$roles_exists) {
    $create_roles_table = "CREATE TABLE Roles (
        RoleID INT AUTO_INCREMENT PRIMARY KEY,
        RoleName VARCHAR(50) NOT NULL UNIQUE,
        Description VARCHAR(255),
        CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($create_roles_table) === TRUE) {
        // Thêm các vai trò mặc định
        $insert_roles = "INSERT INTO Roles (RoleName, Description) VALUES 
            ('superadmin', 'Quản trị viên cao cấp với tất cả quyền'),
            ('admin', 'Quản trị viên thông thường'),
            ('manager', 'Quản lý nội dung và đơn hàng')";
        $conn->query($insert_roles);
    } else {
        $error_message = "Lỗi tạo bảng Roles: " . $conn->error;
    }
}

// Tạo bảng Admins nếu chưa có
if (!$admins_exists) {
    $create_admins_table = "CREATE TABLE Admins (
        AdminID INT AUTO_INCREMENT PRIMARY KEY,
        Username VARCHAR(50) NOT NULL UNIQUE,
        PasswordHash VARCHAR(255) NOT NULL,
        FullName VARCHAR(100) NOT NULL,
        Email VARCHAR(100) NOT NULL UNIQUE,
        RoleID INT NOT NULL,
        IsActive BOOLEAN DEFAULT TRUE,
        LastLogin TIMESTAMP NULL,
        CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (RoleID) REFERENCES Roles(RoleID)
    )";
    
    if ($conn->query($create_admins_table) !== TRUE) {
        $error_message = "Lỗi tạo bảng Admins: " . $conn->error;
    }
}

// Kiểm tra xem đã có admin nào chưa
if ($admins_exists) {
    $check_admins = "SELECT COUNT(*) as total FROM Admins";
    $result = $conn->query($check_admins);
    if ($result && $row = $result->fetch_assoc()) {
        $has_admins = $row['total'] > 0;
    }
}

// Xử lý form khi submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username'] ?? '');
    $plainPassword = $_POST['password'] ?? '';
    $fullname = $conn->real_escape_string($_POST['fullname'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $role_id = (int)($_POST['role_id'] ?? 1); // Mặc định là 1 (superadmin) nếu không chọn
    
    // Validate dữ liệu nhập vào
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Tên đăng nhập không được để trống";
    }
    
    if (empty($plainPassword)) {
        $errors[] = "Mật khẩu không được để trống";
    } elseif (strlen($plainPassword) < 6) {
        $errors[] = "Mật khẩu phải có ít nhất 6 ký tự";
    }
    
    if (empty($fullname)) {
        $errors[] = "Họ tên không được để trống";
    }
    
    if (empty($email)) {
        $errors[] = "Email không được để trống";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }
    
    // Kiểm tra username và email đã tồn tại chưa
    $check_duplicate = "SELECT Username, Email FROM Admins WHERE Username = ? OR Email = ?";
    $stmt = $conn->prepare($check_duplicate);
    $stmt->bind_param('ss', $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row['Username'] === $username) {
                $errors[] = "Tên đăng nhập $username đã tồn tại";
            }
            if ($row['Email'] === $email) {
                $errors[] = "Email $email đã tồn tại";
            }
        }
    }
    
    // Nếu không có lỗi, tiến hành tạo tài khoản
    if (empty($errors)) {
        // Băm mật khẩu
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        
        // Thêm admin vào database
        $insert_admin = "INSERT INTO Admins (Username, PasswordHash, FullName, Email, RoleID, IsActive) 
                         VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $conn->prepare($insert_admin);
        $stmt->bind_param('ssssi', $username, $hashedPassword, $fullname, $email, $role_id);
        
        if ($stmt->execute()) {
            $success_message = "Đã tạo tài khoản admin $username thành công!";
            // Cập nhật trạng thái đã có admin
            $has_admins = true;
        } else {
            $error_message = "Lỗi tạo tài khoản admin: " . $stmt->error;
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}

// Lấy danh sách roles
$roles = [];
if ($roles_exists) {
    $get_roles = "SELECT RoleID, RoleName, Description FROM Roles ORDER BY RoleID";
    $result = $conn->query($get_roles);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $roles[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Tài Khoản Admin - VLXD Online</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #343a40;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">
                            <?php if (!$has_admins): ?>
                                Tạo Tài Khoản SuperAdmin
                            <?php else: ?>
                                Tạo Tài Khoản Admin
                            <?php endif; ?>
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success">
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!$has_admins): ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle-fill"></i> Chưa có tài khoản admin nào. Hãy tạo tài khoản SuperAdmin đầu tiên.
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="username" class="form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="form-text">Mật khẩu nên có ít nhất 6 ký tự.</div>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="fullname" class="form-label">Họ tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="role_id" class="form-label">Vai trò</label>
                                <select class="form-select" id="role_id" name="role_id" 
                                        <?php echo !$has_admins ? 'disabled' : ''; ?>>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?php echo $role['RoleID']; ?>" 
                                                <?php echo !$has_admins && $role['RoleName'] === 'superadmin' ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($role['RoleName'] . ' - ' . $role['Description']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!$has_admins): ?>
                                    <input type="hidden" name="role_id" value="1"> <!-- Gán mặc định là superadmin -->
                                    <div class="form-text text-warning">Tài khoản đầu tiên sẽ được tạo với vai trò SuperAdmin.</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-person-plus-fill me-2"></i>Tạo Tài Khoản Admin
                                </button>
                            </div>
                        </form>
                        
                        <?php if ($has_admins): ?>
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="admin_login.php" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Quay lại trang đăng nhập
                                </a>
                                
                                <?php if (isset($_SESSION['admin_id'])): ?>
                                    <a href="dashboard.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-speedometer2 me-2"></i>Quay lại Dashboard
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Phần mã SQL được tạo -->
                <?php if (!empty($success_message)): ?>
                    <div class="card mt-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Thông tin tài khoản vừa tạo</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Tên đăng nhập:</strong> <?php echo htmlspecialchars($username ?? ''); ?></p>
                            <p><strong>Mật khẩu:</strong> <?php echo htmlspecialchars($plainPassword ?? ''); ?> (đã được băm an toàn trong cơ sở dữ liệu)</p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($email ?? ''); ?></p>
                            <p><strong>Vai trò:</strong> 
                                <?php 
                                foreach ($roles as $role) {
                                    if ($role['RoleID'] == $role_id) {
                                        echo htmlspecialchars($role['RoleName']);
                                        break;
                                    }
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?> 