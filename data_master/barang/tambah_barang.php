<?php
require_once '../../auth.php';
require_role([1]);
include "../../dbconnect.php";
global $conn;

// ambil data satuan aktif
$querysatuan = "SELECT idsatuan, nama_satuan
                FROM satuan
                WHERE status = 1
                ORDER BY nama_satuan ASC";

$resultsatuan = mysqli_query($conn, $querysatuan);

include "../../navbar.php";

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

<div class="pengadaan-container">

    <h1>Tambah Barang Toko Kocak Gaming</h1>
    <br></br>

    <?php if (isset($pesan_error)) { ?>
        <p style="color: red; text-align: center;">
            <?php echo htmlspecialchars($pesan_error); ?>
        </p>
    <?php } ?>

    <form method="POST" class="pengadaan-form">

        <!-- Informasi Barang -->
        <div class="barang-section">

            <div class="form-field">
                <label for="nama">Nama Barang</label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    placeholder="Masukkan nama barang"
                    required
                >
            </div>

            <div class="form-field">
                <label for="jenis">Kategori</label>

                <select id="jenis" name="jenis" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="A">Aksesoris</option>
                    <option value="B">Boardgame</option>
                    <option value="C">Card</option>
                    <option value="G">Game</option>
                </select>
            </div>

            <div class="form-field">
                <label for="idsatuan">Satuan</label>

                <select id="idsatuan" name="idsatuan" required>
                    <option value="">-- Pilih Satuan --</option>

                    <?php while ($satuan = mysqli_fetch_assoc($resultsatuan)) { ?>

                        <option value="<?php echo $satuan['idsatuan']; ?>">
                            <?php echo htmlspecialchars($satuan['nama_satuan']); ?>
                        </option>

                    <?php } ?>
                </select>
            </div>

            <div class="form-field">
                <label for="harga">Harga</label>

                <input
                    type="number"
                    id="harga"
                    name="harga"
                    min="0"
                    placeholder="Masukkan harga barang"
                    required
                >
            </div>
        </div>

        <!-- Tombol -->
        <div class="pengadaan-actions">
            <a href="index.php" class="btn-batal">Batal </a>

            <button type="submit" class="btn-simpan">
                Tambah Barang
            </button>
        </div>

    </form>
</div>
</body>
</html>