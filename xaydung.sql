-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 23, 2025 at 07:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xaydung`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `AdminID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `FullName` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `RoleID` int(11) NOT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedAt` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`AdminID`, `Username`, `PasswordHash`, `FullName`, `Email`, `RoleID`, `IsActive`, `CreatedAt`) VALUES
(1, 'Admin', '$2y$10$HDK7ptngOYbjWDbSCh9lau6Hpn6y9PsiiITAQ7gWvX7XToAJi0sMW', 'Nguyen Viet Hoannn', 'nguyenviethoan@gmail.com', 1, 1, '2025-05-22 00:46:55');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `BrandID` int(11) NOT NULL,
  `BrandName` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `LogoPath` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `ImagePath` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`CategoryID`, `CategoryName`, `Description`, `ImagePath`) VALUES
(2, 'Xi măng', 'Xi măng', 'https://kfa.vn/wp-content/uploads/2020/05/So-sanh-su-khac-nhau-giua-xi-mang-PC40-va-xi-mang-PCB-40.png'),
(3, 'Gạch xây nhà', 'Gạch dùng để xây nhà', 'https://th.bing.com/th/id/OIP.XC4sGIzamsq20Zy6mmTmzgHaG8?rs=1&pid=ImgDetMain'),
(4, 'Mái tôn', 'Mái tôn lợp', 'https://th.bing.com/th/id/OIP.Mlg_KvQQ6YH15z9Q3eqR0QHaHa?rs=1&pid=ImgDetMain');

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `OrderItemID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL CHECK (`Quantity` > 0),
  `PriceAtOrder` decimal(12,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orderitems`
--

INSERT INTO `orderitems` (`OrderItemID`, `OrderID`, `ProductID`, `Quantity`, `PriceAtOrder`) VALUES
(1, 1, 1, 4, 20000),
(2, 2, 16, 5, 3000),
(3, 3, 17, 5, 4000);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `CustomerName` varchar(100) NOT NULL,
  `CustomerPhone` varchar(15) NOT NULL,
  `CustomerAddress` text NOT NULL,
  `OrderDate` datetime DEFAULT current_timestamp(),
  `TotalAmount` decimal(15,0) NOT NULL CHECK (`TotalAmount` >= 0),
  `Status` varchar(50) DEFAULT 'Mới',
  `Notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`OrderID`, `UserID`, `CustomerName`, `CustomerPhone`, `CustomerAddress`, `OrderDate`, `TotalAmount`, `Status`, `Notes`) VALUES
(1, 1, 'Nguyen Viet Hoan', '0978461855', 'Số 30 thôn An sơn 2', '2025-05-22 00:07:24', 80000, 'Hoàn thành', ''),
(2, 1, 'Nguyen Viet Hoan', '0978461855', 'Số 30 thôn An sơn 2', '2025-05-22 14:51:15', 15000, 'Đang giao', ''),
(3, 1, 'Nguyen Viet Hoan', '0978461855', 'Thôn An sơn 2', '2025-05-22 15:20:07', 20000, 'Đang xử lý', '');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `ProductID` int(11) NOT NULL,
  `ProductName` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `ImagePath` varchar(255) DEFAULT NULL,
  `Price` decimal(12,0) NOT NULL CHECK (`Price` >= 0),
  `Unit` varchar(50) NOT NULL,
  `StockQuantity` int(11) NOT NULL DEFAULT 0 CHECK (`StockQuantity` >= 0),
  `CategoryID` int(11) DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `UpdatedAt` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `IsDeleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`ProductID`, `ProductName`, `Description`, `ImagePath`, `Price`, `Unit`, `StockQuantity`, `CategoryID`, `CreatedAt`, `UpdatedAt`, `IsDeleted`) VALUES
(11, 'Xi măng COTEC', '', 'https://xaydungbaominh.com/wp-content/uploads/2022/08/xi-mang-cotec.jpg', 20000, 'bao', 30, 2, '2025-05-22 14:40:29', '2025-05-22 14:40:29', 0),
(12, 'Xi măng Chinfon', '', 'https://th.bing.com/th/id/OIP.EoGdBuoCP9oteZNn_eastAHaHa?rs=1&pid=ImgDetMain', 30000, 'bao', 43, 2, '2025-05-22 14:42:12', '2025-05-22 14:42:12', 0),
(13, 'Xi măng Hà Tiên', '', 'https://cdn.hoasenhome.vn/catalog/product/x/i/xi-mang-vicem-ha-tien-da-dung-50kg.jpg', 34000, 'bao', 45, 2, '2025-05-22 14:43:03', '2025-05-22 14:43:03', 0),
(14, 'Xi măng INSEE (Holcim)', '', 'https://th.bing.com/th/id/OIP.vSx9Zxu91uzKMN1EOK34hwAAAA?rs=1&pid=ImgDetMain', 21000, 'bao', 37, 2, '2025-05-22 14:43:53', '2025-05-22 14:43:53', 0),
(15, 'Gạch đất nung (gạch đỏ truyền thống)', 'https://th.bing.com/th/id/OIP.A148gaz2DWWSO4a2KmtNVwHaE6?rs=1&pid=ImgDetMain', 'https://th.bing.com/th/id/OIP.A148gaz2DWWSO4a2KmtNVwHaE6?rs=1&pid=ImgDetMain', 2000, 'viên', 1240, 3, '2025-05-22 14:45:11', '2025-05-22 14:45:11', 0),
(16, 'Gạch 3 và 5 lỗ', '', 'https://5.imimg.com/data5/SELLER/Default/2022/4/NV/GH/FX/150539626/clay-bricks-500x500.jpg', 3000, 'viên', 416, 3, '2025-05-22 14:46:36', '2025-05-22 14:51:15', 0),
(17, 'Gạch 4 lỗ', '', 'https://vatlieuxaydungcmc.vn/wp-content/uploads/2021/01/gach-4-lo-la-gi.png', 4000, 'viên', 2126, 3, '2025-05-22 14:47:35', '2025-05-22 15:20:07', 0),
(18, 'Gạch 6 lỗ', '', 'https://vietnhatcorp.com/uploads/pictures/626288e71456436bf9306791/content_gach-6-lo__4_.jpg', 4000, 'viên', 435, 3, '2025-05-22 14:49:43', '2025-05-22 14:50:08', 0),
(19, 'Mái tôn lạnh', 'Mái tôn mát lạnh', 'https://th.bing.com/th/id/R.2be26c7585fc46ffde793e67c5f73504?rik=xcd5crE3pslrIQ&pid=ImgRaw&r=0', 20000, 'tấm', 302, 4, '2025-05-22 15:23:44', '2025-05-22 15:23:44', 0);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `ReviewID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Rating` int(11) NOT NULL,
  `Comment` text DEFAULT NULL,
  `ReviewDate` datetime NOT NULL DEFAULT current_timestamp(),
  `Status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `RoleID` int(11) NOT NULL,
  `RoleName` varchar(50) NOT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`RoleID`, `RoleName`, `Description`) VALUES
