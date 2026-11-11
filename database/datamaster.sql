-- insert value ke data master
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

-- view dari data master
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

-- perbaiki view data master
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