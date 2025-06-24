<?php
session_start();

// Unset admin session variables
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_fullname']);
unset($_SESSION['admin_role_name']);

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: admin_login.php');
exit();
?> 