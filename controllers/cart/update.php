<?php
session_start();
require_once '../../utils/helpers.php';

// Check if index and quantity are provided
if (!isset($_POST['index']) || !isset($_POST['quantity']) || !is_numeric($_POST['index']) || !is_numeric($_POST['quantity'])) {
    $_SESSION['error_message'] = 'Thông tin không hợp lệ.';
    header('Location: ../../views/cart/view.php');
    exit();
}

$index = (int)$_POST['index'];
$quantity = (int)$_POST['quantity'];

// Check if cart exists and index is valid
if (!isset($_SESSION['cart']) || !isset($_SESSION['cart'][$index])) {
    $_SESSION['error_message'] = 'Sản phẩm không tồn tại trong giỏ hàng.';
    header('Location: ../../views/cart/view.php');
    exit();
}

// Validate quantity
if ($quantity <= 0) {
    // If quantity is zero or negative, remove the item
    unset($_SESSION['cart'][$index]);
    // Reindex the array
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    $_SESSION['success_message'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
} else {
    // Update quantity
    $_SESSION['cart'][$index]['quantity'] = $quantity;
    $_SESSION['success_message'] = 'Đã cập nhật số lượng sản phẩm.';
}

// Redirect back to cart
header('Location: ../../views/cart/view.php');
exit(); 