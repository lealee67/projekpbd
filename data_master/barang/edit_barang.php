<?php
require_once '../../auth.php';
require_role([1]);
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama = $_POST['nama'];
    $jenis = $_POST['jenis'];
    $idsatuan = $_POST['idsatuan'];
    $harga = $_POST['harga'];
    $idbarang = $_POST['idbarang'];

    $query = "UPDATE barang
              SET nama = '$nama',
                  jenis = '$jenis',
                  idsatuan = '$idsatuan',
                  harga = '$harga'
              WHERE idbarang = '$idbarang'";

    $result = mysqli_query($conn, $query);

    if ($result) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal mengubah data barang: " . mysqli_error($conn);
    }
    } else {

    $idbarang = $_GET['idbarang'];

    $query = "SELECT * FROM viewbarang 
              WHERE ID = '$idbarang'";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);

    $querysatuan = "SELECT idsatuan, nama_satuan 
               FROM satuan
               WHERE status = 1
               ORDER BY nama_satuan ASC";
    $resultsatuan = mysqli_query($conn, $querysatuan);
    $datasatuan = mysqli_fetch_assoc($resultsatuan);
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Barang</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <h1>Edit Barang</h1>
    <div class = "form-container">
    <form action="" method="POST">
        <input type="hidden" name="idbarang" value="<?php echo $data['ID']; ?>">

        <label for="nama">Nama Barang:</label>
        <input type="text" id="nama" name="nama" value="<?php echo $data['NAMA']; ?>" required>

        <label for="jenis">Kategori</label>
        <select id="jenis" name="jenis" required>
            <option value="">Pilih Kategori</option>
            <option value="A" <?php if ($data['Kategori'] == 'Aksesoris') echo 'selected'; ?>>Aksesoris</option>
            <option value="B" <?php if ($data['Kategori'] == 'Boardgame') echo 'selected'; ?>>Boardgame</option>
            <option value="C" <?php if ($data['Kategori'] == 'Card') echo 'selected'; ?>>Card</option>
            <option value="G" <?php if ($data['Kategori'] == 'Game') echo 'selected'; ?>>Game</option>
        </select>

        <label for="idsatuan">Satuan:</label>
        <select id="idsatuan" name="idsatuan" required>

            <?php
            while ($satuan = mysqli_fetch_assoc($resultsatuan)) {
                $selected = ($satuan['idsatuan'] == $data['idsatuan']) ? 'selected' : '';
                echo "<option value='" . $satuan['idsatuan'] . "' $selected>" . $satuan['nama_satuan'] . "</option>";
            }
            ?> 

        </select> 
    
        <label for="harga">Harga:</label>
        <input type="number" id="harga" name="harga" value="<?php echo $data['HARGA']; ?>" required>

        <button type="submit">Simpan Perubahan</button>
    </form>
    </div>
</body>
</html>