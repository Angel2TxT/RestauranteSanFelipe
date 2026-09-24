-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: restaurantSF
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `restaurantSF`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `restaurantSF` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `restaurantSF`;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (25,'Desayunos','sun','images/categories/cat-desayunos.jpg','2026-09-25 04:36:29','2026-09-25 04:36:29'),(26,'Comidas','dinner','images/categories/cat-comidas.jpg','2026-09-25 04:36:29','2026-09-25 04:36:29'),(27,'Antojitos','heart','images/categories/cat-antojitos.jpg','2026-09-25 04:36:29','2026-09-25 04:36:29'),(28,'Bebidas','glass','images/categories/cat-bebidas.jpg','2026-09-25 04:36:29','2026-09-25 04:36:29'),(29,'Postres','star','images/categories/cat-postres.jpg','2026-09-25 04:36:29','2026-09-25 04:36:29'),(30,'Especiales','star-empty','images/categories/cat-especiales.jpg','2026-09-25 04:36:29','2026-09-25 04:36:29');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_order`
--

DROP TABLE IF EXISTS `item_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_order` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `item_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned NOT NULL,
  `qty` int unsigned NOT NULL,
  `fecha` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_order_item_id_foreign` (`item_id`),
  KEY `item_order_order_id_foreign` (`order_id`),
  CONSTRAINT `item_order_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_order_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_order`
--

LOCK TABLES `item_order` WRITE;
/*!40000 ALTER TABLE `item_order` DISABLE KEYS */;
INSERT INTO `item_order` VALUES (21,21,9,1,'2026-09-24',NULL,NULL),(22,22,9,1,'2026-09-24',NULL,NULL),(23,23,9,1,'2026-09-24',NULL,NULL),(24,24,10,1,'2026-09-24',NULL,NULL),(25,25,10,1,'2026-09-24',NULL,NULL),(26,26,10,1,'2026-09-24',NULL,NULL),(27,27,11,1,'2026-09-24',NULL,NULL),(28,28,11,1,'2026-09-24',NULL,NULL),(29,29,12,1,'2026-09-24',NULL,NULL),(30,30,12,1,'2026-09-24',NULL,NULL);
/*!40000 ALTER TABLE `item_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `qty` int unsigned NOT NULL,
  `fecha` date NOT NULL,
  `product_id` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (21,'Huevos al gusto','images/products/desayuno-huevos.jpg',85.00,1,'2026-09-24',89,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(22,'Tacos al pastor','images/products/tacos-pastor.jpg',70.00,1,'2026-09-24',99,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(23,'Agua de horchata','images/products/agua-horchata.jpg',35.00,1,'2026-09-24',102,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(24,'Mole de pollo','images/products/mole-pollo.jpg',145.00,1,'2026-09-24',93,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(25,'Quesadillas','images/products/quesadillas.jpg',55.00,1,'2026-09-24',100,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(26,'Café americano','images/products/cafe-americano.jpg',30.00,1,'2026-09-24',104,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(27,'Milanesa de res','images/products/milanesa-res.jpg',155.00,1,'2026-09-24',94,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(28,'Jugo de naranja','images/products/jugo-naranja.jpg',40.00,1,'2026-09-24',103,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(29,'Carne asada','images/products/carne-asada.jpg',180.00,1,'2026-09-24',95,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(30,'Helado de vainilla','images/products/helado-vainilla.jpg',40.00,1,'2026-09-24',107,'2026-09-25 04:36:29','2026-09-25 04:36:29');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_06_10_021934_add_image_to_users_table',1),(5,'2024_06_10_200633_create_sliders_table',1),(6,'2024_06_17_002055_create_categories_table',1),(7,'2024_06_18_175048_create_products_table',1),(8,'2024_06_30_031910_create_orders_table',1),(9,'2024_06_30_160452_add_fields_to_users_table',1),(10,'2024_06_30_185513_create_items_table',1),(11,'2024_06_30_190305_create_item_order_table',1),(12,'2024_07_10_235336_add_admin_to_users',1),(13,'2025_02_22_071537_add_status_to_orders_table',1),(14,'2025_02_23_052952_add_type_to_orders_table',1),(15,'2025_02_25_065420_create_tables_table',1),(16,'2025_02_25_081559_add_table_id_to_orders_table',1),(17,'2025_03_02_082654_create_notifications_table',1),(18,'2025_03_15_023006_add_table_rol_to_table',1),(19,'2025_03_18_034348_update_status_enum_in_orders_table',1),(20,'2026_09_24_223500_add_delivery_address_to_orders_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','in_progress','ready_for_delivery','paid','completed','cancelled_by_user','cancelled_by_store') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `order_type` enum('dine_in','delivery','pickup') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'delivery',
  `delivery_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `table_id` bigint unsigned DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` date NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (9,190.00,'pending','delivery','Calle Reforma 45, Morelia',NULL,'Pedido de demostración','2026-09-24',2,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(10,230.00,'in_progress','dine_in',NULL,9,'Pedido de demostración','2026-09-24',2,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(11,195.00,'ready_for_delivery','pickup',NULL,NULL,'Pedido de demostración','2026-09-24',2,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(12,220.00,'completed','dine_in',NULL,10,'Pedido de demostración','2026-09-24',2,'2026-09-25 04:36:29','2026-09-25 04:36:29');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (89,'Huevos al gusto','Huevos estrellados o revueltos con frijoles y tortillas.','Popular',85.00,'images/products/desayuno-huevos.jpg',25,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(90,'Huevos con chorizo','Huevos revueltos con chorizo artesanal y salsa verde.','Casero',95.00,'images/products/huevoconchorizo.jpg',25,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(91,'Huevos con jamón','Clásico desayuno con jamón, frijoles y pan tostado.',NULL,90.00,'images/products/huevosconjamon.png',25,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(92,'Chilaquiles rojos','Totopos bañados en salsa roja con crema y queso.','Nuevo',110.00,'images/products/chilaquiles.jpg',25,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(93,'Mole de pollo','Pollo bañado en mole casero con arroz y frijoles.','Tradicional',145.00,'images/products/mole-pollo.jpg',26,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(94,'Milanesa de res','Milanesa empanizada con papas fritas y ensalada.','Popular',155.00,'images/products/milanesa-res.jpg',26,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(95,'Carne asada','Arrachera a la parrilla con guacamole y tortillas.','Chef',180.00,'images/products/carne-asada.jpg',26,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(96,'Enchiladas verdes','Tortillas rellenas de pollo con salsa verde y queso.',NULL,125.00,'images/products/enchiladas.jpg',26,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(97,'Sopa azteca','Caldo de jitomate con tiras de tortilla, aguacate y queso.',NULL,75.00,'images/products/sopa-azteca.jpg',26,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(98,'Ensalada fresca','Mix de hojas verdes, vegetales y aderezo de la casa.','Ligero',95.00,'images/products/ensalada.jpg',26,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(99,'Tacos al pastor','Orden de 4 tacos con piña, cebolla y cilantro.','Top',70.00,'images/products/tacos-pastor.jpg',27,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(100,'Quesadillas','Tortillas de maíz con queso y guisado a elegir.',NULL,55.00,'images/products/quesadillas.jpg',27,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(101,'Guacamole','Aguacate fresco con totopos y pico de gallo.',NULL,65.00,'images/products/guacamole.jpg',27,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(102,'Agua de horchata','Refrescante agua de horchata natural (500 ml).',NULL,35.00,'images/products/agua-horchata.jpg',28,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(103,'Jugo de naranja','Jugo natural recién exprimido.','Natural',40.00,'images/products/jugo-naranja.jpg',28,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(104,'Café americano','Café de grano tostado, taza grande.',NULL,30.00,'images/products/cafe-americano.jpg',28,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(105,'Refresco','Refresco embotellado (355 ml).',NULL,28.00,'images/products/refresco.jpg',28,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(106,'Flan napolitano','Flan casero con caramelo.','Casero',45.00,'images/products/flan.jpg',29,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(107,'Helado de vainilla','Dos bolas de helado cremoso de vainilla.',NULL,40.00,'images/products/helado-vainilla.jpg',29,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(108,'Pastel de chocolate','Rebanada de pastel húmedo de chocolate.','Dulce',55.00,'images/products/pastel-chocolate.jpg',29,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(109,'Hamburguesa San Felipe','Carne 180g, queso, vegetales y papas fritas.','Especial',140.00,'images/products/hamburguesa.jpg',30,'2026-09-25 04:36:29','2026-09-25 04:36:29'),(110,'Pizza familiar','Pizza mediana de pepperoni o hawaiana.','Para compartir',190.00,'images/products/pizza.jpg',30,'2026-09-25 04:36:29','2026-09-25 04:36:29');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('67IQ0Hkpp9o1hhFujMNLbaYyMwLwNQe84dlWUDUo',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiM3ZnMXo0NHRobUpVU3hLSG1VOVlGdHNJb1VLTk5ZbExIelZIcDRQSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wcm9maWxlL2VkaXQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YToxOntzOjg6ImludGVuZGVkIjtzOjIxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1790289363),('8aAPfDmpEyrQDrMGNqJ1xbleEOWXXLAEY94h1jmt',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.21.18 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQmw2UWoyUUJmMGtHbTJuMnlaazRydWdHcE1Ka0tZbmlETG1ob2FMcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790288610),('AG7P1w6S3dkY029QSZkBHAwjajZgD1oWVLHv7aYe',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.21.18 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWEoyaUM4OU5sdEtvQVlaWlV0dUNUdEpxamtuSWlpaXdNS25RQVFNaiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790288612),('BCpHouIfw8XKdxiPfB3jHIKVqMunGNAUPiwaUCzL',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.21.18 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMHhyYUVLMnVsVkJWWG5BY0x1QWZkQUFBTmxCZVJ1Uk1wSFRlV0FTbiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790288611),('dxY7ze5DmhovddSwc1Zy9S979SYWFffMmYaVTK6d',1,'127.0.0.1','Symfony','YTo0OntzOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NjoiX3Rva2VuIjtzOjQwOiJwU3o1dk85cXF4NVBVdUlCc25zUGdYMjBrM0t5Sk1sSTZ0T3pSZVFYIjtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyOToiaHR0cDovL2xvY2FsaG9zdC9hZG1pbi9vcmRlcnMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1790289189),('ekLOfw84zTYOOTS3456vCxYWkdAF75YHS5YGjeH5',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.21.18 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiY3hFYlpGb0NGWlNmT0Nkd3U1T2hLOEVMakR5b1JXdEpLd2tOTnFicSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790288605),('J34UvVpFiQXvzYwF1k8MEiuBlawvemFPcvtlRsXQ',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.21.18 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSk45U3cyVHoyTnFXTWxsSTNNQW94NUk2SlZsMlVTZXo3WmZ4ckJOcSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790288608),('juN9KAwn7R0xeCtM7r894h9N7ONI5LnqR67X3NgO',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.21.18 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSlRjZ3c2ZHZqaUdBbGk4UFB3SE93QnZYU3I1UmJZY291NFZwcFNheSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790288606),('LJEaMXUWeSj4rxgG836AjsFKWNxJyFnAXfojEWcN',1,'127.0.0.1','Symfony','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRDd6dTNpMWdZT3ZBdnRnem9obE9tcmRxbFZYVUFNdDhjdjZPbzFQRSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly9sb2NhbGhvc3QvYWRtaW4vb3JkZXJzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1790289413),('O1P9oMrGGzmB9b4glXqC6onjE7nhJvsCcL25b1o0',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.21.18 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoib3M5Y1RNR0lMa0NHZHpkS0R0N281QXhqcFZQbDJCcmN6S3EzM2FvOCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790288608),('r9w2YyaAJcYSKsY1vXMzk6k0rjUtvBBnc4iUp2Ck',NULL,'127.0.0.1','Symfony','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRDd6dTNpMWdZT3ZBdnRnem9obE9tcmRxbFZYVUFNdDhjdjZPbzFQRSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MTY6Imh0dHA6Ly9sb2NhbGhvc3QiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1790289412),('rdL9ULMK6BgUGv0djfbkFpy5dlODC9I8hu09GVww',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; es-MX) WindowsPowerShell/5.1.26100.9549','YTozOntzOjY6Il90b2tlbiI7czo0MDoiRXNFbkF5eTc1amVTSHI0QUlRYTF6MklON1QxV2VDVEVDQ3gzSUUyOSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790288605),('T6WRwe4w0DEKJ6OkzA7JrZG4StP3knYSqp3G6YPc',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.21.18 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVXN3MlRwT3dGb2J0MHlodUxHa25kRFNvUXR4bkJpQ1A4bm11cGtiSCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790288613),('U6sP7xLYo3dEdNgplPy7U71Mpo2ske59PvT3lVaK',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; es-MX) WindowsPowerShell/5.1.26100.9549','YTozOntzOjY6Il90b2tlbiI7czo0MDoiMHFXWTJReWpsdlo3dENhbllVektlemFxcU9ERHVDWjY0S0RTWFNYMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790288874),('v40rtM9jMfrEjW4kh4OZ4b1WgxXpQnMpKhOCXHw0',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.21.18 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVXNUZHV1OUN4bjJ5ZEFKVWJ5QUxMMGdrcWlCZ21NUTBKS0VhZ0hYUiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790288610),('W4LHPYe3r2XtstgZKZfdwWivjIFrLzIotxvACYSb',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Cursor/3.21.18 Chrome/148.0.7778.280 Electron/42.10.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiM1NIVGNTd24xdDNibTRWUWRQeUt6azRvbzV5VHlDeFVtUmg2b1A5ZiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1790288604);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sliders`
--

DROP TABLE IF EXISTS `sliders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sliders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sliders`
--

LOCK TABLES `sliders` WRITE;
/*!40000 ALTER TABLE `sliders` DISABLE KEYS */;
INSERT INTO `sliders` VALUES (13,'Bienvenido a San Felipe','Sabores tradicionales michoacanos en un ambiente familiar.','/shop','Ver menú','images/sliders/slider-bienvenida.jpg','2026-09-25 04:36:29','2026-09-25 04:36:29'),(14,'Nuestro menú del día','Platillos caseros preparados con ingredientes frescos.','/shop','Ordenar ahora','images/sliders/slider-menu.jpg','2026-09-25 04:36:29','2026-09-25 04:36:29'),(15,'Ideal para compartir','Ven con tu familia y disfruta de una comida inolvidable.','/shop','Explorar','images/sliders/slider-familia.jpg','2026-09-25 04:36:29','2026-09-25 04:36:29');
/*!40000 ALTER TABLE `sliders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tables`
--

DROP TABLE IF EXISTS `tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('available','occupied') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tables_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tables`
--

LOCK TABLES `tables` WRITE;
/*!40000 ALTER TABLE `tables` DISABLE KEYS */;
INSERT INTO `tables` VALUES (1,'Mesa 1','available','2026-09-25 04:36:29','2026-09-25 04:36:29'),(2,'Mesa 2','available','2026-09-25 04:36:29','2026-09-25 04:36:29'),(3,'Mesa 3','available','2026-09-25 04:36:29','2026-09-25 04:36:29'),(4,'Mesa 4','available','2026-09-25 04:36:29','2026-09-25 04:36:29'),(5,'Mesa 5','available','2026-09-25 04:36:29','2026-09-25 04:36:29'),(6,'Mesa 6','available','2026-09-25 04:36:29','2026-09-25 04:36:29'),(7,'Mesa 7','available','2026-09-25 04:36:29','2026-09-25 04:36:29'),(8,'Mesa 8','available','2026-09-25 04:36:29','2026-09-25 04:36:29'),(9,'Mesa 9','occupied','2026-09-25 04:36:29','2026-09-25 04:36:29'),(10,'Mesa 10','occupied','2026-09-25 04:36:29','2026-09-25 04:36:29');
/*!40000 ALTER TABLE `tables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` tinyint(1) NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@sanfelipe.test',1,'2026-09-25 04:36:28','$2y$12$z3RHmuspbs/4ttf7Xaag0e5vbQyN6cl63whdUd7JXaTwjMu/fADpK',NULL,'2026-09-25 04:23:12','2026-09-25 04:36:28','images/users/avatar-admin.jpg','San Felipe','4431000001','Av. Principal 100, Morelia'),(2,'María','cliente@sanfelipe.test',0,'2026-09-25 04:36:29','$2y$12$yZI490z1Af8HbdzEfd4Rzupb0shXwSaPokk1/JXeWLf9c9VRfcgvu',NULL,'2026-09-25 04:23:12','2026-09-25 04:36:29','images/users/avatar-cliente.jpg','García','4431000002','Calle Reforma 45, Morelia'),(3,'Carlos','empleado@sanfelipe.test',2,'2026-09-25 04:36:29','$2y$12$83cimmx/DFNg7X8tKEA4V.zFGqH6q4b1fththJslgsTq7KhLFykyy',NULL,'2026-09-25 04:27:01','2026-09-25 04:36:29','images/users/avatar-empleado.jpg','López','4431000003','Col. Centro, Morelia'),(4,'Luis','repartidor@sanfelipe.test',3,'2026-09-25 04:36:29','$2y$12$c/K0rMwV3WvO7CQweAfzX.oD5z9yLcs/SRAfUI57kIyIPJ19dG2k2',NULL,'2026-09-25 04:27:02','2026-09-25 04:36:29','images/users/avatar-empleado.jpg','Hernández','4431000004','Col. Industrial, Morelia');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'restaurantSF'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-24 16:36:53
