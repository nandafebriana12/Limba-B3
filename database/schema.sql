-- ==========================================
-- DATABASE SISTEM TRACKING LIMBAH B3
-- ==========================================

CREATE DATABASE limbah_b3;
GO

USE limbah_b3;
GO

-- ==========================================
-- 1. TABEL UPT
-- ==========================================

CREATE TABLE upts (
    id INT IDENTITY(1,1) PRIMARY KEY,
    kode_upt VARCHAR(50) NOT NULL UNIQUE,
    nama_upt VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT GETDATE()
);
GO

-- ==========================================
-- 2. TABEL USERS
-- ==========================================

CREATE TABLE users (
    id INT IDENTITY(1,1) PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL
        CHECK(role IN ('admin','upt')),
    upt_id INT NULL,
    created_at DATETIME DEFAULT GETDATE(),

    CONSTRAINT FK_users_upt
    FOREIGN KEY (upt_id)
    REFERENCES upts(id)
    ON DELETE SET NULL
);
GO

-- ==========================================
-- 3. MASTER DATA SAMPAH
-- ==========================================

CREATE TABLE sampah_b3 (

    id INT IDENTITY(1,1) PRIMARY KEY,

    kode_sampah VARCHAR(50) NOT NULL UNIQUE,

    jenis_b3 VARCHAR(10) NOT NULL
        CHECK(jenis_b3 IN ('B3P','B3C','NB3')),

    nama_sampah VARCHAR(255) NOT NULL,

    satuan VARCHAR(50) NOT NULL,

    created_at DATETIME DEFAULT GETDATE()

);
GO

-- ==========================================
-- 4. TABEL VENDOR
-- ==========================================

CREATE TABLE vendors (

    id INT IDENTITY(1,1) PRIMARY KEY,

    kode_vendor VARCHAR(50) NOT NULL UNIQUE,

    nama_vendor VARCHAR(255) NOT NULL,

    alamat TEXT,

    kontak VARCHAR(100),

    created_at DATETIME DEFAULT GETDATE()

);
GO

-- ==========================================
-- 5. TRANSAKSI MASUK
-- ==========================================

CREATE TABLE transaksi_masuk (

    id INT IDENTITY(1,1) PRIMARY KEY,

    no_transaksi VARCHAR(50) NOT NULL UNIQUE,

    user_id INT NOT NULL,

    tanggal_masuk DATETIME NOT NULL,

    keterangan TEXT,

    created_at DATETIME DEFAULT GETDATE(),

    CONSTRAINT FK_transaksimasuk_user
    FOREIGN KEY(user_id)
    REFERENCES users(id)

);
GO

-- ==========================================
-- 6. DETAIL MASUK
-- ==========================================

CREATE TABLE detail_masuk (

    id INT IDENTITY(1,1) PRIMARY KEY,

    transaksi_masuk_id INT NOT NULL,

    sampah_b3_id INT NOT NULL,

    quantity DECIMAL(18,2) NOT NULL,

    CONSTRAINT FK_detailmasuk_header
    FOREIGN KEY(transaksi_masuk_id)
    REFERENCES transaksi_masuk(id)
    ON DELETE CASCADE,

    CONSTRAINT FK_detailmasuk_sampah
    FOREIGN KEY(sampah_b3_id)
    REFERENCES sampah_b3(id)

);
GO

-- ==========================================
-- 7. TRANSAKSI KELUAR
-- ==========================================

CREATE TABLE transaksi_keluar (

    id INT IDENTITY(1,1) PRIMARY KEY,

    no_transaksi VARCHAR(50) NOT NULL UNIQUE,

    vendor_id INT NOT NULL,

    tanggal_keluar DATETIME NOT NULL,

    keterangan TEXT,

    created_at DATETIME DEFAULT GETDATE(),

    CONSTRAINT FK_transaksikeluar_vendor
    FOREIGN KEY(vendor_id)
    REFERENCES vendors(id)

);
GO

-- ==========================================
-- 8. DETAIL KELUAR
-- ==========================================

CREATE TABLE detail_keluar (

    id INT IDENTITY(1,1) PRIMARY KEY,

    transaksi_keluar_id INT NOT NULL,

    sampah_b3_id INT NOT NULL,

    quantity DECIMAL(18,2) NOT NULL,

    CONSTRAINT FK_detailkeluar_header
    FOREIGN KEY(transaksi_keluar_id)
    REFERENCES transaksi_keluar(id)
    ON DELETE CASCADE,

    CONSTRAINT FK_detailkeluar_sampah
    FOREIGN KEY(sampah_b3_id)
    REFERENCES sampah_b3(id)

);
GO

-- ==========================================
-- DATA MASTER UPT
-- ==========================================

INSERT INTO upts (kode_upt, nama_upt)
VALUES
('U001','UPT Manufaktur'),
('U002','UPT Pemesinan'),
('U003','UPT Otomotif'),
('U004','UPT Alat Berat'),
('U005','UPT Sipil'),
('U006','UPT Perawatan'),
('U007','UPT Otomasi'),
('U008','UPT Desain & Metrologi'),
('U009','Produksi');
GO

-- ==========================================
-- DATA USER ADMIN
-- Password : admin123
-- ==========================================

INSERT INTO users
(username,password_hash,role)

VALUES

(
'admin',
'$2y$12$ogUgX2p67rQefB.sLCXTYu0JafxOC9xW.irhxrwZkAGmvJl3FFbkG',
'admin'
);
GO

