-- view penjualan
CREATE OR REPLACE VIEW viewpenjualan AS
SELECT j.idpenjualan AS ID,
	   j.subtotal_nilai AS SUBTOTAL,
       j.ppn AS PPN,
       j.total_nilai AS TOTAL,
       u.username AS OLEH
FROM penjualan j
JOIN user u
ON j.iduser = u.iduser;

-- view detail penjualan
CREATE OR REPLACE VIEW viewdetail_penjualan AS
SELECT dj.iddetail_penjualan AS ID,
	   j.idpenjualan AS 'ID PENJUALAN',
       b.nama AS BARANG,
	   dj.harga_satuan AS 'HARGA SATUAN',
       dj.jumlah AS JUMLAH,
       dj.subtotal AS SUBTOTAL
FROM detail_penjualan dj
JOIN penjualan j
ON dj.penjualan_idpenjualan = j.idpenjualan
JOIN barang b
ON dj.idbarang = b.idbarang;

-- trigger detail penjualan
DROP TRIGGER IF EXISTS trigger_detail_penjualan;
DELIMITER //
CREATE TRIGGER trigger_detail_penjualan
AFTER INSERT ON detail_penjualan
FOR EACH ROW
BEGIN
	DECLARE v_stock INT;
    
    SELECT stock
    INTO v_stock
    FROM kartu_stok
    WHERE idbarang = NEW.idbarang
    ORDER BY idkartu_stok DESC
    LIMIT 1;
    
    IF v_stock IS NULL THEN
    SET v_stock = 0;
    END IF;
    
    INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stock, idtransaksi, idbarang)
    VALUES ('J', 0, NEW.jumlah, v_stock - NEW.jumlah, NEW.penjualan_idpenjualan, NEW.idbarang);
END //
DELIMITER ;
