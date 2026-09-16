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

-- ==========================================
-- SELESAI
-- ==========================================

SELECT
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'transaksi_keluar';