-- ==========================================
-- DATA USER UPT
-- Password : upt123
-- ==========================================

INSERT INTO users
(username,password_hash,role,upt_id)

VALUES

(
'upt_ti',
'$2y$12$NCok.I0DZtKdPqP99eil1e6qswFFqxEDYrxg4eg7CvDDtI.SR4T2e',
'upt',
1
);
GO

-- ==========================================
-- MASTER DATA LIMBAH
-- ==========================================

INSERT INTO sampah_b3
(kode_sampah,jenis_b3,nama_sampah,satuan)

VALUES

('B3P001','B3P','Limbah terkontaminasi B3','Kg'),

('B3P002','B3P','Sludge logam mengandung minyak','Kg'),

('B3P003','B3P','Kain majun bekas (Used Rags)','Kg'),

('B3C001','B3C','Emulsi minyak proses cutting dan coolant','Liter'),

('B3C002','B3C','Limbah cat dan varnish','Liter'),

('B3C003','B3C','Minyak pelumas bekas (Oli hidrolik, oli mesin, oli gear)','Liter'),

('NB3001','NB3','Kertas','Kg'),

('NB3002','NB3','Plastik','Kg'),

('NB3003','NB3','Kardus','Kg'),

('NB3004','NB3','Botol','Kg');
GO

-- ==========================================
-- SELESAI
-- ==========================================

SELECT
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'transaksi_keluar';
-- ==========================================
USE limbah_b3;
GO
-- DATA DUMMY VENDORS (10 Data)
-- ==========================================
INSERT INTO vendors (kode_vendor, nama_vendor, alamat, kontak) VALUES
('V001', 'PT. Vendor Pengolah Limbah 1', 'Jl. Industri No. 1, Kawasan Pabrik', '081234567891'),
('V002', 'PT. Vendor Pengolah Limbah 2', 'Jl. Industri No. 2, Kawasan Pabrik', '081234567892'),
('V003', 'PT. Vendor Pengolah Limbah 3', 'Jl. Industri No. 3, Kawasan Pabrik', '081234567893'),
('V004', 'PT. Vendor Pengolah Limbah 4', 'Jl. Industri No. 4, Kawasan Pabrik', '081234567894'),
('V005', 'PT. Vendor Pengolah Limbah 5', 'Jl. Industri No. 5, Kawasan Pabrik', '081234567895'),
('V006', 'PT. Vendor Pengolah Limbah 6', 'Jl. Industri No. 6, Kawasan Pabrik', '081234567896'),
('V007', 'PT. Vendor Pengolah Limbah 7', 'Jl. Industri No. 7, Kawasan Pabrik', '081234567897'),
('V008', 'PT. Vendor Pengolah Limbah 8', 'Jl. Industri No. 8, Kawasan Pabrik', '081234567898'),
('V009', 'PT. Vendor Pengolah Limbah 9', 'Jl. Industri No. 9, Kawasan Pabrik', '081234567899'),
('V010', 'PT. Vendor Pengolah Limbah 10', 'Jl. Industri No. 10, Kawasan Pabrik', '0812345678910');
GO

