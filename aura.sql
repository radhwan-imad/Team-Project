-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 24, 2025 at 02:57 PM
-- Server version: 8.0.41-0ubuntu0.20.04.1
-- PHP Version: 8.3.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cs2team40_aura`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `Address_ID` int NOT NULL,
  `Address_line_1` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Address_line_2` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Postcode` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `country` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`Address_ID`, `Address_line_1`, `Address_line_2`, `Postcode`, `country`) VALUES
(2, '111, Gokuldham Society,', 'Abharama Road', '395006', 'India'),
(5, '70, Chantry road', 'handsworth', 'B219JB', 'United Kingdom'),
(6, '10 jennens road', 'Flat 23, Block B1, Room 2', 'B4 7EN', 'United Kingdom'),
(7, 'Flat', '2 Ladypool Road', 'B12 8JZ', 'United Kingdom'),
(8, 'Flat', '4 Ladypool Road', 'B12 8JZ', 'United Kingdom'),
(9, 'Flat 1', '42 Ladypool Road', 'B12 8JZ', 'United Kingdom'),
(10, 'Flat 1', '412 Ladypool Road', 'B12 8JZ', 'United Kingdom'),
(11, '410 ladypool road', '', 'B12 8JZ', 'United Kingdom');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `Admin_ID` int NOT NULL,
  `First_Name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `Last_Name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `Email` varchar(150) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `Role` enum('Super Admin','Manager','Editor') COLLATE utf8mb4_unicode_520_ci DEFAULT 'Manager',
  `Created_At` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`Admin_ID`, `First_Name`, `Last_Name`, `Email`, `Password`, `Role`, `Created_At`) VALUES
(1, 'Ayan', 'Khan', 'aura@aston.com', 'admin1234', 'Super Admin', '2025-02-17 23:47:48');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `Cart_ID` int NOT NULL,
  `User_ID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`Cart_ID`, `User_ID`) VALUES
(4, 6),
(9, 8),
(3, 10),
(1, 11),
(6, 12),
(5, 13),
(7, 17),
(8, 18),
(10, 19),
(11, 20),
(12, 21);

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `Cart_Item_ID` int NOT NULL,
  `Cart_ID` int NOT NULL,
  `Product_ID` int NOT NULL,
  `Quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`Cart_Item_ID`, `Cart_ID`, `Product_ID`, `Quantity`) VALUES
(72, 9, 14, 1),
(80, 10, 14, 4),
(81, 10, 8, 1),
(87, 11, 14, 1),
(127, 5, 8, 4);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `Category_ID` int NOT NULL,
  `Name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`Category_ID`, `Name`, `description`) VALUES
(1, 'Perfume', 'Fragrances for daily use'),
(2, 'Oil', 'Premium quality scented oils'),
(3, 'Floral Perfumes', 'Floral perfumes capture the essence of blooming gardens, evoking romance, elegance, and timeless femininity. These fragrances are built around the delicate, fresh, and enchanting scents of flowers, such as rose, jasmine, lily, peony, and violet. Often associated with sophistication and charm, floral perfumes range from soft and powdery to vibrant and lush, making them perfect for every occasion. Whether a single bloom or a bouquet-inspired blend, these scents are a celebration of nature’s beauty and offer a sensory escape into a world of pure serenity. Ideal for those who love graceful and classic aromas with a touch of modern allure.'),
(4, 'Fruity Perfumes', 'Fruity perfumes are a burst of vibrant energy, offering playful and juicy aromas inspired by ripe, luscious fruits. These fragrances are infused with the sweetness of berries, the zest of citrus, or the tropical allure of mango, pineapple, and passionfruit. Perfect for adding a fresh, youthful, and cheerful touch to your day, fruity perfumes balance sweetness with a refreshing, tangy twist. They are versatile and uplifting, making them ideal for casual outings, sunny days, or whenever you want to radiate a carefree and joyful vibe. Embrace the irresistible charm of fruity perfumes for a deliciously delightful fragrance experience.'),
(5, 'Floral Candles', 'Floral candles bring the soothing and uplifting essence of blooming gardens into your space, creating a serene and inviting atmosphere. Infused with the delicate fragrances of roses, jasmine, lavender, lilacs, and more, these candles evoke feelings of romance, tranquility, and natural beauty. Perfect for relaxation, setting a cozy mood, or enhancing the elegance of any room, floral candles are a timeless choice for aromatherapy and home décor. Whether it\'s a single floral note or a harmonious bouquet, their soft, fragrant glow adds warmth and charm to every moment.'),
(6, 'Fruity Candles', 'Fruity candles infuse your space with the juicy, uplifting aromas of ripe fruits, creating a cheerful and energizing ambiance. From the sweet scent of berries and peaches to the zesty tang of citrus and tropical notes like mango and pineapple, these candles are a delightful way to brighten any room. Perfect for adding a touch of playfulness and vibrancy to your home, fruity candles evoke memories of sunny days and fresh-picked fruits. Their refreshing fragrances are ideal for kitchens, living spaces, or any area where you want to spark joy and positivity.'),
(7, 'Reed Diffuser', 'A reed diffuser is a home fragrance product that uses natural reeds or sticks to disperse scent throughout a room.');

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `City_ID` int NOT NULL,
  `Name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`City_ID`, `Name`) VALUES
(1, 'Birmingham'),
(2, 'London'),
(3, 'Leicester'),
(4, 'Bristol'),
(5, 'Liverpool'),
(6, ' Manchester'),
(7, 'Bradford'),
(8, ' Derby'),
(9, ' Southampton'),
(10, 'Coventry');

-- --------------------------------------------------------

--
-- Table structure for table `county`
--

CREATE TABLE `county` (
  `County_ID` int NOT NULL,
  `Name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `City_ID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE `image` (
  `Image_ID` int NOT NULL,
  `Product_ID` int NOT NULL,
  `Image_URL` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Image_descriptioin` text COLLATE utf8mb4_general_ci,
  `Is_Main_Image` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `image`
