
<?php
require_once '../../auth.php';
require_role([1, 2]);
include "../../dbconnect.php";
global $conn;

// Ambil data vendor
$query_vendor = mysqli_query($conn, "
    SELECT idvendor, nama_vendor
    FROM vendor
");

$vendor_list = mysqli_fetch_all($query_vendor, MYSQLI_ASSOC);

// Ambil data barang aktif beserta harga modal
$query_barang = mysqli_query($conn, "
    SELECT idbarang, nama, harga
    FROM barang
    WHERE status = 1
");

$barang_list = mysqli_fetch_all($query_barang, MYSQLI_ASSOC);

if (isset($_POST['simpan'])) {

    $idvendor = (int) $_POST['idvendor'];
    $barang = $_POST['barang'] ?? [];
    $jumlah = $_POST['jumlah'] ?? [];

    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        if ($idvendor <= 0 || count($barang) == 0) {
            throw new Exception("Vendor dan barang wajib diisi.");
        }

        if (count($barang) != count($jumlah)) {
            throw new Exception("Data barang tidak lengkap.");
        }

        mysqli_begin_transaction($conn);

        // Simpan header pengadaan
        $iduser = (int) $_SESSION['iduser'];

        $query = mysqli_prepare($conn, "
            INSERT INTO pengadaan
            (status, subtotal_nilai, ppn, total_nilai, user_iduser, vendor_idvendor)
            VALUES ('P', 0, 11, 0, ?, ?)
        ");

        mysqli_stmt_bind_param($query, "ii", $iduser, $idvendor);
        mysqli_stmt_execute($query);

        // Ambil ID pengadaan
        $idpengadaan = mysqli_insert_id($conn);

        // Query untuk mengambil harga modal barang aktif
        $ambil_harga = mysqli_prepare($conn, "
            SELECT harga
            FROM barang
            WHERE idbarang = ? AND status = 1
        ");

        // Query simpan detail pengadaan
        $query_detail = mysqli_prepare($conn, "
            INSERT INTO detail_pengadaan
            (harga_satuan, jumlah, sub_total, idbarang, idpengadaan)
            VALUES (?, ?, 0, ?, ?)
        ");

        // Simpan setiap barang
        for ($i = 0; $i < count($barang); $i++) {

            $idbarang = (int) $barang[$i];
            $jml = (int) $jumlah[$i];

            if ($idbarang <= 0 || $jml <= 0) {
                throw new Exception("Barang dan jumlah harus diisi dengan benar.");
            }

            // Ambil harga modal dari tabel barang
            mysqli_stmt_bind_param($ambil_harga, "i", $idbarang);
            mysqli_stmt_execute($ambil_harga);

            $hasil_harga = mysqli_stmt_get_result($ambil_harga);
            $data_harga = mysqli_fetch_assoc($hasil_harga);

            if (!$data_harga) {
                throw new Exception("Barang tidak ditemukan atau sudah tidak aktif.");
            }

            $harga = (int) $data_harga['harga'];

            // Simpan detail barang
            mysqli_stmt_bind_param(
                $query_detail,
                "iiii",
                $harga,
                $jml,
                $idbarang,
                $idpengadaan
            );

            mysqli_stmt_execute($query_detail);
        }

        // Hitung subtotal, PPN, dan total
        $query_sp = mysqli_prepare($conn, "CALL sp_pengadaan(?)");

        mysqli_stmt_bind_param($query_sp, "i", $idpengadaan);
        mysqli_stmt_execute($query_sp);

        mysqli_commit($conn);

        header("Location: index.php");
        exit();

    } catch (Throwable $e) {

        mysqli_rollback($conn);

        $pesan_error = "Gagal menyimpan pengadaan: " . $e->getMessage();
    }
}

include "../../navbar.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Pengadaan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

<div class="pengadaan-container">

    <h1>Tambah Pengadaan Barang</h1>
    <?php if (isset($pesan_error)): ?>
        <p class="pesan-error">
            <?= htmlspecialchars($pesan_error); ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="pengadaan-form">

        <!-- Informasi Pengadaan -->
        <div class="pengadaan-section">
            <h3>Informasi Pengadaan</h3>

            <div class="form-field">
                <label>Vendor</label>

                <select name="idvendor" required>
                    <option value="">-- Pilih Vendor --</option>

                    <?php foreach ($vendor_list as $vendor): ?>
                        <option value="<?= $vendor['idvendor']; ?>">
                            <?= htmlspecialchars($vendor['nama_vendor']); ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

        </div>

        <!-- Daftar Barang -->
        <div class="pengadaan-section">

            <h3>Daftar Barang</h3>

            <div id="daftar-barang"></div>

            <button type="button" class="btn-tambah-barang"
                    onclick="tambahBarang()">
                + Tambah Barang
            </button>

        </div>

        <!-- Tombol -->
        <div class="pengadaan-actions">

            <a href="index.php" class="btn-batal">
                Batal
            </a>

            <button type="submit" name="simpan" class="btn-simpan">
                Simpan Pengadaan
            </button>

        </div>

    </form>

</div>

<!-- Template Barang -->
<template id="template-barang">

    <div class="barang-section">

        <div class="form-field">
            <label>Barang</label>

            <select name="barang[]" class="pilih-barang" required>
                <option value="">-- Pilih Barang --</option>

                <?php foreach ($barang_list as $barang): ?>
                    <option
                        value="<?= $barang['idbarang']; ?>"
                        data-harga="<?= $barang['harga']; ?>">
                        <?= htmlspecialchars($barang['nama']); ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <div class="form-field">
            <label>Jumlah</label>
            <input type="number" name="jumlah[]" min="1" required>
        </div>

        <div class="form-field">
            <label>Harga Satuan (Modal)</label>
            <input type="number" name="harga_satuan[]" class="harga-satuan" disabled>
        </div>

        <button type="button" class="btn-hapus-barang"
                onclick="this.parentElement.remove()">
            Hapus
        </button>
        <br></br>

    </div>

</template>

<script>
    function tambahBarang() {
        let template = document.getElementById('template-barang');
        let isi = template.content.cloneNode(true);

        document.getElementById('daftar-barang').appendChild(isi);
    }

    // isi harga otomatis saat barang dipilih
    document.getElementById('daftar-barang').addEventListener('change', function(e) {

        if (e.target.classList.contains('pilih-barang')) {

            let pilihan = e.target.options[e.target.selectedIndex];
            let harga = pilihan.getAttribute('data-harga');

            let baris = e.target.closest('.barang-section');
            let inputHarga = baris.querySelector('.harga-satuan');

            inputHarga.value = harga || '';
        }
    });

    // tampilkan baris barang saat halaman dibuka
    tambahBarang();
</script>

</body>
</html>
