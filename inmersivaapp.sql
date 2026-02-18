-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 18-02-2026 a las 16:14:32
-- Versión del servidor: 10.5.29-MariaDB
-- Versión de PHP: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inmersivaapp`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `associates`
--

CREATE TABLE `associates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `category` varchar(255) DEFAULT NULL,
  `payment_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_info`)),
  `w9_status` varchar(255) NOT NULL DEFAULT 'pending',
  `w9_file_url` text DEFAULT NULL,
  `referrer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `associates`
--

INSERT INTO `associates` (`id`, `balance`, `category`, `payment_info`, `w9_status`, `w9_file_url`, `referrer_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 0.00, 'Realtor', NULL, 'verified', NULL, NULL, '2026-02-10 04:06:08', '2026-02-10 04:06:08', NULL),
(2, 0.00, NULL, NULL, 'pending', NULL, NULL, '2026-02-10 04:06:08', '2026-02-10 04:06:08', NULL),
(3, 0.00, NULL, NULL, 'pending', NULL, NULL, '2026-02-10 04:06:08', '2026-02-10 04:06:08', NULL),
(4, 0.00, NULL, NULL, 'pending', NULL, NULL, '2026-02-10 04:06:08', '2026-02-10 04:06:08', NULL),
(5, 0.00, NULL, NULL, 'pending', NULL, NULL, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL),
(6, 0.00, NULL, NULL, 'pending', NULL, NULL, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `associate_offering_commissions`
--

CREATE TABLE `associate_offering_commissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `associate_id` bigint(20) UNSIGNED NOT NULL,
  `offering_id` bigint(20) UNSIGNED NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `associate_offering_links`
--

CREATE TABLE `associate_offering_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `associate_id` bigint(20) UNSIGNED NOT NULL,
  `offering_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity` varchar(255) DEFAULT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `auditable_type` varchar(255) DEFAULT NULL,
  `auditable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `event_type` varchar(255) DEFAULT NULL,
  `actorable_type` varchar(255) DEFAULT NULL,
  `actorable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `previous_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`previous_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `description` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `entity`, `entity_id`, `auditable_type`, `auditable_id`, `action`, `event_type`, `actorable_type`, `actorable_id`, `previous_data`, `new_data`, `description`, `metadata`, `created_at`) VALUES
