-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql210.infinityfree.com
-- Generation Time: Apr 04, 2026 at 07:08 PM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_40751516_spareplug`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `image_url`, `parent_id`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'Engine Parts', 'engine-parts', 'Engine components and accessories', 'engine', 'uploads/categories/cat_1767127995.png', NULL, 0, 1, '2025-12-30 20:27:55'),
(2, 'Brake System', 'brake-system', 'Brake pads, discs, and related parts', 'brake', 'uploads/categories/cat_1767127966.png', NULL, 0, 1, '2025-12-30 20:27:55'),
(3, 'Suspension', 'suspension', 'Shocks, struts, and suspension components', 'suspension', 'uploads/categories/cat_1767128027.png', NULL, 0, 1, '2025-12-30 20:27:55'),
(4, 'Electrical', 'electrical', 'Batteries, alternators, and electrical parts', 'electrical', 'uploads/categories/cat_1767127980.png', NULL, 0, 1, '2025-12-30 20:27:55'),
(5, 'Body Parts', 'body-parts', 'Bumpers, mirrors, and body components', 'body', 'uploads/categories/cat_1767127948.png', NULL, 0, 1, '2025-12-30 20:27:55'),
(6, 'Interior', 'interior', 'Seats, dashboards, and interior accessories', 'interior', 'uploads/categories/cat_1767128009.png', NULL, 0, 1, '2025-12-30 20:27:55'),
(7, 'Transmission ', 'transmission-', ' A car transmission is a crucial component in the drivetrain that transfers power from the engine to the wheels.', NULL, 'uploads/categories/cat_1767128341.jpg', NULL, 0, 1, '2025-12-30 20:59:01'),
(8, 'Cooling System', 'cooling-system', 'Radiators, water pumps, thermostats, and cooling fans', NULL, 'uploads/categories/cat_1775214696.png', NULL, 0, 1, '2026-04-03 11:11:36'),
(9, 'Exhaust System', 'exhaust-system', 'Mufflers, catalytic converters, exhaust pipes, and manifolds', NULL, 'uploads/categories/cat_1775215348.png', NULL, 0, 1, '2026-04-03 11:22:27'),
(10, 'Fuel System ', 'fuel-system-', 'Fuel pumps, injectors, filters, and tanks', NULL, 'uploads/categories/cat_1775215372.png', NULL, 0, 1, '2026-04-03 11:22:52'),
(11, 'Steering Components  ', 'steering-components--', 'Steering racks, tie rods, power steering pumps, and columns', NULL, 'uploads/categories/cat_1775215400.png', NULL, 0, 1, '2026-04-03 11:23:20'),
(12, 'Wheels & Tires  ', 'wheels-&-tires--', 'Rims, tires, hubs, and wheel accessories', NULL, 'uploads/categories/cat_1775215423.png', NULL, 0, 1, '2026-04-03 11:23:43');

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `success` tinyint(1) DEFAULT NULL,
  `failure_reason` varchar(255) DEFAULT NULL,
  `two_factor_used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `email`, `ip_address`, `user_agent`, `success`, `failure_reason`, `two_factor_used`, `created_at`) VALUES
