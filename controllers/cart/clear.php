<?php
session_start();
require_once '../../utils/helpers.php';

// Clear the cart
if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
    $_SESSION['success_message'] = 'Đã xóa tất cả sản phẩm trong giỏ hàng.';
}

// Redirect back to cart
header('Location: ../../views/cart/view.php');
exit(); 