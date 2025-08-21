-- database_setup.sql
-- Script SQL para crear la base de datos y tablas de Cremería Raíz
-- Compatible con MySQL 5.7+ y MariaDB 10.2+

-- Crear la base de datos (opcional si ya está creada en Hostinger)
-- CREATE DATABASE IF NOT EXISTS cremeria_raiz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE cremeria_raiz;

-- Tabla de usuarios para el sistema de administración
CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL UNIQUE,
    `password_hash` varchar(255) NOT NULL,
    `email` varchar(100) DEFAULT NULL,
    `full_name` varchar(100) DEFAULT NULL,
    `role` enum('admin','manager','editor') DEFAULT 'admin',
    `is_active` tinyint(1) DEFAULT 1,
    `last_login` datetime DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_username` (`username`),
    INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de productos para la cremería
CREATE TABLE IF NOT EXISTS `productos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nombre` varchar(100) NOT NULL,
    `descripcion` text NOT NULL,
    `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
    `imagen_url` varchar(500) DEFAULT NULL,
    `categoria` varchar(50) DEFAULT 'Quesos',
    `peso` varchar(20) DEFAULT NULL,
    `ingredientes` text DEFAULT NULL,
    `informacion_nutricional` text DEFAULT NULL,
    `disponible` tinyint(1) DEFAULT 1,
    `destacado` tinyint(1) DEFAULT 0,
    `orden_visualizacion` int(11) DEFAULT 0,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_categoria` (`categoria`),
    INDEX `idx_disponible` (`disponible`),
    INDEX `idx_destacado` (`destacado`),
    INDEX `idx_orden` (`orden_visualizacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar usuario administrador por defecto
-- Contraseña: admin123 (cambiar en producción)
INSERT INTO `users` (`username`, `password_hash`, `email`, `full_name`, `role`, `is_active`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@cremeriaraiz.com', 'Administrador Principal', 'admin', 1)
ON DUPLICATE KEY UPDATE 
`password_hash` = VALUES(`password_hash`),
`updated_at` = CURRENT_TIMESTAMP;

-- Insertar productos de ejemplo
INSERT INTO `productos` (`nombre`, `descripcion`, `precio`, `imagen_url`, `categoria`, `peso`, `disponible`, `destacado`, `orden_visualizacion`) VALUES
('Queso Oaxaca Tradicional', 'El clásico queso de hebra oaxaqueño, perfecto para quesadillas y tlayudas. Elaborado con técnicas ancestrales y leche fresca de la región.', 85.00, 'images/queso-oaxaca.jpg', 'Quesos', '400g', 1, 1, 1),
('Queso Fresco de Rancho', 'Suave y cremoso, elaborado con leche fresca de vacas alimentadas naturalmente. Ideal para desayunos y comidas ligeras.', 65.00, 'images/queso-fresco.jpg', 'Quesos', '300g', 1, 1, 2),
('Requesón Artesanal', 'Delicado y nutritivo, ideal para postres y preparaciones dulces tradicionales. Rico en proteínas y bajo en grasa.', 45.00, 'images/queso-requesón.jpg', 'Quesos', '250g', 1, 0, 3),
('Queso Crema Premium', 'Textura suave y sabor intenso, perfecto para untar y cocinar. Elaborado con crema fresca de la más alta calidad.', 95.00, 'images/queso-crema.jpg', 'Quesos', '200g', 1, 1, 4),
('Manchego Oaxaqueño', 'Madurado con técnicas tradicionales, de sabor profundo y textura firme. Perfecto para tablas de quesos y maridajes.', 120.00, 'images/queso-manchego.jpg', 'Quesos', '500g', 1, 0, 5),
('Doble Crema Especial', 'Extraordinariamente cremoso, el favorito de los conocedores del buen queso. Sabor único e inigualable.', 110.00, 'images/queso-doble-crema.jpg', 'Quesos', '350g', 1, 1, 6)
ON DUPLICATE KEY UPDATE 
`precio` = VALUES(`precio`),
`disponible` = VALUES(`disponible`),
`updated_at` = CURRENT_TIMESTAMP;

-- Crear índices adicionales para optimizar las consultas
CREATE INDEX IF NOT EXISTS `idx_productos_nombre` ON `productos` (`nombre`);
CREATE INDEX IF NOT EXISTS `idx_productos_precio` ON `productos` (`precio`);
CREATE INDEX IF NOT EXISTS `idx_productos_created` ON `productos` (`created_at`);
CREATE INDEX IF NOT EXISTS `idx_users_last_login` ON `users` (`last_login`);
CREATE INDEX IF NOT EXISTS `idx_users_created` ON `users` (`created_at`);

-- Crear vistas útiles (opcional)
CREATE OR REPLACE VIEW `productos_disponibles` AS
SELECT 
    `id`,
    `nombre`,
    `descripcion`,
    `precio`,
    `imagen_url`,
    `categoria`,
    `peso`,
    `destacado`,
    `created_at`
FROM `productos` 
WHERE `disponible` = 1 
ORDER BY `destacado` DESC, `orden_visualizacion` ASC, `nombre` ASC;

CREATE OR REPLACE VIEW `productos_destacados` AS
SELECT 
    `id`,
    `nombre`,
    `descripcion`,
    `precio`,
    `imagen_url`,
    `categoria`,
    `peso`,
    `created_at`
FROM `productos` 
WHERE `disponible` = 1 AND `destacado` = 1 
ORDER BY `orden_visualizacion` ASC, `nombre` ASC;

-- Crear procedimiento almacenado para obtener estadísticas (opcional)
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `GetDashboardStats`()
BEGIN
    DECLARE total_productos INT DEFAULT 0;
    DECLARE productos_disponibles INT DEFAULT 0;
    DECLARE productos_destacados INT DEFAULT 0;
    DECLARE precio_promedio DECIMAL(10,2) DEFAULT 0.00;
    
    SELECT COUNT(*) INTO total_productos FROM productos;
    SELECT COUNT(*) INTO productos_disponibles FROM productos WHERE disponible = 1;
    SELECT COUNT(*) INTO productos_destacados FROM productos WHERE disponible = 1 AND destacado = 1;
    SELECT AVG(precio) INTO precio_promedio FROM productos WHERE disponible = 1;
    
    SELECT 
        total_productos,
        productos_disponibles,
        productos_destacados,
        ROUND(precio_promedio, 2) as precio_promedio;
END//
DELIMITER ;

-- Configuración adicional de la base de datos
-- Establecer charset por defecto
ALTER DATABASE cremeria_raiz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Optimizaciones de performance
SET GLOBAL innodb_buffer_pool_size = 128M;
SET GLOBAL query_cache_size = 32M;
SET GLOBAL query_cache_type = 1;

-- Comentarios y documentación
-- Tabla users: Almacena los usuarios del sistema de administración
-- Tabla productos: Almacena la información de todos los productos de la cremería
-- Vista productos_disponibles: Solo productos activos y disponibles
-- Vista productos_destacados: Solo productos destacados en la página principal

-- NOTAS IMPORTANTES:
-- 1. Cambiar la contraseña del usuario 'admin' en producción
-- 2. Configurar las credenciales de base de datos en config.php
-- 3. Asegurarse de que las imágenes estén en el directorio 'images/'
-- 4. Realizar respaldos regulares de la base de datos
-- 5. Monitorear el performance de las consultas en producción

-- Verificación final
SELECT 'Base de datos configurada exitosamente' as status;
SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as total_productos FROM productos;

-- IMPORTANTE: Para generar el hash de una nueva contraseña en PHP usar:
-- password_hash('tu_nueva_password', PASSWORD_DEFAULT)