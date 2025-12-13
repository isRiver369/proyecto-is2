/* =============================================
   0. CONFIGURACIÓN INICIAL
   ============================================= */
DROP DATABASE IF EXISTS proservicios_db;
CREATE DATABASE proservicios_db;
USE proservicios_db;

SET FOREIGN_KEY_CHECKS = 0;

/* =============================================
   1. TABLA: CATEGORIAS (Creamos esto primero para poder relacionarla)
   ============================================= */
CREATE TABLE `categorias` (
  `categoria_id` int NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(100) NOT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`categoria_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categorias` (nombre_categoria) VALUES 
('Música'), ('Tecnología'), ('Gastronomía'), ('Arte'), ('Bienestar');

/* =============================================
   2. TABLA: USUARIOS
   ============================================= */
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `usuarios` VALUES 
(1,'Matias Obed','Peñaherrera Gonzalez','matiaspg@gmail.com','0923244434','$2y$10$0DPgb7VkiPUE6Z2VNpHhm.vK9sojJGU7mI.I6aqjrtqJvH4edzogq','proveedor','2025-11-30 03:21:08','Mi historia empieza en esta universidad','https://matiaspg.com'),
(2,'Carlos Marcos','Zambrano Bravo','carlos@gmail.com','0944122234','$2y$10$z5lTdsRPbW9ROQXpkqr/0Oo7PXWtrWHRqlbKWcUeM88uZusiCQ8cW','cliente','2025-11-30 03:59:25',NULL,NULL),
(3,'Alejandro José','Castillo López','alcastillo@gmail.com','0973724438','$2y$10$AopTe21r3wDHs1UmJCZvxenT3i3QMq.yJACmjC19BwKlnLw.N6Mju','proveedor','2025-12-03 04:24:25',NULL,NULL);

/* =============================================
   3. TABLA: SERVICIOS (Modificada con tus requisitos)
   ============================================= */
CREATE TABLE `servicios` (
  `servicio_id` int NOT NULL AUTO_INCREMENT,
  `proveedor_id` int DEFAULT NULL,
  `categoria_id` int DEFAULT NULL, /* Agregado directamente */
  `nombre_servicio` varchar(150) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `disponible` tinyint(1) DEFAULT '1',
  `cupo_maximo` int DEFAULT '5', /* Modificado default a 5 */
  `cupos_restantes` int DEFAULT '5', /* Columna nueva agregada aquí */
  `modalidad` varchar(50) DEFAULT 'Presencial', /* NUEVO: Modalidad */
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `horario` varchar(255) DEFAULT NULL, /* Guardaremos opciones separadas por coma */
  `politicas` text,
  PRIMARY KEY (`servicio_id`),
  KEY `proveedor_id` (`proveedor_id`),
  KEY `fk_servicio_categoria` (`categoria_id`),
  CONSTRAINT `servicios_ibfk_1` FOREIGN KEY (`proveedor_id`) REFERENCES `usuarios` (`usuario_id`),
  CONSTRAINT `fk_servicio_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`categoria_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Datos de servicios (Cupos ajustados entre 3 y 5, Modalidad Presencial, Horarios separados)
INSERT INTO `servicios` 
(servicio_id, proveedor_id, categoria_id, nombre_servicio, descripcion, precio, disponible, cupo_maximo, cupos_restantes, modalidad, fecha_inicio, fecha_fin, horario, politicas) 
VALUES 
(1, 4, 1, 'Clase de Piano Avanzado', 'Taller intensivo de armonía y técnica avanzada.', 65.00, 1, 3, 3, 'Presencial', '2026-01-05', '2026-02-16', 'Mañana (10:00 - 12:00),Tarde (15:00 - 17:00)', ''),
(2, 4, 2, 'Consultoría SEO Principiantes', 'Aprende a optimizar tu sitio web para Google.', 40.00, 1, 5, 5, 'Presencial', '2026-01-26', '2026-02-16', 'Mañana (09:00 - 11:00)', ''),
(3, 4, 5, 'Masaje Relajante Express', 'Sesión anti-estrés de 30 minutos.', 25.00, 1, 4, 4, 'Presencial', '2025-12-15', '2025-12-29', 'Tarde (16:00 - 16:30),Tarde (17:00 - 17:30)', ''),
(4, 4, 4, 'Curso Completo de Pintura', 'Técnicas al óleo, acrílico y acuarela.', 90.00, 0, 5, 0, 'Presencial', '2026-03-02', '2026-04-06', 'Mañana (08:00 - 11:00)', ''),
(5, 4, 3, 'Curso de Cocina Mediterránea', 'Aprende a preparar platos saludables.', 55.00, 1, 5, 5, 'Presencial', '2025-12-15', '2026-02-09', 'Mañana (10:00 - 13:00),Tarde (16:00 - 19:00)', ''),
(6, 2, 3, 'Curso de cocina 3', 'Gastronomía profesional.', 45.00, 1, 3, 3, 'Presencial', '2025-12-07', '2026-01-11', 'Mañana (09:00 - 12:00)', ''),
(7, 2, 3, 'Curso Pastelería 1', 'Pasteles de distintos sabores y colores.', 18.00, 1, 5, 5, 'Presencial', '2025-12-01', '2026-01-05', 'Mañana (08:00 - 12:00),Tarde (14:00 - 18:00)', ''),
(8, 2, 5, 'Curso de Yoga y Meditación', 'Encuentra tu paz interior.', 35.00, 1, 4, 4, 'Presencial', '2025-12-31', '2026-01-21', 'Mañana (06:00 - 07:00),Tarde (18:00 - 19:00)', '');

/* =============================================
   4. TABLA: RESERVAS
   ============================================= */
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `reservas` (reserva_id, usuario_id, servicio_id, fecha_reserva, total_pagar, estado, horario_elegido) VALUES 
(1,3,9,'2025-12-01',18.00,'pagada', 'Mañana (08:00 - 12:00)'),
(2,3,14,'2025-12-31',55.00,'pendiente', 'Tarde (18:00 - 19:00)'),
(3,3,9,'2025-12-01',18.00,'pagada', 'Tarde (14:00 - 18:00)');

/* =============================================
   5. TABLA: PAGOS
   ============================================= */
CREATE TABLE `pagos` (
  `pago_id` int NOT NULL AUTO_INCREMENT,
  `reserva_id` int DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `estado_pago` enum('aprobado','rechazado','pendiente') DEFAULT NULL,
  PRIMARY KEY (`pago_id`),
  KEY `reserva_id` (`reserva_id`),
  CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`reserva_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pagos` VALUES 
(1,4,18.00,'tarjeta','aprobado'),
(2,9,18.00,'tarjeta','aprobado');

/* =============================================
   6. TABLA: CONFIGURACIÓN
   ============================================= */
CREATE TABLE `configuracion` (
  `id` int NOT NULL DEFAULT 1,
  `nombre_sitio` varchar(100) DEFAULT 'ProServicios',
  `email_admin` varchar(150) DEFAULT 'admin@proservicios.com',
  `tasa_impuesto` decimal(5,2) DEFAULT 15.00,
  `moneda` varchar(5) DEFAULT '$',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `configuracion` (id, nombre_sitio, email_admin, tasa_impuesto, moneda)
VALUES (1, 'ProServicios', 'admin@proservicios.com', 15.00, '$')
ON DUPLICATE KEY UPDATE id=1; 

SET FOREIGN_KEY_CHECKS = 1;