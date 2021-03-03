-- MySQL dump 10.13  Distrib 5.5.62, for Win64 (AMD64)
--
-- Host: 192.168.10.10    Database: inventarios
-- ------------------------------------------------------
-- Server version	8.0.21-0ubuntu0.20.04.4

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `almacenes`
--

DROP TABLE IF EXISTS `almacenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `almacenes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre_almacen` varchar(255) DEFAULT NULL,
  `localizacion` varchar(255) DEFAULT NULL,
  `responsable` varchar(150) DEFAULT NULL,
  `tipo` int DEFAULT NULL COMMENT '1 = virtual, 2 = fisico',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `almacenes`
--

LOCK TABLES `almacenes` WRITE;
/*!40000 ALTER TABLE `almacenes` DISABLE KEYS */;
INSERT INTO `almacenes` VALUES (1,'Almacen 1','Veracruz','Hector Dominguez',1),(2,'Almacen 2','CDMX','Raul Sanchez',2),(3,'Almacen 3','Queretaro','Rocio Gomez',1),(4,'Almacen 4','Jalisco','Victoria Ponce',2);
/*!40000 ALTER TABLE `almacenes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `existencias`
--

DROP TABLE IF EXISTS `existencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `existencias` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `id_producto` int unsigned NOT NULL,
  `id_almacen` int unsigned NOT NULL,
  `existencias` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_producto` (`id_producto`),
  KEY `id_almacen` (`id_almacen`),
  CONSTRAINT `existencias_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`),
  CONSTRAINT `existencias_ibfk_2` FOREIGN KEY (`id_almacen`) REFERENCES `almacenes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `existencias`
--

LOCK TABLES `existencias` WRITE;
/*!40000 ALTER TABLE `existencias` DISABLE KEYS */;
INSERT INTO `existencias` VALUES (1,1,1,5),(2,2,1,2),(3,3,2,3),(4,4,2,10),(5,5,3,12),(6,6,3,11),(7,7,4,10),(8,8,4,7),(9,9,1,8),(10,10,2,8),(11,11,3,5),(12,12,4,10),(13,13,1,20),(14,14,1,2),(15,15,3,6),(16,6,1,0),(17,7,2,7),(18,8,2,1),(19,11,4,5),(20,2,4,9),(21,1,2,10),(22,1,3,15),(24,1,4,2),(35,2,2,1),(36,2,3,1),(37,5,1,50),(38,5,2,10),(39,8,3,10);
/*!40000 ALTER TABLE `existencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `productos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(255) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `marca` varchar(150) DEFAULT NULL,
  `color` varchar(100) DEFAULT NULL,
  `precio` decimal(10,0) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,'A-45','Teclado Mecanico','Razer','Negro Mate',1500),(2,'B-02','Monitor 32\"','Asus','Negor',4500),(3,'C-05','Memoria Ram 2 modulos de 8GB','XPG','Blanco',2500),(4,'A-56','Monitor 27\"','Samsung','Negro',3500),(5,'S-25','Disipador liquido','Auorus','Negro',5000),(6,'S-55','RTX 2060','Aorus Gygabite','Negro',8500),(7,'A-55','Motherboard Elite','Aorus','Negro',5000),(8,'C-56','Ryzen 3600X','AMD','Plata',5500),(9,'W-55','Gabinete','Deepcool','Blanco',3500),(10,'A-02','Mouse ','Razer','Negro RGB',2100),(11,'A-06','Raspberry pi model B 2GB ram','RPI','Blanco',1500),(12,'C-01','Router ','TP LInk','Blanco',2500),(13,'C-45','Audifonos bluetooth','Bose','Negro Mate',3500),(14,'D-85','HDD 1TB','Seagate','Plata',1000),(15,'E-25','SSD M.2 500Gb','XPG','Rojo',2500);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'inventarios'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-03-03 13:28:33
