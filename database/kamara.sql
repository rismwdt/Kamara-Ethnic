-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 08 Sep 2025 pada 06.04
-- Versi server: 8.4.3
-- Versi PHP: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kamara`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint UNSIGNED NOT NULL,
  `booking_code` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `event_id` bigint UNSIGNED NOT NULL,
  `price` bigint NOT NULL DEFAULT '0',
  `dp` bigint NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `location_detail` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `client_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `male_parents` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `female_parents` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nuance` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_photo` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `priority` enum('normal','darurat') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `is_family` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('tertunda','diterima','ditolak','selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tertunda',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `bookings`
--

INSERT INTO `bookings` (`id`, `booking_code`, `event_type`, `user_id`, `event_id`, `price`, `dp`, `date`, `start_time`, `end_time`, `location_detail`, `latitude`, `longitude`, `client_name`, `event_name`, `male_parents`, `female_parents`, `phone`, `email`, `nuance`, `location_photo`, `image`, `description`, `notes`, `priority`, `is_family`, `status`, `created_at`, `updated_at`) VALUES
(108, 'BK250813ZTKX', NULL, 5, 3, 0, 0, '2025-08-22', '13:00:00', '15:00:00', 'Trans Luxury Hotel, Jl. Gatot Subroto No.45, Bandung', -6.910500, 107.610000, 'Hyuni & Lola', NULL, 'Budi & Ani', 'Sari & Tono', '081278901234', 'hyuni@example.com', 'Pernikahan tradisional', 'location_photos/photo10.jpg', 'image/photo10.jpg', NULL, 'Butuh dekorasi natural', 'normal', 0, 'diterima', '2025-08-13 07:52:22', '2025-08-21 09:33:04'),
(110, 'BK250814CISH', NULL, 5, 1, 0, 0, '2025-08-23', '09:00:00', '11:00:00', 'Ballroom Aston, Jl. Asia Afrika No.10, Bandung', -6.920100, 107.610200, 'Alya & Fajar', NULL, 'Riko & Lila', 'Maya & Beni', '081300112233', 'alya@example.com', 'Pernikahan modern', 'location_photos/photo11.jpg', 'image/photo11.jpg', NULL, 'MC lucu dan interaktif', 'normal', 0, 'diterima', '2025-08-14 02:23:59', '2025-08-21 09:39:44'),
(112, 'BK250814D1DS', NULL, 9, 3, 0, 0, '2025-08-24', '08:30:00', '10:30:00', 'Hotel Savoy Homann, Jl. Asia Afrika No.120, Bandung', -6.917200, 107.608800, 'Nadia & Aldi', NULL, 'Budi & Sari', 'Tina & Eko', '081322334455', 'nadia@example.com', 'Pernikahan modern', 'location_photos/photo13.jpg', 'image/photo13.jpg', NULL, 'Minta MC lucu', 'normal', 0, 'diterima', '2025-08-14 02:23:59', '2025-08-21 09:41:30'),
(113, 'BK250814AWPG', NULL, 4, 4, 0, 0, '2025-08-24', '11:00:00', '13:00:00', 'Gedung Trisakti 2, Jl. Setiabudi No.110, Bandung', -6.916800, 107.609700, 'Riko & Livia', NULL, 'Agus & Lilis', 'Rina & Eko', '081333445566', 'riko@example.com', 'Pernikahan tradisional', 'location_photos/photo14.jpg', 'image/photo14.jpg', NULL, 'Butuh dekorasi minimalis', 'normal', 0, 'diterima', '2025-08-14 02:23:59', '2025-08-21 12:28:36'),
(114, 'BK250814MXDZ', NULL, 5, 1, 0, 0, '2025-08-25', '09:00:00', '11:00:00', 'Hotel Grand Preanger, Jl. Asia Afrika No.77, Bandung', -6.919000, 107.612000, 'Hana & Ardi', NULL, 'Adi & Lita', 'Rina & Budi', '081344556677', 'hana@example.com', 'Pernikahan modern', 'location_photos/photo15.jpg', 'image/photo15.jpg', NULL, 'MC interaktif, musik akustik', 'normal', 0, 'diterima', '2025-08-14 03:24:33', '2025-08-21 10:04:09'),
(135, 'BK2508171Y6P', NULL, 5, 2, 0, 0, '2025-09-07', '10:18:00', '12:18:00', 'Alun-Alun Bandung, Balonggede, Regol, Kota Bandung, Jawa Barat, Jawa, Indonesia', -6.921678, 107.607151, 'Sonia & Joshua', NULL, 'Ujang & Nana', 'Agus dan Warni', '0895329252292', 'sonia@gmail.com', 'Hijau', 'location_photos/IYRWEfgFXrIhJnYMvVV2O5ZB0e6uxtChfyey0Drj.png', 'image/aAYtcM8DyMr81aPv1mqAJQ6ppoEkPWJZCIAzj6Qz.png', NULL, NULL, 'normal', 0, 'tertunda', '2025-08-17 14:20:39', '2025-08-17 17:28:51'),
(137, 'BK250818065Q', NULL, 3, 3, 4000000, 2000000, '2025-09-01', '08:00:00', '09:30:00', 'Sangkuriang, Gang Saad, Kotamas, Cimahi, Jawa Barat, Jawa, 40526, Indonesia', -6.868951, 107.536471, 'Yati dan Yuda', NULL, 'Nana dan Amin', 'Hida dan Ujang', '0897426613813', 'santi@gmail.com', 'pink', NULL, 'image/JqK4hps2TXPz6FADeelPhfa20RcCoYSBZkraanQ3.jpg', NULL, NULL, 'normal', 0, 'tertunda', '2025-08-18 14:58:28', '2025-08-18 14:58:53'),
(138, 'BK250818LGJE', NULL, 5, 1, 3250000, 1625000, '2025-09-05', '10:30:00', '12:00:00', 'Jalan Singosari Raya, Melong, Cimahi, Jawa Barat, Jawa, 40535, Indonesia (Lat: -6.916292, Lon: 107.5597306)', -6.916292, 107.559731, 'Meila dan M.Salahuddin', NULL, 'Ujang & Nana', 'Alex & Nia', '089564615487', 'sonia@gmail.com', 'Ungu', NULL, 'image/Mm0FYmEAGhIW1sFKY7NCDjjXd3KbGtpWLO2dYUVO.jpg', NULL, NULL, 'normal', 0, 'tertunda', '2025-08-18 15:57:44', '2025-08-18 15:58:01'),
(139, 'BK250819ZERT', NULL, 5, 1, 3250000, 1625000, '2025-08-30', '06:30:00', '08:00:00', 'Rancabolang, Jalan Soekarno-Hatta, Sekejati, Buahbatu, Kota Bandung, Jawa Barat, Jawa, 40286, Indonesia', -6.939327, 107.663316, 'Meila dan M.Salahuddin', NULL, 'Ujang & Nana', 'Agus dan Warni', '089564615487', 'sonia@gmail.com', 'Pink', NULL, 'image/ZxC06xuISS4Pp0zMxbg9KrRQN97BreUGEvOVAyZp.jpg', NULL, NULL, 'normal', 0, 'diterima', '2025-08-19 06:18:18', '2025-08-25 06:11:44'),
(143, 'BK250825MQRB', 'khitan', 3, 1, 3250000, 1625000, '2025-08-30', '10:00:00', '11:00:00', 'Sangkuriang, Gang Saad, Kotamas, Cimahi, Jawa Barat, Jawa, 40526, Indonesia', -6.868951, 107.536471, 'Santi', 'Yuda', NULL, NULL, '089564615487', 'santi@gmail.com', 'Hijau', NULL, 'payment_proofs/PnjgOnDqRJD874Ym0CTqIy9lpKfVrc7zV7RSlb00.jpg', NULL, NULL, 'normal', 0, 'tertunda', '2025-08-25 01:53:28', '2025-08-25 01:53:28'),
(144, 'BK2508254IP5', 'grand_opening', 3, 3, 4000000, 2000000, '2025-08-30', '11:00:00', '13:30:00', 'Grand Hotel Preanger, 81, Jalan Asia Afrika, Braga, Sumur Bandung, Kota Bandung, Jawa Barat, Jawa, 40621, Indonesia', -6.921343, 107.611654, 'Jati', 'Opening Demon Hunter', NULL, NULL, '08565416464', 'santi@gmail.com', 'Biru', NULL, 'payment_proofs/gbGj8oDq4A6QRHXCicIGMXfbMyfsLIXYWevW43U3.jpg', NULL, NULL, 'normal', 0, 'tertunda', '2025-08-25 01:55:57', '2025-08-25 01:55:57'),
(145, 'BK250825OUO7', 'pernikahan', 3, 4, 2000000, 1000000, '2025-08-30', '08:00:00', '10:00:00', 'Citopeng Sari, Melong, Cimahi, Jawa Barat, Jawa, 40535, Indonesia', -6.913128, 107.565043, 'Santi dan yuda', NULL, 'Ujang & Nana', 'Alex & Nia', '089564615487', 'santi@gmail.com', 'Abu', NULL, 'payment_proofs/VD8IRFkz874iQhLMW9E2hCh630V6ZmB9WovT02TV.jpg', NULL, NULL, 'normal', 0, 'tertunda', '2025-08-25 01:59:26', '2025-08-25 01:59:26'),
(146, 'BK250825HXXB', 'pernikahan', 5, 2, 3700000, 1850000, '2025-08-31', '07:00:00', '11:00:00', 'Sudirman Grand Ballroom, Jalan Jendral Sudirman, Dunguscariang, Andir, Kota Bandung, Jawa Barat, Jawa, 40211, Indonesia', -6.916833, 107.579692, 'Meila dan M.Salahuddin', NULL, 'Iyan dan Parni', 'Agus dan Warni', '0895329252292', 'sonia@gmail.com', 'Ungu', NULL, 'payment_proofs/QXqTUTSxKcVY3lHA3BiEpILBT777hRd82imJF1Fl.jpg', NULL, NULL, 'normal', 0, 'diterima', '2025-08-25 06:25:48', '2025-08-25 06:29:20'),
(147, 'BK250825YWRC', 'gathering', 5, 4, 2000000, 1000000, '2025-08-31', '11:00:00', '12:00:00', 'Citopeng Sari 2, Cibeureum, Cimahi, Jawa Barat, Jawa, 40535, Indonesia', -6.912057, 107.563722, 'Santi', 'Keluarga cemara', NULL, NULL, '089564615487', 'sonia@gmail.com', 'Hijau', NULL, 'payment_proofs/VOuJCctXaK9vRncWAvxCoEwPir4KCXW7RNFxVW6s.jpg', NULL, NULL, 'normal', 0, 'diterima', '2025-08-25 06:26:48', '2025-08-25 06:29:34'),
(148, 'BK250825FZOK', 'grand_opening', 5, 4, 2000000, 1000000, '2025-08-31', '08:27:00', '09:27:00', 'Cimahi, Jawa Barat, Jawa, Indonesia', -6.873153, 107.542310, 'Santi', 'Opening Demon Hunter', NULL, NULL, '081626486541', 'sonia@gmail.com', 'Ungu', NULL, 'payment_proofs/3WgQHI6CiWVCUznUfYgebFtbC2XiCd2obJjzGsbG.jpg', NULL, NULL, 'normal', 0, 'diterima', '2025-08-25 06:27:57', '2025-08-25 06:29:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `booking_performers`
--

CREATE TABLE `booking_performers` (
  `id` bigint UNSIGNED NOT NULL,
  `booking_id` bigint UNSIGNED NOT NULL,
  `performer_id` bigint UNSIGNED NOT NULL,
  `performer_role_id` bigint UNSIGNED DEFAULT NULL,
  `is_external` tinyint(1) NOT NULL DEFAULT '0',
  `confirmation_status` enum('tertunda','dikonfirmasi','ditolak','dibatalkan') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tertunda',
  `agreed_rate` decimal(12,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `booking_performers`
--

INSERT INTO `booking_performers` (`id`, `booking_id`, `performer_id`, `performer_role_id`, `is_external`, `confirmation_status`, `agreed_rate`, `created_at`, `updated_at`) VALUES
(730, 108, 8, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:33:04', '2025-08-21 09:33:04'),
(731, 108, 5, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:33:04', '2025-08-21 09:33:04'),
(732, 108, 19, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:33:04', '2025-08-21 09:33:04'),
(733, 108, 7, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:33:04', '2025-08-21 09:33:04'),
(734, 108, 9, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:33:04', '2025-08-21 09:33:04'),
(735, 108, 10, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:33:04', '2025-08-21 09:33:04'),
(736, 108, 11, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:33:04', '2025-08-21 09:33:04'),
(737, 108, 12, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:33:04', '2025-08-21 09:33:04'),
(738, 108, 15, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:33:04', '2025-08-21 09:33:04'),
(739, 108, 17, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:33:04', '2025-08-21 09:33:04'),
(740, 108, 1, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:33:04', '2025-08-21 09:33:04'),
(741, 108, 4, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:33:04', '2025-08-21 09:33:04'),
(742, 108, 3, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:33:04', '2025-08-21 09:33:04'),
(743, 108, 20, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:33:04', '2025-08-21 09:33:04'),
(744, 110, 8, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:39:44', '2025-08-21 09:39:44'),
(745, 110, 5, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:39:44', '2025-08-21 09:39:44'),
(746, 110, 6, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:39:44', '2025-08-21 09:39:44'),
(747, 110, 7, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:39:44', '2025-08-21 09:39:44'),
(748, 110, 9, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:39:44', '2025-08-21 09:39:44'),
(750, 110, 10, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:39:44', '2025-08-21 09:39:44'),
(751, 110, 11, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:39:44', '2025-08-21 09:39:44'),
(753, 110, 4, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:39:44', '2025-08-21 09:39:44'),
(754, 110, 3, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:39:44', '2025-08-21 09:39:44'),
(755, 110, 20, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:39:44', '2025-08-21 09:39:44'),
(756, 110, 2, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:39:44', '2025-08-21 09:39:44'),
(769, 112, 8, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:41:30', '2025-08-21 09:41:30'),
(770, 112, 5, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:41:30', '2025-08-21 09:41:30'),
(771, 112, 6, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:41:30', '2025-08-21 09:41:30'),
(772, 112, 7, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:41:30', '2025-08-21 09:41:30'),
(773, 112, 9, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:41:30', '2025-08-21 09:41:30'),
(775, 112, 11, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:41:30', '2025-08-21 09:41:30'),
(777, 112, 15, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:41:30', '2025-08-21 09:41:30'),
(778, 112, 17, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:41:30', '2025-08-21 09:41:30'),
(779, 112, 4, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:41:30', '2025-08-21 09:41:30'),
(780, 112, 3, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:41:30', '2025-08-21 09:41:30'),
(781, 112, 20, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:41:30', '2025-08-21 09:41:30'),
(782, 112, 2, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 09:41:30', '2025-08-21 09:41:30'),
(788, 114, 8, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 10:04:09', '2025-08-21 10:04:09'),
(789, 114, 5, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 10:04:09', '2025-08-21 10:04:09'),
(790, 114, 6, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 10:04:09', '2025-08-21 10:04:09'),
(791, 114, 7, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 10:04:09', '2025-08-21 10:04:09'),
(792, 114, 9, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 10:04:09', '2025-08-21 10:04:09'),
(794, 114, 10, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 10:04:09', '2025-08-21 10:04:09'),
(795, 114, 11, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 10:04:09', '2025-08-21 10:04:09'),
(797, 114, 4, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 10:04:09', '2025-08-21 10:04:09'),
(798, 114, 3, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 10:04:09', '2025-08-21 10:04:09'),
(799, 114, 20, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 10:04:09', '2025-08-21 10:04:09'),
(800, 114, 2, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 10:04:09', '2025-08-21 10:04:09'),
(815, 113, 10, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 12:28:36', '2025-08-21 12:28:36'),
(816, 113, 11, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 12:28:36', '2025-08-21 12:28:36'),
(817, 113, 12, NULL, 0, 'dikonfirmasi', NULL, '2025-08-21 12:28:36', '2025-08-21 12:28:36'),
(837, 139, 8, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:11:44', '2025-08-25 06:11:44'),
(838, 139, 5, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:11:44', '2025-08-25 06:11:44'),
(839, 139, 6, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:11:44', '2025-08-25 06:11:44'),
(840, 139, 7, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:11:44', '2025-08-25 06:11:44'),
(841, 139, 9, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:11:44', '2025-08-25 06:11:44'),
(842, 139, 10, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:11:44', '2025-08-25 06:11:44'),
(844, 139, 11, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:11:44', '2025-08-25 06:11:44'),
(845, 139, 12, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:11:44', '2025-08-25 06:11:44'),
(846, 139, 4, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:11:44', '2025-08-25 06:11:44'),
(847, 139, 3, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:11:44', '2025-08-25 06:11:44'),
(848, 139, 2, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:11:44', '2025-08-25 06:11:44'),
(849, 139, 1, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:11:44', '2025-08-25 06:11:44'),
(850, 146, 8, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:29:20', '2025-08-25 06:29:20'),
(851, 146, 5, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:29:20', '2025-08-25 06:29:20'),
(852, 146, 6, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:29:20', '2025-08-25 06:29:20'),
(853, 146, 7, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:29:20', '2025-08-25 06:29:20'),
(854, 146, 9, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:29:20', '2025-08-25 06:29:20'),
(855, 146, 10, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:29:20', '2025-08-25 06:29:20'),
(856, 146, 11, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:29:20', '2025-08-25 06:29:20'),
(857, 146, 12, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:29:20', '2025-08-25 06:29:20'),
(858, 146, 4, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:29:20', '2025-08-25 06:29:20'),
(859, 146, 3, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:29:20', '2025-08-25 06:29:20'),
(860, 146, 2, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:29:20', '2025-08-25 06:29:20'),
(861, 146, 1, NULL, 0, 'dikonfirmasi', NULL, '2025-08-25 06:29:20', '2025-08-25 06:29:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_5c785c036466adea360111aa28563bfd556b5fba', 'i:4;', 1756300658),
('laravel_cache_5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1756300658;', 1756300658);

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `events`
--

CREATE TABLE `events` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('upacara_adat','siraman','sisingaan','lainnya') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('aktif','nonaktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `events`
--

INSERT INTO `events` (`id`, `name`, `price`, `description`, `image`, `type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PAKET A', 3250000.00, 'Paket ini cocok untuk pembukaan acara dengan nuansa adat Sunda yang anggun dan penuh makna.\r\nIncludes:\r\n- Tari Mojang / Badaya\r\n- Pertunjukan suling/biola\r\n- Sinden\r\n- Kacapi\r\n- Kendang\r\n- Perkusi\r\n- Pemayang 4 orang\r\n- Baksa / Payung\r\n- Ambu \r\n- Lengser', 'events/Chi8RtHkWuz5FDQdl4KvQTeZPztQWmwuNF7BoHiv.jpg', 'upacara_adat', 'aktif', '2025-07-14 18:56:43', '2025-07-15 03:06:00'),
(2, 'PAKET B', 3700000.00, 'Paket ini menampilkan tarian enerjik perpaduan kendang rampak dan mojang yang memukau.\r\nIncludes:\r\n- Tari Rampak Kendang & Mojang\r\n- Pertunjukan suling/biola\r\n- Sinden\r\n- Kacapi\r\n- Kendang\r\n- Perkusi\r\n- Pemayang 4 orang\r\n- Baksa / Payung\r\n- Ambu\r\n- Lengser\r\n- Firework (kembang api) 2 titik', 'events/BW17Is4y1H8yE68V727n1xPPjJ1Se9Jqrjs0oqXe.jpg', 'upacara_adat', 'aktif', '2025-07-14 19:12:00', '2025-07-14 20:25:34'),
(3, 'PAKET C', 4000000.00, 'Paket komplit untuk acara megah yang memadukan tari adat dan kisah pewayangan yang elegan.\r\nIncludes:\r\n- Tari Rampak Kendang & Mojang + Tari Rama & Sinta\r\n- Pertunjukan suling/biola\r\n- Sinden\r\n- Kacapi\r\n- Kendang\r\n- Perkusi\r\n- Pemayang 4 orang\r\n- Baksa / Payung\r\n- Ambu\r\n- Lengser\r\n- Tari Rama & Sinta\r\n- Firework (kembang api) 2 titik', 'events/ivP73HF4obPjaYM13kaqa8DtUYLBmgbY3QnZkjdB.jpg', 'upacara_adat', 'aktif', '2025-07-14 19:19:28', '2025-07-14 20:25:51'),
(4, 'SIRAMAN', 2000000.00, 'Paket khusus untuk prosesi siraman dengan suasana sakral dan penuh estetika.\r\nIncludes:\r\n- Sound system\r\n- Bunga melati\r\n- Gentong dan alat siraman lengkap\r\n- Sinden\r\n- Kacapi\r\n- Suling', 'events/Y8QQ10zSiwnk8KxAdqTClnLaPc3ZhL4yD3aIMV0G.jpg', 'siraman', 'aktif', '2025-07-14 19:46:28', '2025-07-14 20:26:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(6, '2025_07_08_035405_create_permission_tables', 1),
(10, '2025_07_01_062049_create_performers_table', 2),
(11, '2025_07_03_020448_create_events_table', 3),
(12, '2025_07_09_024959_create_bookings_table', 4),
(13, '2025_07_09_025031_create_locations_table', 5),
(14, '2025_07_15_003302_create_location_estimates_table', 6),
(15, '2025_07_15_003931_create_booking_performers_table', 7),
(16, '2025_07_28_072011_create_notifications_table', 8),
(17, '2025_08_12_212211_create_performer_roles_table', 9),
(18, '2025_08_12_223402_create_schedules_table', 10),
(19, '2025_08_13_163244_create_performer_requirements_table', 11);

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 4),
(2, 'App\\Models\\User', 5),
(2, 'App\\Models\\User', 7);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('4f3b5c29-4b46-4eb6-ba66-5f338862c415', 'App\\Notifications\\NewBookingNotification', 'App\\Models\\User', 1, '{\"booking_code\":\"7QMGMOR37N\",\"client_name\":null,\"date\":\"2025-08-10\",\"time\":\"10:39 - 12:39\",\"id\":55}', NULL, '2025-07-28 00:47:44', '2025-07-28 00:47:44');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `performers`
--

CREATE TABLE `performers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('laki-laki','perempuan','lainnya') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `performer_role_id` bigint UNSIGNED NOT NULL,
  `is_active` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_number` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_external` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `performers`
--

INSERT INTO `performers` (`id`, `name`, `gender`, `performer_role_id`, `is_active`, `phone`, `account_number`, `bank_name`, `is_external`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'Santi', 'perempuan', 15, 1, '62895393908145', '6395709210', 'BCA', 0, '-', '2025-07-14 21:30:33', '2025-08-14 03:06:44'),
(2, 'Mey', 'perempuan', 15, 1, '6283116898440', '-', '-', 0, '-', '2025-07-14 21:43:26', '2025-08-14 03:06:51'),
(3, 'Diana', 'perempuan', 15, 1, '-', '-', '-', 0, '-', '2025-07-14 21:43:58', '2025-08-14 03:06:58'),
(4, 'Anya', 'perempuan', 15, 1, '-', '-', '-', 0, '-', '2025-07-14 21:45:12', '2025-08-14 03:07:05'),
(5, 'Mang Asep', 'laki-laki', 3, 1, '-', '-', '-', 0, '-', '2025-07-14 21:45:41', '2025-08-14 03:07:43'),
(6, 'Amat', 'laki-laki', 4, 1, '-', '-', '-', 0, '-', '2025-07-14 21:46:23', '2025-08-14 03:07:55'),
(7, 'Olik', 'laki-laki', 5, 1, '6285603417990', '-', '-', 0, '-', '2025-07-14 21:47:44', '2025-08-14 03:08:06'),
(8, 'Abah Bayu', 'laki-laki', 2, 1, '6283149299818', '-', '-', 0, '-', '2025-07-14 21:48:37', '2025-08-14 03:08:20'),
(9, 'Rommy', 'laki-laki', 8, 1, '6282114831071', '-', '-', 0, '-', '2025-07-14 21:49:30', '2025-08-14 03:08:43'),
(10, 'Ovi', 'laki-laki', 10, 1, '6283181073353', '-', '-', 0, '-', '2025-07-14 21:51:33', '2025-08-14 03:08:55'),
(11, 'Haikal', 'laki-laki', 11, 1, '083818970782', '-', '-', 0, '-', '2025-07-14 21:52:18', '2025-08-17 12:59:59'),
(12, 'Mamah Nia', 'perempuan', 12, 1, '083873532446', '-', '-', 0, '-', '2025-07-14 21:53:11', '2025-08-17 13:00:10'),
(13, 'Sonia', 'perempuan', 14, 1, '085813666926', '085813666926', 'DANA', 1, '-', '2025-07-14 21:54:59', '2025-08-17 13:00:25'),
(14, 'Risma', 'perempuan', 15, 1, '-', '-', '-', 1, NULL, '2025-08-11 06:45:04', '2025-08-17 12:59:03'),
(15, 'Onami', 'laki-laki', 13, 1, '-', '-', '-', 1, NULL, '2025-08-11 06:45:57', '2025-08-14 03:10:18'),
(16, 'hyuni', 'laki-laki', 5, 1, '-', '-', '-', 1, NULL, '2025-08-11 06:47:31', '2025-08-17 12:59:17'),
(17, 'Aprilla', 'perempuan', 14, 1, '-', '-', '-', 1, NULL, '2025-08-11 06:47:59', '2025-08-14 03:11:06'),
(18, 'Jajang', 'laki-laki', 3, 1, '081626486541', '-', '-', 1, NULL, '2025-08-11 06:49:09', '2025-08-17 12:59:40'),
(19, 'Dadan', 'laki-laki', 4, 1, '-', '-', '-', 1, NULL, '2025-08-11 06:49:42', '2025-08-14 03:11:28'),
(20, 'Lola', 'perempuan', 15, 1, '-', '-', '-', 1, NULL, '2025-08-12 15:28:52', '2025-08-14 03:11:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `performer_requirements`
--

CREATE TABLE `performer_requirements` (
  `id` bigint UNSIGNED NOT NULL,
  `event_id` bigint UNSIGNED NOT NULL,
  `performer_role_id` bigint UNSIGNED NOT NULL,
  `quantity` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `performer_requirements`
--

INSERT INTO `performer_requirements` (`id`, `event_id`, `performer_role_id`, `quantity`, `notes`, `created_at`, `updated_at`) VALUES
(60, 3, 2, 1, NULL, '2025-08-14 03:04:55', '2025-08-14 03:04:55'),
(61, 3, 10, 1, NULL, '2025-08-14 03:04:55', '2025-08-14 03:04:55'),
(62, 3, 5, 1, 'Baksa / Payung', '2025-08-14 03:04:55', '2025-08-14 03:04:55'),
(63, 3, 3, 1, NULL, '2025-08-14 03:04:55', '2025-08-14 03:04:55'),
(64, 3, 4, 1, NULL, '2025-08-14 03:04:55', '2025-08-14 03:04:55'),
(65, 3, 8, 1, NULL, '2025-08-14 03:04:55', '2025-08-14 03:04:55'),
(66, 3, 11, 1, 'Suling/biola', '2025-08-14 03:04:55', '2025-08-14 03:04:55'),
(67, 3, 12, 1, 'Sinden', '2025-08-14 03:04:55', '2025-08-14 03:04:55'),
(68, 3, 15, 4, 'Pemayang 4 orang', '2025-08-14 03:04:55', '2025-08-14 03:04:55'),
(69, 3, 13, 1, NULL, '2025-08-14 03:04:55', '2025-08-14 03:04:55'),
(70, 3, 14, 1, NULL, '2025-08-14 03:04:55', '2025-08-14 03:04:55'),
(80, 2, 2, 1, NULL, '2025-08-14 03:05:18', '2025-08-14 03:05:18'),
(81, 2, 10, 1, NULL, '2025-08-14 03:05:18', '2025-08-14 03:05:18'),
(82, 2, 5, 1, 'Baksa / Payung', '2025-08-14 03:05:18', '2025-08-14 03:05:18'),
(83, 2, 3, 1, NULL, '2025-08-14 03:05:18', '2025-08-14 03:05:18'),
(84, 2, 4, 1, NULL, '2025-08-14 03:05:18', '2025-08-14 03:05:18'),
(85, 2, 8, 1, NULL, '2025-08-14 03:05:18', '2025-08-14 03:05:18'),
(86, 2, 11, 1, 'Suling/biola', '2025-08-14 03:05:18', '2025-08-14 03:05:18'),
(87, 2, 12, 1, 'Sinden', '2025-08-14 03:05:18', '2025-08-14 03:05:18'),
(88, 2, 15, 4, 'Pemayang 4 orang', '2025-08-14 03:05:18', '2025-08-14 03:05:18'),
(93, 4, 7, 2, NULL, '2025-08-18 08:26:33', '2025-08-18 08:26:33'),
(94, 4, 10, 1, NULL, '2025-08-18 08:26:33', '2025-08-18 08:26:33'),
(95, 4, 11, 1, 'Suling', '2025-08-18 08:26:33', '2025-08-18 08:26:33'),
(96, 4, 12, 1, 'Sinden', '2025-08-18 08:26:33', '2025-08-18 08:26:33'),
(97, 1, 2, 1, NULL, '2025-08-29 13:33:21', '2025-08-29 13:33:21'),
(98, 1, 3, 1, NULL, '2025-08-29 13:33:21', '2025-08-29 13:33:21'),
(99, 1, 4, 1, NULL, '2025-08-29 13:33:21', '2025-08-29 13:33:21'),
(100, 1, 5, 1, 'Baksa / Payung', '2025-08-29 13:33:21', '2025-08-29 13:33:21'),
(101, 1, 8, 1, NULL, '2025-08-29 13:33:21', '2025-08-29 13:33:21'),
(102, 1, 10, 1, NULL, '2025-08-29 13:33:21', '2025-08-29 13:33:21'),
(103, 1, 11, 1, 'Suling/biola', '2025-08-29 13:33:21', '2025-08-29 13:33:21'),
(104, 1, 12, 1, 'Sinden', '2025-08-29 13:33:21', '2025-08-29 13:33:21'),
(105, 1, 15, 4, 'Pemayang 4 Orang', '2025-08-29 13:33:21', '2025-08-29 13:33:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `performer_roles`
--

CREATE TABLE `performer_roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `performer_roles`
--

INSERT INTO `performer_roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(2, 'Pemusik - Kendang', '2025-08-12 14:33:03', '2025-08-12 14:33:03'),
(3, 'Lengser', '2025-08-12 14:33:03', '2025-08-12 14:33:03'),
(4, 'Ambu', '2025-08-12 14:33:03', '2025-08-12 14:33:03'),
(5, 'Baksa', '2025-08-12 14:33:03', '2025-08-12 14:33:03'),
(6, 'MC', '2025-08-12 14:33:03', '2025-08-12 14:33:03'),
(7, 'Stage Crew', '2025-08-12 14:33:03', '2025-08-12 14:33:03'),
(8, 'Pemusik - Perkusi', NULL, NULL),
(10, 'Pemusik - Kacapi', '2025-08-14 02:51:58', '2025-08-14 02:51:58'),
(11, 'Pemusik - Melodi', '2025-08-14 02:51:58', '2025-08-14 02:51:58'),
(12, 'Pemusik - Vokal', '2025-08-14 02:51:58', '2025-08-14 02:51:58'),
(13, 'Penari - Rama', '2025-08-14 02:51:58', '2025-08-14 02:51:58'),
(14, 'Penari - Sinta', '2025-08-14 02:51:58', '2025-08-14 02:51:58'),
(15, 'Penari - Pemayang', '2025-08-14 02:51:58', '2025-08-14 02:51:58'),
(16, 'Penari - Rampak Kendang', '2025-08-14 02:51:58', '2025-08-16 14:47:22'),
(21, 'Pemusik - Sinden', '2025-08-17 17:14:04', '2025-08-17 17:14:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-07-07 21:24:58', '2025-07-07 21:24:58'),
(2, 'client', 'web', '2025-07-07 21:24:58', '2025-07-07 21:24:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('VidQ9qJj0BzOSn1BZkue4fh2oBKjQGkHUq1ZVUkN', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOU0zanhIVXRWaHRxV0VEVW1zMVpISnFCd2puQnNNcjFxWW43cVVsWCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTQ6Imh0dHA6Ly9rYW1hcmEtZXRobmljLnRlc3QvYWRtaW4vcGVuZ2F0dXJhbi1wYWtldC1hY2FyYSI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1756474403);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', NULL, '$2y$12$F4jaxHJhksn1CrW9HUwh/uomhwuP2lIiMByaeNiNZCf7s9XwylGsm', NULL, '2025-07-07 21:24:59', '2025-07-07 21:24:59'),
(3, 'Santi', 'santi@gmail.com', NULL, '$2y$12$LiYYAQmkHO9yUStDxr5WguVlbY0amc7DIxYbJXYipdwNN3kVRLPNS', NULL, '2025-07-07 22:22:52', '2025-07-07 22:22:52'),
(4, 'sanji', 'sanji@gmail.com', NULL, '$2y$12$m3RztJYMoUNpXeMYJ1fFaukLnw/qm/Ynn.3PfztsmjPuavQp/whfu', NULL, '2025-07-08 08:44:47', '2025-07-08 08:44:47'),
(5, 'Sonia', 'sonia@gmail.com', NULL, '$2y$12$5t2XZ4mmqdzBuKo0OG/tvenvsI0PT5d.IH50KJiQeThSVVThcVjQe', NULL, '2025-07-20 18:34:54', '2025-07-20 18:34:54'),
(7, 'risma', 'risma@gmail.com', NULL, '$2y$12$lNxs6HntHPpRfKQ4KLFAheLUa01QMwaCE1piHSOjRdbFKrwXBYfHW', NULL, '2025-07-28 19:07:49', '2025-07-28 19:07:49'),
(8, 'Siti Sumiati', 'siti@example.com', NULL, '$2y$12$A5UBQYbSveRIZWCWvA2eVOfYAe5Dp0VV5nc54Cl207Fcm.txqbpi.', NULL, '2025-08-13 07:13:38', '2025-08-13 07:13:38'),
(9, 'Rina Wijaya', 'rina@example.com', NULL, '$2y$12$.4nDbERZlVr66aOBp6BDzu1EuEB1wB3doOhOSqhtEMKQpJ.QAf.Xa', NULL, '2025-08-13 07:13:38', '2025-08-13 07:13:38');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bookings_booking_code_unique` (`booking_code`),
  ADD KEY `fk_bookings_user` (`user_id`),
  ADD KEY `idx_bookings_date_status` (`date`,`status`),
  ADD KEY `idx_bookings_date_start_time` (`date`,`start_time`),
  ADD KEY `idx_bookings_event_date` (`event_id`,`date`);

--
-- Indeks untuk tabel `booking_performers`
--
ALTER TABLE `booking_performers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_performers_booking_id_performer_id_unique` (`booking_id`,`performer_id`),
  ADD UNIQUE KEY `uq_booking_performer` (`booking_id`,`performer_id`),
  ADD UNIQUE KEY `uniq_booking_performer` (`booking_id`,`performer_id`),
  ADD KEY `idx_perf_time` (`performer_id`),
  ADD KEY `idx_role` (`performer_role_id`),
  ADD KEY `idx_confirmation_status` (`confirmation_status`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_events_status` (`status`),
  ADD KEY `idx_events_type` (`type`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `performers`
--
ALTER TABLE `performers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_performer_role` (`performer_role_id`),
  ADD KEY `idx_performers_active` (`is_active`);

--
-- Indeks untuk tabel `performer_requirements`
--
ALTER TABLE `performer_requirements`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_event_role` (`event_id`,`performer_role_id`),
  ADD KEY `performer_requirements_performer_role_id_foreign` (`performer_role_id`);

--
-- Indeks untuk tabel `performer_roles`
--
ALTER TABLE `performer_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_role_name` (`name`);

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT untuk tabel `booking_performers`
--
ALTER TABLE `booking_performers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=872;

--
-- AUTO_INCREMENT untuk tabel `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `performers`
--
ALTER TABLE `performers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT untuk tabel `performer_requirements`
--
ALTER TABLE `performer_requirements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT untuk tabel `performer_roles`
--
ALTER TABLE `performer_roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bookings_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `booking_performers`
--
ALTER TABLE `booking_performers`
  ADD CONSTRAINT `booking_performers_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_performers_performer_id_foreign` FOREIGN KEY (`performer_id`) REFERENCES `performers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bp_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bp_performer` FOREIGN KEY (`performer_id`) REFERENCES `performers` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `performers`
--
ALTER TABLE `performers`
  ADD CONSTRAINT `fk_performer_role` FOREIGN KEY (`performer_role_id`) REFERENCES `performer_roles` (`id`);

--
-- Ketidakleluasaan untuk tabel `performer_requirements`
--
ALTER TABLE `performer_requirements`
  ADD CONSTRAINT `performer_requirements_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `performer_requirements_performer_role_id_foreign` FOREIGN KEY (`performer_role_id`) REFERENCES `performer_roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
