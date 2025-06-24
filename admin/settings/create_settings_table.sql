-- Tạo bảng Settings
CREATE TABLE IF NOT EXISTS `Settings` (
  `SettingID` int(11) NOT NULL AUTO_INCREMENT,
  `SettingKey` varchar(50) NOT NULL,
  `SettingValue` text DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`SettingID`),
  UNIQUE KEY `SettingKey` (`SettingKey`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Thêm dữ liệu mặc định
INSERT INTO `Settings` (`SettingKey`, `SettingValue`, `Description`) VALUES
('SiteName', 'Xây Dựng Hoàn', 'Tên website'),
('SiteDescription', 'Công ty xây dựng chuyên nghiệp', 'Mô tả website'),
('ContactEmail', 'contact@xaydunghoan.com', 'Email liên hệ'),
('ContactPhone', '0123456789', 'Số điện thoại liên hệ'),
('Address', '123 Đường ABC, Quận XYZ, TP. HCM', 'Địa chỉ công ty'),
('Facebook', 'https://facebook.com/xaydunghoan', 'Link Facebook'),
('Instagram', 'https://instagram.com/xaydunghoan', 'Link Instagram'),
('Twitter', 'https://twitter.com/xaydunghoan', 'Link Twitter'),
('Youtube', 'https://youtube.com/xaydunghoan', 'Link Youtube'),
('LogoPath', 'assets/images/logo.png', 'Đường dẫn logo'),
('FaviconPath', 'assets/images/favicon.ico', 'Đường dẫn favicon'),
('FooterText', '© 2024 Xây Dựng Hoàn. All rights reserved.', 'Nội dung footer'),
('MetaKeywords', 'xây dựng, thiết kế, kiến trúc, nhà đẹp', 'Từ khóa SEO'),
('MetaDescription', 'Công ty xây dựng chuyên nghiệp với hơn 10 năm kinh nghiệm', 'Mô tả SEO'); 