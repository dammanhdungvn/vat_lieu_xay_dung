# 🏗️ VLXD Online - Hệ thống Quản lý và Bán hàng Vật liệu Xây dựng

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-00758F.svg)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3.svg)](https://getbootstrap.com)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E.svg)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

> **Hệ thống e-commerce chuyên biệt cho ngành vật liệu xây dựng với đầy đủ chức năng quản lý và bán hàng trực tuyến**

## 📋 Tổng quan Project

**VLXD Online** là một hệ thống e-commerce được phát triển riêng cho ngành vật liệu xây dựng tại Việt Nam. Hệ thống cung cấp giải pháp toàn diện từ quản lý kho hàng, bán hàng trực tuyến đến chăm sóc khách hàng.

### 🎯 Đối tượng sử dụng

- **🏪 Cửa hàng vật liệu xây dựng**: Quản lý sản phẩm, đơn hàng, kho hàng
- **🏗️ Nhà thầu xây dựng**: Đặt hàng trực tuyến với số lượng lớn
- **🏠 Khách hàng cá nhân**: Mua sắm vật liệu cho công trình nhỏ

## 🚀 Tính năng chính

### 👥 Khách hàng (Frontend)
- **🔐 Quản lý tài khoản**: Đăng ký, đăng nhập, cập nhật thông tin cá nhân
- **🛍️ Mua sắm**: Duyệt sản phẩm theo danh mục, tìm kiếm, xem chi tiết
- **🛒 Giỏ hàng**: Thêm/xóa sản phẩm, cập nhật số lượng
- **📦 Đặt hàng**: Thanh toán, theo dõi đơn hàng
- **⭐ Đánh giá**: Để lại nhận xét về sản phẩm đã mua

### 🔧 Quản trị viên (Admin Panel)
- **📊 Dashboard**: Thống kê tổng quan (sản phẩm, đơn hàng, khách hàng)
- **📦 Quản lý sản phẩm**: CRUD sản phẩm với upload hình ảnh
- **📂 Quản lý danh mục**: Tổ chức sản phẩm theo danh mục
- **🏷️ Quản lý thương hiệu**: Quản lý các nhãn hiệu vật liệu
- **📋 Quản lý đơn hàng**: Xem, cập nhật trạng thái đơn hàng
- **📦 Quản lý kho**: Theo dõi tồn kho, cảnh báo hết hàng
- **👥 Quản lý người dùng**: Quản lý tài khoản khách hàng và admin
- **💬 Quản lý đánh giá**: Duyệt và phản hồi đánh giá khách hàng

## 🛠️ Công nghệ sử dụng

### Backend
- **PHP 8.2+** - Ngôn ngữ lập trình chính
- **MySQL 8.0+** - Cơ sở dữ liệu
- **MySQLi** - Driver kết nối database

### Frontend
- **HTML5 & CSS3** - Cấu trúc và style
- **Bootstrap 5.3** - Framework CSS responsive
- **JavaScript (Vanilla)** - Tương tác client-side
- **Bootstrap Icons** - Bộ icon

### Kiến trúc
- **MVC Pattern** - Tách biệt logic, view và model
- **Session-based Authentication** - Quản lý đăng nhập
- **Prepared Statements** - Bảo mật SQL

## 💻 Yêu cầu hệ thống

### Server Requirements
- **PHP** 8.2+ với các extension:
  - `mysqli` - Kết nối MySQL
  - `session` - Quản lý session
  - `json` - Xử lý JSON
  - `mbstring` - Xử lý chuỗi UTF-8
  - `gd` - Xử lý hình ảnh (nếu có)

- **MySQL** 8.0+ hoặc MariaDB 10.4+
- **Web Server** Apache 2.4+ hoặc Nginx

### Client Support
- Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- Responsive design cho mobile và tablet

## 📥 Cài đặt

### 1. Clone project
```bash
git clone https://github.com/dammanhdungvn/vat_lieu_xay_dung.git
cd vat_lieu_xay_dung
```

### 2. Cấu hình Database
```sql
-- Tạo database
CREATE DATABASE xaydung CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Import dữ liệu
mysql -u username -p xaydung < xaydung.sql
```

### 3. Cấu hình kết nối DB
Chỉnh sửa file `config/db_connection.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'xaydung');
define('DB_PORT', 3306); // Thay đổi nếu cần
```

### 4. Cấu hình Constants
Chỉnh sửa file `config/constants.php`:
```php
define('SITE_URL', 'http://your-domain.com');
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . '/your-project-path');
```

### 5. Cấu hình Web Server

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Bảo mật thư mục config
<Directory "config">
    Require all denied
</Directory>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/vat_lieu_xay_dung;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ^~ /config/ {
        deny all;
        return 403;
    }
}
```

## 📁 Cấu trúc project

```
vat_lieu_xay_dung/
├── admin/                    # Admin panel
│   ├── includes/            # Header, sidebar, auth
│   ├── products/            # Quản lý sản phẩm
│   ├── orders/              # Quản lý đơn hàng
│   ├── categories_management/ # Quản lý danh mục
│   ├── users_management/    # Quản lý người dùng
│   └── dashboard.php        # Trang chủ admin
├── assets/                  # Static files
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   └── images/             # Hình ảnh
├── config/                  # Cấu hình
│   ├── db_connection.php   # Kết nối database
│   └── constants.php       # Constants
├── controllers/             # Controllers
│   ├── auth/               # Xử lý đăng nhập/đăng ký
│   ├── cart/               # Xử lý giỏ hàng
│   └── order/              # Xử lý đơn hàng
├── models/                  # Models
├── views/                   # Views
│   ├── auth/               # Giao diện đăng nhập
│   ├── product/            # Giao diện sản phẩm
│   ├── cart/               # Giao diện giỏ hàng
│   └── order/              # Giao diện đơn hàng
├── includes/                # Shared includes
├── utils/                   # Utilities
├── index.php               # Entry point
└── xaydung.sql            # Database schema
```

## 📊 Database Schema

### Bảng chính
- **`users`** - Thông tin khách hàng
- **`admins`** - Tài khoản quản trị
- **`products`** - Sản phẩm vật liệu xây dựng
- **`categories`** - Danh mục sản phẩm
- **`brands`** - Thương hiệu
- **`orders`** - Đơn hàng
- **`orderitems`** - Chi tiết đơn hàng
- **`reviews`** - Đánh giá sản phẩm
- **`roles`** - Phân quyền
- **`settings`** - Cài đặt hệ thống

## 🎯 Cách sử dụng

### Khách hàng
1. Truy cập trang chủ: `index.php` → chuyển hướng đến `views/product/product.php`
2. Duyệt sản phẩm theo danh mục hoặc tìm kiếm
3. Xem chi tiết sản phẩm: `views/product/detail.php`
4. Thêm vào giỏ hàng: `controllers/cart/add.php`
5. Xem giỏ hàng: `views/cart/view.php`
6. Thanh toán: `views/order/checkout.php`

### Admin
1. Đăng nhập admin: `admin/admin_login.php`
   - username: admin | password: admin123  
2. Dashboard: `admin/dashboard.php`
3. Quản lý sản phẩm: `admin/products/index.php`
4. Quản lý đơn hàng: `admin/orders/index.php`

## 🔒 Bảo mật

- **Password hashing** với `password_hash()` PHP
- **Prepared statements** chống SQL injection
- **Session management** bảo mật
- **Input validation** và escape output
- **Role-based access control**

## 🔧 API/Endpoints chính

### Frontend
- `GET /views/product/product.php` - Danh sách sản phẩm
- `GET /views/product/detail.php?id={id}` - Chi tiết sản phẩm
- `POST /controllers/cart/add.php` - Thêm vào giỏ hàng
- `POST /controllers/order/process_checkout.php` - Xử lý đặt hàng

### Admin
- `GET /admin/dashboard.php` - Dashboard
- `GET /admin/products/index.php` - Quản lý sản phẩm
- `POST /admin/products/add.php` - Thêm sản phẩm
- `GET /admin/orders/index.php` - Quản lý đơn hàng

## 🚦 Testing

Để test hệ thống:

1. **Database test**: Chạy `db_check.php` để kiểm tra kết nối
2. **Admin access**: Đăng nhập admin với tài khoản trong bảng `admins`
3. **Frontend test**: Truy cập `views/product/product.php` để test giao diện
4. **Order flow**: Test toàn bộ quy trình từ chọn sản phẩm đến đặt hàng

## 🐛 Troubleshooting

### Lỗi thường gặp

1. **Lỗi kết nối database**
   - Kiểm tra config trong `config/db_connection.php`
   - Đảm bảo MySQL service đang chạy
   - Kiểm tra port (mặc định 3307 trong config)

2. **Lỗi 404 khi truy cập**
   - Kiểm tra web server configuration
   - Đảm bảo mod_rewrite được bật (Apache)

3. **Lỗi session**
   - Kiểm tra quyền write của thư mục session
   - Đảm bảo session.save_path được config đúng

4. **Lỗi upload hình ảnh**
   - Kiểm tra quyền write của thư mục `assets/images/`
   - Kiểm tra file size limit trong php.ini

## 🤝 Đóng góp

1. Fork project
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📈 Roadmap

- [ ] **API RESTful** hoàn chỉnh
- [ ] **Mobile App** (React Native/Flutter)
- [ ] **Payment Gateway** integration
- [ ] **Inventory management** nâng cao
- [ ] **Analytics dashboard**
- [ ] **Multi-vendor support**
- [ ] **Real-time notifications**

## 📜 Giấy phép

Distributed under the MIT License. See `LICENSE` for more information.

## 👥 Tác giả & Đóng góp

- **Main Developer**: @dammanhdungvn
- **Email**: dammanhdungvn@gmail.com

---

⭐ **Nếu project này hữu ích, hãy cho một star để ủng hộ!**
