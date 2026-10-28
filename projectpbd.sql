CREATE DATABASE toko_le;
USE toko_le;

CREATE TABLE satuan(
idsatuan INT PRIMARY KEY,
nama_satuan VARCHAR(45),
status TINYINT
);

CREATE TABLE role(
idrole INT PRIMARY KEY,
nama_role VARCHAR(100)
);

CREATE TABLE vendor(
idvendor INT PRIMARY KEY,
nama_vendor VARCHAR(100),
badan_hukum CHAR(1),
status CHAR(1)
);

CREATE TABLE barang(
idbarang INT PRIMARY KEY,
jenis CHAR(1),
nama VARCHAR(45),
status TINYINT,
harga INT,
idsatuan INT,
CONSTRAINT barang_idsatuan_fk FOREIGN KEY (idsatuan) REFERENCES satuan(idsatuan)
);

CREATE TABLE user(
iduser INT PRIMARY KEY,
username VARCHAR(45),
password VARCHAR(100),
idrole INT,
CONSTRAINT user_idrole_fk FOREIGN KEY (idrole) REFERENCES role(idrole)
);

CREATE TABLE margin_penjualan(
idmargin_penjualan INT PRIMARY KEY,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
persen DOUBLE,
status TINYINT,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
iduser INT,
CONSTRAINT margin_penjualan_iduser_fk FOREIGN KEY (iduser) REFERENCES user(iduser)
);

CREATE TABLE pengadaan(
idpengadaan BIGINT PRIMARY KEY,
timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
status CHAR(1),
subtotal_nilai INT,
ppn INT,
total_nilai INT,
user_iduser INT,
vendor_idvendor INT,
CONSTRAINT pengadaan_user_iduser_fk FOREIGN KEY (user_iduser) REFERENCES user(iduser),
CONSTRAINT pengadaan_vendor_idvendor_fk FOREIGN KEY (vendor_idvendor) REFERENCES vendor(idvendor)
);

CREATE TABLE detail_pengadaan(
iddetail_pengadaan BIGINT PRIMARY KEY,
harga_satuan INT,
sub_total INT,
idbarang INT,
idpengadaan BIGINT,
CONSTRAINT detail_pengadaan_barang_idbarang_fk FOREIGN KEY (idbarang) REFERENCES barang(idbarang),
CONSTRAINT detail_pengadaan_pengadaan_idpengadaan_fk FOREIGN KEY (idpengadaan) REFERENCES pengadaan(idpengadaan)
);

CREATE TABLE penerimaan(
idpenerimaan BIGINT PRIMARY KEY,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
status CHAR(1),
idpengadaan BIGINT,
iduser INT,
CONSTRAINT penerimaan_pengadaan_idpengadaan_fk FOREIGN KEY (idpengadaan) REFERENCES pengadaan(idpengadaan),
CONSTRAINT penerimaan_user_iduser_fk FOREIGN KEY (iduser) REFERENCES user(iduser)
);

CREATE TABLE detail_penerimaan(
iddetail_penerimaan BIGINT PRIMARY KEY,
jumlah_terima INT,
harga_satuan_terima INT,
sub_total_terima INT,
idpenerimaan BIGINT,
barang_idbarang INT,
CONSTRAINT detail_penerimaan_penerimaan_idpenerimaan_fk FOREIGN KEY (idpenerimaan) REFERENCES penerimaan(idpenerimaan),
CONSTRAINT detail_penerimaan_barang_idbarang_fk FOREIGN KEY (barang_idbarang) REFERENCES barang(idbarang)
);

CREATE TABLE penjualan(
idpenjualan INT PRIMARY KEY,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
subtotal_nilai INT,
ppn INT,
total_nilai INT,
iduser INT,
idmargin_penjualan INT,
CONSTRAINT penjualan_user_iduser_fk FOREIGN KEY (iduser) REFERENCES user(iduser),
CONSTRAINT penjualan_margin_penjualan_idmargin_penjualan_fk FOREIGN KEY (idmargin_penjualan) REFERENCES margin_penjualan(idmargin_penjualan)
);

CREATE TABLE detail_penjualan(
iddetail_penjualan BIGINT PRIMARY KEY,
harga_satuan INT,
jumlah INT,
subtotal INT,
penjualan_idpenjualan INT,
idbarang INT,
CONSTRAINT detail_penjualan_penjualan_idpenjualan_fk FOREIGN KEY (penjualan_idpenjualan) REFERENCES penjualan(idpenjualan),
CONSTRAINT detail_penjualan_barang_idbarang_fk FOREIGN KEY (idbarang) REFERENCES barang(idbarang)
);