--

INSERT INTO `image` (`Image_ID`, `Product_ID`, `Image_URL`, `Image_descriptioin`, `Is_Main_Image`) VALUES
(3, 3, 'Pristelle.png', 'Indulge in the timeless allure of Pristelle, where the fresh zest of grapefruit and the elegance of damask rose weave a symphony of sophistication. A heart of creamy vanilla and sweet honeysuckle adds warmth, while a base of animalic musk and amber leaves a sensual, lingering embrace. Perfect for those who crave understated luxury.', 1),
(4, 4, 'Tresor_lush.png', 'Tresor Lush is a celebration of refined elegance, blending the fruity freshness of apple and bergamot with delicate white lily. The floral heart of violet, frangipani, and lily of the valley captivates, while a rich base of musk, vanilla, and earthy patchouli grounds this captivating scent in timeless beauty.', 1),
(5, 5, 'Ayura_bloom.png', 'Transport yourself to a serene garden with Ayura Bloom, where herbaceous and citrus notes of bergamot and white lily bloom into a vibrant green heart. Accents of vanilla and coffee create an enchanting finish, making this fragrance an irresistible escape into nature’s embrace.', 1),
(6, 6, 'Mosharra_essence.png', 'Mosharra Essence exudes mystery and charm, starting with the spicy sweetness of pink peppercorn and the elegance of damask rose. A bold heart of coffee and honeysuckle brings richness, while a base of animalic musk and amber ensures a long-lasting, intoxicating trail.', 1),
(7, 7, 'Radwen\'s_Haven.png', 'Journey into opulence with Radwen\'s Haven, a sophisticated blend of zesty grapefruit, spicy nutmeg, and luxurious saffron. The heart blooms with orange blossom, cinnamon, jasmine, and sandalwood, settling into an amber and sandalwood base for an unforgettable, warm finish.', 1),
(8, 8, 'Duskhaven.png', 'Duskhaven is a tribute to the exotic, combining the warm spice of nutmeg and saffron with a heart of cinnamon, jasmine, and sandalwood. A base of deep, smoky oud adds depth, making this fragrance a perfect companion for adventurous souls.', 1),
(9, 9, 'Lirien.png', 'Lirien envelops you in an ethereal embrace, with oud and white lily creating a mesmerizing opening. A heart of myrrh and lily of the valley lends a mystical touch, while a base of musk, rose, and jojoba seed adds softness and grace to this enchanting perfume.', 1),
(10, 10, 'Lustrewood.png', 'Bold and captivating, Lustrewood opens with a single, intoxicating note of saffron. The heart reveals a spicy blend of cinnamon, jasmine, and sandalwood, while the base of patchouli, violet, and musk leaves a velvety trail of elegance.', 1),
(11, 11, 'Rayun.png', 'Playful yet refined, Rayun bursts with the juicy sweetness of strawberries, complemented by a heart of fresh bergamot and romantic peony. The scent settles into a delicate base of cedarwood and lily of the valley, offering a radiant and uplifting finish.', 1),
(12, 12, 'Pristelle_Candle.png', 'Pristelle_Candle', 1),
(13, 13, 'Tresor_Lush_Candle.png', 'Tresor_Lush_Candle', 1),
(14, 14, 'Ayura_Bloom_Candle.png', 'Ayura_Bloom_Candle', 1),
(15, 15, 'Mosharra_Essence_Candle.png', 'Mosharra_Essence_Candle', 1),
(16, 16, 'Radwen\'s_Haven_Candle.png', 'Radwen\'s_Haven_Candle', 1),
(17, 17, 'Duskhaven_Candle.png', 'Duskhaven_Candle', 1),
(18, 18, 'Lirien_Candle.png', 'Lirien_Candle', 1),
(19, 19, 'Lustrewood_Candle.png', 'Lustrewood_Candle', 1),
(20, 20, 'Rayun_Candle.png', 'Rayun_Candle', 1),
(37, 41, 'Blue.png', 'csdc', 1),
(38, 42, 'Green.png', 'Radwen’s Haven diffuser', 1),
(39, 43, 'orange.png', 'Lirien reed diffuser', 1),
(40, 44, 'purple.png', 'Duskhaven reed diffuser', 1),
(41, 45, 'red.png', 'Tresor Lush Reed Diffuser', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notes_library`
--

CREATE TABLE `notes_library` (
  `Note_ID` int NOT NULL,
  `Note_Name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `Note_Image` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Note_Text` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes_library`
--

INSERT INTO `notes_library` (`Note_ID`, `Note_Name`, `Note_Image`, `Note_Text`) VALUES
(1, 'Damask Rose', 'Damask_Rose.png', 'The top notes have a fruity and floral blend of grapefruit and Damascena rose.'),
(2, 'Honeysuckle', 'Honeysuckle.png', 'Experience the subtle earthy middle note of musk with the subtle sweetness of honeysuckle.'),
(3, 'Vanilla', 'Vanilla.png', 'Enjoy pure sweetness with this fragrance’s base note of vanilla and amber.'),
(4, 'Apple', 'Apple.png', 'A bursting elixir of succulent apple is blended with juicy blackcurrants to deliver a sweet top note.'),
(5, 'Violet', 'Violet.png', 'A powdery, delicate floral violet heart promises to uplift your spirits with enticing warmth.'),
(6, 'Patchouli', 'Patchouli.png', 'Patchouli lends an earthy, woody deepness to the lingering base of this fragrance.'),
(7, 'Herb Accord', 'Herb_Accord.png', 'Revel in the wonders of nature with the top note of herb accord, blending various earthy flavours.'),
(8, 'Green Accord', 'Green_Accord.png', 'With the middle note as green accord, experience a combination of freshly cut grass and leaves.'),
(9, 'Coffee', 'Coffee.png', 'Earthy, nutty and woody scents feature as the base notes of tonka beans and coffee.'),
(10, 'Pink Peppercorn', 'Pink_Peppercorn.png', 'An aromatic heart of pink peppercorn brings a subtle spice, evoking refined sophistication.'),
(11, 'Grape Fruit', 'Grape_Fruit.png', 'A tangy grapefruit top note adds zing to this lively, invigorating fragrance.'),
(12, 'Orange Blossom', 'Orange_Blossom.png', 'Soft orange blossom heart notes bring floral, romantic warmth to this hypnotic fragrance.'),
(13, 'Sandalwood', 'Sandalwood.png', 'Sandalwood base notes awaken the senses with their woody warmth.'),
(14, 'Saffron', 'Saffron.png', 'Addictively morish notes of saffron lend a spicy cut through the entire scent.'),
(15, 'Cinnamon', 'Cinnamon.png', 'Notes of cinnamon at the heart lend a mischievous and seductive edge.'),
(16, 'Myrrh', 'Myrrh.png', 'The enveloping warmth of myrrh exudes elegance and sophistication.'),
(17, 'Jojoba Seed', 'Jojoba_Seed.png', 'Slightly nutty golden jojoba seeds bring a subtle opening to this beautiful scent.'),
(18, 'Jasmine', 'Jasmine.png', 'Opulent jasmine heart notes bring a sweet, fruity touch to this intoxicating fragrance.'),
(19, 'Strawberry', 'Strawberry.png', 'Indulge in a midsummer daydream with the succulent strawberry top notes woven through the scent.'),
(20, 'Peony', 'Peony.png', 'Like a freshly picked bouquet, notes of peony swirl the scent for a refreshingly feminine charm.'),
(21, 'Cedarwood', 'Cedarwood.png', 'Deep and enticingly warm, cedarwood will immerse your senses into a sensual wonderland.'),
(22, 'Guaiacwood', 'Guaiacwood.png', 'Enticing guaiacwood is equal measures smoky and sweet for a sultry base.'),
(23, 'Coconut', 'Coconut.png', 'Creamy coconut lays at the heart, balancing the poetic signature of this spicy scent.');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `Order_ID` int NOT NULL,
  `User_ID` int NOT NULL,
  `Cart_ID` int NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`Order_ID`, `User_ID`, `Cart_ID`, `date`, `status`) VALUES
