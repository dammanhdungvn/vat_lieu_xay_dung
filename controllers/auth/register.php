<?php
session_start();
require_once '../../config/db_connection.php';
require_once '../../utils/helpers.php';

// Redirect if not POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../views/auth/register.php");
    exit();
}

// Get and sanitize form data
$FullName = trim(htmlspecialchars($_POST['FullName'] ?? ''));
$Email = trim(htmlspecialchars($_POST['Email'] ?? ''));
$PhoneNumber = trim(htmlspecialchars($_POST['PhoneNumber'] ?? ''));
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Save form data to session for repopulating the form if there are errors
$_SESSION['form_data_register'] = [
    'FullName' => $FullName,
    'Email' => $Email,
    'PhoneNumber' => $PhoneNumber
];

// Validate required fields
if (empty($FullName)) {
    $_SESSION['error_register'] = 'Vui lòng nhập họ và tên.';
    header("Location: ../../views/auth/register.php");
    exit();
}

if (empty($Email)) {
    $_SESSION['error_register'] = 'Vui lòng nhập email.';
    header("Location: ../../views/auth/register.php");
    exit();
}

// Validate email format
if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_register'] = 'Địa chỉ email không hợp lệ.';
    header("Location: ../../views/auth/register.php");
    exit();
}

// Validate password
if (empty($password)) {
    $_SESSION['error_register'] = 'Vui lòng nhập mật khẩu.';
    header("Location: ../../views/auth/register.php");
    exit();
}

if (strlen($password) < 6) {
    $_SESSION['error_register'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
    header("Location: ../../views/auth/register.php");
    exit();
}

// Check if passwords match
if ($password !== $confirm_password) {
    $_SESSION['error_register'] = 'Mật khẩu xác nhận không khớp.';
    header("Location: ../../views/auth/register.php");
    exit();
}

// Check for duplicate email
$stmt = $conn->prepare("SELECT UserID FROM Users WHERE Email = ?");
$stmt->bind_param("s", $Email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error_register'] = 'Email đã được sử dụng.';
    header("Location: ../../views/auth/register.php");
    exit();
}
$stmt->close();

// Check for duplicate phone number (if provided)
if (!empty($PhoneNumber)) {
    $stmt = $conn->prepare("SELECT UserID FROM Users WHERE PhoneNumber = ?");
    $stmt->bind_param("s", $PhoneNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error_register'] = 'Số điện thoại đã được sử dụng.';
        header("Location: ../../views/auth/register.php");
        exit();
    }
    $stmt->close();
}

// All validations passed, proceed with registration
// Hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$stmt = $conn->prepare("INSERT INTO Users (FullName, Email, PasswordHash, PhoneNumber, IsActive) VALUES (?, ?, ?, ?, TRUE)");
$stmt->bind_param("ssss", $FullName, $Email, $password_hash, $PhoneNumber);

if ($stmt->execute()) {
    // Registration successful
    unset($_SESSION['form_data_register']);
    if (isset($_SESSION['error_register'])) {
        unset($_SESSION['error_register']);
    }
    $_SESSION['success_login'] = 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.';
    header("Location: ../../views/auth/login.php");
    exit();
} else {
    // Registration failed
    $_SESSION['error_register'] = 'Lỗi hệ thống khi đăng ký. Vui lòng thử lại sau.';
    header("Location: ../../views/auth/register.php");
    exit();
}

$stmt->close();
$conn->close(); 