-- DDL (Create Database dan tabel)
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

-- Menambahkan kolom jumlah pada tabel detail_pengadaan
ALTER TABLE detail_pengadaan
ADD COLUMN jumlah INT AFTER harga_satuan;

-- Mengupdate atribut tabel 
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