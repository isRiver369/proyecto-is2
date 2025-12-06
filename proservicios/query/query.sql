-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: proservicios_db
-- ------------------------------------------------------
-- Server version	9.3.0

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
-- Table structure for table `pagos`
--
use proservicios_db;

CREATE TABLE `pagos` (
  `pago_id` int NOT NULL AUTO_INCREMENT,
  `reserva_id` int DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `estado_pago` enum('aprobado','rechazado','pendiente') DEFAULT NULL,
  PRIMARY KEY (`pago_id`),
  KEY `reserva_id` (`reserva_id`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`reserva_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos`
--
use proservicios_db;
LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
INSERT INTO `pagos` VALUES (2,4,18.00,'tarjeta','aprobado'),(3,9,18.00,'tarjeta','aprobado');
/*!40000 ALTER TABLE `pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservas`
--
use proservicios_db;
DROP TABLE IF EXISTS `reservas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservas` (
  `reserva_id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `servicio_id` int DEFAULT NULL,
  `fecha_reserva` date NOT NULL,
  `total_pagar` decimal(10,2) DEFAULT NULL,
  `estado` enum('pendiente','confirmada','cancelada','pagada') DEFAULT 'pendiente',
  PRIMARY KEY (`reserva_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `servicio_id` (`servicio_id`),
  CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`),
  CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`servicio_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservas`
--

LOCK TABLES `reservas` WRITE;
/*!40000 ALTER TABLE `reservas` DISABLE KEYS */;
INSERT INTO `reservas` VALUES (4,3,9,'2025-12-01',18.00,'pagada'),(8,3,14,'2025-12-31',55.00,'pendiente'),(9,3,9,'2025-12-01',18.00,'pagada');
/*!40000 ALTER TABLE `reservas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servicios`
--

DROP TABLE IF EXISTS `servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servicios` (
  `servicio_id` int NOT NULL AUTO_INCREMENT,
  `proveedor_id` int DEFAULT NULL,
  `nombre_servicio` varchar(150) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `disponible` tinyint(1) DEFAULT '1',
  `cupo_maximo` int DEFAULT '5',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `horario` varchar(255) DEFAULT NULL,
  `politicas` text,
  PRIMARY KEY (`servicio_id`),
  KEY `proveedor_id` (`proveedor_id`),
  CONSTRAINT `servicios_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `usuarios` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicios`
--

LOCK TABLES `servicios` WRITE;
/*!40000 ALTER TABLE `servicios` DISABLE KEYS */;
INSERT INTO `servicios` VALUES (1,4,'Clase de Piano Avanzado','Taller intensivo de armonía y técnica avanzada.',65.00,1,10,'2026-01-05','2026-02-16','Lun-Vie 12:30',''),(2,4,'Consultoría SEO Principiantes','Aprende a optimizar tu sitio web para Google.',40.00,1,10,'2026-01-26','2026-02-16','Lun-Vie 15:00',''),(3,4,'Masaje Relajante Express','Sesión anti-estrés de 30 minutos.',25.00,1,10,'2025-12-15','2025-12-29','Lun-Vie 17:00',''),(4,4,'Curso Completo de Pintura','Técnicas al óleo, acrílico y acuarela.',90.00,0,10,'2026-03-02','2026-04-06','Lun-Vie 08:00',''),(5,4,'Curso de Cocina Mediterránea','Aprende a preparar platos saludables y deliciosos.',55.00,1,10,'2025-12-15','2026-02-09','Lun-Vie 16:00',''),(7,2,'Curso de cocina 3','Aprende de la mejor gastronomía y a cocinar como solo los mejores profesionales lo hacen.',45.00,1,20,'2025-12-07','2026-01-11','Dom 13:00',''),(9,2,'Curso Pastelería 1','Vamos a aprender a cocinar pasteles de distintos sabores y colores.',18.00,1,15,'2025-12-01','2026-01-05','Lun-Vie 09:00',''),(14,2,'Curso de Prueba 5','Esta descripción es una prueba, para revisar que todo el catalogo esté funcionando correctamente.',55.00,0,35,'2025-12-31','2026-01-21','Vie-Dom 13:00','');
/*!40000 ALTER TABLE `servicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `usuario_id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('cliente','proveedor','administrador') DEFAULT 'cliente',
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `bio` text,
  `portafolio_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`usuario_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (2,'Matias Obed','Peñaherrera Gonzalez','matiaspg@gmail.com','0923244434','$2y$10$0DPgb7VkiPUE6Z2VNpHhm.vK9sojJGU7mI.I6aqjrtqJvH4edzogq','proveedor','2025-11-30 03:21:08','Mi historia empieza en esta universidad','https://matiaspg.com'),(3,'Carlos Marcos','Zambrano Bravo','carlos@gmail.com','0944122234','$2y$10$z5lTdsRPbW9ROQXpkqr/0Oo7PXWtrWHRqlbKWcUeM88uZusiCQ8cW','cliente','2025-11-30 03:59:25',NULL,NULL),(4,'Alejandro José','Castillo López','alcastillo@gmail.com','0973724438','$2y$10$AopTe21r3wDHs1UmJCZvxenT3i3QMq.yJACmjC19BwKlnLw.N6Mju','proveedor','2025-12-03 04:24:25',NULL,NULL);
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

-- Dump completed on 2025-12-03  3:39:16

USE proservicios_db;
-- Agregamos la columna donde se guardará si es "Mañana" o "Tarde"
ALTER TABLE reservas 
ADD COLUMN horario_elegido VARCHAR(100) NULL AFTER fecha_reserva;
-- Actualizar los horarios de los servicios existentes
USE proservicios_db;
SET SQL_SAFE_UPDATES = 0; -- Desactiva la protección temporalmente
UPDATE servicios
SET horario = 'Mañana (08:00 - 12:00),Tarde (14:00 - 18:00)'
WHERE servicio_id > 0;

SET SQL_SAFE_UPDATES = 1; -- La reactiva por seguridad