CREATE TABLE retur(
idretur BIGINT PRIMARY KEY,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
idpenerimaan BIGINT,
iduser INT,
CONSTRAINT retur_penerimaan_idpenerimaan_fk FOREIGN KEY (idpenerimaan) REFERENCES penerimaan(idpenerimaan),
CONSTRAINT retur_user_iduser_fk FOREIGN KEY (iduser) REFERENCES user(iduser)
);

CREATE TABLE detail_retur(
iddetail_retur INT PRIMARY KEY,
jumlah INT,
alasan VARCHAR(200),
idretur BIGINT,
iddetail_penerimaan BIGINT,
CONSTRAINT detail_retur_retur_idretur_fk FOREIGN KEY (idretur) REFERENCES retur(idretur),
CONSTRAINT detail_retur_detail_penerimaan_iddetail_penerimaan_fk FOREIGN KEY (iddetail_penerimaan) REFERENCES detail_penerimaan(iddetail_penerimaan)
);

CREATE TABLE kartu_stok(
idkartu_stok BIGINT PRIMARY KEY,
jenis_transaksi CHAR(1),
masuk INT,
keluar INT,
stock INT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
idtransaksi INT,
idbarang INT,
CONSTRAINT kartu_stok_barang_idbarang_fk FOREIGN KEY (idbarang) REFERENCES barang(idbarang)
);

INSERT INTO satuan(idsatuan, nama_satuan, status)
VALUES ('1', 'pcs', '1'),
	   ('2', 'pack', '1'),
	   ('3', 'box', '1' ),
	   ('4', 'unit','1' );

INSERT INTO barang(idbarang, jenis, nama, status, harga, idsatuan)
VALUES ('101', 'C', 'Pokemon TCG Booster Pack', '1', '35000', '2'),
	   ('102', 'C', 'Aikatsu Card Pack', '1', '50000', '2'),
	   ('103', 'B', 'UNO Express', '1', '25000', '3'),
	   ('104', 'B', 'Monopoly', '1', '150000', '3'),
	   ('105', 'G', 'Nintendo Switch Card Game', '1', '500000', '1'),
	   ('106', 'G', 'Playstation Game Disc', '1', '100000', '1'),
	   ('107', 'A', 'Playstation Controller', '1', '250000', '4'),
	   ('108', 'A', 'Nintendo Switch Controller', '1', '800000', '4');

INSERT INTO vendor(idvendor, nama_vendor, badan_hukum, status)
VALUES ('1', 'PT Game Nusantara', 'P', '1'),
	   ('2', 'CV Brack Station', 'C', '1'),
	   ('3', 'PT Playtime Indonesia', 'P', '1');

INSERT INTO role(idrole, nama_role)
VALUES ('1', 'Admin'),
	   ('2', 'Staff Gudang'),
	   ('3', 'Kasir');

INSERT INTO user(iduser, username, password, idrole)
VALUES ('201', 'admin.datang', 'admin123', '1'),
	   ('202', 'gudang_gawroam', 'gogogudang67', '2'),
       ('203', 'cashieng.mcqueen', 'cachiew67', '3');

INSERT INTO margin_penjualan(idmargin_penjualan, persen, status, iduser)
VALUES ('301', '10', '0', '201'),
	   ('305', '20', '0', '201'),
       ('310', '30', '1', '201'),
       ('315', '40', '0', '201'),
       ('320', '50', '0', '201');

CREATE VIEW viewsatuanbarang 
AS SELECT * FROM satuan;

CREATE VIEW viewrole
AS SELECT * FROM role;

CREATE VIEW viewvendor
AS SELECT * FROM vendor;

CREATE VIEW viewbarang
AS SELECT * FROM barang;

CREATE VIEW viewuser
AS SELECT * FROM user;

CREATE VIEW viewmargin_penjualan
AS SELECT * FROM margin_penjualan;

CREATE OR REPLACE VIEW viewsatuanbarang 
AS SELECT idsatuan AS ID, nama_satuan AS SATUAN,
	 CASE 
		WHEN status = 0 THEN 'Tidak Aktif'
        ELSE 'Aktif'
	 END AS STATUS
FROM satuan;

CREATE OR REPLACE VIEW viewrole
AS SELECT idrole AS ID, nama_role AS ROLE
FROM role;

