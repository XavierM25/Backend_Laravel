-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-08-2024 a las 02:25:09
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
-- Base de datos: `api_test`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`admin`@`localhost` PROCEDURE `generar_usuario_unico` (IN `nombre` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, IN `apellido_paterno` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, IN `apellido_materno` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, OUT `usuario` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci)   BEGIN
    DECLARE base_usuario VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    DECLARE contador INT DEFAULT 1;
    DECLARE longitud INT;

    SET base_usuario = CONCAT(
        LEFT(nombre, 1),
        apellido_paterno,
        LEFT(apellido_materno, 1)
    );

    SET longitud = LENGTH(base_usuario);

    WHILE longitud < 5 DO
        SET base_usuario = CONCAT(base_usuario, 'x');
        SET longitud = LENGTH(base_usuario);
    END WHILE;

    IF longitud > 10 THEN
        SET base_usuario = LEFT(base_usuario, 10);
    END IF;

    SET usuario = base_usuario;

    WHILE EXISTS (
        SELECT 1 FROM usuarios WHERE username = usuario
    ) DO
        SET contador = contador + 1;
        SET usuario = CONCAT(
            LEFT(base_usuario, 10 - LENGTH(contador)),
            contador
        );
    END WHILE;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Deportes', '2024-06-16 08:37:43', '2024-06-16 08:37:43'),
(2, 'Cultura y Eventos', '2024-06-16 08:38:02', '2024-06-16 08:38:02'),
(3, 'Talleres', '2024-06-16 08:38:10', '2024-06-16 08:38:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `habilidades`
--

CREATE TABLE `habilidades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `categoria_id` bigint(20) UNSIGNED NOT NULL,
  `subcategoria_id` bigint(20) UNSIGNED NOT NULL,
  `puntos` int(11) NOT NULL,
  `estado` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `habilidades`
--

INSERT INTO `habilidades` (`id`, `usuario_id`, `categoria_id`, `subcategoria_id`, `puntos`, `estado`, `created_at`, `updated_at`) VALUES
(11, 6, 3, 4, 1600, 'Estudiante Destacado', '2024-08-11 05:29:23', '2024-08-11 05:29:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subcategoria_id` bigint(20) UNSIGNED NOT NULL,
  `dia` varchar(255) NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`id`, `subcategoria_id`, `dia`, `hora_inicio`, `hora_fin`, `created_at`, `updated_at`) VALUES
