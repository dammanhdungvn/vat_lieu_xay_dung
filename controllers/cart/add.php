<?php
session_start();
require_once '../../config/db_connection.php';
require_once '../../utils/helpers.php';

// Check if product_id and quantity are provided
if (!isset($_POST['product_id']) || !isset($_POST['quantity']) || !is_numeric($_POST['product_id']) || !is_numeric($_POST['quantity'])) {
    $_SESSION['error_message'] = 'Thông tin sản phẩm không hợp lệ.';
    header('Location: ../../views/product/list.php');
    exit();
}

$product_id = (int)$_POST['product_id'];
$quantity = (int)$_POST['quantity'];

// Validate quantity
if ($quantity <= 0) {
    $_SESSION['error_message'] = 'Số lượng phải lớn hơn 0.';
    header('Location: ../../views/product/detail.php?id=' . $product_id);
    exit();
}

// Check if product exists and has enough stock
$stmt = $conn->prepare("SELECT ProductID, ProductName, Price, StockQuantity, ImagePath FROM Products WHERE ProductID = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = 'Sản phẩm không tồn tại.';
    header('Location: ../../views/product/list.php');
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();

// Check stock quantity
if ($product['StockQuantity'] < $quantity) {
    $_SESSION['error_message'] = 'Số lượng sản phẩm không đủ. Hiện tại chỉ còn ' . $product['StockQuantity'] . ' sản phẩm.';
    header('Location: ../../views/product/detail.php?id=' . $product_id);
    exit();
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if product already in cart
$product_exists = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['product_id'] == $product_id) {
        // Update quantity
        $item['quantity'] += $quantity;
        $product_exists = true;
        break;
    }
}

// Add product to cart if not exists
if (!$product_exists) {
    $_SESSION['cart'][] = [
        'product_id' => $product_id,
        'name' => $product['ProductName'],
        'price' => $product['Price'],
        'quantity' => $quantity,
        'image' => $product['ImagePath']
    ];
}

// Set success message
$_SESSION['success_message'] = 'Đã thêm sản phẩm vào giỏ hàng.';

// Redirect to cart page or product page
if (isset($_POST['redirect_to_cart']) && $_POST['redirect_to_cart'] == 1) {
    header('Location: ../../views/cart/view.php');
} else {
    header('Location: ../../views/product/detail.php?id=' . $product_id);
}
exit(); 