CREATE OR REPLACE VIEW viewvendor
AS SELECT idvendor AS ID, nama_vendor AS VENDOR,
	 CASE badan_hukum
		WHEN 'P' THEN 'PT'
        WHEN 'C' THEN 'CV'
     END AS 'BADAN HUKUM',
     CASE status
		WHEN 0 THEN 'Tidak Aktif'
        ELSE 'Aktif'
	 END AS STATUS
FROM vendor;

CREATE OR REPLACE VIEW viewbarang
AS SELECT b.idbarang AS ID,
	 CASE b.jenis
		WHEN 'A' THEN 'Aksesoris'
        WHEN 'B' THEN 'Boardgame'
        WHEN 'C' THEN 'Card'
        WHEN 'G' THEN 'Game'
	 END AS Kategori, 
     b.nama AS NAMA, 
     b.harga AS HARGA, 
     s.nama_satuan AS SATUAN,
	 CASE b.status
		WHEN 0 THEN 'Tidak Aktif'
        ELSE 'Aktif'
	 END AS STATUS
FROM barang b, satuan s 
WHERE b.idsatuan = s.idsatuan;

CREATE OR REPLACE VIEW viewuser
AS SELECT u.iduser AS ID, u.username AS USERNAME, r.nama_role AS ROLE
FROM user u, role r
WHERE u.idrole = r.idrole;

CREATE OR REPLACE VIEW viewmargin_penjualan
AS SELECT mp.idmargin_penjualan AS ID, mp.created_at AS 'TANGGAL DIBUAT', mp.persen AS PERSENTASE, 
CASE mp.status
		WHEN 0 THEN 'Tidak Aktif'
        ELSE 'Aktif'
	 END AS STATUS, 
     mp.updated_at AS 'TERAKHIR DIUBAH',
     u.username AS USER
FROM margin_penjualan mp, user u
WHERE mp.iduser = u.iduser;

ALTER TABLE detail_pengadaan
ADD COLUMN jumlah INT AFTER harga_satuan;

DELIMITER $$
CREATE FUNCTION hitung_harga_jual(id_barang INT)
RETURNS DECIMAL(12,2)
BEGIN
	DECLARE v_harga_barang INT;
    DECLARE v_persen_margin DOUBLE;
    DECLARE v_harga_margin DECIMAL(12,2);
	DECLARE v_harga_jual DECIMAL(12,2);

    
    SELECT harga INTO v_harga_barang
    FROM barang
    WHERE idbarang = id_barang;
    
    SELECT persen INTO v_persen_margin
    FROM margin_penjualan
    WHERE status = 1;
    
    SET v_harga_margin = v_harga_barang * v_persen_margin / 100;
    SET v_harga_jual = v_harga_barang + v_harga_margin;
    RETURN v_harga_jual;
END $$

CREATE FUNCTION hitung_subtotal(id_barang INT, jumlah INT)
RETURNS DECIMAL(12,2)
BEGIN
	DECLARE v_harga_jual DECIMAL(12,2);
    DECLARE v_subtotal DECIMAL(12,2);
    
    SET v_harga_jual = hitung_harga_jual(id_barang);
    SET v_subtotal = v_harga_jual * jumlah;
    RETURN v_subtotal;
END $$
    
CREATE FUNCTION hitung_total_ppn(id_barang INT, jumlah INT)
RETURNS DECIMAL(12,2)
BEGIN
	DECLARE v_subtotal DECIMAL(12,2);
    DECLARE v_ppn DOUBLE;
    DECLARE v_total DECIMAL(12,2);
    
    SET v_subtotal = hitung_subtotal(id_barang, jumlah);
    SET v_ppn = 11;
    SET v_total = v_subtotal + (v_subtotal * v_ppn / 100);
    RETURN v_total;
END $$
DELIMITER ;

ALTER TABLE barang
DROP FOREIGN KEY barang_idsatuan_fk;

ALTER TABLE user
DROP FOREIGN KEY user_idrole_fk;

ALTER TABLE margin_penjualan
DROP FOREIGN KEY margin_penjualan_iduser_fk;

ALTER TABLE pengadaan
DROP FOREIGN KEY pengadaan_user_iduser_fk,
DROP FOREIGN KEY pengadaan_vendor_idvendor_fk;

