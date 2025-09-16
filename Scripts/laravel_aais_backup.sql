-- MySQL dump 10.13  Distrib 8.0.37, for Win64 (x86_64)
--
-- Host: localhost    Database: aais
-- ------------------------------------------------------
-- Server version	8.0.37

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
-- Table structure for table `academic_sessions`
--

DROP TABLE IF EXISTS `academic_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `academic_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `current_term` enum('First','Second','Third') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `academic_sessions_year_unique` (`year`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `academic_sessions`
--

LOCK TABLES `academic_sessions` WRITE;
/*!40000 ALTER TABLE `academic_sessions` DISABLE KEYS */;
INSERT INTO `academic_sessions` VALUES (1,'2023/2024',1,'Third','2025-09-12 09:58:02','2025-09-15 16:07:06'),(2,'2024/2025',0,'First','2025-09-14 21:25:53','2025-09-15 01:50:25');
/*!40000 ALTER TABLE `academic_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES (1,1,'Logged in','2025-09-12 09:56:22','2025-09-12 09:56:22','2025-09-12 09:56:22'),(2,1,'MFA setup verified','2025-09-12 09:56:53','2025-09-12 09:56:53','2025-09-12 09:56:53'),(3,1,'Dashboard loaded','2025-09-12 09:56:54','2025-09-12 09:56:54','2025-09-12 09:56:54'),(4,1,'Created academic session 2023/2024','2025-09-12 09:58:02','2025-09-12 09:58:02','2025-09-12 09:58:02'),(5,1,'Class created: Creche','2025-09-12 09:58:34','2025-09-12 09:58:34','2025-09-12 09:58:34'),(6,1,'Student registered: 2023/2024/DSVB','2025-09-12 09:59:24','2025-09-12 09:59:24','2025-09-12 09:59:24'),(7,1,'Approved student: 2023/2024/DSVB','2025-09-12 11:49:27','2025-09-12 11:49:27','2025-09-12 11:49:27'),(8,1,'Student registered: AAIS/0559/004','2025-09-12 12:09:02','2025-09-12 12:09:02','2025-09-12 12:09:02'),(9,1,'Approved student: ','2025-09-12 12:10:00','2025-09-12 12:10:00','2025-09-12 12:10:00'),(10,1,'Approved student: AAIS/0559/004','2025-09-12 12:12:00','2025-09-12 12:12:00','2025-09-12 12:12:00'),(11,1,'MFA verified','2025-09-14 18:18:49','2025-09-14 18:18:49','2025-09-14 18:18:49'),(12,1,'Dashboard loaded','2025-09-14 18:18:53','2025-09-14 18:18:53','2025-09-14 18:18:53'),(13,1,'Dashboard loaded','2025-09-14 18:19:36','2025-09-14 18:19:36','2025-09-14 18:19:36'),(14,1,'Student registered: AAIS/0559/005','2025-09-14 19:52:57','2025-09-14 19:52:57','2025-09-14 19:52:57'),(15,1,'Updated academic session 2023/2024','2025-09-14 19:56:39','2025-09-14 19:56:39','2025-09-14 19:56:39'),(16,1,'Updated academic session 2023/2024','2025-09-14 19:56:53','2025-09-14 19:56:53','2025-09-14 19:56:53'),(17,1,'Updated academic session 2023/2024','2025-09-14 20:51:36','2025-09-14 20:51:36','2025-09-14 20:51:36'),(18,1,'Set system-wide current session to 2023/2024 (Third)','2025-09-14 20:56:35','2025-09-14 20:56:35','2025-09-14 20:56:35'),(19,1,'Updated academic session 2023/2024','2025-09-14 21:02:10','2025-09-14 21:02:10','2025-09-14 21:02:10'),(20,1,'Set system-wide current session to 2023/2024 (First)','2025-09-14 21:07:55','2025-09-14 21:07:55','2025-09-14 21:07:55'),(21,1,'Created academic session 2024/2025','2025-09-14 21:25:53','2025-09-14 21:25:53','2025-09-14 21:25:53'),(22,1,'Set system-wide current session to 2024/2025 (First)','2025-09-14 21:26:18','2025-09-14 21:26:18','2025-09-14 21:26:18'),(23,1,'Set system-wide current session to 2023/2024 (First)','2025-09-14 21:27:17','2025-09-14 21:27:17','2025-09-14 21:27:17'),(24,1,'Set system-wide current session to 2024/2025 (First)','2025-09-14 22:05:28','2025-09-14 22:05:28','2025-09-14 22:05:28'),(25,1,'Set system-wide current session to 2023/2024 (Third)','2025-09-14 22:18:36','2025-09-14 22:18:36','2025-09-14 22:18:36'),(26,1,'Set system-wide current session to 2023/2024 (Third)','2025-09-14 22:24:50','2025-09-14 22:24:50','2025-09-14 22:24:50'),(27,1,'Set system-wide current session to 2024/2025 (First)','2025-09-14 22:38:30','2025-09-14 22:38:30','2025-09-14 22:38:30'),(28,1,'Set system-wide current session to 2023/2024 (Third)','2025-09-14 22:39:19','2025-09-14 22:39:19','2025-09-14 22:39:19'),(29,1,'Set system-wide current session to 2024/2025 (First)','2025-09-14 23:00:57','2025-09-14 23:00:57','2025-09-14 23:00:57'),(30,1,'Set system-wide current session to 2023/2024 (Third)','2025-09-14 23:01:23','2025-09-14 23:01:23','2025-09-14 23:01:23'),(31,1,'Set system-wide current session to 2023/2024 (Third)','2025-09-14 23:12:09','2025-09-14 23:12:09','2025-09-14 23:12:09'),(32,1,'Student registered: AAIS/0559/006','2025-09-14 23:53:07','2025-09-14 23:53:07','2025-09-14 23:53:07'),(33,1,'Updated student: AAIS/0559/006','2025-09-15 00:35:50','2025-09-15 00:35:50','2025-09-15 00:35:50'),(34,1,'Updated student: AAIS/0559/006','2025-09-15 00:35:52','2025-09-15 00:35:52','2025-09-15 00:35:52'),(35,1,'Updated student: 2023/2024/DSVB','2025-09-15 00:37:05','2025-09-15 00:37:05','2025-09-15 00:37:05'),(36,1,'Class created: Pre-Nursery','2025-09-15 00:38:30','2025-09-15 00:38:30','2025-09-15 00:38:30'),(37,1,'Class created: Nursery 1','2025-09-15 00:38:49','2025-09-15 00:38:49','2025-09-15 00:38:49'),(38,1,'Student registered: AAIS/0559/007','2025-09-15 00:40:51','2025-09-15 00:40:51','2025-09-15 00:40:51'),(39,1,'Student registered: AAIS/0559/008','2025-09-15 00:41:24','2025-09-15 00:41:24','2025-09-15 00:41:24'),(40,1,'Permanently deleted student: 2023/2024/DSVB','2025-09-15 01:20:00','2025-09-15 01:20:00','2025-09-15 01:20:00'),(41,1,'Dashboard loaded','2025-09-15 01:20:30','2025-09-15 01:20:30','2025-09-15 01:20:30'),(42,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:03','2025-09-15 01:25:03','2025-09-15 01:25:03'),(43,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:04','2025-09-15 01:25:04','2025-09-15 01:25:04'),(44,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:05','2025-09-15 01:25:05','2025-09-15 01:25:05'),(45,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:06','2025-09-15 01:25:06','2025-09-15 01:25:06'),(46,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:07','2025-09-15 01:25:07','2025-09-15 01:25:07'),(47,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:08','2025-09-15 01:25:08','2025-09-15 01:25:08'),(48,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:09','2025-09-15 01:25:09','2025-09-15 01:25:09'),(49,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:10','2025-09-15 01:25:10','2025-09-15 01:25:10'),(50,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:10','2025-09-15 01:25:10','2025-09-15 01:25:10'),(51,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:11','2025-09-15 01:25:11','2025-09-15 01:25:11'),(52,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:12','2025-09-15 01:25:12','2025-09-15 01:25:12'),(53,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:12','2025-09-15 01:25:12','2025-09-15 01:25:12'),(54,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:13','2025-09-15 01:25:13','2025-09-15 01:25:13'),(55,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:14','2025-09-15 01:25:14','2025-09-15 01:25:14'),(56,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:15','2025-09-15 01:25:15','2025-09-15 01:25:15'),(57,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:16','2025-09-15 01:25:16','2025-09-15 01:25:16'),(58,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:17','2025-09-15 01:25:17','2025-09-15 01:25:17'),(59,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:18','2025-09-15 01:25:18','2025-09-15 01:25:18'),(60,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:19','2025-09-15 01:25:19','2025-09-15 01:25:19'),(61,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:19','2025-09-15 01:25:19','2025-09-15 01:25:19'),(62,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:21','2025-09-15 01:25:21','2025-09-15 01:25:21'),(63,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:22','2025-09-15 01:25:22','2025-09-15 01:25:22'),(64,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:23','2025-09-15 01:25:23','2025-09-15 01:25:23'),(65,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:24','2025-09-15 01:25:24','2025-09-15 01:25:24'),(66,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:24','2025-09-15 01:25:24','2025-09-15 01:25:24'),(67,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:25','2025-09-15 01:25:25','2025-09-15 01:25:25'),(68,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:26','2025-09-15 01:25:26','2025-09-15 01:25:26'),(69,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:27','2025-09-15 01:25:27','2025-09-15 01:25:27'),(70,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:28','2025-09-15 01:25:28','2025-09-15 01:25:28'),(71,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:30','2025-09-15 01:25:30','2025-09-15 01:25:30'),(72,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:39','2025-09-15 01:25:39','2025-09-15 01:25:39'),(73,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:41','2025-09-15 01:25:41','2025-09-15 01:25:41'),(74,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:42','2025-09-15 01:25:42','2025-09-15 01:25:42'),(75,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:43','2025-09-15 01:25:43','2025-09-15 01:25:43'),(76,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:44','2025-09-15 01:25:44','2025-09-15 01:25:44'),(77,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:45','2025-09-15 01:25:45','2025-09-15 01:25:45'),(78,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:46','2025-09-15 01:25:46','2025-09-15 01:25:46'),(79,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:47','2025-09-15 01:25:47','2025-09-15 01:25:47'),(80,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:47','2025-09-15 01:25:47','2025-09-15 01:25:47'),(81,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:48','2025-09-15 01:25:48','2025-09-15 01:25:48'),(82,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:49','2025-09-15 01:25:49','2025-09-15 01:25:49'),(83,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:50','2025-09-15 01:25:50','2025-09-15 01:25:50'),(84,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:51','2025-09-15 01:25:51','2025-09-15 01:25:51'),(85,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:52','2025-09-15 01:25:52','2025-09-15 01:25:52'),(86,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:52','2025-09-15 01:25:52','2025-09-15 01:25:52'),(87,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:53','2025-09-15 01:25:53','2025-09-15 01:25:53'),(88,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:54','2025-09-15 01:25:54','2025-09-15 01:25:54'),(89,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:54','2025-09-15 01:25:54','2025-09-15 01:25:54'),(90,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:55','2025-09-15 01:25:55','2025-09-15 01:25:55'),(91,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:56','2025-09-15 01:25:56','2025-09-15 01:25:56'),(92,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:57','2025-09-15 01:25:57','2025-09-15 01:25:57'),(93,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:58','2025-09-15 01:25:58','2025-09-15 01:25:58'),(94,1,'Approved student: AAIS/0559/008','2025-09-15 01:25:59','2025-09-15 01:25:59','2025-09-15 01:25:59'),(95,1,'Approved student: AAIS/0559/008','2025-09-15 01:26:00','2025-09-15 01:26:00','2025-09-15 01:26:00'),(96,1,'Approved student: AAIS/0559/008','2025-09-15 01:26:01','2025-09-15 01:26:01','2025-09-15 01:26:01'),(97,1,'Approved student: AAIS/0559/008','2025-09-15 01:26:02','2025-09-15 01:26:02','2025-09-15 01:26:02'),(98,1,'Approved student: AAIS/0559/008','2025-09-15 01:26:02','2025-09-15 01:26:02','2025-09-15 01:26:02'),(99,1,'Approved student: AAIS/0559/008','2025-09-15 01:26:03','2025-09-15 01:26:03','2025-09-15 01:26:03'),(100,1,'Approved student: AAIS/0559/008','2025-09-15 01:26:04','2025-09-15 01:26:04','2025-09-15 01:26:04'),(101,1,'Approved student: AAIS/0559/008','2025-09-15 01:26:04','2025-09-15 01:26:04','2025-09-15 01:26:04'),(102,1,'Approved student: AAIS/0559/008','2025-09-15 01:26:05','2025-09-15 01:26:05','2025-09-15 01:26:05'),(103,1,'Approved student: AAIS/0559/007','2025-09-15 01:26:16','2025-09-15 01:26:16','2025-09-15 01:26:16'),(104,1,'Approved student: AAIS/0559/005','2025-09-15 01:26:23','2025-09-15 01:26:23','2025-09-15 01:26:23'),(105,1,'Approved student: AAIS/0559/006','2025-09-15 01:26:41','2025-09-15 01:26:41','2025-09-15 01:26:41'),(106,1,'Set system-wide current session to 2024/2025 (First)','2025-09-15 01:48:54','2025-09-15 01:48:54','2025-09-15 01:48:54'),(107,1,'Set system-wide current session to 2023/2024 (First)','2025-09-15 01:50:25','2025-09-15 01:50:25','2025-09-15 01:50:25'),(108,1,'Set system-wide current session to 2023/2024 (Second)','2025-09-15 01:52:15','2025-09-15 01:52:15','2025-09-15 01:52:15'),(109,1,'MFA verified','2025-09-15 08:06:55','2025-09-15 08:06:55','2025-09-15 08:06:55'),(110,1,'Dashboard loaded','2025-09-15 08:06:57','2025-09-15 08:06:57','2025-09-15 08:06:57'),(111,1,'Marked student as left: AAIS/0559/007','2025-09-15 08:30:22','2025-09-15 08:30:22','2025-09-15 08:30:22'),(112,1,'Toggled fee status for student AAIS/0559/008','2025-09-15 08:37:10','2025-09-15 08:37:10','2025-09-15 08:37:10'),(113,1,'Toggled fee status for student AAIS/0559/008','2025-09-15 08:37:22','2025-09-15 08:37:22','2025-09-15 08:37:22'),(114,1,'Toggled fee status for student AAIS/0559/006','2025-09-15 08:37:37','2025-09-15 08:37:37','2025-09-15 08:37:37'),(115,1,'Toggled fee status for student AAIS/0559/005','2025-09-15 08:37:44','2025-09-15 08:37:44','2025-09-15 08:37:44'),(116,1,'Permanently deleted student: ','2025-09-15 08:38:57','2025-09-15 08:38:57','2025-09-15 08:38:57'),(117,1,'MFA verified','2025-09-15 14:22:24','2025-09-15 14:22:24','2025-09-15 14:22:24'),(118,1,'Dashboard loaded','2025-09-15 14:22:32','2025-09-15 14:22:32','2025-09-15 14:22:32'),(119,1,'Set system-wide current session to 2023/2024 (First)','2025-09-15 14:55:37','2025-09-15 14:55:37','2025-09-15 14:55:37'),(120,1,'Student registered: AAIS/0559/009','2025-09-15 14:56:21','2025-09-15 14:56:21','2025-09-15 14:56:21'),(121,1,'Set system-wide current session to 2023/2024 (Second)','2025-09-15 14:56:52','2025-09-15 14:56:52','2025-09-15 14:56:52'),(122,1,'Toggled approval status for student AAIS/0559/009','2025-09-15 15:02:07','2025-09-15 15:02:07','2025-09-15 15:02:07'),(123,1,'Marked student as left: AAIS/0559/009','2025-09-15 15:03:17','2025-09-15 15:03:17','2025-09-15 15:03:17'),(124,1,'Student registered: AAIS/0559/010','2025-09-15 15:04:58','2025-09-15 15:04:58','2025-09-15 15:04:58'),(125,1,'Toggled approval status for student AAIS/0559/010','2025-09-15 15:06:29','2025-09-15 15:06:29','2025-09-15 15:06:29'),(126,1,'Toggled fee status for student AAIS/0559/010','2025-09-15 15:06:50','2025-09-15 15:06:50','2025-09-15 15:06:50'),(127,1,'Toggled fee status for student AAIS/0559/008','2025-09-15 15:09:48','2025-09-15 15:09:48','2025-09-15 15:09:48'),(128,1,'Toggled fee status for student AAIS/0559/008','2025-09-15 15:10:03','2025-09-15 15:10:03','2025-09-15 15:10:03'),(129,1,'Set system-wide current session to 2023/2024 (Third)','2025-09-15 16:07:06','2025-09-15 16:07:06','2025-09-15 16:07:06'),(130,1,'Class created: Nursery 2','2025-09-15 17:14:59','2025-09-15 17:14:59','2025-09-15 17:14:59'),(131,1,'Fee status marked as paid for student AAIS/0559/010','2025-09-15 17:15:57','2025-09-15 17:15:57','2025-09-15 17:15:57'),(132,1,'Fee status marked as unpaid for student AAIS/0559/010','2025-09-15 17:16:03','2025-09-15 17:16:03','2025-09-15 17:16:03');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

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
INSERT INTO `cache` VALUES ('aunty-annes-international-school-cache-spatie.permission.cache','a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:7:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:12:\"manage_users\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:15:\"manage_sessions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:14:\"manage_classes\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:14:\"manage_results\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:15:\"manage_teachers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:15:\"manage_subjects\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:12:\"view_reports\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}}s:5:\"roles\";a:3:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:7:\"teacher\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:7:\"student\";s:1:\"c\";s:3:\"web\";}}}',1757987321);
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
-- Table structure for table `class_subject`
--

DROP TABLE IF EXISTS `class_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_subject` (
  `class_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`class_id`,`subject_id`),
  KEY `class_subject_subject_id_foreign` (`subject_id`),
  CONSTRAINT `class_subject_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_subject_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_subject`
--

LOCK TABLES `class_subject` WRITE;
/*!40000 ALTER TABLE `class_subject` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_teacher`
--

DROP TABLE IF EXISTS `class_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_teacher` (
  `class_id` bigint unsigned NOT NULL,
  `teacher_id` bigint unsigned NOT NULL,
  `session_id` bigint unsigned NOT NULL DEFAULT '1',
  `term` enum('First','Second','Third') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_form_teacher` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`class_id`,`teacher_id`,`session_id`,`term`),
  KEY `class_teacher_teacher_id_foreign` (`teacher_id`),
  KEY `class_teacher_session_id_foreign` (`session_id`),
  CONSTRAINT `class_teacher_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_teacher_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `academic_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `class_teacher_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_teacher`
--

LOCK TABLES `class_teacher` WRITE;
/*!40000 ALTER TABLE `class_teacher` DISABLE KEYS */;
/*!40000 ALTER TABLE `class_teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hierarchy` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `classes_name_unique` (`name`),
  UNIQUE KEY `classes_hierarchy_unique` (`hierarchy`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classes`
--

LOCK TABLES `classes` WRITE;
/*!40000 ALTER TABLE `classes` DISABLE KEYS */;
INSERT INTO `classes` VALUES (1,'Creche','Nursery',1,'2025-09-12 09:58:34','2025-09-12 09:58:34'),(2,'Pre-Nursery','Nursery',2,'2025-09-15 00:38:30','2025-09-15 00:38:30'),(3,'Nursery 1','Nursery',3,'2025-09-15 00:38:49','2025-09-15 00:38:49'),(4,'Nursery 2','Nursery',4,'2025-09-15 17:14:59','2025-09-15 17:14:59');
/*!40000 ALTER TABLE `classes` ENABLE KEYS */;
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
-- Table structure for table `fee_payments`
--

DROP TABLE IF EXISTS `fee_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fee_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `session_id` bigint unsigned NOT NULL DEFAULT '1',
  `term` enum('First','Second','Third') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `has_paid_fee` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_payments_student_id_foreign` (`student_id`),
  KEY `fee_payments_session_id_foreign` (`session_id`),
  CONSTRAINT `fee_payments_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `academic_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fee_payments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fee_payments`
--

LOCK TABLES `fee_payments` WRITE;
/*!40000 ALTER TABLE `fee_payments` DISABLE KEYS */;
INSERT INTO `fee_payments` VALUES (1,7,1,'Second',0,'2025-09-15 08:37:10','2025-09-15 15:10:03'),(2,5,1,'Third',1,'2025-09-15 08:37:37','2025-09-15 08:37:37'),(3,4,1,'Third',1,'2025-09-15 08:37:44','2025-09-15 08:37:44'),(4,9,1,'Second',1,'2025-09-15 15:06:50','2025-09-15 15:06:50'),(5,9,1,'Third',0,'2025-09-15 17:15:57','2025-09-15 17:16:03');
/*!40000 ALTER TABLE `fee_payments` ENABLE KEYS */;
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
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `level` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `context` json NOT NULL,
  `file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_09_10_181419_alter_users_table_add_auth_fields',1),(5,'2025_09_10_181500_create_permission_tables',1),(6,'2025_09_10_181725_create_audit_logs_table',1),(7,'2025_09_11_054407_add_notifications_field_to_users_table',1),(8,'2025_09_11_055421_create_classes_table',1),(9,'2025_09_11_055423_create_students_table',1),(10,'2025_09_11_055451_create_subjects_table',1),(11,'2025_09_11_055500_create_teachers_table',1),(12,'2025_09_11_055519_create_academic_sessions_table',1),(13,'2025_09_11_055529_create_results_table',1),(14,'2025_09_11_055530_create_student_class_history_table',1),(15,'2025_09_11_055531_create_fee_payments_table',1),(16,'2025_09_11_055532_create_student_term_summaries_table',1),(17,'2025_09_11_055541_create_notifications_table',1),(18,'2025_09_11_055605_create_logs_table',1),(19,'2025_09_11_055626_create_class_subject_table',1),(20,'2025_09_11_055645_create_teacher_subject_table',1),(21,'2025_09_11_055712_create_class_teacher_table',1),(22,'2025_09_11_140559_create_user_session_preferences_table',1),(23,'2025_09_14_222005_modify_academic_sessions_current_term_nullable',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(3,'App\\Models\\User',2),(3,'App\\Models\\User',3),(3,'App\\Models\\User',4),(3,'App\\Models\\User',5),(3,'App\\Models\\User',6),(3,'App\\Models\\User',7),(3,'App\\Models\\User',8),(3,'App\\Models\\User',9);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_foreign` (`user_id`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
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
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'manage_users','web','2025-09-12 09:47:04','2025-09-12 09:47:04'),(2,'manage_sessions','web','2025-09-12 09:47:04','2025-09-12 09:47:04'),(3,'manage_classes','web','2025-09-12 09:47:05','2025-09-12 09:47:05'),(4,'manage_results','web','2025-09-12 09:47:05','2025-09-12 09:47:05'),(5,'manage_teachers','web','2025-09-12 09:47:06','2025-09-12 09:47:06'),(6,'manage_subjects','web','2025-09-12 09:47:06','2025-09-12 09:47:06'),(7,'view_reports','web','2025-09-12 09:47:07','2025-09-12 09:47:07');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `results`
--

