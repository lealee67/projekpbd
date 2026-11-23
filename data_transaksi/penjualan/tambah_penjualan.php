
<?php
require_once '../../auth.php';
require_role([1, 2]);
include "../../dbconnect.php";
global $conn;

// Ambil margin penjualan aktif
$query_margin = mysqli_query($conn, "
    SELECT idmargin_penjualan, persen
    FROM margin_penjualan
    WHERE status = 1
    LIMIT 1
");

$margin = mysqli_fetch_assoc($query_margin);

// Ambil barang aktif, harga jual, dan stok
$query_barang = mysqli_query($conn, "
    SELECT
        b.idbarang,
        b.nama,
        ROUND(hitung_harga_jual(b.idbarang)) AS harga_jual,
        COALESCE(
            (SELECT ks.stock
             FROM kartu_stok ks
             WHERE ks.idbarang = b.idbarang
             ORDER BY ks.idkartu_stok DESC
             LIMIT 1), 0
        ) AS stock
    FROM barang b
    WHERE b.status = 1
");

$barang_list = mysqli_fetch_all($query_barang, MYSQLI_ASSOC);

// Proses simpan penjualan
if (isset($_POST['simpan'])) {

    $barang = $_POST['barang'] ?? [];
    $jumlah = $_POST['jumlah'] ?? [];

    $transaksi = false;

    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        // Validasi margin dan barang
        if (!$margin) {
            throw new Exception("Margin penjualan belum aktif.");
        }

        if (count($barang) == 0 || count($barang) != count($jumlah)) {
            throw new Exception("Pilih minimal satu barang.");
        }

        // Mulai transaksi database
        mysqli_begin_transaction($conn);
        $transaksi = true;

        // Simpan data utama penjualan
        $iduser = (int) $_SESSION['iduser'];
        $idmargin = (int) $margin['idmargin_penjualan'];

        $query = mysqli_prepare($conn, "
            INSERT INTO penjualan
            (subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan)
            VALUES (0, 11, 0, ?, ?)
        ");

        mysqli_stmt_bind_param($query, "ii", $iduser, $idmargin);
        mysqli_stmt_execute($query);

        // Ambil ID penjualan
        $idpenjualan = mysqli_insert_id($conn);

        // Query cek barang aktif, harga, dan stok
       

        // Query simpan detail penjualan
        $query_detail = mysqli_prepare($conn, "
            INSERT INTO detail_penjualan
            (harga_satuan, jumlah, subtotal, penjualan_idpenjualan, idbarang)
            VALUES (?, ?, ?, ?, ?)
        ");

        $subtotal_nilai = 0;
        $barang_terpilih = [];

        // Simpan setiap barang
        for ($i = 0; $i < count($barang); $i++) {

            $idbarang = (int) $barang[$i];
            $jml = (int) $jumlah[$i];

            // Validasi input
            if ($idbarang <= 0 || $jml <= 0) {
                throw new Exception("Barang dan jumlah harus diisi dengan benar.");
            }

            // Cegah barang yang sama dipilih dua kali
            if (in_array($idbarang, $barang_terpilih)) {
                throw new Exception("Barang yang sama hanya boleh dipilih satu kali.");
            }

            $barang_terpilih[] = $idbarang;
            // Cek barang aktif, harga, dan stok sekalian ambil harga jual dan stok barang
            $query_cek = mysqli_query($conn, "
                SELECT
                    ROUND(hitung_harga_jual(b.idbarang)) AS harga,
                    COALESCE(
                        (SELECT ks.stock
                        FROM kartu_stok ks
                        WHERE ks.idbarang = b.idbarang
                        ORDER BY ks.idkartu_stok DESC
                        LIMIT 1), 0
                    ) AS stock
                FROM barang b
                WHERE b.idbarang = $idbarang
                AND b.status = 1
            ");

            $data_cek = mysqli_fetch_assoc($query_cek);
            mysqli_free_result($query_cek);

            if (!$data_cek) {
                throw new Exception("Barang tidak ditemukan atau tidak aktif.");
            }

            $harga = (int) $data_cek['harga'];
            $stock = (int) $data_cek['stock'];

            // Validasi stok
            if ($jml > $stock) {
                throw new Exception("Stok barang tidak mencukupi.");
            }

            // Hitung subtotal menggunakan function database
            $query_hitung = mysqli_query($conn, "
                SELECT hitung_subtotal($harga, $jml) AS subtotal
            ");

            $data_hitung = mysqli_fetch_assoc($query_hitung);
            $subtotal = (int) $data_hitung['subtotal'];

            mysqli_free_result($query_hitung);

            // Simpan detail barang
            mysqli_stmt_bind_param(
                $query_detail,
                "iiiii",
                $harga,
                $jml,
                $subtotal,
                $idpenjualan,
                $idbarang
            );

            mysqli_stmt_execute($query_detail);

            // Hitung subtotal seluruh penjualan
            $subtotal_nilai += $subtotal;
        }

        // Hitung total menggunakan function database
        $query_total = mysqli_query($conn, "
            SELECT ROUND(hitung_total_ppn($subtotal_nilai)) AS total
        ");

        $data_total = mysqli_fetch_assoc($query_total);
        $total_nilai = (int) $data_total['total'];

        mysqli_free_result($query_total);

        // Update subtotal, PPN, dan total penjualan
        $query_update = mysqli_prepare($conn, "
            UPDATE penjualan
            SET subtotal_nilai = ?,
                ppn = 11,
                total_nilai = ?
            WHERE idpenjualan = ?
        ");

        mysqli_stmt_bind_param(
            $query_update,
            "iii",
            $subtotal_nilai,
            $total_nilai,
            $idpenjualan
        );

        mysqli_stmt_execute($query_update);

        // Tutup query
        mysqli_stmt_close($query_detail);
        mysqli_stmt_close($query_update);
        mysqli_stmt_close($query);

        // Simpan transaksi
        mysqli_commit($conn);
        $transaksi = false;

        header("Location: index.php");
        exit();

    } catch (Throwable $e) {

        if ($transaksi == true) {
            mysqli_rollback($conn);
        }

        $pesan_error = "Gagal menyimpan penjualan: " . $e->getMessage();
    }
}

include "../../navbar.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Penjualan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

<div class="pengadaan-container">

    <h1>Tambah Penjualan Barang</h1>

    <?php if (isset($pesan_error)): ?>
        <p style="color: red;">
            <?= htmlspecialchars($pesan_error); ?>
        </p>
    <?php endif; ?>

    <form method="POST" class="pengadaan-form">

        <!-- Informasi Penjualan -->
        <div class="pengadaan-section">

            <h3>Informasi Penjualan</h3>

            <div class="form-field">
                <label>Margin Penjualan</label>

                <input type="text"
                       value="<?= $margin ? $margin['persen'] . '%' : 'Belum tersedia'; ?>"
                       disabled>
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

        <!-- Ringkasan Pembayaran -->
        <div class="pengadaan-section">

            <h3>Ringkasan Pembayaran</h3>

            <div class="form-field">
                <label>Subtotal</label>
                <input type="text" id="subtotal_nilai"
                       value="Rp0" disabled>
            </div>

            <div class="form-field">
                <label>PPN (11%)</label>
                <input type="text" id="nilai_ppn"
                       value="Rp0" disabled>
            </div>

            <div class="form-field">
                <label>Total Pembayaran</label>
                <input type="text" id="total_nilai"
                       value="Rp0" disabled>
            </div>

        </div>

        <!-- Tombol -->
        <div class="pengadaan-actions">

            <a href="index.php" class="btn-batal">
                Batal
            </a>

            <button type="submit" name="simpan" class="btn-simpan">
                Simpan Penjualan
            </button>

        </div>

    </form>

</div>

<!-- Template input barang -->
<template id="template-barang">

    <div class="barang-row">

        <div class="form-field">
            <label>Barang</label>

            <select name="barang[]" class="pilih-barang"
                    onchange="hitungTotal()" required>

                <option value="">-- Pilih Barang --</option>

                <?php foreach ($barang_list as $item): ?>

                    <option
                        value="<?= $item['idbarang']; ?>"
                        data-harga="<?= (float) $item['harga_jual']; ?>"
                        data-stock="<?= $item['stock']; ?>">

                        <?= htmlspecialchars($item['nama']); ?>
                        (Stok: <?= $item['stock']; ?>)

                    </option>

                <?php endforeach; ?>

            </select>
        </div>

        <div class="form-field">
            <label>Jumlah</label>

            <input type="number" name="jumlah[]"
                   class="jumlah-barang"
                   min="1" required
                   oninput="hitungTotal()">
        </div>

        <div class="form-field">
            <label>Harga Jual</label>

            <input type="text" class="harga-barang"
                   value="Rp0" disabled>
        </div>

        <div class="form-field">
            <label>Subtotal</label>

            <input type="text" class="subtotal-barang"
                   value="Rp0" disabled>
        </div>

        <button type="button" class="btn-hapus-barang"
                onclick="this.closest('.barang-row').remove(); hitungTotal();">
            Hapus
        </button>

    </div>

</template>

<script>

// Format Rupiah
function formatRupiah(angka) {
    return 'Rp' + Number(angka).toLocaleString('id-ID');
}

// Tambah barang
function tambahBarang() {

    let template = document.getElementById('template-barang');
    let isi = template.content.cloneNode(true);

    document.getElementById('daftar-barang').appendChild(isi);

}

// Hitung total pembayaran
function hitungTotal() {

    let daftarBarang = document.querySelectorAll('.barang-row');
    let subtotalSemua = 0;

    daftarBarang.forEach(function(baris) {

        let pilihan = baris.querySelector('.pilih-barang');
        let jumlah = baris.querySelector('.jumlah-barang');
        let hargaInput = baris.querySelector('.harga-barang');
        let subtotalInput = baris.querySelector('.subtotal-barang');

        let harga = 0;

        if (pilihan.value != "") {
            let option = pilihan.options[pilihan.selectedIndex];
            harga = Number(option.dataset.harga) || 0;
        }

        let banyak = Number(jumlah.value) || 0;
        let subtotal = harga * banyak;

        hargaInput.value = formatRupiah(harga);
        subtotalInput.value = formatRupiah(subtotal);

        subtotalSemua += subtotal;

    });

    let ppn = Math.round(subtotalSemua * 11 / 100);
    let total = subtotalSemua + ppn;

    document.getElementById('subtotal_nilai').value = formatRupiah(subtotalSemua);
    document.getElementById('nilai_ppn').value = formatRupiah(ppn);
    document.getElementById('total_nilai').value = formatRupiah(total);

}

// Tampilkan satu barang saat halaman dibuka
tambahBarang();

</script>

</body>
</html>
