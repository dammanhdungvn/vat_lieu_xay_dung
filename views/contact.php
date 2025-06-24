<?php
session_start();
$page_title = 'Liên hệ - VLXD Online';
require_once '../config/db_connection.php';
require_once '../config/constants.php';
require_once '../utils/helpers.php';

// Đặt biến base_url trước khi include header
$base_url = SITE_URL;

$message_sent = false;
// Xử lý form liên hệ khi submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    // Xử lý dữ liệu gửi từ form
    $fullname = sanitize($_POST['fullname'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    // Kiểm tra điều kiện
    $errors = [];
    
    if (empty($fullname)) {
        $errors[] = 'Vui lòng nhập họ tên';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }
    
    if (empty($phone)) {
        $errors[] = 'Vui lòng nhập số điện thoại';
    }
    
    if (empty($message)) {
        $errors[] = 'Vui lòng nhập nội dung';
    }
    
    // Nếu không có lỗi
    if (empty($errors)) {
        // Lưu thông tin liên hệ vào database (giả định có bảng Contact)
        // Trong thực tế, bạn sẽ lưu vào bảng và/hoặc gửi email đến admin
        
        // Hiển thị thông báo thành công
        $message_sent = true;
        set_flash_message('success', 'Cảm ơn bạn đã gửi liên hệ. Chúng tôi sẽ phản hồi trong thời gian sớm nhất!');
    } else {
        // Hiển thị lỗi
        foreach ($errors as $error) {
            set_flash_message('error', $error);
        }
    }
}

include_once '../includes/header.php';
?>

<div class="container mt-5 mb-5">
    <h2 class="text-center mb-4">Liên hệ với chúng tôi</h2>
    
    <div class="row">
        <!-- Thông tin liên hệ -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Thông tin liên hệ</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 d-flex align-items-start">
                        <i class="bi bi-geo-alt-fill text-primary me-3 fs-4"></i>
                        <div>
                            <h5>Địa chỉ</h5>
                            <p>123 Đường Nguyễn Văn Linh, Quận 7, TP.HCM</p>
                        </div>
                    </div>
                    
                    <div class="mb-3 d-flex align-items-start">
                        <i class="bi bi-telephone-fill text-primary me-3 fs-4"></i>
                        <div>
                            <h5>Điện thoại</h5>
                            <p>
                                Kinh doanh: (028) 3812 3456<br>
                                Hỗ trợ: 0909 123 456
                            </p>
                        </div>
                    </div>
                    
                    <div class="mb-3 d-flex align-items-start">
                        <i class="bi bi-envelope-fill text-primary me-3 fs-4"></i>
                        <div>
                            <h5>Email</h5>
                            <p>
                                Kinh doanh: sales@vlxdonline.com<br>
                                Hỗ trợ: support@vlxdonline.com
                            </p>
                        </div>
                    </div>
                    
                    <div class="mb-3 d-flex align-items-start">
                        <i class="bi bi-clock-fill text-primary me-3 fs-4"></i>
                        <div>
                            <h5>Giờ làm việc</h5>
                            <p>
                                Thứ Hai - Thứ Sáu: 8:00 - 17:30<br>
                                Thứ Bảy: 8:00 - 12:00<br>
                                Chủ Nhật: Nghỉ
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h5>Kết nối với chúng tôi</h5>
                        <div class="d-flex gap-3 mt-2">
                            <a href="#" class="text-primary fs-4"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="text-info fs-4"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="text-danger fs-4"><i class="bi bi-youtube"></i></a>
                            <a href="#" class="text-success fs-4"><i class="bi bi-telephone-fill"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form liên hệ -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Gửi tin nhắn cho chúng tôi</h5>
                </div>
                <div class="card-body">
                    <?php if ($message_sent): ?>
                        <div class="alert alert-success">
                            <p class="mb-0">Cảm ơn bạn đã gửi liên hệ. Chúng tôi sẽ phản hồi trong thời gian sớm nhất!</p>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fullname" class="form-label">Họ tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Nhập họ tên" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Nhập số điện thoại" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="subject" class="form-label">Chủ đề</label>
                                    <input type="text" class="form-control" id="subject" name="subject" placeholder="Nhập chủ đề">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Nội dung <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="5" placeholder="Nhập nội dung" required></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="contact_submit" class="btn btn-primary">
                                    <i class="bi bi-send me-2"></i>Gửi tin nhắn
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Google Map -->
    <div class="card mt-4">
        <div class="card-body p-0">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.651860337213!2d106.69908231532036!3d10.762909362389766!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f1b7c3ed289%3A0xa06651894598e488!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBCw6FjaCBraG9hIC0gxJDhuqFpIGjhu41jIFF14buRYyBnaWEgVFAuSENN!5e0!3m2!1svi!2s!4v1679123456789!5m2!1svi!2s" 
                    width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?> 