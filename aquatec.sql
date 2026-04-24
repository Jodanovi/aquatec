-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-04-2026 a las 21:22:22
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
-- Base de datos: `aquatec`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL
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
(1, '2026_04_10_000000_create_users_table', 1),
(2, '2026_04_10_000001_create_aquatec_tables', 1),
(3, '2026_04_10_170621_create_personal_access_tokens_table', 1),
(4, '2026_04_10_171346_create_sessions_table', 1),
(5, '2026_04_10_172228_add_role_to_users_table', 1),
(6, '2026_04_10_173834_create_cache_table', 1),
(7, '2026_04_10_175717_add_audit_to_orden_trabajos_table', 1),
(8, '2026_04_10_193840_create_reportes_final_table', 1),
(9, '2026_04_12_152056_add_editor_to_ot_tarea_reportes', 1),
(10, '2026_04_17_161522_add_conclusion_jefe_to_orden_trabajos_table', 2),
(11, '2026_04_17_163523_add_validacion_jefe_to_ot_tareas_table', 3),
(12, '2026_04_20_174221_add_indicaciones_tecnicas_to_ot_tareas_table', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_trabajos`
--

CREATE TABLE `orden_trabajos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `numero_ot` varchar(255) NOT NULL,
  `cliente` varchar(255) NOT NULL,
  `embarcacion_unidad` varchar(255) NOT NULL,
  `lugar` varchar(255) NOT NULL,
  `permiso_fecha_vigencia` date DEFAULT NULL,
  `permiso_negociable` tinyint(1) NOT NULL DEFAULT 0,
  `estado` enum('creada','en_planificacion','activa','finalizada') NOT NULL DEFAULT 'creada',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `conclusion_jefe` text DEFAULT NULL,
  `fecha_finalizacion` datetime DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `orden_trabajos`
--

INSERT INTO `orden_trabajos` (`id`, `numero_ot`, `cliente`, `embarcacion_unidad`, `lugar`, `permiso_fecha_vigencia`, `permiso_negociable`, `estado`, `created_at`, `updated_at`, `user_id`, `updated_by`, `conclusion_jefe`, `fecha_finalizacion`, `fecha_inicio`) VALUES
(1, '001.26', 'mercopar', 'r/e parapiti', 'coratei', '2026-04-11', 0, 'finalizada', '2026-04-21 22:46:51', '2026-04-21 23:18:28', 1, 2, 'Todo el proceso se realizo respetando los requerimientos exigidos y dejando todo operativo', '2026-04-21 19:18:28', '2026-04-21 19:00:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ot_indicaciones`
--

CREATE TABLE `ot_indicaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `orden_trabajo_id` bigint(20) UNSIGNED NOT NULL,
  `indicacion` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ot_indicaciones`
--

INSERT INTO `ot_indicaciones` (`id`, `orden_trabajo_id`, `indicacion`, `created_at`, `updated_at`) VALUES
(1, 1, 'Lugar de trabajo CORATEI\r\nContamos con permiso de trabajo que lo llevan de acá cuando se van', '2026-04-21 22:59:33', '2026-04-21 22:59:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ot_reportes`
--

CREATE TABLE `ot_reportes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ot_tarea_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `reporte_crudo` text NOT NULL,
  `reporte_profesional` text DEFAULT NULL,
  `fotos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`fotos`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ot_tareas`
--

CREATE TABLE `ot_tareas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `orden_trabajo_id` bigint(20) UNSIGNED NOT NULL,
  `descripcion_tarea` varchar(255) NOT NULL,
  `indicaciones_tecnicas` text DEFAULT NULL,
  `esta_completada` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `validacion_profesional` text DEFAULT NULL,
  `estado_tarea` varchar(255) NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ot_tareas`
--

INSERT INTO `ot_tareas` (`id`, `orden_trabajo_id`, `descripcion_tarea`, `indicaciones_tecnicas`, `esta_completada`, `created_at`, `updated_at`, `validacion_profesional`, `estado_tarea`) VALUES
(1, 1, 'instalacion de fuente para control de generador', 'se tiene que pasar a buscar junto a paulo 2 convertidores DC-DC de 24 a 5v para este fin', 0, '2026-04-21 22:59:33', '2026-04-21 23:14:33', 'Se procedió a instalación de 2 convertidores DC-DC de 24 a 5v para la fuente de control del generador dejando operativo.', 'validada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ot_tarea_reportes`
--

CREATE TABLE `ot_tarea_reportes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ot_tarea_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `comentario` text NOT NULL,
  `foto_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ot_tarea_reportes`
--

INSERT INTO `ot_tarea_reportes` (`id`, `ot_tarea_id`, `user_id`, `comentario`, `foto_path`, `created_at`, `updated_at`, `updated_by`) VALUES
(1, 1, 3, 'Puse los 2 convertidores por el tablero para dejar la fuente operativa para el generador', 'reportes/9JpKQUwOcqhoAS09hjfhRJMbNWJEjvit2LUwcIfb.jpg', '2026-04-21 23:08:54', '2026-04-21 23:08:54', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('admin','operador','jefe_tecnico','tecnico') NOT NULL DEFAULT 'tecnico',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Operador', 'operador@aquatec.com.py', 'operador', NULL, '$2y$12$zaMpuk768SrgNQ0IR8OlOeUxo/Nxju70NfBs6Tx9p.vuehVsJKIni', NULL, '2026-04-17 16:40:46', '2026-04-17 16:40:46'),
(2, 'Jefe Tecnico', 'jefetecnico@aquatec.com.py', 'jefe_tecnico', NULL, '$2y$12$5Ksl1ajYxX5hu6oh2CBXD.tM/VahiQa0NVuWxWExLCcZs3akN4cri', NULL, '2026-04-17 16:41:47', '2026-04-17 16:41:47'),
(3, 'Técnico Uno', 'tecnicouno@aquatec.com.py', 'tecnico', NULL, '$2y$12$cUsbr0Bgm5aAiDUAYHISTu9S23bZl523CAxxBObKWMHayMwJHtoEy', NULL, '2026-04-17 16:43:24', '2026-04-17 16:43:24'),
(5, 'Tecnico Dos', 'tecnicodos@aquatec.com.py', 'tecnico', NULL, '$2y$12$1rDWXCMXk0u5KNlSSdPv9eDMrUi9kNxV6Ijm6Tu2Uz4xC5t3In/Sq', NULL, '2026-04-17 16:47:16', '2026-04-17 16:47:16'),
(6, 'Administrador', 'administrador@aquatec.com.py', 'admin', NULL, '$2y$12$Q4LIZfysjVIQsVA.L7iWee.F4B5k2KaNhrED6klG/H7FEfLEJ8uhC', NULL, '2026-04-17 16:49:28', '2026-04-17 16:49:28');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orden_trabajos`
--
ALTER TABLE `orden_trabajos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orden_trabajos_numero_ot_unique` (`numero_ot`),
  ADD KEY `orden_trabajos_user_id_foreign` (`user_id`),
  ADD KEY `orden_trabajos_updated_by_foreign` (`updated_by`);

--
-- Indices de la tabla `ot_indicaciones`
--
ALTER TABLE `ot_indicaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ot_indicaciones_orden_trabajo_id_foreign` (`orden_trabajo_id`);

--
-- Indices de la tabla `ot_reportes`
--
ALTER TABLE `ot_reportes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ot_reportes_ot_tarea_id_foreign` (`ot_tarea_id`),
  ADD KEY `ot_reportes_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `ot_tareas`
--
ALTER TABLE `ot_tareas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ot_tareas_orden_trabajo_id_foreign` (`orden_trabajo_id`);

--
-- Indices de la tabla `ot_tarea_reportes`
--
ALTER TABLE `ot_tarea_reportes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ot_tarea_reportes_ot_tarea_id_foreign` (`ot_tarea_id`),
  ADD KEY `ot_tarea_reportes_user_id_foreign` (`user_id`),
  ADD KEY `ot_tarea_reportes_updated_by_foreign` (`updated_by`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `orden_trabajos`
--
ALTER TABLE `orden_trabajos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ot_indicaciones`
--
ALTER TABLE `ot_indicaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ot_reportes`
--
ALTER TABLE `ot_reportes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ot_tareas`
--
ALTER TABLE `ot_tareas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `ot_tarea_reportes`
--
ALTER TABLE `ot_tarea_reportes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `orden_trabajos`
--
ALTER TABLE `orden_trabajos`
  ADD CONSTRAINT `orden_trabajos_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orden_trabajos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `ot_indicaciones`
--
ALTER TABLE `ot_indicaciones`
  ADD CONSTRAINT `ot_indicaciones_orden_trabajo_id_foreign` FOREIGN KEY (`orden_trabajo_id`) REFERENCES `orden_trabajos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ot_reportes`
--
ALTER TABLE `ot_reportes`
  ADD CONSTRAINT `ot_reportes_ot_tarea_id_foreign` FOREIGN KEY (`ot_tarea_id`) REFERENCES `ot_tareas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ot_reportes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `ot_tareas`
--
ALTER TABLE `ot_tareas`
  ADD CONSTRAINT `ot_tareas_orden_trabajo_id_foreign` FOREIGN KEY (`orden_trabajo_id`) REFERENCES `orden_trabajos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ot_tarea_reportes`
--
ALTER TABLE `ot_tarea_reportes`
  ADD CONSTRAINT `ot_tarea_reportes_ot_tarea_id_foreign` FOREIGN KEY (`ot_tarea_id`) REFERENCES `ot_tareas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ot_tarea_reportes_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ot_tarea_reportes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
