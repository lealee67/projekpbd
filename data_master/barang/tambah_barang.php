<?php
include "../../dbconnect.php";
?>

<!DOCTYPE html>
<html> 
<head>
    <title>Tambah Barang</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <h1>Tambah Barang Toko Kocak Gaming</h1>
    <div class="form-container">
    <form action="proses_tambah_barang.php" method="POST">
        <label for="nama">Nama Barang:</label>
        <input type="text" id="nama" name="nama" required>

        <label for="jenis">Kategori</label>
        <select id="jenis" name="jenis" required>
            <option value="">Pilih Kategori</option>
            <option value="A">Aksesoris</option>
            <option value="B">Boardgame</option>
            <option value="C">Card</option>
            <option value="G">Game</option>
        </select>

        <label for="idsatuan">Satuan:</label>
        <select id="idsatuan" name="idsatuan" required>
            <option value="">Pilih Satuan</option>

            <?php
            $querysatuan = "SELECT idsatuan, nama_satuan 
            FROM satuan
            WHERE status = 1
            ORDER BY nama_satuan ASC";

            $resultsatuan = mysqli_query($conn, $querysatuan);

            while ($satuan = mysqli_fetch_assoc($resultsatuan)) {
                echo "<option value='" . $satuan['idsatuan'] . "'>" . $satuan['nama_satuan'] . "</option>";
            }
            ?> 

        </select> 
    
        <label for="harga">Harga:</label>
        <input type="number" id="harga" name="harga" required>

        <button type="submit">Tambah Barang</button>
    </form>
</div>
</body>
</html>