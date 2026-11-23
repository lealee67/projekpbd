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
function hitungData($conn, $tabel, $aktif = false) {
    if($aktif){
        $query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$tabel` WHERE status = 1"
    );
    }else {
        $query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$tabel`"
        );
    }
    if (!$query) {
        return 0;
    }

    $data = mysqli_fetch_assoc($query);
    return (int) $data['total'];
}

// statistik  berdasarkan role
if ($idrole == 1) {

    $judul = "Ringkasan Toko";

    $statistik = [
        ['label' => 'Total Barang', 'nilai' => hitungData($conn, 'barang', true)],
        ['label' => 'Total Vendor', 'nilai' => hitungData($conn, 'vendor', true)],
        ['label' => 'Total User', 'nilai' => hitungData($conn, 'user')],
        ['label' => 'Total Penjualan', 'nilai' => hitungData($conn, 'penjualan')]
    ];


} elseif ($idrole == 2) {

    $judul = "Ringkasan Gudang";

    $statistik = [
        ['label' => 'Total Barang', 'nilai' => hitungData($conn, 'barang', true)],
        ['label' => 'Total Vendor', 'nilai' => hitungData($conn, 'vendor', true)],
        ['label' => 'Total Pengadaan', 'nilai' => hitungData($conn, 'pengadaan')],
        ['label' => 'Total Penerimaan', 'nilai' => hitungData($conn, 'penerimaan')]
    ];


} elseif ($idrole == 3) {

    $judul = "Ringkasan Penjualan";

    $statistik = [
        ['label' => 'Total Barang', 'nilai' => hitungData($conn, 'barang', true)],
        ['label' => 'Total Penjualan', 'nilai' => hitungData($conn, 'penjualan')],
        ['label' => 'Total Retur', 'nilai' => hitungData($conn, 'retur')]
    ];

} else {
    header("Location: login.php");
    exit();
}

// aktivitas terakhir
$aktivitas = [];

// admin dan staff gudang
if ($idrole == 1 || $idrole == 2) {

    // nampilin aktivitas pengadaan
    $query = mysqli_query($conn, "
        SELECT 'Pengadaan' AS jenis,
               p.idpengadaan AS id,
               p.timestamp AS waktu,
               CONCAT('Pengadaan #', p.idpengadaan, ' - Vendor: ', v.nama_vendor) AS deskripsi
        FROM pengadaan p
        JOIN vendor v ON p.vendor_idvendor = v.idvendor
        ORDER BY p.timestamp DESC
        LIMIT 5
    ");

    if ($query) {
        while ($data = mysqli_fetch_assoc($query)) {
            $aktivitas[] = $data;
        }
    }

    // nampilin aktivitas penerimaan
    $query = mysqli_query($conn, "
        SELECT 'Penerimaan' AS jenis,
               p.idpenerimaan AS id,
               p.created_at AS waktu,
               CONCAT('Penerimaan #', p.idpenerimaan, ' - Barang diterima dari Pengadaan #', p.idpengadaan) AS deskripsi
        FROM penerimaan p
        ORDER BY p.created_at DESC
        LIMIT 5
    ");

    if ($query) {
        while ($data = mysqli_fetch_assoc($query)) {
            $aktivitas[] = $data;
        }
    }
}

// admin dan kasir
if ($idrole == 1 || $idrole == 3) {
    // nampilin aktivitas penjualan
    $query = mysqli_query($conn, "
        SELECT 'Penjualan' AS jenis,
               j.idpenjualan AS id,
               j.created_at AS waktu,
               CONCAT('Penjualan #', j.idpenjualan, ' - Total transaksi Rp ', j.total_nilai) AS deskripsi
        FROM penjualan j
        ORDER BY j.created_at DESC
        LIMIT 5
    ");

    if ($query) {
        while ($data = mysqli_fetch_assoc($query)) {
            $aktivitas[] = $data;
        }
    }

    // nampilin aktivitas retur
    $query = mysqli_query($conn, "
        SELECT 'Retur' AS jenis,
               r.idretur AS id,
               r.created_at AS waktu,
               CONCAT('Retur #', r.idretur, ' - Dari Penerimaan #', r.idpenerimaan) AS deskripsi
        FROM retur r
        ORDER BY r.created_at DESC
        LIMIT 5
    ");

    if ($query) {
        while ($data = mysqli_fetch_assoc($query)) {
            $aktivitas[] = $data;
        }
    }
}

// urut aktivitas dari yang terbaru
usort($aktivitas, function ($a, $b) {
    return strtotime($b['waktu']) - strtotime($a['waktu']);
});

// ngambil 5 aktivitas terbaru
$aktivitas = array_slice($aktivitas, 0, 5);
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

    <!-- aktivitas terakhir -->
     <div class="dashboard-section">
        <h2>Aktivitas Terakhir</h2>
        <div class="dashboard-activities">
            <?php if(empty($aktivitas)): ?>

                <p class="activity-empty">
                    Belum ada aktivitas transaksi
                </p>

            <?php else: ?>
                <?php foreach ($aktivitas as $item):?>
                   <a href="detail_transaksi.php?jenis=<?= urlencode($item['jenis']); ?>&id=<?= (int) $item['id']; ?>"
                    class="activity-item">

            <div class="activity-type">
                <?= htmlspecialchars($item['jenis']); ?>
            </div>

            <div class="activity-info">
                <p><?= htmlspecialchars($item['deskripsi']); ?></p>
            </div>

            <time>
                <?= date('d/m/Y H:i', strtotime($item['waktu'])); ?>
            </time>
        </a>

     <?php endforeach; ?>

    <?php endif; ?>

    </div>
</div>
</body>
</html>