DROP TABLE IF EXISTS `results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned NOT NULL,
  `session_id` bigint unsigned NOT NULL DEFAULT '1',
  `term` enum('First','Second','Third') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Third',
  `class_assessment` int DEFAULT NULL,
  `summative_test` int DEFAULT NULL,
  `exam` int DEFAULT NULL,
  `total` int DEFAULT NULL,
  `grade` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remark` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_result` (`student_id`,`subject_id`,`class_id`,`term`,`session_id`),
  KEY `results_subject_id_foreign` (`subject_id`),
  KEY `results_class_id_foreign` (`class_id`),
  KEY `results_session_id_foreign` (`session_id`),
  CONSTRAINT `results_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `results_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `academic_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `results_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `results_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `results`
--

LOCK TABLES `results` WRITE;
/*!40000 ALTER TABLE `results` DISABLE KEYS */;
/*!40000 ALTER TABLE `results` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(7,2),(7,3);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','web','2025-09-12 09:47:07','2025-09-12 09:47:07'),(2,'teacher','web','2025-09-12 09:47:08','2025-09-12 09:47:08'),(3,'student','web','2025-09-12 09:47:08','2025-09-12 09:47:08');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
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
INSERT INTO `sessions` VALUES ('5f8i5o0CZ6ajMuZODAw6jYPzOwm3TpBVJj4hstaJ',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMFdkc2xkVDhHekZKNW5JQXEwMnJoWGZtemVLTGlNeTZvVnJyelY4SCI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czo3NjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2FkbWluL2NsYXNzZXMvUHJlLU51cnNlcnkvc3R1ZGVudHMvZ2VuZXJhdGVfYnJvYWRzaGVldCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MTI6Im1mYV92ZXJpZmllZCI7YjoxO30=',1757957552);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_class_history`
--

DROP TABLE IF EXISTS `student_class_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_class_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `session_id` bigint unsigned NOT NULL DEFAULT '1',
  `class_id` bigint unsigned DEFAULT NULL,
  `start_term` enum('First','Second','Third') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'First',
  `end_term` enum('First','Second','Third') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `join_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `leave_date` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_class_history_session_id_foreign` (`session_id`),
  KEY `student_class_history_class_id_foreign` (`class_id`),
  KEY `idx_student_session` (`student_id`,`session_id`),
  CONSTRAINT `student_class_history_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_class_history_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `academic_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_class_history_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_class_history`
--

LOCK TABLES `student_class_history` WRITE;
/*!40000 ALTER TABLE `student_class_history` DISABLE KEYS */;
INSERT INTO `student_class_history` VALUES (2,3,1,1,'Third',NULL,'2025-09-12 12:09:02',NULL,1,'2025-09-12 12:09:02','2025-09-12 12:09:02'),(3,4,1,1,'Third',NULL,'2025-09-14 19:52:57',NULL,1,'2025-09-14 19:52:57','2025-09-14 19:52:57'),(4,5,1,1,'Third',NULL,'2025-09-14 23:53:07',NULL,1,'2025-09-14 23:53:07','2025-09-14 23:53:07'),(5,6,1,2,'First','Second','2025-09-15 00:40:51','2025-09-15 08:30:22',1,'2025-09-15 00:40:51','2025-09-15 08:30:22'),(6,7,1,3,'Second',NULL,'2025-09-15 00:41:24',NULL,1,'2025-09-15 00:41:24','2025-09-15 00:41:24'),(7,8,1,2,'First','First','2025-09-15 14:56:21',NULL,1,'2025-09-15 14:56:21','2025-09-15 15:03:17'),(8,8,1,2,'Second','Second','2025-09-15 15:03:17','2025-09-15 15:03:17',0,'2025-09-15 15:03:17','2025-09-15 15:03:17'),(9,9,1,2,'First',NULL,'2025-09-15 15:04:58',NULL,1,'2025-09-15 15:04:58','2025-09-15 15:04:58');
/*!40000 ALTER TABLE `student_class_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_term_summaries`
--

DROP TABLE IF EXISTS `student_term_summaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student_term_summaries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `class_id` bigint unsigned NOT NULL,
  `session_id` bigint unsigned NOT NULL DEFAULT '1',
  `term` enum('First','Second','Third') COLLATE utf8mb4_unicode_ci NOT NULL,
  `grand_total` int DEFAULT NULL,
  `term_average` double DEFAULT NULL,
  `cumulative_average` double DEFAULT NULL,
  `last_term_average` double DEFAULT NULL,
  `subjects_offered` int DEFAULT NULL,
  `position` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal_remark` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `teacher_remark` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `next_term_begins` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_issued` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_term_summary` (`student_id`,`class_id`,`term`,`session_id`),
  KEY `student_term_summaries_class_id_foreign` (`class_id`),
  KEY `student_term_summaries_session_id_foreign` (`session_id`),
  CONSTRAINT `student_term_summaries_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_term_summaries_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `academic_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_term_summaries_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_term_summaries`
--

LOCK TABLES `student_term_summaries` WRITE;
/*!40000 ALTER TABLE `student_term_summaries` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_term_summaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reg_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `parent_name` varchar(70) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_phone_number` text COLLATE utf8mb4_unicode_ci,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_occupation` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state_of_origin` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `local_government_area` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `religion` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `profile_pic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `students_reg_no_unique` (`reg_no`),
  KEY `students_user_id_foreign` (`user_id`),
  CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (3,'AAIS/0559/004','Glory',NULL,'Isaac','female',NULL,NULL,'eyJpdiI6IlRoSy9qQjc2UnM5MU9ibUdpY1hKZWc9PSIsInZhbHVlIjoiYnhkbitYY3V1OXBORXhiUk8ySTJxdz09IiwibWFjIjoiMzRjOWViMTRkZTdmMWM0MmU5YWE3ZGZkNWViYjhkOGM2YTZjZDNjZGQyMTAzOGQ5OWNhMTVhODc4ZDdiNzY1OCIsInRhZyI6IiJ9','No 6 Oomnne Drive',NULL,'Abia',NULL,NULL,'2025-09-12 12:09:00',1,NULL,3,'2025-09-12 12:09:00','2025-09-12 12:12:00'),(4,'AAIS/0559/005','Gospel',NULL,'Isaac','male',NULL,NULL,'eyJpdiI6IlVPTDI1VXIyUUQ0YzZocDNmaWhCQ3c9PSIsInZhbHVlIjoibnQ3SzRGSmNBUlNTak9BNVRiMDlOUT09IiwibWFjIjoiNTA4MGM0ZmZiNzRiMTE3MWQ0ZDUxOGE4NTY4MjdiNWIyN2RkYmJkZmZlNzRkMzlhZDQzMmFhNWYwZmI1NTY1NSIsInRhZyI6IiJ9','No 6 Oomnne Drive',NULL,'Abia',NULL,NULL,'2025-09-14 19:52:55',1,NULL,4,'2025-09-14 19:52:55','2025-09-15 01:26:23'),(5,'AAIS/0559/006','Victoria','Chizitere','Uwadiegwu','female',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-14 23:53:05',1,NULL,5,'2025-09-14 23:53:05','2025-09-15 01:26:41'),(6,'AAIS/0559/007','Gospel',NULL,'Isaac','male',NULL,NULL,'eyJpdiI6IktSSHp0OFhOaEpobE9sUWdBQXFRYXc9PSIsInZhbHVlIjoiU1hpekxPYlRySXIzYXJ2TW1QYUVjUT09IiwibWFjIjoiZTI5MjRhMGE1OWM0ZjNkNzRhZTAxNTJmZjIzNmZjOWZhMWRmNGFjMTZiYTA5NTVjNjhhZDZhMDAzNzliZmFhZCIsInRhZyI6IiJ9','No 6 Oomnne Drive',NULL,'Abia',NULL,NULL,'2025-09-15 00:40:50',1,NULL,6,'2025-09-15 00:40:50','2025-09-15 01:26:16'),(7,'AAIS/0559/008','Gospel',NULL,'Isaac','female',NULL,NULL,'eyJpdiI6InNmU0laeW45Y1REZDNZR0g5dm1BQXc9PSIsInZhbHVlIjoiZi90M0g5b1lOdGVSajhrc21GQUZWQT09IiwibWFjIjoiMTIxYmUyNzZmM2RjZDFhZjNlNTJjN2VmODY4YmY3MjdhMTYzYjI2ZTgxMjdjZGUyZTQwYTMyZTljZjJmMGVjMyIsInRhZyI6IiJ9','No 6 Oomnne Drive',NULL,'Abia',NULL,NULL,'2025-09-15 00:41:23',1,NULL,7,'2025-09-15 00:41:23','2025-09-15 01:25:03'),(8,'AAIS/0559/009','Gospel',NULL,'Isaac','male',NULL,NULL,'eyJpdiI6IloyQ2VKWEROd2Y2OHpSYzB4SC81MUE9PSIsInZhbHVlIjoici9yTXF0TjNoVXpVZy9NSmc1QngwQT09IiwibWFjIjoiZDEwNDU1ZDliNTE3YTIxYTFlM2I4NzBhNGYzOWZjZDE0YmUwM2U4YTA1MTFkNjVmMWVjNjA2NWMyMTMwNjE0MSIsInRhZyI6IiJ9','No 6 Oomnne Drive',NULL,'Abia',NULL,NULL,'2025-09-15 14:56:20',1,NULL,8,'2025-09-15 14:56:20','2025-09-15 15:02:07'),(9,'AAIS/0559/010','Gospel','Okwuchukwu','Isaac','male',NULL,NULL,'eyJpdiI6IklNd1psUW9PNEpoYTJpTDhoQ2xQOFE9PSIsInZhbHVlIjoib1BjQ0lZSlRPbk1DVjN0dXNadWpTdz09IiwibWFjIjoiOWUxMTNjOWYzY2MzNjljNjQwYmE3NDBlYWU2MTc4ZjhlOWMxODY3NTdjYTQ2MDJjODAxNDc5NTRkNjJhOWIzNiIsInRhZyI6IiJ9','No 6 Oomnne Drive',NULL,'Abia',NULL,NULL,'2025-09-15 15:04:57',1,NULL,9,'2025-09-15 15:04:57','2025-09-15 15:06:28');
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subjects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deactivated` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subjects_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subjects`
--

LOCK TABLES `subjects` WRITE;
/*!40000 ALTER TABLE `subjects` DISABLE KEYS */;
/*!40000 ALTER TABLE `subjects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher_subject`
--

DROP TABLE IF EXISTS `teacher_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teacher_subject` (
  `teacher_id` bigint unsigned NOT NULL,
  `subject_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`teacher_id`,`subject_id`),
  KEY `teacher_subject_subject_id_foreign` (`subject_id`),
  CONSTRAINT `teacher_subject_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teacher_subject_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_subject`
--

LOCK TABLES `teacher_subject` WRITE;
/*!40000 ALTER TABLE `teacher_subject` DISABLE KEYS */;
/*!40000 ALTER TABLE `teacher_subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `teachers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `employee_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teachers_employee_id_unique` (`employee_id`),
  UNIQUE KEY `teachers_email_unique` (`email`),
  KEY `teachers_user_id_foreign` (`user_id`),
  CONSTRAINT `teachers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_session_preferences`
--

DROP TABLE IF EXISTS `user_session_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_session_preferences` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `session_id` bigint unsigned NOT NULL DEFAULT '1',
  `current_term` enum('First','Second','Third') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'First',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_session_preferences_user_id_foreign` (`user_id`),
  KEY `user_session_preferences_session_id_foreign` (`session_id`),
  CONSTRAINT `user_session_preferences_session_id_foreign` FOREIGN KEY (`session_id`) REFERENCES `academic_sessions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_session_preferences_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_session_preferences`
--

LOCK TABLES `user_session_preferences` WRITE;
/*!40000 ALTER TABLE `user_session_preferences` DISABLE KEYS */;
INSERT INTO `user_session_preferences` VALUES (19,1,1,'Third','2025-09-15 16:07:06','2025-09-15 16:07:06');
/*!40000 ALTER TABLE `user_session_preferences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identifier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mfa_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notifications` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_identifier_unique` (`identifier`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin',NULL,'admin',NULL,'2025-09-12 09:47:09','$2y$12$bIbfSwHxSkKPJn6og3OP7O6gM6Nftz58u4BBnPpz805JpRJ8ks8r2','ZVAFKCLIJIZN23FR',1,'m7kgFukXLo9DQfcaHv0RK3KhZ7qGJBIHAxm8imrzfi90i2fEfsExPRAX1GlZ','2025-09-12 09:47:09','2025-09-12 09:47:09','active',1),(3,NULL,NULL,'AAIS/0559/004',NULL,NULL,'$2y$12$V2YP/RyfY7RBpwGtr76sKu57taZ9NN375xhlXhC2rOBTvns3Q0Gfm',NULL,1,NULL,'2025-09-12 12:09:01','2025-09-12 12:09:01','active',1),(4,NULL,NULL,'AAIS/0559/005',NULL,NULL,'$2y$12$.TtlWC0/si4EwiYNJ62Oq.c9TqFvItLFnLBss6Ita1qRGjUtUzUHK',NULL,1,NULL,'2025-09-14 19:52:56','2025-09-14 19:52:56','active',1),(5,NULL,NULL,'AAIS/0559/006',NULL,NULL,'$2y$12$KE2wOzhLH7bsmf.tJ8aDPeKmLHCPqoeG4v86iWSseQoy42cl4AFPK',NULL,1,NULL,'2025-09-14 23:53:06','2025-09-14 23:53:06','active',1),(6,NULL,NULL,'AAIS/0559/007',NULL,NULL,'$2y$12$UkU9dksuGVASuCWcw8dqP.u2PD1NQR3FTmKwEas.ZfTOE4Qe9YCya',NULL,1,NULL,'2025-09-15 00:40:51','2025-09-15 00:40:51','active',1),(7,NULL,NULL,'AAIS/0559/008',NULL,NULL,'$2y$12$Kdr5nMaaI8RQdgx.5xRs.O/32V6eezZcTzT1Lvdszj1hqSlfROb3i',NULL,1,NULL,'2025-09-15 00:41:23','2025-09-15 00:41:23','active',1),(8,NULL,NULL,'AAIS/0559/009',NULL,NULL,'$2y$12$J0YbTN9Hw/fyT0TTCDiytOAOJWcYmD/6KcvxMyy47SV9VhrWgKZUq',NULL,1,NULL,'2025-09-15 14:56:21','2025-09-15 14:56:21','active',1),(9,NULL,NULL,'AAIS/0559/010',NULL,NULL,'$2y$12$Q3dAAcwwBEhDbNOcsoyqGemmfuGIxp/IXuUU32s9eM59IkfPG8hkG',NULL,1,NULL,'2025-09-15 15:04:58','2025-09-15 15:04:58','active',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-15 21:55:57
