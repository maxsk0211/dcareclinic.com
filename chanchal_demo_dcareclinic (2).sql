-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 25, 2025 at 06:27 PM
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
-- Database: `chanchal_demo_dcareclinic`
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
(1, 'เครื่องวัดความดันโลหิตดิจิทัล', 1, 'วัดความดันโลหิตอัตโนมัติ แสดงผลบนจอ LCD', 7, 185, 120, 1, 1),
(2, 'เตียงผู้ป่วยไฟฟ้า', 1, 'ปรับระดับด้วยรีโมทคอนโทรล รับน้ำหนักได้สูงสุด 200 กก.', 8, 0, 2500, 10, 1),
(3, 'เครื่องกระตุกหัวใจไฟฟ้าแบบอัตโนมัติ (AED)', 1, 'ใช้งานง่าย มีคำแนะนำเสียงภาษาไทย', 4, -1, 5000, 1, 1),
(4, 'เครื่องช่วยหายใจแบบพกพา', 1, 'ใช้แบตเตอรี่ ทำงานได้ต่อเนื่อง 8 ชั่วโมง', 2, 0, 3000, 1, 1),
(5, 'เครื่องอัลตราซาวด์', 1, 'ความละเอียดสูง มีโหมดการทำงานหลากหลาย', 3, 0, 8000, 1, 1),
(6, 'ชุดเครื่องมือผ่าตัดทั่วไป', 1, 'ผลิตจากสแตนเลสคุณภาพสูง ปราศจากเชื้อ', 1, 0, 150, 2, 1),
(7, 'รถเข็นทำแผล', 1, 'มีล้อล็อคได้ พร้อมถาดสแตนเลส', 12, 0, 500, 1, 1),
(8, 'เครื่องฉายแสง UV ฆ่าเชื้อ', 1, 'ใช้สำหรับฆ่าเชื้อในห้องผ่าตัดและห้องคนไข้', 10, 0, 1000, 1, 1),
(9, 'เครื่องวัดออกซิเจนในเลือด', 1, 'แสดงผลแบบดิจิทัล วัดได้รวดเร็ว', 7, -15, 350, 1, 1),
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
  `acc_type_name` varchar(50) NOT NULL,
  `branch_id` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `acc_type`
--

INSERT INTO `acc_type` (`acc_type_id`, `acc_type_name`, `branch_id`) VALUES
(1, 'เครื่องมือผ่าตัด', 0),
(2, 'อุปกรณ์ช่วยหายใจ', 0),
(3, 'เครื่องมือตรวจวินิจฉัย', 0),
(4, 'อุปกรณ์ฉุกเฉิน', 0),
(5, 'เครื่องมือทันตกรรม', 0),
(6, 'อุปกรณ์กายภาพบำบัด', 0),
(7, 'เครื่องมือวัดสัญญาณชีพ', 0),
(8, 'อุปกรณ์ช่วยเหลือการเคลื่อนไหว', 0),
(9, 'เครื่องมือห้องปฏิบัติการ', 0),
(10, 'อุปกรณ์ทำความสะอาดและฆ่าเชื้อ', 0),
(11, 'เครื่องมือรังสีวิทยา', 0),
(12, 'อุปกรณ์การพยาบาล', 0),
(13, 'เครื่องมือจักษุวิทยา', 0),
(14, 'อุปกรณ์การแพทย์ทางไกล', 0),
(15, 'เครื่องมือศัลยกรรมพลาสติก', 0),
(17, 'qwdqw', 0);

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `entity_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `entity_id` int(11) NOT NULL,
  `details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `branch_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `details`, `branch_id`, `created_at`) VALUES
(1, 1, 'create', 'voucher', 1, '0', 1, '2024-10-27 12:34:40'),
(2, 1, 'create', 'voucher', 2, '0', 1, '2024-10-27 15:39:42'),
(3, 1, 'cancel', 'voucher', 2, '0', 1, '2024-10-27 16:36:20'),
(4, 1, 'create', 'voucher', 3, '0', 1, '2024-10-27 16:37:25'),
(5, 1, 'cancel', 'voucher', 6, '0', 1, '2024-10-27 22:40:02'),
(6, 1, 'cancel', 'voucher', 8, '0', 1, '2024-10-28 14:11:08'),
(7, 1, 'cancel', 'voucher', 8, '0', 1, '2024-10-28 15:08:56'),
(8, 1, 'cancel', 'voucher', 8, '0', 1, '2024-10-28 19:06:23'),
(9, 1, 'cancel', 'voucher', 8, '0', 1, '2024-10-28 19:22:10'),
(10, 1, 'cancel', 'booking', 30, '0', 1, '2024-10-31 15:59:45'),
(11, 1, 'cancel', 'voucher', 8, '0', 1, '2024-10-31 21:32:25'),
(12, 1, 'cancel', 'voucher', 8, '0', 1, '2024-10-31 22:34:35'),
(13, 1, 'approve', 'booking', 39, '0', 1, '2024-11-02 14:55:05'),
(14, 1, 'cancel', 'booking', 44, '0', 1, '2024-11-11 15:59:05'),
(17, 1, 'cancel', 'booking', 47, '{\"reason\":\"\\u0e22\\u0e01\\u0e40\\u0e25\\u0e34\\u0e01\",\"changes\":{\"status\":{\"from\":\"confirmed\",\"to\":\"cancelled\"}}}', 1, '2024-12-24 23:36:35'),
(18, 1, 'cancel', 'booking', 46, '{\"reason\":\"\\u0e23\\u0e30\\u0e1a\\u0e38\\u0e40\\u0e2b\\u0e15\\u0e38\\u0e1c\\u0e25\\u0e01\\u0e32\\u0e23\\u0e22\\u0e01\\u0e40\\u0e25\\u0e34\\u0e01\",\"changes\":{\"status\":{\"from\":\"confirmed\",\"to\":\"cancelled\"}}}', 1, '2024-12-25 22:56:01'),
(19, 1, 'cancel', 'booking', 45, '{\"reason\":\"\\u0e23\\u0e30\\u0e1a\\u0e38\\u0e40\\u0e2b\\u0e15\\u0e38\\u0e1c\\u0e25\\u0e01\\u0e32\\u0e23\\u0e22\\u0e01\\u0e40\\u0e25\\u0e34\\u0e01\",\"changes\":{\"status\":{\"from\":\"confirmed\",\"to\":\"cancelled\"}}}', 1, '2024-12-25 23:05:47'),
(20, 1, 'cancel', 'booking', 43, '{\"reason\":\"???????????????????\",\"booking_info\":{\"date\":\"2024-11-11 15:49:00\",\"customer\":\"???????? ????????\"},\"changes\":{\"status\":{\"from\":\"cancelled\",\"to\":\"cancelled\"}}}', 1, '2024-12-25 23:06:13'),
(21, 1, 'cancel', 'booking', 42, '{\"reason\":\"ระบุเหตุผลการยกเลิก\",\"booking_info\":{\"date\":\"2024-11-03 11:33:00\",\"customer\":\"สมศักดิ์ รักเรียน\"},\"changes\":{\"status\":{\"from\":\"cancelled\",\"to\":\"cancelled\"}}}', 1, '2024-12-25 23:07:30'),
(22, 1, 'update', 'course', 25, '{\"changes\":{\"course_start\":{\"from\":\"2024-12-01\",\"to\":\"23\\/07\\/2567\"},\"course_end\":{\"from\":\"2024-12-31\",\"to\":\"22\\/08\\/2567\"},\"duration\":{\"from\":60,\"to\":null}},\"course_name\":\"11\"}', 1, '2024-12-25 23:38:21'),
(23, 1, 'delete', 'course', 25, '{\"reason\":\"ยืนยันการลบข้อมูล?\",\"deleted_data\":{\"course_name\":\"11\",\"course_price\":1,\"course_amount\":1,\"course_detail\":\"1\"}}', 1, '2024-12-25 23:40:54'),
(24, 1, 'update', 'course', 22, '{\"changes\":{\"course_start\":{\"from\":\"2024-04-20\",\"to\":\"10\\/12\\/2566\"},\"course_end\":{\"from\":\"2025-04-20\",\"to\":\"10\\/12\\/2567\"},\"duration\":{\"from\":60,\"to\":null}},\"course_name\":\"ฟื้นฟูผิวด้วยเซลล์ต้นกำเนิด\",\"course_code\":\"C-000022\"}', 1, '2024-12-25 23:53:01'),
(25, 1, 'delete', 'course', 26, '{\"reason\":\"ยืนยันการลบข้อมูล?\",\"deleted_data\":{\"course_id\":\"C-000026\",\"course_name\":\"1\",\"course_price\":1,\"course_amount\":1,\"course_detail\":\"1\"}}', 1, '2024-12-25 23:54:17'),
(26, 1, 'cancel_payment', 'payment', 34, '{\"reason\":\"ยืนยันการยกเลิกการชำระเงิน\",\"payment_info\":{\"amount\":30000,\"payment_type\":\"เงินสด\",\"payment_date\":\"2024-12-21 19:53:31\"},\"customer_info\":{\"name\":\"1qwdqf adfsd\"}}', 1, '2024-12-26 23:08:15'),
(27, 1, 'create', 'drug', 27, '{\"drug_name\":\"ๅ\",\"drug_type\":\"ยาแก้ปวดลดไข้\",\"properties\":\"ๅ\",\"unit_id\":\"1\",\"status\":\"1\",\"additional_info\":{\"advice\":\"ๅ\",\"warning\":\"ๅ\"}}', 1, '2024-12-29 11:31:11'),
(28, 1, 'update', 'drug', 27, '{\"changes\":{\"drug_name\":{\"from\":\"ๅ\",\"to\":\"1\"},\"properties\":{\"from\":\"ๅ\",\"to\":\"2\"}},\"drug_name\":\"1\",\"drug_id\":\"27\"}', 1, '2024-12-29 11:33:12'),
(29, 1, 'delete', 'drug', 27, '{\"reason\":\"ยืนยันการลบข้อมูล?\",\"deleted_data\":{\"drug_name\":\"1\",\"drug_type\":\"ยาแก้ปวดลดไข้\",\"properties\":\"2\",\"amount\":0,\"status\":1}}', 1, '2024-12-29 11:37:19'),
(30, 1, 'delete', 'drug', 25, '{\"reason\":\"ๆไกๆไกๆไกๆไก\",\"deleted_data\":{\"drug_name\":\"23r\",\"drug_type\":\"ยารักษาโรคกระเพาะ\",\"properties\":\"23r23r\",\"amount\":0,\"status\":1}}', 1, '2024-12-29 11:44:32'),
(31, 1, 'create', 'accessory', 19, '{\"acc_name\":\"ๆได\",\"acc_type\":\"เครื่องมือผ่าตัด\",\"properties\":\"ๆไพๆไ\",\"unit_id\":\"1\",\"status\":\"1\"}', 1, '2024-12-29 12:02:41'),
(32, 1, 'update', 'accessory', 19, '{\"changes\":{\"acc_name\":{\"from\":\"ๆได\",\"to\":\"123123\"},\"acc_type\":{\"from\":\"เครื่องมือผ่าตัด\",\"to\":\"เครื่องมือตรวจวินิจฉัย\"},\"properties\":{\"from\":\"ๆไพๆไ\",\"to\":\"fwerwer\"}},\"acc_name\":\"123123\",\"acc_code\":\"ACC-000019\"}', 1, '2024-12-29 12:04:32'),
(33, 1, 'delete', 'accessory', 19, '{\"reason\":\"กฟหกฟหก\",\"deleted_data\":{\"acc_code\":\"ACC-000019\",\"acc_name\":\"123123\",\"acc_type\":\"เครื่องมือตรวจวินิจฉัย\",\"properties\":\"fwerwer\",\"amount\":0,\"status\":1}}', 1, '2024-12-29 12:18:43'),
(37, 1, 'create', 'tool', 21, '{\"tool_name\":\"qwdwq\",\"branch_id\":\"1\",\"tool_detail\":\"qwd\",\"tool_amount\":\"0\",\"tool_unit_id\":\"1\",\"tool_status\":\"1\"}', 1, '2024-12-29 12:57:45'),
(39, 1, 'update', 'tool', 21, '{\"changes\":{\"tool_name\":{\"from\":\"123123\",\"to\":\"qe3eq3\"},\"tool_detail\":{\"from\":\"123121\",\"to\":\"q3eq3\"},\"tool_unit_id\":{\"from\":\"3\",\"to\":\"6\"}},\"tool_name\":\"qe3eq3\"}', 1, '2024-12-29 12:59:48'),
(40, 1, 'update', 'tool', 21, '{\"changes\":[],\"tool_name\":\"qe3eq3\"}', 1, '2024-12-29 13:00:09'),
(44, 1, 'delete', 'tool', 19, '{\"reason\":\"u0e22u0e37u0e19u0e22u0e31u0e19u0e01u0e32u0e23u0e25u0e1au0e02u0e49u0e2du0e21u0e39u0e25?\",\"deleted_data\":{\"tool_id\":\"19\",\"tool_name\":\"u0e44u0e14u0e46u0e44u0e14\",\"tool_detail\":\"u0e46u0e44u0e14\"}}', 1, '2024-12-29 13:02:01'),
(46, 1, 'delete', 'tool', 18, '{\"reason\":\"u0e22u0e37u0e19u0e22u0e31u0e19u0e01u0e32u0e23u0e25u0e1au0e02u0e49u0e2du0e21u0e39u0e25?\",\"deleted_data\":{\"tool_id\":\"18\",\"tool_name\":\"u0e46u0e44u0e01u0e46u0e44\",\"tool_detail\":\"u0e46u0e44u0e01u0e46\"}}', 1, '2024-12-29 15:49:55'),
(47, 1, 'create', 'tool', 22, '{\"tool_name\":\"qwdqwd\",\"branch_id\":\"1\",\"tool_detail\":\"qwdq\",\"tool_amount\":\"0\",\"tool_unit_id\":\"4\",\"tool_status\":\"1\"}', 1, '2024-12-29 16:07:34'),
(48, 1, 'delete', 'tool', 22, '{\"reason\":\"qwqwd\",\"deleted_data\":{\"tool_id\":\"22\",\"tool_name\":\"qwdqwd\",\"tool_detail\":\"qwdq\"}}', 1, '2024-12-29 16:08:07'),
(49, 1, 'delete', 'drug', 20, '{\"reason\":\"ีรสีรส\",\"deleted_data\":{\"drug_name\":\"เซเลโคซิบ\",\"drug_type\":null,\"properties\":\"ยาแก้ปวดต้านการอักเสบ\",\"amount\":0,\"status\":1}}', 1, '2024-12-29 16:20:43'),
(50, 1, 'cancel_payment', 'payment', 35, '{\"reason\":\"33\",\"payment_info\":{\"amount\":25000,\"payment_type\":\"เงินสด\",\"payment_date\":\"2024-12-30 11:33:57\"},\"customer_info\":{\"name\":\"สนธยา แข็งแรง\"}}', 1, '2024-12-30 11:35:03'),
(51, 1, 'cancel_payment', 'payment', 36, '{\"reason\":\"55\",\"payment_info\":{\"amount\":25000,\"payment_type\":\"เงินสด\",\"payment_date\":\"2024-12-30 22:59:59\"},\"customer_info\":{\"name\":\"สนธยา แข็งแรง\"}}', 1, '2024-12-30 23:01:25'),
(52, 1, 'cancel_payment', 'payment', 36, '{\"reason\":\"11\",\"payment_info\":{\"amount\":25000,\"payment_type\":\"เงินสด\",\"payment_date\":\"2024-12-30 23:01:40\"},\"customer_info\":{\"name\":\"สนธยา แข็งแรง\"}}', 1, '2024-12-30 23:01:47'),
(53, 1, 'cancel_payment', 'payment', 36, '{\"reason\":\"ทดสอบ\",\"payment_info\":{\"amount\":25000,\"payment_type\":\"บัตรเครดิต\",\"payment_date\":\"2024-12-30 23:04:09\"},\"customer_info\":{\"name\":\"สนธยา แข็งแรง\"}}', 1, '2024-12-30 23:05:08'),
(54, 1, 'cancel_payment', 'payment', 37, '{\"reason\":\"ทดสอบ\",\"payment_info\":{\"amount\":25000,\"payment_type\":\"เงินสด\",\"payment_date\":\"2024-12-31 09:42:36\"},\"customer_info\":{\"name\":\"สนธยา แข็งแรง\"}}', 1, '2024-12-31 09:43:12'),
(55, 1, 'cancel_payment', 'payment', 37, '{\"reason\":\"ยืนยันการยกเลิกการชำระเงิน\",\"payment_info\":{\"amount\":25000,\"payment_type\":\"เงินสด\",\"payment_date\":\"2025-01-01 18:36:56\"},\"customer_info\":{\"name\":\"สนธยา แข็งแรง\"}}', 1, '2025-01-01 18:37:05'),
(56, 1, 'cancel_payment', 'payment', 37, '{\"reason\":\"ยืนยันการยกเลิกการชำระเงิน\",\"payment_info\":{\"amount\":25000,\"payment_type\":\"บัตรเครดิต\",\"payment_date\":\"2025-01-01 18:37:19\"},\"customer_info\":{\"name\":\"สนธยา แข็งแรง\"}}', 1, '2025-01-01 19:15:58'),
(57, 1, 'cancel_payment', 'payment', 37, '{\"reason\":\"ยืนยันการยกเลิกการชำระเงิน\",\"payment_info\":{\"amount\":25000,\"payment_type\":\"ยังไม่จ่ายเงิน\",\"payment_date\":null},\"customer_info\":{\"name\":\"สนธยา แข็งแรง\"}}', 1, '2025-01-01 19:17:45'),
(58, 1, 'cancel_payment', 'payment', 37, '{\"reason\":\"ยันการยกเลิกการชำระเงิน\",\"payment_info\":{\"amount\":25000,\"payment_type\":\"เงินสด\",\"payment_date\":\"2025-01-01 19:18:51\"},\"customer_info\":{\"name\":\"สนธยา แข็งแรง\"}}', 1, '2025-01-01 19:19:03'),
(59, 1, 'cancel_payment', 'payment', 35, '{\"reason\":\"wdqwdq\",\"payment_info\":{\"amount\":25000,\"payment_type\":\"เงินสด\",\"payment_date\":\"2024-12-30 11:35:58\"},\"customer_info\":{\"name\":\"สนธยา แข็งแรง\"}}', 1, '2025-02-01 12:50:51');

-- --------------------------------------------------------

--
-- Table structure for table `before_after_images`
--

CREATE TABLE `before_after_images` (
  `id` int(11) NOT NULL,
  `opd_id` int(11) NOT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `image_type` enum('before','after') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `before_after_images`
--

INSERT INTO `before_after_images` (`id`, `opd_id`, `image_path`, `description`, `image_type`, `created_at`) VALUES
(5, 46, '6700e9862c18b_e416dd6cee765c42.jpg', 'wrjbgwkjrgb', 'before', '2024-10-05 07:23:50'),
(6, 46, '6700e99f336a3_ac87f5cf61775331.png', 'qecwqecwewevw', 'after', '2024-10-05 07:24:15'),
(7, 1, '670d3bb26db2f_6b2c853fb954e6f1.jpg', 'ทดสอบ', 'before', '2024-10-14 15:41:38');

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `branch_id` int(11) NOT NULL,
  `branch_name` varchar(50) NOT NULL,
  `branch_address` text DEFAULT NULL,
  `branch_phone` varchar(20) DEFAULT NULL,
  `branch_email` varchar(100) DEFAULT NULL,
  `branch_tax_id` varchar(20) DEFAULT NULL,
  `branch_license_no` varchar(50) DEFAULT NULL,
  `branch_services` text DEFAULT NULL,
  `branch_description` text DEFAULT NULL,
  `branch_logo` varchar(255) DEFAULT NULL,
  `branch_working_hours` text DEFAULT NULL,
  `branch_social_media` text DEFAULT NULL,
  `branch_location_map` text DEFAULT NULL,
  `branch_website` varchar(255) DEFAULT NULL,
  `branch_line_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch`
--

INSERT INTO `branch` (`branch_id`, `branch_name`, `branch_address`, `branch_phone`, `branch_email`, `branch_tax_id`, `branch_license_no`, `branch_services`, `branch_description`, `branch_logo`, `branch_working_hours`, `branch_social_media`, `branch_location_map`, `branch_website`, `branch_line_id`) VALUES
(1, 'Demo 1', '107 ม.3 ต.ไสไทย อ.เมือง จ.กระบี่ 81000', '0808930617', 'max.sk0211@gmail.com', '181992151114515', '424257070', 'คลินิคเสริมความงาม', NULL, 'd.png', NULL, NULL, NULL, 'dcareclinic.com', NULL),
(2, 'Demo 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Demo 3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
(4, 1, 'โบท็อกซ์ลดริ้วรอย', 'การฉีดโบท็อกซ์เพื่อลดเลือนริ้วรอยบนใบหน้า', 15000, 5, 1, '2023-07-31', '2024-08-01', '66c9f0beb125d.jpg', 'เหมาะสำหรับผู้ที่มีริ้วรอยบนใบหน้า', 1, 60),
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
(22, 1, 'ฟื้นฟูผิวด้วยเซลล์ต้นกำเนิด', 'การใช้เซลล์ต้นกำเนิดเพื่อฟื้นฟูผิวให้อ่อนเยาว์', 100000, 3, 3, '0000-00-00', '2024-12-10', '66c9f0beb125d.jpg', 'นวัตกรรมล่าสุดในวงการความงาม', 1, 60),
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
  `room_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `users_id` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  `is_follow_up` tinyint(1) DEFAULT 0 COMMENT 'เป็นการนัดติดตามผลหรือไม่ (0 = ไม่ใช่, 1 = ใช่)'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `course_bookings`
--

INSERT INTO `course_bookings` (`id`, `branch_id`, `cus_id`, `booking_datetime`, `room_id`, `created_at`, `users_id`, `status`, `is_follow_up`) VALUES
(4, 1, 7, '2024-10-14 12:00:00', 1, '2024-11-02 07:50:40', 1, 'pending', 0),
(5, 1, 7, '2024-10-14 12:00:00', 2, '2024-10-13 15:35:09', 1, 'confirmed', 0),
(6, 1, 7, '2024-10-14 15:45:00', 2, '2024-10-13 15:35:40', 1, 'confirmed', 0),
(7, 1, 8, '2024-10-14 12:15:00', 1, '2024-10-13 15:36:33', 1, 'confirmed', 0),
(8, 1, 7, '2024-10-13 09:00:00', 1, '2024-10-13 16:15:55', 1, 'confirmed', 0),
(9, 1, 7, '2024-10-15 09:00:00', 1, '2024-10-14 15:02:44', 1, 'confirmed', 1),
(10, 1, 3, '2024-10-20 12:00:00', 1, '2024-10-20 05:40:30', 1, 'confirmed', 0),
(11, 1, 12, '2024-10-24 12:00:00', 1, '2024-10-24 06:09:34', 1, 'confirmed', 0),
(12, 1, 13, '2024-10-24 12:30:00', 1, '2024-10-24 06:09:53', 1, 'confirmed', 0),
(13, 1, 7, '2024-10-24 15:00:00', 1, '2024-10-24 06:10:44', 1, 'confirmed', 0),
(14, 1, 7, '2024-10-24 10:30:00', 2, '2024-10-24 06:11:44', 1, 'confirmed', 0),
(15, 1, 5, '2024-10-25 22:00:00', 1, '2024-10-25 16:09:58', 1, 'confirmed', 0),
(16, 1, 5, '2024-10-25 22:30:00', 1, '2024-10-25 16:24:56', 1, 'confirmed', 1),
(17, 1, 5, '2024-10-25 23:30:00', 1, '2024-10-25 16:36:27', 1, 'confirmed', 1),
(18, 1, 5, '2024-10-29 12:00:00', 1, '2024-10-25 16:37:26', 1, 'cancelled', 1),
(19, 1, 3, '2024-10-26 23:30:00', 1, '2024-10-26 16:11:42', 1, 'confirmed', 0),
(20, 1, 1, '2024-10-26 23:34:00', 1, '2024-10-26 16:34:49', 0, 'confirmed', 0),
(21, 1, 5, '2024-10-26 23:56:00', 2, '2024-10-26 16:56:24', 0, 'confirmed', 0),
(22, 1, 2, '2024-10-27 00:06:00', 1, '2024-10-26 17:06:19', 0, 'confirmed', 0),
(23, 1, 6, '2024-10-27 00:06:00', 2, '2024-10-26 17:06:33', 0, 'confirmed', 0),
(24, 1, 6, '2024-10-27 00:06:00', 1, '2024-10-26 17:06:42', 0, 'confirmed', 0),
(25, 1, 10, '2024-10-27 10:23:00', 1, '2024-10-27 03:23:43', 0, 'cancelled', 0),
(26, 1, 10, '2024-10-29 12:00:00', 1, '2024-10-28 02:32:23', 1, 'confirmed', 0),
(27, 1, 6, '2024-10-28 10:00:00', 1, '2024-10-28 02:33:17', 1, 'confirmed', 0),
(28, 1, 7, '2024-10-28 19:06:00', 2, '2024-10-28 12:07:04', 0, 'confirmed', 0),
(29, 1, 6, '2024-10-30 09:00:00', 1, '2024-10-30 01:45:27', 1, 'confirmed', 0),
(30, 1, 6, '2024-10-31 10:00:00', 1, '2024-10-31 08:59:45', 1, 'cancelled', 0),
(31, 1, 2, '2024-10-30 09:30:00', 1, '2024-10-30 16:16:15', 1, 'confirmed', 0),
(32, 1, 10, '2024-10-30 14:00:00', 2, '2024-10-30 16:19:31', 1, 'confirmed', 0),
(33, 1, 12, '2024-10-30 09:30:00', 2, '2024-10-30 16:22:55', 1, 'confirmed', 0),
(34, 1, 13, '2024-11-01 14:01:00', 1, '2024-11-01 07:01:28', 0, 'confirmed', 0),
(35, 1, 17, '2024-11-20 09:00:00', NULL, '2024-11-02 06:36:11', 0, 'pending', 0),
(36, 1, 17, '2024-11-02 14:30:00', 2, '2024-11-02 08:29:48', 0, 'confirmed', 0),
(37, 1, 17, '2024-11-02 14:00:00', 1, '2024-11-02 07:40:37', 0, 'confirmed', 0),
(38, 1, 17, '2024-11-02 13:30:00', 1, '2024-11-02 07:45:43', 0, 'pending', 0),
(39, 1, 17, '2024-11-02 13:00:00', 1, '2024-11-02 07:55:05', 0, 'confirmed', 0),
(40, 1, 11, '2024-11-02 14:30:00', 1, '2024-11-02 14:41:15', 1, 'confirmed', 0),
(41, 1, 17, '2024-11-02 12:30:00', 1, '2024-11-02 14:52:24', 0, 'pending', 0),
(42, 1, 3, '2024-11-03 11:33:00', 1, '2024-12-25 16:07:30', 0, 'cancelled', 0),
(43, 1, 3, '2024-11-11 15:49:00', 1, '2024-12-25 16:06:13', 0, 'cancelled', 0),
(44, 1, 3, '2024-11-11 15:30:00', 1, '2024-11-11 08:59:05', 1, 'cancelled', 1),
(45, 1, 5, '2024-11-19 16:41:00', 1, '2024-12-25 16:05:47', 0, 'cancelled', 0),
(46, 1, 2, '2024-12-24 22:26:00', 1, '2024-12-25 15:56:01', 0, 'cancelled', 0),
(47, 1, 13, '2024-12-24 22:53:00', 2, '2024-12-24 16:36:35', 0, 'cancelled', 0),
(48, 1, 17, '2024-12-29 22:37:00', 1, '2024-12-29 15:37:11', 0, 'confirmed', 0),
(49, 1, 13, '2024-12-29 23:06:00', 2, '2024-12-29 16:07:12', 0, 'confirmed', 0),
(50, 1, 13, '2024-12-30 10:30:00', 1, '2024-12-30 04:33:11', 1, 'confirmed', 0),
(51, 1, 13, '2024-12-30 13:00:00', 1, '2024-12-30 15:58:14', 1, 'confirmed', 0),
(52, 1, 13, '2024-12-31 12:00:00', 1, '2024-12-31 02:40:46', 1, 'confirmed', 0);

