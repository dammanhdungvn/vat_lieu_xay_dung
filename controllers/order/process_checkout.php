<?php
session_start();
require_once '../../config/db_connection.php';

// Chỉ xử lý nếu là POST request, có place_order_submit, và giỏ hàng không rỗng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order_submit']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    
    // Lấy và validate dữ liệu
    $customer_name = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
    $customer_phone = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
    $customer_address = isset($_POST['customer_address']) ? trim($_POST['customer_address']) : '';
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
    
    // Validation
    $errors = [];
    
    if (empty($customer_name)) {
        $errors[] = 'Vui lòng nhập họ tên người nhận.';
    }
    
    if (empty($customer_phone)) {
        $errors[] = 'Vui lòng nhập số điện thoại.';
    } elseif (!preg_match('/^[0-9]{10,11}$/', $customer_phone)) {
        $errors[] = 'Số điện thoại không hợp lệ. Vui lòng nhập 10-11 chữ số.';
    }
    
    if (empty($customer_address)) {
        $errors[] = 'Vui lòng nhập địa chỉ giao hàng.';
    }
    
    // Nếu có lỗi, chuyển hướng về trang checkout với thông báo lỗi
    if (!empty($errors)) {
        $_SESSION['checkout_error'] = implode('<br>', $errors);
        header('Location: ../../views/order/checkout.php');
        exit();
    }
    
    // Lấy UserID nếu người dùng đã đăng nhập
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    // Tính tổng tiền đơn hàng
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
    
    // Bắt đầu transaction
    try {
        $conn->begin_transaction();
        
        // Thêm đơn hàng mới vào bảng Orders
        $insert_order_sql = "INSERT INTO Orders (UserID, CustomerName, CustomerPhone, CustomerAddress, OrderDate, TotalAmount, Status, Notes) 
                             VALUES (?, ?, ?, ?, NOW(), ?, 'Mới', ?)";
        
        $stmt = $conn->prepare($insert_order_sql);
        $stmt->bind_param("isssds", $user_id, $customer_name, $customer_phone, $customer_address, $total_amount, $notes);
        
        if (!$stmt->execute()) {
            throw new Exception("Không thể tạo đơn hàng: " . $stmt->error);
        }
        
        $order_id = $conn->insert_id;
        $stmt->close();
        
        // Thêm chi tiết đơn hàng và cập nhật tồn kho
        foreach ($_SESSION['cart'] as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            
            // Kiểm tra tồn kho
            $check_stock_sql = "SELECT StockQuantity, ProductName FROM Products WHERE ProductID = ?";
            $stmt = $conn->prepare($check_stock_sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception("Sản phẩm không tồn tại.");
            }
            
            $product = $result->fetch_assoc();
            $stmt->close();
            
            if ($quantity > $product['StockQuantity']) {
                throw new Exception("Sản phẩm '{$product['ProductName']}' không đủ hàng. Hiện chỉ còn {$product['StockQuantity']} sản phẩm.");
            }
            
            // Thêm vào bảng OrderItems
            $insert_item_sql = "INSERT INTO OrderItems (OrderID, ProductID, Quantity, PriceAtOrder) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_item_sql);
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            
            if (!$stmt->execute()) {
                throw new Exception("Không thể thêm chi tiết đơn hàng: " . $stmt->error);
            }
            
            $stmt->close();
            
            // Cập nhật tồn kho
            $update_stock_sql = "UPDATE Products SET StockQuantity = StockQuantity - ? WHERE ProductID = ?";
            $stmt = $conn->prepare($update_stock_sql);
            $stmt->bind_param("ii", $quantity, $product_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Không thể cập nhật tồn kho: " . $stmt->error);
            }
            
            $stmt->close();
        }
        
        // Commit transaction nếu mọi thứ thành công
        $conn->commit();
        
        // Lưu OrderID và xóa giỏ hàng
        $_SESSION['last_order_id'] = $order_id;
        unset($_SESSION['cart']);
        
        // Chuyển hướng đến trang cảm ơn
        header('Location: ../../views/order/thank_you.php');
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction nếu có lỗi
        $conn->rollback();
        
        // Lưu thông báo lỗi và chuyển hướng về trang checkout
        $_SESSION['checkout_error'] = 'Đã xảy ra lỗi khi xử lý đơn hàng: ' . $e->getMessage();
        header('Location: ../../views/order/checkout.php');
        exit();
    }
    
} else {
    // Nếu không thỏa mãn điều kiện, chuyển hướng về trang giỏ hàng
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        $_SESSION['cart_message'] = 'Giỏ hàng của bạn đang trống.';
        header('Location: ../../views/cart/view.php');
    } else {
        header('Location: ../../views/order/checkout.php');
    }
    exit();
}
?> 