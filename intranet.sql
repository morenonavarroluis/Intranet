-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-10-2026 a las 22:53:08
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
-- Estructura de tabla para la tabla `areas`
--

CREATE TABLE `areas` (
  `id_area` int(11) NOT NULL,
  `nombre_area` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `areas`
--

INSERT INTO `areas` (`id_area`, `nombre_area`) VALUES
(1, 'Presidencia'),
(2, 'Proyecto'),
(3, 'Consultoria Juridica'),
(4, 'Planificación y Presupuesto'),
(5, 'Gestion Humana'),
(6, 'Procura'),
(7, 'Administración y Finanzas'),
(8, 'Tic'),
(9, 'Atencion al ciudadano'),
(10, 'Comercializacion'),
(11, 'Seguridad'),
(12, 'Seguridad Integral'),
(13, 'Producción');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_conversaciones`
--

CREATE TABLE `chat_conversaciones` (
  `id_conversacion` int(11) NOT NULL,
  `id_usuario_1` int(11) NOT NULL,
  `id_usuario_2` int(11) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultima_actividad` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `chat_conversaciones`
--

INSERT INTO `chat_conversaciones` (`id_conversacion`, `id_usuario_1`, `id_usuario_2`, `fecha_creacion`, `ultima_actividad`) VALUES
(1, 1, 16, '2026-10-08 20:44:03', '2026-10-08 20:49:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `chat_mensajes`
--

CREATE TABLE `chat_mensajes` (
  `id_mensaje` int(11) NOT NULL,
  `id_conversacion` int(11) NOT NULL,
  `id_emisor` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT 0,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `chat_mensajes`
--

INSERT INTO `chat_mensajes` (`id_mensaje`, `id_conversacion`, `id_emisor`, `mensaje`, `leido`, `fecha`) VALUES
(1, 1, 1, 'hola como estas', 1, '2026-10-08 20:44:03'),
(2, 1, 16, 'bien y tu', 1, '2026-10-08 20:49:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes`
--

CREATE TABLE `imagenes` (
  `cod_imagen` int(11) NOT NULL,
  `imagen` varchar(255) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `comentario` text DEFAULT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `IDDATOS` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `imagenes`
--

INSERT INTO `imagenes` (`cod_imagen`, `imagen`, `nombre`, `comentario`, `fecha_publicacion`, `IDDATOS`) VALUES
(1, 'noticia1.jpg', 'Nueva Canaima 2024', 'Lanzamiento de la nueva Canaima con mejoras en el sistema.', '2026-10-08 20:08:36', 1),
(2, 'noticia2.jpg', 'Capacitación en Linux', 'Taller de fundamentos de Linux para el personal.', '2026-10-08 20:08:36', 1),
(3, 'noticia3.jpg', 'Mantenimiento del Sistema', 'Se informa a todo el personal que el día sábado 15 de marzo se realizará mantenimiento programado al servidor principal. El sistema estará fuera de servicio de 8:00 AM a 2:00 PM.', '2024-03-05 18:00:00', 6),
(4, 'noticia4.jpg', 'Nuevos Equipos Adquiridos', 'La institución ha recibido un lote de 50 nuevas computadoras Canaima que serán distribuidas entre las diferentes áreas según las necesidades detectadas.', '2024-03-20 15:00:00', 1),
(5, 'noticia5.jpg', 'Jornada de Vacunación', 'Se invita a todo el personal a participar en la jornada de vacunación que se realizará en las instalaciones de la empresa el próximo 10 de abril.', '2024-04-01 12:00:00', 4),
(6, 'noticia6.jpg', 'Actualización de Contraseñas', 'Por políticas de seguridad, todos los usuarios deben actualizar sus contraseñas antes del 30 de abril. La nueva contraseña debe tener mínimo 8 caracteres.', '2024-04-15 20:00:00', 3),
(7, 'noticia7.jpg', 'Día del Trabajador', 'La institución felicita a todos sus trabajadores en su día. Se realizará un acto conmemorativo el 1 de mayo a las 10:00 AM en el auditorio principal.', '2024-04-30 13:00:00', 1),
(8, 'noticia8.jpg', 'Nuevo Sistema de Solicitudes', 'Ya está disponible el nuevo módulo de solicitudes de soporte técnico. Los usuarios pueden ahora dar seguimiento en tiempo real a sus requerimientos.', '2024-05-10 16:00:00', 12);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `niveles`
--

CREATE TABLE `niveles` (
  `id_nivel` int(11) NOT NULL,
  `nombre_nivel` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `niveles`
--

INSERT INTO `niveles` (`id_nivel`, `nombre_nivel`) VALUES
(1, 'Alta'),
(2, 'Media'),
(3, 'Baja');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos`
--

CREATE TABLE `permisos` (
  `IDPERMISO` int(11) NOT NULL,
  `NOMBRE` varchar(100) NOT NULL,
  `SLUG` varchar(100) NOT NULL,
  `MODULO` varchar(50) NOT NULL,
  `DESCRIPCION` varchar(255) DEFAULT NULL,
  `ACTIVO` tinyint(1) NOT NULL DEFAULT 1,
  `FECHA_CREACION` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos`
--

INSERT INTO `permisos` (`IDPERMISO`, `NOMBRE`, `SLUG`, `MODULO`, `DESCRIPCION`, `ACTIVO`, `FECHA_CREACION`) VALUES
(1, 'Ver dashboard', 'dashboard.ver', 'dashboard', NULL, 1, '2026-10-08 20:14:31'),
(2, 'Ver estadísticas globales', 'dashboard.stats', 'dashboard', NULL, 1, '2026-10-08 20:14:31'),
(3, 'Ver notificaciones', 'dashboard.notif', 'dashboard', NULL, 1, '2026-10-08 20:14:31'),
(4, 'Ver usuarios', 'usuarios.ver', 'usuarios', NULL, 1, '2026-10-08 20:14:31'),
(5, 'Crear usuarios', 'usuarios.crear', 'usuarios', NULL, 1, '2026-10-08 20:14:31'),
(6, 'Editar usuarios', 'usuarios.editar', 'usuarios', NULL, 1, '2026-10-08 20:14:31'),
(7, 'Eliminar usuarios', 'usuarios.eliminar', 'usuarios', NULL, 1, '2026-10-08 20:14:31'),
(8, 'Cambiar contraseña', 'usuarios.password', 'usuarios', NULL, 1, '2026-10-08 20:14:31'),
(9, 'Asignar roles', 'usuarios.roles', 'usuarios', NULL, 1, '2026-10-08 20:14:31'),
(10, 'Crear solicitud soporte', 'soporte.crear', 'soporte', NULL, 1, '2026-10-08 20:14:31'),
(11, 'Ver propias solicitudes', 'soporte.ver_propias', 'soporte', NULL, 1, '2026-10-08 20:14:31'),
(12, 'Ver todas las solicitudes', 'soporte.ver_todas', 'soporte', NULL, 1, '2026-10-08 20:14:31'),
(13, 'Atender solicitudes', 'soporte.atender', 'soporte', NULL, 1, '2026-10-08 20:14:31'),
(14, 'Asignar prioridad', 'soporte.prioridad', 'soporte', NULL, 1, '2026-10-08 20:14:31'),
(15, 'Exportar casos', 'soporte.exportar', 'soporte', NULL, 1, '2026-10-08 20:14:31'),
(16, 'Ver noticias', 'noticias.ver', 'noticias', NULL, 1, '2026-10-08 20:14:31'),
(17, 'Crear noticias', 'noticias.crear', 'noticias', NULL, 1, '2026-10-08 20:14:31'),
(18, 'Editar noticias', 'noticias.editar', 'noticias', NULL, 1, '2026-10-08 20:14:31'),
(19, 'Eliminar noticias', 'noticias.eliminar', 'noticias', NULL, 1, '2026-10-08 20:14:31'),
(20, 'Ver propio perfil', 'perfil.ver', 'perfil', NULL, 1, '2026-10-08 20:14:31'),
(21, 'Editar propio perfil', 'perfil.editar', 'perfil', NULL, 1, '2026-10-08 20:14:31'),
(22, 'Cambiar propia contraseña', 'perfil.password', 'perfil', NULL, 1, '2026-10-08 20:14:31'),
(23, 'Subir foto de perfil', 'perfil.foto', 'perfil', NULL, 1, '2026-10-08 20:14:31'),
(24, 'Descargar planillas', 'recursos.descargar', 'recursos', NULL, 1, '2026-10-08 20:14:31'),
(25, 'Ver biblioteca digital', 'recursos.biblioteca', 'recursos', NULL, 1, '2026-10-08 20:14:31'),
(26, 'Solicitar constancia', 'recursos.constancia', 'recursos', NULL, 1, '2026-10-08 20:14:31'),
(27, 'Ver recibo de pago', 'recursos.recibo', 'recursos', NULL, 1, '2026-10-08 20:14:31'),
(28, 'Ver reportes', 'reportes.ver', 'reportes', NULL, 1, '2026-10-08 20:14:31'),
(29, 'Exportar reportes', 'reportes.exportar', 'reportes', NULL, 1, '2026-10-08 20:14:31'),
(30, 'Ver estadísticas', 'reportes.stats', 'reportes', NULL, 1, '2026-10-08 20:14:31'),
(31, 'Configurar sistema', 'sistema.config', 'sistema', NULL, 1, '2026-10-08 20:14:31'),
(32, 'Ver logs', 'sistema.logs', 'sistema', NULL, 1, '2026-10-08 20:14:31'),
(33, 'Gestionar roles', 'sistema.roles', 'sistema', NULL, 1, '2026-10-08 20:14:31'),
(34, 'Respaldar BD', 'sistema.backup', 'sistema', NULL, 1, '2026-10-08 20:14:31'),
(69, 'Usar chat interno', 'chat.usar', 'chat', 'Acceder al chat con otros usuarios', 1, '2026-10-08 20:41:29'),
(70, 'Chat grupal', 'chat.grupal', 'chat', 'Crear y participar en chats grupales', 1, '2026-10-08 20:41:29'),
(71, 'Ver historial completo', 'chat.historial', 'chat', 'Ver historial completo del chat', 1, '2026-10-08 20:41:29'),
(72, 'Eliminar mensajes', 'chat.eliminar', 'chat', 'Eliminar mensajes propios o ajenos', 1, '2026-10-08 20:41:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `report`
--

CREATE TABLE `report` (
  `ID_REPORT` int(11) NOT NULL,
  `TITLE` varchar(255) NOT NULL,
  `name_surname` varchar(200) DEFAULT NULL,
  `area` int(11) DEFAULT NULL,
  `ID_NAME` int(11) DEFAULT NULL,
  `CREATION_DATE` date DEFAULT NULL,
  `DATE_FINAL` date DEFAULT NULL,
  `FECHA_SOLUTION` date DEFAULT NULL,
  `STATUS` int(11) NOT NULL DEFAULT 3,
  `ID_LEVEL` int(11) DEFAULT 3,
  `SOLUTION` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `report`
--

INSERT INTO `report` (`ID_REPORT`, `TITLE`, `name_surname`, `area`, `ID_NAME`, `CREATION_DATE`, `DATE_FINAL`, `FECHA_SOLUTION`, `STATUS`, `ID_LEVEL`, `SOLUTION`) VALUES
(1, 'La computadora no enciende después de un corte eléctrico', 'Juan Perez', 13, 2, '2024-05-01', NULL, NULL, 3, 1, NULL),
(2, 'El monitor muestra pantalla azul al iniciar sesión', 'Ana Rodriguez', 7, 5, '2024-05-02', NULL, NULL, 3, 1, NULL),
(3, 'No puedo acceder al sistema de intranet', 'Pedro Gomez', 3, 7, '2024-05-03', NULL, NULL, 3, 2, NULL),
(4, 'Impresora no imprime documentos', 'Laura Fernandez', 5, 8, '2024-04-28', NULL, NULL, 2, 3, NULL),
(5, 'Problemas con el correo institucional', 'Roberto Diaz', 6, 9, '2024-04-29', NULL, NULL, 2, 2, NULL),
(6, 'Instalación de software de diseño', 'Martha Rojas', 4, 13, '2024-04-30', NULL, NULL, 2, 3, NULL),
(7, 'Cambio de teclado dañado', 'Jose Morales', 11, 10, '2024-04-15', '2024-04-16', '2024-04-16', 4, 3, 'Se reemplazó el teclado por uno nuevo. Equipo funcionando correctamente.'),
(8, 'Recuperación de archivos borrados', 'Carmen Silva', 9, 11, '2024-04-10', '2024-04-12', '2024-04-12', 4, 2, 'Se recuperaron los archivos desde el backup del servidor. Se recomendó al usuario guardar copias de seguridad periódicas.'),
(9, 'Configuración de red inalámbrica', 'Daniel Castillo', 8, 12, '2024-04-05', '2024-04-06', '2024-04-06', 4, 3, 'Se configuró la conexión WiFi corporativa en el equipo del usuario.'),
(10, 'Reemplazo de disco duro', 'Fernando Herrera', 10, 14, '2024-03-20', '2024-03-22', '2024-03-22', 4, 1, 'Se reemplazó el disco duro por uno SSD de 500GB. Se migró toda la información del usuario.'),
(11, 'Actualización de sistema operativo', 'Gabriela Vargas', 12, 15, '2024-03-15', '2024-03-18', '2024-03-18', 4, 2, 'Se actualizó el sistema operativo a la última versión estable de Canaima.'),
(12, 'Falla en el sistema de sonido', 'Juan Perez', 13, 2, '2024-03-01', '2024-03-03', '2024-03-03', 5, 3, 'Se reinstalaron los controladores de audio. Problema resuelto.'),
(13, 'Configuración de VPN', 'Ana Rodriguez', 7, 5, '2024-02-20', '2024-02-21', '2024-02-21', 5, 2, 'Se configuró el acceso VPN para trabajo remoto.'),
(14, 'Solicitud de equipo nuevo', 'Pedro Gomez', 3, 7, '2024-02-10', NULL, '2024-02-11', 6, 3, 'Solicitud rechazada: el equipo actual cumple con los requisitos necesarios. Se recomienda optimizar el uso de recursos.'),
(15, 'Error al generar constancia de trabajo', 'Laura Fernandez', 5, 8, '2024-05-04', NULL, NULL, 3, 1, NULL),
(16, 'Olvidé mi contraseña del sistema', 'Roberto Diaz', 6, 9, '2024-05-05', NULL, NULL, 3, 2, NULL),
(17, 'La impresora imprime con líneas', 'Martha Rojas', 4, 13, '2024-05-06', NULL, NULL, 3, 3, NULL),
(18, 'No tengo acceso a la carpeta compartida', 'Jose Morales', 11, 10, '2024-05-07', NULL, NULL, 3, 2, NULL),
(19, 'El mouse no responde correctamente', 'Carmen Silva', 9, 11, '2024-05-08', NULL, NULL, 3, 3, NULL),
(20, 'Problemas con el sistema de respaldo', 'Daniel Castillo', 8, 12, '2024-05-09', NULL, NULL, 3, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `IDROLS` int(11) NOT NULL,
  `NOMBRE_ROL` varchar(50) NOT NULL,
  `SLUG` varchar(50) NOT NULL,
  `DESCRIPCION` varchar(255) DEFAULT NULL,
  `NIVEL` int(11) NOT NULL DEFAULT 10,
  `COLOR` varchar(20) DEFAULT '#667eea',
  `ICONO` varchar(50) DEFAULT 'bi-person',
  `ACTIVO` tinyint(1) NOT NULL DEFAULT 1,
  `FECHA_CREACION` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permiso`
--

CREATE TABLE `rol_permiso` (
  `IDROL` int(11) NOT NULL,
  `IDPERMISO` int(11) NOT NULL,
  `FECHA_ASIGNACION` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `status_report`
--

CREATE TABLE `status_report` (
  `id_status` int(11) NOT NULL,
  `nombre_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `status_report`
--

INSERT INTO `status_report` (`id_status`, `nombre_status`) VALUES
(1, 'Pendiente'),
(2, 'En Proceso'),
(3, 'Enviado'),
(4, 'Resuelto'),
(5, 'Cerrado'),
(6, 'Rechazado');

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
(1, 'admin', '$2y$10$r67HI1R20GPL9bynX45Fn.2uHR4.pCEs40WLf5gjRSXqlKIvGJhyu', 'Nelson', 'Sistema', 'V-12345678', 'admin@canaima.gob.ve', '0212-2344176', 'Tic', 1, 'images/Canaima.png', '2026-10-08 20:08:36'),
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
(16, 'Manuel', '$2y$10$71XwgP9Q3xShb9UVzOKRu.P.FOKCJIcznSPC4J7GEJrI/YI./osGi', 'manuel', 'navarro', '31158004', 'manuel@gmail.com', '04241871113', 'Presidencia', 1, 'images/usuarios/user_1791490884_8620.jpg', '2026-10-08 20:21:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_permiso_extra`
--

CREATE TABLE `usuario_permiso_extra` (
  `IDDATOS` int(11) NOT NULL,
  `IDPERMISO` int(11) NOT NULL,
  `TIPO` enum('grant','deny') NOT NULL DEFAULT 'grant',
  `FECHA` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id_area`);

--
-- Indices de la tabla `chat_conversaciones`
--
ALTER TABLE `chat_conversaciones`
  ADD PRIMARY KEY (`id_conversacion`),
  ADD UNIQUE KEY `conv_unica` (`id_usuario_1`,`id_usuario_2`),
  ADD KEY `user1_idx` (`id_usuario_1`),
  ADD KEY `user2_idx` (`id_usuario_2`);

--
-- Indices de la tabla `chat_mensajes`
--
ALTER TABLE `chat_mensajes`
  ADD PRIMARY KEY (`id_mensaje`),
  ADD KEY `conv_idx` (`id_conversacion`),
  ADD KEY `emisor_idx` (`id_emisor`);

--
-- Indices de la tabla `imagenes`
--
ALTER TABLE `imagenes`
  ADD PRIMARY KEY (`cod_imagen`),
  ADD KEY `IDDATOS_FK` (`IDDATOS`);

--
-- Indices de la tabla `niveles`
--
ALTER TABLE `niveles`
  ADD PRIMARY KEY (`id_nivel`);

--
-- Indices de la tabla `permisos`
--
ALTER TABLE `permisos`
  ADD PRIMARY KEY (`IDPERMISO`),
  ADD UNIQUE KEY `SLUG_UNIQUE` (`SLUG`);

--
-- Indices de la tabla `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`ID_REPORT`),
  ADD KEY `ID_NAME_FK` (`ID_NAME`),
  ADD KEY `STATUS_FK` (`STATUS`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`IDROLS`),
  ADD UNIQUE KEY `SLUG_UNIQUE` (`SLUG`);

--
-- Indices de la tabla `rol_permiso`
--
ALTER TABLE `rol_permiso`
  ADD PRIMARY KEY (`IDROL`,`IDPERMISO`),
  ADD KEY `fk_rp_permiso` (`IDPERMISO`);

--
-- Indices de la tabla `status_report`
--
ALTER TABLE `status_report`
  ADD PRIMARY KEY (`id_status`);

--
-- Indices de la tabla `user_datos`
--
ALTER TABLE `user_datos`
  ADD PRIMARY KEY (`IDDATOS`),
  ADD UNIQUE KEY `USER_UNIQUE` (`USER`),
  ADD UNIQUE KEY `CEDULA_UNIQUE` (`CEDULA`),
  ADD KEY `IDROLS_FK` (`IDROLS`);

--
-- Indices de la tabla `usuario_permiso_extra`
--
ALTER TABLE `usuario_permiso_extra`
  ADD PRIMARY KEY (`IDDATOS`,`IDPERMISO`),
  ADD KEY `fk_upe_permiso` (`IDPERMISO`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `areas`
--
ALTER TABLE `areas`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `chat_conversaciones`
--
ALTER TABLE `chat_conversaciones`
  MODIFY `id_conversacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `chat_mensajes`
--
ALTER TABLE `chat_mensajes`
  MODIFY `id_mensaje` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `imagenes`
--
ALTER TABLE `imagenes`
  MODIFY `cod_imagen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `niveles`
--
ALTER TABLE `niveles`
  MODIFY `id_nivel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `permisos`
--
ALTER TABLE `permisos`
  MODIFY `IDPERMISO` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT de la tabla `report`
--
ALTER TABLE `report`
  MODIFY `ID_REPORT` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `IDROLS` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `status_report`
--
ALTER TABLE `status_report`
  MODIFY `id_status` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `user_datos`
--
ALTER TABLE `user_datos`
  MODIFY `IDDATOS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `rol_permiso`
--
ALTER TABLE `rol_permiso`
  ADD CONSTRAINT `fk_rp_permiso` FOREIGN KEY (`IDPERMISO`) REFERENCES `permisos` (`IDPERMISO`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rp_rol` FOREIGN KEY (`IDROL`) REFERENCES `roles` (`IDROLS`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuario_permiso_extra`
--
ALTER TABLE `usuario_permiso_extra`
  ADD CONSTRAINT `fk_upe_permiso` FOREIGN KEY (`IDPERMISO`) REFERENCES `permisos` (`IDPERMISO`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_upe_user` FOREIGN KEY (`IDDATOS`) REFERENCES `user_datos` (`IDDATOS`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