-- --------------------------------------------------------

--
-- Table structure for table `course_detail_logs`
--

CREATE TABLE `course_detail_logs` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `old_detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_by` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
(1, 4, 'drug', 4, 10, '2024-10-21 13:39:32', '2024-10-21 13:39:32'),
(2, 4, 'tool', 2, 1, '2024-10-21 13:39:38', '2024-10-21 13:39:38'),
(3, 4, 'accessory', 9, 5, '2024-10-21 13:39:46', '2024-10-21 13:39:46'),
(4, 12, 'drug', 1, 15, '2024-12-30 04:31:12', '2024-12-30 04:31:12'),
(5, 12, 'tool', 1, 1, '2024-12-30 04:31:19', '2024-12-30 04:31:19'),
(6, 12, 'drug', 3, 20, '2024-12-30 04:31:30', '2024-12-30 04:31:30'),
(7, 12, 'drug', 9, 15, '2024-12-30 04:31:37', '2024-12-30 04:31:37'),
(8, 12, 'accessory', 1, 20, '2024-12-30 15:57:50', '2024-12-30 15:57:50');

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
  `order_detail_id` int(11) NOT NULL,
  `usage_date` datetime NOT NULL,
  `usage_count` int(11) NOT NULL DEFAULT 1,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `course_usage`
--

INSERT INTO `course_usage` (`id`, `order_detail_id`, `usage_date`, `usage_count`, `notes`, `created_at`) VALUES
(12, 24, '2024-10-23 22:01:00', 1, '', '2024-10-23 15:01:15'),
(16, 43, '2024-11-01 23:49:00', 5, '', '2024-11-01 16:49:15'),
(17, 52, '2024-12-30 11:34:00', 1, '', '2024-12-30 04:34:40');

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
  `occupation` varchar(255) NOT NULL,
  `height` int(4) NOT NULL,
  `weight` int(4) NOT NULL,
  `emergency_name` varchar(150) NOT NULL,
  `emergency_tel` text NOT NULL,
  `emergency_note` varchar(255) NOT NULL,
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
  `line_picture_url` text DEFAULT '../img/customer/customer.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`cus_id`, `cus_id_card_number`, `cus_birthday`, `cus_firstname`, `cus_lastname`, `cus_title`, `cus_gender`, `cus_nickname`, `cus_email`, `cus_blood`, `cus_tel`, `cus_drugallergy`, `cus_congenital`, `occupation`, `height`, `weight`, `emergency_name`, `emergency_tel`, `emergency_note`, `cus_remark`, `cus_address`, `cus_district`, `cus_city`, `cus_province`, `cus_postal_code`, `cus_image`, `cus_status`, `line_user_id`, `line_display_name`, `line_picture_url`) VALUES