(12, 2, 'Miércoles', '18:00:00', '20:00:00', '2024-06-19 18:14:06', '2024-06-19 18:14:06'),
(13, 6, 'Martes', '15:00:00', '16:00:00', '2024-06-19 18:14:22', '2024-06-19 18:14:22'),
(17, 1, 'Miércoles', '20:00:00', '22:00:00', '2024-06-19 18:45:23', '2024-06-19 18:45:23'),
(18, 7, 'Sábado', '07:30:00', '08:30:00', '2024-06-19 18:45:52', '2024-06-19 18:45:52'),
(19, 8, 'Sábado', '08:30:00', '09:45:00', '2024-06-19 18:46:08', '2024-06-19 18:46:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inscripciones`
--

CREATE TABLE `inscripciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `categoria_id` bigint(20) UNSIGNED NOT NULL,
  `subcategoria_id` bigint(20) UNSIGNED NOT NULL,
  `horario_id` bigint(20) UNSIGNED NOT NULL,
  `estado` enum('Aprobado','Pendiente','Rechazado') NOT NULL DEFAULT 'Pendiente',
  `numero_vacante` int(11) NOT NULL,
  `comentarios` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `likes`
--

CREATE TABLE `likes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `video_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_06_05_202838_create_roles_table', 1),
(2, '2024_06_05_205559_create_usuarios_table', 2),
(3, '2024_05_30_231149_create_personal_access_tokens_table', 3),
(4, '0001_01_01_000001_create_cache_table', 4),
(5, '0001_01_01_000002_create_jobs_table', 4),
(6, '2024_06_14_050059_add_google_id_to_usuarios_table', 5),
(7, '2024_06_16_021519_create_categorias_table', 6),
(8, '2024_06_16_021530_create_subcategorias_table', 7),
(9, '2024_06_16_021539_create_horarios_table', 8),
(10, '2024_06_17_235112_create_habilidades_table', 9),
(15, '2024_06_18_050958_create_videos_table', 10),
(19, '2024_06_18_160505_create_inscripciones_table', 11),
(21, '2024_06_22_154911_create_likes_table', 12),
(24, '2024_07_02_100405_create_reportes_table', 13);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

CREATE TABLE `password_resets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `verification_code` varchar(6) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes`
--

CREATE TABLE `reportes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `modulo` varchar(255) NOT NULL,
  `formato` varchar(255) NOT NULL,
  `fecha_inicio` varchar(255) NOT NULL,
  `fecha_fin` varchar(255) NOT NULL,
  `ordenar_por` varchar(255) DEFAULT NULL,
  `orden` enum('asc','desc') DEFAULT NULL,
  `filtros` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reportes`
--

INSERT INTO `reportes` (`id`, `modulo`, `formato`, `fecha_inicio`, `fecha_fin`, `ordenar_por`, `orden`, `filtros`, `file_path`, `created_at`, `updated_at`) VALUES
(5, 'Inscripciones', 'PDF', '2024-06-06', '2024-07-31', 'usuario', 'desc', NULL, 'reportes/Inscripciones_reporte_1720163800.pdf', '2024-07-05 12:16:40', '2024-07-05 12:16:40'),
(6, 'Usuarios', 'PDF', '2024-07-18', '2024-08-12', 'username', 'desc', NULL, 'reportes/Usuarios_reporte_1723341631.pdf', '2024-08-11 07:00:32', '2024-08-11 07:00:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Cliente', NULL, NULL),
(2, 'Administrador', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subcategorias`
--

CREATE TABLE `subcategorias` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `categoria_id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `vacantes` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `vacantes_ocupadas` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `subcategorias`
--

INSERT INTO `subcategorias` (`id`, `categoria_id`, `nombre`, `vacantes`, `created_at`, `updated_at`, `vacantes_ocupadas`) VALUES
(1, 1, 'Fútbol', 30, '2024-06-18 08:23:42', '2024-08-11 06:23:11', 2),
(2, 2, 'Concierto', 30, '2024-06-19 13:31:01', '2024-06-22 13:48:32', 0),
(3, 3, 'Canto', 30, '2024-06-19 13:31:21', '2024-06-19 13:31:21', 0),
(4, 3, 'Baile', 50, '2024-06-19 14:08:35', '2024-06-19 14:08:35', 0),
(6, 2, 'Concurso', 60, '2024-06-19 14:26:28', '2024-07-05 12:08:03', 2),
(7, 1, 'Basketball', 40, '2024-06-19 16:01:51', '2024-06-25 09:22:16', 1),
(8, 1, 'Volleyball', 40, '2024-06-19 16:02:01', '2024-06-22 18:53:52', 0),
(16, 2, 'Test', 60, '2024-06-22 18:56:14', '2024-06-22 18:56:14', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `apellido_paterno` varchar(255) NOT NULL,
  `apellido_materno` varchar(255) NOT NULL,
  `sexo` enum('M','F') NOT NULL,
  `username` varchar(255) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `rol_id` bigint(20) UNSIGNED DEFAULT NULL,
  `imagen_perfil` varchar(255) DEFAULT NULL,
  `campus` varchar(255) DEFAULT NULL,
  `carrera` varchar(255) DEFAULT NULL,
  `numero_telefono` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido_paterno`, `apellido_materno`, `sexo`, `username`, `contraseña`, `email`, `fecha_nacimiento`, `rol_id`, `imagen_perfil`, `campus`, `carrera`, `numero_telefono`, `google_id`, `created_at`, `updated_at`) VALUES
