-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 03, 2024 at 09:46 AM
-- Server version: 10.6.17-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chanchal_dcareclinic`
--

-- --------------------------------------------------------

--
-- Table structure for table `accessories`
--

CREATE TABLE `accessories` (
  `acc_id` int(11) NOT NULL,
  `acc_name` varchar(100) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `acc_properties` text DEFAULT NULL,
  `acc_type_id` int(11) DEFAULT NULL,
  `acc_amount` int(11) DEFAULT 0,
  `acc_cost` float NOT NULL,
  `acc_unit_id` int(11) DEFAULT NULL,
  `acc_status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `acc_type`
--

CREATE TABLE `acc_type` (
  `acc_type_id` int(11) NOT NULL,
  `acc_type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `branch_id` int(11) NOT NULL,
  `branch_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch`
--

INSERT INTO `branch` (`branch_id`, `branch_name`) VALUES
(1, 'Demo 1'),
(2, 'Demo 2'),
(3, 'Demo 3');

-- --------------------------------------------------------

--
-- Table structure for table `clinic_closures`
--

CREATE TABLE `clinic_closures` (
  `id` int(11) NOT NULL,
  `closure_date` date NOT NULL,
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clinic_hours`
--

CREATE TABLE `clinic_hours` (
  `id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `course_id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `course_name` varchar(100) DEFAULT NULL,
  `course_detail` text DEFAULT NULL,
  `course_price` int(6) DEFAULT NULL,
  `course_amount` int(3) DEFAULT NULL,
  `course_type_id` int(10) DEFAULT NULL,
  `course_start` date DEFAULT NULL,
  `course_end` date DEFAULT NULL,
  `course_pic` varchar(100) DEFAULT 'course.png',
  `course_note` text DEFAULT NULL,
  `course_status` int(3) DEFAULT NULL,
  `duration` int(11) DEFAULT 60
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course_id`, `branch_id`, `course_name`, `course_detail`, `course_price`, `course_amount`, `course_type_id`, `course_start`, `course_end`, `course_pic`, `course_note`, `course_status`, `duration`) VALUES
(4, 1, 'โบท็อกซ์ลดริ้วรอย', 'การฉีดโบท็อกซ์เพื่อลดเลือนริ้วรอยบนใบหน้า', 15000, 1, 1, '2024-04-23', '2025-04-21', '66c9f0beb125d.jpg', 'เหมาะสำหรับผู้ที่มีริ้วรอยบนใบหน้า', 1, 60),
(5, 1, 'ฟิลเลอร์เติมเต็มร่องลึก', 'การฉีดฟิลเลอร์เพื่อเติมเต็มร่องลึกบนใบหน้า', 20000, 1, 1, '2024-05-07', '2025-05-05', '66c9f12a1170d.jpg', 'ช่วยเพิ่มความอ่อนเยาว์ให้ใบหน้า', 1, 60),
(6, 1, 'เลเซอร์กำจัดขน', 'การใช้เลเซอร์เพื่อกำจัดขนถาวร', 30000, 6, 2, '2024-05-23', '2024-11-20', '66c9f15d49841.jpg', 'ผลลัพธ์ที่ดีที่สุดหลังการทำ 6 ครั้ง', 1, 60),
(7, 1, 'ร้อยไหมหน้าเรียว', 'การร้อยไหมเพื่อยกกระชับใบหน้า', 35000, 1, 1, '2024-06-06', '2025-06-04', '66c9f20f6a38f.jpg', 'ผลลัพธ์อยู่ได้นาน 1-2 ปี', 1, 60),
(8, 1, 'ทรีทเมนต์หน้าใส', 'การทำทรีทเมนต์เพื่อฟื้นฟูผิวหน้าให้กระจ่างใส', 5000, 5, 3, '2024-06-23', '2024-11-20', '66c9f23ecf061.jpg', 'แนะนำให้ทำต่อเนื่องเพื่อผลลัพธ์ที่ดีที่สุด', 1, 60),
(9, 1, 'ปรับรูปหน้า V-Shape', 'การฉีดและการทำทรีทเมนต์เพื่อปรับรูปหน้าให้เป็นทรง V', 50000, 3, 1, '2024-11-15', '2025-05-14', '66c9f0beb125d.jpg', 'รวมการฉีดโบท็อกซ์และฟิลเลอร์', 1, 60),
(10, 1, 'ยกกระชับด้วยอัลตร้าซาวด์', 'การใช้อัลตร้าซาวด์เพื่อยกกระชับผิวหน้า', 40000, 3, 2, '2024-12-01', '2025-02-28', '66c9f0beb125d.jpg', 'ไม่เจ็บ ไม่มีดาวน์ไทม์', 1, 60),
(11, 1, 'ฉีดผิวขาวใส', 'การฉีดวิตามินเพื่อให้ผิวขาวกระจ่างใส', 10000, 5, 1, '2024-12-15', '2025-05-14', '66c9f0beb125d.jpg', 'ผลลัพธ์เห็นชัดหลังทำครบคอร์ส', 1, 60),
(12, 1, 'กำจัดสิว รอยสิว', 'การรักษาสิวและรอยสิวด้วยเลเซอร์และทรีทเมนต์', 25000, 5, 2, '2025-01-01', '2025-05-31', '66c9f0beb125d.jpg', 'เหมาะสำหรับผู้ที่มีปัญหาสิวเรื้อรัง', 1, 60),
(13, 1, 'ลดน้ำหนักด้วยเครื่องมือแพทย์', 'การใช้เครื่องมือแพทย์เพื่อลดไขมันและกระชับสัดส่วน', 60000, 10, 2, '2025-01-15', '2025-11-14', '66c9f0beb125d.jpg', 'ควบคู่กับการควบคุมอาหารและออกกำลังกาย', 1, 60),
(14, 1, 'ฟื้นฟูผมร่วง', 'การรักษาผมร่วงด้วยเทคโนโลยีทันสมัย', 30000, 6, 2, '2025-02-01', '2025-07-31', '66c9f0beb125d.jpg', 'ใช้ PRP และเลเซอร์กระตุ้นการงอกของเส้นผม', 1, 60),
(15, 1, 'ศัลยกรรมตาสองชั้น', 'การทำศัลยกรรมเพื่อสร้างตาสองชั้นแบบธรรมชาติ', 50000, 1, 4, '2025-02-15', '2026-02-14', '66c9f0beb125d.jpg', 'การผ่าตัดโดยแพทย์ผู้เชี่ยวชาญ', 1, 60),
(16, 1, 'ปรับโครงหน้าด้วยฟิลเลอร์', 'การฉีดฟิลเลอร์เพื่อปรับโครงหน้าให้สมดุล', 40000, 1, 1, '2025-03-01', '2026-02-28', '66c9f0beb125d.jpg', 'ปรับรูปหน้าโดยไม่ต้องผ่าตัด', 1, 60),
(17, 1, 'ลบรอยสักด้วยเลเซอร์', 'การใช้เลเซอร์เพื่อลบรอยสักที่ไม่ต้องการ', 20000, 5, 2, '2025-03-15', '2025-08-14', '66c9f0beb125d.jpg', 'จำนวนครั้งขึ้นอยู่กับขนาดและสีของรอยสัก', 1, 60),
(18, 1, 'ฟิลเลอร์ริมฝีปากอิ่ม', 'การฉีดฟิลเลอร์เพื่อเพิ่มความอิ่มเอิบให้ริมฝีปาก', 15000, 1, 1, '2025-04-01', '2026-03-31', '66c9f0beb125d.jpg', 'ให้ริมฝีปากดูอิ่มเอิบเป็นธรรมชาติ', 1, 60),
(19, 1, 'ยกคิ้วด้วยโบท็อกซ์', 'การฉีดโบท็อกซ์เพื่อยกคิ้วและเปิดหางตา', 12000, 1, 1, '2025-04-15', '2026-04-14', '66c9f0beb125d.jpg', 'ช่วยให้ดวงตาดูสดใสขึ้น', 1, 60),
(20, 1, 'ลดกราม ปรับทรงหน้า', 'การฉีดโบท็อกซ์เพื่อลดขนาดกรามและปรับทรงหน้า', 25000, 1, 1, '2025-05-01', '2026-04-30', '66c9f0beb125d.jpg', 'ให้ใบหน้าดูเรียวขึ้น', 1, 60),
(21, 1, 'รักษาฝ้า กระ จุดด่างดำ', 'การใช้เลเซอร์และครีมเพื่อรักษาฝ้า กระ และจุดด่างดำ', 30000, 5, 3, '2025-05-15', '2025-10-14', '66c9f0beb125d.jpg', 'ผลลัพธ์ขึ้นอยู่กับความรุนแรงของปัญหาผิว', 1, 60),
(22, 1, 'ฟื้นฟูผิวด้วยเซลล์ต้นกำเนิด', 'การใช้เซลล์ต้นกำเนิดเพื่อฟื้นฟูผิวให้อ่อนเยาว์', 100000, 3, 3, '2025-06-01', '2025-08-31', '66c9f0beb125d.jpg', 'นวัตกรรมล่าสุดในวงการความงาม', 1, 60);

-- --------------------------------------------------------

--
-- Table structure for table `course_bookings`
--

CREATE TABLE `course_bookings` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `cus_id` int(11) NOT NULL,
  `booking_datetime` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `users_id` int(11) NOT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `course_bookings`
--

INSERT INTO `course_bookings` (`id`, `branch_id`, `cus_id`, `booking_datetime`, `created_at`, `users_id`, `status`) VALUES
(3, 1, 1, '2024-08-31 17:00:00', '2024-09-02 04:08:03', 1, 'cancelled'),
(4, 1, 1, '2024-08-31 17:15:00', '2024-09-02 04:08:07', 1, 'cancelled'),
(5, 1, 3, '2024-09-05 09:00:00', '2024-09-01 04:17:35', 1, 'confirmed'),
(6, 1, 13, '2024-09-02 09:00:00', '2024-09-01 05:35:19', 1, 'confirmed'),
(7, 1, 2, '2024-09-02 11:30:00', '2024-09-02 04:06:44', 1, 'confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `course_resources`
--

CREATE TABLE `course_resources` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `resource_type` enum('drug','tool','accessory') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `resource_id` int(11) NOT NULL,
  `quantity` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_type`
--

CREATE TABLE `course_type` (
  `course_type_id` int(11) NOT NULL,
  `course_type_name` varchar(100) NOT NULL,
  `course_type_status` int(3) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `cus_id` int(11) NOT NULL,
  `cus_id_card_number` varchar(13) NOT NULL,
  `cus_birthday` date DEFAULT NULL,
  `cus_firstname` varchar(100) NOT NULL,
  `cus_lastname` varchar(100) NOT NULL,
  `cus_title` varchar(10) NOT NULL,
  `cus_gender` varchar(10) NOT NULL,
  `cus_nickname` varchar(20) DEFAULT NULL,
  `cus_email` varchar(100) DEFAULT NULL,
  `cus_blood` varchar(10) DEFAULT NULL,
  `cus_tel` varchar(15) NOT NULL,
  `cus_drugallergy` text DEFAULT NULL,
  `cus_congenital` text DEFAULT NULL,
  `cus_remark` text DEFAULT NULL,
  `cus_address` varchar(100) NOT NULL,
  `cus_district` varchar(100) NOT NULL,
  `cus_city` varchar(100) NOT NULL,
  `cus_province` varchar(100) NOT NULL,
  `cus_postal_code` varchar(10) NOT NULL,
  `cus_image` varchar(100) DEFAULT 'customer,png',
  `cus_status` int(5) NOT NULL DEFAULT 1,
  `line_user_id` varchar(255) DEFAULT NULL,
  `line_display_name` varchar(255) DEFAULT NULL,
  `line_picture_url` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`cus_id`, `cus_id_card_number`, `cus_birthday`, `cus_firstname`, `cus_lastname`, `cus_title`, `cus_gender`, `cus_nickname`, `cus_email`, `cus_blood`, `cus_tel`, `cus_drugallergy`, `cus_congenital`, `cus_remark`, `cus_address`, `cus_district`, `cus_city`, `cus_province`, `cus_postal_code`, `cus_image`, `cus_status`, `line_user_id`, `line_display_name`, `line_picture_url`) VALUES
(1, '1819900189796', '2024-08-15', 'สน', '123', 'นาย', 'ชาย', '123', '123@ef.wef', 'A+', '123123', '123', '123', '123', '123', '123', '12', '1233', '123', 'customer.png', 1, NULL, NULL, NULL),
(2, '2345678901234', '1995-02-15', 'สมหญิง', 'ใจเย็น', 'นางสาว', 'หญิง', 'หญิง', 'somying@example.com', 'B', '0823456789', 'ยา penicillin', NULL, NULL, '456 หมู่ 5', 'บางรัก', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10500', 'customer.png', 1, NULL, NULL, NULL),
(3, '3456789012345', '1985-03-30', 'สมศักดิ์', 'รักเรียน', 'นาย', 'ชาย', 'ศักดิ์', 'somsak@example.com', 'O', '0834567890', NULL, 'โรคหัวใจ', NULL, '789 หมู่ 6', 'ห้วยขวาง', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10310', 'customer.png', 1, NULL, NULL, NULL),
(4, '123', '1995-11-02', '123', '123', 'นาย', 'ชาย', '', '', 'A+', '1123', '123', '123', '123', '123', '123', '123', '', '123', 'customer.png', 1, NULL, NULL, NULL),
(5, '5678901234567', '1992-05-25', 'สมหมาย', 'ใจสู้', 'นาย', 'ชาย', 'หมาย', 'sommai@example.com', 'A', '0856789012', 'ยาแอสไพริน', NULL, NULL, '234 หมู่ 8', 'บางกะปิ', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10240', 'customer.png', 1, NULL, NULL, NULL),
(6, '6789012345678', '1988-06-20', 'สมใจ', 'ใจถึง', 'นางสาว', 'หญิง', 'ใจ', 'somjai@example.com', 'B', '0867890123', NULL, 'โรคเบาหวาน', NULL, '567 หมู่ 9', 'ลาดพร้าว', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10230', 'customer.png', 1, NULL, NULL, NULL),
(7, '7890123456789', '1997-07-05', 'สมคิด', 'ใจกว้าง', 'นาย', 'ชาย', 'คิด', 'somkit@example.com', 'O', '0878901234', NULL, NULL, NULL, '890 หมู่ 10', 'บางเขน', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10220', 'customer.png', 1, NULL, NULL, NULL),
(8, '8901234567890', '1983-08-18', 'สมรัก', 'ใจดี', 'นาง', 'หญิง', 'รัก', 'somrak@example.com', 'AB', '0889012345', 'อาหารทะเล', NULL, NULL, '123 หมู่ 1', 'บางซื่อ', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10800', 'customer.png', 1, NULL, NULL, NULL),
(9, '9012345678901', '2002-09-22', 'สมหวัง', 'ใจเย็น', 'นาย', 'ชาย', 'หวัง', 'somwang@example.com', 'A', '0890123456', NULL, 'โรคความดันโลหิตสูง', NULL, '456 หมู่ 2', 'ดุสิต', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10300', 'customer.png', 1, NULL, NULL, NULL),
(10, '0123456789012', '1994-10-08', 'สมบูรณ์', 'ใจสู้', 'นาย', 'ชาย', 'บูรณ์', 'somboon@example.com', 'B', '0901234567', NULL, NULL, NULL, '789 หมู่ 3', 'พญาไท', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10400', 'customer.png', 1, NULL, NULL, NULL),
(11, '1234567890123', '1990-01-01', 'สมชาย', 'ใจดี', 'นาย', 'ชาย', 'ชาย', 'somchai@example.com', 'A', '0812345678', NULL, NULL, NULL, '123 หมู่ 4', 'เมือง', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10100', 'customer.png', 1, NULL, NULL, NULL),
(12, '4567890123456', '2000-04-10', 'สมพร', 'ใจบุญ', 'นาง', 'หญิง', 'พร', 'somporn@example.com', 'AB', '0845678901', NULL, NULL, 'แพ้ฝุ่น', '101 หมู่ 7', 'จตุจักร', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10900', 'customer.png', 1, NULL, NULL, NULL),
(13, '', NULL, '', '', '', '', NULL, 'asdas@wefw.e', NULL, '1234234234', NULL, NULL, NULL, '', '', '', '', '', 'customer,png', 1, 'U4ff5ebe11da5e7e2698cd4cb9a6e8786', 'Max', 'https://profile.line-scdn.net/0hL1AsKHJDEx5sCgbSsVptYRxaEHRPe0oMQDsIfQkNRC1ZO1BAFGxdKw0DHSkFaQAdQG9dflFdHy1gGWR4clzvKms6Ti9QPVRAQm9a_A');

-- --------------------------------------------------------

--
-- Table structure for table `drug`
--

CREATE TABLE `drug` (
  `drug_id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `drug_name` varchar(100) DEFAULT NULL,
  `drug_type_id` int(11) DEFAULT NULL,
  `drug_properties` text DEFAULT NULL,
  `drug_advice` varchar(200) DEFAULT NULL,
  `drug_warning` varchar(200) DEFAULT NULL,
  `drug_amount` int(11) DEFAULT 0,
  `drug_unit_id` int(11) DEFAULT NULL,
  `drug_cost` float NOT NULL,
  `drug_pic` varchar(100) NOT NULL DEFAULT 'drug.png',
  `drug_status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drug_type`
--

CREATE TABLE `drug_type` (
  `drug_type_id` int(11) NOT NULL,
  `drug_type_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_course`
--

CREATE TABLE `order_course` (
  `oc_id` int(11) NOT NULL,
  `cus_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `course_bookings_id` int(11) NOT NULL,
  `order_datetime` datetime NOT NULL,
  `order_payment` varchar(50) DEFAULT NULL,
  `order_net_total` int(11) DEFAULT NULL,
  `order_payment_date` datetime DEFAULT NULL,
  `order_status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_course`
--

INSERT INTO `order_course` (`oc_id`, `cus_id`, `users_id`, `course_bookings_id`, `order_datetime`, `order_payment`, `order_net_total`, `order_payment_date`, `order_status`) VALUES
(9, 1, 1, 3, '2024-08-31 15:01:29', 'ยังไม่จ่ายเงิน', 35000, NULL, 1),
(10, 1, 1, 4, '2024-08-31 15:10:29', 'เงินสด', 50000, NULL, 1),
(11, 3, 1, 5, '2024-09-01 11:17:35', 'ยังไม่จ่ายเงิน', 35000, NULL, 1),
(12, 13, 1, 6, '2024-09-01 12:35:19', 'เงินสด', 35000, NULL, 1),
(21, 2, 1, 7, '2024-09-02 11:06:44', 'ยังไม่จ่ายเงิน', 45000, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `od_id` int(11) NOT NULL,
  `oc_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `od_amount` int(11) NOT NULL,
  `od_price` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`od_id`, `oc_id`, `course_id`, `od_amount`, `od_price`) VALUES
(5, 9, 4, 1, 15000),
(6, 9, 5, 1, 20000),
(7, 10, 6, 1, 30000),
(8, 10, 5, 1, 20000),
(9, 11, 4, 1, 15000),
(10, 11, 5, 1, 20000),
(11, 12, 4, 1, 15000),
(12, 12, 5, 1, 20000),
(21, 21, 4, 1, 15000),
(22, 21, 6, 1, 30000);

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `position_id` int(11) NOT NULL COMMENT 'รหัสตำแหน่ง',
  `position_name` varchar(50) NOT NULL COMMENT 'ชื่อตำแหน่ง'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_transactions`
--

CREATE TABLE `stock_transactions` (
  `transaction_id` int(11) NOT NULL,
  `transaction_date` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `quantity` float NOT NULL,
  `expiry_date` varchar(15) DEFAULT NULL,
  `cost_per_unit` float NOT NULL,
  `stock_type` enum('drug','accessory','tool') NOT NULL,
  `related_id` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT 1,
  `branch_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tool`
--

CREATE TABLE `tool` (
  `tool_id` int(11) NOT NULL,
  `tool_name` varchar(100) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `tool_detail` varchar(200) DEFAULT NULL,
  `tool_amount` int(11) DEFAULT NULL,
  `tool_cost` float NOT NULL,
  `tool_unit_id` int(11) DEFAULT NULL,
  `tool_status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `unit`
--

CREATE TABLE `unit` (
  `unit_id` int(11) NOT NULL,
  `unit_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `users_id` int(11) NOT NULL,
  `users_username` varchar(50) NOT NULL,
  `users_password` varchar(50) NOT NULL,
  `users_fname` varchar(100) NOT NULL,
  `users_lname` varchar(100) NOT NULL,
  `users_nickname` varchar(50) NOT NULL,
  `users_tel` varchar(12) NOT NULL,
  `position_id` int(11) NOT NULL,
  `users_license` varchar(20) DEFAULT NULL,
  `branch_id` int(11) NOT NULL,
  `users_status` int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`users_id`, `users_username`, `users_password`, `users_fname`, `users_lname`, `users_nickname`, `users_tel`, `position_id`, `users_license`, `branch_id`, `users_status`) VALUES
(1, 'admin', 'Toshibal_123', 'ผู้ดูแลระบบ', '..', '', '', 1, '', 0, 1),
(51, '123', '123', '123', '123', '123', '00000', 2, NULL, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accessories`
--
ALTER TABLE `accessories`
  ADD PRIMARY KEY (`acc_id`);

--
-- Indexes for table `acc_type`
--
ALTER TABLE `acc_type`
  ADD PRIMARY KEY (`acc_type_id`);

--
-- Indexes for table `branch`
--
ALTER TABLE `branch`
  ADD PRIMARY KEY (`branch_id`);

--
-- Indexes for table `clinic_closures`
--
ALTER TABLE `clinic_closures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clinic_hours`
--
ALTER TABLE `clinic_hours`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `course_bookings`
--
ALTER TABLE `course_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cus_id` (`cus_id`);

--
-- Indexes for table `course_resources`
--
ALTER TABLE `course_resources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_type`
--
ALTER TABLE `course_type`
  ADD PRIMARY KEY (`course_type_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cus_id`),
  ADD UNIQUE KEY `line_user_id` (`line_user_id`);

--
-- Indexes for table `drug`
--
ALTER TABLE `drug`
  ADD PRIMARY KEY (`drug_id`);

--
-- Indexes for table `drug_type`
--
ALTER TABLE `drug_type`
  ADD PRIMARY KEY (`drug_type_id`);

--
-- Indexes for table `order_course`
--
ALTER TABLE `order_course`
  ADD PRIMARY KEY (`oc_id`),
  ADD KEY `cus_id` (`cus_id`),
  ADD KEY `users_id` (`users_id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`od_id`),
  ADD KEY `oc_id` (`oc_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`position_id`);

--
-- Indexes for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `transaction_date` (`transaction_date`),
  ADD KEY `stock_type` (`stock_type`,`related_id`),
  ADD KEY `expiry_date` (`expiry_date`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `fk_stock_user` (`users_id`);

--
-- Indexes for table `tool`
--
ALTER TABLE `tool`
  ADD PRIMARY KEY (`tool_id`),
  ADD KEY `tool_unit_id` (`tool_unit_id`);

--
-- Indexes for table `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`unit_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`users_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accessories`
--
ALTER TABLE `accessories`
  MODIFY `acc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `acc_type`
--
ALTER TABLE `acc_type`
  MODIFY `acc_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branch`
--
ALTER TABLE `branch`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `clinic_closures`
--
ALTER TABLE `clinic_closures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clinic_hours`
--
ALTER TABLE `clinic_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `course_bookings`
--
ALTER TABLE `course_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `course_resources`
--
ALTER TABLE `course_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_type`
--
ALTER TABLE `course_type`
  MODIFY `course_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `drug`
--
ALTER TABLE `drug`
  MODIFY `drug_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drug_type`
--
ALTER TABLE `drug_type`
  MODIFY `drug_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_course`
--
ALTER TABLE `order_course`
  MODIFY `oc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `od_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `position`
--
ALTER TABLE `position`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสตำแหน่ง';

--
-- AUTO_INCREMENT for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tool`
--
ALTER TABLE `tool`
  MODIFY `tool_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unit`
--
ALTER TABLE `unit`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `course_bookings`
--
ALTER TABLE `course_bookings`
  ADD CONSTRAINT `course_bookings_ibfk_2` FOREIGN KEY (`cus_id`) REFERENCES `customer` (`cus_id`);

--
-- Constraints for table `order_course`
--
ALTER TABLE `order_course`
  ADD CONSTRAINT `order_course_ibfk_1` FOREIGN KEY (`cus_id`) REFERENCES `customer` (`cus_id`),
  ADD CONSTRAINT `order_course_ibfk_2` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`),
  ADD CONSTRAINT `order_course_ibfk_3` FOREIGN KEY (`course_bookings_id`) REFERENCES `course_bookings` (`id`);

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`oc_id`) REFERENCES `order_course` (`oc_id`),
  ADD CONSTRAINT `order_detail_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`);

--
-- Constraints for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  ADD CONSTRAINT `fk_stock_branch` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`),
  ADD CONSTRAINT `fk_stock_user` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`);

--
-- Constraints for table `tool`
--
ALTER TABLE `tool`
  ADD CONSTRAINT `tool_ibfk_1` FOREIGN KEY (`tool_unit_id`) REFERENCES `unit` (`unit_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
