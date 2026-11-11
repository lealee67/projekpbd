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

    $query = "INSERT INTO barang
              (nama, jenis, status, idsatuan, harga)
              VALUES (?, ?, 1, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param($stmt, "ssii",
        $nama, $jenis, $idsatuan, $harga
    );

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal menambahkan barang: " . mysqli_error($conn);
    }
}
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
        <form action="" method="POST">

            <label for="nama">Nama Barang:</label>
            <input type="text" id="nama" name="nama" required>

            <label for="jenis">Kategori:</label>
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
                    echo "<option value='" . $satuan['idsatuan'] . "'>"
                         . htmlspecialchars($satuan['nama_satuan']) .
                         "</option>";
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