-- view kartu stok
CREATE OR REPLACE VIEW viewkartu_stok AS
SELECT ks.idkartu_stok AS ID,
	   ks.idtransaksi AS 'ID TRANSAKSI',
       b.nama AS BARANG,
	   CASE ks.jenis_transaksi 
       WHEN 'P' THEN 'PENERIMAAN'
       WHEN 'J' THEN 'PENJUALAN'
       WHEN 'R' THEN 'RETUR'
       END AS 'JENIS TRANSAKSI',
       ks.masuk AS 'BARANG MASUK',
       ks.keluar AS 'BARANG KELUAR',
       ks.stock AS 'STOCK BARANG'
FROM kartu_stok ks
JOIN barang b
ON ks.idbarang = b.idbarang;