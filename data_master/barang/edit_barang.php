<?php
include "../../dbconnect.php";
$idbarang = $_GET['idbarang'];
$query = "SELECT idbarang, nama, jenis, idsatuan, harga 
          FROM barang 
          WHERE idbarang = '$idbarang'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

$querysatuan = "SELECT idsatuan, nama_satuan 
               FROM satuan
               WHERE status = 1
               ORDER BY nama_satuan ASC";
$resultsatuan = mysqli_query($conn, $querysatuan);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Barang</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <form action="proses_edit_barang.php" method="POST">
        <input type="hidden" name="idbarang" value="<?php echo $data['idbarang']; ?>">

        <label for="nama">Nama Barang:</label>
        <input type="text" id="nama" name="nama" value="<?php echo $data['nama']; ?>" required>

        <label for="jenis">Kategori</label>
        <select id="jenis" name="jenis" required>
            <option value="">Pilih Kategori</option>
            <option value="A" <?php if ($data['jenis'] == 'A') echo 'selected'; ?>>Aksesoris</option>
            <option value="B" <?php if ($data['jenis'] == 'B') echo 'selected'; ?>>Boardgame</option>
            <option value="C" <?php if ($data['jenis'] == 'C') echo 'selected'; ?>>Card</option>
            <option value="G" <?php if ($data['jenis'] == 'G') echo 'selected'; ?>>Game</option>
        </select>

        <label for="idsatuan">Satuan:</label>
        <select id="idsatuan" name="idsatuan" required>
            <option value="">Pilih Satuan</option>

            <?php
            while ($satuan = mysqli_fetch_assoc($resultsatuan)) {
                $selected = ($satuan['idsatuan'] == $data['idsatuan']) ? 'selected' : '';
                echo "<option value='" . $satuan['idsatuan'] . "' $selected>" . $satuan['nama_satuan'] . "</option>";
            }
            ?> 

        </select> 
    
        <label for="harga">Harga:</label>
        <input type="number" id="harga" name="harga" value="<?php echo $data['harga']; ?>" required>

        <button type="submit">Simpan Perubahan</button>
<body>