<?php
session_start();
require_once '../../config/db_connection.php';
require_once '../../utils/helpers.php';

// Redirect if not POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../views/auth/login.php");
    exit();
}

// Get and sanitize form data
$Email = trim($_POST['Email'] ?? '');
$password = trim($_POST['password'] ?? '');

// Save email to session for repopulating the form if there are errors
$_SESSION['form_data_login'] = [
    'Email' => $Email
];

// Validate required fields
if (empty($Email)) {
    $_SESSION['error_login'] = 'Vui lòng nhập địa chỉ email.';
    header("Location: ../../views/auth/login.php");
    exit();
}

if (empty($password)) {
    $_SESSION['error_login'] = 'Vui lòng nhập mật khẩu.';
    header("Location: ../../views/auth/login.php");
    exit();
}

// Prepare SQL statement to check user credentials
$stmt = $conn->prepare("SELECT UserID, FullName, Email, PasswordHash, IsActive FROM Users WHERE Email = ?");
$stmt->bind_param("s", $Email);
$stmt->execute();
$result = $stmt->get_result();

// Check if user exists and is active
if ($result->num_rows === 0) {
    $_SESSION['error_login'] = 'Email hoặc mật khẩu không đúng.';
    header("Location: ../../views/auth/login.php");
    exit();
}

$user_row = $result->fetch_assoc();

// Check if account is active
if ($user_row['IsActive'] == FALSE) {
    $_SESSION['error_login'] = 'Email hoặc mật khẩu không đúng, hoặc tài khoản đã bị khóa.';
    header("Location: ../../views/auth/login.php");
    exit();
}

// Verify password
if (password_verify($password, $user_row['PasswordHash'])) {
    // Login successful - set session variables
    $_SESSION['user_id'] = $user_row['UserID'];
    $_SESSION['user_fullname'] = $user_row['FullName'];
    
    // Clear login form data and errors
    unset($_SESSION['form_data_login']);
    if (isset($_SESSION['error_login'])) {
        unset($_SESSION['error_login']);
    }
    
    // Redirect to homepage
    header("Location: ../../index.php");
    exit();
} else {
    // Password incorrect
    $_SESSION['error_login'] = 'Email hoặc mật khẩu không đúng.';
    header("Location: ../../views/auth/login.php");
    exit();
}

$stmt->close();
$conn->close(); 