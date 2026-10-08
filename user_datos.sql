-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-10-2026 a las 22:30:59
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `intranet`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_datos`
--

CREATE TABLE `user_datos` (
  `IDDATOS` int(11) NOT NULL,
  `USER` varchar(50) NOT NULL,
  `PASSWORD` varchar(100) NOT NULL,
  `NAME` varchar(100) NOT NULL,
  `SURNAME` varchar(100) NOT NULL,
  `CEDULA` varchar(20) NOT NULL,
  `EMAIL` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `ASSIGNED_AREA` varchar(100) DEFAULT NULL,
  `IDROLS` int(11) NOT NULL DEFAULT 2,
  `foto` varchar(255) DEFAULT 'images/default.png',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `user_datos`
--

INSERT INTO `user_datos` (`IDDATOS`, `USER`, `PASSWORD`, `NAME`, `SURNAME`, `CEDULA`, `EMAIL`, `telefono`, `ASSIGNED_AREA`, `IDROLS`, `foto`, `fecha_registro`) VALUES
(1, 'admin', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Nelson', 'Sistema', 'V-12345678', 'admin@canaima.gob.ve', '0212-2344176', 'Tic', 1, 'images/Canaima.png', '2026-10-08 20:08:36'),
(2, 'usuario', 'f74be0f8d6a5b5b6b4f0c5b0e8e9a9a0e8e9a9a0', 'Juan', 'Perez', 'V-23456789', 'jperez@canaima.gob.ve', '0414-1234567', 'Producción', 2, 'images/Canaima.png', '2026-10-08 20:08:36'),
(3, 'tecnico', 'f74be0f8d6a5b5b6b4f0c5b0e8e9a9a0e8e9a9a0', 'Carlos', 'Lopez', 'V-34567890', 'clopez@canaima.gob.ve', '0424-7654321', 'Tic', 3, 'images/Canaima.png', '2026-10-08 20:08:36'),
(4, 'rrhh', 'f74be0f8d6a5b5b6b4f0c5b0e8e9a9a0e8e9a9a0', 'Maria', 'Gonzalez', 'V-45678901', 'mgonzalez@canaima.gob.ve', '0412-9876543', 'Gestion Humana', 4, 'images/Canaima.png', '2026-10-08 20:08:36'),
(5, 'arodriguez', 'f74be0f8d6a5b5b6b4f0c5b0e8e9a9a0e8e9a9a0', 'Ana', 'Rodriguez', 'V-56789012', 'arodriguez@canaima.gob.ve', '0416-5556677', 'Administración y Finanzas', 2, 'images/Canaima.png', '2026-10-08 20:09:02'),
(6, 'lmartinez', 'f74be0f8d6a5b5b6b4f0c5b0e8e9a9a0e8e9a9a0', 'Luis', 'Martinez', 'V-67890123', 'lmartinez@canaima.gob.ve', '0426-8889900', 'Tic', 3, 'images/Canaima.png', '2026-10-08 20:09:02'),
(7, 'pgomez', 'f74be0f8d6a5b5b6b4f0c5b0e8e9a9a0e8e9a9a0', 'Pedro', 'Gomez', 'V-78901234', 'pgomez@canaima.gob.ve', '0414-2223344', 'Consultoria Juridica', 2, 'images/Canaima.png', '2026-10-08 20:09:02'),
(8, 'lfernandez', 'f74be0f8d6a5b5b6b4f0c5b0e8e9a9a0e8e9a9a0', 'Laura', 'Fernandez', 'V-89012345', 'lfernandez@canaima.gob.ve', '0424-1112233', 'Gestion Humana', 4, 'images/Canaima.png', '2026-10-08 20:09:02'),
(9, 'rdiaz', 'f74be0f8d6a5b5b6b4f0c5b0e8e9a9a0e8e9a9a0', 'Roberto', 'Diaz', 'V-90123456', 'rdiaz@canaima.gob.ve', '0412-4445566', 'Procura', 2, 'images/Canaima.png', '2026-10-08 20:09:02'),
(10, 'jmorales', 'f74be0f8d6a5b5b6b4f0c5b0e8e9a9a0e8e9a9a0', 'Jose', 'Morales', 'V-01234567', 'jmorales@canaima.gob.ve', '0416-7778899', 'Seguridad', 2, 'images/Canaima.png', '2026-10-08 20:09:02'),
(11, 'csilva', 'f74be0f8d6a5b5b6b4f0c5b0e8e9a9a0e8e9a9a0', 'Carmen', 'Silva', 'V-11223344', 'csilva@canaima.gob.ve', '0426-3334455', 'Atencion al ciudadano', 2, 'images/Canaima.png', '2026-10-08 20:09:02'),
(12, 'dcastillo', 'f74be0f8d6a5b5b6b4f0c5b0e8e9a9a0e8e9a9a0', 'Daniel', 'Castillo', 'V-22334455', 'dcastillo@canaima.gob.ve', '0414-9990011', 'Tic', 3, 'images/Canaima.png', '2026-10-08 20:09:02'),
(13, 'mrojas', 'f74be0f8d6a5b5b6b4f0c5b0e8e9a9a0e8e9a9a0', 'Martha', 'Rojas', 'V-33445566', 'mrojas@canaima.gob.ve', '0412-6667788', 'Planificación y Presupuesto', 2, 'images/Canaima.png', '2026-10-08 20:09:02'),
(14, 'fherrera', 'f74be0f8d6a5b5b6b4f0c5b0e8e9a9a0e8e9a9a0', 'Fernando', 'Herrera', 'V-44556677', 'fherrera@canaima.gob.ve', '0424-2221133', 'Comercializacion', 2, 'images/Canaima.png', '2026-10-08 20:09:02'),
(15, 'gvargas', 'f74be0f8d6a5b5b6b4f0c5b0e8e9a9a0e8e9a9a0', 'Gabriela', 'Vargas', 'V-55667788', 'gvargas@canaima.gob.ve', '0416-4443322', 'Seguridad Integral', 2, 'images/Canaima.png', '2026-10-08 20:09:02'),
(16, 'Manuel', '$2y$10$71XwgP9Q3xShb9UVzOKRu.P.FOKCJIcznSPC4J7GEJrI/YI./osGi', 'manuel', 'navarro', '31158004', 'manuel@gmail.com', '04241871113', 'Presidencia', 2, 'images/usuarios/user_1791490884_8620.jpg', '2026-10-08 20:21:24');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `user_datos`
--
ALTER TABLE `user_datos`
  ADD PRIMARY KEY (`IDDATOS`),
  ADD UNIQUE KEY `USER_UNIQUE` (`USER`),
  ADD UNIQUE KEY `CEDULA_UNIQUE` (`CEDULA`),
  ADD KEY `IDROLS_FK` (`IDROLS`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `user_datos`
--
ALTER TABLE `user_datos`
  MODIFY `IDDATOS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