(1, '1819900189796', '2024-08-15', 'สน', '123', 'นาย', 'ชาย', '123', '123@ef.wef', 'A+', '', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', 'customer.png', 1, NULL, NULL, '../img/customer/customer.png'),
(2, '2345678901234', '1995-02-15', 'สมหญิง', 'ใจเย็น', 'นางสาว', 'หญิง', 'หญิง', 'somying@example.com', 'B', '0823456789', 'ยา penicillin', NULL, '', 0, 0, '', '', '', NULL, '456 หมู่ 5', 'บางรัก', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10500', 'customer.png', 1, NULL, NULL, '../img/customer/customer.png'),
(3, '3456789012345', '1985-03-30', 'สมศักดิ์', 'รักเรียน', 'นาย', 'ชาย', 'ศักดิ์', 'somsak@example.com', 'O', '0834567890', NULL, 'โรคหัวใจ', '', 0, 0, '', '', '', NULL, '789 หมู่ 6', 'ห้วยขวาง', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10310', 'customer.png', 1, NULL, NULL, '../img/customer/customer.png'),
(5, '5678901234567', '1992-05-25', 'สมหมาย', 'ใจสู้', 'นาย', 'ชาย', 'หมาย', 'sommai@example.com', 'A', '0856789012', 'ยาแอสไพริน', NULL, '', 0, 0, '', '', '', NULL, '234 หมู่ 8', 'บางกะปิ', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10240', 'customer.png', 1, NULL, NULL, '../img/customer/customer.png'),
(6, '6789012345678', '1988-06-20', 'สมใจ', 'ใจถึง', 'นางสาว', 'หญิง', 'ใจ', 'somjai@example.com', 'B', '0867890123', NULL, 'โรคเบาหวาน', '', 0, 0, '', '', '', NULL, '567 หมู่ 9', 'ลาดพร้าว', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10230', 'customer.png', 1, NULL, NULL, '../img/customer/customer.png'),
(7, '7890123456789', '1997-07-05', 'สมคิด', 'ใจกว้าง', 'นาย', 'ชาย', 'คิด', 'somkit@example.com', 'O', '0878901234', NULL, NULL, '', 0, 0, '', '', '', NULL, '890 หมู่ 10', 'บางเขน', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10220', 'customer.png', 1, NULL, NULL, '../img/customer/customer.png'),
(8, '8901234567890', '1983-08-18', 'สมรัก', 'ใจดี', 'นาง', 'หญิง', 'รัก', 'somrak@example.com', 'AB', '0889012345', 'อาหารทะเล', NULL, '', 0, 0, '', '', '', NULL, '123 หมู่ 1', 'บางซื่อ', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10800', 'customer.png', 1, NULL, NULL, '../img/customer/customer.png'),
(9, '9012345678901', '2002-09-22', 'สมหวัง', 'ใจเย็น', 'นาย', 'ชาย', 'หวัง', 'somwang@example.com', 'A', '0890123456', NULL, 'โรคความดันโลหิตสูง', '', 0, 0, '', '', '', NULL, '456 หมู่ 2', 'ดุสิต', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10300', 'customer.png', 1, NULL, NULL, '../img/customer/customer.png'),
(10, '0123456789012', '1994-10-08', 'สมบูรณ์', 'ใจสู้', 'นาย', 'ชาย', 'บูรณ์', 'somboon@example.com', 'B', '0901234567', NULL, NULL, '', 0, 0, '', '', '', NULL, '789 หมู่ 3', 'พญาไท', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10400', 'customer.png', 1, NULL, NULL, '../img/customer/customer.png'),
(11, '1234567890123', '1990-01-01', 'สมชาย', 'ใจดี', 'นาย', 'ชาย', 'ชาย', 'somchai@example.com', 'A', '0812345678', NULL, NULL, '', 0, 0, '', '', '', NULL, '123 หมู่ 4', 'เมือง', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10100', 'customer.png', 1, NULL, NULL, '../img/customer/customer.png'),
(12, '4567890123456', '2000-04-10', 'สมพร', 'ใจบุญ', 'นาง', 'หญิง', 'พร', 'somporn@example.com', 'AB', '0845678901', NULL, NULL, '', 0, 0, '', '', '', 'แพ้ฝุ่น', '101 หมู่ 7', 'จตุจักร', 'กรุงเทพมหานคร', 'กรุงเทพมหานคร', '10900', 'customer.png', 1, NULL, NULL, '../img/customer/customer.png'),
(13, '1819900489796', '1995-11-02', 'สนธยา', 'แข็งแรง', 'นาย', 'ชาย', 'max', 'asdas@wefw.e', '', '1234234234', '-', '-', '0', 170, 110, '', '', '', '-', '107', 'ไสไทย', 'เมือง', 'กระบี่', '81000', 'customer,png', 1, NULL, NULL, NULL),
(14, '1819900254181', '1998-01-29', 'สุดชญา', 'เจียวก๊ก', 'นางสาว', 'หญิง', 'ตุ๊กติ๊ก', 'tuktik2901@gmail.com', 'B-', '0928121387', 'ไม่มี', 'ภูมิแพ้', '', 0, 0, '', '', '', 'ไม่มี', '86', 'ปกาสัย', 'เหนือคลอง', 'กระบี่', '81130', 'customer,png', 1, 'Uefd57d73644282d669338a2bde1231a6', 'Sudchaya.Jeawkok', 'https://profile.line-scdn.net/0h2I5s9Q47bWYYCn498PETGWhabgw7ezR0YThyA30OZ1QtOXg5MGQkByQCNwN1bihgZDwiVH4KZwMUGRoABlyRUh86MFckPSo4Nm8khA'),
(16, '3409934061', '2000-08-28', 'เจนณรงค์', 'อู่อ้น', 'นาย', 'ชาย', 'เจน', 'ck2510@gmail.com', 'A+', '0818304741', NULL, NULL, '', 0, 0, '', '', '', NULL, '', '', '', '', '', 'customer.png', 1, NULL, NULL, '../img/customer/customer.png'),
(17, '123123', '2024-11-02', '1qwdqf', 'adfsd', 'นาย', 'ชาย', '', '', '', '123123123', '', '', '', 0, 0, '', '', '', '', '', '', '', '', '', 'customer,png', 1, 'U4ff5ebe11da5e7e2698cd4cb9a6e8786', 'Max', 'https://profile.line-scdn.net/0hL1AsKHJDEx5sCgbSsVptYRxaEHRPe0oMQDsIfQkNRC1ZO1BAFGxdKw0DHSkFaQAdQG9dflFdHy1gGWR4clzvKms6Ti9QPVRAQm9a_A'),
(18, '1', '2024-12-31', '1', '1', 'นาย', 'ชาย', '', '', 'A', '1', '', '', '7', 8, 9, '9', '8', '7', NULL, '', '', '', '', '', 'customer.png', 1, NULL, NULL, '../img/customer/customer.png');

-- --------------------------------------------------------

--
-- Table structure for table `deposit_cancellation_logs`
--

CREATE TABLE `deposit_cancellation_logs` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `cancelled_by` int(11) NOT NULL,
  `deposit_amount` decimal(10,2) NOT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `cancelled_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deposit_cancellation_logs`
--

INSERT INTO `deposit_cancellation_logs` (`id`, `order_id`, `cancelled_by`, `deposit_amount`, `cancellation_reason`, `cancelled_at`) VALUES
(2, 34, 1, '300.00', 'ยกเลิกค่ามัดจำ', '2024-11-10 12:58:53'),
(3, 34, 1, '300.00', 'ยกเลิกค่ามัดจำ', '2024-11-10 13:52:31'),
(4, 34, 55, '300.00', 'ยกเลิกค่ามัดจำ', '2024-11-10 15:28:52'),
(5, 34, 55, '5.00', 'เลิกค่ามัดจำ', '2024-11-10 15:41:44');

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
(1, 1, 'พาราเซตามอล', 1, 'ยาแก้ปวด ลดไข้', 'รับประทานหลังอาหารทันที', 'ห้ามใช้เกินขนาดที่กำหนด', 710, 1, 15.1, '66c9aebf20f77.jpg', 1),
(2, 1, 'อะม็อกซีซิลลิน', 2, 'ยาปฏิชีวนะ', 'รับประทานติดต่อกันจนหมด', 'แจ้งแพทย์หากมีอาการแพ้', 0, 1, 30, '', 1),
(3, 1, 'ออมีพราโซล', 3, 'ยารักษาโรคกระเพาะ', 'รับประทานก่อนอาหาร 30 นาที', 'ห้ามใช้ในผู้ที่แพ้ยานี้', -60, 2, 45.75, '', 1),
(4, 1, 'ไอบูโพรเฟน', 1, 'ยาแก้ปวด ต้านการอักเสบ', 'รับประทานหลังอาหารทันที', 'ห้ามใช้ในผู้ที่เป็นโรคกระเพาะ', -10, 1, 5.4, '', 1),
(5, 1, 'เมทฟอร์มิน', 4, 'ยารักษาเบาหวาน', 'รับประทานพร้อมอาหาร', 'ติดตามระดับน้ำตาลในเลือดสม่ำเสมอ', 0, 2, 18, '', 1),
(6, 1, 'ซิมวาสแตติน', 5, 'ยาลดไขมันในเลือด', 'รับประทานก่อนนอน', 'แจ้งแพทย์หากมีอาการปวดกล้ามเนื้อ', 0, 1, 55.3, '', 1),
(7, 1, 'เซอร์ทราลีน', 6, 'ยารักษาโรคซึมเศร้า', 'รับประทานตามแพทย์สั่ง', 'ห้ามหยุดยาทันทีโดยไม่ปรึกษาแพทย์', 0, 2, 60, '', 1),
(8, 1, 'ลอราทาดีน', 7, 'ยาแก้แพ้', 'รับประทานวันละครั้ง', 'อาจทำให้ง่วงซึม', 0, 1, 12.8, '', 1),
(9, 1, 'แอสไพริน', 8, 'ยาต้านการแข็งตัวของเลือด', 'รับประทานหลังอาหารทันที', 'ระวังในผู้ที่มีแนวโน้มเลือดออกง่าย', -45, 1, 10, '', 1),
(10, 1, 'เมโทโพรลอล', 9, 'ยารักษาความดันโลหิตสูง', 'รับประทานในเวลาเดียวกันทุกวัน', 'ห้ามหยุดยาทันทีโดยไม่ปรึกษาแพทย์', 0, 2, 38.4, '', 1),
(11, 1, 'เลโวไทร็อกซิน', 10, 'ยารักษาโรคไทรอยด์', 'รับประทานตอนท้องว่าง', 'ติดตามระดับฮอร์โมนไทรอยด์สม่ำเสมอ', 0, 2, 25.6, '', 1),
(12, 2, 'กาบาเพนติน', 11, 'ยารักษาอาการปวดประสาท', 'เริ่มจากขนาดต่ำและค่อยๆ เพิ่ม', 'อาจทำให้ง่วงซึม', 0, 1, 70.25, '', 1),
(13, 1, 'วาร์ฟาริน', 8, 'ยาต้านการแข็งตัวของเลือด', 'รับประทานตามแพทย์สั่งอย่างเคร่งครัด', 'ติดตาม INR อย่างสม่ำเสมอ', 0, 2, 42.9, '', 1),
(14, 1, 'เมโทเทรกเซต', 12, 'ยารักษาโรคข้ออักเสบรูมาตอยด์', 'รับประทานสัปดาห์ละครั้ง', 'ห้ามใช้ในสตรีมีครรภ์', 0, 2, 120, '', 1),
(15, 2, 'ฟลูอ็อกซิทีน', 6, 'ยารักษาโรคซึมเศร้า', 'รับประทานตอนเช้า', 'อาจต้องใช้เวลา 2-4 สัปดาห์จึงเห็นผล', 0, 1, 35.15, '', 1),
(16, 1, 'อะทอร์วาสแตติน', 5, 'ยาลดไขมันในเลือด', 'รับประทานก่อนนอน', 'ตรวจระดับเอนไซม์ตับเป็นระยะ', 0, 1, 62.7, '', 1),
(17, 1, 'โดมเพอริโดน', 13, 'ยาแก้คลื่นไส้อาเจียน', 'รับประทานก่อนอาหาร 15-30 นาที', 'ไม่ควรใช้ติดต่อกันนานเกิน 7 วัน', 0, 1, 8.5, '', 1),
(18, 1, 'ไรสเพอริโดน', 14, 'ยารักษาโรคจิตเภท', 'รับประทานตามแพทย์สั่ง', 'อาจทำให้น้ำหนักเพิ่ม', 0, 2, 95.8, '', 1),
(19, 1, 'มอนเทลูคาสต์', 1, 'ยารักษาโรคหอบหืด', 'รับประทานก่อนนอน', 'แจ้งแพทย์หากมีอาการทางจิตประสาท', 0, 1, 28.35, '', 0),
(21, 1, 'rthrt', 1, 'rthrth', 'rthr', 'rhtrt', 0, 1, 0, 'durg.png', 1),
(22, 1, '1231', 2, '123123', '23123', '234234', 0, 2, 0, 'durg.png', 1),
(23, 1, 'ๅ/-', 4, 'ๅ/-', 'ๅ/-', 'ๅ/-', 0, 5, 0, '66c8082e2b15f.png', 1),
(24, 1, '12e12', 3, '12e12', 'e12e', '12e12', 0, 2, 0, '', 1),
(26, 1, '12123', 3, '123123', '123', '12312', 0, 3, 0, '66c80d4ed1afa.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `drug_type`
--

CREATE TABLE `drug_type` (
  `drug_type_id` int(11) NOT NULL,
  `drug_type_name` varchar(100) NOT NULL,
  `branch_id` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drug_type`
--

INSERT INTO `drug_type` (`drug_type_id`, `drug_type_name`, `branch_id`) VALUES
(1, 'ยาแก้ปวดลดไข้', 1),
(2, 'ยาปฏิชีวนะ', 1),
(3, 'ยารักษาโรคกระเพาะ', 1),
(4, 'ยารักษาเบาหวาน', 1),
(5, 'ยาลดไขมันในเลือด', 1),
(6, 'ยารักษาโรคซึมเศร้า', 1),
(7, 'ยาแก้แพ้', 1),
(8, 'ยาต้านการแข็งตัวของเลือด', 1),
(9, 'ยารักษาความดันโลหิตสูง', 1),
(10, 'ยารักษาโรคหัวใจ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `follow_up_notes`
--

CREATE TABLE `follow_up_notes` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `follow_up_notes`
--

INSERT INTO `follow_up_notes` (`id`, `booking_id`, `note`, `created_at`) VALUES
(1, 9, 'นัดเทสๆ', '2024-10-14 14:59:03'),
(2, 16, 'asdfasf', '2024-10-25 16:24:56'),
(3, 18, 'wefwef', '2024-10-25 16:37:18'),
(4, 44, 'ทดสอบ', '2024-11-11 08:58:47');

-- --------------------------------------------------------

--
-- Table structure for table `gift_vouchers`
--

CREATE TABLE `gift_vouchers` (
  `voucher_id` int(11) NOT NULL,
  `voucher_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `remaining_amount` decimal(10,2) DEFAULT NULL,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `discount_type` enum('fixed','percent') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'fixed',
  `expire_date` date NOT NULL,
  `status` enum('unused','used','expired') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'unused',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `used_at` datetime DEFAULT NULL,
  `used_in_order` int(11) DEFAULT NULL,
  `first_used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `gift_vouchers`
--

INSERT INTO `gift_vouchers` (`voucher_id`, `voucher_code`, `amount`, `remaining_amount`, `max_discount`, `discount_type`, `expire_date`, `status`, `notes`, `customer_id`, `created_by`, `created_at`, `used_at`, `used_in_order`, `first_used_at`) VALUES
(5, 'GVI4EP6O7J5QGH', '15.00', NULL, '3000.00', 'percent', '2024-10-31', '', '', 6, 1, '2024-10-27 20:06:12', NULL, NULL, '2024-10-27 23:08:36'),
(6, 'GV2E00BT3RK1H9', '3500.00', NULL, NULL, 'fixed', '2024-10-31', 'expired', 'ยกเลิกเมื่อ: 2024-10-27 22:40:02\nเหตุผล: ยกเลิก\nยกเลิกโดย: ผู้ดูแลระบบ ..', NULL, 1, '2024-10-27 21:50:34', NULL, NULL, NULL),
(7, 'UWEBNHXWBEPK', '15000.00', NULL, NULL, 'fixed', '2024-10-31', '', '', 6, 1, '2024-10-27 23:28:08', NULL, NULL, '2024-10-27 23:32:43'),
(8, 'TPWLB4OTU52H', '15000.00', '15000.00', NULL, 'fixed', '2024-10-31', 'expired', '', NULL, 1, '2024-10-27 23:36:57', NULL, NULL, NULL),
(9, '7O206RK82XCT', '2000.00', NULL, NULL, 'fixed', '2024-10-31', 'expired', '', NULL, 1, '2024-10-28 21:34:42', NULL, NULL, NULL),
(10, 'V44CZVN2XL74', '1000.00', NULL, NULL, 'fixed', '2024-11-30', 'unused', '', 6, 1, '2024-10-31 18:46:09', NULL, NULL, '2024-10-31 22:34:42'),
(11, 'RMH07EQ95GSN', '5000.00', NULL, NULL, 'fixed', '2024-11-30', 'unused', '', 3, 1, '2024-11-11 20:39:22', NULL, NULL, '2024-11-11 20:39:37');

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
(1, 1, 7, NULL, 90, 170, 31.14, 90, 110, 80, 'เทส', 'เทส', 'ไม่สูบ', 'ไม่ดื่ม', 'ไม่มี', 'ไม่มี', NULL, 1, '2024-10-14 06:55:06', '2024-10-14 15:41:52'),
(2, 4, 5, NULL, 90, 170, 31.14, 90, 130, 110, 'วินิจฉัย ทดสอบ', 'หมายเหตุ ทดสอบ', 'ไม่สูบ', 'ไม่ดื่ม', 'ไม่มี', 'ไม่มี', NULL, 1, '2024-10-25 16:11:50', '2024-10-25 16:12:14'),
(3, 25, 13, NULL, 90, 180, 27.78, 90, 120, 110, NULL, NULL, 'ไม่สูบ', 'ไม่ดื่ม', 'ไม่ไมี', 'ไม่มี', NULL, 0, '2024-11-01 07:02:08', '2024-11-01 07:02:08'),
(4, 29, 3, NULL, 90, 156, 36.98, 90, 110, 90, NULL, NULL, 'ไม่สูบ', 'ไม่ดื่ม', 'ไม่มี', 'ไม่มี', NULL, 0, '2024-11-03 04:34:57', '2024-11-03 04:34:57'),
(5, 30, 3, NULL, 90, 180, 27.78, 90, 130, 110, NULL, NULL, 'ไม่สูบ', 'ไม่ดื่ม', 'ไม่มี', 'ไม่มี', NULL, 0, '2024-11-11 08:56:10', '2024-11-11 08:56:10'),
(6, 32, 2, NULL, 12312, 123, 8138.01, 12, 12, 22, NULL, NULL, 'ไม่สูบ', 'ไม่ดื่ม', '1', '1', NULL, 0, '2024-12-24 15:51:03', '2024-12-24 15:51:03'),
(7, 33, 13, NULL, 1, 1, 10000, 1, 1, 1, NULL, NULL, 'ไม่สูบ', 'ไม่ดื่ม', '1', '1', NULL, 0, '2024-12-24 15:53:51', '2024-12-24 15:53:51'),
(8, 35, 13, NULL, 110, 170, 38.06, 0, 90, 120, NULL, NULL, 'ไม่สูบ', 'ไม่ดื่ม', '-', '-', NULL, 0, '2024-12-29 16:32:04', '2024-12-29 16:35:32'),
(9, 36, 13, NULL, 110, 170, 38.06, 0, 90, 112, '.', '.', 'ไม่สูบ', 'ไม่ดื่ม', '-', '-', NULL, 1, '2024-12-30 04:33:32', '2024-12-30 04:33:38'),
(10, 37, 13, NULL, 110, 170, 38.06, 0, 90, 120, '.', '.', 'ไม่สูบ', 'ไม่ดื่ม', '-', '-', NULL, 1, '2024-12-30 15:58:31', '2024-12-30 15:58:37'),
(11, 38, 13, NULL, 110, 170, 38.06, 0, 90, 120, '.', '.', 'ไม่สูบ', 'ไม่ดื่ม', '-', '-', NULL, 1, '2024-12-31 02:41:22', '2024-12-31 02:41:27');

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
(1, 1, 'opd_drawing_1728920458.png', '2024-10-14 15:40:58'),
(2, 1, 'opd_drawing_1728920469.png', '2024-10-14 15:41:09'),
(4, 5, 'opd_drawing_1731316067.png', '2024-11-11 09:07:47'),
(5, 5, 'opd_drawing_1731318840.png', '2024-11-11 09:54:00'),
(6, 5, 'opd_drawing_1731318868.png', '2024-11-11 09:54:28'),
(7, 5, 'opd_drawing_1731321716.png', '2024-11-11 10:41:56'),
(8, 5, 'opd_drawing_1731330858.png', '2024-11-11 13:14:18'),
(10, 5, 'opd_drawing_1731378677.png', '2024-11-12 02:31:17');

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
  `seller_id` int(11) DEFAULT NULL,
  `payment_proofs` varchar(50) NOT NULL,
  `order_status` int(11) DEFAULT NULL,
  `deposit_amount` decimal(10,2) DEFAULT 0.00,
  `deposit_payment_type` enum('เงินสด','บัตรเครดิต','เงินโอน') DEFAULT NULL,
  `deposit_slip_image` varchar(255) DEFAULT NULL,
  `deposit_date` datetime DEFAULT NULL,
  `branch_id` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_course`
--

INSERT INTO `order_course` (`oc_id`, `cus_id`, `users_id`, `course_bookings_id`, `order_datetime`, `order_payment`, `order_net_total`, `order_payment_date`, `seller_id`, `payment_proofs`, `order_status`, `deposit_amount`, `deposit_payment_type`, `deposit_slip_image`, `deposit_date`, `branch_id`) VALUES
(7, 7, 1, 4, '2024-10-13 22:34:37', 'ยังไม่จ่ายเงิน', 15000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(8, 7, 1, 5, '2024-10-13 22:35:09', 'ยังไม่จ่ายเงิน', 15000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(9, 7, 1, 6, '2024-10-13 22:35:40', 'ยังไม่จ่ายเงิน', 15000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(10, 8, 1, 7, '2024-10-13 22:36:33', 'ยังไม่จ่ายเงิน', 15000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(11, 7, 1, 8, '2024-10-13 23:15:55', 'ยังไม่จ่ายเงิน', 15000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(12, 3, 1, 10, '2024-10-20 12:40:30', 'บัตรเครดิต', 40000, '2024-10-24 09:17:09', 1, '', NULL, '0.00', NULL, NULL, NULL, 1),
(13, 12, 1, 11, '2024-10-24 13:09:34', 'ยังไม่จ่ายเงิน', 15000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(14, 13, 1, 12, '2024-10-24 13:09:53', 'ยังไม่จ่ายเงิน', 15000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(15, 7, 1, 13, '2024-10-24 13:10:44', 'ยังไม่จ่ายเงิน', 65000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(16, 7, 1, 14, '2024-10-24 13:11:44', 'บัตรเครดิต', 10000, '2024-10-25 22:57:54', 1, '', NULL, '0.00', NULL, NULL, NULL, 1),
(17, 5, 1, 15, '2024-10-25 23:09:58', 'บัตรเครดิต', 15000, '2024-10-25 23:10:52', 1, '', NULL, '0.00', NULL, NULL, NULL, 1),
(18, 3, 1, 19, '2024-10-26 23:11:42', 'ยังไม่จ่ายเงิน', 30000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(19, 10, 1, 26, '2024-10-28 09:32:23', 'ยังไม่จ่ายเงิน', 15000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(20, 6, 1, 27, '2024-10-28 09:33:17', 'ยังไม่จ่ายเงิน', 0, NULL, NULL, '', NULL, '1500.00', 'บัตรเครดิต', NULL, '2024-10-31 09:27:44', 1),
(21, 6, 1, 29, '2024-10-30 08:45:27', 'บัตรเครดิต', 25000, '2024-10-30 08:47:27', 1, '', NULL, '0.00', NULL, NULL, NULL, 1),
(22, 6, 1, 30, '2024-10-30 09:11:34', 'ยังไม่จ่ายเงิน', 15000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(23, 2, 1, 31, '2024-10-30 23:16:15', 'ยังไม่จ่ายเงิน', 15000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(24, 10, 1, 32, '2024-10-30 23:19:31', 'ยังไม่จ่ายเงิน', 30000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(25, 12, 1, 33, '2024-10-30 23:22:55', 'ยังไม่จ่ายเงิน', 15000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(26, 13, 1, 34, '2024-11-01 14:25:12', 'บัตรเครดิต', 12000, '2024-11-01 23:49:44', 1, '', 1, '3000.00', 'เงินสด', NULL, '2024-11-01 23:49:24', 1),
(28, 17, 17, 35, '2024-11-02 13:36:11', 'ยังไม่จ่ายเงิน', 20000, NULL, NULL, '', 1, '0.00', NULL, NULL, NULL, 1),
(29, 17, 17, 36, '2024-11-02 14:34:14', 'ยังไม่จ่ายเงิน', 30000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(30, 17, 17, 37, '2024-11-02 14:40:37', 'ยังไม่จ่ายเงิน', 30000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(31, 17, 0, 38, '2024-11-02 14:45:43', 'ยังไม่จ่ายเงิน', 30000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(32, 17, 0, 39, '2024-11-02 14:50:56', 'ยังไม่จ่ายเงิน', 30000, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(33, 11, 1, 40, '2024-11-02 21:41:15', 'เงินสด', 14000, '2024-12-21 19:54:41', 1, '', NULL, '0.00', NULL, NULL, NULL, 1),
(34, 17, 0, 41, '2024-11-02 21:52:24', 'ยังไม่จ่ายเงิน', 0, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(35, 13, 1, 50, '2024-12-30 11:33:11', 'เงินสด', 25000, '2025-02-01 12:50:59', 1, '', NULL, '0.00', NULL, NULL, NULL, 1),
(36, 13, 1, 51, '2024-12-30 22:58:14', 'ยังไม่จ่ายเงิน', 0, NULL, NULL, '', NULL, '0.00', NULL, NULL, NULL, 1),
(37, 13, 1, 52, '2024-12-31 09:40:46', 'เงินโอน', 25000, '2025-01-03 21:55:22', 1, '6777fa5a498d8.jpg', NULL, '0.00', NULL, NULL, NULL, 1);

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
(248, 31, 8, 'drug', 1, '30.00'),
(249, 31, 8, 'tool', 5, '10.00'),
(250, 31, 8, 'tool', 3, '10.00'),
(251, 31, 8, 'accessory', 3, '5.00'),
(256, 31, 11, 'tool', 14, '3.00'),
(258, 32, 7, 'accessory', 7, '15.00'),
(259, 32, 7, 'drug', 16, '2.00'),
(260, 32, 7, 'drug', 26, '15.00'),
(360, 33, 8, 'drug', 1, '30.00'),
(361, 33, 8, 'tool', 5, '1.00'),
(362, 33, 8, 'tool', 3, '1.20'),
(363, 33, 8, 'accessory', 3, '1.00'),
(389, 34, 12, 'tool', 12, '10.00'),
(391, 34, 12, 'tool', 12, '5.00'),
(393, 34, 12, 'tool', 12, '5.00'),
(403, 32, 12, 'tool', 10, '5.00'),
(404, 32, 12, 'drug', 13, '10.00'),
(405, 29, 8, 'drug', 1, '30.00'),
(406, 29, 8, 'tool', 5, '1.00'),
(407, 29, 8, 'tool', 3, '1.20'),
(408, 29, 8, 'accessory', 3, '1.00'),
(412, 39, 8, 'drug', 1, '52.00'),
(413, 39, 8, 'tool', 5, '1.00'),
(415, 39, 8, 'accessory', 3, '1.00'),
(419, 42, 12, 'drug', 8, '20.00'),
(420, 44, 8, 'drug', 1, '30.00'),
(421, 44, 8, 'tool', 5, '1.00'),
(422, 44, 8, 'tool', 3, '1.20'),
(423, 44, 8, 'accessory', 3, '1.00'),
(427, 46, 8, 'drug', 1, '30.00'),
(428, 46, 8, 'tool', 5, '1.00'),
(429, 46, 8, 'tool', 3, '1.20'),
(430, 46, 8, 'accessory', 3, '1.00'),
(434, 47, 11, 'drug', 15, '3.00'),
(435, 47, 11, 'tool', 15, '3.00'),
(436, 47, 11, 'drug', 21, '10.00'),
(437, 48, 18, 'accessory', 14, '5.00'),
(438, 48, 18, 'drug', 6, '10.00'),
(439, 49, 8, 'drug', 1, '30.00'),
(440, 49, 8, 'tool', 5, '1.00'),
(441, 49, 8, 'tool', 3, '1.20'),
(442, 49, 8, 'accessory', 3, '1.00'),
(446, 49, 8, 'drug', 1, '111.00'),
(447, 49, 8, 'tool', 3, '3.00'),
(448, 50, 11, 'tool', 5, '5.00'),
(449, 50, 11, 'drug', 15, '20.00'),
(450, 50, 11, 'tool', 1, '5.00'),
(451, 50, 11, 'tool', 6, '5.00'),
(452, 50, 11, 'drug', 8, '30.00'),
(453, 51, 8, 'drug', 1, '30.00'),
(454, 51, 8, 'tool', 5, '1.00'),
(455, 51, 8, 'tool', 3, '1.20'),
(456, 51, 8, 'accessory', 3, '1.00'),
(460, 52, 19, 'drug', 13, '55.00'),
(461, 52, 19, 'tool', 4, '10.00'),
(463, 53, 6, 'drug', 9, '10.00'),
(464, 53, 6, 'tool', 8, '10.00'),
(465, 53, 6, 'drug', 10, '10.00'),
(466, 53, 6, 'drug', 10, '5.00'),
(467, 53, 6, 'tool', 4, '1.00'),
(468, 53, 6, 'drug', 12, '10.00'),
(470, 54, 4, 'drug', 1, '10.00'),
(471, 54, 4, 'tool', 3, '1.00'),
(472, 54, 4, 'tool', 5, '10.00'),
(473, 54, 4, 'drug', 5, '10.00'),
(474, 54, 4, 'drug', 11, '5.00'),
(477, 52, 19, 'accessory', 12, '10.00'),
(478, 43, 9, 'drug', 8, '5.00'),
(479, 43, 9, 'accessory', 4, '10.00'),
(480, 43, 9, 'drug', 9, '5.00'),
(481, 55, 14, 'drug', 4, '12.00'),
(482, 55, 14, 'drug', 8, '10.00'),
(483, 55, 14, 'drug', 1, '32.00'),
(484, 56, 13, 'tool', 7, '1.00'),
(485, 56, 13, 'drug', 16, '50.00'),
(486, 56, 13, 'drug', 14, '30.00'),
(487, 57, 19, 'accessory', 14, '2.00'),
(488, 57, 19, 'drug', 2, '50.00'),
(489, 58, 7, 'drug', 9, '10.00'),
(490, 58, 7, 'drug', 15, '15.00'),
(491, 58, 7, 'drug', 15, '5.00'),
(492, 58, 7, 'tool', 11, '5.00'),
(496, 59, 21, 'drug', 11, '50.00'),
(497, 59, 21, 'drug', 7, '15.00'),
(498, 59, 21, 'accessory', 12, '1.00'),
(499, 60, 13, 'drug', 12, '30.00'),
(500, 60, 13, 'tool', 2, '2.00'),
(501, 60, 13, 'accessory', 12, '2.00'),
(502, 62, 23, 'drug', 12, '25.00'),
(503, 64, 4, 'drug', 1, '10.00'),
(504, 64, 4, 'tool', 3, '1.00'),
(505, 64, 4, 'tool', 5, '10.00'),
(506, 64, 4, 'drug', 5, '10.00'),
(507, 64, 4, 'drug', 11, '5.00'),
(510, 65, 4, 'drug', 1, '15.00'),
(511, 65, 4, 'tool', 3, '1.00'),
(512, 65, 4, 'tool', 5, '10.00'),
(513, 65, 4, 'drug', 5, '10.00'),
(514, 65, 4, 'drug', 11, '5.00'),
(517, 65, 14, 'drug', 4, '12.00'),
(518, 65, 14, 'drug', 8, '10.00'),
(519, 65, 14, 'drug', 1, '32.00'),
(520, 66, 6, 'drug', 9, '10.00'),
(521, 66, 6, 'tool', 8, '10.00'),
(522, 66, 6, 'drug', 10, '10.00'),
(523, 66, 6, 'drug', 10, '5.00'),
(524, 66, 6, 'tool', 4, '1.00'),
(525, 66, 6, 'drug', 12, '10.00'),
(530, 11, 4, 'drug', 4, '10.00'),
(531, 11, 4, 'tool', 2, '1.00'),
(532, 11, 4, 'accessory', 9, '5.00'),
(533, 16, 11, 'drug', 1, '60.00'),
(534, 17, 4, 'drug', 4, '10.00'),
(535, 17, 4, 'tool', 2, '1.00'),
(536, 17, 4, 'accessory', 9, '5.00'),
(555, 22, 4, 'drug', 4, '10.00'),
(556, 22, 4, 'tool', 2, '1.00'),
(557, 22, 4, 'accessory', 9, '5.00'),
(558, 26, 4, 'drug', 4, '10.00'),
(559, 26, 4, 'tool', 2, '1.00'),
(560, 26, 4, 'accessory', 9, '5.00'),
(561, 33, 4, 'drug', 4, '10.00'),
(562, 33, 4, 'tool', 2, '1.00'),
(563, 33, 4, 'accessory', 9, '5.00'),
(564, 35, 12, 'drug', 1, '15.00'),
(565, 35, 12, 'tool', 1, '1.00'),
(566, 35, 12, 'drug', 3, '20.00'),
(567, 35, 12, 'drug', 9, '15.00'),
(568, 36, 12, 'accessory', 1, '5.00'),
(569, 36, 12, 'drug', 1, '25.00'),
(570, 37, 12, 'drug', 1, '15.00'),
(571, 37, 12, 'tool', 1, '1.00'),
(572, 37, 12, 'drug', 3, '20.00'),
(573, 37, 12, 'drug', 9, '15.00'),
(574, 37, 12, 'accessory', 1, '20.00');

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `od_id` int(11) NOT NULL,
  `oc_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `od_amount` int(11) NOT NULL,
  `od_price` float NOT NULL,
  `detail` text DEFAULT NULL,
  `course_notes` text DEFAULT NULL,
  `used_sessions` int(11) NOT NULL DEFAULT 0,
  `last_note_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `note_updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`od_id`, `oc_id`, `course_id`, `od_amount`, `od_price`, `detail`, `course_notes`, `used_sessions`, `last_note_update`, `note_updated_by`) VALUES
(7, 7, 4, 1, 15000, NULL, NULL, 0, '2024-10-26 07:27:48', NULL),
(8, 8, 4, 1, 15000, NULL, NULL, 0, '2024-10-26 07:27:48', NULL),
(9, 9, 4, 1, 15000, NULL, NULL, 0, '2024-10-26 07:27:48', NULL),
(10, 10, 4, 1, 15000, NULL, NULL, 0, '2024-10-26 07:27:48', NULL),
(11, 11, 4, 1, 15000, NULL, NULL, 1, '2024-10-26 07:27:48', NULL),
(23, 12, 6, 1, 15000, NULL, NULL, 0, '2024-10-26 07:27:48', NULL),
(24, 12, 12, 1, 25000, NULL, NULL, 1, '2024-10-26 07:27:48', NULL),
(25, 13, 4, 1, 15000, NULL, NULL, 0, '2024-10-26 07:27:48', NULL),
(26, 14, 4, 1, 15000, NULL, NULL, 0, '2024-10-26 07:27:48', NULL),
(27, 15, 6, 1, 15000, 'asfwsdfsd', 'qfqefwefwef', 0, '2024-10-26 08:59:40', 1),
(28, 16, 11, 1, 10000, NULL, NULL, 0, '2024-10-26 07:27:48', NULL),
(29, 17, 4, 1, 15000, NULL, NULL, 0, '2024-10-26 07:27:48', NULL),
(30, 15, 9, 1, 50000, NULL, NULL, 0, '2024-10-26 07:41:26', NULL),
(31, 18, 6, 1, 30000, NULL, NULL, 0, '2024-10-26 16:11:42', NULL),
(32, 19, 4, 1, 15000, NULL, NULL, 0, '2024-10-28 02:32:23', NULL),
(34, 21, 6, 1, 30000, NULL, NULL, 0, '2024-10-30 01:45:27', NULL),
(35, 22, 4, 1, 15000, NULL, NULL, 0, '2024-10-30 02:11:34', NULL),
(36, 23, 4, 1, 15000, NULL, NULL, 0, '2024-10-30 16:16:15', NULL),
(37, 24, 6, 1, 30000, NULL, NULL, 0, '2024-10-30 16:19:31', NULL),
(38, 25, 4, 1, 15000, NULL, NULL, 0, '2024-10-30 16:22:55', NULL),
(43, 26, 4, 1, 15000, NULL, NULL, 5, '2024-11-01 16:49:15', NULL),
(45, 28, 5, 1, 20000, NULL, NULL, 0, '2024-11-02 06:36:11', NULL),
(46, 29, 6, 1, 30000, NULL, NULL, 0, '2024-11-02 07:34:14', NULL),
(47, 30, 6, 1, 30000, NULL, NULL, 0, '2024-11-02 07:40:37', NULL),
(48, 31, 6, 1, 30000, NULL, NULL, 0, '2024-11-02 07:45:43', NULL),
(49, 32, 6, 1, 30000, NULL, NULL, 0, '2024-11-02 07:50:56', NULL),
(50, 33, 4, 1, 14000, NULL, NULL, 0, '2024-11-10 16:13:44', NULL),
(51, 34, 6, 1, 30000, NULL, NULL, 0, '2024-11-02 14:52:24', NULL),
(52, 35, 12, 1, 25000, NULL, NULL, 1, '2024-12-30 04:34:40', NULL),
(53, 36, 12, 1, 25000, NULL, NULL, 0, '2024-12-30 15:58:14', NULL),
(54, 37, 12, 1, 25000, NULL, NULL, 0, '2024-12-31 02:43:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL,
  `permission_name` varchar(100) NOT NULL,
  `page` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permission_id`, `permission_name`, `page`, `action`, `description`, `category`, `status`, `created_at`) VALUES
(1, 'บันทึกการมัดจำ', 'bill.php', 'deposit_add', 'สิทธิ์ในการบันทึกการมัดจำ', 'การเงิน', 1, '2024-11-07 09:17:57'),
(2, 'ยกเลิกการมัดจำ', 'bill.php', 'deposit_cancel', 'สิทธิ์ในการยกเลิกการมัดจำ', 'การเงิน', 1, '2024-11-07 09:17:57'),
(3, 'บันทึกการชำระเงิน', 'bill.php', 'payment_add', 'สิทธิ์ในการบันทึกการชำระเงิน', 'การเงิน', 1, '2024-11-07 09:17:57'),
(4, 'ยกเลิกการชำระเงิน', 'bill.php', 'payment_cancel', 'สิทธิ์ในการยกเลิกการชำระเงิน', 'การเงิน', 1, '2024-11-07 09:17:57'),
(5, 'บันทึกประวัติการใช้บริการ', 'bill.php', 'service_history_add', 'สิทธิ์ในการบันทึกประวัติการใช้บริการ', 'การบริการ', 1, '2024-11-07 09:17:57'),
(6, 'ยกเลิกประวัติการใช้บริการ', 'bill.php', 'service_history_cancel', 'สิทธิ์ในการยกเลิกประวัติการใช้บริการ', 'การบริการ', 1, '2024-11-07 09:17:57'),
(7, 'แก้ไขราคาคอร์ส', 'edit-order.php', 'edit_price', 'สิทธิ์ในการแก้ไขราคาคอร์ส', 'การเงิน', 1, '2024-11-07 09:17:57'),
(10, 'จัดการประเภทอุปกรณ์', 'acc-type.php', 'access', NULL, 'ข้อมูลพื้นฐาน', 1, '2024-11-10 07:16:24'),
(11, 'จัดการอุปกรณ์', 'accessories.php', 'access', NULL, 'ข้อมูลพื้นฐาน', 1, '2024-11-10 07:16:24'),
(12, 'รายละเอียดอุปกรณ์', 'accessories-detail.php', 'access', NULL, 'ข้อมูลพื้นฐาน', 1, '2024-11-10 07:16:24'),
(13, 'จัดการประเภทยา', 'drug-type.php', 'access', NULL, 'ข้อมูลพื้นฐาน', 1, '2024-11-10 07:16:24'),
(14, 'จัดการยา', 'drug.php', 'access', NULL, 'ข้อมูลพื้นฐาน', 1, '2024-11-10 07:16:24'),
(15, 'รายละเอียดยา', 'drug-detail.php', 'access', NULL, 'ข้อมูลพื้นฐาน', 1, '2024-11-10 07:16:24'),
(16, 'จัดการเครื่องมือ', 'tool.php', 'access', NULL, 'ข้อมูลพื้นฐาน', 1, '2024-11-10 07:16:24'),
(17, 'รายละเอียดเครื่องมือ', 'tool-detail.php', 'access', NULL, 'ข้อมูลพื้นฐาน', 1, '2024-11-10 07:16:24'),
(18, 'การเงิน', 'bill.php', 'access', NULL, 'การเงิน', 1, '2024-11-10 07:16:24'),
(20, 'แก้ไขรายการ', 'edit-order.php', 'access', NULL, 'การเงิน', 1, '2024-11-10 07:16:24'),
(21, 'รายการสั่งซื้อ', 'order-list.php', 'access', NULL, 'การเงิน', 1, '2024-11-10 07:16:24'),
(22, 'บัตรกำนัล', 'gift-vouchers.php', 'access', NULL, 'การเงิน', 1, '2024-11-10 07:16:24'),
(23, 'ค่าตอบแทนแพทย์', 'df.php', 'access', NULL, 'การเงิน', 1, '2024-11-10 07:16:24'),
(24, 'จัดการคอร์ส', 'course.php', 'access', NULL, 'คอร์ส', 1, '2024-11-10 07:16:24'),
(25, 'รายละเอียดคอร์ส', 'course-detail.php', 'access', NULL, 'คอร์ส', 1, '2024-11-10 07:16:24'),
(26, 'ประเภทคอร์ส', 'type-course.php', 'access', NULL, 'คอร์ส', 1, '2024-11-10 07:16:24'),
(27, 'การจอง', 'booking.php', 'access', NULL, 'การจอง', 1, '2024-11-10 07:16:24'),
(29, 'แสดงการจอง', 'booking-show.php', 'access', NULL, 'การจอง', 1, '2024-11-10 07:16:24'),
(30, 'จัดการคิว', 'queue-management.php', 'access', NULL, 'การจอง', 1, '2024-11-10 07:16:24'),
(32, 'จัดการห้อง', 'manage-rooms.php', 'access', NULL, 'ห้อง', 1, '2024-11-10 07:16:24'),
(33, 'ปฏิทินการใช้ห้อง', 'room-occupancy-calendar.php', 'access', NULL, 'ห้อง', 1, '2024-11-10 07:16:24'),
(34, 'สรุปการใช้ห้อง', 'room-service-summary.php', 'access', NULL, 'ห้อง', 1, '2024-11-10 07:16:24'),
(35, 'จัดการลูกค้า', 'customer.php', 'access', NULL, 'ลูกค้าและบริการ', 1, '2024-11-10 07:16:24'),
(36, 'รายละเอียดลูกค้า', 'customer-detail.php', 'access', NULL, 'ลูกค้าและบริการ', 1, '2024-11-10 07:16:24'),
(37, 'จัดการบริการ', 'service.php', 'access', NULL, 'ลูกค้าและบริการ', 1, '2024-11-10 07:16:24'),
(38, 'บันทึกการตรวจ', 'opd.php', 'access', NULL, 'ลูกค้าและบริการ', 1, '2024-11-10 07:16:24'),
(43, 'จัดการผู้ใช้', 'users.php', 'access', NULL, 'ระบบ', 1, '2024-11-10 07:16:24'),
(44, 'หน้าแรก Dashboard ', 'index.php', 'access', NULL, 'ระบบ', 1, '2024-11-10 07:16:24'),
(45, 'จัดการสิทธิ์การใช้งาน', 'permissions.php', 'access', NULL, 'ระบบ', 1, '2024-11-10 07:16:24');

-- --------------------------------------------------------

--
-- Table structure for table `permission_logs`
--

CREATE TABLE `permission_logs` (
  `log_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `action_type` enum('grant','revoke','modify') NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `performed_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `price_adjustment_logs`
--

CREATE TABLE `price_adjustment_logs` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `old_price` decimal(10,2) NOT NULL,
  `new_price` decimal(10,2) NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `adjusted_by` int(11) NOT NULL,
  `adjusted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `price_adjustment_logs`
--

INSERT INTO `price_adjustment_logs` (`id`, `order_id`, `course_id`, `old_price`, `new_price`, `reason`, `adjusted_by`, `adjusted_at`) VALUES
(1, 15, 6, '30000.00', '15000.00', 'ทดสอบ', 1, '2024-10-26 06:41:57'),
(2, 20, 4, '15000.00', '14500.00', 'ทดสอบ', 1, '2024-10-29 02:47:32'),
(3, 20, 4, '14500.00', '14000.00', 'ทดสอบ', 1, '2024-10-29 14:12:45'),
(4, 33, 4, '15000.00', '14000.00', 'ทดสอบ', 55, '2024-11-10 16:13:44');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `granted` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `branch_id` int(11) NOT NULL,
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `daily_status` enum('open','closed') DEFAULT 'closed'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_name`, `branch_id`, `status`, `created_at`, `daily_status`) VALUES
(1, 'ห้องตรวจที่ 1', 1, 'active', '2024-10-06 14:57:17', 'closed'),
(2, 'ห้องตรวจที่ 2', 1, 'active', '2024-10-06 15:12:20', 'closed');

-- --------------------------------------------------------

--
-- Table structure for table `room_courses`
--

CREATE TABLE `room_courses` (
  `room_course_id` int(11) NOT NULL,
  `schedule_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `room_courses`
--

INSERT INTO `room_courses` (`room_course_id`, `schedule_id`, `course_id`) VALUES
(86, 32, 9),
(87, 32, 16),
(91, 33, 18),
(92, 33, 5),
(95, 34, 6),
(96, 34, 4),
(101, 37, 6),
(102, 37, 4),
(103, 38, 6),
(104, 38, 4),
(109, 40, 6),
(110, 40, 4),
(111, 36, 6),
(112, 36, 4),
(113, 39, 6),
(114, 39, 4),
(115, 41, 6),
(116, 41, 4),
(117, 42, 6),
(118, 42, 4),
(119, 43, 6),
(120, 43, 4),
(121, 44, 6),
(122, 44, 4),
(133, 49, 9),
(134, 49, 18),
(164, 54, 6),
(165, 54, 4),
(168, 55, 4),
(169, 55, 6),
(170, 56, 18),
(171, 56, 6),
(172, 56, 4),
(173, 57, 6),
(174, 57, 4),
(175, 58, 15),
(176, 58, 6),
(177, 58, 4),
(178, 59, 6),
(179, 59, 4),
(180, 60, 4),
(181, 60, 6),
(182, 60, 15),
(183, 61, 15),
(184, 61, 6),
(185, 61, 4),
(189, 62, 15),
(190, 62, 6),
(191, 62, 4),
(192, 63, 6),
(193, 63, 4),
(194, 64, 15),
(195, 64, 4),
(196, 65, 12),
(197, 66, 12);

-- --------------------------------------------------------

--
-- Table structure for table `room_schedules`
--

CREATE TABLE `room_schedules` (
  `schedule_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `interval_minutes` int(11) NOT NULL,
  `schedule_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `room_schedules`
--

INSERT INTO `room_schedules` (`schedule_id`, `room_id`, `date`, `start_time`, `end_time`, `interval_minutes`, `schedule_name`) VALUES
(32, 1, '2024-10-08', '12:00:00', '14:00:00', 30, 'หดอกดิ'),
(33, 1, '2024-10-10', '12:00:00', '14:00:00', 30, 'ฟิลเลอร์'),
(34, 1, '2024-10-12', '12:00:00', '17:00:00', 15, 'หน้าใส่'),
(36, 1, '2024-10-13', '09:00:00', '12:00:00', 30, 'ๆำดไำดไ'),
(37, 1, '2024-10-11', '12:00:00', '16:00:00', 30, 'ะัาะัา'),
(38, 1, '2024-10-14', '12:00:00', '14:00:00', 15, 'พัีะ'),
(39, 2, '2024-10-13', '13:00:00', '17:00:00', 30, 'กเดื'),
(40, 2, '2024-10-14', '12:00:00', '16:00:00', 15, '่ยง่้ย'),
(41, 2, '2024-10-12', '12:00:00', '14:00:00', 30, 'trthr'),
(42, 1, '2024-10-15', '09:00:00', '12:00:00', 30, 'sdvwdf'),
(43, 2, '2024-10-15', '12:00:00', '15:00:00', 30, 'qfqef'),
(44, 1, '2024-10-20', '12:00:00', '17:00:00', 30, 'w'),
(49, 1, '2024-10-17', '15:00:00', '17:00:00', 30, 'กดิ'),
(54, 1, '2024-10-24', '09:00:00', '16:00:00', 30, 'ไะำไพะ'),
(55, 1, '2024-10-25', '22:00:00', '23:45:00', 30, 'qwedwef'),
(56, 1, '2024-10-29', '12:00:00', '16:00:00', 15, 'ภถ้ภถ้'),
(57, 1, '2024-10-26', '12:00:00', '23:55:00', 30, 'qaswqef'),
(58, 1, '2024-10-28', '10:00:00', '15:00:00', 30, 'เทสๆ'),
(59, 1, '2024-10-30', '09:00:00', '12:00:00', 30, 'ทดสอบ'),
(60, 1, '2024-10-31', '10:00:00', '12:00:00', 30, 'ทดสอบ'),
(61, 2, '2024-10-30', '11:00:00', '15:00:00', 30, 'ทดสแบ'),
(62, 2, '2024-10-30', '09:00:00', '11:00:00', 30, 'ทดสอบ'),
(63, 1, '2024-11-02', '10:00:00', '15:00:00', 30, 'ทดสอบ'),
(64, 1, '2024-11-11', '12:00:00', '16:00:00', 30, 'ทดสแบ'),
(65, 1, '2024-12-30', '10:00:00', '14:00:00', 30, '่่่wswg'),
(66, 1, '2024-12-31', '10:00:00', '15:00:00', 30, 'ทดสอบ');

-- --------------------------------------------------------

--
-- Table structure for table `room_status`
--

CREATE TABLE `room_status` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `daily_status` enum('open','closed') DEFAULT 'closed',
  `branch_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `room_status`
--

INSERT INTO `room_status` (`id`, `room_id`, `date`, `daily_status`, `branch_id`) VALUES
(1, 1, '2024-10-08', 'open', 1),
(2, 1, '2024-10-09', 'open', 1),
(3, 2, '2024-10-09', 'open', 1),
(4, 1, '2024-10-10', 'closed', 1),
(5, 2, '2024-10-10', 'open', 1),
(6, 2, '2024-10-08', 'open', 1),
(7, 1, '2024-10-12', 'open', 1),
(8, 2, '2024-10-12', 'open', 1),
(9, 1, '2024-10-13', 'open', 1),
(10, 2, '2024-10-13', 'open', 1),
(11, 1, '2024-10-11', 'open', 1),
(12, 1, '2024-10-14', 'open', 1),
(13, 2, '2024-10-14', 'open', 1),
(14, 1, '2024-10-15', 'open', 1),
(15, 2, '2024-10-15', 'open', 1),
(16, 1, '2024-10-20', 'open', 1),
(17, 1, '2024-10-24', 'open', 1),
(18, 2, '2024-10-24', 'open', 1),
(19, 1, '2024-10-25', 'open', 1),
(20, 1, '2024-10-29', 'open', 1),
(21, 1, '2024-10-26', 'open', 1),
(22, 2, '2024-10-26', 'open', 1),
(23, 1, '2024-10-28', 'open', 1),
(24, 1, '2024-10-30', 'open', 1),
(25, 2, '2024-10-30', 'open', 1),
(26, 1, '2024-10-31', 'open', 1),
(27, 1, '2024-11-02', 'open', 1),
(28, 1, '2024-11-11', 'open', 1),
(29, 2, '2024-11-11', 'open', 1),
(30, 1, '2024-12-30', 'open', 1),
(31, 1, '2024-12-31', 'open', 1);

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
(1, 1, 7, 4, 'Q001', '2024-10-14', '12:00:00', 'in_progress', '2024-10-14 06:37:23', '2024-10-14 06:37:25', ''),
(2, 1, 2, NULL, 'Q001', '2024-10-20', '12:36:00', 'cancelled', '2024-10-20 05:36:40', '2024-10-20 05:36:54', ''),
(3, 1, 3, 10, 'Q002', '2024-10-20', '12:00:00', 'in_progress', '2024-10-20 05:40:40', '2024-10-20 05:41:39', ''),
(4, 1, 5, 15, 'Q001', '2024-10-25', '22:00:00', 'in_progress', '2024-10-25 16:10:05', '2024-10-25 16:10:07', ''),
(10, 1, 3, 19, 'Q001', '2024-10-26', '23:30:00', 'in_progress', '2024-10-26 16:53:43', '2024-10-26 16:55:15', ''),
(11, 1, 1, 20, 'Q002', '2024-10-26', '23:34:00', 'waiting', '2024-10-26 16:54:01', '2024-10-26 16:54:01', ''),
(12, 1, 5, 21, 'Q003', '2024-10-26', '23:56:00', 'waiting', '2024-10-26 16:56:24', '2024-10-26 16:56:24', ''),
(13, 1, 2, 22, 'Q001', '2024-10-27', '00:06:00', 'completed', '2024-10-26 17:06:19', '2024-10-26 17:35:13', ''),
(14, 1, 6, 23, 'Q002', '2024-10-27', '00:06:00', 'completed', '2024-10-26 17:06:33', '2024-10-26 17:35:03', ''),
(15, 1, 6, 24, 'Q003', '2024-10-27', '00:06:00', 'in_progress', '2024-10-26 17:06:42', '2024-10-27 03:22:49', ''),
(16, 1, 10, 25, 'Q004', '2024-10-27', '10:23:00', 'cancelled', '2024-10-27 03:23:16', '2024-10-27 03:23:43', ''),
(17, 1, 6, 27, 'Q001', '2024-10-28', '10:00:00', 'in_progress', '2024-10-28 02:33:48', '2024-10-28 02:34:09', ''),
(18, 1, 7, 28, 'Q002', '2024-10-28', '19:06:00', 'in_progress', '2024-10-28 12:07:04', '2024-10-28 12:07:07', ''),
(19, 1, 10, 26, 'Q001', '2024-10-29', '12:00:00', 'in_progress', '2024-10-29 01:33:34', '2024-10-29 01:33:41', ''),
(20, 1, 6, 29, 'Q001', '2024-10-30', '09:00:00', 'in_progress', '2024-10-30 01:45:37', '2024-10-30 01:45:40', ''),
(21, 1, 12, 33, 'Q002', '2024-10-30', '09:30:00', 'in_progress', '2024-10-30 16:30:33', '2024-10-30 16:31:18', ''),
(22, 1, 2, 31, 'Q003', '2024-10-30', '09:30:00', 'waiting', '2024-10-30 16:30:38', '2024-10-30 16:30:38', ''),
(23, 1, 10, 32, 'Q004', '2024-10-30', '14:00:00', 'completed', '2024-10-30 16:30:42', '2024-10-30 16:31:14', ''),
(24, 1, 6, 30, 'Q001', '2024-10-31', '10:00:00', 'in_progress', '2024-10-31 10:48:59', '2024-10-31 10:49:04', ''),
(25, 1, 13, 34, 'Q001', '2024-11-01', '14:01:00', 'in_progress', '2024-11-01 07:01:28', '2024-11-01 16:52:21', ''),
(26, 1, 17, 36, 'Q001', '2024-11-02', '14:30:00', 'in_progress', '2024-11-02 07:34:14', '2024-11-02 08:29:48', ''),
(27, 1, 17, 37, 'Q002', '2024-11-02', '14:00:00', 'in_progress', '2024-11-02 07:40:37', '2024-11-02 08:30:16', ''),
(28, 1, 17, 38, 'Q003', '2024-11-02', '13:30:00', 'completed', '2024-11-02 07:45:43', '2024-11-02 08:30:10', ''),
(29, 1, 3, 42, 'Q001', '2024-11-03', '11:33:00', 'in_progress', '2024-11-03 04:34:07', '2024-11-03 04:34:11', ''),
(30, 1, 3, 43, 'Q001', '2024-11-11', '15:49:00', 'in_progress', '2024-11-11 08:50:12', '2024-11-11 08:50:14', ''),
(31, 1, 5, 45, 'Q001', '2024-11-19', '16:41:00', 'waiting', '2024-11-19 09:42:05', '2024-11-19 09:42:05', ''),
(32, 1, 2, 46, 'Q001', '2024-12-24', '22:26:00', 'in_progress', '2024-12-24 15:26:55', '2024-12-24 15:26:57', ''),
(33, 1, 13, 47, 'Q002', '2024-12-24', '22:53:00', 'in_progress', '2024-12-24 15:53:31', '2024-12-24 15:53:36', ''),
(34, 1, 17, 48, 'Q001', '2024-12-29', '22:37:00', 'in_progress', '2024-12-29 15:37:11', '2024-12-29 15:37:13', ''),
(35, 1, 13, 49, 'Q002', '2024-12-29', '23:06:00', 'in_progress', '2024-12-29 16:07:05', '2024-12-29 16:07:12', ''),
(36, 1, 13, 50, 'Q001', '2024-12-30', '10:30:00', 'completed', '2024-12-30 04:33:19', '2024-12-30 04:33:45', ''),
(37, 1, 13, 51, 'Q002', '2024-12-30', '13:00:00', 'completed', '2024-12-30 15:58:21', '2024-12-30 15:58:42', ''),
(38, 1, 13, 52, 'Q001', '2024-12-31', '12:00:00', 'completed', '2024-12-31 02:40:53', '2024-12-31 02:41:33', '');

-- --------------------------------------------------------

--
-- Table structure for table `service_staff_records`
--

CREATE TABLE `service_staff_records` (
  `staff_record_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `staff_type` enum('doctor','nurse','seller') NOT NULL,
  `staff_df` decimal(10,2) NOT NULL,
  `staff_df_type` enum('amount','percent') NOT NULL,
  `branch_id` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `service_staff_records`
--

INSERT INTO `service_staff_records` (`staff_record_id`, `service_id`, `staff_id`, `staff_type`, `staff_df`, `staff_df_type`, `branch_id`) VALUES
(5, 25, 52, 'doctor', '3.00', 'percent', 1),
(6, 25, 57, 'nurse', '350.00', 'amount', 1),
(7, 25, 58, 'nurse', '450.00', 'amount', 1),
(8, 25, 55, 'seller', '150.00', 'amount', 1),
(9, 25, 57, 'seller', '350.00', 'amount', 1);

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
(24, '2024-08-24 18:46:00', 1, 123, '', 11, 'tool', 1, 1, 1, '', '2024-08-24 11:47:04', '2024-08-24 11:47:04'),
(25, '2024-10-25 22:39:36', 1, 60, NULL, 0, 'drug', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ', '2024-10-25 15:39:36', '2024-10-25 15:39:36'),
(26, '2024-10-25 22:41:00', 1, 60, NULL, 0, 'drug', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ', '2024-10-25 15:41:00', '2024-10-25 15:41:00'),
(27, '2024-10-25 22:51:48', 1, 60, NULL, 0, 'drug', 1, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000016', '2024-10-25 15:51:48', '2024-10-25 15:51:48'),
(28, '2024-10-25 22:52:10', 1, 60, NULL, 0, 'drug', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ', '2024-10-25 15:52:10', '2024-10-25 15:52:10'),
(29, '2024-10-25 22:52:24', 1, 60, NULL, 0, 'drug', 1, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000016', '2024-10-25 15:52:24', '2024-10-25 15:52:24'),
(30, '2024-10-25 22:52:30', 1, 60, NULL, 0, 'drug', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ', '2024-10-25 15:52:30', '2024-10-25 15:52:30'),
(31, '2024-10-25 22:57:47', 1, 60, NULL, 0, 'drug', 1, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000016', '2024-10-25 15:57:47', '2024-10-25 15:57:47'),
(32, '2024-10-25 22:57:54', 1, 60, NULL, 0, 'drug', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000016', '2024-10-25 15:57:54', '2024-10-25 15:57:54'),
(33, '2024-10-25 23:10:52', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000017', '2024-10-25 16:10:52', '2024-10-25 16:10:52'),
(34, '2024-10-25 23:10:52', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000017', '2024-10-25 16:10:52', '2024-10-25 16:10:52'),
(35, '2024-10-25 23:10:52', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000017', '2024-10-25 16:10:52', '2024-10-25 16:10:52'),
(36, '2024-10-29 09:34:15', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 02:34:15', '2024-10-29 02:34:15'),
(37, '2024-10-29 09:34:15', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 02:34:15', '2024-10-29 02:34:15'),
(38, '2024-10-29 09:34:15', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 02:34:15', '2024-10-29 02:34:15'),
(39, '2024-10-29 09:35:23', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 02:35:23', '2024-10-29 02:35:23'),
(40, '2024-10-29 09:35:23', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 02:35:23', '2024-10-29 02:35:23'),
(41, '2024-10-29 09:35:23', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 02:35:23', '2024-10-29 02:35:23'),
(42, '2024-10-29 09:35:39', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 02:35:39', '2024-10-29 02:35:39'),
(43, '2024-10-29 09:35:39', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 02:35:39', '2024-10-29 02:35:39'),
(44, '2024-10-29 09:35:39', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 02:35:39', '2024-10-29 02:35:39'),
(45, '2024-10-29 09:35:45', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 02:35:45', '2024-10-29 02:35:45'),
(46, '2024-10-29 09:35:45', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 02:35:45', '2024-10-29 02:35:45'),
(47, '2024-10-29 09:35:45', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 02:35:45', '2024-10-29 02:35:45'),
(48, '2024-10-29 09:36:02', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 02:36:02', '2024-10-29 02:36:02'),
(49, '2024-10-29 09:36:02', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 02:36:02', '2024-10-29 02:36:02'),
(50, '2024-10-29 09:36:02', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 02:36:02', '2024-10-29 02:36:02'),
(51, '2024-10-29 09:47:08', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 02:47:08', '2024-10-29 02:47:08'),
(52, '2024-10-29 09:47:08', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 02:47:08', '2024-10-29 02:47:08'),
(53, '2024-10-29 09:47:08', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 02:47:08', '2024-10-29 02:47:08'),
(54, '2024-10-29 10:03:47', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 03:03:47', '2024-10-29 03:03:47'),
(55, '2024-10-29 10:03:47', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 03:03:47', '2024-10-29 03:03:47'),
(56, '2024-10-29 10:03:47', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 03:03:47', '2024-10-29 03:03:47'),
(57, '2024-10-29 20:20:26', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 13:20:26', '2024-10-29 13:20:26'),
(58, '2024-10-29 20:20:26', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 13:20:26', '2024-10-29 13:20:26'),
(59, '2024-10-29 20:20:26', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 13:20:26', '2024-10-29 13:20:26'),
(60, '2024-10-29 20:32:40', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 13:32:40', '2024-10-29 13:32:40'),
(61, '2024-10-29 20:32:40', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 13:32:40', '2024-10-29 13:32:40'),
(62, '2024-10-29 20:32:40', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 13:32:40', '2024-10-29 13:32:40'),
(63, '2024-10-29 21:12:21', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 14:12:21', '2024-10-29 14:12:21'),
(64, '2024-10-29 21:12:21', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 14:12:21', '2024-10-29 14:12:21'),
(65, '2024-10-29 21:12:21', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 14:12:21', '2024-10-29 14:12:21'),
(66, '2024-10-29 21:14:11', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 14:14:11', '2024-10-29 14:14:11'),
(67, '2024-10-29 21:14:11', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 14:14:11', '2024-10-29 14:14:11'),
(68, '2024-10-29 21:14:11', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 14:14:11', '2024-10-29 14:14:11'),
(69, '2024-10-29 21:47:45', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 14:47:45', '2024-10-29 14:47:45'),
(70, '2024-10-29 21:47:45', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 14:47:45', '2024-10-29 14:47:45'),
(71, '2024-10-29 21:47:45', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 14:47:45', '2024-10-29 14:47:45'),
(72, '2024-10-29 21:47:58', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 14:47:58', '2024-10-29 14:47:58'),
(73, '2024-10-29 21:47:58', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 14:47:58', '2024-10-29 14:47:58'),
(74, '2024-10-29 21:47:58', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-29 14:47:58', '2024-10-29 14:47:58'),
(75, '2024-10-29 22:10:53', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 15:10:53', '2024-10-29 15:10:53'),
(76, '2024-10-29 22:10:53', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 15:10:53', '2024-10-29 15:10:53'),
(77, '2024-10-29 22:10:53', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-29 15:10:53', '2024-10-29 15:10:53'),
(78, '2024-10-31 09:18:38', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-31 02:18:38', '2024-10-31 02:18:38'),
(79, '2024-10-31 09:18:38', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-31 02:18:38', '2024-10-31 02:18:38'),
(80, '2024-10-31 09:18:38', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-31 02:18:38', '2024-10-31 02:18:38'),
(81, '2024-10-31 09:27:05', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-31 02:27:05', '2024-10-31 02:27:05'),
(82, '2024-10-31 09:27:05', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-31 02:27:05', '2024-10-31 02:27:05'),
(83, '2024-10-31 09:27:05', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-31 02:27:05', '2024-10-31 02:27:05'),
(84, '2024-10-31 09:28:37', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-31 02:28:37', '2024-10-31 02:28:37'),
(85, '2024-10-31 09:28:37', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-31 02:28:37', '2024-10-31 02:28:37'),
(86, '2024-10-31 09:28:37', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-31 02:28:37', '2024-10-31 02:28:37'),
(87, '2024-10-31 09:49:05', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-31 02:49:05', '2024-10-31 02:49:05'),
(88, '2024-10-31 09:49:05', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-31 02:49:05', '2024-10-31 02:49:05'),
(89, '2024-10-31 09:49:05', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-31 02:49:05', '2024-10-31 02:49:05'),
(90, '2024-10-31 09:51:57', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-31 02:51:57', '2024-10-31 02:51:57'),
(91, '2024-10-31 09:51:57', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-31 02:51:57', '2024-10-31 02:51:57'),
(92, '2024-10-31 09:51:57', 1, 5, NULL, 0, 'drug', 13, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-31 02:51:57', '2024-10-31 02:51:57'),
(93, '2024-10-31 09:51:57', 1, 1, NULL, 0, 'tool', 5, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-31 02:51:57', '2024-10-31 02:51:57'),
(94, '2024-10-31 09:52:13', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-31 02:52:13', '2024-10-31 02:52:13'),
(95, '2024-10-31 09:52:13', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-31 02:52:13', '2024-10-31 02:52:13'),
(96, '2024-10-31 09:52:13', 1, 5, NULL, 0, 'drug', 13, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-31 02:52:13', '2024-10-31 02:52:13'),
(97, '2024-10-31 09:52:13', 1, 1, NULL, 0, 'tool', 5, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-31 02:52:13', '2024-10-31 02:52:13'),
(98, '2024-10-31 09:52:26', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-31 02:52:26', '2024-10-31 02:52:26'),
(99, '2024-10-31 09:52:26', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-31 02:52:26', '2024-10-31 02:52:26'),
(100, '2024-10-31 09:52:26', 1, 5, NULL, 0, 'drug', 13, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-31 02:52:26', '2024-10-31 02:52:26'),
(101, '2024-10-31 09:52:26', 1, 1, NULL, 0, 'tool', 5, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000020', '2024-10-31 02:52:26', '2024-10-31 02:52:26'),
(102, '2024-10-31 10:35:54', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-31 03:35:54', '2024-10-31 03:35:54'),
(103, '2024-10-31 10:35:54', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-31 03:35:54', '2024-10-31 03:35:54'),
(104, '2024-10-31 10:35:54', 1, 5, NULL, 0, 'drug', 13, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-31 03:35:54', '2024-10-31 03:35:54'),
(105, '2024-10-31 10:35:54', 1, 1, NULL, 0, 'tool', 5, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000020', '2024-10-31 03:35:54', '2024-10-31 03:35:54'),
(106, '2024-10-31 12:54:00', 1, 20, '2024-10-31', 5.4, 'drug', 4, 1, 1, '', '2024-10-31 05:55:06', '2024-10-31 05:55:06'),
(107, '2024-11-01 23:49:44', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000026', '2024-11-01 16:49:44', '2024-11-01 16:49:44'),
(108, '2024-11-01 23:49:44', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000026', '2024-11-01 16:49:44', '2024-11-01 16:49:44'),
(109, '2024-11-01 23:49:44', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000026', '2024-11-01 16:49:44', '2024-11-01 16:49:44'),
(110, '2024-11-10 22:20:38', 1, 10, NULL, 0, 'tool', 12, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000034', '2024-11-10 15:20:38', '2024-11-10 15:20:38'),
(111, '2024-11-10 22:20:38', 1, 5, NULL, 0, 'tool', 12, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000034', '2024-11-10 15:20:38', '2024-11-10 15:20:38'),
(112, '2024-11-10 22:20:38', 1, 5, NULL, 0, 'tool', 12, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000034', '2024-11-10 15:20:38', '2024-11-10 15:20:38'),
(113, '2024-11-10 22:26:37', 55, 10, NULL, 0, 'tool', 12, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000034', '2024-11-10 15:26:37', '2024-11-10 15:26:37'),
(114, '2024-11-10 22:26:37', 55, 5, NULL, 0, 'tool', 12, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000034', '2024-11-10 15:26:37', '2024-11-10 15:26:37'),
(115, '2024-11-10 22:26:37', 55, 5, NULL, 0, 'tool', 12, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000034', '2024-11-10 15:26:37', '2024-11-10 15:26:37'),
(116, '2024-11-10 22:26:50', 55, 10, NULL, 0, 'tool', 12, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000034', '2024-11-10 15:26:50', '2024-11-10 15:26:50'),
(117, '2024-11-10 22:26:50', 55, 5, NULL, 0, 'tool', 12, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000034', '2024-11-10 15:26:50', '2024-11-10 15:26:50'),
(118, '2024-11-10 22:26:50', 55, 5, NULL, 0, 'tool', 12, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000034', '2024-11-10 15:26:50', '2024-11-10 15:26:50'),
(119, '2024-11-10 22:27:33', 55, 10, NULL, 0, 'tool', 12, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000034', '2024-11-10 15:27:33', '2024-11-10 15:27:33'),
(120, '2024-11-10 22:27:33', 55, 5, NULL, 0, 'tool', 12, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000034', '2024-11-10 15:27:33', '2024-11-10 15:27:33'),
(121, '2024-11-10 22:27:33', 55, 5, NULL, 0, 'tool', 12, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000034', '2024-11-10 15:27:33', '2024-11-10 15:27:33'),
(122, '2024-12-21 19:53:31', 1, 10, NULL, 0, 'tool', 12, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000034', '2024-12-21 12:53:31', '2024-12-21 12:53:31'),
(123, '2024-12-21 19:53:31', 1, 5, NULL, 0, 'tool', 12, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000034', '2024-12-21 12:53:31', '2024-12-21 12:53:31'),
(124, '2024-12-21 19:53:31', 1, 5, NULL, 0, 'tool', 12, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000034', '2024-12-21 12:53:31', '2024-12-21 12:53:31'),
(125, '2024-12-21 19:54:41', 1, 30, NULL, 0, 'drug', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000033', '2024-12-21 12:54:41', '2024-12-21 12:54:41'),
(126, '2024-12-21 19:54:41', 1, 1, NULL, 0, 'tool', 5, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000033', '2024-12-21 12:54:41', '2024-12-21 12:54:41'),
(127, '2024-12-21 19:54:41', 1, 1.2, NULL, 0, 'tool', 3, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000033', '2024-12-21 12:54:41', '2024-12-21 12:54:41'),
(128, '2024-12-21 19:54:41', 1, 1, NULL, 0, 'accessory', 3, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000033', '2024-12-21 12:54:41', '2024-12-21 12:54:41'),
(129, '2024-12-21 19:54:41', 1, 10, NULL, 0, 'drug', 4, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000033', '2024-12-21 12:54:41', '2024-12-21 12:54:41'),
(130, '2024-12-21 19:54:41', 1, 1, NULL, 0, 'tool', 2, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000033', '2024-12-21 12:54:41', '2024-12-21 12:54:41'),
(131, '2024-12-21 19:54:41', 1, 5, NULL, 0, 'accessory', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000033', '2024-12-21 12:54:41', '2024-12-21 12:54:41'),
(132, '2024-12-30 11:31:00', 1, 30, '', 16, 'drug', 1, 1, 1, '', '2024-12-30 04:32:18', '2024-12-30 04:32:18'),
(133, '2024-12-30 11:35:58', 1, 15, NULL, 0, 'drug', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000035', '2024-12-30 04:35:58', '2024-12-30 04:35:58'),
(134, '2024-12-30 11:35:58', 1, 1, NULL, 0, 'tool', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000035', '2024-12-30 04:35:58', '2024-12-30 04:35:58'),
(135, '2024-12-30 11:35:58', 1, 20, NULL, 0, 'drug', 3, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000035', '2024-12-30 04:35:58', '2024-12-30 04:35:58'),
(136, '2024-12-30 11:35:58', 1, 15, NULL, 0, 'drug', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000035', '2024-12-30 04:35:58', '2024-12-30 04:35:58'),
(137, '2024-12-30 11:59:00', 1, 50, '', 15.5, 'drug', 1, 1, 1, '', '2024-12-30 05:00:58', '2024-12-30 05:00:58'),
(138, '2024-12-30 12:08:00', 1, 50, '', 15.1, 'drug', 1, 1, 1, 'ทดสอบ', '2024-12-30 05:09:10', '2024-12-30 05:09:10'),
(139, '2024-12-30 22:59:59', 1, 5, NULL, 0, 'accessory', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000036', '2024-12-30 15:59:59', '2024-12-30 15:59:59'),
(140, '2024-12-30 23:01:40', 1, 5, NULL, 0, 'accessory', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000036', '2024-12-30 16:01:40', '2024-12-30 16:01:40'),
(141, '2024-12-30 23:04:09', 1, 5, NULL, 0, 'accessory', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000036', '2024-12-30 16:04:09', '2024-12-30 16:04:09'),
(142, '2024-12-30 23:04:09', 1, 25, NULL, 0, 'drug', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000036', '2024-12-30 16:04:09', '2024-12-30 16:04:09'),
(143, '2024-12-31 09:42:36', 1, 15, NULL, 0, 'drug', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2024-12-31 02:42:36', '2024-12-31 02:42:36'),
(144, '2024-12-31 09:42:36', 1, 1, NULL, 0, 'tool', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2024-12-31 02:42:36', '2024-12-31 02:42:36'),
(145, '2024-12-31 09:42:36', 1, 20, NULL, 0, 'drug', 3, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2024-12-31 02:42:36', '2024-12-31 02:42:36'),
(146, '2024-12-31 09:42:36', 1, 15, NULL, 0, 'drug', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2024-12-31 02:42:36', '2024-12-31 02:42:36'),
(147, '2024-12-31 09:42:36', 1, 20, NULL, 0, 'accessory', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2024-12-31 02:42:36', '2024-12-31 02:42:36'),
(148, '2025-01-01 18:36:56', 1, 15, NULL, 0, 'drug', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-01 11:36:56', '2025-01-01 11:36:56'),
(149, '2025-01-01 18:36:56', 1, 1, NULL, 0, 'tool', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-01 11:36:56', '2025-01-01 11:36:56'),
(150, '2025-01-01 18:36:56', 1, 20, NULL, 0, 'drug', 3, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-01 11:36:56', '2025-01-01 11:36:56'),
(151, '2025-01-01 18:36:56', 1, 15, NULL, 0, 'drug', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-01 11:36:56', '2025-01-01 11:36:56'),
(152, '2025-01-01 18:36:56', 1, 20, NULL, 0, 'accessory', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-01 11:36:56', '2025-01-01 11:36:56'),
(153, '2025-01-01 18:37:18', 1, 15, NULL, 0, 'drug', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-01 11:37:18', '2025-01-01 11:37:18'),
(154, '2025-01-01 18:37:18', 1, 1, NULL, 0, 'tool', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-01 11:37:18', '2025-01-01 11:37:18'),
(155, '2025-01-01 18:37:18', 1, 20, NULL, 0, 'drug', 3, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-01 11:37:18', '2025-01-01 11:37:18'),
(156, '2025-01-01 18:37:18', 1, 15, NULL, 0, 'drug', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-01 11:37:18', '2025-01-01 11:37:18'),
(157, '2025-01-01 18:37:18', 1, 20, NULL, 0, 'accessory', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-01 11:37:18', '2025-01-01 11:37:18'),
(166, '2025-01-01 19:15:58', 1, 15, NULL, 0, 'drug', 1, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000037', '2025-01-01 12:15:58', '2025-01-01 12:15:58'),
(167, '2025-01-01 19:15:58', 1, 1, NULL, 0, 'tool', 1, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000037', '2025-01-01 12:15:58', '2025-01-01 12:15:58'),
(168, '2025-01-01 19:15:58', 1, 20, NULL, 0, 'drug', 3, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000037', '2025-01-01 12:15:58', '2025-01-01 12:15:58'),
(169, '2025-01-01 19:15:58', 1, 15, NULL, 0, 'drug', 9, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000037', '2025-01-01 12:15:58', '2025-01-01 12:15:58'),
(170, '2025-01-01 19:15:58', 1, 20, NULL, 0, 'accessory', 1, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000037', '2025-01-01 12:15:58', '2025-01-01 12:15:58'),
(171, '2025-01-01 19:17:45', 1, 15, NULL, 0, 'drug', 1, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000037', '2025-01-01 12:17:45', '2025-01-01 12:17:45'),
(172, '2025-01-01 19:17:45', 1, 1, NULL, 0, 'tool', 1, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000037', '2025-01-01 12:17:45', '2025-01-01 12:17:45'),
(173, '2025-01-01 19:17:45', 1, 20, NULL, 0, 'drug', 3, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000037', '2025-01-01 12:17:45', '2025-01-01 12:17:45'),
(174, '2025-01-01 19:17:45', 1, 15, NULL, 0, 'drug', 9, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000037', '2025-01-01 12:17:45', '2025-01-01 12:17:45'),
(175, '2025-01-01 19:17:45', 1, 20, NULL, 0, 'accessory', 1, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000037', '2025-01-01 12:17:45', '2025-01-01 12:17:45'),
(176, '2025-01-01 19:18:51', 1, 15, NULL, 0, 'drug', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-01 12:18:51', '2025-01-01 12:18:51'),
(177, '2025-01-01 19:18:51', 1, 1, NULL, 0, 'tool', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-01 12:18:51', '2025-01-01 12:18:51'),
(178, '2025-01-01 19:18:51', 1, 20, NULL, 0, 'drug', 3, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-01 12:18:51', '2025-01-01 12:18:51'),
(179, '2025-01-01 19:18:51', 1, 15, NULL, 0, 'drug', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-01 12:18:51', '2025-01-01 12:18:51'),
(180, '2025-01-01 19:18:51', 1, 20, NULL, 0, 'accessory', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-01 12:18:51', '2025-01-01 12:18:51'),
(181, '2025-01-01 19:19:03', 1, 15, NULL, 0, 'drug', 1, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000037', '2025-01-01 12:19:03', '2025-01-01 12:19:03'),
(182, '2025-01-01 19:19:03', 1, 1, NULL, 0, 'tool', 1, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000037', '2025-01-01 12:19:03', '2025-01-01 12:19:03'),
(183, '2025-01-01 19:19:03', 1, 20, NULL, 0, 'drug', 3, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000037', '2025-01-01 12:19:03', '2025-01-01 12:19:03'),
(184, '2025-01-01 19:19:03', 1, 15, NULL, 0, 'drug', 9, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000037', '2025-01-01 12:19:03', '2025-01-01 12:19:03'),
(185, '2025-01-01 19:19:03', 1, 20, NULL, 0, 'accessory', 1, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000037', '2025-01-01 12:19:03', '2025-01-01 12:19:03'),
(186, '2025-01-03 21:55:22', 1, 15, NULL, 0, 'drug', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-03 14:55:22', '2025-01-03 14:55:22'),
(187, '2025-01-03 21:55:22', 1, 1, NULL, 0, 'tool', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-03 14:55:22', '2025-01-03 14:55:22'),
(188, '2025-01-03 21:55:22', 1, 20, NULL, 0, 'drug', 3, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-03 14:55:22', '2025-01-03 14:55:22'),
(189, '2025-01-03 21:55:22', 1, 15, NULL, 0, 'drug', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-03 14:55:22', '2025-01-03 14:55:22'),
(190, '2025-01-03 21:55:22', 1, 20, NULL, 0, 'accessory', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000037', '2025-01-03 14:55:22', '2025-01-03 14:55:22'),
(191, '2025-02-01 12:50:51', 1, 15, NULL, 0, 'drug', 1, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000035', '2025-02-01 05:50:51', '2025-02-01 05:50:51'),
(192, '2025-02-01 12:50:51', 1, 1, NULL, 0, 'tool', 1, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000035', '2025-02-01 05:50:51', '2025-02-01 05:50:51'),
(193, '2025-02-01 12:50:51', 1, 20, NULL, 0, 'drug', 3, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000035', '2025-02-01 05:50:51', '2025-02-01 05:50:51'),
(194, '2025-02-01 12:50:51', 1, 15, NULL, 0, 'drug', 9, 1, 1, 'คืนสต๊อกจากการยกเลิกชำระเงิน ORDER-000035', '2025-02-01 05:50:51', '2025-02-01 05:50:51'),
(195, '2025-02-01 12:50:59', 1, 15, NULL, 0, 'drug', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000035', '2025-02-01 05:50:59', '2025-02-01 05:50:59'),
(196, '2025-02-01 12:50:59', 1, 1, NULL, 0, 'tool', 1, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000035', '2025-02-01 05:50:59', '2025-02-01 05:50:59'),
(197, '2025-02-01 12:50:59', 1, 20, NULL, 0, 'drug', 3, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000035', '2025-02-01 05:50:59', '2025-02-01 05:50:59'),
(198, '2025-02-01 12:50:59', 1, 15, NULL, 0, 'drug', 9, 1, 1, 'ตัดสต๊อกจากการใช้บริการ ORDER-000035', '2025-02-01 05:50:59', '2025-02-01 05:50:59');

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
(1, 'เครื่องนับเม็ดยา', 1, 'ใช้สำหรับนับเม็ดยาอัตโนมัติ ความแม่นยำสูง', 120, 500, 1, 1),
(2, 'ตู้เย็นเก็บยา', 1, 'ตู้เย็นควบคุมอุณหภูมิสำหรับเก็บยาที่ต้องการความเย็น', -3, 1200, 1, 1),
(3, 'เครื่องบดยา', 1, 'ใช้สำหรับบดยาเม็ดให้เป็นผง', -1, 300, 1, 1),
(4, 'เครื่องผสมยา', 1, 'สำหรับผสมยาในรูปแบบของเหลว', 0, 800, 1, 1),
(5, 'ชุดเครื่องมือแบ่งบรรจุยา', 1, 'ใช้สำหรับแบ่งบรรจุยาเป็นซอง', -1, 200, 2, 1),
(6, 'เครื่องพิมพ์ฉลากยา', 1, 'พิมพ์ฉลากยาอัตโนมัติ ความละเอียดสูง', 0, 1000, 1, 1),
(7, 'ถาดจัดยา', 1, 'ถาดพลาสติกสำหรับจัดเรียงยาก่อนบรรจุ', 0, 50, 1, 1),
(8, 'เครื่องวัดความดันโลหิต', 1, 'ใช้วัดความดันโลหิตแบบดิจิทัล', 0, 150, 1, 1),
(9, 'เครื่องวัดระดับน้ำตาลในเลือด', 1, 'ใช้ตรวจวัดระดับน้ำตาลในเลือด', 0, 200, 1, 1),
(10, 'ชุดให้สารละลายทางหลอดเลือดดำ', 1, 'อุปกรณ์สำหรับให้สารละลายทางหลอดเลือดดำ', 0, 100, 2, 1),
(11, 'เครื่องพ่นยา', 1, 'ใช้สำหรับพ่นยาละอองฝอย', 0, 300, 1, 1),
(12, 'ตู้เก็บยาควบคุมพิเศษ', 1, 'ตู้เก็บยาที่มีระบบล็อคพิเศษสำหรับยาควบคุม', 20, 800, 1, 1),
(13, 'เครื่องวัดอุณหภูมิร่างกาย', 1, 'เทอร์โมมิเตอร์ดิจิทัลสำหรับวัดไข้', 0, 50, 1, 1),
(14, 'ชุดทำแผล', 1, 'อุปกรณ์สำหรับทำแผลและจ่ายยาทาภายนอก', 0, 100, 2, 1),
(15, 'เครื่องชั่งน้ำหนักดิจิทัล', 1, 'ใช้ชั่งน้ำหนักผู้ป่วยเพื่อคำนวณปริมาณยา', 0, 300, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `unit`
--

CREATE TABLE `unit` (
  `unit_id` int(11) NOT NULL,
  `unit_name` varchar(50) NOT NULL,
  `branch_id` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unit`
--

INSERT INTO `unit` (`unit_id`, `unit_name`, `branch_id`) VALUES
(1, 'เม็ด', 1),
(2, 'แคปซูล', 1),
(3, 'ขวด', 1),
(4, 'หลอด', 1),
(5, 'แผง', 1),
(6, 'ซอง', 1),
(7, 'เข็ม', 1),
(8, 'หลอดฉีดยา', 1),
(9, 'แผ่น', 1),
(10, 'ชุด', 1),
(11, 'กระปุก', 1),
(12, 'กล่อง', 1),
(13, 'มิลลิลิตร', 1),
(14, 'กรัม', 1),
(15, 'แอมพูล', 1);

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
(53, 'แพทย์ 2', 'แพทย์ 2', 'แพทย์ 2', 'แพทย์ 2', 'แพทย์ 2', '1234564897', 3, 'ว12345645', 2, 1),
(54, 'แพทย์ 3', 'แพทย์ 3', 'แพทย์ 3', 'แพทย์ 3', 'แพทย์ 3', '1234564897', 3, 'ว12345645', 1, 1),
(55, '1', '1', 'รับ 1', 'รับ 1', 'รับ 1', '123', 5, '', 1, 1),
(56, 'พยาบาล 1', 'พยาบาล 1', 'พยาบาล 1', 'พยาบาล 1', 'พยาบาล 1', '12312312', 4, '', 1, 1),
(57, 'พยาบาล 2', 'พยาบาล 2', 'พยาบาล 2', 'พยาบาล 2', 'พยาบาล 2', '123123', 4, '', 1, 1),
(58, 'พยาบาล 3', 'พยาบาล 3', 'พยาบาล 3', 'พยาบาล 3', 'พยาบาล 3', '123123', 4, '', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_specific_permissions`
--

CREATE TABLE `user_specific_permissions` (
  `id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `granted` tinyint(1) DEFAULT 1,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `note` text DEFAULT NULL,
  `granted_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voucher_usage_history`
--

CREATE TABLE `voucher_usage_history` (
  `id` int(11) NOT NULL,
  `voucher_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount_used` decimal(10,2) NOT NULL,
  `remaining_amount` decimal(10,2) NOT NULL,
  `used_at` datetime DEFAULT current_timestamp(),
  `branch_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `voucher_usage_history`
--

INSERT INTO `voucher_usage_history` (`id`, `voucher_id`, `order_id`, `customer_id`, `amount_used`, `remaining_amount`, `used_at`, `branch_id`, `notes`) VALUES
(1, 8, 20, 6, '10000.00', '5000.00', '2024-10-28 22:46:35', 1, NULL),
(2, 8, 21, 6, '5000.00', '0.00', '2024-10-30 08:46:11', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wp_commentmeta`
--

CREATE TABLE `wp_commentmeta` (
  `meta_id` bigint(20) UNSIGNED NOT NULL,
  `comment_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wp_comments`
--

CREATE TABLE `wp_comments` (
  `comment_ID` bigint(20) UNSIGNED NOT NULL,
  `comment_post_ID` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `comment_author` tinytext NOT NULL,
  `comment_author_email` varchar(100) NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text NOT NULL,
  `comment_karma` int(11) NOT NULL DEFAULT 0,
  `comment_approved` varchar(20) NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) NOT NULL DEFAULT '',
  `comment_type` varchar(20) NOT NULL DEFAULT 'comment',
  `comment_parent` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `wp_comments`
--

INSERT INTO `wp_comments` (`comment_ID`, `comment_post_ID`, `comment_author`, `comment_author_email`, `comment_author_url`, `comment_author_IP`, `comment_date`, `comment_date_gmt`, `comment_content`, `comment_karma`, `comment_approved`, `comment_agent`, `comment_type`, `comment_parent`, `user_id`) VALUES
(1, 1, 'ผู้แสดงความเห็นเวิร์ดเพรส', 'wapuu@wordpress.example', 'https://wordpress.org/', '', '2025-01-26 19:58:44', '2025-01-26 12:58:44', 'Hi, this is a comment.\nTo get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.\nCommenter avatars come from <a href=\"https://gravatar.com/\">Gravatar</a>.', 0, '1', '', 'comment', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `wp_links`
--

CREATE TABLE `wp_links` (
  `link_id` bigint(20) UNSIGNED NOT NULL,
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_name` varchar(255) NOT NULL DEFAULT '',
  `link_image` varchar(255) NOT NULL DEFAULT '',
  `link_target` varchar(25) NOT NULL DEFAULT '',
  `link_description` varchar(255) NOT NULL DEFAULT '',
  `link_visible` varchar(20) NOT NULL DEFAULT 'Y',
  `link_owner` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `link_rating` int(11) NOT NULL DEFAULT 0,
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) NOT NULL DEFAULT '',
  `link_notes` mediumtext NOT NULL,
  `link_rss` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wp_options`
--

CREATE TABLE `wp_options` (
  `option_id` bigint(20) UNSIGNED NOT NULL,
  `option_name` varchar(191) NOT NULL DEFAULT '',
  `option_value` longtext NOT NULL,
  `autoload` varchar(20) NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `wp_options`
--

INSERT INTO `wp_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(1, 'cron', 'a:11:{i:1740427124;a:1:{s:34:\"wp_privacy_delete_old_export_files\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}}i:1740445141;a:1:{s:21:\"wp_update_user_counts\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1740448724;a:1:{s:16:\"wp_version_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1740450524;a:1:{s:17:\"wp_update_plugins\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1740452324;a:1:{s:16:\"wp_update_themes\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1740488324;a:1:{s:32:\"recovery_mode_clean_expired_keys\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1740488341;a:2:{s:19:\"wp_scheduled_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:25:\"delete_expired_transients\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1740488345;a:1:{s:30:\"wp_scheduled_auto_draft_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1740922896;a:1:{s:30:\"wp_delete_temp_updater_backups\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}}i:1741006724;a:1:{s:30:\"wp_site_health_scheduled_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"weekly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:604800;}}}s:7:\"version\";i:2;}', 'on'),
(2, 'siteurl', 'https://demo.dcareclinic.com', 'on'),
(3, 'home', 'https://demo.dcareclinic.com', 'on'),
(4, 'blogname', 'dcareclinic.com', 'on'),
(5, 'blogdescription', '', 'on'),
(6, 'users_can_register', '0', 'on'),
(7, 'admin_email', 'max.sk0211@gmail.com', 'on'),
(8, 'start_of_week', '1', 'on'),
(9, 'use_balanceTags', '0', 'on'),
(10, 'use_smilies', '1', 'on'),
(11, 'require_name_email', '1', 'on'),
(12, 'comments_notify', '1', 'on'),
(13, 'posts_per_rss', '10', 'on'),
(14, 'rss_use_excerpt', '0', 'on'),
(15, 'mailserver_url', 'mail.example.com', 'on'),
(16, 'mailserver_login', 'login@example.com', 'on'),
(17, 'mailserver_pass', '', 'on'),
(18, 'mailserver_port', '110', 'on'),
(19, 'default_category', '1', 'on'),
(20, 'default_comment_status', 'open', 'on'),
(21, 'default_ping_status', 'open', 'on'),
(22, 'default_pingback_flag', '1', 'on'),
(23, 'posts_per_page', '10', 'on'),
(24, 'date_format', 'j F Y', 'on'),
(25, 'time_format', 'G:i น.', 'on'),
(26, 'links_updated_date_format', 'j F Y G:i น.', 'on'),
(27, 'comment_moderation', '0', 'on'),
(28, 'moderation_notify', '1', 'on'),
(29, 'permalink_structure', '/%year%/%monthnum%/%day%/%postname%/', 'on'),
(30, 'rewrite_rules', 'a:97:{s:11:\"^wp-json/?$\";s:22:\"index.php?rest_route=/\";s:14:\"^wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:21:\"^index.php/wp-json/?$\";s:22:\"index.php?rest_route=/\";s:24:\"^index.php/wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:17:\"^wp-sitemap\\.xml$\";s:23:\"index.php?sitemap=index\";s:17:\"^wp-sitemap\\.xsl$\";s:36:\"index.php?sitemap-stylesheet=sitemap\";s:23:\"^wp-sitemap-index\\.xsl$\";s:34:\"index.php?sitemap-stylesheet=index\";s:48:\"^wp-sitemap-([a-z]+?)-([a-z\\d_-]+?)-(\\d+?)\\.xml$\";s:75:\"index.php?sitemap=$matches[1]&sitemap-subtype=$matches[2]&paged=$matches[3]\";s:34:\"^wp-sitemap-([a-z]+?)-(\\d+?)\\.xml$\";s:47:\"index.php?sitemap=$matches[1]&paged=$matches[2]\";s:47:\"category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:42:\"category/(.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:23:\"category/(.+?)/embed/?$\";s:46:\"index.php?category_name=$matches[1]&embed=true\";s:35:\"category/(.+?)/page/?([0-9]{1,})/?$\";s:53:\"index.php?category_name=$matches[1]&paged=$matches[2]\";s:17:\"category/(.+?)/?$\";s:35:\"index.php?category_name=$matches[1]\";s:44:\"tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:39:\"tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:20:\"tag/([^/]+)/embed/?$\";s:36:\"index.php?tag=$matches[1]&embed=true\";s:32:\"tag/([^/]+)/page/?([0-9]{1,})/?$\";s:43:\"index.php?tag=$matches[1]&paged=$matches[2]\";s:14:\"tag/([^/]+)/?$\";s:25:\"index.php?tag=$matches[1]\";s:45:\"type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:40:\"type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:21:\"type/([^/]+)/embed/?$\";s:44:\"index.php?post_format=$matches[1]&embed=true\";s:33:\"type/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?post_format=$matches[1]&paged=$matches[2]\";s:15:\"type/([^/]+)/?$\";s:33:\"index.php?post_format=$matches[1]\";s:12:\"robots\\.txt$\";s:18:\"index.php?robots=1\";s:13:\"favicon\\.ico$\";s:19:\"index.php?favicon=1\";s:12:\"sitemap\\.xml\";s:24:\"index.php??sitemap=index\";s:48:\".*wp-(atom|rdf|rss|rss2|feed|commentsrss2)\\.php$\";s:18:\"index.php?feed=old\";s:20:\".*wp-app\\.php(/.*)?$\";s:19:\"index.php?error=403\";s:18:\".*wp-register.php$\";s:23:\"index.php?register=true\";s:32:\"feed/(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:27:\"(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:8:\"embed/?$\";s:21:\"index.php?&embed=true\";s:20:\"page/?([0-9]{1,})/?$\";s:28:\"index.php?&paged=$matches[1]\";s:41:\"comments/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:36:\"comments/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:17:\"comments/embed/?$\";s:21:\"index.php?&embed=true\";s:44:\"search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:39:\"search/(.+)/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:20:\"search/(.+)/embed/?$\";s:34:\"index.php?s=$matches[1]&embed=true\";s:32:\"search/(.+)/page/?([0-9]{1,})/?$\";s:41:\"index.php?s=$matches[1]&paged=$matches[2]\";s:14:\"search/(.+)/?$\";s:23:\"index.php?s=$matches[1]\";s:47:\"author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:42:\"author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:23:\"author/([^/]+)/embed/?$\";s:44:\"index.php?author_name=$matches[1]&embed=true\";s:35:\"author/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?author_name=$matches[1]&paged=$matches[2]\";s:17:\"author/([^/]+)/?$\";s:33:\"index.php?author_name=$matches[1]\";s:69:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:64:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:45:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$\";s:74:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&embed=true\";s:57:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]\";s:39:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$\";s:63:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]\";s:56:\"([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:51:\"([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:32:\"([0-9]{4})/([0-9]{1,2})/embed/?$\";s:58:\"index.php?year=$matches[1]&monthnum=$matches[2]&embed=true\";s:44:\"([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]\";s:26:\"([0-9]{4})/([0-9]{1,2})/?$\";s:47:\"index.php?year=$matches[1]&monthnum=$matches[2]\";s:43:\"([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:38:\"([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:19:\"([0-9]{4})/embed/?$\";s:37:\"index.php?year=$matches[1]&embed=true\";s:31:\"([0-9]{4})/page/?([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&paged=$matches[2]\";s:13:\"([0-9]{4})/?$\";s:26:\"index.php?year=$matches[1]\";s:58:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:68:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:88:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:83:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:83:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:64:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:53:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/embed/?$\";s:91:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&embed=true\";s:57:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/trackback/?$\";s:85:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&tb=1\";s:77:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:97:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]\";s:72:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:97:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&feed=$matches[5]\";s:65:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/page/?([0-9]{1,})/?$\";s:98:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&paged=$matches[5]\";s:72:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)/comment-page-([0-9]{1,})/?$\";s:98:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&cpage=$matches[5]\";s:61:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/([^/]+)(?:/([0-9]+))?/?$\";s:97:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&name=$matches[4]&page=$matches[5]\";s:47:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:57:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:77:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:72:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:72:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:53:\"[0-9]{4}/[0-9]{1,2}/[0-9]{1,2}/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:64:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&cpage=$matches[4]\";s:51:\"([0-9]{4})/([0-9]{1,2})/comment-page-([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&cpage=$matches[3]\";s:38:\"([0-9]{4})/comment-page-([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&cpage=$matches[2]\";s:27:\".?.+?/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\".?.+?/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\".?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\".?.+?/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:16:\"(.?.+?)/embed/?$\";s:41:\"index.php?pagename=$matches[1]&embed=true\";s:20:\"(.?.+?)/trackback/?$\";s:35:\"index.php?pagename=$matches[1]&tb=1\";s:40:\"(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:35:\"(.?.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:28:\"(.?.+?)/page/?([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&paged=$matches[2]\";s:35:\"(.?.+?)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&cpage=$matches[2]\";s:24:\"(.?.+?)(?:/([0-9]+))?/?$\";s:47:\"index.php?pagename=$matches[1]&page=$matches[2]\";}', 'on'),
(31, 'hack_file', '0', 'on'),
(32, 'blog_charset', 'UTF-8', 'on'),
(33, 'moderation_keys', '', 'off'),
(34, 'active_plugins', 'a:0:{}', 'on'),
(35, 'category_base', '', 'on'),
(36, 'ping_sites', 'http://rpc.pingomatic.com/', 'on'),
(37, 'comment_max_links', '2', 'on'),
(38, 'gmt_offset', '7', 'on'),
(39, 'default_email_category', '1', 'on'),
(40, 'recently_edited', 'a:2:{i:0;s:91:\"/home/chanchal/domains/demo.dcareclinic.com/public_html/wp-content/themes/Impreza/style.css\";i:2;s:0:\"\";}', 'off'),
(41, 'template', 'Impreza', 'on'),
(42, 'stylesheet', 'Impreza', 'on'),
(43, 'comment_registration', '0', 'on'),
(44, 'html_type', 'text/html', 'on'),
(45, 'use_trackback', '0', 'on'),
(46, 'default_role', 'subscriber', 'on'),
(47, 'db_version', '58975', 'on'),
(48, 'uploads_use_yearmonth_folders', '1', 'on'),
(49, 'upload_path', '', 'on'),
(50, 'blog_public', '1', 'on'),
(51, 'default_link_category', '2', 'on'),
(52, 'show_on_front', 'posts', 'on'),
(53, 'tag_base', '', 'on'),
(54, 'show_avatars', '1', 'on'),
(55, 'avatar_rating', 'G', 'on'),
(56, 'upload_url_path', '', 'on'),
(57, 'thumbnail_size_w', '150', 'on'),
(58, 'thumbnail_size_h', '150', 'on'),
(59, 'thumbnail_crop', '1', 'on'),
(60, 'medium_size_w', '300', 'on'),
(61, 'medium_size_h', '300', 'on'),
(62, 'avatar_default', 'mystery', 'on'),
(63, 'large_size_w', '1024', 'on'),
(64, 'large_size_h', '1024', 'on'),
(65, 'image_default_link_type', 'none', 'on'),
(66, 'image_default_size', '', 'on'),
(67, 'image_default_align', '', 'on'),
(68, 'close_comments_for_old_posts', '0', 'on'),
(69, 'close_comments_days_old', '14', 'on'),
(70, 'thread_comments', '1', 'on'),
(71, 'thread_comments_depth', '5', 'on'),
(72, 'page_comments', '0', 'on'),
(73, 'comments_per_page', '50', 'on'),
(74, 'default_comments_page', 'newest', 'on'),
(75, 'comment_order', 'asc', 'on'),
(76, 'sticky_posts', 'a:0:{}', 'on'),
(77, 'widget_categories', 'a:0:{}', 'on'),
(78, 'widget_text', 'a:0:{}', 'on'),
(79, 'widget_rss', 'a:0:{}', 'on'),
(80, 'uninstall_plugins', 'a:0:{}', 'off'),
(81, 'timezone_string', '', 'on'),
(82, 'page_for_posts', '0', 'on'),
(83, 'page_on_front', '0', 'on'),
(84, 'default_post_format', '0', 'on'),
(85, 'link_manager_enabled', '0', 'on'),
(86, 'finished_splitting_shared_terms', '1', 'on'),
(87, 'site_icon', '0', 'on'),
(88, 'medium_large_size_w', '768', 'on'),
(89, 'medium_large_size_h', '0', 'on'),
(90, 'wp_page_for_privacy_policy', '3', 'on'),
(91, 'show_comments_cookies_opt_in', '1', 'on'),
(92, 'admin_email_lifespan', '1753448324', 'on'),
(93, 'disallowed_keys', '', 'off'),
(94, 'comment_previously_approved', '1', 'on'),
(95, 'auto_plugin_theme_update_emails', 'a:0:{}', 'off'),
(96, 'auto_update_core_dev', 'enabled', 'on'),
(97, 'auto_update_core_minor', 'enabled', 'on'),
(98, 'auto_update_core_major', 'enabled', 'on'),
(99, 'wp_force_deactivated_plugins', 'a:0:{}', 'on'),
(100, 'wp_attachment_pages_enabled', '0', 'on'),
(101, 'initial_db_version', '58975', 'on'),
(102, 'wp_user_roles', 'a:5:{s:13:\"administrator\";a:2:{s:4:\"name\";s:13:\"Administrator\";s:12:\"capabilities\";a:61:{s:13:\"switch_themes\";b:1;s:11:\"edit_themes\";b:1;s:16:\"activate_plugins\";b:1;s:12:\"edit_plugins\";b:1;s:10:\"edit_users\";b:1;s:10:\"edit_files\";b:1;s:14:\"manage_options\";b:1;s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:6:\"import\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:8:\"level_10\";b:1;s:7:\"level_9\";b:1;s:7:\"level_8\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:12:\"delete_users\";b:1;s:12:\"create_users\";b:1;s:17:\"unfiltered_upload\";b:1;s:14:\"edit_dashboard\";b:1;s:14:\"update_plugins\";b:1;s:14:\"delete_plugins\";b:1;s:15:\"install_plugins\";b:1;s:13:\"update_themes\";b:1;s:14:\"install_themes\";b:1;s:11:\"update_core\";b:1;s:10:\"list_users\";b:1;s:12:\"remove_users\";b:1;s:13:\"promote_users\";b:1;s:18:\"edit_theme_options\";b:1;s:13:\"delete_themes\";b:1;s:6:\"export\";b:1;}}s:6:\"editor\";a:2:{s:4:\"name\";s:6:\"Editor\";s:12:\"capabilities\";a:34:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;}}s:6:\"author\";a:2:{s:4:\"name\";s:6:\"Author\";s:12:\"capabilities\";a:10:{s:12:\"upload_files\";b:1;s:10:\"edit_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;s:22:\"delete_published_posts\";b:1;}}s:11:\"contributor\";a:2:{s:4:\"name\";s:11:\"Contributor\";s:12:\"capabilities\";a:5:{s:10:\"edit_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;}}s:10:\"subscriber\";a:2:{s:4:\"name\";s:10:\"Subscriber\";s:12:\"capabilities\";a:2:{s:4:\"read\";b:1;s:7:\"level_0\";b:1;}}}', 'on'),
(103, 'fresh_site', '1', 'off'),
(104, 'WPLANG', 'th', 'auto'),
(105, 'user_count', '1', 'off'),
(106, 'widget_block', 'a:6:{i:2;a:1:{s:7:\"content\";s:19:\"<!-- wp:search /-->\";}i:3;a:1:{s:7:\"content\";s:178:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>เรื่องล่าสุด</h2><!-- /wp:heading --><!-- wp:latest-posts /--></div><!-- /wp:group -->\";}i:4;a:1:{s:7:\"content\";s:254:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>ความเห็นล่าสุด</h2><!-- /wp:heading --><!-- wp:latest-comments {\"displayAvatar\":false,\"displayDate\":false,\"displayExcerpt\":false} /--></div><!-- /wp:group -->\";}i:5;a:1:{s:7:\"content\";s:162:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>คลังเก็บ</h2><!-- /wp:heading --><!-- wp:archives /--></div><!-- /wp:group -->\";}i:6;a:1:{s:7:\"content\";s:164:\"<!-- wp:group --><div class=\"wp-block-group\"><!-- wp:heading --><h2>หมวดหมู่</h2><!-- /wp:heading --><!-- wp:categories /--></div><!-- /wp:group -->\";}s:12:\"_multiwidget\";i:1;}', 'auto'),
(107, 'sidebars_widgets', 'a:2:{s:19:\"wp_inactive_widgets\";a:5:{i:0;s:7:\"block-2\";i:1;s:7:\"block-3\";i:2;s:7:\"block-4\";i:3;s:7:\"block-5\";i:4;s:7:\"block-6\";}s:13:\"array_version\";i:3;}', 'auto'),
(108, 'widget_pages', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'auto'),
(109, 'widget_calendar', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'auto'),
(110, 'widget_archives', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'auto'),
(111, 'widget_media_audio', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'auto'),
(112, 'widget_media_image', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'auto'),
(113, 'widget_media_gallery', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'auto'),
(114, 'widget_media_video', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'auto'),
(115, 'widget_meta', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'auto'),
(116, 'widget_search', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'auto'),
(117, 'widget_recent-posts', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'auto'),
(118, 'widget_recent-comments', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'auto'),
(119, 'widget_tag_cloud', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'auto'),
(120, 'widget_nav_menu', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'auto'),
(121, 'widget_custom_html', 'a:1:{s:12:\"_multiwidget\";i:1;}', 'auto'),
(122, '_transient_wp_core_block_css_files', 'a:2:{s:7:\"version\";s:5:\"6.7.1\";s:5:\"files\";a:540:{i:0;s:23:\"archives/editor-rtl.css\";i:1;s:27:\"archives/editor-rtl.min.css\";i:2;s:19:\"archives/editor.css\";i:3;s:23:\"archives/editor.min.css\";i:4;s:22:\"archives/style-rtl.css\";i:5;s:26:\"archives/style-rtl.min.css\";i:6;s:18:\"archives/style.css\";i:7;s:22:\"archives/style.min.css\";i:8;s:20:\"audio/editor-rtl.css\";i:9;s:24:\"audio/editor-rtl.min.css\";i:10;s:16:\"audio/editor.css\";i:11;s:20:\"audio/editor.min.css\";i:12;s:19:\"audio/style-rtl.css\";i:13;s:23:\"audio/style-rtl.min.css\";i:14;s:15:\"audio/style.css\";i:15;s:19:\"audio/style.min.css\";i:16;s:19:\"audio/theme-rtl.css\";i:17;s:23:\"audio/theme-rtl.min.css\";i:18;s:15:\"audio/theme.css\";i:19;s:19:\"audio/theme.min.css\";i:20;s:21:\"avatar/editor-rtl.css\";i:21;s:25:\"avatar/editor-rtl.min.css\";i:22;s:17:\"avatar/editor.css\";i:23;s:21:\"avatar/editor.min.css\";i:24;s:20:\"avatar/style-rtl.css\";i:25;s:24:\"avatar/style-rtl.min.css\";i:26;s:16:\"avatar/style.css\";i:27;s:20:\"avatar/style.min.css\";i:28;s:21:\"button/editor-rtl.css\";i:29;s:25:\"button/editor-rtl.min.css\";i:30;s:17:\"button/editor.css\";i:31;s:21:\"button/editor.min.css\";i:32;s:20:\"button/style-rtl.css\";i:33;s:24:\"button/style-rtl.min.css\";i:34;s:16:\"button/style.css\";i:35;s:20:\"button/style.min.css\";i:36;s:22:\"buttons/editor-rtl.css\";i:37;s:26:\"buttons/editor-rtl.min.css\";i:38;s:18:\"buttons/editor.css\";i:39;s:22:\"buttons/editor.min.css\";i:40;s:21:\"buttons/style-rtl.css\";i:41;s:25:\"buttons/style-rtl.min.css\";i:42;s:17:\"buttons/style.css\";i:43;s:21:\"buttons/style.min.css\";i:44;s:22:\"calendar/style-rtl.css\";i:45;s:26:\"calendar/style-rtl.min.css\";i:46;s:18:\"calendar/style.css\";i:47;s:22:\"calendar/style.min.css\";i:48;s:25:\"categories/editor-rtl.css\";i:49;s:29:\"categories/editor-rtl.min.css\";i:50;s:21:\"categories/editor.css\";i:51;s:25:\"categories/editor.min.css\";i:52;s:24:\"categories/style-rtl.css\";i:53;s:28:\"categories/style-rtl.min.css\";i:54;s:20:\"categories/style.css\";i:55;s:24:\"categories/style.min.css\";i:56;s:19:\"code/editor-rtl.css\";i:57;s:23:\"code/editor-rtl.min.css\";i:58;s:15:\"code/editor.css\";i:59;s:19:\"code/editor.min.css\";i:60;s:18:\"code/style-rtl.css\";i:61;s:22:\"code/style-rtl.min.css\";i:62;s:14:\"code/style.css\";i:63;s:18:\"code/style.min.css\";i:64;s:18:\"code/theme-rtl.css\";i:65;s:22:\"code/theme-rtl.min.css\";i:66;s:14:\"code/theme.css\";i:67;s:18:\"code/theme.min.css\";i:68;s:22:\"columns/editor-rtl.css\";i:69;s:26:\"columns/editor-rtl.min.css\";i:70;s:18:\"columns/editor.css\";i:71;s:22:\"columns/editor.min.css\";i:72;s:21:\"columns/style-rtl.css\";i:73;s:25:\"columns/style-rtl.min.css\";i:74;s:17:\"columns/style.css\";i:75;s:21:\"columns/style.min.css\";i:76;s:33:\"comment-author-name/style-rtl.css\";i:77;s:37:\"comment-author-name/style-rtl.min.css\";i:78;s:29:\"comment-author-name/style.css\";i:79;s:33:\"comment-author-name/style.min.css\";i:80;s:29:\"comment-content/style-rtl.css\";i:81;s:33:\"comment-content/style-rtl.min.css\";i:82;s:25:\"comment-content/style.css\";i:83;s:29:\"comment-content/style.min.css\";i:84;s:26:\"comment-date/style-rtl.css\";i:85;s:30:\"comment-date/style-rtl.min.css\";i:86;s:22:\"comment-date/style.css\";i:87;s:26:\"comment-date/style.min.css\";i:88;s:31:\"comment-edit-link/style-rtl.css\";i:89;s:35:\"comment-edit-link/style-rtl.min.css\";i:90;s:27:\"comment-edit-link/style.css\";i:91;s:31:\"comment-edit-link/style.min.css\";i:92;s:32:\"comment-reply-link/style-rtl.css\";i:93;s:36:\"comment-reply-link/style-rtl.min.css\";i:94;s:28:\"comment-reply-link/style.css\";i:95;s:32:\"comment-reply-link/style.min.css\";i:96;s:30:\"comment-template/style-rtl.css\";i:97;s:34:\"comment-template/style-rtl.min.css\";i:98;s:26:\"comment-template/style.css\";i:99;s:30:\"comment-template/style.min.css\";i:100;s:42:\"comments-pagination-numbers/editor-rtl.css\";i:101;s:46:\"comments-pagination-numbers/editor-rtl.min.css\";i:102;s:38:\"comments-pagination-numbers/editor.css\";i:103;s:42:\"comments-pagination-numbers/editor.min.css\";i:104;s:34:\"comments-pagination/editor-rtl.css\";i:105;s:38:\"comments-pagination/editor-rtl.min.css\";i:106;s:30:\"comments-pagination/editor.css\";i:107;s:34:\"comments-pagination/editor.min.css\";i:108;s:33:\"comments-pagination/style-rtl.css\";i:109;s:37:\"comments-pagination/style-rtl.min.css\";i:110;s:29:\"comments-pagination/style.css\";i:111;s:33:\"comments-pagination/style.min.css\";i:112;s:29:\"comments-title/editor-rtl.css\";i:113;s:33:\"comments-title/editor-rtl.min.css\";i:114;s:25:\"comments-title/editor.css\";i:115;s:29:\"comments-title/editor.min.css\";i:116;s:23:\"comments/editor-rtl.css\";i:117;s:27:\"comments/editor-rtl.min.css\";i:118;s:19:\"comments/editor.css\";i:119;s:23:\"comments/editor.min.css\";i:120;s:22:\"comments/style-rtl.css\";i:121;s:26:\"comments/style-rtl.min.css\";i:122;s:18:\"comments/style.css\";i:123;s:22:\"comments/style.min.css\";i:124;s:20:\"cover/editor-rtl.css\";i:125;s:24:\"cover/editor-rtl.min.css\";i:126;s:16:\"cover/editor.css\";i:127;s:20:\"cover/editor.min.css\";i:128;s:19:\"cover/style-rtl.css\";i:129;s:23:\"cover/style-rtl.min.css\";i:130;s:15:\"cover/style.css\";i:131;s:19:\"cover/style.min.css\";i:132;s:22:\"details/editor-rtl.css\";i:133;s:26:\"details/editor-rtl.min.css\";i:134;s:18:\"details/editor.css\";i:135;s:22:\"details/editor.min.css\";i:136;s:21:\"details/style-rtl.css\";i:137;s:25:\"details/style-rtl.min.css\";i:138;s:17:\"details/style.css\";i:139;s:21:\"details/style.min.css\";i:140;s:20:\"embed/editor-rtl.css\";i:141;s:24:\"embed/editor-rtl.min.css\";i:142;s:16:\"embed/editor.css\";i:143;s:20:\"embed/editor.min.css\";i:144;s:19:\"embed/style-rtl.css\";i:145;s:23:\"embed/style-rtl.min.css\";i:146;s:15:\"embed/style.css\";i:147;s:19:\"embed/style.min.css\";i:148;s:19:\"embed/theme-rtl.css\";i:149;s:23:\"embed/theme-rtl.min.css\";i:150;s:15:\"embed/theme.css\";i:151;s:19:\"embed/theme.min.css\";i:152;s:19:\"file/editor-rtl.css\";i:153;s:23:\"file/editor-rtl.min.css\";i:154;s:15:\"file/editor.css\";i:155;s:19:\"file/editor.min.css\";i:156;s:18:\"file/style-rtl.css\";i:157;s:22:\"file/style-rtl.min.css\";i:158;s:14:\"file/style.css\";i:159;s:18:\"file/style.min.css\";i:160;s:23:\"footnotes/style-rtl.css\";i:161;s:27:\"footnotes/style-rtl.min.css\";i:162;s:19:\"footnotes/style.css\";i:163;s:23:\"footnotes/style.min.css\";i:164;s:23:\"freeform/editor-rtl.css\";i:165;s:27:\"freeform/editor-rtl.min.css\";i:166;s:19:\"freeform/editor.css\";i:167;s:23:\"freeform/editor.min.css\";i:168;s:22:\"gallery/editor-rtl.css\";i:169;s:26:\"gallery/editor-rtl.min.css\";i:170;s:18:\"gallery/editor.css\";i:171;s:22:\"gallery/editor.min.css\";i:172;s:21:\"gallery/style-rtl.css\";i:173;s:25:\"gallery/style-rtl.min.css\";i:174;s:17:\"gallery/style.css\";i:175;s:21:\"gallery/style.min.css\";i:176;s:21:\"gallery/theme-rtl.css\";i:177;s:25:\"gallery/theme-rtl.min.css\";i:178;s:17:\"gallery/theme.css\";i:179;s:21:\"gallery/theme.min.css\";i:180;s:20:\"group/editor-rtl.css\";i:181;s:24:\"group/editor-rtl.min.css\";i:182;s:16:\"group/editor.css\";i:183;s:20:\"group/editor.min.css\";i:184;s:19:\"group/style-rtl.css\";i:185;s:23:\"group/style-rtl.min.css\";i:186;s:15:\"group/style.css\";i:187;s:19:\"group/style.min.css\";i:188;s:19:\"group/theme-rtl.css\";i:189;s:23:\"group/theme-rtl.min.css\";i:190;s:15:\"group/theme.css\";i:191;s:19:\"group/theme.min.css\";i:192;s:21:\"heading/style-rtl.css\";i:193;s:25:\"heading/style-rtl.min.css\";i:194;s:17:\"heading/style.css\";i:195;s:21:\"heading/style.min.css\";i:196;s:19:\"html/editor-rtl.css\";i:197;s:23:\"html/editor-rtl.min.css\";i:198;s:15:\"html/editor.css\";i:199;s:19:\"html/editor.min.css\";i:200;s:20:\"image/editor-rtl.css\";i:201;s:24:\"image/editor-rtl.min.css\";i:202;s:16:\"image/editor.css\";i:203;s:20:\"image/editor.min.css\";i:204;s:19:\"image/style-rtl.css\";i:205;s:23:\"image/style-rtl.min.css\";i:206;s:15:\"image/style.css\";i:207;s:19:\"image/style.min.css\";i:208;s:19:\"image/theme-rtl.css\";i:209;s:23:\"image/theme-rtl.min.css\";i:210;s:15:\"image/theme.css\";i:211;s:19:\"image/theme.min.css\";i:212;s:29:\"latest-comments/style-rtl.css\";i:213;s:33:\"latest-comments/style-rtl.min.css\";i:214;s:25:\"latest-comments/style.css\";i:215;s:29:\"latest-comments/style.min.css\";i:216;s:27:\"latest-posts/editor-rtl.css\";i:217;s:31:\"latest-posts/editor-rtl.min.css\";i:218;s:23:\"latest-posts/editor.css\";i:219;s:27:\"latest-posts/editor.min.css\";i:220;s:26:\"latest-posts/style-rtl.css\";i:221;s:30:\"latest-posts/style-rtl.min.css\";i:222;s:22:\"latest-posts/style.css\";i:223;s:26:\"latest-posts/style.min.css\";i:224;s:18:\"list/style-rtl.css\";i:225;s:22:\"list/style-rtl.min.css\";i:226;s:14:\"list/style.css\";i:227;s:18:\"list/style.min.css\";i:228;s:22:\"loginout/style-rtl.css\";i:229;s:26:\"loginout/style-rtl.min.css\";i:230;s:18:\"loginout/style.css\";i:231;s:22:\"loginout/style.min.css\";i:232;s:25:\"media-text/editor-rtl.css\";i:233;s:29:\"media-text/editor-rtl.min.css\";i:234;s:21:\"media-text/editor.css\";i:235;s:25:\"media-text/editor.min.css\";i:236;s:24:\"media-text/style-rtl.css\";i:237;s:28:\"media-text/style-rtl.min.css\";i:238;s:20:\"media-text/style.css\";i:239;s:24:\"media-text/style.min.css\";i:240;s:19:\"more/editor-rtl.css\";i:241;s:23:\"more/editor-rtl.min.css\";i:242;s:15:\"more/editor.css\";i:243;s:19:\"more/editor.min.css\";i:244;s:30:\"navigation-link/editor-rtl.css\";i:245;s:34:\"navigation-link/editor-rtl.min.css\";i:246;s:26:\"navigation-link/editor.css\";i:247;s:30:\"navigation-link/editor.min.css\";i:248;s:29:\"navigation-link/style-rtl.css\";i:249;s:33:\"navigation-link/style-rtl.min.css\";i:250;s:25:\"navigation-link/style.css\";i:251;s:29:\"navigation-link/style.min.css\";i:252;s:33:\"navigation-submenu/editor-rtl.css\";i:253;s:37:\"navigation-submenu/editor-rtl.min.css\";i:254;s:29:\"navigation-submenu/editor.css\";i:255;s:33:\"navigation-submenu/editor.min.css\";i:256;s:25:\"navigation/editor-rtl.css\";i:257;s:29:\"navigation/editor-rtl.min.css\";i:258;s:21:\"navigation/editor.css\";i:259;s:25:\"navigation/editor.min.css\";i:260;s:24:\"navigation/style-rtl.css\";i:261;s:28:\"navigation/style-rtl.min.css\";i:262;s:20:\"navigation/style.css\";i:263;s:24:\"navigation/style.min.css\";i:264;s:23:\"nextpage/editor-rtl.css\";i:265;s:27:\"nextpage/editor-rtl.min.css\";i:266;s:19:\"nextpage/editor.css\";i:267;s:23:\"nextpage/editor.min.css\";i:268;s:24:\"page-list/editor-rtl.css\";i:269;s:28:\"page-list/editor-rtl.min.css\";i:270;s:20:\"page-list/editor.css\";i:271;s:24:\"page-list/editor.min.css\";i:272;s:23:\"page-list/style-rtl.css\";i:273;s:27:\"page-list/style-rtl.min.css\";i:274;s:19:\"page-list/style.css\";i:275;s:23:\"page-list/style.min.css\";i:276;s:24:\"paragraph/editor-rtl.css\";i:277;s:28:\"paragraph/editor-rtl.min.css\";i:278;s:20:\"paragraph/editor.css\";i:279;s:24:\"paragraph/editor.min.css\";i:280;s:23:\"paragraph/style-rtl.css\";i:281;s:27:\"paragraph/style-rtl.min.css\";i:282;s:19:\"paragraph/style.css\";i:283;s:23:\"paragraph/style.min.css\";i:284;s:35:\"post-author-biography/style-rtl.css\";i:285;s:39:\"post-author-biography/style-rtl.min.css\";i:286;s:31:\"post-author-biography/style.css\";i:287;s:35:\"post-author-biography/style.min.css\";i:288;s:30:\"post-author-name/style-rtl.css\";i:289;s:34:\"post-author-name/style-rtl.min.css\";i:290;s:26:\"post-author-name/style.css\";i:291;s:30:\"post-author-name/style.min.css\";i:292;s:26:\"post-author/editor-rtl.css\";i:293;s:30:\"post-author/editor-rtl.min.css\";i:294;s:22:\"post-author/editor.css\";i:295;s:26:\"post-author/editor.min.css\";i:296;s:25:\"post-author/style-rtl.css\";i:297;s:29:\"post-author/style-rtl.min.css\";i:298;s:21:\"post-author/style.css\";i:299;s:25:\"post-author/style.min.css\";i:300;s:33:\"post-comments-form/editor-rtl.css\";i:301;s:37:\"post-comments-form/editor-rtl.min.css\";i:302;s:29:\"post-comments-form/editor.css\";i:303;s:33:\"post-comments-form/editor.min.css\";i:304;s:32:\"post-comments-form/style-rtl.css\";i:305;s:36:\"post-comments-form/style-rtl.min.css\";i:306;s:28:\"post-comments-form/style.css\";i:307;s:32:\"post-comments-form/style.min.css\";i:308;s:27:\"post-content/editor-rtl.css\";i:309;s:31:\"post-content/editor-rtl.min.css\";i:310;s:23:\"post-content/editor.css\";i:311;s:27:\"post-content/editor.min.css\";i:312;s:26:\"post-content/style-rtl.css\";i:313;s:30:\"post-content/style-rtl.min.css\";i:314;s:22:\"post-content/style.css\";i:315;s:26:\"post-content/style.min.css\";i:316;s:23:\"post-date/style-rtl.css\";i:317;s:27:\"post-date/style-rtl.min.css\";i:318;s:19:\"post-date/style.css\";i:319;s:23:\"post-date/style.min.css\";i:320;s:27:\"post-excerpt/editor-rtl.css\";i:321;s:31:\"post-excerpt/editor-rtl.min.css\";i:322;s:23:\"post-excerpt/editor.css\";i:323;s:27:\"post-excerpt/editor.min.css\";i:324;s:26:\"post-excerpt/style-rtl.css\";i:325;s:30:\"post-excerpt/style-rtl.min.css\";i:326;s:22:\"post-excerpt/style.css\";i:327;s:26:\"post-excerpt/style.min.css\";i:328;s:34:\"post-featured-image/editor-rtl.css\";i:329;s:38:\"post-featured-image/editor-rtl.min.css\";i:330;s:30:\"post-featured-image/editor.css\";i:331;s:34:\"post-featured-image/editor.min.css\";i:332;s:33:\"post-featured-image/style-rtl.css\";i:333;s:37:\"post-featured-image/style-rtl.min.css\";i:334;s:29:\"post-featured-image/style.css\";i:335;s:33:\"post-featured-image/style.min.css\";i:336;s:34:\"post-navigation-link/style-rtl.css\";i:337;s:38:\"post-navigation-link/style-rtl.min.css\";i:338;s:30:\"post-navigation-link/style.css\";i:339;s:34:\"post-navigation-link/style.min.css\";i:340;s:28:\"post-template/editor-rtl.css\";i:341;s:32:\"post-template/editor-rtl.min.css\";i:342;s:24:\"post-template/editor.css\";i:343;s:28:\"post-template/editor.min.css\";i:344;s:27:\"post-template/style-rtl.css\";i:345;s:31:\"post-template/style-rtl.min.css\";i:346;s:23:\"post-template/style.css\";i:347;s:27:\"post-template/style.min.css\";i:348;s:24:\"post-terms/style-rtl.css\";i:349;s:28:\"post-terms/style-rtl.min.css\";i:350;s:20:\"post-terms/style.css\";i:351;s:24:\"post-terms/style.min.css\";i:352;s:24:\"post-title/style-rtl.css\";i:353;s:28:\"post-title/style-rtl.min.css\";i:354;s:20:\"post-title/style.css\";i:355;s:24:\"post-title/style.min.css\";i:356;s:26:\"preformatted/style-rtl.css\";i:357;s:30:\"preformatted/style-rtl.min.css\";i:358;s:22:\"preformatted/style.css\";i:359;s:26:\"preformatted/style.min.css\";i:360;s:24:\"pullquote/editor-rtl.css\";i:361;s:28:\"pullquote/editor-rtl.min.css\";i:362;s:20:\"pullquote/editor.css\";i:363;s:24:\"pullquote/editor.min.css\";i:364;s:23:\"pullquote/style-rtl.css\";i:365;s:27:\"pullquote/style-rtl.min.css\";i:366;s:19:\"pullquote/style.css\";i:367;s:23:\"pullquote/style.min.css\";i:368;s:23:\"pullquote/theme-rtl.css\";i:369;s:27:\"pullquote/theme-rtl.min.css\";i:370;s:19:\"pullquote/theme.css\";i:371;s:23:\"pullquote/theme.min.css\";i:372;s:39:\"query-pagination-numbers/editor-rtl.css\";i:373;s:43:\"query-pagination-numbers/editor-rtl.min.css\";i:374;s:35:\"query-pagination-numbers/editor.css\";i:375;s:39:\"query-pagination-numbers/editor.min.css\";i:376;s:31:\"query-pagination/editor-rtl.css\";i:377;s:35:\"query-pagination/editor-rtl.min.css\";i:378;s:27:\"query-pagination/editor.css\";i:379;s:31:\"query-pagination/editor.min.css\";i:380;s:30:\"query-pagination/style-rtl.css\";i:381;s:34:\"query-pagination/style-rtl.min.css\";i:382;s:26:\"query-pagination/style.css\";i:383;s:30:\"query-pagination/style.min.css\";i:384;s:25:\"query-title/style-rtl.css\";i:385;s:29:\"query-title/style-rtl.min.css\";i:386;s:21:\"query-title/style.css\";i:387;s:25:\"query-title/style.min.css\";i:388;s:20:\"query/editor-rtl.css\";i:389;s:24:\"query/editor-rtl.min.css\";i:390;s:16:\"query/editor.css\";i:391;s:20:\"query/editor.min.css\";i:392;s:19:\"quote/style-rtl.css\";i:393;s:23:\"quote/style-rtl.min.css\";i:394;s:15:\"quote/style.css\";i:395;s:19:\"quote/style.min.css\";i:396;s:19:\"quote/theme-rtl.css\";i:397;s:23:\"quote/theme-rtl.min.css\";i:398;s:15:\"quote/theme.css\";i:399;s:19:\"quote/theme.min.css\";i:400;s:23:\"read-more/style-rtl.css\";i:401;s:27:\"read-more/style-rtl.min.css\";i:402;s:19:\"read-more/style.css\";i:403;s:23:\"read-more/style.min.css\";i:404;s:18:\"rss/editor-rtl.css\";i:405;s:22:\"rss/editor-rtl.min.css\";i:406;s:14:\"rss/editor.css\";i:407;s:18:\"rss/editor.min.css\";i:408;s:17:\"rss/style-rtl.css\";i:409;s:21:\"rss/style-rtl.min.css\";i:410;s:13:\"rss/style.css\";i:411;s:17:\"rss/style.min.css\";i:412;s:21:\"search/editor-rtl.css\";i:413;s:25:\"search/editor-rtl.min.css\";i:414;s:17:\"search/editor.css\";i:415;s:21:\"search/editor.min.css\";i:416;s:20:\"search/style-rtl.css\";i:417;s:24:\"search/style-rtl.min.css\";i:418;s:16:\"search/style.css\";i:419;s:20:\"search/style.min.css\";i:420;s:20:\"search/theme-rtl.css\";i:421;s:24:\"search/theme-rtl.min.css\";i:422;s:16:\"search/theme.css\";i:423;s:20:\"search/theme.min.css\";i:424;s:24:\"separator/editor-rtl.css\";i:425;s:28:\"separator/editor-rtl.min.css\";i:426;s:20:\"separator/editor.css\";i:427;s:24:\"separator/editor.min.css\";i:428;s:23:\"separator/style-rtl.css\";i:429;s:27:\"separator/style-rtl.min.css\";i:430;s:19:\"separator/style.css\";i:431;s:23:\"separator/style.min.css\";i:432;s:23:\"separator/theme-rtl.css\";i:433;s:27:\"separator/theme-rtl.min.css\";i:434;s:19:\"separator/theme.css\";i:435;s:23:\"separator/theme.min.css\";i:436;s:24:\"shortcode/editor-rtl.css\";i:437;s:28:\"shortcode/editor-rtl.min.css\";i:438;s:20:\"shortcode/editor.css\";i:439;s:24:\"shortcode/editor.min.css\";i:440;s:24:\"site-logo/editor-rtl.css\";i:441;s:28:\"site-logo/editor-rtl.min.css\";i:442;s:20:\"site-logo/editor.css\";i:443;s:24:\"site-logo/editor.min.css\";i:444;s:23:\"site-logo/style-rtl.css\";i:445;s:27:\"site-logo/style-rtl.min.css\";i:446;s:19:\"site-logo/style.css\";i:447;s:23:\"site-logo/style.min.css\";i:448;s:27:\"site-tagline/editor-rtl.css\";i:449;s:31:\"site-tagline/editor-rtl.min.css\";i:450;s:23:\"site-tagline/editor.css\";i:451;s:27:\"site-tagline/editor.min.css\";i:452;s:26:\"site-tagline/style-rtl.css\";i:453;s:30:\"site-tagline/style-rtl.min.css\";i:454;s:22:\"site-tagline/style.css\";i:455;s:26:\"site-tagline/style.min.css\";i:456;s:25:\"site-title/editor-rtl.css\";i:457;s:29:\"site-title/editor-rtl.min.css\";i:458;s:21:\"site-title/editor.css\";i:459;s:25:\"site-title/editor.min.css\";i:460;s:24:\"site-title/style-rtl.css\";i:461;s:28:\"site-title/style-rtl.min.css\";i:462;s:20:\"site-title/style.css\";i:463;s:24:\"site-title/style.min.css\";i:464;s:26:\"social-link/editor-rtl.css\";i:465;s:30:\"social-link/editor-rtl.min.css\";i:466;s:22:\"social-link/editor.css\";i:467;s:26:\"social-link/editor.min.css\";i:468;s:27:\"social-links/editor-rtl.css\";i:469;s:31:\"social-links/editor-rtl.min.css\";i:470;s:23:\"social-links/editor.css\";i:471;s:27:\"social-links/editor.min.css\";i:472;s:26:\"social-links/style-rtl.css\";i:473;s:30:\"social-links/style-rtl.min.css\";i:474;s:22:\"social-links/style.css\";i:475;s:26:\"social-links/style.min.css\";i:476;s:21:\"spacer/editor-rtl.css\";i:477;s:25:\"spacer/editor-rtl.min.css\";i:478;s:17:\"spacer/editor.css\";i:479;s:21:\"spacer/editor.min.css\";i:480;s:20:\"spacer/style-rtl.css\";i:481;s:24:\"spacer/style-rtl.min.css\";i:482;s:16:\"spacer/style.css\";i:483;s:20:\"spacer/style.min.css\";i:484;s:20:\"table/editor-rtl.css\";i:485;s:24:\"table/editor-rtl.min.css\";i:486;s:16:\"table/editor.css\";i:487;s:20:\"table/editor.min.css\";i:488;s:19:\"table/style-rtl.css\";i:489;s:23:\"table/style-rtl.min.css\";i:490;s:15:\"table/style.css\";i:491;s:19:\"table/style.min.css\";i:492;s:19:\"table/theme-rtl.css\";i:493;s:23:\"table/theme-rtl.min.css\";i:494;s:15:\"table/theme.css\";i:495;s:19:\"table/theme.min.css\";i:496;s:24:\"tag-cloud/editor-rtl.css\";i:497;s:28:\"tag-cloud/editor-rtl.min.css\";i:498;s:20:\"tag-cloud/editor.css\";i:499;s:24:\"tag-cloud/editor.min.css\";i:500;s:23:\"tag-cloud/style-rtl.css\";i:501;s:27:\"tag-cloud/style-rtl.min.css\";i:502;s:19:\"tag-cloud/style.css\";i:503;s:23:\"tag-cloud/style.min.css\";i:504;s:28:\"template-part/editor-rtl.css\";i:505;s:32:\"template-part/editor-rtl.min.css\";i:506;s:24:\"template-part/editor.css\";i:507;s:28:\"template-part/editor.min.css\";i:508;s:27:\"template-part/theme-rtl.css\";i:509;s:31:\"template-part/theme-rtl.min.css\";i:510;s:23:\"template-part/theme.css\";i:511;s:27:\"template-part/theme.min.css\";i:512;s:30:\"term-description/style-rtl.css\";i:513;s:34:\"term-description/style-rtl.min.css\";i:514;s:26:\"term-description/style.css\";i:515;s:30:\"term-description/style.min.css\";i:516;s:27:\"text-columns/editor-rtl.css\";i:517;s:31:\"text-columns/editor-rtl.min.css\";i:518;s:23:\"text-columns/editor.css\";i:519;s:27:\"text-columns/editor.min.css\";i:520;s:26:\"text-columns/style-rtl.css\";i:521;s:30:\"text-columns/style-rtl.min.css\";i:522;s:22:\"text-columns/style.css\";i:523;s:26:\"text-columns/style.min.css\";i:524;s:19:\"verse/style-rtl.css\";i:525;s:23:\"verse/style-rtl.min.css\";i:526;s:15:\"verse/style.css\";i:527;s:19:\"verse/style.min.css\";i:528;s:20:\"video/editor-rtl.css\";i:529;s:24:\"video/editor-rtl.min.css\";i:530;s:16:\"video/editor.css\";i:531;s:20:\"video/editor.min.css\";i:532;s:19:\"video/style-rtl.css\";i:533;s:23:\"video/style-rtl.min.css\";i:534;s:15:\"video/style.css\";i:535;s:19:\"video/style.min.css\";i:536;s:19:\"video/theme-rtl.css\";i:537;s:23:\"video/theme-rtl.min.css\";i:538;s:15:\"video/theme.css\";i:539;s:19:\"video/theme.min.css\";}}', 'on'),
(126, 'theme_mods_twentytwentyfive', 'a:2:{s:18:\"custom_css_post_id\";i:-1;s:16:\"sidebars_widgets\";a:2:{s:4:\"time\";i:1737905927;s:4:\"data\";a:3:{s:19:\"wp_inactive_widgets\";a:0:{}s:9:\"sidebar-1\";a:3:{i:0;s:7:\"block-2\";i:1;s:7:\"block-3\";i:2;s:7:\"block-4\";}s:9:\"sidebar-2\";a:2:{i:0;s:7:\"block-5\";i:1;s:7:\"block-6\";}}}}', 'off');
INSERT INTO `wp_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(127, '_transient_wp_styles_for_blocks', 'a:2:{s:4:\"hash\";s:32:\"c92664442cf20f88c8be1ed98c105d42\";s:6:\"blocks\";a:52:{s:11:\"core/button\";s:0:\"\";s:14:\"core/site-logo\";s:0:\"\";s:18:\"core/post-template\";s:0:\"\";s:12:\"core/columns\";s:769:\":root :where(.wp-block-columns-is-layout-flow) > :first-child{margin-block-start: 0;}:root :where(.wp-block-columns-is-layout-flow) > :last-child{margin-block-end: 0;}:root :where(.wp-block-columns-is-layout-flow) > *{margin-block-start: var(--wp--preset--spacing--50);margin-block-end: 0;}:root :where(.wp-block-columns-is-layout-constrained) > :first-child{margin-block-start: 0;}:root :where(.wp-block-columns-is-layout-constrained) > :last-child{margin-block-end: 0;}:root :where(.wp-block-columns-is-layout-constrained) > *{margin-block-start: var(--wp--preset--spacing--50);margin-block-end: 0;}:root :where(.wp-block-columns-is-layout-flex){gap: var(--wp--preset--spacing--50);}:root :where(.wp-block-columns-is-layout-grid){gap: var(--wp--preset--spacing--50);}\";s:14:\"core/pullquote\";s:306:\":root :where(.wp-block-pullquote){font-size: var(--wp--preset--font-size--xx-large);font-weight: 300;line-height: 1.2;padding-top: var(--wp--preset--spacing--30);padding-bottom: var(--wp--preset--spacing--30);}:root :where(.wp-block-pullquote p:last-of-type){margin-bottom: var(--wp--preset--spacing--30);}\";s:32:\"c48738dcb285a3f6ab83acff204fc486\";s:106:\":root :where(.wp-block-pullquote cite){font-size: var(--wp--preset--font-size--small);font-style: normal;}\";s:11:\"core/avatar\";s:57:\":root :where(.wp-block-avatar img){border-radius: 100px;}\";s:12:\"core/buttons\";s:665:\":root :where(.wp-block-buttons-is-layout-flow) > :first-child{margin-block-start: 0;}:root :where(.wp-block-buttons-is-layout-flow) > :last-child{margin-block-end: 0;}:root :where(.wp-block-buttons-is-layout-flow) > *{margin-block-start: 16px;margin-block-end: 0;}:root :where(.wp-block-buttons-is-layout-constrained) > :first-child{margin-block-start: 0;}:root :where(.wp-block-buttons-is-layout-constrained) > :last-child{margin-block-end: 0;}:root :where(.wp-block-buttons-is-layout-constrained) > *{margin-block-start: 16px;margin-block-end: 0;}:root :where(.wp-block-buttons-is-layout-flex){gap: 16px;}:root :where(.wp-block-buttons-is-layout-grid){gap: 16px;}\";s:9:\"core/code\";s:427:\":root :where(.wp-block-code){background-color: var(--wp--preset--color--accent-5);color: var(--wp--preset--color--contrast);font-family: var(--wp--preset--font-family--fira-code);font-size: var(--wp--preset--font-size--medium);font-weight: 300;padding-top: var(--wp--preset--spacing--40);padding-right: var(--wp--preset--spacing--40);padding-bottom: var(--wp--preset--spacing--40);padding-left: var(--wp--preset--spacing--40);}\";s:24:\"core/comment-author-name\";s:169:\":root :where(.wp-block-comment-author-name){color: var(--wp--preset--color--accent-4);font-size: var(--wp--preset--font-size--small);margin-top: 5px;margin-bottom: 0px;}\";s:32:\"c0002c260f8238c4212f3e4c369fc4f7\";s:143:\":root :where(.wp-block-comment-author-name a:where(:not(.wp-element-button))){color: var(--wp--preset--color--accent-4);text-decoration: none;}\";s:32:\"1e7c38b45537b325dbbbaec17a301676\";s:112:\":root :where(.wp-block-comment-author-name a:where(:not(.wp-element-button)):hover){text-decoration: underline;}\";s:20:\"core/comment-content\";s:178:\":root :where(.wp-block-comment-content){font-size: var(--wp--preset--font-size--medium);margin-top: var(--wp--preset--spacing--30);margin-bottom: var(--wp--preset--spacing--30);}\";s:17:\"core/comment-date\";s:127:\":root :where(.wp-block-comment-date){color: var(--wp--preset--color--contrast);font-size: var(--wp--preset--font-size--small);}\";s:32:\"c83ca7b3e52884c70f7830c54f99b318\";s:114:\":root :where(.wp-block-comment-date a:where(:not(.wp-element-button))){color: var(--wp--preset--color--contrast);}\";s:22:\"core/comment-edit-link\";s:90:\":root :where(.wp-block-comment-edit-link){font-size: var(--wp--preset--font-size--small);}\";s:32:\"41d70710612536a90e368c12bcb0efea\";s:119:\":root :where(.wp-block-comment-edit-link a:where(:not(.wp-element-button))){color: var(--wp--preset--color--contrast);}\";s:23:\"core/comment-reply-link\";s:91:\":root :where(.wp-block-comment-reply-link){font-size: var(--wp--preset--font-size--small);}\";s:32:\"13c96340dbf37700add1f4c5cae19f3e\";s:120:\":root :where(.wp-block-comment-reply-link a:where(:not(.wp-element-button))){color: var(--wp--preset--color--contrast);}\";s:23:\"core/post-comments-form\";s:565:\":root :where(.wp-block-post-comments-form){font-size: var(--wp--preset--font-size--medium);padding-top: var(--wp--preset--spacing--40);padding-bottom: var(--wp--preset--spacing--40);}:root :where(.wp-block-post-comments-form textarea, .wp-block-post-comments-form input:not([type=submit])){border-radius:.25rem; border-color: var(--wp--preset--color--accent-6) !important;}:root :where(.wp-block-post-comments-form input[type=checkbox]){margin:0 .2rem 0 0 !important;}:root :where(.wp-block-post-comments-form label){font-size: var(--wp--preset--font-size--small);}\";s:24:\"core/comments-pagination\";s:182:\":root :where(.wp-block-comments-pagination){font-size: var(--wp--preset--font-size--medium);margin-top: var(--wp--preset--spacing--40);margin-bottom: var(--wp--preset--spacing--40);}\";s:29:\"core/comments-pagination-next\";s:98:\":root :where(.wp-block-comments-pagination-next){font-size: var(--wp--preset--font-size--medium);}\";s:32:\"core/comments-pagination-numbers\";s:101:\":root :where(.wp-block-comments-pagination-numbers){font-size: var(--wp--preset--font-size--medium);}\";s:33:\"core/comments-pagination-previous\";s:102:\":root :where(.wp-block-comments-pagination-previous){font-size: var(--wp--preset--font-size--medium);}\";s:14:\"core/post-date\";s:124:\":root :where(.wp-block-post-date){color: var(--wp--preset--color--accent-4);font-size: var(--wp--preset--font-size--small);}\";s:32:\"ac0d4e00f5ec22d14451759983e5bd43\";s:133:\":root :where(.wp-block-post-date a:where(:not(.wp-element-button))){color: var(--wp--preset--color--accent-4);text-decoration: none;}\";s:32:\"0ae6ffd1b886044c2da62d75d05ab13d\";s:102:\":root :where(.wp-block-post-date a:where(:not(.wp-element-button)):hover){text-decoration: underline;}\";s:25:\"core/post-navigation-link\";s:94:\":root :where(.wp-block-post-navigation-link){font-size: var(--wp--preset--font-size--medium);}\";s:15:\"core/post-terms\";s:158:\":root :where(.wp-block-post-terms){font-size: var(--wp--preset--font-size--small);font-weight: 600;}:root :where(.wp-block-post-terms a){white-space: nowrap;}\";s:15:\"core/post-title\";s:0:\"\";s:32:\"bb496d3fcd9be3502ce57ff8281e5687\";s:92:\":root :where(.wp-block-post-title a:where(:not(.wp-element-button))){text-decoration: none;}\";s:32:\"12380ab98fdc81351bb32a39bbfc9249\";s:103:\":root :where(.wp-block-post-title a:where(:not(.wp-element-button)):hover){text-decoration: underline;}\";s:10:\"core/quote\";s:1315:\":root :where(.wp-block-quote){border-color: currentColor;border-width: 0 0 0 2px;border-style: solid;font-size: var(--wp--preset--font-size--large);font-weight: 300;margin-right: 0;margin-left: 0;padding-top: var(--wp--preset--spacing--30);padding-right: var(--wp--preset--spacing--40);padding-bottom: var(--wp--preset--spacing--30);padding-left: var(--wp--preset--spacing--40);}:root :where(.wp-block-quote-is-layout-flow) > :first-child{margin-block-start: 0;}:root :where(.wp-block-quote-is-layout-flow) > :last-child{margin-block-end: 0;}:root :where(.wp-block-quote-is-layout-flow) > *{margin-block-start: var(--wp--preset--spacing--30);margin-block-end: 0;}:root :where(.wp-block-quote-is-layout-constrained) > :first-child{margin-block-start: 0;}:root :where(.wp-block-quote-is-layout-constrained) > :last-child{margin-block-end: 0;}:root :where(.wp-block-quote-is-layout-constrained) > *{margin-block-start: var(--wp--preset--spacing--30);margin-block-end: 0;}:root :where(.wp-block-quote-is-layout-flex){gap: var(--wp--preset--spacing--30);}:root :where(.wp-block-quote-is-layout-grid){gap: var(--wp--preset--spacing--30);}:root :where(.wp-block-quote.has-text-align-right ){border-width: 0 2px 0 0;}:root :where(.wp-block-quote.has-text-align-center ){border-width: 0;border-inline: 0; padding-inline: 0;}\";s:32:\"1de7a22e22013106efc5be82788cb6c0\";s:176:\":root :where(.wp-block-quote cite){font-size: var(--wp--preset--font-size--small);font-style: normal;font-weight: 300;}:root :where(.wp-block-quote cite sub){font-size: 0.65em}\";s:21:\"core/query-pagination\";s:107:\":root :where(.wp-block-query-pagination){font-size: var(--wp--preset--font-size--medium);font-weight: 500;}\";s:11:\"core/search\";s:380:\":root :where(.wp-block-search .wp-block-search__label, .wp-block-search .wp-block-search__input, .wp-block-search .wp-block-search__button){font-size: var(--wp--preset--font-size--medium);line-height: 1.6;}:root :where(.wp-block-search .wp-block-search__input){border-radius:3.125rem;padding-left:1.5625rem;padding-right:1.5625rem;border-color:var(--wp--preset--color--accent-6);}\";s:32:\"14fa6a3d0cfbde171cbc0fb04aa8a6cf\";s:138:\":root :where(.wp-block-search .wp-element-button,.wp-block-search  .wp-block-button__link){border-radius: 3.125rem;margin-left: 1.125rem;}\";s:32:\"05993ee2f3de94b5d1350998a7e9b6b0\";s:130:\":root :where(.wp-block-search .wp-element-button:hover,.wp-block-search  .wp-block-button__link:hover){border-color: transparent;}\";s:14:\"core/separator\";s:148:\":root :where(.wp-block-separator){border-color: currentColor;border-width: 0 0 1px 0;border-style: solid;color: var(--wp--preset--color--accent-6);}\";s:17:\"core/site-tagline\";s:86:\":root :where(.wp-block-site-tagline){font-size: var(--wp--preset--font-size--medium);}\";s:15:\"core/site-title\";s:75:\":root :where(.wp-block-site-title){font-weight: 700;letter-spacing: -.5px;}\";s:32:\"f513d889cf971b13995cc3fffed2f39b\";s:92:\":root :where(.wp-block-site-title a:where(:not(.wp-element-button))){text-decoration: none;}\";s:32:\"22c37a317cc0ebd50155b5ad78564f37\";s:103:\":root :where(.wp-block-site-title a:where(:not(.wp-element-button)):hover){text-decoration: underline;}\";s:21:\"core/term-description\";s:90:\":root :where(.wp-block-term-description){font-size: var(--wp--preset--font-size--medium);}\";s:15:\"core/navigation\";s:84:\":root :where(.wp-block-navigation){font-size: var(--wp--preset--font-size--medium);}\";s:32:\"25289a01850f5a0264ddb79a9a3baf3d\";s:92:\":root :where(.wp-block-navigation a:where(:not(.wp-element-button))){text-decoration: none;}\";s:32:\"026c04da08398d655a95047f1f235d97\";s:103:\":root :where(.wp-block-navigation a:where(:not(.wp-element-button)):hover){text-decoration: underline;}\";s:9:\"core/list\";s:52:\":root :where(.wp-block-list li){margin-top: 0.5rem;}\";s:12:\"core/heading\";s:0:\"\";s:14:\"core/paragraph\";s:0:\"\";s:10:\"core/group\";s:0:\"\";s:11:\"core/column\";s:0:\"\";}}', 'on'),
(128, 'recovery_keys', 'a:0:{}', 'off'),
(141, 'can_compress_scripts', '0', 'on'),
(156, 'finished_updating_comment_type', '1', 'auto'),
(183, 'current_theme', 'Impreza', 'auto'),
(184, 'theme_mods_Impreza', 'a:3:{i:0;b:0;s:18:\"nav_menu_locations\";a:0:{}s:18:\"custom_css_post_id\";i:-1;}', 'on'),
(185, 'theme_switched', '', 'auto'),
(194, '_transient_health-check-site-status-result', '{\"good\":19,\"recommended\":4,\"critical\":0}', 'on'),
(312, '_site_transient_update_core', 'O:8:\"stdClass\":4:{s:7:\"updates\";a:1:{i:0;O:8:\"stdClass\":10:{s:8:\"response\";s:6:\"latest\";s:8:\"download\";s:59:\"https://downloads.wordpress.org/release/wordpress-6.7.2.zip\";s:6:\"locale\";s:2:\"th\";s:8:\"packages\";O:8:\"stdClass\":5:{s:4:\"full\";s:59:\"https://downloads.wordpress.org/release/wordpress-6.7.2.zip\";s:10:\"no_content\";s:70:\"https://downloads.wordpress.org/release/wordpress-6.7.2-no-content.zip\";s:11:\"new_bundled\";s:71:\"https://downloads.wordpress.org/release/wordpress-6.7.2-new-bundled.zip\";s:7:\"partial\";s:0:\"\";s:8:\"rollback\";s:0:\"\";}s:7:\"current\";s:5:\"6.7.2\";s:7:\"version\";s:5:\"6.7.2\";s:11:\"php_version\";s:6:\"7.2.24\";s:13:\"mysql_version\";s:5:\"5.5.5\";s:11:\"new_bundled\";s:3:\"6.7\";s:15:\"partial_version\";s:0:\"\";}}s:12:\"last_checked\";i:1740425989;s:15:\"version_checked\";s:5:\"6.7.2\";s:12:\"translations\";a:0:{}}', 'off'),
(317, 'auto_core_update_notified', 'a:4:{s:4:\"type\";s:7:\"success\";s:5:\"email\";s:20:\"max.sk0211@gmail.com\";s:7:\"version\";s:5:\"6.7.2\";s:9:\"timestamp\";i:1739430840;}', 'off'),
(464, '_site_transient_timeout_theme_roots', '1740427783', 'off'),
(465, '_site_transient_theme_roots', 'a:4:{s:7:\"Impreza\";s:7:\"/themes\";s:16:\"twentytwentyfive\";s:7:\"/themes\";s:16:\"twentytwentyfour\";s:7:\"/themes\";s:17:\"twentytwentythree\";s:7:\"/themes\";}', 'off'),
(466, '_site_transient_timeout_php_check_38979a08dcd71638878b7b4419751271', '1741030783', 'off'),
(467, '_site_transient_php_check_38979a08dcd71638878b7b4419751271', 'a:5:{s:19:\"recommended_version\";s:3:\"7.4\";s:15:\"minimum_version\";s:6:\"7.2.24\";s:12:\"is_supported\";b:1;s:9:\"is_secure\";b:1;s:13:\"is_acceptable\";b:1;}', 'off'),
(469, '_site_transient_update_themes', 'O:8:\"stdClass\":5:{s:12:\"last_checked\";i:1740425991;s:7:\"checked\";a:4:{s:7:\"Impreza\";s:6:\"8.33.1\";s:16:\"twentytwentyfive\";s:3:\"1.0\";s:16:\"twentytwentyfour\";s:3:\"1.3\";s:17:\"twentytwentythree\";s:3:\"1.6\";}s:8:\"response\";a:1:{s:16:\"twentytwentyfive\";a:6:{s:5:\"theme\";s:16:\"twentytwentyfive\";s:11:\"new_version\";s:3:\"1.1\";s:3:\"url\";s:46:\"https://wordpress.org/themes/twentytwentyfive/\";s:7:\"package\";s:62:\"https://downloads.wordpress.org/theme/twentytwentyfive.1.1.zip\";s:8:\"requires\";s:3:\"6.7\";s:12:\"requires_php\";s:3:\"7.2\";}}s:9:\"no_update\";a:2:{s:16:\"twentytwentyfour\";a:6:{s:5:\"theme\";s:16:\"twentytwentyfour\";s:11:\"new_version\";s:3:\"1.3\";s:3:\"url\";s:46:\"https://wordpress.org/themes/twentytwentyfour/\";s:7:\"package\";s:62:\"https://downloads.wordpress.org/theme/twentytwentyfour.1.3.zip\";s:8:\"requires\";s:3:\"6.4\";s:12:\"requires_php\";s:3:\"7.0\";}s:17:\"twentytwentythree\";a:6:{s:5:\"theme\";s:17:\"twentytwentythree\";s:11:\"new_version\";s:3:\"1.6\";s:3:\"url\";s:47:\"https://wordpress.org/themes/twentytwentythree/\";s:7:\"package\";s:63:\"https://downloads.wordpress.org/theme/twentytwentythree.1.6.zip\";s:8:\"requires\";s:3:\"6.1\";s:12:\"requires_php\";s:3:\"5.6\";}}s:12:\"translations\";a:0:{}}', 'off'),
(470, '_site_transient_update_plugins', 'O:8:\"stdClass\":5:{s:12:\"last_checked\";i:1740425992;s:8:\"response\";a:1:{s:19:\"akismet/akismet.php\";O:8:\"stdClass\":13:{s:2:\"id\";s:21:\"w.org/plugins/akismet\";s:4:\"slug\";s:7:\"akismet\";s:6:\"plugin\";s:19:\"akismet/akismet.php\";s:11:\"new_version\";s:5:\"5.3.7\";s:3:\"url\";s:38:\"https://wordpress.org/plugins/akismet/\";s:7:\"package\";s:56:\"https://downloads.wordpress.org/plugin/akismet.5.3.7.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:60:\"https://ps.w.org/akismet/assets/icon-256x256.png?rev=2818463\";s:2:\"1x\";s:60:\"https://ps.w.org/akismet/assets/icon-128x128.png?rev=2818463\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:63:\"https://ps.w.org/akismet/assets/banner-1544x500.png?rev=2900731\";s:2:\"1x\";s:62:\"https://ps.w.org/akismet/assets/banner-772x250.png?rev=2900731\";}s:11:\"banners_rtl\";a:0:{}s:8:\"requires\";s:3:\"5.8\";s:6:\"tested\";s:5:\"6.7.2\";s:12:\"requires_php\";s:6:\"5.6.20\";s:16:\"requires_plugins\";a:0:{}}}s:12:\"translations\";a:0:{}s:9:\"no_update\";a:1:{s:9:\"hello.php\";O:8:\"stdClass\":10:{s:2:\"id\";s:25:\"w.org/plugins/hello-dolly\";s:4:\"slug\";s:11:\"hello-dolly\";s:6:\"plugin\";s:9:\"hello.php\";s:11:\"new_version\";s:5:\"1.7.2\";s:3:\"url\";s:42:\"https://wordpress.org/plugins/hello-dolly/\";s:7:\"package\";s:60:\"https://downloads.wordpress.org/plugin/hello-dolly.1.7.3.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:64:\"https://ps.w.org/hello-dolly/assets/icon-256x256.jpg?rev=2052855\";s:2:\"1x\";s:64:\"https://ps.w.org/hello-dolly/assets/icon-128x128.jpg?rev=2052855\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:67:\"https://ps.w.org/hello-dolly/assets/banner-1544x500.jpg?rev=2645582\";s:2:\"1x\";s:66:\"https://ps.w.org/hello-dolly/assets/banner-772x250.jpg?rev=2052855\";}s:11:\"banners_rtl\";a:0:{}s:8:\"requires\";s:3:\"4.6\";}}s:7:\"checked\";a:2:{s:19:\"akismet/akismet.php\";s:5:\"5.3.5\";s:9:\"hello.php\";s:5:\"1.7.2\";}}', 'off'),
(471, '_site_transient_timeout_wp_theme_files_patterns-3113e870028ab1b799bc96402df5433d', '1740427801', 'off'),
(472, '_site_transient_wp_theme_files_patterns-3113e870028ab1b799bc96402df5433d', 'a:2:{s:7:\"version\";s:6:\"8.33.1\";s:8:\"patterns\";a:0:{}}', 'off');

-- --------------------------------------------------------

--
-- Table structure for table `wp_postmeta`
--

CREATE TABLE `wp_postmeta` (
  `meta_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `wp_postmeta`
--

INSERT INTO `wp_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES
(1, 2, '_wp_page_template', 'default'),
(2, 3, '_wp_page_template', 'default'),
(5, 7, '_menu_item_type', 'custom'),
(6, 7, '_menu_item_menu_item_parent', '0'),
(7, 7, '_menu_item_object_id', '7'),
(8, 7, '_menu_item_object', 'custom'),
(9, 7, '_menu_item_target', ''),
(10, 7, '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'),
(11, 7, '_menu_item_xfn', ''),
(12, 7, '_menu_item_url', 'https://demo.dcareclinic.com/'),
(13, 7, '_menu_item_orphaned', '1738387455'),
(14, 8, '_menu_item_type', 'post_type'),
(15, 8, '_menu_item_menu_item_parent', '0'),
(16, 8, '_menu_item_object_id', '2'),
(17, 8, '_menu_item_object', 'page'),
(18, 8, '_menu_item_target', ''),
(19, 8, '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'),
(20, 8, '_menu_item_xfn', ''),
(21, 8, '_menu_item_url', ''),
(22, 8, '_menu_item_orphaned', '1738387455'),
(23, 9, '_menu_item_type', 'custom'),
(24, 9, '_menu_item_menu_item_parent', '0'),
(25, 9, '_menu_item_object_id', '9'),
(26, 9, '_menu_item_object', 'custom'),
(27, 9, '_menu_item_target', ''),
(28, 9, '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'),
(29, 9, '_menu_item_xfn', ''),
(30, 9, '_menu_item_url', 'https://demo.dcareclinic.com/'),
(31, 9, '_menu_item_orphaned', '1738387474'),
(32, 10, '_menu_item_type', 'post_type'),
(33, 10, '_menu_item_menu_item_parent', '0'),
(34, 10, '_menu_item_object_id', '2'),
(35, 10, '_menu_item_object', 'page'),
(36, 10, '_menu_item_target', ''),
(37, 10, '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'),
(38, 10, '_menu_item_xfn', ''),
(39, 10, '_menu_item_url', ''),
(40, 10, '_menu_item_orphaned', '1738387474'),
(41, 11, '_menu_item_type', 'custom'),
(42, 11, '_menu_item_menu_item_parent', '0'),
(43, 11, '_menu_item_object_id', '11'),
(44, 11, '_menu_item_object', 'custom'),
(45, 11, '_menu_item_target', ''),
(46, 11, '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'),
(47, 11, '_menu_item_xfn', ''),
(48, 11, '_menu_item_url', 'https://demo.dcareclinic.com/'),
(49, 11, '_menu_item_orphaned', '1738387753'),
(50, 12, '_menu_item_type', 'post_type'),
(51, 12, '_menu_item_menu_item_parent', '0'),
(52, 12, '_menu_item_object_id', '2'),
(53, 12, '_menu_item_object', 'page'),
(54, 12, '_menu_item_target', ''),
(55, 12, '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'),
(56, 12, '_menu_item_xfn', ''),
(57, 12, '_menu_item_url', ''),
(58, 12, '_menu_item_orphaned', '1738387753'),
(59, 13, '_menu_item_type', 'custom'),
(60, 13, '_menu_item_menu_item_parent', '0'),
(61, 13, '_menu_item_object_id', '13'),
(62, 13, '_menu_item_object', 'custom'),
(63, 13, '_menu_item_target', ''),
(64, 13, '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'),
(65, 13, '_menu_item_xfn', ''),
(66, 13, '_menu_item_url', 'https://demo.dcareclinic.com/'),
(67, 13, '_menu_item_orphaned', '1738387755'),
(68, 14, '_menu_item_type', 'post_type'),
(69, 14, '_menu_item_menu_item_parent', '0'),
(70, 14, '_menu_item_object_id', '2'),
(71, 14, '_menu_item_object', 'page'),
(72, 14, '_menu_item_target', ''),
(73, 14, '_menu_item_classes', 'a:1:{i:0;s:0:\"\";}'),
(74, 14, '_menu_item_xfn', ''),
(75, 14, '_menu_item_url', ''),
(76, 14, '_menu_item_orphaned', '1738387755');

-- --------------------------------------------------------

--
-- Table structure for table `wp_posts`
--

CREATE TABLE `wp_posts` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `post_author` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext NOT NULL,
  `post_title` text NOT NULL,
  `post_excerpt` text NOT NULL,
  `post_status` varchar(20) NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) NOT NULL DEFAULT 'open',
  `post_password` varchar(255) NOT NULL DEFAULT '',
  `post_name` varchar(200) NOT NULL DEFAULT '',
  `to_ping` text NOT NULL,
  `pinged` text NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext NOT NULL,
  `post_parent` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `guid` varchar(255) NOT NULL DEFAULT '',
  `menu_order` int(11) NOT NULL DEFAULT 0,
  `post_type` varchar(20) NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) NOT NULL DEFAULT '',
  `comment_count` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `wp_posts`
--

INSERT INTO `wp_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES
(1, 1, '2025-01-26 19:58:44', '2025-01-26 12:58:44', '<!-- wp:paragraph -->\n<p>ยินดีต้อนรับสู่ WordPress นี่คือเรื่องแรกของคุณ แก้ไขหรือลบทิ้งไป แล้วมาเริ่มเขียนกัน!</p>\n<!-- /wp:paragraph -->', 'สวัสดีชาวโลก - -\'', '', 'publish', 'open', 'open', '', 'hello-world', '', '', '2025-01-26 19:58:44', '2025-01-26 12:58:44', '', 0, 'https://demo.dcareclinic.com/?p=1', 0, 'post', '', 1),
(2, 1, '2025-01-26 19:58:44', '2025-01-26 12:58:44', '<!-- wp:paragraph -->\n<p>นี่คือหน้าตัวอย่าง มันแตกต่างจากเรื่องของบล็อกเพราะว่ามันจะอยู่ในที่ที่เดียว และจะถูกแสดงออกมาในการใช้งานเว็บของคุณ (ในธีมส่วนใหญ่) หลายคนเริ่มด้วยหน้าเกี่ยวกับ ซึ่งจะแนะนำพวกเขาถึงผู้เยี่ยมชมเว็บที่เป็นไปได้ มันอาจจะพูดถึงบางสิ่งประมาณนี้:</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><p>สวัสดี! ฉันเป็นพนักงานส่งของในตอนกลางวัน ปรารถนาเป็นนักแสดงในตอนกลางคืน และนี่คือเว็บของฉัน ฉันอาศัยอยู่ที่ลอสแองเจลิส มีสุนัขที่ยอดเยี่ยมชื่อแจ๊ค และฉันชื่นชอบพีน่า โคลาด้า (และกำลังติดฝนอยู่)</p></blockquote>\n<!-- /wp:quote -->\n\n<!-- wp:paragraph -->\n<p>...หรือบางสิ่งคล้ายกันนี้:</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><p>บริษัทของเด็กเล่น XYZ ก่อตั้งในปี 1971 และได้จัดจำหน่ายของเด็กเล่นที่มีคุณภาพสู่สาธารณะตั้งแต่นั้นมา ตั้งอยู่ที่เมืองกอร์ทเทม บริษัทจ้างงานกว่า 2,000 คนและได้ทำสิ่งที่ดีเลิศมากมายสำหรับชุมชนเมืองกอร์ทเทม</p></blockquote>\n<!-- /wp:quote -->\n\n<!-- wp:paragraph -->\n<p>ในฐานะผู้ใช้ WordPress ใหม่ คุณควรจะไปที่ <a href=\"https://demo.dcareclinic.com/wp-admin/\">แผงควบคุมของคุณ</a> เพื่อลบหน้านี้ และสร้างหน้าใหม่สำหรับเนื้อหาของคุณ สนุกกัน!</p>\n<!-- /wp:paragraph -->', 'หน้าตัวอย่าง', '', 'publish', 'closed', 'open', '', 'sample-page', '', '', '2025-01-26 19:58:44', '2025-01-26 12:58:44', '', 0, 'https://demo.dcareclinic.com/?page_id=2', 0, 'page', '', 0),
(3, 1, '2025-01-26 19:58:44', '2025-01-26 12:58:44', '<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">เราคือใคร</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">ข้อความแนะนำ: </strong>ที่อยู่เว็บไซต์ของเราคือ: https://demo.dcareclinic.com</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">ความเห็น</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">ข้อความแนะนำ: </strong>เมื่อผู้เยี่ยมชมแสดงความเห็นในเว็บไซต์ เราเก็บข้อมูลที่แสดงในฟอร์มแสดงความเห็น และหมายเลขไอพีและเบราว์เซอร์ของผู้เยี่ยมชมด้วยเพื่อช่วยการตรวจสอบสแปม</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>สตริงนิรนามถูกสร้างขึ้นจากอีเมลของคุณ (หรือที่เรียกว่าแฮช) อาจจะถูกส่งให้บริการ Gravatar เพื่อดูว่าคุณใช้งานหรือไม่ นโยบายความเป็นส่วนตัวของบริการ Gravatar สามารถดูได้ที่นี่: https://automattic.com/privacy/ หลังจากได้รับการยืนยันความเห็นของคุณ รูปภาพข้อมูลส่วนตัวของคุณจะปรากฏสู่สาธารณะในบริบทของความเห็นของคุณ</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">สื่อ</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">ข้อความแนะนำ: </strong>ถ้าคุณอัปโหลดรูปภาพขึ้นมายังเว็บไซต์ คุณควรจะหลีกเลี่ยงการอัปโหลดรูปภาพที่มีข้อมูลตำแหน่งที่ตั้งฝังมาด้วย (EXIF GPS) ผู้เยี่ยมชมเว็บไซต์สามารถดาวน์โหลดและดึงข้อมูลตำแหน่งที่ตั้งใด ๆ จากรูปภาพบนเว็บไซต์ได้</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">คุกกี้</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">ข้อความแนะนำ: </strong>ถ้าคุณแสดงความเห็นบนเว็บไซต์ของเรา คุณอาจจะเลือกเข้าสู่การบันทึกชื่อ อีเมลและเว็บไซต์ของคุณในคุกกี้ นี่จะเป็นการอำนวยความสะดวกสำหรับคุณโดยที่คุณไม่ต้องกรอกรายละเอียดเหล่านี้ซ้ำอีกครั้งในขณะที่คุณแสดงความเห็นอื่น คุกกี้นี้จะอยู่เป็นระยะเวลาหนึ่งปี</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>ถ้าคุณเยี่ยมชมหน้าเข้าสู่ระบบ เราจะตั้งคุกกี้ชั่วคราวเพื่อตรวจสอบว่าเบราว์เซอร์ยอมรับคุกกี้ได้ คุกกี้นี้ไม่มีข้อมูลส่วนตัวรวมอยู่ด้วยและถูกยกเลิกเมื่อคุณปิดเบราว์เซอร์ของคุณ</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>เมื่อคุณเข้าสู่ระบบ เราจะจัดตั้งหลายคุกกี้เพื่อที่จะบันทึกข้อมูลการเข้าสู่ระบบของคุณและตัวเลือกการแสดงผลหน้าจอของคุณ คุกกี้การเข้าสู่ระบบจะคงอยู่ภายในสองวัน และคุกกี้ตัวเลือกหน้าจอจะอยู่เป็นเวลาหนึ่งปี ถ้าคุณเลือก &quot;บันทึกการใช้งานของฉัน&quot; การเข้าสู่ระบบของคุณจะยังคงอยู่เป็นเวลาสองสัปดาห์ ถ้าคุณออกจากระบบบัญชีของคุณ คุกกี้การเข้าสู่ระบบจะถูกลบทิ้ง</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>ถ้าคุณแก้ไขหรือเผยแพร่บทความ คุกกี้ที่เพิ่มเติมจะถูกบันทึกไว้ในเบราว์เซอร์ของคุณ คุกกี้นี้ไม่มีข้อมูลส่วนตัวรวมอยู่และชี้ไปที่ ID ของเรื่องบทความที่คุณเพิ่งจะแก้ไข ซึ่งจะหมดอายุใน 1 วัน</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">แนบเนื้อหาจากเว็บไซต์อื่น</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">ข้อความแนะนำ: </strong>บทความบนเว็บไซต์นี้อาจจะมีเนื้อหาที่ถูกแนบไว้ (เช่น วีดีโอ รูปภาพ บทความ เป็นต้น) เนื้อหาที่ถูกแนบไว้จากเว็บไซต์อื่นปฏิบัติในแนวทางเดียวกันกับที่ผู้เยี่ยมชมได้เข้าชมเว็บไซต์อื่น</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>เว็บไซต์เหล่านี้อาจจะมีการเก็บข้อมูลของคุณ ใช้คุกกี้ การฝังการติดตามบุคคลที่สามเพิ่มเติม และเฝ้าดูการปฏิสัมพันธ์กับเนื้อหาที่แนบไว้ รวมถึงการติดตามการปฏิสัมพันธ์ของคุณกับเนื้อหาที่ถูกแนบไว้ถ้าคุณมีบัญชีและเข้าสู่ระบบในเว็บไซต์นั้นไว้</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">ใครที่เราแชร์ข้อมูลของคุณด้วย</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">ข้อความแนะนำ: </strong>หากคุณร้องขอให้มีการล้างค่ารหัสผ่าน ที่อยู่ไอพีของคุณจะถูกรวมเข้าไปอยู่ในอีเมลล้างค่าด้วย</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">เราเก็บรักษาข้อมูลของคุณไว้นานแค่ไหน</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">ข้อความแนะนำ: </strong>ถ้าคุณแสดงความเห็น ความเห็นและข้อมูลภายในนั้นจะถูกเก็บไว้ตลอด นี่คือสิ่งที่เราสามารถจดจำและพิสูจน์ความเห็นที่ตามมาอย่างอัตโนมัติแทนที่จะต้องชะงักไว้ในคิวการจัดการ</p>\n<!-- /wp:paragraph -->\n<!-- wp:paragraph -->\n<p>สำหรับผู้ใช้ที่ลงทะเบียนบนเว็บไซต์ของเรา (ถ้ามี) เรามีการเก็บข้อมูลส่วนตัวตามที่พวกเขาให้ไว้ในโปรไฟล์เช่นกัน ผู้ใช้ทั้งหมดสามารถดู แก้ไข หรือลบข้อมูลส่วนตัวเมื่อใดก็ได้ (ยกเว้นพวกเขาไม่สามารถเปลี่ยนชื่อผู้ใช้ได้) ผู้ดูแลเว็บไซต์สามารถเห็นและแก้ไขข้อมูลเหล่านั้นได้เช่นกัน</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">คุณมีสิทธิ์อะไรบ้างกับข้อมูลของคุณ</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">ข้อความแนะนำ: </strong>หากคุณมีบัญชีบนเว็บนี้ หรือได้เคยแสดงความเห็นเอาไว้ คุณสามารถร้องขอไฟล์ส่งออกข้อมูลส่วนบุคคลที่เกี่ยวกับตัวคุณที่เราเก็บไว้ให้ส่งไปที่ทางคุณได้ รวมไปถึงข้อมูลใดๆ ที่คุณได้เคยให้ไว้กับเรา คุณสามารถที่จะร้องขอให้เราลบข้อมูลส่วนตัวเกี่ยวกับตัวคุณที่เราเก็บไว้ได้เช่นกัน แต่จะไม่รวมไปถึงข้อมูลใดๆ ที่เราจำเป็นจะต้องเก็บไว้สำหรับการจัดการ ข้อกฎหมาย หรือวัตถุประสงค์ในด้านมาตรการความปลอดภัย</p>\n<!-- /wp:paragraph -->\n<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">Where your data is sent</h2>\n<!-- /wp:heading -->\n<!-- wp:paragraph -->\n<p><strong class=\"privacy-policy-tutorial\">ข้อความแนะนำ: </strong>ความเห็นของผู้เยี่ยมชมอาจถูกตรวจสอบผ่านบริการตรวจสอบสแปมอัตโนมัติ</p>\n<!-- /wp:paragraph -->\n', 'นโยบายความเป็นส่วนตัว', '', 'draft', 'closed', 'open', '', 'นโยบาย-ความเป็นส่วนตัว', '', '', '2025-01-26 19:58:44', '2025-01-26 12:58:44', '', 0, 'https://demo.dcareclinic.com/?page_id=3', 0, 'page', '', 0),
(4, 0, '2025-01-26 19:58:44', '2025-01-26 12:58:44', '<!-- wp:page-list /-->', 'Navigation', '', 'publish', 'closed', 'closed', '', 'navigation', '', '', '2025-01-26 19:58:44', '2025-01-26 12:58:44', '', 0, 'https://demo.dcareclinic.com/2025/01/26/navigation/', 0, 'wp_navigation', '', 0),
(7, 1, '2025-02-01 12:24:15', '0000-00-00 00:00:00', '', 'หน้าแรก', '', 'draft', 'closed', 'closed', '', '', '', '', '2025-02-01 12:24:15', '0000-00-00 00:00:00', '', 0, 'https://demo.dcareclinic.com/?p=7', 1, 'nav_menu_item', '', 0),
(8, 1, '2025-02-01 12:24:15', '0000-00-00 00:00:00', ' ', '', '', 'draft', 'closed', 'closed', '', '', '', '', '2025-02-01 12:24:15', '0000-00-00 00:00:00', '', 0, 'https://demo.dcareclinic.com/?p=8', 1, 'nav_menu_item', '', 0),
(9, 1, '2025-02-01 12:24:34', '0000-00-00 00:00:00', '', 'หน้าแรก', '', 'draft', 'closed', 'closed', '', '', '', '', '2025-02-01 12:24:34', '0000-00-00 00:00:00', '', 0, 'https://demo.dcareclinic.com/?p=9', 1, 'nav_menu_item', '', 0),
(10, 1, '2025-02-01 12:24:34', '0000-00-00 00:00:00', ' ', '', '', 'draft', 'closed', 'closed', '', '', '', '', '2025-02-01 12:24:34', '0000-00-00 00:00:00', '', 0, 'https://demo.dcareclinic.com/?p=10', 1, 'nav_menu_item', '', 0),
(11, 1, '2025-02-01 12:29:13', '0000-00-00 00:00:00', '', 'หน้าแรก', '', 'draft', 'closed', 'closed', '', '', '', '', '2025-02-01 12:29:13', '0000-00-00 00:00:00', '', 0, 'https://demo.dcareclinic.com/?p=11', 1, 'nav_menu_item', '', 0),
(12, 1, '2025-02-01 12:29:13', '0000-00-00 00:00:00', ' ', '', '', 'draft', 'closed', 'closed', '', '', '', '', '2025-02-01 12:29:13', '0000-00-00 00:00:00', '', 0, 'https://demo.dcareclinic.com/?p=12', 1, 'nav_menu_item', '', 0),
(13, 1, '2025-02-01 12:29:15', '0000-00-00 00:00:00', '', 'หน้าแรก', '', 'draft', 'closed', 'closed', '', '', '', '', '2025-02-01 12:29:15', '0000-00-00 00:00:00', '', 0, 'https://demo.dcareclinic.com/?p=13', 1, 'nav_menu_item', '', 0),
(14, 1, '2025-02-01 12:29:15', '0000-00-00 00:00:00', ' ', '', '', 'draft', 'closed', 'closed', '', '', '', '', '2025-02-01 12:29:15', '0000-00-00 00:00:00', '', 0, 'https://demo.dcareclinic.com/?p=14', 1, 'nav_menu_item', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `wp_termmeta`
--

CREATE TABLE `wp_termmeta` (
  `meta_id` bigint(20) UNSIGNED NOT NULL,
  `term_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wp_terms`
--

CREATE TABLE `wp_terms` (
  `term_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL DEFAULT '',
  `slug` varchar(200) NOT NULL DEFAULT '',
  `term_group` bigint(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `wp_terms`
--

INSERT INTO `wp_terms` (`term_id`, `name`, `slug`, `term_group`) VALUES
(1, 'ไม่มีหมวดหมู่', 'uncategorized', 0);

-- --------------------------------------------------------

--
-- Table structure for table `wp_term_relationships`
--

CREATE TABLE `wp_term_relationships` (
  `object_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `term_taxonomy_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `term_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `wp_term_relationships`
--

INSERT INTO `wp_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`) VALUES
(1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `wp_term_taxonomy`
--

CREATE TABLE `wp_term_taxonomy` (
  `term_taxonomy_id` bigint(20) UNSIGNED NOT NULL,
  `term_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `taxonomy` varchar(32) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `parent` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `count` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `wp_term_taxonomy`
--

INSERT INTO `wp_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES
(1, 1, 'category', '', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wp_usermeta`
--

CREATE TABLE `wp_usermeta` (
  `umeta_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `wp_usermeta`
--

INSERT INTO `wp_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) VALUES
(1, 1, 'nickname', 'max'),
(2, 1, 'first_name', ''),
(3, 1, 'last_name', ''),
(4, 1, 'description', ''),
(5, 1, 'rich_editing', 'true'),
(6, 1, 'syntax_highlighting', 'true'),
(7, 1, 'comment_shortcuts', 'false'),
(8, 1, 'admin_color', 'fresh'),
(9, 1, 'use_ssl', '0'),
(10, 1, 'show_admin_bar_front', 'true'),
(11, 1, 'locale', ''),
(12, 1, 'wp_capabilities', 'a:1:{s:13:\"administrator\";b:1;}'),
(13, 1, 'wp_user_level', '10'),
(14, 1, 'dismissed_wp_pointers', ''),
(15, 1, 'show_welcome_panel', '1'),
(16, 1, 'session_tokens', 'a:1:{s:64:\"1e05c5697cb3e583d0deb9e1dde9fcbf0b095a6212281b0c5087236a40cd05f7\";a:4:{s:10:\"expiration\";i:1739105941;s:2:\"ip\";s:14:\"124.120.39.139\";s:2:\"ua\";s:125:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36 Edg/132.0.0.0\";s:5:\"login\";i:1737896341;}}'),
(17, 1, 'wp_dashboard_quick_press_last_post_id', '5'),
(18, 1, 'community-events-location', 'a:1:{s:2:\"ip\";s:12:\"124.120.39.0\";}'),
(19, 1, 'Impreza_cpt_in_menu_set', '1'),
(20, 1, 'managenav-menuscolumnshidden', 'a:5:{i:0;s:11:\"link-target\";i:1;s:11:\"css-classes\";i:2;s:3:\"xfn\";i:3;s:11:\"description\";i:4;s:15:\"title-attribute\";}'),
(21, 1, 'metaboxhidden_nav-menus', 'a:4:{i:0;s:12:\"add-post_tag\";i:1;s:15:\"add-post_format\";i:2;s:25:\"add-us_portfolio_category\";i:3;s:20:\"add-us_portfolio_tag\";}');

-- --------------------------------------------------------

--
-- Table structure for table `wp_users`
--

CREATE TABLE `wp_users` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_pass` varchar(255) NOT NULL DEFAULT '',
  `user_nicename` varchar(50) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `user_url` varchar(100) NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT 0,
  `display_name` varchar(250) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `wp_users`
--

INSERT INTO `wp_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES
(1, 'max', '$P$BVwrZf92dJEsUpDXVAcaPPWZlfxvsx1', 'max', 'max.sk0211@gmail.com', 'https://demo.dcareclinic.com', '2025-01-26 12:58:44', '', 0, 'max');

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
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `before_after_images`
--
ALTER TABLE `before_after_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `opd_id` (`opd_id`);

--
-- Indexes for table `branch`
--
ALTER TABLE `branch`
  ADD PRIMARY KEY (`branch_id`);

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
  ADD KEY `cus_id` (`cus_id`),
  ADD KEY `fk_course_bookings_room` (`room_id`);

--
-- Indexes for table `course_detail_logs`
--
ALTER TABLE `course_detail_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `updated_by` (`updated_by`);

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
  ADD KEY `order_detail_id` (`order_detail_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`cus_id`),
  ADD UNIQUE KEY `line_user_id` (`line_user_id`);

--
-- Indexes for table `deposit_cancellation_logs`
--
ALTER TABLE `deposit_cancellation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `cancelled_by` (`cancelled_by`);

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
-- Indexes for table `follow_up_notes`
--
ALTER TABLE `follow_up_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `gift_vouchers`
--
ALTER TABLE `gift_vouchers`
  ADD PRIMARY KEY (`voucher_id`),
  ADD UNIQUE KEY `voucher_code` (`voucher_code`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `used_in_order` (`used_in_order`),
  ADD KEY `idx_voucher_code` (`voucher_code`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_expire_date` (`expire_date`),
  ADD KEY `customer_id` (`customer_id`);

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
  ADD KEY `course_id` (`course_id`),
  ADD KEY `note_updated_by` (`note_updated_by`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `unique_permission` (`page`,`action`);

--
-- Indexes for table `permission_logs`
--
ALTER TABLE `permission_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `users_id` (`users_id`),
  ADD KEY `permission_id` (`permission_id`),
  ADD KEY `performed_by` (`performed_by`);

--
-- Indexes for table `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`position_id`);

--
-- Indexes for table `price_adjustment_logs`
--
ALTER TABLE `price_adjustment_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `adjusted_by` (`adjusted_by`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_permission` (`position_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `room_courses`
--
ALTER TABLE `room_courses`
  ADD PRIMARY KEY (`room_course_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `schedule_id` (`schedule_id`);

--
-- Indexes for table `room_schedules`
--
ALTER TABLE `room_schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `room_status`
--
ALTER TABLE `room_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_date` (`room_id`,`date`);

--
-- Indexes for table `service_queue`
--
ALTER TABLE `service_queue`
  ADD PRIMARY KEY (`queue_id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `cus_id` (`cus_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `service_staff_records`
--
ALTER TABLE `service_staff_records`
  ADD PRIMARY KEY (`staff_record_id`);

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
-- Indexes for table `user_specific_permissions`
--
ALTER TABLE `user_specific_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_id` (`users_id`),
  ADD KEY `permission_id` (`permission_id`),
  ADD KEY `granted_by` (`granted_by`);

--
-- Indexes for table `voucher_usage_history`
--
ALTER TABLE `voucher_usage_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `voucher_id` (`voucher_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `wp_commentmeta`
--
ALTER TABLE `wp_commentmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `wp_comments`
--
ALTER TABLE `wp_comments`
  ADD PRIMARY KEY (`comment_ID`),
  ADD KEY `comment_post_ID` (`comment_post_ID`),
  ADD KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  ADD KEY `comment_date_gmt` (`comment_date_gmt`),
  ADD KEY `comment_parent` (`comment_parent`),
  ADD KEY `comment_author_email` (`comment_author_email`(10));

--
-- Indexes for table `wp_links`
--
ALTER TABLE `wp_links`
  ADD PRIMARY KEY (`link_id`),
  ADD KEY `link_visible` (`link_visible`);

--
-- Indexes for table `wp_options`
--
ALTER TABLE `wp_options`
  ADD PRIMARY KEY (`option_id`),
  ADD UNIQUE KEY `option_name` (`option_name`),
  ADD KEY `autoload` (`autoload`);

--
-- Indexes for table `wp_postmeta`
--
ALTER TABLE `wp_postmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `wp_posts`
--
ALTER TABLE `wp_posts`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `post_name` (`post_name`(191)),
  ADD KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  ADD KEY `post_parent` (`post_parent`),
  ADD KEY `post_author` (`post_author`);

--
-- Indexes for table `wp_termmeta`
--
ALTER TABLE `wp_termmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `term_id` (`term_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `wp_terms`
--
ALTER TABLE `wp_terms`
  ADD PRIMARY KEY (`term_id`),
  ADD KEY `slug` (`slug`(191)),
  ADD KEY `name` (`name`(191));

--
-- Indexes for table `wp_term_relationships`
--
ALTER TABLE `wp_term_relationships`
  ADD PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  ADD KEY `term_taxonomy_id` (`term_taxonomy_id`);

--
-- Indexes for table `wp_term_taxonomy`
--
ALTER TABLE `wp_term_taxonomy`
  ADD PRIMARY KEY (`term_taxonomy_id`),
  ADD UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  ADD KEY `taxonomy` (`taxonomy`);

--
-- Indexes for table `wp_usermeta`
--
ALTER TABLE `wp_usermeta`
  ADD PRIMARY KEY (`umeta_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `wp_users`
--
ALTER TABLE `wp_users`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `user_login_key` (`user_login`),
  ADD KEY `user_nicename` (`user_nicename`),
  ADD KEY `user_email` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accessories`
--
ALTER TABLE `accessories`
  MODIFY `acc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `acc_type`
--
ALTER TABLE `acc_type`
  MODIFY `acc_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `before_after_images`
--
ALTER TABLE `before_after_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `branch`
--
ALTER TABLE `branch`
  MODIFY `branch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `course_bookings`
--
ALTER TABLE `course_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `course_detail_logs`
--
ALTER TABLE `course_detail_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `course_resources`
--
ALTER TABLE `course_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `course_type`
--
ALTER TABLE `course_type`
  MODIFY `course_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `course_usage`
--
ALTER TABLE `course_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `cus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `deposit_cancellation_logs`
--
ALTER TABLE `deposit_cancellation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `drug`
--
ALTER TABLE `drug`
  MODIFY `drug_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `drug_type`
--
ALTER TABLE `drug_type`
  MODIFY `drug_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `follow_up_notes`
--
ALTER TABLE `follow_up_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gift_vouchers`
--
ALTER TABLE `gift_vouchers`
  MODIFY `voucher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `opd`
--
ALTER TABLE `opd`
  MODIFY `opd_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `opd_drawings`
--
ALTER TABLE `opd_drawings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_course`
--
ALTER TABLE `order_course`
  MODIFY `oc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `order_course_resources`
--
ALTER TABLE `order_course_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=575;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `od_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `permission_logs`
--
ALTER TABLE `permission_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `position`
--
ALTER TABLE `position`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสตำแหน่ง', AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `price_adjustment_logs`
--
ALTER TABLE `price_adjustment_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `room_courses`
--
ALTER TABLE `room_courses`
  MODIFY `room_course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- AUTO_INCREMENT for table `room_schedules`
--
ALTER TABLE `room_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `room_status`
--
ALTER TABLE `room_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `service_queue`
--
ALTER TABLE `service_queue`
  MODIFY `queue_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `service_staff_records`
--
ALTER TABLE `service_staff_records`
  MODIFY `staff_record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `stock_transactions`
--
ALTER TABLE `stock_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT for table `tool`
--
ALTER TABLE `tool`
  MODIFY `tool_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `unit`
--
ALTER TABLE `unit`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `user_specific_permissions`
--
ALTER TABLE `user_specific_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `voucher_usage_history`
--
ALTER TABLE `voucher_usage_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wp_commentmeta`
--
ALTER TABLE `wp_commentmeta`
  MODIFY `meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wp_comments`
--
ALTER TABLE `wp_comments`
  MODIFY `comment_ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wp_links`
--
ALTER TABLE `wp_links`
  MODIFY `link_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wp_options`
--
ALTER TABLE `wp_options`
  MODIFY `option_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=473;

--
-- AUTO_INCREMENT for table `wp_postmeta`
--
ALTER TABLE `wp_postmeta`
  MODIFY `meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `wp_posts`
--
ALTER TABLE `wp_posts`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `wp_termmeta`
--
ALTER TABLE `wp_termmeta`
  MODIFY `meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wp_terms`
--
ALTER TABLE `wp_terms`
  MODIFY `term_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wp_term_taxonomy`
--
ALTER TABLE `wp_term_taxonomy`
  MODIFY `term_taxonomy_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wp_usermeta`
--
ALTER TABLE `wp_usermeta`
  MODIFY `umeta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `wp_users`
--
ALTER TABLE `wp_users`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`users_id`),
  ADD CONSTRAINT `activity_logs_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branch` (`branch_id`);

--
-- Constraints for table `course_bookings`
--
ALTER TABLE `course_bookings`
  ADD CONSTRAINT `course_bookings_ibfk_2` FOREIGN KEY (`cus_id`) REFERENCES `customer` (`cus_id`),
  ADD CONSTRAINT `fk_course_bookings_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`);

--
-- Constraints for table `permission_logs`
--
ALTER TABLE `permission_logs`
  ADD CONSTRAINT `permission_logs_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`),
  ADD CONSTRAINT `permission_logs_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`),
  ADD CONSTRAINT `permission_logs_ibfk_3` FOREIGN KEY (`performed_by`) REFERENCES `users` (`users_id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`position_id`) REFERENCES `position` (`position_id`),
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`);

--
-- Constraints for table `user_specific_permissions`
--
ALTER TABLE `user_specific_permissions`
  ADD CONSTRAINT `user_specific_permissions_ibfk_1` FOREIGN KEY (`users_id`) REFERENCES `users` (`users_id`),
  ADD CONSTRAINT `user_specific_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`),
  ADD CONSTRAINT `user_specific_permissions_ibfk_3` FOREIGN KEY (`granted_by`) REFERENCES `users` (`users_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
