CREATE DATABASE IF NOT EXISTS proservicios_db;
/* 1. Configuración inicial para evitar errores de orden de creación */
SET FOREIGN_KEY_CHECKS = 0;

-- Asegurarnos de usar la base de datos correcta
USE proservicios_db;

/* =============================================
   TABLA: USUARIOS
   ============================================= */
DROP TABLE IF EXISTS `usuarios`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Datos de usuarios
INSERT INTO `usuarios` VALUES 
(2,'Matias Obed','Peñaherrera Gonzalez','matiaspg@gmail.com','0923244434','$2y$10$0DPgb7VkiPUE6Z2VNpHhm.vK9sojJGU7mI.I6aqjrtqJvH4edzogq','proveedor','2025-11-30 03:21:08','Mi historia empieza en esta universidad','https://matiaspg.com'),
(3,'Carlos Marcos','Zambrano Bravo','carlos@gmail.com','0944122234','$2y$10$z5lTdsRPbW9ROQXpkqr/0Oo7PXWtrWHRqlbKWcUeM88uZusiCQ8cW','cliente','2025-11-30 03:59:25',NULL,NULL),
(4,'Alejandro José','Castillo López','alcastillo@gmail.com','0973724438','$2y$10$AopTe21r3wDHs1UmJCZvxenT3i3QMq.yJACmjC19BwKlnLw.N6Mju','proveedor','2025-12-03 04:24:25',NULL,NULL);


/* =============================================
   TABLA: SERVICIOS
   ============================================= */
DROP TABLE IF EXISTS `servicios`;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Datos de servicios
INSERT INTO `servicios` VALUES 
(1,4,'Clase de Piano Avanzado','Taller intensivo de armonía y técnica avanzada.',65.00,1,10,'2026-01-05','2026-02-16','Lun-Vie 12:30',''),
(2,4,'Consultoría SEO Principiantes','Aprende a optimizar tu sitio web para Google.',40.00,1,10,'2026-01-26','2026-02-16','Lun-Vie 15:00',''),
(3,4,'Masaje Relajante Express','Sesión anti-estrés de 30 minutos.',25.00,1,10,'2025-12-15','2025-12-29','Lun-Vie 17:00',''),
(4,4,'Curso Completo de Pintura','Técnicas al óleo, acrílico y acuarela.',90.00,0,10,'2026-03-02','2026-04-06','Lun-Vie 08:00',''),
(5,4,'Curso de Cocina Mediterránea','Aprende a preparar platos saludables y deliciosos.',55.00,1,10,'2025-12-15','2026-02-09','Lun-Vie 16:00',''),
(7,2,'Curso de cocina 3','Aprende de la mejor gastronomía y a cocinar como solo los mejores profesionales lo hacen.',45.00,1,20,'2025-12-07','2026-01-11','Dom 13:00',''),
(9,2,'Curso Pastelería 1','Vamos a aprender a cocinar pasteles de distintos sabores y colores.',18.00,1,15,'2025-12-01','2026-01-05','Lun-Vie 09:00',''),
(14,2,'Curso de Prueba 5','Esta descripción es una prueba, para revisar que todo el catalogo esté funcionando correctamente.',55.00,0,35,'2025-12-31','2026-01-21','Vie-Dom 13:00','');


/* =============================================
   TABLA: RESERVAS
   ============================================= */
DROP TABLE IF EXISTS `reservas`;

CREATE TABLE `reservas` (
  `reserva_id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int DEFAULT NULL,
  `servicio_id` int DEFAULT NULL,
  `fecha_reserva` date NOT NULL,
  `horario_elegido` varchar(100) DEFAULT NULL, /* Agregado directamente aquí */
  `total_pagar` decimal(10,2) DEFAULT NULL,
  `estado` enum('pendiente','confirmada','cancelada','pagada') DEFAULT 'pendiente',
  PRIMARY KEY (`reserva_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `servicio_id` (`servicio_id`),
  CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`),
  CONSTRAINT `reservas_ibfk_2` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`servicio_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Datos de reservas
INSERT INTO `reservas` (reserva_id, usuario_id, servicio_id, fecha_reserva, total_pagar, estado) VALUES 
(4,3,9,'2025-12-01',18.00,'pagada'),
(8,3,14,'2025-12-31',55.00,'pendiente'),
(9,3,9,'2025-12-01',18.00,'pagada');


/* =============================================
   TABLA: PAGOS
   ============================================= */
DROP TABLE IF EXISTS `pagos`;

CREATE TABLE `pagos` (
  `pago_id` int NOT NULL AUTO_INCREMENT,
  `reserva_id` int DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `estado_pago` enum('aprobado','rechazado','pendiente') DEFAULT NULL,
  PRIMARY KEY (`pago_id`),
  KEY `reserva_id` (`reserva_id`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`reserva_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Datos de pagos
INSERT INTO `pagos` VALUES 
(2,4,18.00,'tarjeta','aprobado'),
(3,9,18.00,'tarjeta','aprobado');


/* =============================================
   ACTUALIZACIONES FINALES
   ============================================= */
-- Actualizar el horario de todos los servicios 
UPDATE servicios 
SET horario = 'Mañana (08:00 - 12:00),Tarde (14:00 - 18:00)' 
WHERE servicio_id > 0;

/* Restaurar seguridad de llaves foráneas */
SET FOREIGN_KEY_CHECKS = 1;


-- NUEVO
USE proservicios_db;

-- 1. Crear la tabla de Categorías
CREATE TABLE IF NOT EXISTS `categorias` (
  `categoria_id` int NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(100) NOT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`categoria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Insertar algunas categorías base para no empezar vacíos
INSERT INTO `categorias` (nombre_categoria) VALUES 
('Música'), ('Tecnología'), ('Gastronomía'), ('Arte'), ('Bienestar');

-- 3. Modificar la tabla Servicios para agregar la columna de categoría
-- (Solo si no la has agregado antes)
ALTER TABLE `servicios` 
ADD COLUMN `categoria_id` int DEFAULT NULL AFTER `proveedor_id`;

-- 4. Crear la relación (Llave Foránea)
-- Si borras una categoría, el servicio NO se borra, solo se queda sin categoría (SET NULL)
ALTER TABLE `servicios`
ADD CONSTRAINT `fk_servicio_categoria`
FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`categoria_id`)
ON DELETE SET NULL;


USE proservicios_db;

-- 1. Crear tabla de configuración
CREATE TABLE IF NOT EXISTS `configuracion` (
  `id` int NOT NULL DEFAULT 1,
  `nombre_sitio` varchar(100) DEFAULT 'ProServicios',
  `email_admin` varchar(150) DEFAULT 'admin@proservicios.com',
  `tasa_impuesto` decimal(5,2) DEFAULT 15.00, -- Ej: 15%
  `moneda` varchar(5) DEFAULT '$',
  `modo_mantenimiento` tinyint(1) DEFAULT 0, -- 0: No, 1: Sí
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Insertar la configuración inicial (Solo una vez)
INSERT INTO `configuracion` (id, nombre_sitio, email_admin, tasa_impuesto, moneda, modo_mantenimiento)
VALUES (1, 'ProServicios', 'admin@proservicios.com', 15.00, '$', 0)
ON DUPLICATE KEY UPDATE id=1; 
-- (El ON DUPLICATE evita errores si lo ejecutas dos veces)