-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: multicinelp2
-- ------------------------------------------------------
-- Server version	8.0.43

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `asientos`
--

DROP TABLE IF EXISTS `asientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asientos` (
  `idAsiento` int NOT NULL AUTO_INCREMENT,
  `idSala` int NOT NULL,
  `fila` char(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` int NOT NULL,
  `estado` enum('disponible','ocupado','mantenimiento') COLLATE utf8mb4_unicode_ci DEFAULT 'disponible',
  PRIMARY KEY (`idAsiento`),
  KEY `idSala` (`idSala`),
  CONSTRAINT `asientos_ibfk_1` FOREIGN KEY (`idSala`) REFERENCES `salas` (`idSala`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asientos`
--

LOCK TABLES `asientos` WRITE;
/*!40000 ALTER TABLE `asientos` DISABLE KEYS */;
INSERT INTO `asientos` VALUES (1,4,'J',3,'ocupado'),(2,4,'J',4,'ocupado'),(3,4,'J',5,'ocupado'),(4,4,'J',6,'ocupado'),(5,4,'J',7,'ocupado'),(6,4,'F',8,'ocupado');
/*!40000 ALTER TABLE `asientos` ENABLE KEYS */;
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
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
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
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
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
-- Table structure for table `compras`
--

DROP TABLE IF EXISTS `compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compras` (
  `idCompra` int NOT NULL AUTO_INCREMENT,
  `CI_cliente` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idFuncion` int NOT NULL,
  `asientos` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `codigo_ticket` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_compra` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idCompra`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compras`
--

LOCK TABLES `compras` WRITE;
/*!40000 ALTER TABLE `compras` DISABLE KEYS */;
INSERT INTO `compras` VALUES (1,'14785698',13,'J4, J5',90.00,'TK-D563F','2026-03-01 17:02:51'),(2,'14785698',13,'J6, J7',90.00,'TK-09A2C','2026-03-01 17:19:57'),(3,'12365488',14,'J1, J2, J3',135.00,'CJ-0348D','2026-03-02 18:40:12'),(4,'14785698',14,'J4, J5, J6',135.00,'TK-70949','2026-03-02 18:40:35'),(5,'14785698',13,'E5',45.00,'TK-AA62A','2026-03-03 13:38:25'),(6,'14785698',13,'H5',45.00,'TK-D8D75','2026-03-03 13:39:29'),(7,'14785698',13,'D11',45.00,'TK-4CAF2','2026-03-03 13:51:21'),(8,'13758126',13,'H4, H2, F3',135.00,'TK-D0702','2026-03-03 15:13:59'),(9,'78978978',15,'J1, J2',90.00,'CJ-B9610','2026-03-05 18:01:51'),(10,'14785698',15,'J3, J4, J5, J6, J7',225.00,'TK-4F788','2026-03-05 18:47:26'),(11,'9208876',19,'F8',100.00,'TK-50F69','2026-03-06 02:01:41'),(12,'9208877',19,'F7, G7',180.00,'CJ-B6175','2026-03-06 02:06:18'),(13,'9208877',16,'F7',60.00,'CJ-E01CB','2026-03-06 02:30:38'),(14,'9208877',23,'E7',20.50,'CJ-083CF','2026-03-08 17:19:34'),(15,'9208876',25,'F8, F7, E7, E8',340.00,'TK-62C6D','2026-03-10 07:27:39'),(16,'9208876',24,'E6',31.00,'TK-3401A','2026-03-10 07:30:03'),(17,'9208877',25,'E7',85.00,'TK-ACE41','2026-03-10 07:34:39'),(18,'9208876',18,'D10',50.00,'TK-1813A','2026-03-10 12:48:23'),(19,'9208877',24,'E5',30.00,'TK-8DE1E','2026-03-10 12:50:50'),(20,'9208876',18,'G8',50.00,'TK-938A3','2026-03-10 12:54:42'),(21,'9208877',22,'E7, E8',90.00,'TK-6D11B','2026-03-10 13:00:34'),(22,'9208876',22,'G7, G8, G9, G10, G6, G5',270.00,'TK-777E9','2026-03-10 13:05:13');
/*!40000 ALTER TABLE `compras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `CI` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigoEmpleado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `turno` enum('mañana','tarde','noche') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sucursal` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`CI`),
  UNIQUE KEY `codigoEmpleado` (`codigoEmpleado`),
  CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`CI`) REFERENCES `usuarios` (`CI`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
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
-- Table structure for table `funciones`
--

DROP TABLE IF EXISTS `funciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funciones` (
  `idFuncion` int NOT NULL AUTO_INCREMENT,
  `idPelicula` int NOT NULL,
  `idSala` int NOT NULL,
  `fechaFuncion` date NOT NULL,
  `horaInicio` time NOT NULL,
  `horaFin` time NOT NULL,
  `precioBase` decimal(10,2) NOT NULL,
  `boletos_vendidos` int NOT NULL DEFAULT '0',
  `asientos_vendidos` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idFuncion`),
  KEY `idPelicula` (`idPelicula`),
  KEY `idSala` (`idSala`),
  CONSTRAINT `funciones_ibfk_1` FOREIGN KEY (`idPelicula`) REFERENCES `peliculas` (`idPelicula`) ON DELETE CASCADE,
  CONSTRAINT `funciones_ibfk_2` FOREIGN KEY (`idSala`) REFERENCES `salas` (`idSala`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funciones`
--

LOCK TABLES `funciones` WRITE;
/*!40000 ALTER TABLE `funciones` DISABLE KEYS */;
INSERT INTO `funciones` VALUES (2,3,3,'2026-02-26','19:00:00','22:30:00',75.00,0,NULL),(5,8,3,'2026-02-27','21:00:00','23:15:00',75.00,0,NULL),(8,5,3,'2026-02-28','20:00:00','23:20:00',80.00,5,'J1,J2,J3,J4,J5'),(13,11,4,'2026-03-03','12:00:00','14:00:00',45.00,15,'J1,J2,J3,J4,J5,J6,J7,I9,I10,E5,H5,D11,H4,H2,F3'),(14,2,4,'2026-03-02','15:00:00','17:00:00',45.00,6,'J1,J2,J3,J4,J5,J6'),(15,2,4,'2026-03-05','15:00:00','17:00:00',45.00,7,'J1,J2,J3,J4,J5,J6,J7'),(16,7,4,'2026-03-11','14:14:00','16:14:00',100.00,1,'F7'),(17,12,4,'2026-03-06','15:15:00','17:15:00',45.00,0,NULL),(18,12,4,'2026-03-11','16:16:00','18:16:00',100.00,0,NULL),(19,12,4,'2026-03-12','15:15:00','17:15:00',100.00,3,'F8,F7,G7'),(20,6,4,'2026-03-08','18:18:00','20:18:00',45.00,0,NULL),(21,6,4,'2026-03-09','18:18:00','20:18:00',45.00,0,NULL),(22,6,4,'2026-03-10','18:18:00','20:18:00',45.00,0,'E7,E8,G7,G8,G9,G10,G6,G5'),(23,6,4,'2026-03-11','18:18:00','20:18:00',45.00,1,'E7'),(24,13,3,'2026-03-10','17:17:00','19:17:00',45.00,0,NULL),(25,14,7,'2026-03-10','20:20:00','22:20:00',85.00,0,NULL),(26,14,7,'2026-03-11','20:20:00','22:20:00',85.00,0,NULL),(27,14,7,'2026-03-12','20:20:00','22:20:00',85.00,0,NULL),(28,14,7,'2026-03-13','20:20:00','22:20:00',85.00,0,NULL),(29,14,7,'2026-03-14','20:20:00','22:20:00',85.00,0,NULL),(30,14,7,'2026-03-15','20:20:00','22:20:00',85.00,0,NULL),(31,14,11,'2026-03-10','23:28:00','01:28:00',45.00,0,NULL),(32,12,16,'2026-03-10','13:13:00','15:13:00',45.00,0,NULL),(33,3,12,'2026-03-10','17:17:00','19:17:00',45.00,0,NULL);
/*!40000 ALTER TABLE `funciones` ENABLE KEYS */;
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
  `fecha` datetime NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `accion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
INSERT INTO `logs` VALUES (1,'2026-03-10 09:25:47','Admin Eddy','administrador','Cambió el estado del empleado: Pepe Sanchez a activo'),(2,'2026-03-10 09:32:08','Admin Eddy','administrador','Programó 1 nuevas funciones para la película: Avatar 2'),(3,'2026-03-10 09:45:36','Admin Eddy','administrador','Cerró sesión (Web).');
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_actividad`
--

DROP TABLE IF EXISTS `logs_actividad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs_actividad` (
  `idLog` int NOT NULL AUTO_INCREMENT,
  `CI_usuario` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `accion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idLog`),
  KEY `CI_usuario` (`CI_usuario`),
  CONSTRAINT `logs_actividad_ibfk_1` FOREIGN KEY (`CI_usuario`) REFERENCES `usuarios` (`CI`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_actividad`
--

LOCK TABLES `logs_actividad` WRITE;
/*!40000 ALTER TABLE `logs_actividad` DISABLE KEYS */;
INSERT INTO `logs_actividad` VALUES (1,'1234567','Inicio de sesión en el sistema','2026-03-05 17:55:46'),(2,'7654321','Inicio de sesión en el sistema','2026-03-05 17:56:29'),(3,'1234567','Inicio de sesión en el sistema','2026-03-05 18:02:25'),(4,'7654321','Inicio de sesión en el sistema','2026-03-05 18:30:47'),(5,'14785698','Inicio de sesión en el sistema','2026-03-05 18:47:08'),(6,'7654321','Inicio de sesión en el sistema','2026-03-05 18:47:41'),(7,'1234567','Inicio de sesión en el sistema','2026-03-06 01:40:35'),(8,'1234567','Creó nuevo empleado: Favio Estefano Sandy Gonzales (administrador)','2026-03-06 01:46:22'),(9,'7654321','Inicio de sesión en el sistema','2026-03-06 01:52:05'),(10,'1234567','Inicio de sesión en el sistema','2026-03-06 01:52:27'),(11,'1234567','Inicio de sesión en el sistema','2026-03-06 01:52:28'),(12,'9208876','Inicio de sesión en el sistema','2026-03-06 01:57:23'),(13,'1234567','Inicio de sesión en el sistema','2026-03-06 01:58:03'),(14,'1234567','Inicio de sesión en el sistema','2026-03-06 01:58:03'),(15,'9208876','Inicio de sesión en el sistema','2026-03-06 01:58:55'),(16,'1234567','Inicio de sesión en el sistema','2026-03-06 01:59:35'),(17,'9208876','Inicio de sesión en el sistema','2026-03-06 02:00:30'),(18,'7654321','Inicio de sesión en el sistema','2026-03-06 02:02:27'),(19,'9208877','Inicio de sesión en el sistema','2026-03-06 02:17:26'),(20,'9208876','Inicio de sesión en el sistema','2026-03-06 02:18:12'),(21,'9208876','Inicio de sesión en el sistema','2026-03-06 02:19:14'),(22,'7654321','Inicio de sesión en el sistema','2026-03-06 02:23:09'),(23,'1234567','Inicio de sesión en el sistema','2026-03-06 02:27:03'),(24,'7654321','Inicio de sesión en el sistema','2026-03-06 02:30:07'),(25,'9208877','Inicio de sesión en el sistema','2026-03-08 17:04:07'),(26,'1234567','Inicio de sesión en el sistema','2026-03-08 17:07:13'),(27,'9208876','Inicio de sesión en el sistema','2026-03-08 17:12:54'),(28,'1234567','Inicio de sesión en el sistema','2026-03-08 17:14:19'),(29,'7654321','Inicio de sesión en el sistema','2026-03-08 17:16:32'),(30,'1234567','Inicio de sesión en el sistema','2026-03-08 17:20:30'),(31,'1234567','Inicio de sesión en el sistema','2026-03-08 17:59:44'),(32,'1234567','Inicio de sesión en el sistema','2026-03-08 18:02:10'),(33,'1234567','Inicio de sesión en el sistema','2026-03-08 18:02:28'),(34,'9208877','Inicio de sesión en el sistema','2026-03-09 05:41:45'),(35,'9208877','Inicio de sesión en el sistema','2026-03-09 05:42:24'),(36,'9208877','Inicio de sesión en el sistema','2026-03-09 05:46:15'),(37,'9208876','Inicio de sesión en el sistema','2026-03-09 05:50:54'),(38,'9208866','Inicio de sesión en el sistema','2026-03-09 05:57:39'),(39,'9208866','Inicio de sesión en el sistema','2026-03-09 06:14:32'),(40,'9208866','Inicio de sesión en el sistema','2026-03-09 06:17:50'),(41,'9208866','Inicio de sesión en el sistema','2026-03-09 06:21:52'),(42,'9208866','Inicio de sesión en el sistema','2026-03-09 06:33:30'),(43,'9208876','Inicio de sesión en el sistema','2026-03-09 06:33:52'),(44,'9208877','Inicio de sesión en el sistema','2026-03-09 06:34:25'),(45,'9208877','Inicio de sesión en el sistema','2026-03-09 15:18:50');
/*!40000 ALTER TABLE `logs_actividad` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_03_09_173201_create_personal_access_tokens_table',1),(5,'2026_03_10_092105_create_logs_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
-- Table structure for table `peliculas`
--

DROP TABLE IF EXISTS `peliculas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `peliculas` (
  `idPelicula` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sinopsis` text COLLATE utf8mb4_unicode_ci,
  `duracion` int NOT NULL,
  `genero` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clasificacion` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idioma` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagenPoster` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('activa','inactiva') COLLATE utf8mb4_unicode_ci DEFAULT 'activa',
  PRIMARY KEY (`idPelicula`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peliculas`
--

LOCK TABLES `peliculas` WRITE;
/*!40000 ALTER TABLE `peliculas` DISABLE KEYS */;
INSERT INTO `peliculas` VALUES (1,'Dragon Ball Super: Broly','Lucha épica entre Goku, Vegeta y Broly.',100,'Acción','B','Español Latino','BROLY.jpg','activa'),(2,'The Super Mario Galaxy','Mario explora el cosmos.',95,'Infantil','A','Español Latino','Mario galaxy.jpg','activa'),(3,'Avatar 2','El camino del agua en Pandora.',192,'Sci-Fi','B','Subtitulada','avatar.jpg','activa'),(5,'Titanic','Un romance épico en el viaje inaugural del RMS Titanic.',194,'Romance','B','Español Latino','titanic.jpg','activa'),(6,'Star Wars','Una aventura en una galaxia muy, muy lejana.',121,'Sci-Fi','B','Subtitulada','star ward.jpg','activa'),(7,'Bob Esponja: Al Rescate','Bob y Patricio viajan a la ciudad perdida de Atlantic City.',91,'Infantil','A','Español Latino','bob sponja.jpg','activa'),(8,'Deadpool 3','Deadpool y Wolverine en una misión caótica.',128,'Acción','C','Español latino','deadbool.jpg','activa'),(11,'Spiderman no way Home','Spider-Man: No Way Home (2021) es una película de acción y ciencia ficción del Universo Cinematográfico de Marvel (MCU) donde la identidad de Peter Parker (Tom Holland) es revelada por Mysterio. Buscando ayuda en el Doctor Strange para revertir esto, un hechizo fallido rompe el multiverso, trayendo villanos de otras realidades.',148,'Acción','B','Español latino','Spiderman No Way Home.jpg','activa'),(12,'Marty Supreme','Ping Pong',150,'Drama, Comedia','C','ingles','Marty-Supreme-Alternative-Poster.jpg','activa'),(13,'Parasite','Familia Moderna',150,'Drama','B','Koreano','1773103773_parasite_poster.jpg','activa'),(14,'Scream 7','Muerte',114,'Terror','B','ingles','1773112886_Scream-7.jpg','activa'),(15,'Scream 7','terrorr',150,'Drama','B','ingles','1773130218_Scream-7.jpg','activa'),(16,'Scream 7','terrorr',150,'Drama','B','ingles','1773130218_Scream-7.jpg','activa');
/*!40000 ALTER TABLE `peliculas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservas`
--

DROP TABLE IF EXISTS `reservas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservas` (
  `idReserva` int NOT NULL AUTO_INCREMENT,
  `CICliente` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idFuncion` int NOT NULL,
  `CIEmpleado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaReserva` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` enum('pendiente','confirmada','cancelada') COLLATE utf8mb4_unicode_ci DEFAULT 'confirmada',
  `montoTotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idReserva`),
  KEY `CICliente` (`CICliente`),
  KEY `idFuncion` (`idFuncion`),
  KEY `CIEmpleado` (`CIEmpleado`),
  CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`CICliente`) REFERENCES `usuarios` (`CI`),
  CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`idFuncion`) REFERENCES `funciones` (`idFuncion`),
  CONSTRAINT `reservas_ibfk_3` FOREIGN KEY (`CIEmpleado`) REFERENCES `usuarios` (`CI`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservas`
--

LOCK TABLES `reservas` WRITE;
/*!40000 ALTER TABLE `reservas` DISABLE KEYS */;
INSERT INTO `reservas` VALUES (1,'14785698',14,NULL,'2026-03-02 14:40:35','confirmada',135.00),(2,'14785698',13,NULL,'2026-03-03 09:38:25','confirmada',45.00),(3,'14785698',13,NULL,'2026-03-03 09:39:29','confirmada',45.00),(4,'14785698',13,NULL,'2026-03-03 09:51:21','confirmada',45.00),(5,'13758126',13,NULL,'2026-03-03 11:13:59','confirmada',135.00),(6,'14785698',15,NULL,'2026-03-05 14:47:26','confirmada',225.00),(7,'9208876',19,NULL,'2026-03-05 22:01:41','confirmada',100.00);
/*!40000 ALTER TABLE `reservas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `idRol` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`idRol`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'administrador'),(2,'cajero'),(3,'cliente');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salas`
--

DROP TABLE IF EXISTS `salas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salas` (
  `idSala` int NOT NULL AUTO_INCREMENT,
  `numero` int NOT NULL,
  `tipoPantalla` enum('2D','3D','XL','4D','PLUS') COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacidadTotal` int NOT NULL,
  `estado` enum('activa','mantenimiento','inactiva') COLLATE utf8mb4_unicode_ci DEFAULT 'activa',
  `filas` int NOT NULL DEFAULT '10',
  `columnas` int NOT NULL DEFAULT '10',
  `precio` decimal(10,2) NOT NULL DEFAULT '45.00',
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'sala_default.jpg',
  `tipo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'classic',
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacidad` int NOT NULL,
  PRIMARY KEY (`idSala`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salas`
--

LOCK TABLES `salas` WRITE;
/*!40000 ALTER TABLE `salas` DISABLE KEYS */;
INSERT INTO `salas` VALUES (3,3,'PLUS',100,'activa',10,10,45.00,'sala_default.jpg','classic','Favio Sala 67',100),(4,0,'2D',0,'activa',10,14,45.00,'sala_classic.jpg','classic','Sala 2',140),(6,0,'2D',140,'activa',10,14,45.00,'sala_default.jpg','4d','Sala 1',140),(7,0,'2D',140,'activa',10,14,45.00,'sala_default.jpg','4d','Sala 4D - 1',140),(10,0,'2D',140,'activa',10,14,45.00,'sala_default.jpg','classic','Sala 69',140),(11,0,'2D',140,'activa',10,14,45.00,'sala_default.jpg','classic','Sala 69',140),(12,0,'2D',140,'activa',10,14,45.00,'sala_default.jpg','classic','Sala 69',140),(13,0,'2D',140,'activa',10,14,45.00,'sala_default.jpg','classic','Sala 77',140),(14,0,'2D',140,'activa',10,14,45.00,'sala_default.jpg','classic','Sala 77',140),(16,0,'2D',140,'activa',10,14,45.00,'sala_default.jpg','plus','Sala 10 (VIP)',140);
/*!40000 ALTER TABLE `salas` ENABLE KEYS */;
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
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `idTicket` int NOT NULL AUTO_INCREMENT,
  `idReserva` int NOT NULL,
  `idAsiento` int NOT NULL,
  `codigoQR` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precioFinal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`idTicket`),
  UNIQUE KEY `codigoQR` (`codigoQR`),
  KEY `idReserva` (`idReserva`),
  KEY `idAsiento` (`idAsiento`),
  CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`idReserva`) REFERENCES `reservas` (`idReserva`) ON DELETE CASCADE,
  CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`idAsiento`) REFERENCES `asientos` (`idAsiento`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (1,6,1,'913643decb22f271838ca1f797dd76ac',45.00),(2,6,2,'bc2982786f9b7f44de115fb25b06207d',45.00),(3,6,3,'0ecdd4a28bb588e694b300c966cd725d',45.00),(4,6,4,'514aba8913bc1465b6e5509e94c02410',45.00),(5,6,5,'44dba71914182416a8786c645104da30',45.00),(6,7,6,'fba155a2a18041feedf7977fe6a3f780',100.00);
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
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
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `CI` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contrasena` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idRol` int NOT NULL,
  `estado` enum('activo','inactivo') COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
  `puntos` int DEFAULT '0',
  PRIMARY KEY (`CI`),
  UNIQUE KEY `correo` (`correo`),
  KEY `fk_usuario_rol` (`idRol`),
  CONSTRAINT `fk_usuario_rol` FOREIGN KEY (`idRol`) REFERENCES `roles` (`idRol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES ('1234567','Admin Eddy','admin@multicine.com','admin123','11111111',1,'activo',0),('13758126','cristhian alan vega ramirez','alan@multicine.com','$2y$10$T5qq/JVTcu07UP0r3d92H.NF5LWjvFJeIXGF7qX9yOB/tbwHDNL8O','74025772',3,'activo',0),('14785698','Fernando Vallez','Fernando@gmail.com','$2y$10$cVqJ0UAiN.a05bhq.STwH..haMKnjjoqYlN04FKyC35KjAfgZ3c8K','12345688',3,'activo',7),('7654321','Cajero Pedro','pedro@multicine.com','cajero123','22222222',2,'activo',0),('9208811','Pepe Sanchez','pepe@multicinecom','pepe123',NULL,2,'activo',0),('9208866','Pancho Villa','q@q.com','$2y$12$.uyfsJWN5DPHo5ADSKkJ2eUZTvnUezCED/VY5K9JntD2cBBTCZ15.','77751832',3,'activo',0),('9208876','Favio','favio2@gmail.com','$2y$10$5PjfPrEv6Bg3EMhQlSj/zOGtzP80enYUYdmgulhqSIEa.heCGjAc6','77751832',3,'activo',12),('9208877','Favio Estefano Sandy Gonzales','favio@gmail.com','$2y$10$yNxKvCvZafWbHHHZRzegTeiaAFxnPMpUnha/qESpE1lC7PIBs4d8C',NULL,1,'activo',6);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-10  9:15:09
