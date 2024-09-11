-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 11, 2024 at 10:17 PM
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

--
-- Dumping data for table `accessories`
--

INSERT INTO `accessories` (`acc_id`, `acc_name`, `branch_id`, `acc_properties`, `acc_type_id`, `acc_amount`, `acc_cost`, `acc_unit_id`, `acc_status`) VALUES
(1, 'เครื่องวัดความดันโลหิตดิจิทัล', 1, 'วัดความดันโลหิตอัตโนมัติ แสดงผลบนจอ LCD', 7, 240, 120, 1, 1),
(2, 'เตียงผู้ป่วยไฟฟ้า', 1, 'ปรับระดับด้วยรีโมทคอนโทรล รับน้ำหนักได้สูงสุด 200 กก.', 8, 0, 2500, 10, 1),
(3, 'เครื่องกระตุกหัวใจไฟฟ้าแบบอัตโนมัติ (AED)', 1, 'ใช้งานง่าย มีคำแนะนำเสียงภาษาไทย', 4, 0, 5000, 1, 1),
(4, 'เครื่องช่วยหายใจแบบพกพา', 1, 'ใช้แบตเตอรี่ ทำงานได้ต่อเนื่อง 8 ชั่วโมง', 2, 0, 3000, 1, 1),
(5, 'เครื่องอัลตราซาวด์', 1, 'ความละเอียดสูง มีโหมดการทำงานหลากหลาย', 3, 0, 8000, 1, 1),
(6, 'ชุดเครื่องมือผ่าตัดทั่วไป', 1, 'ผลิตจากสแตนเลสคุณภาพสูง ปราศจากเชื้อ', 1, 0, 150, 2, 1),
(7, 'รถเข็นทำแผล', 1, 'มีล้อล็อคได้ พร้อมถาดสแตนเลส', 12, 0, 500, 1, 1),
(8, 'เครื่องฉายแสง UV ฆ่าเชื้อ', 1, 'ใช้สำหรับฆ่าเชื้อในห้องผ่าตัดและห้องคนไข้', 10, 0, 1000, 1, 1),
(9, 'เครื่องวัดออกซิเจนในเลือด', 1, 'แสดงผลแบบดิจิทัล วัดได้รวดเร็ว', 7, 0, 350, 1, 1),
(10, 'เครื่องดูดเสมหะ', 1, 'แรงดูดปรับได้ ขวดบรรจุขนาด 1 ลิตร', 2, 0, 400, 1, 1),
(11, 'เครื่องวัดอุณหภูมิทางหน้าผากแบบอินฟราเรด', 1, 'วัดได้รวดเร็ว ไม่สัมผัสผิวหนัง', 7, 0, 80, 1, 1),
(12, 'ชุดให้ออกซิเจน', 1, 'ประกอบด้วยถังออกซิเจน หน้ากาก และเกจ์วัด', 2, 0, 200, 2, 1),
(13, 'เครื่องตรวจคลื่นไฟฟ้าหัวใจ (ECG)', 1, '12 ลีด พร้อมซอฟต์แวร์วิเคราะผล', 3, 0, 6000, 1, 1),
(14, 'ถุงมือยางทางการแพทย์', 1, 'ปราศจากแป้ง ไม่ก่อให้เกิดอาการแพ้', 12, 0, 50, 3, 1),
(15, 'เครื่องวัดน้ำตาลในเลือด', 1, 'ใช้เลือดปริมาณน้อย ให้ผลรวดเร็ว', 7, 0, 150, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `acc_type`
--

CREATE TABLE `acc_type` (
  `acc_type_id` int(11) NOT NULL,
  `acc_type_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `acc_type`
--

INSERT INTO `acc_type` (`acc_type_id`, `acc_type_name`) VALUES
(1, 'เครื่องมือผ่าตัด'),
(2, 'อุปกรณ์ช่วยหายใจ'),
(3, 'เครื่องมือตรวจวินิจฉัย'),
(4, 'อุปกรณ์ฉุกเฉิน'),
(5, 'เครื่องมือทันตกรรม'),
(6, 'อุปกรณ์กายภาพบำบัด'),
(7, 'เครื่องมือวัดสัญญาณชีพ'),
(8, 'อุปกรณ์ช่วยเหลือการเคลื่อนไหว'),
(9, 'เครื่องมือห้องปฏิบัติการ'),
(10, 'อุปกรณ์ทำความสะอาดและฆ่าเชื้อ'),
(11, 'เครื่องมือรังสีวิทยา'),
(12, 'อุปกรณ์การพยาบาล'),
(13, 'เครื่องมือจักษุวิทยา'),
(14, 'อุปกรณ์การแพทย์ทางไกล'),
(15, 'เครื่องมือศัลยกรรมพลาสติก');

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

--
-- Dumping data for table `clinic_closures`
--

INSERT INTO `clinic_closures` (`id`, `closure_date`, `reason`) VALUES
(6, '2024-09-17', 'หยุด'),
(7, '2024-09-19', 'วันหยุด');

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

--
-- Dumping data for table `clinic_hours`
--

INSERT INTO `clinic_hours` (`id`, `day_of_week`, `start_time`, `end_time`, `is_closed`) VALUES
(43, 'Monday', '09:00:00', '23:00:00', 0),
(44, 'Tuesday', '09:00:00', '23:00:00', 0),
(45, 'Wednesday', '09:00:00', '23:00:00', 0),
(46, 'Thursday', '09:00:00', '23:00:00', 0),
(47, 'Friday', '09:00:00', '23:00:00', 0),
(48, 'Saturday', '10:00:00', '23:00:00', 0),
(49, 'Sunday', '09:00:00', '11:00:00', 0);

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
(4, 1, 'โบท็อกซ์ลดริ้วรอย', 'การฉีดโบท็อกซ์เพื่อลดเลือนริ้วรอยบนใบหน้า', 15000, 1, 1, '2024-04-20', '2025-04-20', '66c9f0beb125d.jpg', 'เหมาะสำหรับผู้ที่มีริ้วรอยบนใบหน้า', 1, 60),
(5, 1, 'ฟิลเลอร์เติมเต็มร่องลึก', 'การฉีดฟิลเลอร์เพื่อเติมเต็มร่องลึกบนใบหน้า', 20000, 1, 1, '2024-04-20', '2025-04-20', '66c9f12a1170d.jpg', 'ช่วยเพิ่มความอ่อนเยาว์ให้ใบหน้า', 1, 60),
(6, 1, 'เลเซอร์กำจัดขน', 'การใช้เลเซอร์เพื่อกำจัดขนถาวร', 30000, 6, 2, '2024-04-20', '2025-04-20', '66c9f15d49841.jpg', 'ผลลัพธ์ที่ดีที่สุดหลังการทำ 6 ครั้ง', 1, 60),
(7, 1, 'ร้อยไหมหน้าเรียว', 'การร้อยไหมเพื่อยกกระชับใบหน้า', 35000, 1, 1, '2024-04-20', '2025-04-20', '66c9f20f6a38f.jpg', 'ผลลัพธ์อยู่ได้นาน 1-2 ปี', 1, 60),
(8, 1, 'ทรีทเมนต์หน้าใส', 'การทำทรีทเมนต์เพื่อฟื้นฟูผิวหน้าให้กระจ่างใส', 5000, 5, 3, '2024-04-20', '2025-04-20', '66c9f23ecf061.jpg', 'แนะนำให้ทำต่อเนื่องเพื่อผลลัพธ์ที่ดีที่สุด', 1, 60),
(9, 1, 'ปรับรูปหน้า V-Shape', 'การฉีดและการทำทรีทเมนต์เพื่อปรับรูปหน้าให้เป็นทรง V', 50000, 3, 1, '2024-04-20', '2025-04-20', '66c9f0beb125d.jpg', 'รวมการฉีดโบท็อกซ์และฟิลเลอร์', 1, 60),
(10, 1, 'ยกกระชับด้วยอัลตร้าซาวด์', 'การใช้อัลตร้าซาวด์เพื่อยกกระชับผิวหน้า', 40000, 3, 2, '2024-04-20', '2025-04-20', '66c9f0beb125d.jpg', 'ไม่เจ็บ ไม่มีดาวน์ไทม์', 1, 60),
(11, 1, 'ฉีดผิวขาวใส', 'การฉีดวิตามินเพื่อให้ผิวขาวกระจ่างใส', 10000, 5, 1, '2024-04-20', '2025-04-20', '66c9f0beb125d.jpg', 'ผลลัพธ์เห็นชัดหลังทำครบคอร์ส', 1, 60),
(12, 1, 'กำจัดสิว รอยสิว', 'การรักษาสิวและรอยสิวด้วยเลเซอร์และทรีทเมนต์', 25000, 5, 2, '2024-04-20', '2025-04-20', '66c9f0beb125d.jpg', 'เหมาะสำหรับผู้ที่มีปัญหาสิวเรื้อรัง', 1, 60),
(13, 1, 'ลดน้ำหนักด้วยเครื่องมือแพทย์', 'การใช้เครื่องมือแพทย์เพื่อลดไขมันและกระชับสัดส่วน', 60000, 10, 2, '2024-04-20', '2025-04-20', '66c9f0beb125d.jpg', 'ควบคู่กับการควบคุมอาหารและออกกำลังกาย', 1, 60),
(14, 1, 'ฟื้นฟูผมร่วง', 'การรักษาผมร่วงด้วยเทคโนโลยีทันสมัย', 30000, 6, 2, '2024-04-20', '2025-04-20', '66c9f0beb125d.jpg', 'ใช้ PRP และเลเซอร์กระตุ้นการงอกของเส้นผม', 1, 60),
(15, 1, 'ศัลยกรรมตาสองชั้น', 'การทำศัลยกรรมเพื่อสร้างตาสองชั้นแบบธรรมชาติ', 50000, 1, 4, '2024-04-20', '2025-04-20', '66c9f0beb125d.jpg', 'การผ่าตัดโดยแพทย์ผู้เชี่ยวชาญ', 1, 60),
(16, 1, 'ปรับโครงหน้าด้วยฟิลเลอร์', 'การฉีดฟิลเลอร์เพื่อปรับโครงหน้าให้สมดุล', 40000, 1, 1, '2024-04-20', '2025-04-20', '66c9f0beb125d.jpg', 'ปรับรูปหน้าโดยไม่ต้องผ่าตัด', 1, 60),
(17, 1, 'ลบรอยสักด้วยเลเซอร์', 'การใช้เลเซอร์เพื่อลบรอยสักที่ไม่ต้องการ', 20000, 5, 2, '2024-04-20', '2025-04-20', '66c9f0beb125d.jpg', 'จำนวนครั้งขึ้นอยู่กับขนาดและสีของรอยสัก', 1, 60),
(18, 1, 'ฟิลเลอร์ริมฝีปากอิ่ม', 'การฉีดฟิลเลอร์เพื่อเพิ่มความอิ่มเอิบให้ริมฝีปาก', 15000, 1, 1, '2024-04-20', '2025-04-20', '66c9f0beb125d.jpg', 'ให้ริมฝีปากดูอิ่มเอิบเป็นธรรมชาติ', 1, 60),
(19, 1, 'ยกคิ้วด้วยโบท็อกซ์', 'การฉีดโบท็อกซ์เพื่อยกคิ้วและเปิดหางตา', 12000, 1, 1, '2024-04-20', '2025-04-20', '66c9f0beb125d.jpg', 'ช่วยให้ดวงตาดูสดใสขึ้น', 1, 60),
(20, 1, 'ลดกราม ปรับทรงหน้า', 'การฉีดโบท็อกซ์เพื่อลดขนาดกรามและปรับทรงหน้า', 25000, 1, 1, '2024-04-20', '2025-04-20', '66c9f0beb125d.jpg', 'ให้ใบหน้าดูเรียวขึ้น', 1, 60),
(21, 1, 'รักษาฝ้า กระ จุดด่างดำ', 'การใช้เลเซอร์และครีมเพื่อรักษาฝ้า กระ และจุดด่างดำ', 30000, 5, 3, '2024-04-20', '2025-04-20', '66c9f0beb125d.jpg', 'ผลลัพธ์ขึ้นอยู่กับความรุนแรงของปัญหาผิว', 1, 60),
(22, 1, 'ฟื้นฟูผิวด้วยเซลล์ต้นกำเนิด', 'การใช้เซลล์ต้นกำเนิดเพื่อฟื้นฟูผิวให้อ่อนเยาว์', 100000, 3, 3, '2024-04-20', '2025-04-20', '66c9f0beb125d.jpg', 'นวัตกรรมล่าสุดในวงการความงาม', 1, 60),
(23, 2, 'test', 'test', 4989, 1, 1, '2024-08-31', '2025-08-31', '66d83366dac64.png', '..', 1, 60),
(24, 2, 'test', '31082567', 3490, 1, 2, '2024-08-31', '2025-08-31', '66d833eae7551.jpg', '123', 1, 60);

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
  `users_id` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `course_bookings`
--

INSERT INTO `course_bookings` (`id`, `branch_id`, `cus_id`, `booking_datetime`, `created_at`, `users_id`, `status`) VALUES
(23, 1, 13, '2024-09-09 22:45:00', '2024-09-09 13:23:04', 1, 'confirmed'),
(24, 1, 14, '2024-09-09 22:30:00', '2024-09-09 13:23:23', 1, 'confirmed'),
(25, 1, 13, '2024-09-10 10:00:00', '2024-09-10 02:56:58', 1, 'confirmed'),
(26, 1, 13, '2024-09-10 11:15:00', '2024-09-10 02:57:11', 1, 'confirmed'),
(27, 1, 13, '2024-09-10 12:45:00', '2024-09-10 02:57:29', 1, 'confirmed'),
(28, 1, 13, '2024-09-11 14:00:00', '2024-09-11 03:56:19', 1, 'confirmed');

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

--
-- Dumping data for table `course_resources`
--

INSERT INTO `course_resources` (`id`, `course_id`, `resource_type`, `resource_id`, `quantity`, `created_at`, `updated_at`) VALUES
(2, 8, 'drug', 1, 30, '2024-08-24 15:50:38', '2024-08-24 15:50:38'),
(4, 8, 'tool', 5, 1, '2024-08-24 15:53:24', '2024-08-24 15:53:24'),
(6, 8, 'tool', 3, 1.2, '2024-08-24 16:17:27', '2024-08-24 16:17:27'),
(7, 8, 'accessory', 3, 1, '2024-08-24 16:23:35', '2024-08-24 16:23:35');

-- --------------------------------------------------------

--
-- Table structure for table `course_type`
--

CREATE TABLE `course_type` (
  `course_type_id` int(11) NOT NULL,
  `course_type_name` varchar(100) NOT NULL,
  `course_type_status` int(3) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_type`
--

INSERT INTO `course_type` (`course_type_id`, `course_type_name`, `course_type_status`) VALUES
(1, 'ทรีทเมนต์ใบหน้า', 1),
(2, 'ทรีทเมนต์ผิวกาย', 1),
(3, 'ศัลยกรรมความงาม', 1),
(4, 'ลดน้ำหนักและกระชับสัดส่วน', 1),
(5, 'ฟื้นฟูผมและหนังศีรษะ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `course_usage`
--

CREATE TABLE `course_usage` (
  `id` int(11) NOT NULL,
  `od_id` int(11) NOT NULL,
  `queue_id` int(11) NOT NULL,
  `used_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `course_usage`
--

INSERT INTO `course_usage` (`id`, `od_id`, `queue_id`, `used_date`) VALUES
(1, 31, 52, '2024-09-10 23:41:38'),
(2, 33, 52, '2024-09-10 23:41:38');

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
(13, '1819900489796', '1995-11-02', 'สนธยา', 'แข็งแรง', 'นาย', 'ชาย', 'max', 'asdas@wefw.e', 'O+', '1234234234', '-', '-', '-', '107', 'ไสไทย', 'เมือง', 'กระบี่', '81000', 'customer,png', 1, 'U4ff5ebe11da5e7e2698cd4cb9a6e8786', 'Max', 'https://profile.line-scdn.net/0hL1AsKHJDEx5sCgbSsVptYRxaEHRPe0oMQDsIfQkNRC1ZO1BAFGxdKw0DHSkFaQAdQG9dflFdHy1gGWR4clzvKms6Ti9QPVRAQm9a_A'),
(14, '1819900254181', '1998-01-29', 'สุดชญา', 'เจียวก๊ก', 'นางสาว', 'หญิง', 'ตุ๊กติ๊ก', 'tuktik2901@gmail.com', 'B-', '0928121387', 'ไม่มี', 'ภูมิแพ้', 'ไม่มี', '86', 'ปกาสัย', 'เหนือคลอง', 'กระบี่', '81130', 'customer,png', 1, 'Uefd57d73644282d669338a2bde1231a6', 'Sudchaya.Jeawkok', 'https://profile.line-scdn.net/0h2I5s9Q47bWYYCn498PETGWhabgw7ezR0YThyA30OZ1QtOXg5MGQkByQCNwN1bihgZDwiVH4KZwMUGRoABlyRUh86MFckPSo4Nm8khA');

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

--
-- Dumping data for table `drug`
--

INSERT INTO `drug` (`drug_id`, `branch_id`, `drug_name`, `drug_type_id`, `drug_properties`, `drug_advice`, `drug_warning`, `drug_amount`, `drug_unit_id`, `drug_cost`, `drug_pic`, `drug_status`) VALUES
(1, 1, 'พาราเซตามอล', 1, 'ยาแก้ปวด ลดไข้', 'รับประทานหลังอาหารทันที', 'ห้ามใช้เกินขนาดที่กำหนด', 860, 1, 15.5, '66c9aebf20f77.jpg', 1),
(2, 1, 'อะม็อกซีซิลลิน', 2, 'ยาปฏิชีวนะ', 'รับประทานติดต่อกันจนหมด', 'แจ้งแพทย์หากมีอาการแพ้', 0, 1, 30, '', 1),
(3, 2, 'ออมีพราโซล', 3, 'ยารักษาโรคกระเพาะ', 'รับประทานก่อนอาหาร 30 นาที', 'ห้ามใช้ในผู้ที่แพ้ยานี้', 0, 2, 45.75, '', 1),
(4, 2, 'ไอบูโพรเฟน', 1, 'ยาแก้ปวด ต้านการอักเสบ', 'รับประทานหลังอาหารทันที', 'ห้ามใช้ในผู้ที่เป็นโรคกระเพาะ', 0, 1, 22.2, '', 1),
(5, 3, 'เมทฟอร์มิน', 4, 'ยารักษาเบาหวาน', 'รับประทานพร้อมอาหาร', 'ติดตามระดับน้ำตาลในเลือดสม่ำเสมอ', 0, 2, 18, '', 1),
(6, 3, 'ซิมวาสแตติน', 5, 'ยาลดไขมันในเลือด', 'รับประทานก่อนนอน', 'แจ้งแพทย์หากมีอาการปวดกล้ามเนื้อ', 0, 1, 55.3, '', 1),
(7, 1, 'เซอร์ทราลีน', 6, 'ยารักษาโรคซึมเศร้า', 'รับประทานตามแพทย์สั่ง', 'ห้ามหยุดยาทันทีโดยไม่ปรึกษาแพทย์', 0, 2, 60, '', 1),
(8, 2, 'ลอราทาดีน', 7, 'ยาแก้แพ้', 'รับประทานวันละครั้ง', 'อาจทำให้ง่วงซึม', 0, 1, 12.8, '', 1),
(9, 3, 'แอสไพริน', 8, 'ยาต้านการแข็งตัวของเลือด', 'รับประทานหลังอาหารทันที', 'ระวังในผู้ที่มีแนวโน้มเลือดออกง่าย', 0, 1, 10, '', 1),
(10, 1, 'เมโทโพรลอล', 9, 'ยารักษาความดันโลหิตสูง', 'รับประทานในเวลาเดียวกันทุกวัน', 'ห้ามหยุดยาทันทีโดยไม่ปรึกษาแพทย์', 0, 2, 38.4, '', 1),
(11, 2, 'เลโวไทร็อกซิน', 10, 'ยารักษาโรคไทรอยด์', 'รับประทานตอนท้องว่าง', 'ติดตามระดับฮอร์โมนไทรอยด์สม่ำเสมอ', 0, 2, 25.6, '', 1),
(12, 3, 'กาบาเพนติน', 11, 'ยารักษาอาการปวดประสาท', 'เริ่มจากขนาดต่ำและค่อยๆ เพิ่ม', 'อาจทำให้ง่วงซึม', 0, 1, 70.25, '', 1),
(13, 1, 'วาร์ฟาริน', 8, 'ยาต้านการแข็งตัวของเลือด', 'รับประทานตามแพทย์สั่งอย่างเคร่งครัด', 'ติดตาม INR อย่างสม่ำเสมอ', 0, 2, 42.9, '', 1),
(14, 2, 'เมโทเทรกเซต', 12, 'ยารักษาโรคข้ออักเสบรูมาตอยด์', 'รับประทานสัปดาห์ละครั้ง', 'ห้ามใช้ในสตรีมีครรภ์', 0, 2, 120, '', 1),
(15, 3, 'ฟลูอ็อกซิทีน', 6, 'ยารักษาโรคซึมเศร้า', 'รับประทานตอนเช้า', 'อาจต้องใช้เวลา 2-4 สัปดาห์จึงเห็นผล', 0, 1, 35.15, '', 1),
(16, 1, 'อะทอร์วาสแตติน', 5, 'ยาลดไขมันในเลือด', 'รับประทานก่อนนอน', 'ตรวจระดับเอนไซม์ตับเป็นระยะ', 0, 1, 62.7, '', 1),
(17, 2, 'โดมเพอริโดน', 13, 'ยาแก้คลื่นไส้อาเจียน', 'รับประทานก่อนอาหาร 15-30 นาที', 'ไม่ควรใช้ติดต่อกันนานเกิน 7 วัน', 0, 1, 8.5, '', 1),
(18, 3, 'ไรสเพอริโดน', 14, 'ยารักษาโรคจิตเภท', 'รับประทานตามแพทย์สั่ง', 'อาจทำให้น้ำหนักเพิ่ม', 0, 2, 95.8, '', 1),
(19, 1, 'มอนเทลูคาสต์', 1, 'ยารักษาโรคหอบหืด', 'รับประทานก่อนนอน', 'แจ้งแพทย์หากมีอาการทางจิตประสาท', 0, 1, 28.35, '', 0),
(20, 2, 'เซเลโคซิบ', 16, 'ยาแก้ปวดต้านการอักเสบ', 'รับประทานพร้อมอาหาร', 'ระวังในผู้ที่มีความเสี่ยงโรคหัวใจ', 0, 2, 51.2, '', 1),
(21, 1, 'rthrt', 1, 'rthrth', 'rthr', 'rhtrt', 0, 1, 0, 'durg.png', 1),
(22, 1, '1231', 2, '123123', '23123', '234234', 0, 2, 0, 'durg.png', 1),
(23, 1, 'ๅ/-', 4, 'ๅ/-', 'ๅ/-', 'ๅ/-', 0, 5, 0, '66c8082e2b15f.png', 1),
(24, 1, '12e12', 3, '12e12', 'e12e', '12e12', 0, 2, 0, '', 1),
(25, 1, '23r', 3, '23r23r', '23r23', '23r23', 0, 2, 0, '', 1),
(26, 1, '12123', 3, '123123', '123', '12312', 0, 3, 0, '66c80d4ed1afa.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `drug_type`
--

CREATE TABLE `drug_type` (
  `drug_type_id` int(11) NOT NULL,
  `drug_type_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drug_type`
--

INSERT INTO `drug_type` (`drug_type_id`, `drug_type_name`) VALUES
(1, 'ยาแก้ปวดลดไข้'),
(2, 'ยาปฏิชีวนะ'),
(3, 'ยารักษาโรคกระเพาะ'),
(4, 'ยารักษาเบาหวาน'),
(5, 'ยาลดไขมันในเลือด'),
(6, 'ยารักษาโรคซึมเศร้า'),
(7, 'ยาแก้แพ้'),
(8, 'ยาต้านการแข็งตัวของเลือด'),
(9, 'ยารักษาความดันโลหิตสูง'),
(10, 'ยารักษาโรคหัวใจ');

-- --------------------------------------------------------

--
-- Table structure for table `opd`
--

CREATE TABLE `opd` (
  `opd_id` int(11) NOT NULL,
  `queue_id` int(11) NOT NULL,
  `cus_id` int(11) NOT NULL,
  `nurse_id` int(11) DEFAULT NULL,
  `Weight` float DEFAULT NULL,
  `Height` float DEFAULT NULL,
  `BMI` float DEFAULT NULL,
  `FBS` float DEFAULT NULL,
  `Systolic` float DEFAULT NULL,
  `Pulsation` float DEFAULT NULL,
  `opd_diagnose` text DEFAULT NULL,
  `opd_note` text DEFAULT NULL,
  `opd_smoke` varchar(10) DEFAULT NULL,
  `opd_alcohol` varchar(10) DEFAULT NULL,
  `drug_allergy` text DEFAULT NULL,
  `food_allergy` text DEFAULT NULL,
  `opd_physical` varchar(50) DEFAULT NULL,
  `opd_status` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opd`
--

INSERT INTO `opd` (`opd_id`, `queue_id`, `cus_id`, `nurse_id`, `Weight`, `Height`, `BMI`, `FBS`, `Systolic`, `Pulsation`, `opd_diagnose`, `opd_note`, `opd_smoke`, `opd_alcohol`, `drug_allergy`, `food_allergy`, `opd_physical`, `opd_status`, `created_at`, `updated_at`) VALUES
(15, 44, 13, NULL, 130, 180, 40.12, 90, 140, 110, '1', '2', 'ไม่สูบ', 'ไม่ดื่ม', '1', '2', NULL, 1, '2024-09-10 06:40:20', '2024-09-10 06:40:42'),
(16, 51, 13, NULL, 123, 123, 81.3, 123, 123, 123, '123', '123', 'ไม่สูบ', 'ไม่ดื่ม', '123', '123', NULL, 1, '2024-09-10 15:24:13', '2024-09-10 15:24:25'),
(17, 52, 13, NULL, 123, 123, 81.3, 123, 123, 123, '123', '', 'ไม่สูบ', 'ไม่ดื่ม', '123', '123', NULL, 1, '2024-09-10 15:26:17', '2024-09-10 15:26:19'),
(18, 55, 13, NULL, 123, 123, 81.3, 123, 123, 123, '123', '', 'ไม่สูบ', 'ไม่ดื่ม', '123', '123', NULL, 1, '2024-09-11 03:57:01', '2024-09-11 03:57:05'),
(19, 56, 14, NULL, 123, 123, 81.3, 123, 123, 123, '123', '', 'ไม่สูบ', 'ไม่ดื่ม', '123', '123', NULL, 1, '2024-09-11 04:36:23', '2024-09-11 04:36:27');

-- --------------------------------------------------------

--
-- Table structure for table `opd_drawings`
--

CREATE TABLE `opd_drawings` (
  `id` int(11) NOT NULL,
  `opd_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `opd_drawings`
--

INSERT INTO `opd_drawings` (`id`, `opd_id`, `image_path`, `created_at`) VALUES
(99, 16, 'opd_drawing_1725981858.png', '2024-09-10 15:24:18');

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
  `payment_proofs` varchar(50) NOT NULL,
  `order_status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_course`
--

INSERT INTO `order_course` (`oc_id`, `cus_id`, `users_id`, `course_bookings_id`, `order_datetime`, `order_payment`, `order_net_total`, `order_payment_date`, `payment_proofs`, `order_status`) VALUES
(24, 13, 1, 23, '2024-09-09 20:23:04', 'ยังไม่จ่ายเงิน', 15000, NULL, '', 1),
(25, 14, 1, 24, '2024-09-09 20:23:23', 'ยังไม่จ่ายเงิน', 20000, NULL, '', 1),
(26, 13, 1, 25, '2024-09-10 09:56:58', 'ยังไม่จ่ายเงิน', 15000, NULL, '', 1),
(27, 13, 1, 26, '2024-09-10 09:57:11', 'ยังไม่จ่ายเงิน', 5000, NULL, '', 1),
(28, 13, 1, 27, '2024-09-10 09:57:29', 'ยังไม่จ่ายเงิน', 35000, NULL, '', 1),
(29, 13, 1, 28, '2024-09-11 10:56:19', 'ยังไม่จ่ายเงิน', 15000, NULL, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_course_resources`
--

CREATE TABLE `order_course_resources` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `resource_type` enum('drug','tool','accessory') DEFAULT NULL,
  `resource_id` int(11) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_course_resources`
--

INSERT INTO `order_course_resources` (`id`, `order_id`, `course_id`, `resource_type`, `resource_id`, `quantity`) VALUES
(40, 29, 8, 'drug', 1, '30.00'),
(41, 29, 8, 'tool', 5, '1.00'),
(42, 29, 8, 'tool', 3, '5.00'),
(43, 29, 8, 'accessory', 3, '1.00');

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
(29, 24, 4, 1, 15000),
(30, 25, 5, 1, 20000),
(31, 26, 4, 1, 15000),
(32, 27, 8, 1, 5000),
(33, 29, 8, 1, 35000);

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `position_id` int(11) NOT NULL COMMENT 'รหัสตำแหน่ง',
  `position_name` varchar(50) NOT NULL COMMENT 'ชื่อตำแหน่ง'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `position`
--

INSERT INTO `position` (`position_id`, `position_name`) VALUES
(1, 'ผู้ดูแลระบบ'),
(2, 'ผู้จัดการคลินิก'),
(3, 'หมอ'),
(4, 'พยาบาล'),
(5, 'พนักงานต้อนรับ');

-- --------------------------------------------------------

--
-- Table structure for table `service_nurse_records`
--

CREATE TABLE `service_nurse_records` (
  `nurse_record_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `nurse_id` int(11) NOT NULL,
  `nurse_df` decimal(10,2) NOT NULL,
  `nurse_df_type` enum('amount','percent') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_queue`
--

CREATE TABLE `service_queue` (
  `queue_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `cus_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `queue_number` varchar(10) NOT NULL,
  `queue_date` date NOT NULL,
  `queue_time` time NOT NULL,
  `service_status` enum('waiting','in_progress','completed','cancelled') NOT NULL DEFAULT 'waiting',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_queue`
--

INSERT INTO `service_queue` (`queue_id`, `branch_id`, `cus_id`, `booking_id`, `queue_number`, `queue_date`, `queue_time`, `service_status`, `created_at`, `updated_at`, `notes`) VALUES
(52, 1, 13, 25, 'Q001', '2024-09-10', '10:00:00', 'in_progress', '2024-09-10 15:25:52', '2024-09-10 15:26:04', ''),
(53, 1, 13, 26, 'Q002', '2024-09-10', '11:15:00', 'in_progress', '2024-09-10 15:25:57', '2024-09-10 15:26:05', ''),
(54, 1, 13, 27, 'Q003', '2024-09-10', '12:45:00', 'waiting', '2024-09-10 15:26:02', '2024-09-10 15:26:02', ''),
(55, 1, 13, 28, 'Q001', '2024-09-11', '14:00:00', 'in_progress', '2024-09-11 03:56:45', '2024-09-11 03:56:48', ''),
(56, 1, 14, NULL, 'Q002', '2024-09-11', '11:35:00', 'in_progress', '2024-09-11 04:36:07', '2024-09-11 04:36:10', '');

-- --------------------------------------------------------

--
-- Table structure for table `service_records`
--

CREATE TABLE `service_records` (
  `service_id` int(11) NOT NULL,
  `cus_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `doctor_df` decimal(10,2) NOT NULL,
  `doctor_df_type` enum('amount','percent') NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `service_staff_records`
--

CREATE TABLE `service_staff_records` (
  `staff_record_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `staff_type` enum('doctor','nurse') NOT NULL,
  `staff_df` decimal(10,2) NOT NULL,
  `staff_df_type` enum('amount','percent') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

--
-- Dumping data for table `stock_transactions`
--

INSERT INTO `stock_transactions` (`transaction_id`, `transaction_date`, `users_id`, `quantity`, `expiry_date`, `cost_per_unit`, `stock_type`, `related_id`, `status`, `branch_id`, `notes`, `created_at`, `updated_at`) VALUES
(7, '2024-08-24 16:53:00', 1, 100, NULL, 3.2, 'drug', 1, 1, 1, '', '2024-08-24 09:54:13', '2024-08-24 09:54:13'),
(8, '2024-08-24 16:53:00', 1, 100, NULL, 3.2, 'drug', 1, 1, 1, '', '2024-08-24 09:54:37', '2024-08-24 09:54:37'),
(9, '2024-08-24 16:57:00', 1, 120, NULL, 3.1, 'drug', 1, 1, 1, '', '2024-08-24 09:57:18', '2024-08-24 09:57:18'),
(10, '2024-08-24 17:20:00', 1, 12, '2024-08-31', 3.1, 'drug', 1, 1, 1, '', '2024-08-24 10:20:55', '2024-08-24 10:20:55'),
(11, '2024-08-24 17:30:00', 1, 5, '', 3.3, 'drug', 1, 1, 1, '', '2024-08-24 10:31:11', '2024-08-24 10:31:11'),
(12, '2024-08-24 18:07:00', 1, 110, '2024-08-31', 5.1, '', 1, 1, 1, '', '2024-08-24 11:08:13', '2024-08-24 11:08:13'),
(13, '2024-08-24 18:07:00', 1, 110, '2024-08-31', 5.1, '', 1, 1, 1, '', '2024-08-24 11:09:18', '2024-08-24 11:09:18'),
(14, '2024-08-24 18:11:00', 1, 101, '2024-08-31', 20.1, 'accessory', 1, 1, 1, '', '2024-08-24 11:12:00', '2024-08-24 11:12:00'),
(15, '2024-08-24 18:11:00', 1, 101, '2024-08-31', 20.1, 'accessory', 1, 1, 1, '', '2024-08-24 11:12:43', '2024-08-24 11:12:43'),
(16, '2024-08-24 18:11:00', 1, 101, '2024-08-31', 20.1, 'accessory', 1, 1, 1, '', '2024-08-24 11:13:03', '2024-08-24 11:13:03'),
(17, '2024-08-24 18:13:00', 1, 120, '2024-02-28', 5.85, 'accessory', 1, 1, 1, '', '2024-08-24 11:15:42', '2024-08-24 11:15:42'),
(18, '2024-08-24 18:15:00', 1, 120, '', 5.64, 'accessory', 1, 1, 1, '', '2024-08-24 11:15:59', '2024-08-24 11:15:59'),
(19, '2024-08-24 18:42:00', 1, 120, '', 12.2, 'tool', 1, 1, 1, '', '2024-08-24 11:43:26', '2024-08-24 11:43:26'),
(20, '2024-08-24 18:43:00', 1, 122, '2024-08-31', 5.21, 'tool', 1, 1, 1, '', '2024-08-24 11:45:01', '2024-08-24 11:45:01'),
(21, '2024-08-24 18:45:00', 1, 120, '', 5.55, 'tool', 1, 1, 1, '', '2024-08-24 11:45:22', '2024-08-24 11:45:22'),
(22, '2024-08-24 18:46:00', 1, 123, '', 11, 'tool', 1, 1, 1, '', '2024-08-24 11:46:18', '2024-08-24 11:46:18'),
(23, '2024-08-24 18:46:00', 1, 123, '', 11, 'tool', 1, 1, 1, '', '2024-08-24 11:46:38', '2024-08-24 11:46:38'),
(24, '2024-08-24 18:46:00', 1, 123, '', 11, 'tool', 1, 1, 1, '', '2024-08-24 11:47:04', '2024-08-24 11:47:04');

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

--
-- Dumping data for table `tool`
--

INSERT INTO `tool` (`tool_id`, `tool_name`, `branch_id`, `tool_detail`, `tool_amount`, `tool_cost`, `tool_unit_id`, `tool_status`) VALUES
(1, 'เครื่องนับเม็ดยา', 1, 'ใช้สำหรับนับเม็ดยาอัตโนมัติ ความแม่นยำสูง', 123, 500, 1, 1),
(2, 'ตู้เย็นเก็บยา', 1, 'ตู้เย็นควบคุมอุณหภูมิสำหรับเก็บยาที่ต้องการความเย็น', 0, 1200, 1, 1),
(3, 'เครื่องบดยา', 1, 'ใช้สำหรับบดยาเม็ดให้เป็นผง', 0, 300, 1, 1),
(4, 'เครื่องผสมยา', 1, 'สำหรับผสมยาในรูปแบบของเหลว', 0, 800, 1, 1),
(5, 'ชุดเครื่องมือแบ่งบรรจุยา', 1, 'ใช้สำหรับแบ่งบรรจุยาเป็นซอง', 0, 200, 2, 1),
(6, 'เครื่องพิมพ์ฉลากยา', 1, 'พิมพ์ฉลากยาอัตโนมัติ ความละเอียดสูง', 0, 1000, 1, 1),
(7, 'ถาดจัดยา', 1, 'ถาดพลาสติกสำหรับจัดเรียงยาก่อนบรรจุ', 0, 50, 1, 1),
(8, 'เครื่องวัดความดันโลหิต', 1, 'ใช้วัดความดันโลหิตแบบดิจิทัล', 0, 150, 1, 1),
(9, 'เครื่องวัดระดับน้ำตาลในเลือด', 1, 'ใช้ตรวจวัดระดับน้ำตาลในเลือด', 0, 200, 1, 1),
(10, 'ชุดให้สารละลายทางหลอดเลือดดำ', 1, 'อุปกรณ์สำหรับให้สารละลายทางหลอดเลือดดำ', 0, 100, 2, 1),
(11, 'เครื่องพ่นยา', 1, 'ใช้สำหรับพ่นยาละอองฝอย', 0, 300, 1, 1),
(12, 'ตู้เก็บยาควบคุมพิเศษ', 1, 'ตู้เก็บยาที่มีระบบล็อคพิเศษสำหรับยาควบคุม', 0, 800, 1, 1),
(13, 'เครื่องวัดอุณหภูมิร่างกาย', 1, 'เทอร์โมมิเตอร์ดิจิทัลสำหรับวัดไข้', 0, 50, 1, 1),
(14, 'ชุดทำแผล', 1, 'อุปกรณ์สำหรับทำแผลและจ่ายยาทาภายนอก', 0, 100, 2, 1),
(15, 'เครื่องชั่งน้ำหนักดิจิทัล', 1, 'ใช้ชั่งน้ำหนักผู้ป่วยเพื่อคำนวณปริมาณยา', 0, 300, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `unit`
--

CREATE TABLE `unit` (
  `unit_id` int(11) NOT NULL,
  `unit_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unit`
--

INSERT INTO `unit` (`unit_id`, `unit_name`) VALUES
(1, 'เม็ด'),
(2, 'แคปซูล'),
(3, 'ขวด'),
(4, 'หลอด'),
(5, 'แผง'),
(6, 'ซอง'),
(7, 'เข็ม'),
(8, 'หลอดฉีดยา'),
(9, 'แผ่น'),
(10, 'ชุด'),
(11, 'กระปุก'),
(12, 'กล่อง'),
(13, 'มิลลิลิตร'),
(14, 'กรัม'),
(15, 'แอมพูล');

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
(51, '123', '123', '123', '123', '123', '00000', 2, NULL, 2, 1),
(52, 'แพทย์ 1', 'แพทย์ 1', 'แพทย์ 1 ', 'แพทย์ 1', 'แพทย์ 1', '1234564897', 3, 'ว12345645', 1, 1),
(53, 'แพทย์ 2', 'แพทย์ 2', 'แพทย์ 2', 'แพทย์ 2', 'แพทย์ 2', '1234564897', 3, 'ว12345645', 1, 1),
(54, 'แพทย์ 3', 'แพทย์ 3', 'แพทย์ 3', 'แพทย์ 3', 'แพทย์ 3', '1234564897', 3, 'ว12345645', 1, 1),
(55, 'รับ 1', 'รับ 1', 'รับ 1', 'รับ 1', 'รับ 1', '123', 5, '', 1, 1);

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
-- Indexes for table `course_usage`
--
ALTER TABLE `course_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `od_id` (`od_id`),
  ADD KEY `queue_id` (`queue_id`);

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
-- Indexes for table `opd`
--
ALTER TABLE `opd`
  ADD PRIMARY KEY (`opd_id`),
  ADD KEY `queue_id` (`queue_id`),
  ADD KEY `cus_id` (`cus_id`),
  ADD KEY `nurse_id` (`nurse_id`);

--
-- Indexes for table `opd_drawings`
--
ALTER TABLE `opd_drawings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `opd_id` (`opd_id`);

--
-- Indexes for table `order_course`
--
ALTER TABLE `order_course`
  ADD PRIMARY KEY (`oc_id`),
  ADD KEY `cus_id` (`cus_id`);

--
-- Indexes for table `order_course_resources`
--
ALTER TABLE `order_course_resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `course_id` (`course_id`);

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
-- Indexes for table `service_nurse_records`
--
ALTER TABLE `service_nurse_records`
  ADD PRIMARY KEY (`nurse_record_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `nurse_id` (`nurse_id`);

--
-- Indexes for table `service_queue`
--
ALTER TABLE `service_queue`
  ADD PRIMARY KEY (`queue_id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `cus_id` (`cus_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `service_records`
--
ALTER TABLE `service_records`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `cus_id` (`cus_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `service_staff_records`
--
ALTER TABLE `service_staff_records`
  ADD PRIMARY KEY (`staff_record_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `staff_id` (`staff_id`);

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
  MODIFY `acc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `acc_type`
--
ALTER TABLE `acc_type`
  MODIFY `acc_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `branch`
--
ALTER TABLE `branch`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `clinic_closures`
--
ALTER TABLE `clinic_closures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `clinic_hours`
--
ALTER TABLE `clinic_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `course_bookings`
--
ALTER TABLE `course_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `course_resources`
--
ALTER TABLE `course_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `course_type`
--
ALTER TABLE `course_type`
  MODIFY `course_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `course_usage`
--
ALTER TABLE `course_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `drug`
--
ALTER TABLE `drug`
  MODIFY `drug_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `drug_type`
--
ALTER TABLE `drug_type`
  MODIFY `drug_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `opd`
--
ALTER TABLE `opd`
  MODIFY `opd_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `opd_drawings`
--
ALTER TABLE `opd_drawings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `order_course`
--
ALTER TABLE `order_course`
  MODIFY `oc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `order_course_resources`
--
ALTER TABLE `order_course_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `od_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `position`
--
ALTER TABLE `position`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสตำแหน่ง', AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `service_nurse_records`
--
ALTER TABLE `service_nurse_records`
  MODIFY `nurse_record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_queue`
--
ALTER TABLE `service_queue`
  MODIFY `queue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `service_records`
--
ALTER TABLE `service_records`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service_staff_records`
--
ALTER TABLE `service_staff_records`
  MODIFY `staff_record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tool`
--
ALTER TABLE `tool`
  MODIFY `tool_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `unit`
--
ALTER TABLE `unit`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `course_bookings`
--
ALTER TABLE `course_bookings`
  ADD CONSTRAINT `course_bookings_ibfk_2` FOREIGN KEY (`cus_id`) REFERENCES `customer` (`cus_id`);

--
-- Constraints for table `course_usage`
--
ALTER TABLE `course_usage`
  ADD CONSTRAINT `course_usage_ibfk_1` FOREIGN KEY (`od_id`) REFERENCES `order_detail` (`od_id`),
  ADD CONSTRAINT `course_usage_ibfk_2` FOREIGN KEY (`queue_id`) REFERENCES `service_queue` (`queue_id`);

--
-- Constraints for table `opd`
--
ALTER TABLE `opd`
  ADD CONSTRAINT `opd_ibfk_1` FOREIGN KEY (`queue_id`) REFERENCES `service_queue` (`queue_id`),
  ADD CONSTRAINT `opd_ibfk_2` FOREIGN KEY (`cus_id`) REFERENCES `customer` (`cus_id`),
  ADD CONSTRAINT `opd_ibfk_4` FOREIGN KEY (`nurse_id`) REFERENCES `users` (`users_id`);

--
-- Constraints for table `opd_drawings`
--
ALTER TABLE `opd_drawings`
  ADD CONSTRAINT `opd_drawings_ibfk_1` FOREIGN KEY (`opd_id`) REFERENCES `opd` (`opd_id`);

--
-- Constraints for table `order_course`
--
ALTER TABLE `order_course`
  ADD CONSTRAINT `order_course_ibfk_1` FOREIGN KEY (`cus_id`) REFERENCES `customer` (`cus_id`),
  ADD CONSTRAINT `order_course_ibfk_3` FOREIGN KEY (`course_bookings_id`) REFERENCES `course_bookings` (`id`);

--
-- Constraints for table `order_course_resources`
--
ALTER TABLE `order_course_resources`
  ADD CONSTRAINT `order_course_resources_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order_course` (`oc_id`),
  ADD CONSTRAINT `order_course_resources_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`);

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`oc_id`) REFERENCES `order_course` (`oc_id`),
  ADD CONSTRAINT `order_detail_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`);

--
-- Constraints for table `service_nurse_records`
--
ALTER TABLE `service_nurse_records`
  ADD CONSTRAINT `service_nurse_records_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service_records` (`service_id`),
  ADD CONSTRAINT `service_nurse_records_ibfk_2` FOREIGN KEY (`nurse_id`) REFERENCES `users` (`users_id`);

--
-- Constraints for table `service_queue`
--
ALTER TABLE `service_queue`
  ADD CONSTRAINT `service_queue_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`),
  ADD CONSTRAINT `service_queue_ibfk_2` FOREIGN KEY (`cus_id`) REFERENCES `customer` (`cus_id`),
  ADD CONSTRAINT `service_queue_ibfk_3` FOREIGN KEY (`booking_id`) REFERENCES `course_bookings` (`id`);

--
-- Constraints for table `service_records`
--
ALTER TABLE `service_records`
  ADD CONSTRAINT `service_records_ibfk_1` FOREIGN KEY (`cus_id`) REFERENCES `customer` (`cus_id`),
  ADD CONSTRAINT `service_records_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`course_id`),
  ADD CONSTRAINT `service_records_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`users_id`);

--
-- Constraints for table `service_staff_records`
--
ALTER TABLE `service_staff_records`
  ADD CONSTRAINT `service_staff_records_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service_records` (`service_id`),
  ADD CONSTRAINT `service_staff_records_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `users` (`users_id`);

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
