<?php
session_start();

if (!isset($_SESSION['iduser'])) {
    header("Location: login.php");
    exit();
}

include "dbconnect.php";

$idrole = (int) $_SESSION['idrole'];
$username = $_SESSION['username'];
$role = $_SESSION['nama_role'] ?? 'Pengguna';

// Fungsi menghitung jumlah data
function hitungData($conn, $tabel) {
    $query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$tabel`");

    if (!$query) {
        return 0;
    }

    $data = mysqli_fetch_assoc($query);
    return (int) $data['total'];
}

// Statistik dan shortcut berdasarkan role
if ($idrole == 1) {

    $judul = "Ringkasan Toko";

    $statistik = [
        ['label' => 'Total Barang', 'nilai' => hitungData($conn, 'barang')],
        ['label' => 'Total Vendor', 'nilai' => hitungData($conn, 'vendor')],
        ['label' => 'Total User', 'nilai' => hitungData($conn, 'user')],
        ['label' => 'Total Penjualan', 'nilai' => hitungData($conn, 'penjualan')]
    ];

    $shortcut = [
        ['nama' => 'Data Barang', 'deskripsi' => 'Kelola informasi barang dan persediaan.', 'link' => 'data_master/barang/index.php'],
        ['nama' => 'Data Satuan', 'deskripsi' => 'Kelola satuan barang.', 'link' => 'data_master/satuan/index.php'],
        ['nama' => 'Data Role', 'deskripsi' => 'Kelola hak akses pengguna.', 'link' => 'data_master/role/index.php'],
        ['nama' => 'Data User', 'deskripsi' => 'Kelola akun pengguna.', 'link' => 'data_master/user/index.php'],
        ['nama' => 'Data Vendor', 'deskripsi' => 'Kelola informasi pemasok.', 'link' => 'data_master/vendor/index.php'],
        ['nama' => 'Margin Penjualan', 'deskripsi' => 'Atur persentase margin penjualan.', 'link' => 'data_master/margin_penjualan/index.php'],
        ['nama' => 'Penjualan', 'deskripsi' => 'Kelola transaksi penjualan.', 'link' => 'data_transaksi/penjualan/index.php'],
        ['nama' => 'Pengadaan', 'deskripsi' => 'Kelola pengadaan barang.', 'link' => 'data_transaksi/pengadaan/index.php'],
        ['nama' => 'Penerimaan', 'deskripsi' => 'Kelola penerimaan barang.', 'link' => 'data_transaksi/penerimaan/index.php'],
        ['nama' => 'Retur', 'deskripsi' => 'Kelola transaksi retur.', 'link' => 'data_transaksi/retur/index.php'],
        ['nama' => 'Kartu Stok', 'deskripsi' => 'Pantau riwayat pergerakan stok.', 'link' => 'data_transaksi/kartu_stok.php']
    ];

} elseif ($idrole == 2) {

    $judul = "Ringkasan Gudang";

    $statistik = [
        ['label' => 'Total Barang', 'nilai' => hitungData($conn, 'barang')],
        ['label' => 'Total Vendor', 'nilai' => hitungData($conn, 'vendor')],
        ['label' => 'Total Pengadaan', 'nilai' => hitungData($conn, 'pengadaan')],
        ['label' => 'Total Penerimaan', 'nilai' => hitungData($conn, 'penerimaan')]
    ];

    $shortcut = [
        ['nama' => 'Data Barang', 'deskripsi' => 'Lihat informasi barang dan persediaan.', 'link' => 'data_master/barang/index.php'],
        ['nama' => 'Data Vendor', 'deskripsi' => 'Lihat informasi pemasok barang.', 'link' => 'data_master/vendor/index.php'],
        ['nama' => 'Pengadaan', 'deskripsi' => 'Kelola pengadaan barang.', 'link' => 'data_transaksi/pengadaan/index.php'],
        ['nama' => 'Penerimaan', 'deskripsi' => 'Kelola penerimaan barang.', 'link' => 'data_transaksi/penerimaan/index.php'],
        ['nama' => 'Kartu Stok', 'deskripsi' => 'Pantau riwayat pergerakan stok.', 'link' => 'data_transaksi/kartu_stok.php']
    ];

} elseif ($idrole == 3) {

    $judul = "Ringkasan Penjualan";

    $statistik = [
        ['label' => 'Total Barang', 'nilai' => hitungData($conn, 'barang')],
        ['label' => 'Total Penjualan', 'nilai' => hitungData($conn, 'penjualan')],
        ['label' => 'Total Retur', 'nilai' => hitungData($conn, 'retur')]
    ];

    $shortcut = [
        ['nama' => 'Data Barang', 'deskripsi' => 'Lihat informasi barang dan harga jual.', 'link' => 'data_master/barang/index.php'],
        ['nama' => 'Penjualan', 'deskripsi' => 'Kelola transaksi penjualan.', 'link' => 'data_transaksi/penjualan/index.php'],
        ['nama' => 'Retur', 'deskripsi' => 'Kelola transaksi retur.', 'link' => 'data_transaksi/retur/index.php']
    ];

} else {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Toko Kocak Gaming</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="dashboard-container">

    <!-- Bagian Sapaan -->
    <div class="dashboard-welcome">
        <h1>Selamat Datang, <?= htmlspecialchars($username); ?>!</h1>  

    </div>

    <!-- Ringkasan -->
    <div class="dashboard-section">
        <h2><?= $judul; ?></h2>

        <div class="dashboard-stats">
            <?php foreach ($statistik as $item): ?>
                <div class="stat-item">
                    <h3><?= number_format($item['nilai'], 0, ',', '.'); ?></h3>
                    <p><?= $item['label']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Shortcut -->
    <div class="dashboard-section">
        <h2>Akses Menu</h2>

        <div class="dashboard-shortcuts">
            <?php foreach ($shortcut as $item): ?>
                <div class="shortcut-item">
                    <div>
                        <h3><?= htmlspecialchars($item['nama']); ?></h3>
                        <p><?= htmlspecialchars($item['deskripsi']); ?></p>
                    </div>

                    <a href="<?= htmlspecialchars($item['link']); ?>">
                        Buka Menu &rarr;
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

</body>
</html>