(6, 11, 1, '2025-02-01 16:44:09', 'Shipped'),
(8, 11, 1, '2025-02-01 16:52:01', 'Delivered'),
(12, 17, 7, '2025-03-12 15:23:09', 'Cancelled'),
(14, 17, 7, '2025-03-12 15:27:21', 'Placed'),
(18, 11, 1, '2025-03-12 20:26:58', 'Processing'),
(19, 18, 8, '2025-03-16 21:54:10', 'Delivered'),
(20, 18, 8, '2025-03-16 22:09:26', 'Placed'),
(24, 10, 3, '2025-03-18 14:30:43', 'Placed'),
(25, 18, 8, '2025-03-18 15:06:28', 'Placed'),
(26, 6, 4, '2025-03-18 15:17:59', 'Shipped'),
(27, 10, 3, '2025-03-20 10:36:58', 'Placed'),
(28, 10, 3, '2025-03-22 13:23:47', 'Placed'),
(29, 18, 8, '2025-03-22 13:41:01', 'Placed'),
(30, 12, 6, '2025-03-23 16:56:42', 'Placed'),
(31, 17, 7, '2025-03-23 18:54:46', 'Placed'),
(35, 17, 7, '2025-03-23 23:53:52', 'Placed'),
(36, 17, 7, '2025-03-23 23:58:52', 'Placed'),
(38, 13, 5, '2025-03-24 00:25:02', 'Placed'),
(39, 10, 3, '2025-03-24 00:26:43', 'Placed'),
(40, 13, 5, '2025-03-24 00:34:30', 'Placed'),
(41, 13, 5, '2025-03-24 00:36:53', 'Placed'),
(42, 13, 5, '2025-03-24 00:42:14', 'Placed'),
(44, 10, 3, '2025-03-24 00:47:04', 'Placed'),
(45, 21, 12, '2025-03-24 01:11:13', 'Placed'),
(47, 6, 4, '2025-03-24 13:10:09', 'Placed');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `Order_Items_ID` int NOT NULL,
  `Order_ID` int NOT NULL,
  `Product_ID` int NOT NULL,
  `Quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`Order_Items_ID`, `Order_ID`, `Product_ID`, `Quantity`) VALUES
(83, 12, 17, 2),
(85, 14, 17, 1),
(91, 18, 5, 1),
(92, 18, 14, 2),
(93, 18, 8, 1),
(94, 19, 5, 1),
(95, 19, 10, 1),
(96, 19, 16, 1),
(97, 19, 13, 1),
(98, 19, 9, 1),
(99, 19, 17, 2),
(100, 20, 14, 2),
(104, 24, 17, 1),
(105, 24, 9, 1),
(106, 25, 9, 5),
(107, 26, 11, 1),
(108, 26, 8, 1),
(109, 26, 5, 1),
(110, 27, 8, 1),
(111, 28, 14, 3),
(112, 28, 8, 1),
(113, 28, 17, 1),
(114, 28, 9, 1),
(115, 28, 5, 2),
(116, 28, 19, 1),
(117, 29, 14, 1),
(118, 30, 7, 2),
(119, 30, 12, 1),
(120, 31, 7, 1),
(125, 35, 41, 13),
(126, 36, 41, 11),
(132, 38, 41, 6),
(133, 38, 14, 1),
(134, 38, 43, 1),
(135, 38, 10, 1),
(136, 38, 7, 1),
(137, 38, 8, 1),
(138, 39, 5, 1),
(139, 39, 14, 1),
(140, 39, 8, 1),
(141, 39, 41, 1),
(142, 40, 17, 2),
(143, 41, 5, 1),
(144, 42, 8, 1),
(146, 44, 14, 1),
(147, 45, 9, 1),
(148, 45, 16, 1),
(149, 45, 5, 1),
(150, 45, 41, 2),
(151, 45, 43, 1),
(152, 45, 17, 1),
(153, 45, 8, 1),
(155, 47, 41, 1),
(156, 47, 8, 1),
(157, 47, 17, 2);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `Payment_ID` int NOT NULL,
  `Order_ID` int NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`Payment_ID`, `Order_ID`, `Amount`, `method`, `Status`) VALUES
(1, 20, 79.98, 'Credit Card', 'Completed'),
(5, 24, 169.98, 'Credit Card', 'Completed'),
(6, 25, 599.95, 'MasterCard', 'Completed'),
(7, 26, 339.97, 'Credit Card', 'Completed'),
(8, 27, 119.99, 'Credit Card', 'Completed'),
(9, 28, 654.91, 'Credit Card', 'Completed'),
(10, 29, 39.99, 'Credit Card', 'Completed'),
(11, 30, 259.97, 'Credit Card', 'Completed'),
(12, 31, 109.99, 'MasterCard', 'Completed'),
(16, 35, 650.00, 'MasterCard', 'Completed'),
(17, 36, 550.00, 'MasterCard', 'Completed'),
(19, 38, 739.96, 'MasterCard', 'Completed'),
(20, 39, 309.97, 'Visa', 'Completed'),
(21, 40, 99.98, 'MasterCard', 'Completed'),
(22, 41, 99.99, 'MasterCard', 'Completed'),
(23, 42, 119.99, 'MasterCard', 'Completed'),
(25, 44, 39.99, 'Visa', 'Completed'),
(26, 45, 599.95, 'MasterCard', 'Completed'),
(28, 47, 269.97, 'Credit Card', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `postcode`
--

CREATE TABLE `postcode` (
  `Postcode` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `City_ID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `postcode`
