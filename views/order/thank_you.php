<?php
session_start();
$page_title = 'Đặt hàng thành công - VLXD Online';
require_once '../../config/db_connection.php';
require_once '../../utils/helpers.php';

include_once '../../includes/header.php';
?>

<div class="container mt-5 text-center">
    <?php if (isset($_SESSION['last_order_id'])): ?>
        <div class="mb-4">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
            <h1 class="mt-3">Đặt hàng thành công!</h1>
            <p class="lead">Cảm ơn bạn đã mua hàng. Mã đơn hàng của bạn là: #<?php echo $_SESSION['last_order_id']; ?>.</p>
            <p>Chúng tôi sẽ sớm liên hệ xác nhận.</p>
        </div>
        <?php unset($_SESSION['last_order_id']); ?>
    <?php else: ?>
        <div class="mb-4">
            <i class="bi bi-exclamation-circle text-warning" style="font-size: 5rem;"></i>
            <h1 class="mt-3">Không có thông tin đơn hàng.</h1>
            <p>Bạn chưa đặt hàng hoặc thông tin đơn hàng không tồn tại.</p>
        </div>
    <?php endif; ?>
    
    <div class="mt-4">
        <a href="../../index.php" class="btn btn-primary btn-lg me-2">
            <i class="bi bi-cart me-2"></i>Tiếp tục mua sắm
        </a>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="../../views/user/my_account.php?page=order_history" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-clock-history me-2"></i>Xem lịch sử đơn hàng
            </a>
        <?php endif; ?>
    </div>
</div>

<?php
include_once '../../includes/footer.php';
?> 