ALTER TABLE detail_pengadaan
DROP FOREIGN KEY detail_pengadaan_barang_idbarang_fk,
DROP FOREIGN KEY detail_pengadaan_pengadaan_idpengadaan_fk;

ALTER TABLE penerimaan
DROP FOREIGN KEY penerimaan_pengadaan_idpengadaan_fk,
DROP FOREIGN KEY penerimaan_user_iduser_fk;

ALTER TABLE detail_penerimaan
DROP FOREIGN KEY detail_penerimaan_penerimaan_idpenerimaan_fk,
DROP FOREIGN KEY detail_penerimaan_barang_idbarang_fk;

ALTER TABLE penjualan
DROP FOREIGN KEY penjualan_user_iduser_fk,
DROP FOREIGN KEY penjualan_margin_penjualan_idmargin_penjualan_fk;

ALTER TABLE detail_penjualan 
DROP FOREIGN KEY detail_penjualan_penjualan_idpenjualan_fk,
DROP FOREIGN KEY detail_penjualan_barang_idbarang_fk;

ALTER TABLE retur
DROP FOREIGN KEY retur_penerimaan_idpenerimaan_fk,
DROP FOREIGN KEY retur_user_iduser_fk;

ALTER TABLE detail_retur
DROP FOREIGN KEY detail_retur_retur_idretur_fk,
DROP FOREIGN KEY detail_retur_detail_penerimaan_iddetail_penerimaan_fk;

ALTER TABLE kartu_stok
DROP FOREIGN KEY kartu_stok_barang_idbarang_fk;

ALTER TABLE satuan
MODIFY idsatuan INT NOT NULL AUTO_INCREMENT,
MODIFY nama_satuan VARCHAR(45) NOT NULL,
MODIFY status TINYINT NOT NULL;

ALTER TABLE role
MODIFY idrole INT NOT NULL AUTO_INCREMENT,
MODIFY nama_role VARCHAR(100) NOT NULL;

ALTER TABLE vendor
MODIFY idvendor INT NOT NULL AUTO_INCREMENT,
MODIFY nama_vendor VARCHAR(100) NOT NULL,
MODIFY badan_hukum CHAR(1) NOT NULL,
MODIFY status CHAR(1) NOT NULL;

ALTER TABLE barang
MODIFY idbarang INT NOT NULL AUTO_INCREMENT,
MODIFY jenis CHAR(1) NOT NULL,
MODIFY nama VARCHAR(45) NOT NULL,
MODIFY status TINYINT NOT NULL,
MODIFY harga INT NOT NULL,
MODIFY idsatuan INT NOT NULL,
ADD CONSTRAINT barang_idsatuan_fk FOREIGN KEY (idsatuan) REFERENCES satuan(idsatuan);

ALTER TABLE user
MODIFY iduser INT NOT NULL AUTO_INCREMENT,
MODIFY username VARCHAR(45) NOT NULL UNIQUE,
MODIFY password VARCHAR(100) NOT NULL,
MODIFY idrole INT NOT NULL,
ADD CONSTRAINT user_idrole_fk FOREIGN KEY (idrole) REFERENCES role(idrole);

ALTER TABLE margin_penjualan
MODIFY idmargin_penjualan INT NOT NULL AUTO_INCREMENT,
MODIFY persen DOUBLE NOT NULL,
MODIFY status TINYINT NOT NULL,
MODIFY iduser INT NOT NULL,
ADD CONSTRAINT margin_penjualan_iduser_fk FOREIGN KEY (iduser) REFERENCES user(iduser);

ALTER TABLE pengadaan
MODIFY idpengadaan BIGINT NOT NULL AUTO_INCREMENT,
MODIFY status CHAR(1) NOT NULL,
MODIFY subtotal_nilai INT NOT NULL,
MODIFY ppn INT NOT NULL,
MODIFY total_nilai INT NOT NULL,
MODIFY user_iduser INT NOT NULL,
MODIFY vendor_idvendor INT NOT NULL,
ADD CONSTRAINT pengadaan_user_iduser_fk FOREIGN KEY (user_iduser) REFERENCES user(iduser),
ADD CONSTRAINT pengadaan_vendor_idvendor_fk FOREIGN KEY (vendor_idvendor) REFERENCES vendor(idvendor);