--

INSERT INTO `postcode` (`Postcode`, `City_ID`) VALUES
('395006', 1),
('395007', 1),
('B12 8JZ', 1),
('B219JB', 1),
('B4 7EN', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `Product_ID` int NOT NULL,
  `Name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `Category_ID` int NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `Image_ID` int DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Best_Seller` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`Product_ID`, `Name`, `Category_ID`, `description`, `Image_ID`, `Price`, `Best_Seller`) VALUES
(3, 'Pristelle', 3, 'Indulge in the timeless allure of Pristelle, where the fresh zest of grapefruit and the elegance of damask rose weave a symphony of sophistication. A heart of creamy vanilla and sweet honeysuckle adds warmth, while a base of animalic musk and amber leaves a sensual, lingering embrace. Perfect for those who crave understated luxury.', 3, 90.00, 1),
(4, 'Tresor Lush', 3, 'Tresor Lush is a celebration of refined elegance, blending the fruity freshness of apple and bergamot with delicate white lily. The floral heart of violet, frangipani, and lily of the valley captivates, while a rich base of musk, vanilla, and earthy patchouli grounds this captivating scent in timeless beauty.', 4, 109.99, 0),
(5, 'Ayura Bloom ', 3, 'Transport yourself to a serene garden with Ayura Bloom, where herbaceous and citrus notes of bergamot and white lily bloom into a vibrant green heart. Accents of vanilla and coffee create an enchanting finish, making this fragrance an irresistible escape into nature’s embrace.', 5, 99.99, 0),
(6, 'Mosharra Essence ', 3, 'Mosharra Essence exudes mystery and charm, starting with the spicy sweetness of pink peppercorn and the elegance of damask rose. A bold heart of coffee and honeysuckle brings richness, while a base of animalic musk and amber ensures a long-lasting, intoxicating trail.', 6, 114.99, 0),
(7, 'Radwen\'s Haven', 3, 'Journey into opulence with Radwen\'s Haven, a sophisticated blend of zesty grapefruit, spicy nutmeg, and luxurious saffron. The heart blooms with orange blossom, cinnamon, jasmine, and sandalwood, settling into an amber and sandalwood base for an unforgettable, warm finish.', 7, 109.99, 0),
(8, 'Duskhaven ', 4, 'Duskhaven is a tribute to the exotic, combining the warm spice of nutmeg and saffron with a heart of cinnamon, jasmine, and sandalwood. A base of deep, smoky oud adds depth, making this fragrance a perfect companion for adventurous souls.', 8, 119.99, 0),
(9, 'Lirien', 4, 'Lirien envelops you in an ethereal embrace, with oud and white lily creating a mesmerizing opening. A heart of myrrh and lily of the valley lends a mystical touch, while a base of musk, rose, and jojoba seed adds softness and grace to this enchanting perfume.', 9, 119.99, 0),
(10, 'Lustrewood', 4, 'Bold and captivating, Lustrewood opens with a single, intoxicating note of saffron. The heart reveals a spicy blend of cinnamon, jasmine, and sandalwood, while the base of patchouli, violet, and musk leaves a velvety trail of elegance.', 10, 109.99, 1),
(11, 'Rayun', 4, 'Playful yet refined, Rayun bursts with the juicy sweetness of strawberries, complemented by a heart of fresh bergamot and romantic peony. The scent settles into a delicate base of cedarwood and lily of the valley, offering a radiant and uplifting finish.', 11, 119.99, 1),
(12, 'Pristelle Candle', 5, 'Indulge in the timeless allure of Pristelle, where the fresh zest of grapefruit and the elegance of damask rose weave a symphony of sophistication. A heart of creamy vanilla and sweet honeysuckle adds warmth, while a base of animalic musk and amber leaves a sensual, lingering embrace. Perfect for those who crave understated luxury.', 12, 39.99, 1),
(13, 'Tresor Lush Candle', 5, 'Tresor Lush is a celebration of refined elegance, blending the fruity freshness of apple and bergamot with delicate white lily. The floral heart of violet, frangipani, and lily of the valley captivates, while a rich base of musk, vanilla, and earthy patchouli grounds this captivating scent in timeless beauty.', 13, 44.99, 0),
(14, 'Ayura Bloom Candle', 5, 'Transport yourself to a serene garden with Ayura Bloom, where herbaceous and citrus notes of bergamot and white lily bloom into a vibrant green heart. Accents of vanilla and coffee create an enchanting finish, making this fragrance an irresistible escape into nature’s embrace.', 14, 39.99, 0),
(15, 'Mosharra Essence Candle', 5, 'Mosharra Essence exudes mystery and charm, starting with the spicy sweetness of pink peppercorn and the elegance of damask rose. A bold heart of coffee and honeysuckle brings richness, while a base of animalic musk and amber ensures a long-lasting, intoxicating trail.', 15, 44.99, 0),
(16, 'Radwen\'s Haven Candle', 5, 'Journey into opulence with Radwen\'s Haven, a sophisticated blend of zesty grapefruit, spicy nutmeg, and luxurious saffron. The heart blooms with orange blossom, cinnamon, jasmine, and sandalwood, settling into an amber and sandalwood base for an unforgettable, warm finish.', 16, 49.99, 0),
(17, 'Duskhaven Candle', 6, 'Duskhaven is a tribute to the exotic, combining the warm spice of nutmeg and saffron with a heart of cinnamon, jasmine, and sandalwood. A base of deep, smoky oud adds depth, making this fragrance a perfect companion for adventurous souls.', 17, 49.99, 1),
(18, 'Lirien Candle', 6, 'Lirien envelops you in an ethereal embrace, with oud and white lily creating a mesmerizing opening. A heart of myrrh and lily of the valley lends a mystical touch, while a base of musk, rose, and jojoba seed adds softness and grace to this enchanting perfume.', 18, 39.99, 0),
(19, 'Lustrewood Candle', 6, 'Bold and captivating, Lustrewood opens with a single, intoxicating note of saffron. The heart reveals a spicy blend of cinnamon, jasmine, and sandalwood, while the base of patchouli, violet, and musk leaves a velvety trail of elegance.', 19, 44.99, 0),
(20, 'Rayun Candle', 6, 'Playful yet refined, Rayun bursts with the juicy sweetness of strawberries, complemented by a heart of fresh bergamot and romantic peony. The scent settles into a delicate base of cedarwood and lily of the valley, offering a radiant and uplifting finish.', 20, 49.99, 0),
(41, 'Ayura Bloom Diffuser', 7, ' Awaken your senses with the serene aura of Ayura Bloom. Inspired by the crisp morning air and the soft petals of springtime flowers, this diffuser brings a light, refreshing touch to any room. Subtle aquatic notes mingle with delicate blossoms, creating an ambience that’s both calming and rejuvenating—perfect for brightening your day or easing into a peaceful evening.', 37, 50.00, 1),
(42, 'Radwen’s Haven Diffuser', 7, 'Transport yourself to a lush garden retreat with Radwen’s Haven. Hints of fresh-cut herbs and a whisper of woodland greenery infuse your space with an energizing, natural essence. This diffuser balances notes of leafy freshness with a comforting warmth, making it ideal for those who love the outdoors and crave a calming, nature-inspired escape indoors.', 38, 55.00, 0),
(43, 'Lirien Reed Diffuser', 7, 'Step into a sun-kissed orchard every time you inhale the vibrant scent of Lirien. Bursting with zesty orange and gentle spice, this diffuser fills your home with a sense of warmth and optimism. The cheerful citrus top notes gradually settle into a cozy embrace, creating an inviting, uplifting environment that makes any space feel like a personal sanctuary.', 39, 60.00, 0),
(44, 'Duskhaven Reed Diffuser', 7, 'Capture the magic of evening’s gentle hush with Duskhaven. This fragrance evokes a serene twilight garden, blending velvety florals and a whisper of comforting musk. The result is a soothing, sophisticated aroma that helps you unwind after a long day. Whether placed in a bedroom or living area, Duskhaven transforms your surroundings into a tranquil, restorative haven.', 40, 50.00, 1),
(45, 'Tresor Lush Reed Diffuser', 7, 'Indulge in the luxurious allure of Tresor Lush. A fusion of opulent blooms and juicy red fruits, this diffuser exudes a playful yet elegant charm. The fragrance unfolds gently—first with a sweet, fruity bouquet, then settling into lush floral undertones that envelop the senses in a lingering, romantic warmth. Perfect for those seeking a touch of glamour and indulgence in their daily routine.', 41, 65.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_notes`
--

CREATE TABLE `product_notes` (
  `Product_Note_ID` int NOT NULL,
  `Product_ID` int NOT NULL,
  `Note_ID` int NOT NULL,
  `Note_Type` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_notes`
--

INSERT INTO `product_notes` (`Product_Note_ID`, `Product_ID`, `Note_ID`, `Note_Type`) VALUES
(1, 3, 1, 'Top Note'),
(2, 3, 2, 'Heart Note'),
(3, 3, 3, 'Base Note'),
(4, 4, 4, 'Top Note'),
(5, 4, 5, 'Heart Note'),
(6, 4, 6, 'Base Note'),
(7, 5, 7, 'Top Note'),
(8, 5, 8, 'Heart Note'),
(9, 5, 9, 'Base Note'),
(10, 6, 10, 'Top Note'),
(11, 6, 4, 'Heart Note'),
(12, 6, 9, 'Base Note'),
(13, 7, 11, 'Top Note'),
(14, 7, 12, 'Heart Note'),
(15, 7, 13, 'Base Note'),
(16, 8, 14, 'Top Note'),
(17, 8, 15, 'Heart Note'),
(18, 8, 22, 'Base Note'),
(19, 9, 17, 'Top Note'),
(20, 9, 23, 'Heart Note'),
(21, 9, 16, 'Base Note'),
(22, 10, 14, 'Top Note'),
(23, 10, 18, 'Heart Note'),
(24, 10, 6, 'Base Note'),
(25, 11, 19, 'Top Note'),
(26, 11, 20, 'Heart Note'),
(27, 11, 21, 'Base Note'),
(28, 12, 1, 'Top Note'),
(29, 12, 2, 'Heart Note'),
(30, 12, 3, 'Base Note'),
(31, 13, 4, 'Top Note'),
(32, 13, 5, 'Heart Note'),
(33, 13, 6, 'Base Note'),
(34, 14, 7, 'Top Note'),
(35, 14, 8, 'Heart Note'),
(36, 14, 9, 'Base Note'),
(37, 15, 10, 'Top Note'),
(38, 15, 4, 'Heart Note'),
(39, 15, 9, 'Base Note'),
(40, 16, 11, 'Top Note'),
(41, 16, 12, 'Heart Note'),
(42, 16, 13, 'Base Note'),
(43, 17, 14, 'Top Note'),
(44, 17, 15, 'Heart Note'),
(45, 17, 22, 'Base Note'),
(46, 18, 17, 'Top Note'),
(47, 18, 23, 'Heart Note'),
(48, 18, 16, 'Base Note'),
(49, 19, 14, 'Top Note'),
(50, 19, 18, 'Heart Note'),
(51, 19, 6, 'Base Note'),
(52, 20, 19, 'Top Note'),
(53, 20, 20, 'Heart Note'),
(54, 20, 21, 'Base Note'),
(55, 41, 7, 'Top Note'),
(56, 41, 8, 'Base Note'),
(57, 41, 9, 'Heart Note'),
(58, 42, 11, 'Top Note'),
(59, 42, 12, 'Base Note'),
(60, 42, 13, 'Heart Note'),
(61, 43, 17, 'Top Note'),
(62, 43, 23, 'Base Note'),
(63, 43, 16, 'Heart Note'),
(64, 44, 14, 'Top Note'),
(65, 44, 15, 'Base Note'),
(66, 44, 22, 'Heart Note'),
(67, 45, 4, 'Top Note'),
(68, 45, 5, 'Base Note'),
(69, 45, 6, 'Heart Note');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `Review_ID` int NOT NULL,
  `Product_ID` int NOT NULL,
  `User_ID` int NOT NULL,
  `Rating` int NOT NULL,
  `Review_Text` text COLLATE utf8mb4_general_ci
) ;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`Review_ID`, `Product_ID`, `User_ID`, `Rating`, `Review_Text`) VALUES
(1, 3, 1, 4, 'very good, fantastic product.'),
(2, 5, 2, 5, 'it lasts 12 hours on my skin, amazing stuff '),
(3, 5, 4, 4, 'Its very good perfume.'),
(15, 5, 11, 5, 'amazing stuff , I loved it'),
(18, 7, 12, 5, 'loved it'),
(23, 14, 10, 5, 'It was amazing'),
(28, 14, 18, 5, 'Very good'),
(29, 17, 17, 4, 'Lasts very long and smell nice'),
(30, 41, 17, 5, 'i LIKE IT'),
(31, 11, 6, 5, 'very nice product . ');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `User_ID` int NOT NULL,
  `First_Name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Last_Name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `Email_ID` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `Contact_NO` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activation` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  `registration_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `Aura_Points` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`User_ID`, `First_Name`, `Last_Name`, `Email_ID`, `Password`, `Contact_NO`, `activation`, `is_admin`, `registration_date`, `Aura_Points`) VALUES
(1, 'Example1', 'Example1', 'example1@gmail.com', 'Example@123', '7767666565', NULL, 0, '2025-03-04 14:43:08', 0),
(2, 'Ayan', 'Khan', 'leonautx@gmail.com', 'aura1234', '1234567890', NULL, 0, '2025-03-04 14:43:08', 0),
(3, 'Ayan', 'Khan', 'ayankhan962003@gmail.com', '$2y$10$NmK0wJ4zSV2In9HIo1ceiOZXdiLCt8x3WkuGEgdvfwMDqZzVQ0GdS', '07832966046', NULL, 0, '2025-03-04 14:43:08', 0),
(4, 'Dhruhil', 'Gajera', 'dhruhilgajera20@gmail.com', '$2y$10$alPrpSyt0QmOjf8gyVyJJuYWCxTe89kura1gOM2zTX3nPkhpT6L4.', '07746564656', NULL, 0, '2025-03-04 14:43:08', 0),
(5, 'example2', '2', 'example2@gmail.com', '$2y$10$HrdWcKFBtMd01zXqxlJRxuiMLCHb3MtL1sVoTInJ6Zz8vuSVL22T.', '07746564656', NULL, 0, '2025-03-04 14:43:08', 0),
(6, 'a', 'k', '220022691@aston.ac.uk', '$2y$10$ArW4Rf8.u..6RfXf/b7Bfefs8N5uSz1q6U82hNozMdtQv46YL5d4K', '07832966026', NULL, 0, '2025-03-04 14:43:08', 610),
(7, 'Ayan', 'Khan', 'ayankhan@gmail.com', '$2y$10$7qFm9Qe2rWACPh2CbIv66.NR4RypSXXg8HVNOiwUSmqw4V5i7ZzTa', '07832966046', NULL, 0, '2025-03-04 14:43:08', 0),
(8, 'Pri', 'Asamoah', 'priscillsasamoah@gmail.com', '$2y$10$2meObY3OnsIwZfpURrj49OMHOjjA1VXvyTjdu9iYsnUQXRaBsRs6i', '07539433143', 'df85829d3db3617ae37e7017c7d82823', 0, '2025-03-04 14:43:08', 0),
(9, 'vangelis', 'R', 'vangelis@gmail.com', '$2y$10$2ztuURZoOedQKJdmm0oD6OX4U.3AoyjM1X2JuSXux.g6R7LXjHxfi', '1233787888', NULL, 0, '2025-03-04 14:43:08', 0),
(10, 'DD', 'GG', 'DD@gmail.com', '$2y$10$1Yd4Xcum5t.s5u.tzizcK.MjoygtzaV4p6CC77MXRlDhT1u021sTK', '+4407741897935', NULL, 0, '2025-03-04 14:43:08', 1295),
(11, 'Radhwan', 'Imad', '230169306@aston.ac.uk', '$2y$10$bZViHXpYVJ1AVsd58ek9keKr7zUWaeRGRYI7f44zaEvEeaye2JX0u', '07592 921481', NULL, 0, '2025-03-04 14:43:08', 0),
(12, 'Ayan', 'k', '220022691@aston.ac.uk1', '$2y$10$pIkRbu342rSG6g7ehyk9YuqXN23kgCY478urx7PQ.K12pLw/IK.Ji', '07832966046', NULL, 0, '2025-03-04 14:43:08', 260),
(13, 'lemillion', 'r', 'Leonautx1@gmail.com', '$2y$10$RlaFMOjbfAmcNNIh/r04JeUx0uXEhWDHcBQz8Qdnhja8iarMn/v0e', '07832966046', NULL, 0, '2025-03-04 14:43:08', 1710),
(14, 'Admin', 'User', 'admin@example.com', 'admin123', NULL, NULL, 1, '2025-03-04 14:43:08', 0),
(15, 'Abc', 'Abc', 'a123@gmail.com', '$2y$10$.G3r28.1fZqnNgHT9G9us.YiwX1W9EzHnpGMYUkmB91.6ARY4BFPS', '1234123123', NULL, 0, '2025-03-04 14:43:08', 0),
(16, '6', '66', 'test@gmail.com', '$2y$10$0Q0brwdjYgWtqqYW93N/nOtHN6QfmiK.67VBNe5.C2pd6AwqwYN5.', '1234123123', NULL, 0, '2025-03-04 14:43:08', 0),
(17, 'Leo', 'k', 'test1@gmail.com', '$2y$10$on0wRrySfkiWr7BsYazsSuO9o/d74fUz0VCqlgvFj.CzUvIQpmDd2', '07832966046', NULL, 0, '2025-03-10 01:56:38', 1700),
(18, 'Dhruhil', 'Gajera', '240108542@aston.ac.uk', '$2y$10$JTqXHdUb13IUK3pFFkg0dutTypra6DCipDgRCf46vEjXfhCk4X6Ri', '8849641044', NULL, 0, '2025-03-16 21:52:59', 1000),
(19, 'Priscilla', 'p', '230168011@aston.ac.uk', '$2y$10$3nYeewC3As8QLjPQSc8gi.BOob1zL88gfxk299is52WQbYexvicyK', '07539433143', NULL, 0, '2025-03-22 16:52:51', 0),
(20, 'Ali', 'Seleim', 'alikhaledpolo@gmail.com', '$2y$10$5Hg2wMqcg8rGcjbCsvBS1Ow9fMk8iLuUpWPRRvvoe8lLQXGrpBYl2', '+077777777', NULL, 0, '2025-03-23 19:37:11', 0),
(21, 'kHAN', 'AYAN', 'Leonautx@gmail.com22', '$2y$10$hrLRZD/.iTGqSSgqgsYWAO7K7uwuGR5HOgYD3/NNJ09LBL6BjVilW', '07832966046', NULL, 0, '2025-03-24 01:02:17', 720);

-- --------------------------------------------------------

--
-- Table structure for table `user_address`
--

CREATE TABLE `user_address` (
  `User_Address_ID` int NOT NULL,
  `User_ID` int NOT NULL,
  `Address_ID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_address`
--

INSERT INTO `user_address` (`User_Address_ID`, `User_ID`, `Address_ID`) VALUES
(1, 10, 2),
(2, 10, 3),
(3, 10, 4),
(4, 18, 5),
(5, 19, 6),
(6, 12, 7),
(7, 17, 8),
(8, 13, 9),
(9, 21, 10),
(10, 6, 11);

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `Voucher_ID` int NOT NULL,
  `Voucher_Code` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `Discount_Amount` decimal(10,2) NOT NULL,
  `Is_Active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`Voucher_ID`, `Voucher_Code`, `Discount_Amount`, `Is_Active`) VALUES
