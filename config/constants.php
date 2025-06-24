<?php
/**
 * Constants for VLXD Online
 */

// Site information
define('SITE_NAME', 'VLXD Online');
define('SITE_URL', 'http://localhost/hoan');
define('ADMIN_EMAIL', 'admin@vlxdonline.com');

// Paths
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/hoan');
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('UPLOADS_PATH', ASSETS_PATH . '/uploads');

// Pagination
define('ITEMS_PER_PAGE', 12);

// Order status
define('ORDER_STATUS_NEW', 'Mới');
define('ORDER_STATUS_PROCESSING', 'Đang xử lý');
define('ORDER_STATUS_SHIPPING', 'Đang giao');
define('ORDER_STATUS_COMPLETED', 'Hoàn thành');
define('ORDER_STATUS_CANCELLED', 'Đã hủy'); 