(6, 'Jose', 'Saavedra', 'Vega', 'M', 'JSaavedraV', '$2y$12$xWWymSt.r88d9h/b.GjkuuVkZoXpGZZjo/Un1Tsai0G0i/55L6EsO', 'leoavmn@gmail.com', '2000-12-03', 2, 'storage/images/default.jpg', 'Chiclayo', 'Economía', '954854758', NULL, '2024-08-11 05:23:24', '2024-08-11 05:23:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `videos`
--

CREATE TABLE `videos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `campus` varchar(255) NOT NULL,
  `categoria_id` bigint(20) UNSIGNED NOT NULL,
  `subcategoria_id` bigint(20) UNSIGNED NOT NULL,
  `visibilidad` enum('publico','privado') NOT NULL,
  `archivo_video` varchar(255) NOT NULL,
  `likes` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `videos`
--

INSERT INTO `videos` (`id`, `titulo`, `descripcion`, `campus`, `categoria_id`, `subcategoria_id`, `visibilidad`, `archivo_video`, `likes`, `created_at`, `updated_at`) VALUES
(2, 'New York Snowy - Cinematic', 'Cinematografia - New York en Invierno', 'Callao', 2, 6, 'publico', 'storage/videos/FehTCQwr4RY216FYRXE4KXb1t0Qy0pPn8BzJL8hN.mp4', 0, '2024-08-11 05:57:06', '2024-08-11 05:57:06'),
(3, 'Lima - Cinematic', 'Cinematografia - Lima en Invierno', 'Chiclay', 2, 6, 'publico', 'storage/videos/FlRcYFAZ4xTvca016OoPK78ZB7elfDsXIdKW4sF6.mp4', 0, '2024-08-11 05:57:37', '2024-08-11 05:57:37'),
(4, 'Beach at the night', 'Cinematografia - Playa en la Noche', 'Tarapoto', 1, 7, 'publico', 'storage/videos/VzyINnXmollrZtElngHAGi0e7gQzrFitYHCjpm6Z.mp4', 0, '2024-08-11 06:13:34', '2024-08-11 06:13:34');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `habilidades`
--
ALTER TABLE `habilidades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `habilidades_usuario_id_foreign` (`usuario_id`),
  ADD KEY `habilidades_categoria_id_foreign` (`categoria_id`),
  ADD KEY `habilidades_subcategoria_id_foreign` (`subcategoria_id`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `horarios_subcategoria_id_foreign` (`subcategoria_id`);

--
-- Indices de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inscripciones_usuario_id_foreign` (`usuario_id`),
  ADD KEY `inscripciones_categoria_id_foreign` (`categoria_id`),
  ADD KEY `inscripciones_subcategoria_id_foreign` (`subcategoria_id`),
  ADD KEY `inscripciones_horario_id_foreign` (`horario_id`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `likes_usuario_id_foreign` (`usuario_id`),
  ADD KEY `likes_video_id_foreign` (`video_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subcategorias_categoria_id_foreign` (`categoria_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuarios_username_unique` (`username`),
  ADD UNIQUE KEY `usuarios_email_unique` (`email`),
  ADD UNIQUE KEY `usuarios_google_id_unique` (`google_id`),
  ADD KEY `usuarios_rol_id_foreign` (`rol_id`);

--
-- Indices de la tabla `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `videos_categoria_id_foreign` (`categoria_id`),
  ADD KEY `videos_subcategoria_id_foreign` (`subcategoria_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `habilidades`
--
ALTER TABLE `habilidades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `likes`
--
ALTER TABLE `likes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `reportes`
--
ALTER TABLE `reportes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `videos`
--
ALTER TABLE `videos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `habilidades`
--
ALTER TABLE `habilidades`
  ADD CONSTRAINT `habilidades_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `habilidades_subcategoria_id_foreign` FOREIGN KEY (`subcategoria_id`) REFERENCES `subcategorias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `habilidades_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_subcategoria_id_foreign` FOREIGN KEY (`subcategoria_id`) REFERENCES `subcategorias` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `inscripciones`
--
ALTER TABLE `inscripciones`
  ADD CONSTRAINT `inscripciones_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscripciones_horario_id_foreign` FOREIGN KEY (`horario_id`) REFERENCES `horarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscripciones_subcategoria_id_foreign` FOREIGN KEY (`subcategoria_id`) REFERENCES `subcategorias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscripciones_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `likes_video_id_foreign` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`);

--
-- Filtros para la tabla `subcategorias`
--
ALTER TABLE `subcategorias`
  ADD CONSTRAINT `subcategorias_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_rol_id_foreign` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_categoria_id_foreign` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `videos_subcategoria_id_foreign` FOREIGN KEY (`subcategoria_id`) REFERENCES `subcategorias` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
