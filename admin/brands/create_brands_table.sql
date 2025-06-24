-- Tạo bảng Brands
CREATE TABLE IF NOT EXISTS `Brands` (
  `BrandID` int(11) NOT NULL AUTO_INCREMENT,
  `BrandName` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `LogoPath` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`BrandID`),
  UNIQUE KEY `BrandName` (`BrandName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; 