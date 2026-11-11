-- view penerimaan
CREATE OR REPLACE VIEW viewpenerimaan AS
SELECT pr.idpenerimaan AS ID,
	   p.idpengadaan AS 'ID PENGADAAN',
       u.username AS OLEH,
	   CASE pr.status 
       WHEN 'S' THEN 'Selesai'
       END AS STATUS
FROM penerimaan pr
JOIN pengadaan p
ON pr.idpengadaan = p.idpengadaan
JOIN user u
ON pr.iduser = u.iduser;

-- view detail penerimaan
CREATE OR REPLACE VIEW viewdetail_penerimaan AS
SELECT dpr.iddetail_penerimaan AS ID,
	   pr.idpenerimaan AS 'ID PENERIMAAN',
       b.nama AS BARANG,
	   dpr.jumlah_terima AS 'JUMLAH DITERIMA',
       dpr.harga_satuan_terima AS 'HARGA SATUAN',
       dpr.sub_total_terima AS 'SUBTOTAL PENERIMAAN'
FROM detail_penerimaan dpr
JOIN penerimaan pr
ON dpr.idpenerimaan = pr.idpenerimaan
JOIN barang b
ON dpr.barang_idbarang = b.idbarang;

-- sp penerimaan
DROP PROCEDURE IF EXISTS sp_penerimaan;
DELIMITER //
CREATE PROCEDURE sp_penerimaan(IN p_idpengadaan BIGINT)
BEGIN
    
    UPDATE detail_penerimaan dp
    JOIN penerimaan p
    ON p.idpenerimaan = dp.idpenerimaan
    SET dp.sub_total_terima = hitung_subtotal(dp.harga_satuan_terima, dp.jumlah_terima)
    WHERE p.idpengadaan = p_idpengadaan;
    
    IF EXISTS(
		SELECT 1 FROM detail_pengadaan
		WHERE idpengadaan = p_idpengadaan)
        
    AND NOT EXISTS(
    SELECT 1
    FROM (
		SELECT idbarang, SUM(jumlah) AS jumlah_pesan
		FROM detail_pengadaan
        WHERE idpengadaan = p_idpengadaan
        GROUP BY idbarang) AS pesanan
	LEFT JOIN (
		SELECT dp.barang_idbarang, SUM(dp.jumlah_terima) AS jumlah_diterima
        FROM penerimaan p
        JOIN detail_penerimaan dp
        ON dp.idpenerimaan = p.idpenerimaan
        WHERE p.idpengadaan = p_idpengadaan
        GROUP BY dp.barang_idbarang) AS diterima
	ON diterima.barang_idbarang = pesanan.idbarang
    WHERE COALESCE(diterima.jumlah_diterima, 0) < pesanan.jumlah_pesan)
    
    THEN 
		UPDATE pengadaan
        SET status = 'S'
        WHERE idpengadaan = p_idpengadaan;
    ELSE
        UPDATE pengadaan
        SET status = 'P'
        WHERE idpengadaan = p_idpengadaan;
    END IF;
END //
DELIMITER ;

-- trigger detail penerimaan 
DROP TRIGGER IF EXISTS trigger_detail_penerimaan;
DELIMITER //
CREATE TRIGGER trigger_detail_penerimaan
AFTER INSERT ON detail_penerimaan
FOR EACH ROW 
BEGIN
	DECLARE v_stock INT DEFAULT 0;
    
    SELECT stock
    INTO v_stock
    FROM kartu_stok
    WHERE idbarang = NEW.barang_idbarang
    ORDER BY idkartu_stok DESC
    LIMIT 1;
    
    IF v_stock IS NULL THEN
    SET v_stock = 0;
    END IF;

    INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock, idtransaksi, idbarang)
    VALUES ('P', NEW.jumlah_terima, 0, v_stock + NEW.jumlah_terima, NEW.idpenerimaan, NEW.barang_idbarang);
    
    UPDATE barang
    SET harga = NEW.harga_satuan_terima
    WHERE idbarang = NEW.barang_idbarang;
END //
DELIMITER ;