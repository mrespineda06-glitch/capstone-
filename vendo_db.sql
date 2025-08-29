/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.2-MariaDB, for Android (aarch64)
--
-- Host: localhost    Database: vendo_db
-- ------------------------------------------------------
-- Server version	11.8.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) DEFAULT NULL,
  `sender_name` varchar(100) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `messages` VALUES
(1,6,'Dadulla',1,'Hello!',0,'2025-07-26 10:24:57','images/6884acf9bad82_IMG_20250714_105823.jpg'),
(2,6,'Dadulla',1,'',0,'2025-07-26 10:25:17','images/6884ad0d873a8_IMG_20250714_131142.jpg'),
(3,1,NULL,6,'👋',0,'2025-07-26 10:25:57',NULL),
(4,1,NULL,6,'',0,'2025-07-26 10:26:16',NULL),
(5,6,'Dadulla',1,'How\'s life?',0,'2025-07-26 10:34:47',NULL),
(6,1,NULL,6,'Boring',0,'2025-07-27 10:11:51',NULL),
(7,1,NULL,6,'',0,'2025-07-27 10:12:05','images/img_6885fb75967351.48614033.jpg'),
(8,6,'Dadulla',1,'',0,'2025-07-27 10:12:44','images/6885fb9c11d09_Rebisco-Crackers-with-Honey-Butter-10_s.png'),
(9,6,'Dadulla',1,'Good morning!',0,'2025-07-27 10:56:50',NULL),
(10,11,NULL,6,'Hello Admin!',0,'2025-07-27 11:17:02',NULL),
(11,6,'Dadulla',11,'Hello, Richelle!',0,'2025-07-27 11:17:39',NULL),
(12,11,NULL,6,'How are you?',0,'2025-07-27 11:22:50',NULL),
(13,6,'Dadulla',11,'I\'m not sure if I am fine 😔',0,'2025-07-27 11:23:30',NULL),
(14,6,'Dadulla',11,'I\'m trying to be fine',0,'2025-07-27 11:23:44',NULL),
(15,11,NULL,6,'You will be fine💗',0,'2025-07-27 11:25:01',NULL),
(16,6,'Dadulla',11,'For sure🥺',0,'2025-07-27 11:27:00',NULL),
(17,6,'Dadulla',11,'',0,'2025-07-28 16:56:06','images/6887aba63244a_Screenshot_20250716-215254.jpg'),
(18,11,NULL,6,'',0,'2025-07-28 17:24:55','images/img_6887b2679d5da7.79458331.jpg'),
(19,13,NULL,6,'Hello Admin!',0,'2025-07-30 12:50:44',NULL),
(20,6,'Dadulla',13,'Hello ailene!',0,'2025-07-30 12:51:18',NULL),
(21,6,'Dadulla',11,'Hello',0,'2025-07-30 14:07:31',NULL),
(22,11,NULL,6,'Hello!',0,'2025-07-30 14:08:33',NULL),
(23,6,'Dadulla',1,'Good morning!',0,'2025-07-30 23:44:09',NULL),
(24,1,NULL,6,'Good morning!',0,'2025-07-30 23:49:43',NULL),
(25,1,'Jessa',1,'Jess@',0,'2025-08-01 12:15:38',NULL),
(26,1,'Jessa',1,'Jjjv',0,'2025-08-01 12:15:43',NULL),
(27,2,NULL,1,'Gbb',0,'2025-08-01 12:17:22',NULL),
(28,2,NULL,1,'Gbb',0,'2025-08-01 12:19:04',NULL),
(29,2,NULL,1,'Gbb',0,'2025-08-01 12:19:07',NULL),
(30,1,NULL,6,'Hello',0,'2025-08-01 12:19:29',NULL);
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_method` enum('GCash','Coins') NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_time` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `orders` VALUES
(1,'Coins',20.00,'2025-07-18 09:01:51'),
(2,'GCash',0.00,'2025-07-18 09:07:09'),
(3,'GCash',20.00,'2025-07-18 09:07:24'),
(4,'GCash',145.00,'2025-07-18 09:11:23');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `products` VALUES
(7,'Sprite','4801981118601_800x.jpg',59.00,3,'available','2025-07-18 14:38:30'),
(8,'Mountain Dew','mdew295.edited_640x_808c1aa5-c1bf-430e-bda6-31d66fe92e4a.jpg',59.00,86,'available','2025-07-18 14:39:44'),
(9,'Summit Water','SUMMIT-2023-350ML_1200x.jpg',69.00,3,'available','2025-07-18 14:40:50'),
(10,'Rebisco Crackers','rebisco-crackers.jpg',10.00,13,'available','2025-07-20 07:43:59'),
(11,'Mang Juan Chicharron','Mang-Juan.png',100.00,3,'available','2025-07-20 15:13:41'),
(15,'Royal','13054 (1).jpg',290.00,0,'available','2025-07-26 15:48:30'),
(16,'Puppy','1675835_800-600x450.jpg',2000000.00,8,'available','2025-07-27 11:02:02');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `restocks`
--

DROP TABLE IF EXISTS `restocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `restocks` (
  `restock_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `quantity_added` int(11) NOT NULL,
  `restock_time` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`restock_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `restocks_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `restocks`
--

LOCK TABLES `restocks` WRITE;
/*!40000 ALTER TABLE `restocks` DISABLE KEYS */;
set autocommit=0;
/*!40000 ALTER TABLE `restocks` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `profile_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `users` VALUES
(1,'Jessa','$2y$12$OF/HnEcg/gJRPPORbu0WOelD2qi0LxLQvEjFRuS22p9IE957.x55W','user','images/1752992059_Screenshot_20250511-154456.jpg'),
(6,'Dadulla','$2y$12$3pCKMNkOYbnv824imfZO6uINHuVhwkjZyHmFjVbPE9flD/.KtNvdW','admin','images/1753418482_1720343711297.jpg'),
(11,'Richelle','$2y$12$ZulajOGsFsoOtt47GAdHfejf.f4.VYoxjdSmDIb4UDFHbleRiWMoS','user','images/1753615240_1698490669504.png'),
(13,'Ailene','$2y$12$oE74XkgcDyvwx2JKhbdr8.oDA3ccHhVwJMSojduANj11.f2m5r9BC','user','images/1753880011_Screenshot_20250129-203224.jpg');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-08-01 20:40:47