(1, 'SuperAdmin', 'Quản trị viên cấp cao nhất, có toàn quyền quản lý hệ thống.'),
(2, 'Staff', 'Nhân viên quản lý sản phẩm và đơn hàng.');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `SettingID` int(11) NOT NULL,
  `SettingKey` varchar(50) NOT NULL,
  `SettingValue` text DEFAULT NULL,
  `SettingDescription` varchar(255) DEFAULT NULL,
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`SettingID`, `SettingKey`, `SettingValue`, `SettingDescription`, `UpdatedAt`) VALUES
(1, 'site_name', 'Xây Dựng Shop', 'Tên website', '2025-05-22 06:31:03'),
(2, 'site_description', 'Cửa hàng vật liệu xây dựng', 'Mô tả website', '2025-05-22 06:31:03'),
(3, 'contact_email', 'contact@xaydungshop.com', 'Email liên hệ', '2025-05-22 06:31:03'),
(4, 'contact_phone', '0123456789', 'Số điện thoại liên hệ', '2025-05-22 06:31:03'),
(5, 'contact_address', '123 Đường ABC, Quận XYZ, TP.HCM', 'Địa chỉ liên hệ', '2025-05-22 06:31:03'),
(6, 'facebook_url', 'https://facebook.com/xaydungshop', 'Link Facebook', '2025-05-22 06:31:03'),
(7, 'youtube_url', 'https://youtube.com/xaydungshop', 'Link YouTube', '2025-05-22 06:31:03'),
(8, 'instagram_url', 'https://instagram.com/xaydungshop', 'Link Instagram', '2025-05-22 06:31:03'),
(9, 'footer_text', '© 2024 Xây Dựng Shop. All rights reserved.', 'Text footer', '2025-05-22 06:31:03'),
(10, 'currency', 'VND', 'Đơn vị tiền tệ', '2025-05-22 06:31:03'),
(11, 'tax_rate', '10', 'Thuế VAT (%)', '2025-05-22 06:31:03'),
(12, 'shipping_fee', '30000', 'Phí vận chuyển cơ bản', '2025-05-22 06:31:03'),
(13, 'free_shipping_threshold', '1000000', 'Ngưỡng miễn phí vận chuyển', '2025-05-22 06:31:03'),
(14, 'maintenance_mode', '0', 'Chế độ bảo trì (0: tắt, 1: bật)', '2025-05-22 06:31:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `PhoneNumber` varchar(15) DEFAULT NULL,
  `Address` text DEFAULT NULL,
  `CreatedAt` datetime DEFAULT current_timestamp(),
  `IsActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `FullName`, `Email`, `PasswordHash`, `PhoneNumber`, `Address`, `CreatedAt`, `IsActive`) VALUES
(1, 'Nguyen Viet Hoan', 'nguyenviethoan@gmail.com', '$2y$10$6DNC7P3GGreBMJ1sBlryt.twYIYnehiPFsLdfvyQL3AXtwYp9ldGW', '0978461855', 'Thôn An sơn 2', '2025-05-21 23:13:00', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`AdminID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `RoleID` (`RoleID`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`BrandID`),
  ADD UNIQUE KEY `BrandName` (`BrandName`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`CategoryID`),
  ADD UNIQUE KEY `CategoryName` (`CategoryName`);

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`OrderItemID`),
  ADD UNIQUE KEY `OrderID` (`OrderID`,`ProductID`),
  ADD KEY `ProductID` (`ProductID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `ProductID` (`ProductID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`RoleID`),
  ADD UNIQUE KEY `RoleName` (`RoleName`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`SettingID`),
  ADD UNIQUE KEY `SettingKey` (`SettingKey`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `PhoneNumber` (`PhoneNumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `BrandID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `OrderItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `RoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `SettingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`RoleID`) REFERENCES `roles` (`RoleID`);

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `categories` (`CategoryID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`ProductID`) REFERENCES `products` (`ProductID`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
