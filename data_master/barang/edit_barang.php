<?php
require_once '../../auth.php';
require_role([1]);
include "../../dbconnect.php";
global $conn;

// ambil ID barang
$idbarang = (int) ($_GET['idbarang'] ?? $_POST['idbarang'] ?? 0);

// ambil data barang
$query = "SELECT * FROM viewbarang WHERE ID = $idbarang";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// ambil ID satuan langsung dari tabel barang
$query_id_satuan = "SELECT idsatuan FROM barang WHERE idbarang = $idbarang";
$result_id_satuan = mysqli_query($conn, $query_id_satuan);
$data_id_satuan = mysqli_fetch_assoc($result_id_satuan);

$data['idsatuan'] = $data_id_satuan['idsatuan'];

// ambil data satuan aktif
$querysatuan = "SELECT idsatuan, nama_satuan
                FROM satuan
                WHERE status = 1
                ORDER BY nama_satuan ASC";

$resultsatuan = mysqli_query($conn, $querysatuan);

// update barang
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama = $_POST['nama'];
    $jenis = $_POST['jenis'];
    $idsatuan = $_POST['idsatuan'];
    $harga = $_POST['harga'];

    $query_update = "UPDATE barang
                     SET nama = ?,
                         jenis = ?,
                         idsatuan = ?,
                         harga = ?
                     WHERE idbarang = ?";

    $stmt = mysqli_prepare($conn, $query_update);

    mysqli_stmt_bind_param(
        $stmt,
        "ssiii",
        $nama,
        $jenis,
        $idsatuan,
        $harga,
        $idbarang
    );

    $hasil = mysqli_stmt_execute($stmt);

    if ($hasil) {
        mysqli_stmt_close($stmt);
        header("Location: index.php");
        exit();
    } else {
        $pesan_error = "Gagal mengubah data barang: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}

include "../../navbar.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Barang</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

<div class="pengadaan-container">

    <h1>Edit Barang Toko Kocak Gaming</h1>
    <br></br>

    <?php if (isset($pesan_error)) { ?>
        <p style="color: red; text-align: center;">
            <?php echo htmlspecialchars($pesan_error); ?>
        </p>
    <?php } ?>

    <?php if ($data) { ?>

        <form method="POST" class="pengadaan-form">

            <input type="hidden"
                   name="idbarang"
                   value="<?php echo $idbarang; ?>">

            <div class="barang-section">

                <div class="form-field">
                    <label for="nama">Nama Barang</label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="<?php echo htmlspecialchars($data['NAMA']); ?>"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="jenis">Kategori</label>

                    <select id="jenis" name="jenis" required>
                        <option value="">-- Pilih Kategori --</option>

                        <option value="A"
                            <?php if ($data['Kategori'] == 'Aksesoris') echo 'selected'; ?>>
                            Aksesoris
                        </option>

                        <option value="B"
                            <?php if ($data['Kategori'] == 'Boardgame') echo 'selected'; ?>>
                            Boardgame
                        </option>

                        <option value="C"
                            <?php if ($data['Kategori'] == 'Card') echo 'selected'; ?>>
                            Card
                        </option>

                        <option value="G"
                            <?php if ($data['Kategori'] == 'Game') echo 'selected'; ?>>
                            Game
                        </option>
                    </select>
                </div>

                <div class="form-field">
                    <label for="idsatuan">Satuan</label>

                    <select id="idsatuan" name="idsatuan" required>
                        <option value="">-- Pilih Satuan --</option>

                        <?php while ($satuan = mysqli_fetch_assoc($resultsatuan)) { ?>

                            <option
                                value="<?php echo $satuan['idsatuan']; ?>"
                                <?php if ($satuan['idsatuan'] == $data['idsatuan']) echo 'selected'; ?>
                            >
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
                        value="<?php echo $data['HARGA']; ?>"
                        required
                    >
                </div>

            </div>

            <div class="pengadaan-actions">

                <a href="index.php" class="btn-batal">
                    Batal
                </a>

                <button type="submit" class="btn-simpan">
                    Simpan Perubahan
                </button>

            </div>
        </form>

    <?php } else { ?>

        <p style="color: red; text-align: center;">
            Data barang tidak ditemukan.
        </p>

        <div class="pengadaan-actions">
            <a href="index.php" class="btn-batal">Kembali</a>
        </div>

    <?php } ?>

</div>
</body>
</html>