(1, 5, 'mishaelsema@gmail.com', '184.162.166.63', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 1, NULL, 0, '2025-12-30 21:16:32'),
(2, 4, 'admin@spareplug.cm', '184.162.166.63', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 1, NULL, 0, '2025-12-31 05:47:12'),
(3, 5, 'mishaelsema@gmail.com', '184.162.166.63', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 1, NULL, 0, '2025-12-31 15:56:38'),
(4, 5, 'mishaelsema@gmail.com', '184.162.166.63', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 1, NULL, 0, '2025-12-31 17:22:47'),
(5, 5, 'mishaelsema@gmail.com', '184.162.166.63', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 1, NULL, 0, '2025-12-31 17:42:22'),
(6, 4, 'admin@spareplug.cm', '129.0.205.217', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 1, NULL, 0, '2026-03-13 13:47:07'),
(7, 4, 'admin@spareplug.cm', '143.105.152.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, NULL, 0, '2026-03-20 08:08:38'),
(8, NULL, 'mishusema237@gmail.com', '143.105.152.42', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 0, 'Invalid credentials', 0, '2026-03-29 22:04:49'),
(9, 4, 'admin@spareplug.cm', '143.105.152.42', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, NULL, 0, '2026-03-29 22:04:54'),
(10, 6, 'mishusema237@gmail.com', '143.105.152.42', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, NULL, 0, '2026-03-29 22:11:18'),
(11, 7, 'mishusema@gmail.com', '143.105.152.42', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, NULL, 0, '2026-03-29 22:28:11'),
(12, 4, 'admin@spareplug.cm', '143.105.152.42', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, NULL, 0, '2026-03-29 22:38:28'),
(13, NULL, 'mishaelsema@gmail.com', '143.105.152.42', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', 0, 'Invalid credentials', 0, '2026-03-29 22:42:53'),
(14, 5, 'mishaelsema@gmail.com', '143.105.152.42', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', 1, NULL, 0, '2026-03-29 22:43:10'),
(15, 4, 'admin@spareplug.cm', '143.105.152.42', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', 1, NULL, 0, '2026-03-29 22:45:35'),
(16, 4, 'admin@spareplug.cm', '143.105.152.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, NULL, 0, '2026-04-03 10:52:03'),
(17, 5, 'mishaelsema@gmail.com', '143.105.152.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, NULL, 0, '2026-04-03 10:53:09'),
(18, 4, 'admin@spareplug.cm', '143.105.152.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 1, NULL, 0, '2026-04-03 11:05:48'),
(19, 6, 'mishusema237@gmail.com', '143.105.152.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, NULL, 0, '2026-04-03 11:31:36'),
(20, 7, 'mishusema@gmail.com', '143.105.152.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, NULL, 0, '2026-04-03 12:18:53'),
(21, 6, 'mishusema237@gmail.com', '143.105.152.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, NULL, 0, '2026-04-03 13:13:24'),
(22, 5, 'mishaelsema@gmail.com', '143.105.152.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, NULL, 0, '2026-04-03 13:18:34'),
(23, 4, 'admin@spareplug.cm', '143.105.152.194', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, NULL, 0, '2026-04-03 13:25:21');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 5, 'info', '', 'Yo ma boi ', NULL, 1, '2025-12-31 05:47:53');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'XAF',
  `condition` enum('new','used','refurbished') NOT NULL,
  `compatibility` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `vendor_id`, `category_id`, `title`, `description`, `price`, `currency`, `condition`, `compatibility`, `images`, `is_featured`, `is_active`, `views_count`, `inquiries_count`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 'Side Mirror ', 'BMW side mirrors left and right in perfectly new condition.', '45000.00', 'XAF', 'new', '{\"make\":\"BMW\",\"model\":\"228i\",\"year\":\"2023\"}', '[\"uploads\\/products\\/prod_69554b68624d4_0.jpg\",\"uploads\\/products\\/prod_69554b6862b9d_1.jpg\"]', 1, 1, 26, 0, '2025-12-31 16:12:24', '2026-03-29 22:23:45'),
(2, 2, 2, 'Genuine Toyota Corolla Front Brake Pads (2014-2019)', ' Original ceramic brake pads removed from a low-mileage 2017 Corolla. Still has about 80% life left. No squeaking or excessive dust. Great for a budget-friendly replacement.', '25000.00', 'XAF', 'used', '{\"make\":\"TOYOTA\",\"model\":\"Corolla\",\"year\":\"2017\"}', '[\"uploads\\/products\\/prod_69c9a4f8c3569_0.png\",\"uploads\\/products\\/prod_69c9a4f8c364a_1.jpg\",\"uploads\\/products\\/prod_69c9a4f8c38ee_2.png\"]', 0, 1, 0, 0, '2026-03-29 22:17:28', '2026-03-29 22:17:28'),
(3, 2, 1, 'Bosch High-Performance Oil Filter', 'Brand new, unopened Bosch oil filter. Provides superior filtration and engine protection. Fits most modern Hyundai SUVs. Box is slightly dented but the filter is perfect.', '8500.00', 'XAF', 'new', '{\"make\":\"HYUNDAI\",\"model\":\"Santa Fe\",\"year\":\"2021\"}', '[\"uploads\\/products\\/prod_69c9a5a46820e_0.jpg\",\"uploads\\/products\\/prod_69c9a5a4682d5_1.jpg\"]', 1, 1, 1, 0, '2026-03-29 22:20:20', '2026-04-03 13:36:37'),
(4, 2, 4, 'Left Side LED Headlight Assembly - Mercedes C-Class', 'Professionally refurbished left-hand headlight. All LED modules tested and working. Lens has been polished to remove scratches. Direct plug-and-play installation.', '145000.00', 'XAF', 'refurbished', '{\"make\":\"MERCEDES-BENZ\",\"model\":\"C-Class\",\"year\":\"2015\"}', '[\"uploads\\/products\\/prod_69c9a64046289_0.png\"]', 0, 1, 0, 0, '2026-03-29 22:22:56', '2026-03-29 22:22:56'),
(5, 3, 7, 'Automatic Transmission Filter & Gasket Kit', 'Complete service kit including a high-quality filter and rubber gasket. Essential for smooth shifting and extending transmission life.', '35000.00', 'XAF', 'new', '{\"make\":\"HONDA\",\"model\":\"Civic\",\"year\":\"2012\"}', '[\"uploads\\/products\\/prod_69c9a81c23264_0.jpg\",\"uploads\\/products\\/prod_69c9a81c23376_1.jpg\"]', 0, 1, 1, 1, '2026-03-29 22:30:52', '2026-04-03 13:31:04'),
(6, 3, 3, 'Front Left Shock Absorber - Original Equipment', 'Genuine OEM shock absorber. No leaks or visible damage. Removed from a vehicle being upgraded to sport suspension. Good damping pressure.', '18000.00', 'XAF', 'used', '{\"make\":\"VOLKSWAGEN\",\"model\":\"Golf\",\"year\":\"2010\"}', '[\"uploads\\/products\\/prod_69c9a8addcd75_0.jpg\",\"uploads\\/products\\/prod_69c9a8addce3f_1.jpg\"]', 0, 1, 1, 0, '2026-03-29 22:33:18', '2026-03-29 22:41:50'),
(7, 3, 6, 'Leather Steering Wheel Cover (Black/Red Stitching)', 'Premium faux leather cover. Provides excellent grip and protects the original steering wheel from sun damage. Easy DIY installation.', '12500.00', 'XAF', 'new', '{\"make\":\"FORD\",\"model\":\"Classic Sedan\",\"year\":\"2018\"}', '[\"uploads\\/products\\/prod_69c9a9d607c97_0.png\"]', 1, 1, 0, 0, '2026-03-29 22:38:14', '2026-03-29 22:39:42'),
(8, 1, 2, 'Toyota Corolla Brake Pads (Front Set)', 'Brand new front brake pads for Toyota Corolla. Very durable and no noise when braking. Imported quality. Suitable for city and highway driving.', '18000.00', 'XAF', 'new', '{\"make\":\"TOYOTA\",\"model\":\"Corolla\",\"year\":\"2008\"}', '[\"uploads\\/products\\/prod_69cf9d62101c7_0.png\"]', 0, 1, 0, 0, '2026-04-03 10:58:42', '2026-04-03 10:58:42'),
(9, 1, 3, 'Honda CR-V Shock Absorbers (Rear Pair)', 'Clean used rear shocks removed from a working vehicle. Still very strong, no leakage. Good for replacement at affordable price.', '35000.00', 'XAF', 'used', '{\"make\":\"HONDA\",\"model\":\"CR-V\",\"year\":\"2010\"}', '[\"uploads\\/products\\/prod_69cf9e3440906_0.png\",\"uploads\\/products\\/prod_69cf9e34409d5_1.png\"]', 0, 1, 0, 0, '2026-04-03 11:02:12', '2026-04-03 11:02:12'),
(10, 1, 8, 'Nissan Pathfinder Radiator', 'Clean radiator with no leaks. Tested and working perfectly. Ideal replacement for overheating issues.', '40000.00', 'XAF', 'used', '{\"make\":\"NISSAN\",\"model\":\"Pathfinder\",\"year\":\"2008\"}', '[\"uploads\\/products\\/prod_69cfa3f6f21fb_0.png\",\"uploads\\/products\\/prod_69cfa3f6f2586_1.png\"]', 0, 1, 0, 0, '2026-04-03 11:26:46', '2026-04-03 11:26:46'),
(11, 1, 1, 'Lexus RX 350 Engine Mount', 'Brand new side mirrors for Kia Picanto. Comes as a pair. Easy to install.', '22000.00', 'XAF', 'used', '{\"make\":\"LEXUS\",\"model\":\"RX\",\"year\":\"2009\"}', '[\"uploads\\/products\\/prod_69cfa4d4bc000_0.png\",\"uploads\\/products\\/prod_69cfa4d4bc0bb_1.png\"]', 0, 1, 0, 0, '2026-04-03 11:30:28', '2026-04-03 11:30:28'),
(12, 2, 10, 'Peugeot 405 Fuel Pump', 'High-quality fuel pump for Peugeot 406. Ensures smooth fuel delivery and engine performance.', '30000.00', 'XAF', 'new', '{\"make\":\"PEUGEOT\",\"model\":\"405\",\"year\":\"2003\"}', '[\"uploads\\/products\\/prod_69cfa81dd00ac_0.png\",\"uploads\\/products\\/prod_69cfa81dd021c_1.png\",\"uploads\\/products\\/prod_69cfa81dd0318_2.png\",\"uploads\\/products\\/prod_69cfa81dd0420_3.png\"]', 0, 1, 0, 0, '2026-04-03 11:44:29', '2026-04-03 11:44:29'),
(13, 2, 5, 'Toyota Camry Front Bumper', 'Original front bumper removed from Toyota Camry. No cracks, just minor scratches. Can be repainted to look brand new.', '45000.00', 'XAF', 'used', '{\"make\":\"TOYOTA\",\"model\":\"Camry\",\"year\":\"2008\"}', '[\"uploads\\/products\\/prod_69cfa9b56b710_0.png\",\"uploads\\/products\\/prod_69cfa9b56b7e3_1.png\"]', 0, 1, 0, 0, '2026-04-03 11:51:17', '2026-04-03 11:51:17'),
(14, 2, 2, 'Honda Accord Brake Discs (Front Pair)', 'High-quality brake discs with excellent heat resistance. Improves braking performance and durability.', '38000.00', 'XAF', 'new', '{\"make\":\"HONDA\",\"model\":\"Accord\",\"year\":\"2021\"}', '[\"uploads\\/products\\/prod_69cfac2623ada_0.png\",\"uploads\\/products\\/prod_69cfac2623c25_1.png\"]', 0, 1, 0, 0, '2026-04-03 12:01:41', '2026-04-03 12:01:41'),
(15, 3, 4, 'Nissan Altima Alternator', 'Reliable alternator with strong charging capacity. No noise or faults. Ready for installation.', '32000.00', 'XAF', 'used', '{\"make\":\"NISSAN\",\"model\":\"Altima\",\"year\":\"2010\"}', '[\"uploads\\/products\\/prod_69cfb171768dc_0.png\",\"uploads\\/products\\/prod_69cfb171769ff_1.png\"]', 0, 1, 0, 0, '2026-04-03 12:24:17', '2026-04-03 12:24:17'),
(16, 3, 9, 'BMW X5 Exhaust Muffler', 'Used muffler with good sound control. Slight wear but still fully functional. Ideal for budget replacement.', '60000.00', 'XAF', 'used', '{\"make\":\"BMW\",\"model\":\"X5\",\"year\":\"2009\"}', '[\"uploads\\/products\\/prod_69cfb25b6bbc0_0.png\"]', 0, 1, 0, 0, '2026-04-03 12:28:11', '2026-04-03 12:28:11'),
(17, 3, 6, 'Hyundai Elantra Leather Seats (Complete Set)', 'Clean leather seats with no tears. Very comfortable and stylish interior upgrade.', '150000.00', 'XAF', 'new', '{\"make\":\"HYUNDAI\",\"model\":\"Elantra\",\"year\":\"2013\"}', '[\"uploads\\/products\\/prod_69cfb3dd95a3f_0.png\"]', 1, 1, 0, 0, '2026-04-03 12:34:37', '2026-04-03 13:27:32'),
(18, 3, 11, 'Toyota Corolla Steering Rack', 'Clean steering rack with smooth control. No leakage, tested and working well.', '55000.00', 'XAF', 'used', '{\"make\":\"TOYOTA\",\"model\":\"Corolla\",\"year\":\"2010\"}', '[\"uploads\\/products\\/prod_69cfb74d13500_0.png\"]', 0, 1, 1, 0, '2026-04-03 12:49:16', '2026-04-03 13:29:42'),
(19, 3, 12, '4x Used Tires 225/65R17 (RAV4)', 'Strong used tires with good tread life. Suitable for daily driving.', '120000.00', 'XAF', 'used', '{\"make\":\"TOYOTA\",\"model\":\"RAV4\",\"year\":\"2012\"}', '[\"uploads\\/products\\/prod_69cfb9e0d0773_0.png\"]', 0, 1, 0, 0, '2026-04-03 13:00:16', '2026-04-03 13:00:16'),
(20, 3, 1, 'Lexus RX 350 Engine (3.5L V6)', 'Strong engine with good compression. Tested and ready for installation.', '600000.00', 'XAF', 'used', '{\"make\":\"LEXUS\",\"model\":\"RX\",\"year\":\"2011\"}', '[\"uploads\\/products\\/prod_69cfba35a8ff6_0.png\",\"uploads\\/products\\/prod_69cfba35a90af_1.png\"]', 0, 1, 0, 0, '2026-04-03 13:01:41', '2026-04-03 13:01:41'),
(21, 2, 11, 'Toyota Corolla Steering Rack (Tokunbo)', 'Imported steering rack in excellent condition. Durable and long-lasting.', '70000.00', 'XAF', 'new', '{\"make\":\"TOYOTA\",\"model\":\"Corolla\",\"year\":\"2010\"}', '[\"uploads\\/products\\/prod_69cfbd4cc3a41_0.png\"]', 0, 1, 0, 0, '2026-04-03 13:14:52', '2026-04-03 13:14:52'),
(22, 2, 12, 'Brand New Tires 225/65R17', 'Durable all-season tires with excellent road grip and long lifespan.', '180000.00', 'XAF', 'new', '{\"make\":\"TOYOTA\",\"model\":\"RAV4\",\"year\":\"2012\"}', '[\"uploads\\/products\\/prod_69cfbd876b07c_0.png\"]', 0, 1, 0, 0, '2026-04-03 13:15:51', '2026-04-03 13:15:51'),
(23, 2, 1, 'Lexus RX 350 Engine (Tokunbo)', 'Imported engine with low mileage. Clean and very reliable.', '720000.00', 'XAF', 'used', '{\"make\":\"LEXUS\",\"model\":\"RX\",\"year\":\"2011\"}', '[\"uploads\\/products\\/prod_69cfbde7273db_0.png\",\"uploads\\/products\\/prod_69cfbde727537_1.png\"]', 0, 1, 0, 0, '2026-04-03 13:17:26', '2026-04-03 13:17:26'),
(24, 1, 11, 'Brand New Toyota Corolla Steering Rack', 'Brand new steering rack for maximum reliability and smooth driving experience.', '95000.00', 'XAF', 'new', '{\"make\":\"TOYOTA\",\"model\":\"Corolla\",\"year\":\"2010\"}', '[\"uploads\\/products\\/prod_69cfbe8e56577_0.png\"]', 0, 1, 0, 0, '2026-04-03 13:20:14', '2026-04-03 13:20:14'),
(25, 1, 12, 'Premium SUV Tires (Set of 4)', 'High-performance tires designed for comfort, grip, and durability on all terrains.', '210000.00', 'XAF', 'new', '{\"make\":\"TOYOTA\",\"model\":\"RAV4\",\"year\":\"2012\"}', '[\"uploads\\/products\\/prod_69cfbefd6ca43_0.png\"]', 1, 1, 0, 0, '2026-04-03 13:22:05', '2026-04-03 13:27:46'),
(26, 1, 1, 'Lexus RX 350 Engine (Reconditioned)', 'Fully reconditioned engine with replaced components. Long-term reliability guaranteed.', '850000.00', 'XAF', 'refurbished', '{\"make\":\"LEXUS\",\"model\":\"RX\",\"year\":\"2011\"}', '[\"uploads\\/products\\/prod_69cfbf56b8f24_0.png\",\"uploads\\/products\\/prod_69cfbf56b906a_1.png\"]', 0, 1, 0, 0, '2026-04-03 13:23:34', '2026-04-03 13:23:34');

-- --------------------------------------------------------

--
-- Table structure for table `product_inquiries`
--

CREATE TABLE `product_inquiries` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `contact_method` enum('whatsapp','call','email','message') NOT NULL,
  `status` enum('pending','responded','closed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `product_inquiries`
--

INSERT INTO `product_inquiries` (`id`, `product_id`, `buyer_id`, `message`, `contact_method`, `status`, `created_at`) VALUES
(1, 5, 4, 'User initiated WhatsApp contact', 'whatsapp', 'pending', '2026-04-03 13:31:04');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `buyer_id`, `vendor_id`, `rating`, `title`, `comment`, `images`, `is_verified`, `vendor_response`, `vendor_response_date`, `created_at`) VALUES
(1, 1, 5, 1, 4, NULL, 'good shit ', NULL, 0, NULL, NULL, '2025-12-31 16:19:03'),
(2, 1, 4, 1, 1, NULL, 'terrible customer service\r\n', NULL, 0, NULL, NULL, '2025-12-31 16:21:33'),
(3, 1, 4, 1, 2, NULL, 'Test Review', NULL, 0, NULL, NULL, '2026-03-13 13:50:14');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `plan_type` enum('free','basic','premium') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'XAF',
  `billing_cycle` enum('monthly','annual') NOT NULL,
  `status` enum('active','expired','cancelled') DEFAULT 'active',
  `starts_at` timestamp NOT NULL,
  `expires_at` timestamp NOT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `phone_verified` tinyint(1) DEFAULT 0,
  `phone_verification_code` varchar(10) DEFAULT NULL,
  `phone_verification_expires` timestamp NULL DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `user_type` enum('buyer','vendor','admin') NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `two_factor_secret` varchar(255) DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_expires` timestamp NULL DEFAULT NULL,
  `last_password_change` timestamp NULL DEFAULT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `email_verification_token` varchar(255) DEFAULT NULL,
  `email_verification_expires` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','suspended','pending') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `phone`, `phone_verified`, `phone_verification_code`, `phone_verification_expires`, `first_name`, `last_name`, `user_type`, `is_verified`, `two_factor_enabled`, `two_factor_secret`, `password_reset_token`, `password_reset_expires`, `last_password_change`, `verification_token`, `email_verification_token`, `email_verification_expires`, `created_at`, `updated_at`, `last_login`, `status`) VALUES
(3, 'admin@spareplug.com', '$2y$10$sg9tqX.Uu/XDPY8UrENojuF1a6zNTtzNMRrzXN77sq8FivBkbsZ/S', NULL, 0, NULL, NULL, 'System', 'Admin', 'admin', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-23 17:22:20', '2025-12-23 17:39:40', '2025-12-23 16:39:40', 'active'),
(4, 'admin@spareplug.cm', '$2y$10$HQDK7O952A/YSMYCmh.DkOtQ3ZZooxSBY/cW4Dl9AH5yuAvxAI/yS', NULL, 0, NULL, NULL, 'Admin', 'User', 'admin', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-23 22:40:15', '2026-04-03 13:25:21', '2026-04-03 16:25:21', 'active'),
(5, 'mishaelsema@gmail.com', '$2y$10$3ltRfLZIAsVFmvmr5oWmAOyvY81VzJ2cJQwBvuQ.KIsimWdeN2oH.', '+237691324233', 0, NULL, NULL, 'Mishael ', 'Sema', 'vendor', 0, 0, NULL, NULL, NULL, NULL, '1934e2c0c0dd64efb536b1f119085edcef3f4f98fdab1ff5d65a2cf99da8aaa8', NULL, NULL, '2025-12-30 21:16:20', '2026-04-03 13:18:34', '2026-04-03 16:18:34', 'active'),
(6, 'mishusema237@gmail.com', '$2y$10$RLPSqn/Nk2HwCDHhKrvbEu.5PqQ8/hzc51yxLI5UM2e5SoQpXw6dm', '+237620846169', 0, NULL, NULL, 'Boominati', 'Dayo', 'vendor', 0, 0, NULL, NULL, NULL, NULL, '0e3f5c52e41f937c58b32dc6621995b2e2479f18a029ef8ba7a65c484937468a', NULL, NULL, '2026-03-29 22:11:13', '2026-04-03 13:13:24', '2026-04-03 16:13:24', 'active'),
(7, 'mishusema@gmail.com', '$2y$10$B/YpiCbcykivCyu0/1ZDDueLD5fH7WzuX8EQjGbdMxw2TaODXZjpK', '+237679080426', 0, NULL, NULL, 'Pozer', 'Da Plug', 'vendor', 0, 0, NULL, NULL, NULL, NULL, 'e844559a6f6eba798900eb62f34408280754037af55c2241a28aa76a0a28e1bb', NULL, NULL, '2026-03-29 22:28:01', '2026-04-03 12:18:53', '2026-04-03 15:18:53', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_profiles`
--

CREATE TABLE `vendor_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `business_type` enum('individual','company','garage','dealer') NOT NULL,
  `description` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `subscription_plan` enum('free','basic','premium') NOT NULL DEFAULT 'free',
  `is_verified` tinyint(1) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `total_sales` int(11) DEFAULT 0,
  `logo_url` varchar(255) DEFAULT NULL,
  `cover_url` varchar(255) DEFAULT NULL,
  `operating_hours` text DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `google_map_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `website` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `services` text DEFAULT NULL,
  `other_info` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_profiles`
--

INSERT INTO `vendor_profiles` (`id`, `user_id`, `business_name`, `business_type`, `description`, `address`, `city`, `region`, `subscription_plan`, `is_verified`, `rating`, `total_sales`, `logo_url`, `cover_url`, `operating_hours`, `facebook_url`, `instagram_url`, `twitter_url`, `google_map_url`, `created_at`, `website`, `facebook`, `instagram`, `twitter`, `services`, `other_info`) VALUES
(1, 5, 'Metro Booming Auto', 'individual', 'Metro Booming Auto is a modern automotive business focused on delivering reliable vehicles, quality auto parts, and professional car-related services to urban and suburban customers. The company is built around convenience, trust, and performance, serving drivers who want their cars to move smoothly and their time respected.\r\n\r\nWe specialize in car sales, auto parts supply, vehicle maintenance, and diagnostics, offering everything from everyday repairs to performance upgrades. Whether a customer is looking to buy a car, replace a critical component, or keep their vehicle in peak condition, Metro Booming Auto provides solutions that are efficient, affordable, and transparent.\r\n\r\nDriven by innovation and customer satisfaction, Metro Booming Auto blends skilled technicians, genuine parts, and modern automotive technology to keep vehicles road-ready and safe. Our mission is simple: keep the city moving, one car at a time.', 'Damas, Yaounde', 'Yaounde', 'Centre', 'free', 0, '0.00', 0, 'uploads/logos/logo_1_1767129422.jpg', 'uploads/covers/cover_1_1767129826.jpg', '{\"monday\":{\"open\":\"01:02\",\"close\":\"01:00\"},\"tuesday\":{\"open\":\"01:00\",\"close\":\"01:00\"}}', NULL, NULL, NULL, 'https://www.google.com/maps/place/Esm%C3%A9e+Queen+,+artiste+maquilleuse+,makeup+artiste+,mannequin+boudoir.+Personnalit%C3%A9+publique/@46.8320256,-71.2278016,14z/data=!4m6!3m5!1s0x4cb8bd03216e9c9f:0xd3ade9617576ff1a!8m2!3d46.8234852!4d-71.2291447!16s%2Fg%2F11s353s20s?entry=ttu&g_ep=EgoyMDI1MTIwOS4wIKXMDSoKLDEwMDc5MjA2OUgBUAM%3D', '2025-12-30 21:16:20', 'https://www.sp-spareparts.com/en/c/contact-us?_gl=1*1go59aq*_up*MQ..*_gs*MQ..&gclid=CjwKCAiAjc7KBhBvEiwAE2BDOYHpKaVmoPCYiEy5fOgzBtNmNKVOavqqD455E2m-3sJHPjiSKt6uHxoC5mEQAvD_BwE&gbraid=0AAAAAC4m4ro78_HQULL3zs9JzCbe_HVQR', 'https://www.facebook.com/partsourcecanada/', 'https://www.instagram.com/sparepartslife/', '', NULL, NULL),
(2, 6, 'BOOMINATI Automobile', 'individual', 'BOOMINATI Automobile is your trusted plug for quality car parts and honest automotive advice in Yaoundé. We specialize in sourcing durable new and tokunbo parts for a wide range of vehicles including Toyota, Honda, Mercedes-Benz, Peugeot, and more.\r\n\r\nWhether you\'re fixing a daily driver or upgrading your ride, we help you find the right part at the right price. Our focus is simple: reliability, affordability, and customer satisfaction.', 'Rue Damas\r\nRound point Damas', 'Yaounde', 'Centre', 'free', 0, '0.00', 0, 'uploads/logos/logo_2_1775218679.png', 'uploads/covers/cover_2_1775218620.jpg', '{\"monday\":{\"open\":\"08:00\",\"close\":\"17:00\"},\"tuesday\":{\"open\":\"08:00\",\"close\":\"17:00\"},\"wednesday\":{\"open\":\"08:00\",\"close\":\"17:00\"},\"friday\":{\"open\":\"08:00\",\"close\":\"17:00\"},\"saturday\":{\"open\":\"08:00\",\"close\":\"17:00\"}}', NULL, NULL, NULL, '', '2026-03-29 22:11:13', 'https://mishael-sema.vercel.app/', '', 'https://www.instagram.com/mishael_sema/', '', 'Spare parts sales (new & tokunbo)\r\nPart sourcing on request (hard-to-find parts)\r\nInstallation guidance & technical advice\r\nLocal delivery within Yaoundé\r\nMechanic referrals (trusted technicians)\r\nBasic diagnostics consultation', 'We deal only in tested and trusted parts\r\nAll used parts are inspected before sale\r\nNegotiation allowed for serious buyers\r\nFast response on WhatsApp for inquiries\r\nBulk purchases available for workshops\r\nDelivery available within Yaoundé at small fee'),
(3, 7, 'POZER AUTO', 'garage', 'POZER AUTO is a trusted garage and auto parts specialist based in Yaoundé, focused on Japanese and European vehicles. With years of hands-on experience, we provide quality spare parts, accurate diagnostics, and dependable repair services.\r\n\r\nFrom routine maintenance to complex mechanical issues, our team ensures your vehicle stays reliable on the road. We combine technical expertise with honest pricing and customer-focused service.', 'Some where in mayor street', 'Yaounde', 'Nord-Ouest', 'free', 0, '0.00', 0, 'uploads/logos/logo_3_1775221972.png', 'uploads/covers/cover_3_1775221960.png', '{\"monday\":{\"open\":\"08:00\",\"close\":\"17:00\"},\"tuesday\":{\"open\":\"08:00\",\"close\":\"17:00\"},\"wednesday\":{\"open\":\"08:00\",\"close\":\"17:00\"},\"thursday\":{\"open\":\"12:00\",\"close\":\"17:00\"},\"friday\":{\"open\":\"08:00\",\"close\":\"17:00\"},\"saturday\":{\"open\":\"08:00\",\"close\":\"17:00\"},\"sunday\":{\"open\":\"11:00\",\"close\":\"17:00\"}}', NULL, NULL, NULL, '', '2026-03-29 22:28:01', 'https://mishael-sema.vercel.app/', '', 'https://www.instagram.com/mishael_sema/', '', 'Mechanical repairs (engine, suspension, brakes)\r\nSpare parts sales (new & tokunbo)\r\nEngine diagnostics & fault detection\r\nOil change and routine servicing\r\nVehicle inspection before purchase\r\nInstallation of purchased parts\r\nBattery replacement and electrical checks\r\nLocal delivery of spare parts', 'Specialized in Toyota, Honda, Lexus, Mercedes-Benz, and BMW\r\nExperienced technicians with over 10 years in the field\r\nGenuine and tested parts only\r\nFair pricing with negotiation for loyal customers\r\nQuick turnaround time for most repairs\r\nCustomer satisfaction is our priority');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `product_inquiries`
--
ALTER TABLE `product_inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `buyer_id` (`buyer_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_inquiries`
--
ALTER TABLE `product_inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  ADD CONSTRAINT `vendor_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
