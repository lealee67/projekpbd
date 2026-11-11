
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$idrole = $_SESSION['idrole'] ?? 0;
$username = $_SESSION['username'] ?? 'User';

// Mendeteksi halaman yang sedang dibuka
$currentPage = $_SERVER['PHP_SELF'];

function isActive($path) {
    global $currentPage;

      if ($path === '/index.php') {
        return $currentPage === '/projectpbd/index.php' ? 'active' : '';
    }

    return strpos($currentPage, $path) !== false ? 'active' : '';
}
?>

<nav class="navbar">

    <div class="navbar-menu">

        <!-- Dashboard: semua role -->
        <a href="/projectpbd/index.php"
           class="<?php echo isActive('/index.php'); ?>">
            Dashboard
        </a>

        <?php if ($idrole == 1): ?>

            <!-- MENU ADMIN -->
            <a href="/projectpbd/data_master/barang/index.php"
               class="<?php echo isActive('/data_master/barang/'); ?>">Barang</a>

            <a href="/projectpbd/data_master/satuan/index.php"
               class="<?php echo isActive('/data_master/satuan/'); ?>">Satuan</a>

            <a href="/projectpbd/data_master/role/index.php"
               class="<?php echo isActive('/data_master/role/'); ?>">Role</a>

            <a href="/projectpbd/data_master/user/index.php"
               class="<?php echo isActive('/data_master/user/'); ?>">User</a>

            <a href="/projectpbd/data_master/vendor/index.php"
               class="<?php echo isActive('/data_master/vendor/'); ?>">Vendor</a>

            <a href="/projectpbd/data_master/margin_penjualan/index.php"
               class="<?php echo isActive('/data_master/margin_penjualan/'); ?>">Margin Penjualan</a>

            <div class="transaksi-dropdown">
                <a href="#" class="<?php echo isActive('/data_transaksi/'); ?>">
                    Transaksi ▾
                </a>

                <div class="transaksi-menu">
                    <a href="/projectpbd/data_transaksi/penjualan/index.php"
                       class="<?php echo isActive('/data_transaksi/penjualan/'); ?>">Penjualan</a>

                    <a href="/projectpbd/data_transaksi/pengadaan/index.php"
                       class="<?php echo isActive('/data_transaksi/pengadaan/'); ?>">Pengadaan</a>

                    <a href="/projectpbd/data_transaksi/penerimaan/index.php"
                       class="<?php echo isActive('/data_transaksi/penerimaan/'); ?>">Penerimaan</a>

                    <a href="/projectpbd/data_transaksi/retur/index.php"
                       class="<?php echo isActive('/data_transaksi/retur/'); ?>">Retur</a>

                    <a href="/projectpbd/data_transaksi/kartu_stok.php"
                       class="<?php echo isActive('/data_transaksi/kartu_stok.php'); ?>">Kartu Stok</a>
                </div>
            </div>

        <?php elseif ($idrole == 2): ?>

            <!-- MENU STAFF GUDANG -->
            <a href="/projectpbd/data_master/barang/index.php"
               class="<?php echo isActive('/data_master/barang/'); ?>">Barang</a>

            <a href="/projectpbd/data_master/vendor/index.php"
               class="<?php echo isActive('/data_master/vendor/'); ?>">Vendor</a>

            <div class="transaksi-dropdown">
                <a href="#" class="<?php echo isActive('/data_transaksi/'); ?>">
                    Transaksi ▾
                </a>

                <div class="transaksi-menu">
                    <a href="/projectpbd/data_transaksi/pengadaan/index.php"
                       class="<?php echo isActive('/data_transaksi/pengadaan/'); ?>">Pengadaan</a>

                    <a href="/projectpbd/data_transaksi/penerimaan/index.php"
                       class="<?php echo isActive('/data_transaksi/penerimaan/'); ?>">Penerimaan</a>

                    <a href="/projectpbd/data_transaksi/kartu_stok.php"
                       class="<?php echo isActive('/data_transaksi/kartu_stok.php'); ?>">Kartu Stok</a>
                </div>
            </div>

        <?php elseif ($idrole == 3): ?>

            <!-- MENU KASIR -->
            <a href="/projectpbd/data_master/barang/index.php"
               class="<?php echo isActive('/data_master/barang/'); ?>">Barang</a>

            <div class="transaksi-dropdown">
                <a href="#" class="<?php echo isActive('/data_transaksi/'); ?>">
                    Transaksi ▾
                </a>

                <div class="transaksi-menu">
                    <a href="/projectpbd/data_transaksi/penjualan/index.php"
                       class="<?php echo isActive('/data_transaksi/penjualan/'); ?>">Penjualan</a>

                    <a href="/projectpbd/data_transaksi/retur/index.php"
                       class="<?php echo isActive('/data_transaksi/retur/'); ?>">Retur</a>
                </div>
            </div>

        <?php endif; ?>

    </div>

    <div class="navbar-user">
        <a href="/projectpbd/logout.php" class="logout-link">Logout</a>
    </div>

</nav>
