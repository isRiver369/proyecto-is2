CREATE DATABASE  IF NOT EXISTS `proservicios_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `proservicios_db`;
-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: localhost    Database: proservicios_db
-- ------------------------------------------------------
-- Server version	9.5.0

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
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '77e27279-cd33-11f0-9322-02508ca8c486:1-278';

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `categoria_id` int NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(100) NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`categoria_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Música','2025-12-13 21:24:42'),(2,'Tecnología','2025-12-13 21:24:42'),(3,'Gastronomía','2025-12-13 21:24:42'),(4,'Arte','2025-12-13 21:24:42'),(5,'Bienestar','2025-12-13 21:24:42');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracion`
--

DROP TABLE IF EXISTS `configuracion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracion` (
  `id` int NOT NULL DEFAULT '1',
  `nombre_sitio` varchar(100) DEFAULT 'ProServicios',
  `email_admin` varchar(150) DEFAULT 'admin@proservicios.com',
  `tasa_impuesto` decimal(5,2) DEFAULT '15.00',
  `moneda` varchar(5) DEFAULT '$',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion`
--

LOCK TABLES `configuracion` WRITE;
/*!40000 ALTER TABLE `configuracion` DISABLE KEYS */;
INSERT INTO `configuracion` VALUES (1,'ProServicios','admin@proservicios.com',15.00,'$');
/*!40000 ALTER TABLE `configuracion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eventos`
--

DROP TABLE IF EXISTS `eventos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eventos` (
  `evento_id` int NOT NULL AUTO_INCREMENT,
  `proveedor_id` int DEFAULT NULL,
  `nombre_evento` varchar(255) DEFAULT NULL,
  `descripcion` text,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `dia` varchar(20) NOT NULL,
  `hora` int NOT NULL,
  PRIMARY KEY (`evento_id`),
  KEY `proveedor_id` (`proveedor_id`),
  CONSTRAINT `fk_eventos_usuarios` FOREIGN KEY (`proveedor_id`) REFERENCES `usuarios` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eventos`
--

LOCK TABLES `eventos` WRITE;
/*!40000 ALTER TABLE `eventos` DISABLE KEYS */;
/*!40000 ALTER TABLE `eventos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos`
--

DROP TABLE IF EXISTS `pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagos` (
  `pago_id` int NOT NULL AUTO_INCREMENT,
  `reserva_id` int DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `estado_pago` enum('aprobado','rechazado','pendiente') DEFAULT NULL,
  PRIMARY KEY (`pago_id`),
  KEY `reserva_id` (`reserva_id`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`reserva_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos`
--

LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
INSERT INTO `pagos` VALUES (1,4,18.00,'tarjeta','aprobado'),(2,9,18.00,'tarjeta','aprobado'),(5,6,65.00,'tarjeta','aprobado');
/*!40000 ALTER TABLE `pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedores_info`
--

DROP TABLE IF EXISTS `proveedores_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proveedores_info` (
  `proveedor_id` int NOT NULL,
  `biografia` text,
  `enlace_portafolio` varchar(255) DEFAULT NULL,
  `contacto` varchar(255) DEFAULT NULL,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`proveedor_id`),
  CONSTRAINT `proveedores_info_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores_info`
--

LOCK TABLES `proveedores_info` WRITE;
/*!40000 ALTER TABLE `proveedores_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `proveedores_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservas`
--

DROP TABLE IF EXISTS `reservas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservas` (
  `reserva_id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `servicio_id` int DEFAULT NULL,
  `fecha_reserva` date NOT NULL,
  `horario_elegido` varchar(100) DEFAULT NULL,
  `total_pagar` decimal(10,2) DEFAULT NULL,
  `estado` enum('pendiente','confirmada','cancelada','pagada') DEFAULT 'pendiente',
  PRIMARY KEY (`reserva_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `servicio_id` (`servicio_id`),
  CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`),
  CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`servicio_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservas`
--

LOCK TABLES `reservas` WRITE;
/*!40000 ALTER TABLE `reservas` DISABLE KEYS */;
INSERT INTO `reservas` VALUES (1,3,9,'2025-12-01','Mañana (08:00 - 12:00)',18.00,'pagada'),(2,3,14,'2025-12-31','Tarde (18:00 - 19:00)',55.00,'pendiente'),(3,3,9,'2025-12-01','Tarde (14:00 - 18:00)',18.00,'pagada'),(6,4,1,'2026-01-05','General',65.00,'pagada');
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
  `categoria_id` int DEFAULT NULL,
  `nombre_servicio` varchar(150) NOT NULL,
  `descripcion` text,
  `descripcion_breve` varchar(400) DEFAULT NULL,
  `modalidad` varchar(50) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `disponible` tinyint(1) DEFAULT '1',
  `cupo_maximo` int DEFAULT '5',
  `cupos_restantes` int DEFAULT '5',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `horario` varchar(255) DEFAULT NULL,
  `politicas` text,
  PRIMARY KEY (`servicio_id`),
  KEY `proveedor_id` (`proveedor_id`),
  KEY `fk_servicio_categoria` (`categoria_id`),
  CONSTRAINT `fk_servicio_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`categoria_id`) ON DELETE SET NULL,
  CONSTRAINT `servicios_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `usuarios` (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicios`
--

LOCK TABLES `servicios` WRITE;
/*!40000 ALTER TABLE `servicios` DISABLE KEYS */;
INSERT INTO `servicios` VALUES (1,3,1,'Clase de Piano Avanzado','Curso dirigido a estudiantes con conocimientos previos de piano que desean perfeccionar su técnica, interpretación y expresión musical. Se trabajan obras de mayor complejidad, lectura avanzada, velocidad, dinámica y estilo, así como el desarrollo de una interpretación más profesional.','Taller intensivo de armonía y técnica avanzada.','Presencial','Cuenca',65.00,1,3,2,'2026-01-05','2026-02-16','Mañana (10:00 - 12:00),Tarde (15:00 - 17:00)','El alumno debe contar con conocimientos previos de piano para inscribirse.\n\nLa asistencia regular es obligatoria para garantizar el progreso del curso.'),(2,3,2,'Consultoría SEO Principiantes','Servicio introductorio enfocado en enseñar los fundamentos del posicionamiento en buscadores. Aprenderás cómo funciona Google, el uso de palabras clave, optimización básica de sitios web y buenas prácticas para mejorar la visibilidad online de manera orgánica.','Aprende a optimizar tu sitio web para Google.','Presencial','Guayaquil',40.00,1,5,5,'2026-01-26','2026-02-16','Mañana (09:00 - 11:00)','La consultoría está dirigida exclusivamente a personas sin conocimientos avanzados de SEO.\n\nLos resultados pueden variar según la aplicación de las recomendaciones brindadas.'),(3,3,5,'Masaje Relajante Express','Sesión corta diseñada para aliviar tensiones físicas y mentales en poco tiempo. Se enfoca en zonas clave como cuello, espalda y hombros, utilizando técnicas suaves que ayudan a reducir el estrés, mejorar la circulación y promover la relajación.','Sesión anti-estrés de 30 minutos.','Presencial','Guayaquil',25.00,1,4,4,'2025-12-15','2025-12-29','Tarde (16:00 - 16:30),Tarde (17:00 - 17:30)','El servicio debe reservarse con anticipación según disponibilidad.\n\nNo se realizan masajes a personas con condiciones médicas sin autorización previa.'),(4,3,4,'Curso Completo de Pintura','Programa integral que abarca desde los conceptos básicos del dibujo y color hasta técnicas avanzadas de pintura. Incluye el uso de diferentes materiales, estilos artísticos y el desarrollo de la creatividad personal a través de proyectos prácticos.','Técnicas al óleo, acrílico y acuarela.','Presencial','Cuenca',90.00,0,5,0,'2026-03-02','2026-04-06','Mañana (08:00 - 11:00)','Los materiales básicos pueden ser requeridos por el alumno.\n\nLa participación activa en las prácticas es necesaria para completar el curso.'),(5,3,3,'Curso de Cocina Mediterránea','Curso dedicado a la preparación de platos tradicionales de la dieta mediterránea. Se enseñan recetas saludables, el uso de ingredientes frescos como aceite de oliva, verduras y pescados, así como técnicas culinarias que promueven una alimentación equilibrada.','Aprende a preparar platos saludables.','Presencial','Quito',55.00,1,5,5,'2025-12-15','2026-02-09','Mañana (10:00 - 13:00),Tarde (16:00 - 19:00)','El alumno debe cumplir con las normas de higiene y seguridad alimentaria.\n\nLa institución no se hace responsable por alergias no informadas.'),(6,1,3,'Curso de cocina 3','Nivel avanzado enfocado en perfeccionar habilidades culinarias ya adquiridas. Se trabajan recetas más elaboradas, presentación de platos, combinaciones de sabores y técnicas profesionales para mejorar el nivel gastronómico del estudiante.','Gastronomía profesional.','Presencial','Quito',45.00,1,3,3,'2025-12-07','2026-01-11','Mañana (09:00 - 12:00)','Es requisito haber completado niveles previos o demostrar experiencia.\n\nLa puntualidad es obligatoria para el correcto desarrollo de las clases.'),(7,1,3,'Curso Pastelería 1','Curso introductorio al mundo de la pastelería donde se aprenden las bases para la elaboración de postres. Incluye masas, cremas, bizcochos y técnicas básicas de decoración, ideal para principiantes o aficionados.','Pasteles de distintos sabores y colores.','Presencial','Guayaquil',18.00,1,5,5,'2025-12-01','2026-01-05','Mañana (08:00 - 12:00),Tarde (14:00 - 18:00)','El curso está diseñado para principiantes sin experiencia previa.\n\nEl uso adecuado del equipo de cocina es responsabilidad del alumno.'),(8,1,5,'Curso de Yoga y Meditación','Programa orientado al bienestar físico y mental mediante la práctica del yoga y técnicas de meditación. Ayuda a mejorar la flexibilidad, la respiración, la concentración y la reducción del estrés, promoviendo el equilibrio entre cuerpo y mente.','Encuentra tu paz interior.','Presencial','Cuenca',35.00,1,4,4,'2025-12-31','2026-01-21','Mañana (06:00 - 07:00),Tarde (18:00 - 19:00)','Se recomienda informar al instructor sobre lesiones o condiciones físicas.\n\nLa práctica se realiza bajo responsabilidad personal del participante.'),(11,4,5,'Yoga 3','Este es un curso para yoga y mas articulos','Este es un curso para yoga','Presencial','Guayaquil',20.00,1,2,2,'2025-12-13','2025-12-20','Mañana: 19:46 - 18:46','Solo pueden inscribirse alumnos con experiencia previa comprobable.\n\nEl participante asume la responsabilidad de realizar las posturas correctamente.');
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
  PRIMARY KEY (`usuario_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Matias Obed','Peñaherrera Gonzalez','matiaspg@gmail.com','0923244434','$2y$10$0DPgb7VkiPUE6Z2VNpHhm.vK9sojJGU7mI.I6aqjrtqJvH4edzogq','proveedor','2025-11-30 08:21:08'),(2,'Carlos Marcos','Zambrano Bravo','carlos@gmail.com','0944122234','$2y$10$z5lTdsRPbW9ROQXpkqr/0Oo7PXWtrWHRqlbKWcUeM88uZusiCQ8cW','cliente','2025-11-30 08:59:25'),(3,'Alejandro José','Castillo López','alcastillo@gmail.com','0973724438','$2y$10$AopTe21r3wDHs1UmJCZvxenT3i3QMq.yJACmjC19BwKlnLw.N6Mju','proveedor','2025-12-03 09:24:25'),(4,'Pepito Carlos','Lozano Gonzales','pepito1@gmail.com','2156156121','$2y$10$12uVhiAsCqWXH0s4r1h.kOPA2zGayX/FtRQs.cBQ5LYNjUn.YgjAO','proveedor','2025-12-13 21:29:10'),(5,'A','B','alex@gmail.com','5459464156','$2y$10$2edrYGn2.1s70P/IN2tt0OJBNgGgtz/bMT5FbKSZ0mHVemDn.blO.','cliente','2025-12-13 22:07:33');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-13 17:14:03