-- ==========================================
-- DATA DUMMY TRANSAKSI MASUK (100 Data)
-- ==========================================
INSERT INTO transaksi_masuk (no_transaksi, user_id, tanggal_masuk, keterangan) VALUES
('TM0001', 2, '2026-06-02 08:32:00', 'Setoran rutin limbah mingguan 1'),
('TM0002', 2, '2026-02-13 09:28:00', 'Setoran rutin limbah mingguan 2'),
('TM0003', 2, '2026-12-28 16:43:00', 'Setoran rutin limbah mingguan 3'),
('TM0004', 2, '2026-06-06 09:45:00', 'Setoran rutin limbah mingguan 4'),
('TM0005', 2, '2026-05-02 08:28:00', 'Setoran rutin limbah mingguan 5'),
('TM0006', 2, '2026-05-02 12:46:00', 'Setoran rutin limbah mingguan 6'),
('TM0007', 2, '2026-01-14 15:56:00', 'Setoran rutin limbah mingguan 7'),
('TM0008', 2, '2026-06-04 10:57:00', 'Setoran rutin limbah mingguan 8'),
('TM0009', 2, '2026-01-04 16:00:00', 'Setoran rutin limbah mingguan 9'),
('TM0010', 2, '2026-06-28 14:28:00', 'Setoran rutin limbah mingguan 10'),
('TM0011', 2, '2026-06-17 16:40:00', 'Setoran rutin limbah mingguan 11'),
('TM0012', 2, '2026-02-27 15:42:00', 'Setoran rutin limbah mingguan 12'),
('TM0013', 2, '2026-03-15 11:10:00', 'Setoran rutin limbah mingguan 13'),
('TM0014', 2, '2026-07-19 12:04:00', 'Setoran rutin limbah mingguan 14'),
('TM0015', 2, '2026-10-11 14:35:00', 'Setoran rutin limbah mingguan 15'),
('TM0016', 2, '2026-09-14 09:22:00', 'Setoran rutin limbah mingguan 16'),
('TM0017', 2, '2026-11-25 09:32:00', 'Setoran rutin limbah mingguan 17'),
('TM0018', 2, '2026-12-11 13:39:00', 'Setoran rutin limbah mingguan 18'),
('TM0019', 2, '2026-01-19 10:23:00', 'Setoran rutin limbah mingguan 19'),
('TM0020', 2, '2026-12-13 13:25:00', 'Setoran rutin limbah mingguan 20'),
('TM0021', 2, '2026-11-03 09:25:00', 'Setoran rutin limbah mingguan 21'),
('TM0022', 2, '2026-08-16 16:24:00', 'Setoran rutin limbah mingguan 22'),
('TM0023', 2, '2026-09-09 12:05:00', 'Setoran rutin limbah mingguan 23'),
('TM0024', 2, '2026-01-01 09:57:00', 'Setoran rutin limbah mingguan 24'),
('TM0025', 2, '2026-02-12 08:51:00', 'Setoran rutin limbah mingguan 25'),
('TM0026', 2, '2026-05-15 12:32:00', 'Setoran rutin limbah mingguan 26'),
('TM0027', 2, '2026-04-14 11:01:00', 'Setoran rutin limbah mingguan 27'),
('TM0028', 2, '2026-09-20 15:15:00', 'Setoran rutin limbah mingguan 28'),
('TM0029', 2, '2026-08-14 09:17:00', 'Setoran rutin limbah mingguan 29'),
('TM0030', 2, '2026-06-16 12:03:00', 'Setoran rutin limbah mingguan 30'),
('TM0031', 2, '2026-05-10 15:24:00', 'Setoran rutin limbah mingguan 31'),
('TM0032', 2, '2026-11-08 15:32:00', 'Setoran rutin limbah mingguan 32'),
('TM0033', 2, '2026-04-01 16:41:00', 'Setoran rutin limbah mingguan 33'),
('TM0034', 2, '2026-07-23 15:27:00', 'Setoran rutin limbah mingguan 34'),
('TM0035', 2, '2026-06-18 11:18:00', 'Setoran rutin limbah mingguan 35'),
('TM0036', 2, '2026-12-18 15:05:00', 'Setoran rutin limbah mingguan 36'),
('TM0037', 2, '2026-08-01 11:59:00', 'Setoran rutin limbah mingguan 37'),
('TM0038', 2, '2026-07-23 12:53:00', 'Setoran rutin limbah mingguan 38'),
('TM0039', 2, '2026-12-13 13:33:00', 'Setoran rutin limbah mingguan 39'),
('TM0040', 2, '2026-06-24 16:11:00', 'Setoran rutin limbah mingguan 40'),
('TM0041', 2, '2026-02-20 10:31:00', 'Setoran rutin limbah mingguan 41'),
('TM0042', 2, '2026-05-10 12:40:00', 'Setoran rutin limbah mingguan 42'),
('TM0043', 2, '2026-07-02 10:55:00', 'Setoran rutin limbah mingguan 43'),
('TM0044', 2, '2026-08-11 10:21:00', 'Setoran rutin limbah mingguan 44'),
('TM0045', 2, '2026-10-24 15:30:00', 'Setoran rutin limbah mingguan 45'),
('TM0046', 2, '2026-08-26 14:31:00', 'Setoran rutin limbah mingguan 46'),
('TM0047', 2, '2026-03-20 13:45:00', 'Setoran rutin limbah mingguan 47'),
('TM0048', 2, '2026-01-03 14:04:00', 'Setoran rutin limbah mingguan 48'),
('TM0049', 2, '2026-11-12 14:24:00', 'Setoran rutin limbah mingguan 49'),
('TM0050', 2, '2026-06-24 12:54:00', 'Setoran rutin limbah mingguan 50'),
('TM0051', 2, '2026-04-07 15:41:00', 'Setoran rutin limbah mingguan 51'),
('TM0052', 2, '2026-12-21 12:52:00', 'Setoran rutin limbah mingguan 52'),
('TM0053', 2, '2026-05-21 08:58:00', 'Setoran rutin limbah mingguan 53'),
('TM0054', 2, '2026-06-11 11:28:00', 'Setoran rutin limbah mingguan 54'),
('TM0055', 2, '2026-01-20 10:52:00', 'Setoran rutin limbah mingguan 55'),
('TM0056', 2, '2026-02-10 12:07:00', 'Setoran rutin limbah mingguan 56'),
('TM0057', 2, '2026-06-27 11:31:00', 'Setoran rutin limbah mingguan 57'),
('TM0058', 2, '2026-08-28 12:23:00', 'Setoran rutin limbah mingguan 58'),
('TM0059', 2, '2026-04-18 10:20:00', 'Setoran rutin limbah mingguan 59'),
('TM0060', 2, '2026-12-19 09:24:00', 'Setoran rutin limbah mingguan 60'),
('TM0061', 2, '2026-01-10 14:32:00', 'Setoran rutin limbah mingguan 61'),
('TM0062', 2, '2026-12-07 15:23:00', 'Setoran rutin limbah mingguan 62'),
('TM0063', 2, '2026-08-05 16:22:00', 'Setoran rutin limbah mingguan 63'),
('TM0064', 2, '2026-03-18 10:24:00', 'Setoran rutin limbah mingguan 64'),
('TM0065', 2, '2026-07-10 08:39:00', 'Setoran rutin limbah mingguan 65'),
('TM0066', 2, '2026-02-26 16:08:00', 'Setoran rutin limbah mingguan 66'),
('TM0067', 2, '2026-03-20 13:01:00', 'Setoran rutin limbah mingguan 67'),
('TM0068', 2, '2026-04-06 14:55:00', 'Setoran rutin limbah mingguan 68'),
('TM0069', 2, '2026-05-19 12:15:00', 'Setoran rutin limbah mingguan 69'),
('TM0070', 2, '2026-01-28 12:44:00', 'Setoran rutin limbah mingguan 70'),
('TM0071', 2, '2026-04-12 11:14:00', 'Setoran rutin limbah mingguan 71'),
('TM0072', 2, '2026-02-14 16:29:00', 'Setoran rutin limbah mingguan 72'),
('TM0073', 2, '2026-02-13 16:31:00', 'Setoran rutin limbah mingguan 73'),
('TM0074', 2, '2026-08-07 10:58:00', 'Setoran rutin limbah mingguan 74'),
('TM0075', 2, '2026-06-05 12:50:00', 'Setoran rutin limbah mingguan 75'),
('TM0076', 2, '2026-07-05 08:07:00', 'Setoran rutin limbah mingguan 76'),
('TM0077', 2, '2026-01-23 13:40:00', 'Setoran rutin limbah mingguan 77'),
('TM0078', 2, '2026-08-17 15:07:00', 'Setoran rutin limbah mingguan 78'),
('TM0079', 2, '2026-02-18 16:36:00', 'Setoran rutin limbah mingguan 79'),
('TM0080', 2, '2026-01-09 13:46:00', 'Setoran rutin limbah mingguan 80'),
('TM0081', 2, '2026-04-25 13:10:00', 'Setoran rutin limbah mingguan 81'),
('TM0082', 2, '2026-10-14 13:00:00', 'Setoran rutin limbah mingguan 82'),
('TM0083', 2, '2026-11-22 15:29:00', 'Setoran rutin limbah mingguan 83'),
('TM0084', 2, '2026-03-25 14:08:00', 'Setoran rutin limbah mingguan 84'),
('TM0085', 2, '2026-05-05 14:31:00', 'Setoran rutin limbah mingguan 85'),
('TM0086', 2, '2026-08-27 13:08:00', 'Setoran rutin limbah mingguan 86'),
('TM0087', 2, '2026-08-06 09:24:00', 'Setoran rutin limbah mingguan 87'),
('TM0088', 2, '2026-09-27 08:48:00', 'Setoran rutin limbah mingguan 88'),
('TM0089', 2, '2026-04-26 13:06:00', 'Setoran rutin limbah mingguan 89'),
('TM0090', 2, '2026-12-27 14:44:00', 'Setoran rutin limbah mingguan 90'),
('TM0091', 2, '2026-11-09 15:27:00', 'Setoran rutin limbah mingguan 91'),
('TM0092', 2, '2026-01-22 15:03:00', 'Setoran rutin limbah mingguan 92'),
('TM0093', 2, '2026-02-24 13:15:00', 'Setoran rutin limbah mingguan 93'),
('TM0094', 2, '2026-02-23 13:58:00', 'Setoran rutin limbah mingguan 94'),
('TM0095', 2, '2026-05-08 15:31:00', 'Setoran rutin limbah mingguan 95'),
('TM0096', 2, '2026-10-15 11:09:00', 'Setoran rutin limbah mingguan 96'),
('TM0097', 2, '2026-09-19 15:49:00', 'Setoran rutin limbah mingguan 97'),
('TM0098', 2, '2026-12-22 15:37:00', 'Setoran rutin limbah mingguan 98'),
('TM0099', 2, '2026-02-01 11:27:00', 'Setoran rutin limbah mingguan 99'),
('TM0100', 2, '2026-10-18 09:15:00', 'Setoran rutin limbah mingguan 100');
GO

