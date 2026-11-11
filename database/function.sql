-- function ngitung harga jual
DELIMITER //
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
END //
DELIMITER ;

-- function ngitung subtotal
DELIMITER //
CREATE FUNCTION hitung_subtotal(p_hargasatuan DECIMAL(12, 2), p_jumlah INT)
RETURNS DECIMAL(12,2)
BEGIN
DECLARE v_subtotal DECIMAL(12,2);

SET v_subtotal = p_hargasatuan * p_jumlah;

RETURN v_subtotal;
END //
DELIMITER ;

-- function ngitung harga setelah ppn
DELIMITER //
CREATE FUNCTION hitung_total_ppn(p_subtotal DECIMAL(12,2))
RETURNS DECIMAL(12,2)
BEGIN 
DECLARE v_ppn DOUBLE;
DECLARE v_harga_setelah_ppn DECIMAL(12,2);

SET v_ppn = 11/100;
SET v_harga_setelah_ppn = p_subtotal + (p_subtotal * v_ppn);
RETURN v_harga_setelah_ppn;
END //
DELIMITER ;