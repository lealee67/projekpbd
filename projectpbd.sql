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
SELECT * FROM viewmargin_penjualan;