-- ==========================================
-- DATA DUMMY DETAIL MASUK
-- ==========================================
INSERT INTO detail_masuk (transaksi_masuk_id, sampah_b3_id, quantity) VALUES
(1, 7, 23.49),
(2, 5, 14.8),
(2, 2, 6.62),
(3, 5, 46.86),
(4, 5, 20.39),
(4, 9, 5.15),
(4, 3, 41.05),
(5, 1, 49.58),
(6, 3, 15.91),
(6, 8, 46.23),
(6, 6, 29.29),
(7, 4, 23.25),
(8, 6, 21.44),
(8, 3, 25.75),
(8, 4, 22.93),
(9, 4, 34.28),
(9, 7, 48.04),
(9, 7, 27.98),
(10, 10, 41.39),
(10, 6, 46.99),
(10, 2, 34.66),
(11, 6, 14.82),
(12, 10, 13.97),
(12, 2, 20.53),
(13, 7, 14.55),
(14, 3, 32.53),
(14, 10, 8.5),
(14, 9, 15.92),
(15, 10, 35.83),
(15, 7, 28.92),
(16, 6, 35.44),
(16, 2, 23.43),
(16, 2, 24.8),
(17, 7, 40.34),
(17, 10, 43.39),
(17, 2, 17.71),
(18, 5, 36.79),
(19, 9, 40.9),
(19, 4, 15.6),
(19, 6, 8.32),
(20, 10, 49.63),
(20, 10, 43.73),
(20, 9, 10.95),
(21, 2, 45.12),
(21, 1, 21.61),
(21, 2, 27.12),
(22, 4, 20.8),
(22, 6, 5.9),
(22, 7, 10.77),
(23, 10, 34.89),
(23, 3, 29.93),
(24, 2, 34.86),
(24, 7, 35.55),
(25, 4, 38.58),
(25, 10, 38.9),
(25, 7, 26.3),
(26, 10, 12),
(27, 8, 31.4),
(28, 5, 35.26),
(28, 5, 47.41),
(28, 5, 18.58),
(29, 3, 45.13),
(30, 9, 29.85),
(31, 6, 11.3),
(31, 9, 19.2),
(31, 8, 9.38),
(32, 9, 33.81),
(33, 2, 39.79),
(33, 8, 17.39),
(33, 1, 26.6),
(34, 5, 10.58),
(35, 4, 34.69),
(35, 5, 12.93),
(35, 1, 12.2),
(36, 9, 26.96),
(37, 5, 49.7),
(38, 8, 25.35),
(38, 4, 18.1),
(39, 6, 24.24),
(39, 2, 39.71),
(40, 10, 5.02),
(40, 7, 20.43),
(40, 10, 16.23),
(41, 1, 9.34),
(41, 8, 24.86),
(42, 1, 32.11),
(42, 5, 27.85),
(43, 2, 13.38),
(44, 2, 20.27),
(45, 4, 48.95),
(45, 8, 39.5),
(45, 3, 7.19),
(46, 6, 17.29),
(46, 3, 13.51),
(47, 8, 41.79),
(48, 10, 24.23),
(48, 10, 42.06),
(48, 2, 17.8),
(49, 4, 36.62),
(49, 1, 17.26),
(49, 4, 23.34),
(50, 4, 11.73),
(51, 1, 13.3),
(51, 5, 25.85),
(52, 4, 20.25),
(53, 8, 48.04),
(53, 2, 24.34),
(53, 6, 9.26),
(54, 8, 30.69),
(55, 8, 19.41),
(56, 1, 34.2),
(57, 10, 16.27),
(58, 3, 23.5),
(59, 8, 6.18),
(59, 7, 31.91),
(60, 6, 7.48),
(60, 6, 19.26),
(61, 10, 50.04),
(61, 9, 21.35),
(62, 2, 14.41),
(62, 8, 7.88),
(63, 4, 48.37),
(64, 3, 9.61),
(64, 3, 38.1),
(64, 7, 48.17),
(65, 3, 14.36),
(65, 10, 41.12),
(65, 2, 34.2),
(66, 10, 30.56),
(66, 5, 35.47),
(67, 7, 6.09),
(68, 2, 23.1),
(68, 1, 5.25),
(68, 8, 27.37),
(69, 8, 11.52),
(70, 1, 45.59),
(70, 2, 22.97),
(71, 6, 6.25),
(71, 9, 29.58),
(72, 10, 5.97),
(72, 6, 8.16),
(73, 6, 28.98),
(73, 9, 11.39),
(74, 1, 20.35),
(74, 7, 38.06),
(74, 5, 47.63),
(75, 3, 24.46),
(75, 6, 31.08),
(76, 5, 39.17),
(76, 2, 42.92),
(76, 3, 9.4),
(77, 6, 33.92),
(77, 4, 30.24),
(78, 10, 24.46),
(78, 9, 5.68),
(79, 6, 15.98),
(79, 9, 19.13),
(79, 6, 38.33),
(80, 3, 23.7),
(81, 4, 49.39),
(82, 3, 29.29),
(82, 6, 26.43),
(82, 6, 27.25),
(83, 9, 50.11),
(84, 4, 41.55),
(84, 4, 5.65),
(85, 10, 29.09),
(85, 9, 13.27),
(86, 10, 47.02),
(86, 2, 9.25),
(86, 3, 31.73),
(87, 9, 38.25),
(87, 4, 8.77),
(88, 10, 43.9),
(88, 4, 12.8),
(89, 4, 24.32),
(89, 10, 34.79),
(89, 1, 11.96),
(90, 2, 30.64),
(90, 10, 25.28),
(91, 3, 32.62),
(92, 4, 45.32),
(93, 7, 10.77),
(94, 6, 26),
(95, 1, 9.37),
(95, 6, 44.82),
(95, 5, 32.02),
(96, 1, 27.18),
(96, 3, 48.6),
(96, 6, 42.15),
(97, 8, 19.76),
(98, 4, 33.56),
(99, 10, 33.9),
(99, 10, 12.23),
(99, 4, 5.05),
(100, 6, 10.65),
(100, 7, 14.38),
(100, 6, 10.87);
GO