(1, 'AU5OFF', 5.00, 1),
(2, 'AU10OFF', 10.00, 1),
(3, 'AU15OFF', 15.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `Wishlist_ID` int NOT NULL,
  `User_ID` int NOT NULL,
  `Product_ID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`Wishlist_ID`, `User_ID`, `Product_ID`) VALUES
(39, 6, 14),
(41, 6, 17),
(42, 18, 14),
(43, 20, 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`Address_ID`),
  ADD KEY `Postcode` (`Postcode`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`Admin_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`Cart_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`Cart_Item_ID`),
  ADD KEY `Cart_ID` (`Cart_ID`),
  ADD KEY `Product_ID` (`Product_ID`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`Category_ID`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`City_ID`);

--
-- Indexes for table `county`
--
ALTER TABLE `county`
  ADD PRIMARY KEY (`County_ID`),
  ADD KEY `fk_cityID` (`City_ID`);

--
-- Indexes for table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`Image_ID`),
  ADD KEY `Product_ID` (`Product_ID`);

--
-- Indexes for table `notes_library`
--
ALTER TABLE `notes_library`
  ADD PRIMARY KEY (`Note_ID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`Order_ID`),
  ADD KEY `User_ID` (`User_ID`),
  ADD KEY `Cart_ID` (`Cart_ID`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`Order_Items_ID`),
  ADD KEY `Order_ID` (`Order_ID`),
  ADD KEY `Product_ID` (`Product_ID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`Payment_ID`),
  ADD KEY `Order_ID` (`Order_ID`);

--
-- Indexes for table `postcode`
--
ALTER TABLE `postcode`
  ADD PRIMARY KEY (`Postcode`),
  ADD KEY `City_ID` (`City_ID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`Product_ID`),
  ADD KEY `Category_ID` (`Category_ID`);

