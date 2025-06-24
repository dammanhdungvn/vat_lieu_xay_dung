<?php
/**
 * Helper functions for VLXD Online
 */

/**
 * Redirects to a specified URL
 * @param string $url The URL to redirect to
 * @return void
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Sets a flash message in the session
 * @param string $type The message type ('success', 'error', 'warning', 'info')
 * @param string $message The message content
 * @return void
 */
function set_flash_message($type, $message) {
    if ($type === 'success') {
        $_SESSION['success_message'] = $message;
    } elseif ($type === 'error') {
        $_SESSION['error_message'] = $message;
    } elseif ($type === 'login_success') {
        $_SESSION['success_login'] = $message;
    } elseif ($type === 'login_error') {
        $_SESSION['error_login'] = $message;
    } elseif ($type === 'register_success') {
        $_SESSION['success_register'] = $message;
    } elseif ($type === 'register_error') {
        $_SESSION['error_register'] = $message;
    }
}

/**
 * Formats a price with currency symbol
 * @param float $price The price to format
 * @return string The formatted price
 */
function format_price($price) {
    return number_format($price, 0, ',', '.') . ' đ';
}

/**
 * Sanitizes user input
 * @param string $data The input data to sanitize
 * @return string The sanitized data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Checks if user is logged in
 * @return boolean
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Require user to be logged in, redirect if not
 * @param string $redirect_url The URL to redirect to if not logged in
 * @return void
 */
function require_login($redirect_url = '../views/auth/login.php') {
    if (!is_logged_in()) {
        set_flash_message('error', 'Vui lòng đăng nhập để tiếp tục.');
        redirect($redirect_url);
    }
}

/**
 * Generate a random string
 * @param int $length The length of the random string
 * @return string The generated random string
 */
function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    $charactersLength = strlen($characters);
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
} 