-- ==========================================
-- DATA DUMMY TRANSAKSI KELUAR (100 Data)
-- ==========================================
INSERT INTO transaksi_keluar (no_transaksi, vendor_id, tanggal_keluar, keterangan) VALUES
('TK0001', 1, '2026-08-19 08:19:00', 'Pengangkutan limbah oleh vendor'),
('TK0002', 5, '2026-01-16 14:54:00', 'Pengangkutan limbah oleh vendor'),
('TK0003', 8, '2026-03-25 10:39:00', 'Pengangkutan limbah oleh vendor'),
('TK0004', 1, '2026-06-02 14:56:00', 'Pengangkutan limbah oleh vendor'),
('TK0005', 4, '2026-11-21 09:00:00', 'Pengangkutan limbah oleh vendor'),
('TK0006', 3, '2026-03-16 14:48:00', 'Pengangkutan limbah oleh vendor'),
('TK0007', 9, '2026-01-07 11:43:00', 'Pengangkutan limbah oleh vendor'),
('TK0008', 10, '2026-06-23 15:18:00', 'Pengangkutan limbah oleh vendor'),
('TK0009', 10, '2026-07-03 15:43:00', 'Pengangkutan limbah oleh vendor'),
('TK0010', 1, '2026-09-01 09:34:00', 'Pengangkutan limbah oleh vendor'),
('TK0011', 7, '2026-04-15 11:11:00', 'Pengangkutan limbah oleh vendor'),
('TK0012', 7, '2026-05-21 11:50:00', 'Pengangkutan limbah oleh vendor'),
('TK0013', 4, '2026-10-17 15:10:00', 'Pengangkutan limbah oleh vendor'),
('TK0014', 6, '2026-11-23 16:59:00', 'Pengangkutan limbah oleh vendor'),
('TK0015', 10, '2026-09-12 09:49:00', 'Pengangkutan limbah oleh vendor'),
('TK0016', 2, '2026-04-07 08:03:00', 'Pengangkutan limbah oleh vendor'),
('TK0017', 5, '2026-06-24 11:39:00', 'Pengangkutan limbah oleh vendor'),
('TK0018', 1, '2026-06-15 14:43:00', 'Pengangkutan limbah oleh vendor'),
('TK0019', 2, '2026-11-06 11:49:00', 'Pengangkutan limbah oleh vendor'),
('TK0020', 1, '2026-01-27 10:48:00', 'Pengangkutan limbah oleh vendor'),
('TK0021', 1, '2026-05-19 09:24:00', 'Pengangkutan limbah oleh vendor'),
('TK0022', 8, '2026-11-01 12:04:00', 'Pengangkutan limbah oleh vendor'),
('TK0023', 4, '2026-12-25 11:49:00', 'Pengangkutan limbah oleh vendor'),
('TK0024', 4, '2026-06-01 16:48:00', 'Pengangkutan limbah oleh vendor'),
('TK0025', 7, '2026-03-28 16:50:00', 'Pengangkutan limbah oleh vendor'),
('TK0026', 10, '2026-07-10 10:46:00', 'Pengangkutan limbah oleh vendor'),
('TK0027', 6, '2026-07-14 12:24:00', 'Pengangkutan limbah oleh vendor'),
('TK0028', 9, '2026-02-14 15:00:00', 'Pengangkutan limbah oleh vendor'),
('TK0029', 7, '2026-05-05 11:24:00', 'Pengangkutan limbah oleh vendor'),
('TK0030', 2, '2026-01-03 14:33:00', 'Pengangkutan limbah oleh vendor'),
('TK0031', 10, '2026-11-07 15:45:00', 'Pengangkutan limbah oleh vendor'),
('TK0032', 10, '2026-03-15 14:39:00', 'Pengangkutan limbah oleh vendor'),
('TK0033', 3, '2026-11-04 13:40:00', 'Pengangkutan limbah oleh vendor'),
('TK0034', 5, '2026-04-25 16:31:00', 'Pengangkutan limbah oleh vendor'),
('TK0035', 1, '2026-08-25 08:05:00', 'Pengangkutan limbah oleh vendor'),
('TK0036', 5, '2026-05-20 12:41:00', 'Pengangkutan limbah oleh vendor'),
('TK0037', 3, '2026-04-15 14:05:00', 'Pengangkutan limbah oleh vendor'),
('TK0038', 6, '2026-01-02 08:35:00', 'Pengangkutan limbah oleh vendor'),
('TK0039', 10, '2026-12-02 16:39:00', 'Pengangkutan limbah oleh vendor'),
('TK0040', 1, '2026-12-24 11:54:00', 'Pengangkutan limbah oleh vendor'),
('TK0041', 7, '2026-05-02 14:33:00', 'Pengangkutan limbah oleh vendor'),
('TK0042', 5, '2026-06-18 13:51:00', 'Pengangkutan limbah oleh vendor'),
('TK0043', 9, '2026-07-28 14:26:00', 'Pengangkutan limbah oleh vendor'),
('TK0044', 3, '2026-03-13 16:59:00', 'Pengangkutan limbah oleh vendor'),
('TK0045', 9, '2026-03-10 12:13:00', 'Pengangkutan limbah oleh vendor'),
('TK0046', 5, '2026-09-28 13:02:00', 'Pengangkutan limbah oleh vendor'),
('TK0047', 5, '2026-02-22 08:51:00', 'Pengangkutan limbah oleh vendor'),
('TK0048', 8, '2026-09-26 09:17:00', 'Pengangkutan limbah oleh vendor'),
('TK0049', 7, '2026-02-06 14:03:00', 'Pengangkutan limbah oleh vendor'),
('TK0050', 10, '2026-08-02 14:43:00', 'Pengangkutan limbah oleh vendor'),
('TK0051', 5, '2026-11-05 16:40:00', 'Pengangkutan limbah oleh vendor'),
('TK0052', 1, '2026-10-20 08:32:00', 'Pengangkutan limbah oleh vendor'),
('TK0053', 1, '2026-02-14 08:29:00', 'Pengangkutan limbah oleh vendor'),
('TK0054', 2, '2026-09-17 16:44:00', 'Pengangkutan limbah oleh vendor'),
('TK0055', 10, '2026-04-07 09:32:00', 'Pengangkutan limbah oleh vendor'),
('TK0056', 8, '2026-06-16 13:20:00', 'Pengangkutan limbah oleh vendor'),
('TK0057', 8, '2026-09-26 09:28:00', 'Pengangkutan limbah oleh vendor'),
('TK0058', 1, '2026-01-08 09:30:00', 'Pengangkutan limbah oleh vendor'),
('TK0059', 2, '2026-12-27 09:43:00', 'Pengangkutan limbah oleh vendor'),
('TK0060', 6, '2026-01-27 10:52:00', 'Pengangkutan limbah oleh vendor'),
('TK0061', 3, '2026-03-17 10:25:00', 'Pengangkutan limbah oleh vendor'),
('TK0062', 7, '2026-08-04 13:26:00', 'Pengangkutan limbah oleh vendor'),
('TK0063', 9, '2026-02-26 11:25:00', 'Pengangkutan limbah oleh vendor'),
('TK0064', 8, '2026-05-03 12:39:00', 'Pengangkutan limbah oleh vendor'),
('TK0065', 3, '2026-10-17 14:02:00', 'Pengangkutan limbah oleh vendor'),
('TK0066', 6, '2026-02-23 12:16:00', 'Pengangkutan limbah oleh vendor'),
('TK0067', 6, '2026-11-16 11:24:00', 'Pengangkutan limbah oleh vendor'),
('TK0068', 4, '2026-03-17 09:25:00', 'Pengangkutan limbah oleh vendor'),
('TK0069', 4, '2026-01-28 14:14:00', 'Pengangkutan limbah oleh vendor'),
('TK0070', 5, '2026-10-08 12:10:00', 'Pengangkutan limbah oleh vendor'),
('TK0071', 2, '2026-02-26 14:56:00', 'Pengangkutan limbah oleh vendor'),
('TK0072', 6, '2026-06-16 08:37:00', 'Pengangkutan limbah oleh vendor'),
('TK0073', 5, '2026-07-02 09:38:00', 'Pengangkutan limbah oleh vendor'),
('TK0074', 8, '2026-11-10 16:42:00', 'Pengangkutan limbah oleh vendor'),
('TK0075', 10, '2026-08-04 08:42:00', 'Pengangkutan limbah oleh vendor'),
('TK0076', 2, '2026-08-26 10:24:00', 'Pengangkutan limbah oleh vendor'),
('TK0077', 2, '2026-01-03 15:21:00', 'Pengangkutan limbah oleh vendor'),
('TK0078', 2, '2026-07-22 13:08:00', 'Pengangkutan limbah oleh vendor'),
('TK0079', 8, '2026-03-04 09:31:00', 'Pengangkutan limbah oleh vendor'),
('TK0080', 4, '2026-11-22 13:36:00', 'Pengangkutan limbah oleh vendor'),
('TK0081', 6, '2026-12-04 16:29:00', 'Pengangkutan limbah oleh vendor'),
('TK0082', 1, '2026-06-08 15:45:00', 'Pengangkutan limbah oleh vendor'),
('TK0083', 6, '2026-01-23 13:57:00', 'Pengangkutan limbah oleh vendor'),
('TK0084', 10, '2026-02-08 09:15:00', 'Pengangkutan limbah oleh vendor'),
('TK0085', 9, '2026-11-10 12:26:00', 'Pengangkutan limbah oleh vendor'),
('TK0086', 8, '2026-09-28 11:36:00', 'Pengangkutan limbah oleh vendor'),
('TK0087', 8, '2026-01-22 08:11:00', 'Pengangkutan limbah oleh vendor'),
('TK0088', 2, '2026-01-16 14:18:00', 'Pengangkutan limbah oleh vendor'),
('TK0089', 9, '2026-08-21 13:44:00', 'Pengangkutan limbah oleh vendor'),
('TK0090', 1, '2026-01-11 13:29:00', 'Pengangkutan limbah oleh vendor'),
('TK0091', 3, '2026-12-14 09:01:00', 'Pengangkutan limbah oleh vendor'),
('TK0092', 4, '2026-08-12 11:41:00', 'Pengangkutan limbah oleh vendor'),
('TK0093', 10, '2026-06-09 10:26:00', 'Pengangkutan limbah oleh vendor'),
('TK0094', 4, '2026-03-04 10:25:00', 'Pengangkutan limbah oleh vendor'),
('TK0095', 3, '2026-05-14 15:44:00', 'Pengangkutan limbah oleh vendor'),
('TK0096', 9, '2026-07-05 08:28:00', 'Pengangkutan limbah oleh vendor'),
('TK0097', 2, '2026-05-15 16:24:00', 'Pengangkutan limbah oleh vendor'),
('TK0098', 6, '2026-06-12 08:06:00', 'Pengangkutan limbah oleh vendor'),
('TK0099', 8, '2026-10-13 08:15:00', 'Pengangkutan limbah oleh vendor'),
('TK0100', 1, '2026-08-13 10:06:00', 'Pengangkutan limbah oleh vendor');
GO

