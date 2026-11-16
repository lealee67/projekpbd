<?php
require_once '../../auth.php';
require_role([1, 2]);
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

// ambil data vendor
$query_vendor = mysqli_query($conn, "
    SELECT idvendor, nama_vendor
    FROM vendor
");

// ambil data barang aktif
$query_barang = mysqli_query($conn, "
    SELECT idbarang, nama, harga
    FROM barang
    WHERE status = 1
");

$vendor_list = mysqli_fetch_all($query_vendor, MYSQLI_ASSOC);
$barang_list = mysqli_fetch_all($query_barang, MYSQLI_ASSOC);

if(isset($_POST['simpan'])){
    $idvendor = (int) $_POST['idvendor'];
    $barang = $_POST['barang'] ?? [];
    $jumlah = $_POST['jumlah'] ?? [];
    $harga_satuan = $_POST['harga_satuan'] ?? [];

    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        // validasi vendor dan barang
        if ($idvendor <= 0 || count($barang) == 0) {
            throw new Exception("Vendor dan barang wajib diisi.");
        }

        if (
            count($barang) != count($jumlah) ||
            count($barang) != count($harga_satuan)
        ) {
            throw new Exception("Data barang tidak lengkap.");
        }

        mysqli_begin_transaction($conn);

        // simpan header pengadaan
        $iduser = (int) $_SESSION['iduser'];
        $query = mysqli_prepare($conn, "
            INSERT INTO pengadaan
            (status, subtotal_nilai, ppn, total_nilai, user_iduser, vendor_idvendor)
            VALUES ('P', 0, 11, 0, ?, ?)
        ");

        mysqli_stmt_bind_param($query, "ii", $iduser, $idvendor);
        mysqli_stmt_execute($query);

        // ambil ID pengadaan yang baru dibuat
        $idpengadaan = mysqli_insert_id($conn);

        // query detail pengadaan
        $query_detail = mysqli_prepare($conn, "
            INSERT INTO detail_pengadaan
            (harga_satuan, jumlah, sub_total, idbarang, idpengadaan)
            VALUES (?, ?, 0, ?, ?)
        ");

        // cek barang aktif
        $cek_barang = mysqli_prepare($conn, "
            SELECT idbarang
            FROM barang
            WHERE idbarang = ? AND status = 1
        ");

        // simpan setiap barang
        for ($i = 0; $i < count($barang); $i++) {

            $idbarang = (int) $barang[$i];
            $jml = (int) $jumlah[$i];
            $harga = (int) $harga_satuan[$i];

            if ($idbarang <= 0 || $jml <= 0 || $harga <= 0) {
                throw new Exception("Data barang, jumlah, dan harga harus diisi dengan benar.");
            }

            // memastikan barang masih aktif
            mysqli_stmt_bind_param($cek_barang, "i", $idbarang);
            mysqli_stmt_execute($cek_barang);
            mysqli_stmt_store_result($cek_barang);

            if (mysqli_stmt_num_rows($cek_barang) == 0) {
                throw new Exception("Barang tidak ditemukan atau sudah tidak aktif.");
            }

            mysqli_stmt_free_result($cek_barang);

            // input detail barang
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

        // subtotal, ppn, dan total menggunakan stored procedure
        $query_sp = mysqli_prepare($conn, "CALL sp_pengadaan(?)");

        mysqli_stmt_bind_param($query_sp, "i", $idpengadaan);
        mysqli_stmt_execute($query_sp);

        mysqli_commit($conn);

        // Kembali ke halaman daftar pengadaan
        header("Location: index.php");
        exit();

    } catch (Throwable $e) {

        mysqli_rollback($conn);

        $pesan_error = "Gagal menyimpan pengadaan: " . $e->getMessage();
    }
}
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

    <form method="POST" class="pengadaan-form">

        <!-- Informasi Vendor -->
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

<!-- template input barang -->
<template id="template-barang">

     <div class="barang-row">

        <div class="form-field">
            <label>Barang</label>
            <select name="barang[]" required>
                <option value="">-- Pilih Barang --</option>

                <?php foreach ($barang_list as $barang): ?>
                    <option value="<?= $barang['idbarang']; ?>">
                        <?= htmlspecialchars($barang['nama']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
             <br></br>

        </div>
        <div class="form-field">
            <label>Jumlah</label>
            <input type="number" name="jumlah[]" min="1" required>
        </div>
        <br></br>

        <div class="form-field">
            <label>Harga Satuan</label>
            <input type="number" name="harga_satuan[]" min="1" required>
        </div>
        <br></br>

        <button type="button" class="btn-hapus-barang"
                onclick="this.parentElement.remove()">
            Hapus
        </button>

    </div>

</template>

<script>
    function tambahBarang() {
        let template = document.getElementById('template-barang');
        let isi = template.content.cloneNode(true);

        document.getElementById('daftar-barang').appendChild(isi);
    }

    // Menampilkan satu input barang saat halaman dibuka
    tambahBarang();
</script>

</body>
</html>