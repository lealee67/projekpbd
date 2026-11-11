-- view pengadaan
CREATE OR REPLACE VIEW viewpengadaan AS
SELECT p.idpengadaan AS ID, 
		  CASE p.status
          WHEN 'P' THEN 'Diproses'
          WHEN 'S' THEN 'Selesai'
          END AS STATUS,
          p.subtotal_nilai AS SUBTOTAL,
          p.ppn AS PPN,
          p.total_nilai AS TOTAL,
          v.nama_vendor AS VENDOR,
          u.username AS OLEH
FROM pengadaan  p
JOIN vendor v
ON p.vendor_idvendor = v.idvendor
JOIN user u
ON p.user_iduser = u.iduser;

-- view detail pengadaan
CREATE OR REPLACE VIEW viewdetail_pengadaan AS
SELECT dp.iddetail_pengadaan AS 'ID DETAIL', 
	   p.idpengadaan AS 'ID PENGADAAN',
	   b.nama AS BARANG,
	   dp.jumlah AS 'JUMLAH BARANG',
	   dp.harga_satuan AS 'HARGA SATUAN BARANG',
       dp.sub_total AS SUBTOTAL
FROM detail_pengadaan dp
JOIN barang b
ON dp.idbarang = b.idbarang
JOIN pengadaan p
ON dp.idpengadaan = p.idpengadaan;

-- sp pengadaan
DROP PROCEDURE IF EXISTS sp_pengadaan;
DELIMITER //
CREATE PROCEDURE sp_pengadaan(IN p_idpengadaan BIGINT)
BEGIN
DECLARE v_subtotal DECIMAL(12,2);
DECLARE v_total DECIMAL(12,2);

UPDATE detail_pengadaan
SET sub_total = hitung_subtotal(harga_satuan, jumlah)
WHERE idpengadaan = p_idpengadaan;

SELECT COALESCE(sum(sub_total),0)
INTO v_subtotal
FROM detail_pengadaan
WHERE idpengadaan = p_idpengadaan;

SET v_total = hitung_total_ppn(v_subtotal);

UPDATE pengadaan
SET subtotal_nilai = v_subtotal, 
	ppn = 11,
    total_nilai = ROUND(v_total)
WHERE idpengadaan = p_idpengadaan;
END //
DELIMITER ;