-- ==========================================
-- DATA DUMMY DETAIL KELUAR
-- ==========================================
INSERT INTO detail_keluar (transaksi_keluar_id, sampah_b3_id, quantity) VALUES
(1, 10, 115.5),
(2, 10, 54.67),
(2, 6, 121.78),
(2, 8, 179.45),
(3, 2, 139.18),
(3, 7, 135.88),
(4, 7, 171.53),
(5, 1, 81.44),
(5, 3, 158.44),
(6, 4, 109.95),
(6, 7, 63.87),
(7, 3, 77.56),
(7, 1, 88.28),
(8, 4, 178.85),
(8, 2, 73.31),
(9, 3, 85.99),
(9, 2, 192.85),
(10, 7, 143.47),
(11, 6, 79.55),
(12, 8, 78.09),
(13, 8, 72.9),
(13, 5, 194.53),
(14, 10, 132.99),
(14, 6, 153.06),
(15, 6, 99.97),
(15, 7, 125.85),
(16, 5, 117.53),
(17, 1, 103.17),
(18, 5, 110.16),
(18, 4, 87.19),
(19, 6, 169.53),
(19, 6, 162.38),
(20, 8, 156.72),
(20, 1, 198.91),
(21, 2, 134.91),
(21, 2, 134.87),
(21, 3, 138.17),
(22, 1, 87.49),
(22, 6, 89.02),
(23, 7, 92.44),
(23, 10, 193.05),
(23, 10, 89.09),
(24, 2, 63.04),
(24, 8, 72.9),
(25, 3, 82.67),
(25, 4, 191.36),
(26, 9, 107.68),
(27, 8, 138.57),
(28, 6, 98.43),
(28, 2, 197.73),
(28, 1, 78.19),
(29, 8, 64.35),
(29, 10, 180.86),
(29, 9, 179.85),
(30, 2, 60.62),
(31, 2, 191.52),
(31, 9, 108.08),
(32, 6, 82.64),
(33, 8, 150.45),
(33, 6, 131.06),
(33, 6, 128.31),
(34, 10, 58.04),
(35, 7, 115.96),
(35, 6, 176.45),
(36, 5, 140.33),
(37, 1, 84.87),
(37, 1, 65.8),
(38, 3, 164.53),
(38, 5, 65.25),
(39, 5, 195.53),
(39, 2, 85.89),
(40, 4, 65.87),
(40, 7, 169.75),
(40, 2, 140.55),
(41, 7, 183.81),
(42, 9, 90.55),
(42, 5, 142.86),
(43, 1, 87.25),
(43, 5, 138.13),
(44, 2, 72.6),
(45, 7, 89.6),
(46, 10, 125.09),
(46, 1, 191.19),
(46, 9, 121.43),
(47, 3, 74.21),
(47, 8, 173.31),
(47, 2, 67.88),
(48, 2, 120.47),
(49, 8, 124.69),
(49, 5, 89.17),
(50, 6, 89.07),
(51, 6, 163.67),
(51, 8, 101.23),
(52, 9, 55.45),
(52, 2, 103.29),
(52, 4, 165.2),
(53, 9, 117.25),
(53, 6, 130.12),
(54, 9, 141.82),
(55, 6, 132.1),
(55, 3, 125.23),
(55, 8, 55.1),
(56, 8, 183.32),
(56, 6, 119.13),
(57, 1, 150),
(58, 10, 108.78),
(58, 9, 89.37),
(58, 3, 115.89),
(59, 1, 95.12),
(60, 7, 54.28),
(60, 4, 50.74),
(60, 1, 198.06),
(61, 6, 79.24),
(61, 5, 168.76),
(62, 4, 105.3),
(62, 8, 200.21),
(63, 9, 171.08),
(64, 4, 171.69),
(64, 7, 138.53),
(64, 5, 130.42),
(65, 9, 119.69),
(65, 5, 83.58),
(65, 6, 118.46),
(66, 3, 71.6),
(66, 7, 101.57),
(66, 4, 68.19),
(67, 10, 79.55),
(67, 4, 125.12),
(67, 9, 65.03),
(68, 2, 107.83),
(68, 7, 173.18),
(68, 9, 194.84),
(69, 5, 71.82),
(69, 3, 77.63),
(70, 2, 73.51),
(71, 8, 98.18),
(72, 3, 78.96),
(72, 7, 88.11),
(73, 2, 59.12),
(73, 3, 170.04),
(74, 2, 111.2),
(74, 2, 79.84),
(75, 7, 160.63),
(75, 7, 165.04),
(76, 4, 78.01),
(76, 6, 186.88),
(76, 4, 163.64),
(77, 5, 139.08),
(78, 6, 87.34),
(78, 5, 137.23),
(79, 6, 117.68),
(79, 10, 78.58),
(80, 8, 52.11),
(81, 6, 93.74),
(82, 10, 136.24),
(82, 5, 176.06),
(83, 2, 54.64),
(83, 9, 170.83),
(83, 9, 172.13),
(84, 6, 85.16),
(84, 4, 198.05),
(85, 7, 121.57),
(86, 7, 167.02),
(86, 1, 184.87),
(86, 2, 143.23),
(87, 6, 89.55),
(88, 1, 50.64),
(88, 8, 87.33),
(88, 1, 119.03),
(89, 5, 176.22),
(89, 9, 82.51),
(89, 2, 50.16),
(90, 6, 145.03),
(90, 2, 119.01),
(90, 5, 167.81),
(91, 2, 80.88),
(91, 1, 128.58),
(91, 7, 165.29),
(92, 7, 171.47),
(92, 4, 143.77),
(93, 4, 65.82),
(93, 1, 117.06),
(94, 1, 55.39),
(95, 3, 200.28),
(95, 4, 131.13),
(95, 2, 176.11),
(96, 4, 139.57),
(96, 4, 177.9),
(96, 3, 169.08),
(97, 4, 78.91),
(98, 4, 83.64),
(98, 7, 129.96),
(99, 2, 183.74),
(99, 4, 167.16),
(99, 3, 193.69),
(100, 9, 128.78),
(100, 7, 195.83),
(100, 2, 140.62);
GO
