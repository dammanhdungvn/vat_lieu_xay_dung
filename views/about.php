<?php
session_start();
$page_title = 'Giới thiệu - VLXD Online';
require_once '../config/db_connection.php';
require_once '../config/constants.php';
require_once '../utils/helpers.php';

// Đặt biến base_url trước khi include header
$base_url = SITE_URL;

include_once '../includes/header.php';
?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h2 class="text-center mb-4">Giới thiệu về VLXD Online</h2>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title">Chúng tôi là ai?</h4>
                    <p class="card-text">
                        VLXD Online là đơn vị chuyên cung cấp vật liệu xây dựng chất lượng cao với mức giá cạnh tranh nhất trên thị trường. 
                        Được thành lập vào năm 2023, chúng tôi đã nhanh chóng trở thành đối tác tin cậy của các công trình xây dựng lớn nhỏ 
                        trên toàn quốc.
                    </p>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title">Sứ mệnh của chúng tôi</h4>
                    <p class="card-text">
                        VLXD Online cam kết cung cấp các sản phẩm vật liệu xây dựng chất lượng, đảm bảo đúng tiêu chuẩn và nguồn gốc rõ ràng.
                        Chúng tôi luôn đặt sự hài lòng của khách hàng lên hàng đầu, từ khâu tư vấn, báo giá đến giao hàng và hỗ trợ sau bán hàng.
                    </p>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title">Vì sao chọn chúng tôi?</h4>
                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3 text-primary">
                                    <i class="bi bi-check-circle-fill fs-4"></i>
                                </div>
                                <div>
                                    <h5>Sản phẩm chất lượng</h5>
                                    <p>Toàn bộ sản phẩm được kiểm soát chất lượng nghiêm ngặt, đúng tiêu chuẩn</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3 text-primary">
                                    <i class="bi bi-currency-dollar fs-4"></i>
                                </div>
                                <div>
                                    <h5>Giá cả hợp lý</h5>
                                    <p>Mức giá cạnh tranh nhờ hệ thống phân phối trực tiếp từ nhà sản xuất</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3 text-primary">
                                    <i class="bi bi-truck fs-4"></i>
                                </div>
                                <div>
                                    <h5>Giao hàng nhanh chóng</h5>
                                    <p>Hệ thống kho bãi rộng khắp, đảm bảo giao hàng đúng tiến độ</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3 text-primary">
                                    <i class="bi bi-headset fs-4"></i>
                                </div>
                                <div>
                                    <h5>Hỗ trợ 24/7</h5>
                                    <p>Đội ngũ tư vấn viên chuyên nghiệp, hỗ trợ khách hàng mọi lúc mọi nơi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title">Đối tác của chúng tôi</h4>
                    <p class="card-text">
                        VLXD Online tự hào là đối tác tin cậy của các nhà sản xuất vật liệu xây dựng uy tín trong và ngoài nước như: 
                        Vicem, Hoa Sen, Thép Việt, Unilever, AkzoNobel, Dulux và nhiều đơn vị khác.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?> 