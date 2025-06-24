<?php
session_start();
require_once '../../config/db_connection.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Get action, product_id, and quantity
$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';
$product_id = isset($_REQUEST['product_id']) ? (int)$_REQUEST['product_id'] : 0;
$quantity = isset($_REQUEST['quantity']) ? (int)$_REQUEST['quantity'] : 1;

// Validate inputs
if (empty($action)) {
    $_SESSION['cart_message'] = 'Thao tác không hợp lệ.';
    header('Location: ../../index.php');
    exit();
}

switch ($action) {
    case 'add':
        // Validate product_id and quantity
        if ($product_id <= 0) {
            $_SESSION['cart_message'] = 'ID sản phẩm không hợp lệ.';
            header('Location: ../../index.php');
            exit();
        }
        
        if ($quantity <= 0) {
            $quantity = 1;
        }
        
        // Query database to get product details
        $sql = "SELECT ProductName, Price, ImagePath, Unit, StockQuantity FROM Products WHERE ProductID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Check if product exists
        if ($result->num_rows === 0) {
            $_SESSION['cart_message'] = 'Sản phẩm không tồn tại.';
            header('Location: ../../views/product/list.php');
            exit();
        }
        
        $product = $result->fetch_assoc();
        $stmt->close();
        
        // Check if quantity exceeds stock
        if ($quantity > $product['StockQuantity']) {
            $_SESSION['cart_message'] = 'Số lượng yêu cầu vượt quá số lượng tồn kho.';
            header('Location: ../../views/product/detail.php?id=' . $product_id);
            exit();
        }
        
        // Check if product already exists in cart
        $found = false;
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['product_id'] == $product_id) {
                // Calculate the new quantity
                $new_quantity = $item['quantity'] + $quantity;
                
                // Make sure the new quantity doesn't exceed stock
                if ($new_quantity > $product['StockQuantity']) {
                    $new_quantity = $product['StockQuantity'];
                    $_SESSION['cart_message'] = 'Đã cập nhật số lượng tối đa cho sản phẩm.';
                } else {
                    $_SESSION['cart_message'] = 'Đã cập nhật số lượng sản phẩm trong giỏ hàng.';
                }
                
                $_SESSION['cart'][$key]['quantity'] = $new_quantity;
                $found = true;
                break;
            }
        }
        
        // If product not found in cart, add it
        if (!$found) {
            $_SESSION['cart'][] = array(
                'product_id' => $product_id,
                'name' => $product['ProductName'],
                'price' => $product['Price'],
                'image' => $product['ImagePath'],
                'unit' => $product['Unit'],
                'quantity' => $quantity
            );
            
            $_SESSION['cart_message'] = 'Đã thêm sản phẩm vào giỏ hàng!';
        }
        
        // Redirect to previous page or cart
        if (isset($_SERVER['HTTP_REFERER'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            header('Location: ../../views/cart/view.php');
        }
        exit();
        
    case 'update_multiple':
        // Process multiple quantity updates
        if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
            foreach ($_POST['quantity'] as $pid => $qty) {
                $pid = (int)$pid;
                $qty = (int)$qty;
                
                if ($pid > 0) {
                    // Find product in cart
                    foreach ($_SESSION['cart'] as $key => $item) {
                        if ($item['product_id'] == $pid) {
                            // If quantity <= 0, remove the product
                            if ($qty <= 0) {
                                unset($_SESSION['cart'][$key]);
                            } else {
                                // Query database to check stock
                                $sql = "SELECT StockQuantity FROM Products WHERE ProductID = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $pid);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $product = $result->fetch_assoc();
                                $stmt->close();
                                
                                // Make sure quantity doesn't exceed stock
                                if ($qty > $product['StockQuantity']) {
                                    $qty = $product['StockQuantity'];
                                }
                                
                                $_SESSION['cart'][$key]['quantity'] = $qty;
                            }
                            break;
                        }
                    }
                }
            }
            
            // Reindex the cart array after potential removals
            if (!empty($_SESSION['cart'])) {
                $_SESSION['cart'] = array_values($_SESSION['cart']);
            }
            
            $_SESSION['cart_message'] = 'Giỏ hàng đã được cập nhật.';
        }
        
        header('Location: ../../views/cart/view.php');
        exit();
        
    case 'update':
        // Validate product_id and quantity
        if ($product_id <= 0) {
            $_SESSION['cart_message'] = 'ID sản phẩm không hợp lệ.';
            header('Location: ../../views/cart/view.php');
            exit();
        }
        
        // Find product in cart and update quantity
        $found = false;
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $found = true;
                
                // If quantity <= 0, remove the product
                if ($quantity <= 0) {
                    unset($_SESSION['cart'][$key]);
                    $_SESSION['cart_message'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
                } else {
                    // Query database to check stock
                    $sql = "SELECT StockQuantity FROM Products WHERE ProductID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $product_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $product = $result->fetch_assoc();
                    $stmt->close();
                    
                    // Make sure quantity doesn't exceed stock
                    if ($quantity > $product['StockQuantity']) {
                        $quantity = $product['StockQuantity'];
                        $_SESSION['cart_message'] = 'Đã cập nhật với số lượng tối đa có thể.';
                    } else {
                        $_SESSION['cart_message'] = 'Đã cập nhật số lượng sản phẩm.';
                    }
                    
                    $_SESSION['cart'][$key]['quantity'] = $quantity;
                }
                break;
            }
        }
        
        // If product not found in cart
        if (!$found) {
            $_SESSION['cart_message'] = 'Sản phẩm không có trong giỏ hàng.';
        }
        
        // Reindex the cart array after potential removal
        if (!empty($_SESSION['cart'])) {
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }
        
        header('Location: ../../views/cart/view.php');
        exit();
        
    case 'remove':
        // Validate product_id
        if ($product_id <= 0) {
            $_SESSION['cart_message'] = 'ID sản phẩm không hợp lệ.';
            header('Location: ../../views/cart/view.php');
            exit();
        }
        
        // Find product in cart and remove it
        $found = false;
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['product_id'] == $product_id) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart_message'] = 'Đã xóa sản phẩm khỏi giỏ hàng.';
                $found = true;
                break;
            }
        }
        
        // If product not found in cart
        if (!$found) {
            $_SESSION['cart_message'] = 'Sản phẩm không có trong giỏ hàng.';
        }
        
        // Reindex the cart array after removal
        if (!empty($_SESSION['cart'])) {
            $_SESSION['cart'] = array_values($_SESSION['cart']);
        }
        
        header('Location: ../../views/cart/view.php');
        exit();
        
    case 'clear':
        // Clear the cart
        $_SESSION['cart'] = array();
        $_SESSION['cart_message'] = 'Đã xóa tất cả sản phẩm trong giỏ hàng.';
        header('Location: ../../views/cart/view.php');
        exit();
        
    default:
        // Invalid action
        $_SESSION['cart_message'] = 'Thao tác không hợp lệ.';
        header('Location: ../../index.php');
        exit();
}
?> 