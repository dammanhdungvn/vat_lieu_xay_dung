<?php
/**
 * File: auth_check.php
 * Mục đích: Kiểm tra người dùng đã đăng nhập với vai trò admin chưa
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    // Redirect to admin login
    header('Location: admin_login.php');
    exit();
}

/**
 * Kiểm tra đăng nhập admin
 * 
 * @return void
 */
function check_admin_login() {
    // 2. Kiểm tra xem $_SESSION['admin_id'] có tồn tại và không rỗng không
    if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
        // 3. Nếu không tồn tại, tạo thông báo và chuyển hướng
        $_SESSION['redirect_message_admin'] = 'Vui lòng đăng nhập để tiếp tục!';
        header('Location: ../admin_login.php');
        exit();
    }
}

/**
 * Kiểm tra quyền admin
 * 
 * @param array $allowed_roles_array Mảng các vai trò được phép truy cập
 * @return void
 */
function check_role($allowed_roles_array) {
    // Đảm bảo đã đăng nhập trước
    check_admin_login();
    
    // 4. Kiểm tra vai trò
    if (!isset($_SESSION['admin_role_name']) || !in_array($_SESSION['admin_role_name'], $allowed_roles_array)) {
        // Nếu không có quyền, chuyển hướng hoặc hiển thị thông báo lỗi
        $_SESSION['redirect_message_admin'] = 'Bạn không có quyền truy cập trang này!';
        header('Location: ../admin_dashboard.php');
        exit();
    }
}

// Kiểm tra đăng nhập admin mặc định khi file được include
check_admin_login();

// Optional: Check admin role for restricted pages if needed
// if (isset($require_role) && $_SESSION['admin_role_name'] != $require_role) {
//     // Redirect or show access denied
//     header('Location: dashboard.php?error=access_denied');
//     exit();
// }
?> 