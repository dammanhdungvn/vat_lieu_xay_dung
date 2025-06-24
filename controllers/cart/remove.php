<?php
session_start();
require_once '../../utils/helpers.php';

// Check if index is provided
if (!isset($_POST['index']) || !is_numeric($_POST['index'])) {
    $_SESSION['error_message'] = 'Thông tin không hợp lệ.';
    header('Location: ../../views/cart/view.php');
    exit();
}

$index = (int)$_POST['index'];

// Check if cart exists and index is valid
if (!isset($_SESSION['cart']) || !isset($_SESSION['cart'][$index])) {
    $_SESSION['error_message'] = 'Sản phẩm không tồn tại trong giỏ hàng.';
    header('Location: ../../views/cart/view.php');
    exit();
}

// Remove item from cart
$product_name = $_SESSION['cart'][$index]['name'];
unset($_SESSION['cart'][$index]);

// Reindex the array
$_SESSION['cart'] = array_values($_SESSION['cart']);

$_SESSION['success_message'] = 'Đã xóa sản phẩm "' . $product_name . '" khỏi giỏ hàng.';

// Redirect back to cart
header('Location: ../../views/cart/view.php');
exit(); 