ALTER TABLE detail_pengadaan
MODIFY iddetail_pengadaan BIGINT NOT NULL AUTO_INCREMENT,
MODIFY harga_satuan INT NOT NULL,
MODIFY jumlah INT NOT NULL,
MODIFY sub_total INT NOT NULL,
MODIFY idbarang INT NOT NULL,
MODIFY idpengadaan BIGINT NOT NULL,
ADD CONSTRAINT detail_pengadaan_barang_idbarang_fk FOREIGN KEY (idbarang) REFERENCES barang(idbarang),
ADD CONSTRAINT detail_pengadaan_pengadaan_idpengadaan_fk FOREIGN KEY (idpengadaan) REFERENCES pengadaan(idpengadaan);

ALTER TABLE penerimaan
MODIFY idpenerimaan BIGINT NOT NULL AUTO_INCREMENT,
MODIFY status CHAR(1) NOT NULL,
MODIFY idpengadaan BIGINT NOT NULL,
MODIFY iduser INT NOT NULL,
ADD CONSTRAINT penerimaan_pengadaan_idpengadaan_fk FOREIGN KEY (idpengadaan) REFERENCES pengadaan(idpengadaan),
ADD CONSTRAINT penerimaan_user_iduser_fk FOREIGN KEY (iduser) REFERENCES user(iduser);

ALTER TABLE detail_penerimaan
MODIFY iddetail_penerimaan BIGINT NOT NULL AUTO_INCREMENT,
MODIFY jumlah_terima INT NOT NULL,
MODIFY harga_satuan_terima INT NOT NULL,
MODIFY sub_total_terima INT NOT NULL,
MODIFY idpenerimaan BIGINT NOT NULL,
MODIFY barang_idbarang INT NOT NULL,
ADD CONSTRAINT detail_penerimaan_penerimaan_idpenerimaan_fk FOREIGN KEY (idpenerimaan) REFERENCES penerimaan(idpenerimaan),
ADD CONSTRAINT detail_penerimaan_barang_idbarang_fk FOREIGN KEY (barang_idbarang) REFERENCES barang(idbarang);

ALTER TABLE penjualan
MODIFY idpenjualan INT NOT NULL AUTO_INCREMENT,
MODIFY subtotal_nilai INT NOT NULL,
MODIFY ppn INT NOT NULL,
MODIFY total_nilai INT NOT NULL,
MODIFY iduser INT NOT NULL,
MODIFY idmargin_penjualan INT NOT NULL,
ADD CONSTRAINT penjualan_user_iduser_fk FOREIGN KEY (iduser) REFERENCES user(iduser),
ADD CONSTRAINT penjualan_margin_penjualan_idmargin_penjualan_fk FOREIGN KEY (idmargin_penjualan) REFERENCES margin_penjualan(idmargin_penjualan);

ALTER TABLE detail_penjualan 
MODIFY iddetail_penjualan BIGINT NOT NULL AUTO_INCREMENT,
MODIFY harga_satuan INT NOT NULL,
MODIFY jumlah INT NOT NULL,
MODIFY subtotal INT NOT NULL,
MODIFY penjualan_idpenjualan INT NOT NULL,
MODIFY idbarang INT NOT NULL,
ADD CONSTRAINT detail_penjualan_penjualan_idpenjualan_fk FOREIGN KEY (penjualan_idpenjualan) REFERENCES penjualan(idpenjualan),
ADD CONSTRAINT detail_penjualan_barang_idbarang_fk FOREIGN KEY (idbarang) REFERENCES barang(idbarang);

ALTER TABLE retur
MODIFY idretur BIGINT NOT NULL AUTO_INCREMENT,
MODIFY idpenerimaan BIGINT NOT NULL,
MODIFY iduser INT NOT NULL,
ADD CONSTRAINT retur_penerimaan_idpenerimaan_fk FOREIGN KEY (idpenerimaan) REFERENCES penerimaan(idpenerimaan),
ADD CONSTRAINT retur_user_iduser_fk FOREIGN KEY (iduser) REFERENCES user(iduser);

ALTER TABLE detail_retur
MODIFY iddetail_retur BIGINT NOT NULL AUTO_INCREMENT,
MODIFY jumlah INT NOT NULL,
MODIFY alasan VARCHAR(200) NOT NULL,
MODIFY idretur BIGINT NOT NULL,
MODIFY iddetail_penerimaan BIGINT NOT NULL,
ADD CONSTRAINT detail_retur_retur_idretur_fk FOREIGN KEY (idretur) REFERENCES retur(idretur),
ADD CONSTRAINT detail_retur_detail_penerimaan_iddetail_penerimaan_fk FOREIGN KEY (iddetail_penerimaan) REFERENCES detail_penerimaan(iddetail_penerimaan);