(1, NULL, NULL, 'App\\Models\\Referral', 1, 'UPDATE', 'status_change', 'App\\Models\\User', 1, '{\"status\":\"Prospecto\"}', '{\"status\":\"Contactado\"}', 'Status changed from Prospecto to Contactado', '{\"notes\":\"la llamamos y esta interesad EN COMPRAR EL 10 DE FEBRERO\",\"note\":\"la llamamos y esta interesad EN COMPRAR EL 10 DE FEBRERO\"}', '2026-02-10 04:21:46'),
(2, NULL, NULL, 'App\\Models\\Referral', 2, 'UPDATE', 'status_change', 'App\\Models\\User', 1, '{\"status\":\"Prospecto\"}', '{\"status\":\"Contactado\"}', 'Status changed from Prospecto to Contactado', '{\"notes\":\"se llamo al cliente para pedirle la informacion\",\"note\":\"se llamo al cliente para pedirle la informacion\"}', '2026-02-11 15:26:08'),
(3, NULL, NULL, 'App\\Models\\Referral', 2, 'UPDATE', 'status_change', 'App\\Models\\User', 1, '{\"status\":\"Contactado\"}', '{\"status\":\"Cerrado\"}', 'Status changed from Contactado to Cerrado', '{\"notes\":\"se le dio un seguro de salud Ambetter\",\"note\":\"se le dio un seguro de salud Ambetter\"}', '2026-02-11 15:33:18');

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
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Salud', 'Servicios de salud y bienestar', 1, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL),
(2, 'Vida', 'Protección financiera y seguros de vida', 1, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL),
(3, 'Propiedad y Accidentes', 'Coberturas de auto, casa y propiedad', 1, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL),
(4, 'Empresarial', 'Servicios y coberturas para empresas', 1, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL),
(5, 'Personal', 'Servicios personales y particulares', 1, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL),
(6, 'Administrativo', 'Trámites y gestión documental', 1, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `commissions`
--

CREATE TABLE `commissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `referral_id` bigint(20) UNSIGNED DEFAULT NULL,
  `associate_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `commission_percentage` decimal(5,2) DEFAULT NULL,
  `commission_type` varchar(255) NOT NULL DEFAULT 'direct',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `recurrence_type` varchar(255) NOT NULL DEFAULT 'one_time',
  `recurrence_interval` varchar(255) DEFAULT NULL,
  `recurrence_end_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `parent_commission_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `commissions`
--

INSERT INTO `commissions` (`id`, `referral_id`, `associate_id`, `amount`, `commission_percentage`, `commission_type`, `status`, `paid_at`, `recurrence_type`, `recurrence_interval`, `recurrence_end_date`, `created_at`, `updated_at`, `deleted_at`, `parent_commission_id`) VALUES
(1, 2, 1, 0.00, 30.00, 'percentage', 'pending', NULL, 'one_time', NULL, NULL, '2026-02-11 15:33:18', '2026-02-11 15:33:18', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `commission_overrides`
--

CREATE TABLE `commission_overrides` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `associate_id` bigint(20) UNSIGNED NOT NULL,
  `offering_id` bigint(20) UNSIGNED NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department` varchar(255) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `internal_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `employees`
--

INSERT INTO `employees` (`id`, `department`, `job_title`, `internal_code`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Executive', 'System Owner', 'EMP-001', '2026-02-10 04:06:07', '2026-02-10 04:06:07', NULL),
(2, 'Operations', 'Manager', 'EMP-002', '2026-02-10 04:06:08', '2026-02-10 04:06:08', NULL);

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_02_05_120000_create_permission_tables', 1),
(5, '2026_02_05_120100_create_associates_table', 1),
(6, '2026_02_05_120110_create_employees_table', 1),
(7, '2026_02_05_120120_create_categories_table', 1),
(8, '2026_02_05_120130_create_offerings_table', 1),
(9, '2026_02_05_120140_create_referrals_table', 1),
(10, '2026_02_05_120150_create_commissions_table', 1),
(11, '2026_02_05_120160_create_commission_overrides_table', 1),
(12, '2026_02_05_120170_create_associate_offering_commissions_table', 1),
(13, '2026_02_05_120180_create_associate_offering_links_table', 1),
(14, '2026_02_05_120190_create_referral_clicks_table', 1),
(15, '2026_02_05_120200_create_audit_logs_table', 1),
(16, '2026_02_05_120210_create_security_logs_table', 1),
(17, '2026_02_05_120220_create_networks_table', 1),
(18, '2026_02_05_120230_create_referral_history_table', 1),
(19, '2026_02_05_120240_create_system_settings_table', 1),
(20, '2026_02_10_031431_add_preferred_currency_to_users_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 3),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 6),
(3, 'App\\Models\\User', 7),
(3, 'App\\Models\\User', 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `networks`
--

CREATE TABLE `networks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_associate_id` bigint(20) UNSIGNED NOT NULL,
  `child_associate_id` bigint(20) UNSIGNED NOT NULL,
  `level` int(11) NOT NULL DEFAULT 1,
  `total_sales` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `offerings`
--

CREATE TABLE `offerings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `owner_employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `base_price` decimal(10,2) DEFAULT NULL,
  `base_commission` decimal(10,2) NOT NULL DEFAULT 0.00,
  `commission_rate` decimal(5,2) DEFAULT NULL,
  `form_schema` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`form_schema`)),
  `commission_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`commission_config`)),
  `commission_rules` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`commission_rules`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `offerings`
--

INSERT INTO `offerings` (`id`, `owner_employee_id`, `type`, `category`, `category_id`, `name`, `description`, `base_price`, `base_commission`, `commission_rate`, `form_schema`, `commission_config`, `commission_rules`, `is_active`, `metadata`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'service', NULL, 1, 'Seguros de Salud', 'Cobertura médica integral para individuos y familias.', NULL, 0.00, 30.00, NULL, '{\"monthly\":{\"percentage\":30,\"amount\":null,\"duration_months\":null}}', '[{\"condition\":\"default\",\"commission_rate\":30,\"label\":\"Salud mensual\",\"roles\":[\"associate\"]}]', 1, NULL, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL),
(2, 1, 'service', NULL, 2, 'Seguros de Vida', 'Protección financiera y tranquilidad para seres queridos.', NULL, 25.00, NULL, NULL, NULL, '[{\"condition\":\"default\",\"fixed\":25,\"label\":\"Vida pago \\u00fanico\",\"roles\":[\"associate\"]}]', 1, NULL, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL),
(3, 1, 'service', NULL, 3, 'Seguros de Carro y Casa', 'Protección para hogar y vehículo contra imprevistos.', NULL, 25.00, NULL, NULL, NULL, '[{\"condition\":\"default\",\"fixed\":25,\"label\":\"Auto y casa\",\"roles\":[\"associate\"]}]', 1, NULL, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL),
(4, 1, 'service', NULL, 4, 'Group Insurance (5+ empleados)', 'Seguros colectivos para empresas y PyMEs.', NULL, 50.00, NULL, '[{\"label\":\"Nombre de la empresa\",\"type\":\"text\",\"required\":true},{\"label\":\"N\\u00famero de empleados\",\"type\":\"number\",\"required\":true}]', NULL, '[{\"condition\":\"default\",\"fixed\":50,\"label\":\"Group Insurance\",\"roles\":[\"associate\"]}]', 1, NULL, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL),
(5, 1, 'service', NULL, 4, 'Business Liability / Workers Comp', 'Responsabilidad civil y compensación laboral para negocios.', NULL, 0.00, 10.00, NULL, NULL, '[{\"condition\":\"default\",\"commission_rate\":10,\"label\":\"Liability\",\"roles\":[\"associate\"]}]', 1, NULL, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL),
(6, 1, 'service', NULL, 5, 'Taxes Personales', 'Preparación y presentación de impuestos personales.', NULL, 25.00, NULL, '[{\"label\":\"A\\u00f1o fiscal\",\"type\":\"number\",\"required\":true},{\"label\":\"Estado civil\",\"type\":\"select\",\"required\":true,\"options\":\"Soltero, Casado, Cabeza de hogar\"}]', NULL, '[{\"condition\":\"default\",\"fixed\":25,\"label\":\"Taxes personales\",\"roles\":[\"associate\"]}]', 1, NULL, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL),
(7, 1, 'service', NULL, 4, 'Taxes Corporativos', 'Servicios fiscales para corporaciones y negocios.', NULL, 50.00, NULL, '[{\"label\":\"Nombre corporaci\\u00f3n\",\"type\":\"text\",\"required\":true},{\"label\":\"EIN\",\"type\":\"text\",\"required\":true},{\"label\":\"Tipo de entidad\",\"type\":\"select\",\"required\":true,\"options\":\"LLC, S-Corp, C-Corp, Partnership\"}]', NULL, '[{\"condition\":\"default\",\"fixed\":50,\"label\":\"Taxes corporativos\",\"roles\":[\"associate\"]}]', 1, NULL, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL),
(8, 1, 'service', NULL, 6, 'Solicitud de Certificado (CDI)', 'Gestión de documentos y certificados oficiales (Liability, Workers Comp, etc.).', NULL, 0.00, NULL, '[{\"label\":\"Titular\",\"type\":\"text\",\"required\":true},{\"label\":\"Asegurado\",\"type\":\"text\",\"required\":true},{\"label\":\"Tipo de certificado\",\"type\":\"select\",\"required\":true,\"options\":\"Liability, Workers Comp, Otros\"},{\"label\":\"Fecha requerida\",\"type\":\"date\",\"required\":true},{\"label\":\"Direcci\\u00f3n de env\\u00edo\",\"type\":\"text\",\"required\":true}]', NULL, NULL, 1, NULL, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `referrals`
--

CREATE TABLE `referrals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `associate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `offering_id` bigint(20) UNSIGNED NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `client_contact` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Prospecto',
  `deal_value` decimal(12,2) DEFAULT NULL,
  `revenue_generated` decimal(12,2) DEFAULT NULL,
  `contract_id` varchar(255) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `down_payment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `agency_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `closed_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `referrals`
--

INSERT INTO `referrals` (`id`, `associate_id`, `offering_id`, `client_name`, `client_contact`, `status`, `deal_value`, `revenue_generated`, `contract_id`, `payment_method`, `down_payment`, `agency_fee`, `notes`, `metadata`, `closed_at`, `paid_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 2, 'Priscilla Marin', 'primarinb@gmail.com', 'Contactado', NULL, NULL, NULL, NULL, 0.00, 0.00, 'Test', '[]', NULL, NULL, '2026-02-10 04:20:55', '2026-02-10 04:21:46', NULL),
(2, 1, 1, 'peggy ojeda', '7867027699', 'Cerrado', NULL, 9.00, NULL, NULL, 0.00, 0.00, NULL, '[]', NULL, NULL, '2026-02-11 15:24:30', '2026-02-11 15:33:18', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `referral_clicks`
--

CREATE TABLE `referral_clicks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `referrer_associate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `offering_id` bigint(20) UNSIGNED DEFAULT NULL,
  `link_type` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `clicked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `referral_history`
--

CREATE TABLE `referral_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `referral_id` bigint(20) UNSIGNED NOT NULL,
  `actorable_type` varchar(255) DEFAULT NULL,
  `actorable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `previous_status` varchar(255) DEFAULT NULL,
  `new_status` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-02-10 04:06:07', '2026-02-10 04:06:07'),
(2, 'psadmin', 'web', '2026-02-10 04:06:07', '2026-02-10 04:06:07'),
(3, 'associate', 'web', '2026-02-10 04:06:07', '2026-02-10 04:06:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `security_logs`
--

CREATE TABLE `security_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_type` varchar(255) NOT NULL,
  `actorable_type` varchar(255) DEFAULT NULL,
  `actorable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `security_logs`
--

INSERT INTO `security_logs` (`id`, `event_type`, `actorable_type`, `actorable_id`, `email`, `ip_address`, `user_agent`, `metadata`, `created_at`) VALUES
(1, 'LOGIN_SUCCESS', 'App\\Models\\Employee', 2, 'admin@psrefer.com', '38.25.2.4', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"guard\":\"web\"}', '2026-02-10 04:06:37'),
(2, 'LOGIN_SUCCESS', 'App\\Models\\Associate', 1, 'partner@psrefer.com', '2803:5840:2006:3b00:7869:2c43:46af:decc', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"guard\":\"web\"}', '2026-02-10 04:20:15'),
(3, 'LOGIN_SUCCESS', 'App\\Models\\Employee', 1, 'psadmin@psrefer.com', '2803:5840:2006:3b00:7869:2c43:46af:decc', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"guard\":\"web\"}', '2026-02-10 04:21:19'),
(4, 'LOGIN_SUCCESS', 'App\\Models\\Employee', 1, 'psadmin@psrefer.com', '99.59.225.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"guard\":\"web\"}', '2026-02-11 15:21:54'),
(5, 'LOGIN_SUCCESS', 'App\\Models\\Associate', 1, 'partner@psrefer.com', '99.59.225.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', '{\"guard\":\"web\"}', '2026-02-11 15:22:54'),
(6, 'LOGIN_SUCCESS', 'App\\Models\\Employee', 2, 'admin@psrefer.com', '38.25.2.4', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"guard\":\"web\"}', '2026-02-13 02:03:16'),
(7, 'LOGIN_SUCCESS', 'App\\Models\\Associate', 1, 'partner@psrefer.com', '99.59.225.82', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '{\"guard\":\"web\"}', '2026-02-17 14:18:41'),
(8, 'LOGIN_SUCCESS', 'App\\Models\\Employee', 1, 'psadmin@psrefer.com', '162.251.62.58', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.2 Mobile/15E148 Safari/604.1', '{\"guard\":\"web\"}', '2026-02-18 14:34:13'),
(9, 'LOGIN_SUCCESS', 'App\\Models\\Employee', 1, 'psadmin@psrefer.com', '38.25.2.4', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '{\"guard\":\"web\"}', '2026-02-18 14:41:23');

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
-- Estructura de tabla para la tabla `system_settings`
--

CREATE TABLE `system_settings` (
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `phone` varchar(255) DEFAULT NULL,
  `logo_url` text DEFAULT NULL,
  `profileable_type` varchar(255) DEFAULT NULL,
  `profileable_id` bigint(20) UNSIGNED DEFAULT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `theme` varchar(255) DEFAULT NULL,
  `preferred_currency` varchar(255) NOT NULL DEFAULT 'USD',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `is_active`, `phone`, `logo_url`, `profileable_type`, `profileable_id`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `theme`, `preferred_currency`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PS Administrator', 'psadmin@psrefer.com', '2026-02-10 04:06:08', '$2y$12$xZj3ulP2abNTSv498NDFWeP2a11IYP7hbeWK2MVf13qZVM6KcGL.6', 1, '555-0001', 'https://ui-avatars.com/api/?name=PS+Administrator&background=random', 'App\\Models\\Employee', 1, NULL, NULL, NULL, NULL, 'USD', NULL, '2026-02-10 04:06:08', '2026-02-10 04:06:08', NULL),
(2, 'System Manager', 'admin@psrefer.com', '2026-02-10 04:06:08', '$2y$12$OF.gtcdXiGWNrsHBD27IW.xIbYWV3v7Gwgt4Yabbo0ZZIgO1qtNzW', 1, '555-0002', 'https://ui-avatars.com/api/?name=System+Manager&background=random', 'App\\Models\\Employee', 2, NULL, NULL, NULL, NULL, 'USD', NULL, '2026-02-10 04:06:08', '2026-02-10 04:06:08', NULL),
(3, 'Active Partner', 'partner@psrefer.com', '2026-02-10 04:06:08', '$2y$12$pT5o4sDfoDHMtmv7vtBGNOvBQqxJAn20QJFdi9vwsAUxdLQkmbV3u', 1, '555-1000', 'https://ui-avatars.com/api/?name=Active+Partner&background=random', 'App\\Models\\Associate', 1, NULL, NULL, NULL, NULL, 'USD', 'sBVQg226dXcgeGHpIfCIUiesV28oAl1ZM4Z4oM7ShY0iNn17pSaR6np24kaQ', '2026-02-10 04:06:08', '2026-02-10 04:06:08', NULL),
(4, 'Associate 0', 'associate0@example.com', NULL, '$2y$12$L4ToyFQg79GHI/IJWW5TseobgLTU5FLM7jP4560sPL6b5fwQE5eV2', 1, '555-1000', 'https://ui-avatars.com/api/?name=Associate+0&background=random', 'App\\Models\\Associate', 2, NULL, NULL, NULL, NULL, 'USD', NULL, '2026-02-10 04:06:08', '2026-02-10 04:06:08', NULL),
(5, 'Associate 1', 'associate1@example.com', NULL, '$2y$12$1/RJW3AVqf59xsvRt3wXVu2zddS6MCoQNnNpaKfczW7ek2kBZfcAO', 1, '555-1001', 'https://ui-avatars.com/api/?name=Associate+1&background=random', 'App\\Models\\Associate', 3, NULL, NULL, NULL, NULL, 'USD', NULL, '2026-02-10 04:06:08', '2026-02-10 04:06:08', NULL),
(6, 'Associate 2', 'associate2@example.com', NULL, '$2y$12$IA0d7B/YT843r9N.IRoyWuj9N616wru0oeo3Z/hv.PeT9FavyUPaW', 1, '555-1002', 'https://ui-avatars.com/api/?name=Associate+2&background=random', 'App\\Models\\Associate', 4, NULL, NULL, NULL, NULL, 'USD', NULL, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL),
(7, 'Associate 3', 'associate3@example.com', NULL, '$2y$12$sHdSAVg7QrkbAU4ESH5UiupSEbO63Pi4EiGPozr7Sq61kwE.WcxZK', 1, '555-1003', 'https://ui-avatars.com/api/?name=Associate+3&background=random', 'App\\Models\\Associate', 5, NULL, NULL, NULL, NULL, 'USD', NULL, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL),
(8, 'Associate 4', 'associate4@example.com', NULL, '$2y$12$fvktP/FFZnk2Em0OEWUDjOikkkGZz77FIdk/cECt5jvjPaTgsXSnG', 1, '555-1004', 'https://ui-avatars.com/api/?name=Associate+4&background=random', 'App\\Models\\Associate', 6, NULL, NULL, NULL, NULL, 'USD', NULL, '2026-02-10 04:06:09', '2026-02-10 04:06:09', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `associates`
--
ALTER TABLE `associates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `associates_referrer_id_index` (`referrer_id`);

--
-- Indices de la tabla `associate_offering_commissions`
--
ALTER TABLE `associate_offering_commissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `associate_offering_commissions_associate_id_offering_id_unique` (`associate_id`,`offering_id`),
  ADD KEY `associate_offering_commissions_offering_id_foreign` (`offering_id`);

--
-- Indices de la tabla `associate_offering_links`
--
ALTER TABLE `associate_offering_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `associate_offering_links_associate_id_offering_id_unique` (`associate_id`,`offering_id`),
  ADD KEY `associate_offering_links_offering_id_foreign` (`offering_id`);

--
-- Indices de la tabla `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_auditable_type_auditable_id_index` (`auditable_type`,`auditable_id`),
  ADD KEY `audit_logs_actorable_type_actorable_id_index` (`actorable_type`,`actorable_id`),
  ADD KEY `audit_logs_entity_entity_id_index` (`entity`,`entity_id`);

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
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_deleted_at_unique` (`name`,`deleted_at`),
  ADD KEY `categories_is_active_index` (`is_active`);

--
-- Indices de la tabla `commissions`
--
ALTER TABLE `commissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `commissions_associate_id_index` (`associate_id`),
  ADD KEY `commissions_referral_id_index` (`referral_id`),
  ADD KEY `commissions_status_index` (`status`),
  ADD KEY `commissions_commission_type_index` (`commission_type`),
  ADD KEY `commissions_paid_at_index` (`paid_at`),
  ADD KEY `commissions_created_at_index` (`created_at`),
  ADD KEY `commissions_parent_commission_id_index` (`parent_commission_id`);

--
-- Indices de la tabla `commission_overrides`
--
ALTER TABLE `commission_overrides`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `commission_overrides_associate_id_offering_id_deleted_at_unique` (`associate_id`,`offering_id`,`deleted_at`),
  ADD KEY `commission_overrides_offering_id_foreign` (`offering_id`);

--
-- Indices de la tabla `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indices de la tabla `networks`
--
ALTER TABLE `networks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `networks_parent_associate_id_child_associate_id_unique` (`parent_associate_id`,`child_associate_id`),
  ADD KEY `networks_child_associate_id_foreign` (`child_associate_id`);

--
-- Indices de la tabla `offerings`
--
ALTER TABLE `offerings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `offerings_is_active_index` (`is_active`),
  ADD KEY `offerings_category_id_index` (`category_id`),
  ADD KEY `offerings_owner_employee_id_index` (`owner_employee_id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referrals_associate_id_index` (`associate_id`),
  ADD KEY `referrals_offering_id_index` (`offering_id`),
  ADD KEY `referrals_status_index` (`status`),
  ADD KEY `referrals_status_created_at_index` (`status`,`created_at`),
  ADD KEY `referrals_created_at_index` (`created_at`),
  ADD KEY `referrals_closed_at_index` (`closed_at`),
  ADD KEY `referrals_paid_at_index` (`paid_at`);

--
-- Indices de la tabla `referral_clicks`
--
ALTER TABLE `referral_clicks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referral_clicks_referrer_associate_id_index` (`referrer_associate_id`),
  ADD KEY `referral_clicks_offering_id_index` (`offering_id`),
  ADD KEY `referral_clicks_clicked_at_index` (`clicked_at`),
  ADD KEY `referral_clicks_referrer_associate_id_clicked_at_index` (`referrer_associate_id`,`clicked_at`);

--
-- Indices de la tabla `referral_history`
--
ALTER TABLE `referral_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referral_history_actorable_type_actorable_id_index` (`actorable_type`,`actorable_id`),
  ADD KEY `referral_history_referral_id_index` (`referral_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indices de la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indices de la tabla `security_logs`
--
ALTER TABLE `security_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `security_logs_actorable_type_actorable_id_index` (`actorable_type`,`actorable_id`),
  ADD KEY `security_logs_created_at_index` (`created_at`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_profileable_type_profileable_id_index` (`profileable_type`,`profileable_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `associates`
--
ALTER TABLE `associates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `associate_offering_commissions`
--
ALTER TABLE `associate_offering_commissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `associate_offering_links`
--
ALTER TABLE `associate_offering_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `commissions`
--
ALTER TABLE `commissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `commission_overrides`
--
ALTER TABLE `commission_overrides`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `networks`
--
ALTER TABLE `networks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `offerings`
--
ALTER TABLE `offerings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `referral_clicks`
--
ALTER TABLE `referral_clicks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `referral_history`
--
ALTER TABLE `referral_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `security_logs`
--
ALTER TABLE `security_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `associates`
--
ALTER TABLE `associates`
  ADD CONSTRAINT `associates_referrer_id_foreign` FOREIGN KEY (`referrer_id`) REFERENCES `associates` (`id`);

--
-- Filtros para la tabla `associate_offering_commissions`
--
ALTER TABLE `associate_offering_commissions`
  ADD CONSTRAINT `associate_offering_commissions_associate_id_foreign` FOREIGN KEY (`associate_id`) REFERENCES `associates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `associate_offering_commissions_offering_id_foreign` FOREIGN KEY (`offering_id`) REFERENCES `offerings` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `associate_offering_links`
--
ALTER TABLE `associate_offering_links`
  ADD CONSTRAINT `associate_offering_links_associate_id_foreign` FOREIGN KEY (`associate_id`) REFERENCES `associates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `associate_offering_links_offering_id_foreign` FOREIGN KEY (`offering_id`) REFERENCES `offerings` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `commissions`
--
ALTER TABLE `commissions`
  ADD CONSTRAINT `commissions_associate_id_foreign` FOREIGN KEY (`associate_id`) REFERENCES `associates` (`id`),
  ADD CONSTRAINT `commissions_parent_commission_id_foreign` FOREIGN KEY (`parent_commission_id`) REFERENCES `commissions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `commissions_referral_id_foreign` FOREIGN KEY (`referral_id`) REFERENCES `referrals` (`id`);

--
-- Filtros para la tabla `commission_overrides`
--
ALTER TABLE `commission_overrides`
  ADD CONSTRAINT `commission_overrides_associate_id_foreign` FOREIGN KEY (`associate_id`) REFERENCES `associates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `commission_overrides_offering_id_foreign` FOREIGN KEY (`offering_id`) REFERENCES `offerings` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `networks`
--
ALTER TABLE `networks`
  ADD CONSTRAINT `networks_child_associate_id_foreign` FOREIGN KEY (`child_associate_id`) REFERENCES `associates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `networks_parent_associate_id_foreign` FOREIGN KEY (`parent_associate_id`) REFERENCES `associates` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `offerings`
--
ALTER TABLE `offerings`
  ADD CONSTRAINT `offerings_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `offerings_owner_employee_id_foreign` FOREIGN KEY (`owner_employee_id`) REFERENCES `employees` (`id`);

--
-- Filtros para la tabla `referrals`
--
ALTER TABLE `referrals`
  ADD CONSTRAINT `referrals_associate_id_foreign` FOREIGN KEY (`associate_id`) REFERENCES `associates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `referrals_offering_id_foreign` FOREIGN KEY (`offering_id`) REFERENCES `offerings` (`id`);

--
-- Filtros para la tabla `referral_clicks`
--
ALTER TABLE `referral_clicks`
  ADD CONSTRAINT `referral_clicks_offering_id_foreign` FOREIGN KEY (`offering_id`) REFERENCES `offerings` (`id`),
  ADD CONSTRAINT `referral_clicks_referrer_associate_id_foreign` FOREIGN KEY (`referrer_associate_id`) REFERENCES `associates` (`id`);

--
-- Filtros para la tabla `referral_history`
--
ALTER TABLE `referral_history`
  ADD CONSTRAINT `referral_history_referral_id_foreign` FOREIGN KEY (`referral_id`) REFERENCES `referrals` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
