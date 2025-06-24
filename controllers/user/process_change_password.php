<?php
session_start();
require_once '../../config/db_connection.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit();
}

// Kiểm tra nếu form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    $errors = [];
    
    // Kiểm tra nếu trường nào đó trống
    if (empty($current_password)) {
        $errors[] = 'Vui lòng nhập mật khẩu hiện tại.';
    }
    
    if (empty($new_password)) {
        $errors[] = 'Vui lòng nhập mật khẩu mới.';
    } elseif (strlen($new_password) < 8) {
        $errors[] = 'Mật khẩu mới phải có ít nhất 8 ký tự.';
    } elseif (!preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $errors[] = 'Mật khẩu mới phải bao gồm chữ hoa, chữ thường và số.';
    }
    
    if ($new_password !== $confirm_password) {
        $errors[] = 'Mật khẩu nhập lại không khớp với mật khẩu mới.';
    }
    
    // Nếu không có lỗi, tiếp tục xử lý
    if (empty($errors)) {
        // Lấy mật khẩu hiện tại từ database
        $stmt = $conn->prepare("SELECT Password FROM Users WHERE UserID = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        
        // Xác thực mật khẩu hiện tại
        if (!password_verify($current_password, $user['Password'])) {
            $_SESSION['account_message'] = 'Mật khẩu hiện tại không chính xác.';
            $_SESSION['message_type'] = 'danger';
            header('Location: ../../views/user/my_account.php?page=change_password');
            exit();
        }
        
        // Băm mật khẩu mới
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Cập nhật mật khẩu mới
        $stmt = $conn->prepare("UPDATE Users SET Password = ? WHERE UserID = ?");
        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['account_message'] = 'Mật khẩu đã được thay đổi thành công.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['account_message'] = 'Có lỗi xảy ra khi thay đổi mật khẩu: ' . $conn->error;
            $_SESSION['message_type'] = 'danger';
        }
        
        $stmt->close();
    } else {
        // Có lỗi validation
        $_SESSION['account_message'] = implode('<br>', $errors);
        $_SESSION['message_type'] = 'danger';
    }
    
    // Chuyển hướng về trang đổi mật khẩu
    header('Location: ../../views/user/my_account.php?page=change_password');
    exit();
} else {
    // Nếu không phải POST request, chuyển hướng về trang tài khoản
    header('Location: ../../views/user/my_account.php');
    exit();
}
?> 