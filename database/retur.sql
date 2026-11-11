-- view retur
CREATE OR REPLACE VIEW viewretur AS
SELECT r.idretur AS ID,
	   pr.idpenerimaan AS 'ID PENERIMAAN',
       u.username AS 'DIPROSES OLEH'
FROM retur r
JOIN penerimaan pr
ON r.idpenerimaan = pr.idpenerimaan
JOIN user u
ON r.iduser = u.iduser;

-- view detail retur
CREATE OR REPLACE VIEW viewdetail_retur AS
SELECT dr.iddetail_retur AS ID,
	   r.idretur AS 'ID RETUR',
	   b.nama AS BARANG,
       dr.jumlah AS JUMLAH,
       dr.alasan AS 'ALASAN RETUR'
FROM detail_retur dr
JOIN retur r 
ON dr.idretur = r.idretur
JOIN detail_penerimaan dpr
ON dr.iddetail_penerimaan = dpr.iddetail_penerimaan
JOIN barang b
ON dpr.barang_idbarang = b.idbarang;

-- trigger detail retur
DROP TRIGGER IF EXISTS trigger_detail_retur;
DELIMITER //
CREATE TRIGGER trigger_detail_retur
AFTER INSERT ON detail_retur
FOR EACH ROW
BEGIN
	DECLARE v_stock INT;
    DECLARE v_idbarang INT;
    SELECT barang_idbarang
    INTO v_idbarang
    FROM detail_penerimaan
    WHERE iddetail_penerimaan = NEW.iddetail_penerimaan;
    
    SELECT stock
    INTO v_stock
    FROM kartu_stok
    WHERE idbarang = v_idbarang
    ORDER BY idkartu_stok DESC
    LIMIT 1;
    
    IF v_stock IS NULL THEN
    SET v_stock = 0;
    END IF;
    
     INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock, idtransaksi, idbarang)
    VALUES ('R', 0, NEW.jumlah, v_stock - NEW.jumlah, NEW.idretur, v_idbarang);
END //
DELIMITER ;