--
-- Indexes for table `product_notes`
--
ALTER TABLE `product_notes`
  ADD PRIMARY KEY (`Product_Note_ID`),
  ADD KEY `Product_ID` (`Product_ID`),
  ADD KEY `Note_ID` (`Note_ID`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`Review_ID`),
  ADD KEY `Product_ID` (`Product_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`User_ID`);

--
-- Indexes for table `user_address`
--
ALTER TABLE `user_address`
  ADD PRIMARY KEY (`User_Address_ID`),
  ADD KEY `User_ID` (`User_ID`),
  ADD KEY `Address_ID` (`Address_ID`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`Voucher_ID`),
  ADD UNIQUE KEY `Voucher_Code` (`Voucher_Code`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`Wishlist_ID`),
  ADD UNIQUE KEY `user_product` (`User_ID`,`Product_ID`),
  ADD KEY `Product_ID` (`Product_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `Address_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `Admin_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `Cart_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `Cart_Item_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `Category_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `City_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `county`
--
ALTER TABLE `county`
  MODIFY `County_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `Image_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `notes_library`
--
ALTER TABLE `notes_library`
  MODIFY `Note_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `Order_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `Order_Items_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `Payment_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `Product_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `product_notes`
--
ALTER TABLE `product_notes`
  MODIFY `Product_Note_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `Review_ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `User_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `user_address`
--
ALTER TABLE `user_address`
  MODIFY `User_Address_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `Voucher_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `Wishlist_ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`Postcode`) REFERENCES `postcode` (`Postcode`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`Cart_ID`) REFERENCES `cart` (`Cart_ID`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`);

--
-- Constraints for table `county`
--
ALTER TABLE `county`
  ADD CONSTRAINT `fk_cityID` FOREIGN KEY (`City_ID`) REFERENCES `city` (`City_ID`);

--
-- Constraints for table `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `image_ibfk_1` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`Cart_ID`) REFERENCES `cart` (`Cart_ID`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`),
  ADD CONSTRAINT `order_items_order_fk` FOREIGN KEY (`Order_ID`) REFERENCES `orders` (`Order_ID`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`Order_ID`) REFERENCES `orders` (`Order_ID`);

--
-- Constraints for table `postcode`
--
ALTER TABLE `postcode`
  ADD CONSTRAINT `postcode_ibfk_1` FOREIGN KEY (`City_ID`) REFERENCES `city` (`City_ID`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`Category_ID`) REFERENCES `category` (`Category_ID`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`),
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`Product_ID`) REFERENCES `product` (`Product_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
