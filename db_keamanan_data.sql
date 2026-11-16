-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 16 Nov 2026 pada 12.08
-- Versi server: 11.8.6-MariaDB-log
-- Versi PHP: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `db_keamanan_data`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin_contents`
--

CREATE TABLE `admin_contents` (
  `id` int(11) NOT NULL,
  `encrypted_title` text NOT NULL,
  `encrypted_payload` longtext NOT NULL,
  `iv` varchar(255) NOT NULL,
  `encrypted_key` text NOT NULL,
  `private_key` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data untuk tabel `admin_contents`
--

INSERT INTO `admin_contents` (`id`, `encrypted_title`, `encrypted_payload`, `iv`, `encrypted_key`, `private_key`, `created_at`) VALUES
(1, 'wpdRgMMYbPKZe+V9wiXz+g==', 'cIIwE3BXbBvGQYD1X037yA==', 'BsmTNbzdhuiLCu3xvSVPDw==', 'MrfmNcEJC6eGUxCnvUpAvG0HdfI/YGdCJqdmDE3DHl9ouovjjG49gOFZGiOMtp9gE7sLV/bw5wgMvToyj7kAvzg46pXZV25N5eMD9QKFfqTaQIZghXzPBfilvVYIi2bBud6G2avGAxuZUCuAaZ56vJFcZAbsfhKToVewwY0shfMN5D0g9cMc8QW9mIUECXTT2sKkdgYRZNriIp6Yb3GF4VBD2nWJtZn4/GAKhRf+n9+mSu1lyCiX3wEawsdpY84SYwwOw1p6HOp59jhsWUoHtGJYXcDYVJhynQudOfkPU5xF9F5nr3iCivy5bEtYT89DRUDNwsHyl09M0EQw8uf39Q==', 'LS0tLS1CRUdJTiBQUklWQVRFIEtFWS0tLS0tCk1JSUV2UUlCQURBTkJna3Foa2lHOXcwQkFRRUZBQVNDQktjd2dnU2pBZ0VBQW9JQkFRRExGU2tKYzU2TFEzYXoKTHJndmJQb3lQSEdpb2c3ejB4Z0p3a3pRcklLczIwUlYyQWcxVkM4MitSdEh3Yi9vNTNpT0xFRzdWZmhGRDNqMApabGhlM2U0bjFBZDUwZ25rOGIzL052VSs2ckptNXpQTnFOMUp6YWxsQ3Y1YlpmWk5vcndyMnNzQzl2ODRmQllTCmJLVlZ4Wjd1SFJjc1Ywc0xvdlZjWFI0c0NRMGltSnVzYk4ycGZtU0l2QlU1RURpcFF2NzNWZTlYMkdPL1JzVXgKTEZSekFiMmhjQU9yZm11U0ZZWmpzU0JGSkZjOTJtN2VsNVQ0cGpHU09ZMkltdHRtNDl3RkNtTGkvL0R3ZEZjdQpjZ0NyQzh1R0gvTUxyV01USE9MWkJEa1N5QUxWQ25WWWI5R1dCdXBxL3l4SGx0cU1Pc3JUOSsrVjB3RE92a2xWCno3NEtSaUEvQWdNQkFBRUNnZ0VBTmtVWjJkSkJjSkxmUWlQK2lZSDBYSDlsbUtINUgvUnQ3SkcxYUdCcGJWc04KS0dtYVMvSUt5Z1NJNDBTbURjM3BuQWk4cWh6Zncwak91SFV2TVc3YVNWOXlmZjRBc2VOZU5Ea1NFYld6Wmlkcgo0dzQ0MEpPNnJHdGpMTGFUT3d6OWZ6U3hZc0h4MHp6cHd4T3duVHE1N1lUdG4rWldBaTZTcGhPbVQvczlQTnlUCjhsczJ4NjlaSStXOUFuMXQzVVZwRnpuTHJkYXpBekxTdU9XQ1REa3R1QUlwZWVzYWJGckhCSUZBQnZpZkgrSHAKR1AydEJBSjRwMk9TaHVLU24weWh2Ui9nQzJrQVNkTHFidVVHU0pWTFkxYys0U0lrdGRCQlFwcWo1YXk4ellzMQpYNkNYMXVCTHdIZFFYTTU0MlhTYWVpL013cHMrSURiZHVQNmEzQVZFQVFLQmdRRDdJRHR3V01lN1I2MGE2cGExCmNEL2wrTTdwRFpKMW1hWXlvdHVlK3c2bjZrcU9ieVNqRzVRZnIwdmFMVmhlYjhoT2ZqUy9HeUIraktFanhlSDgKeUdVbWZKTTRUbTZ1TFpLYUZvUE9KR2c2MlZBYUJsN0hZMTlVaW5zRllvQVdkQW05ZG1odHRxdGdZbnBGN1BULwpUQ2pvZGc4UjI4MktJZm1DWkhRVmJYMTBQd0tCZ1FEUEJqZEZuYjZxcXgyMm5pbG5sZldmWEpzanJSMzUzeDJsCmd1Y3c2bC9ENkRCb0VscWo5dElLRS90QzZ2TGYybW9qTXZkVkdVNmtFQ2N1ZFhuQWhFU1BtdERiQjJLM1p3dXYKd0NzNDB3eTQ3ZGwvemt0bTVaR3o4c2plRmNudURraUtwUkZqaTBiajhNQWQ4dnZDQWR2OXcwRXRZbzBvNnphLwp4SXMvVkZ4VUFRS0JnQUdEZVRnOUlMOE5PSXJmL1JhTTNhVzZNUzBRYXVDUWs0TGNubXcvNFIrNzlzVVRBR3FIClFRWkwzUzRzZGNyajY1WWxocFhjU1A3YVJDSmVEL1doRTVMK3p6aFlxS3dlRkRNZmpaM1JkRnRlY2ZBbU42NEYKK0U0Zkk0eXR3azNnVnlxd0t3TDJSZVFuMDhyV0djZnNJcGI4RnkwcFlQekRKYWpXaUNpVTUxdXJBb0dBVzlkMApHY3c3VTllSjNRUVpkanpPdEo0T3JTc1Z5MDR5QnNVbDcwdHFwS3lUSFBoWGdibnp3RWpSQlFUYUxuams4U3loCkVQMmpKNkdLNEU5NWJmQy93YjE2dmxCdWVOS2NHcUgwQ3NlR00xeEJTek9udThSSWVUNVI0a1JrU3RuSFVNcWUKQmowNWU5SElTUUVNc3lyRC83WHBha3I0T0pqd3R1NW9uL1c2NkFFQ2dZRUExQTM3eTFnb25lenhSWmp5Sjg2Mgo0K2VJUkFzSWlzRVlyeEFLQUdUa1FsQTVxTEhOaUpNMlF2cWo1cXBVQVR2NkpuSVJVUFAyK2xDckNxcnVVU2xBCmlYeEVNM3pmNDF5cS9mcWNJQ2Y5RkRyNDlsa0FKaDZnRndkQ1EzdnJCamhMOVpOemVwVGJPa240S0pGMHNZM0EKQTZ0QnZwanR4WjlvcDJSOGZwQ09oS009Ci0tLS0tRU5EIFBSSVZBVEUgS0VZLS0tLS0K', '2026-11-16 11:08:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `created_at`) VALUES
(1, 'admin', '$2y$12$D7FpPSJAXUdd2B5fIYGG4Ohunh1csf5Z3JCwMV0Nc0UOQLkrXupem', 'Administrator Sistem', '2026-11-16 11:07:30');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `admin_contents`
--
ALTER TABLE `admin_contents`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin_contents`
--
ALTER TABLE `admin_contents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