ALTER TABLE kartu_stok
MODIFY idkartu_stok BIGINT NOT NULL AUTO_INCREMENT,
MODIFY jenis_transaksi CHAR(1) NOT NULL,
MODIFY masuk INT NOT NULL,
MODIFY keluar INT NOT NULL,
MODIFY stock INT NOT NULL,
MODIFY idtransaksi INT NOT NULL,
MODIFY idbarang INT NOT NULL,
ADD CONSTRAINT kartu_stok_barang_idbarang_fk FOREIGN KEY (idbarang) REFERENCES barang(idbarang);

DELIMITER $$
CREATE PROCEDURE sp_simpan_pengadaan(IN m_idpengadaan BIGINT,
									 IN m_status CHAR(1),
                                     IN m_user_iduser INT,
                                     IN m_vendor_idvendor INT,
                                     IN m_idbarang INT,
                                     IN m_harga_satuan INT,
                                     IN m_jumlah INT,
                                     IN m_iddetail_pengadaan BIGINT)
BEGIN
	DECLARE v_subtotal INT;
    DECLARE v_ppn INT;
    DECLARE v_total INT;
    
    SET v_subtotal = m_harga_satuan * m_jumlah;
    SET v_ppn = v_subtotal * 11 / 100;
    SET v_total = v_subtotal + v_ppn;
    
    INSERT INTO pengadaan (idpengadaan, status, subtotal_nilai, ppn, total_nilai, user_iduser, vendor_idvendor)
    VALUES (m_idpengadaan, m_status, v_subtotal, v_ppn, v_total, m_user_iduser, m_vendor_idvendor);
    
    INSERT INTO detail_pengadaan (iddetail_pengadaan, harga_satuan, jumlah, sub_total, idbarang, idpengadaan)
    VALUES (m_iddetail_pengadaan, m_harga_satuan, m_jumlah, v_subtotal, m_idbarang, m_idpengadaan);
    END $$
    DELIMITER ;


DELIMITER $$
CREATE PROCEDURE sp_simpan_penerimaan( IN m_idpenerimaan BIGINT,
									   IN m_status CHAR(1),
                                       IN m_jumlah_terima INT,
                                       IN m_harga_satuan_terima INT,
                                       IN m_idpengadaan BIGINT,
                                       IN m_iduser INT,
                                       IN m_iddetail_penerimaan BIGINT,
                                       IN m_idbarang INT)
BEGIN
	DECLARE v_subtotal_terima INT;
    
    
    SET v_subtotal_terima = m_harga_satuan_terima * m_jumlah_terima;
    
    INSERT INTO penerimaan (idpenerimaan, status, idpengadaan,iduser)
    VALUES (m_idpenerimaan, m_status, m_idpengadaan, m_iduser);

	INSERT INTO detail_penerimaan (iddetail_penerimaan, jumlah_terima, harga_satuan_terima, sub_total_terima, idpenerimaan, barang_idbarang)
	VALUES (m_iddetail_penerimaan, m_jumlah_terima, m_harga_satuan_terima, v_subtotal_terima, m_idpenerimaan, m_idbarang);
END $$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE sp_simpan_penjualan(IN k_idpenjualan INT,
									 IN k_iduser INT,
							 		 IN k_idmargin_penjualan INT,
                                     IN k_iddetail_penjualan BIGINT,
                                     IN k_harga_satuan INT,
                                     IN k_jumlah INT,
                                     IN k_idbarang INT)
BEGIN
	DECLARE v_subtotal INT;
    DECLARE v_ppn INT;
    DECLARE v_total INT;
    
    SET v_subtotal = hitung_subtotal(k_idbarang, k_jumlah);
    SET v_total = hitung_total_ppn(k_idbarang, k_jumlah);
	SET v_ppn = v_total - v_subtotal;
    
    INSERT INTO penjualan (idpenjualan, subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan)
    VALUES (k_idpenjualan, v_subtotal, v_ppn, v_total, k_iduser, k_idmargin_penjualan);
    
    INSERT INTO detail_penjualan (iddetail_penjualan, harga_satuan, jumlah, subtotal, penjualan_idpenjualan, idbarang)
    VALUES (k_iddetail_penjualan, k_harga_Satuan, k_jumlah, v_subtotal, k_idpenjualan, k_idbarang);
END $$
DELIMITER ;