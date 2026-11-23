<?php
require_once "../../auth.php";
require_login();
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

$idrole = (int) $_SESSION['idrole'];

// ambil filter status
$status = $_GET['status'] ?? 'Aktif';

if ($status == 'Aktif') {
    $query = "SELECT * FROM viewbarang_aktif";
} else {
    $query = "SELECT * FROM viewbarang";
}

$query .= " ORDER BY ID ASC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Barang</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <h1>Data Barang Toko Kocak Gaming</h1>
    <?php if ($idrole == 1) : ?>
        <a href="tambah_barang.php" class="button">
            + Tambah Barang
        </a>
        <br><br>
    <?php endif; ?>

    <!-- filter status -->
    <form method="GET" style="margin: 15px 0;">

        <label>Filter Status:</label>

        <select name="status" onchange="this.form.submit()">

            <option value="Semua"
                <?= $status == 'Semua' ? 'selected' : ''; ?>>
                Semua Status
            </option>

            <option value="Aktif"
                <?= $status == 'Aktif' ? 'selected' : ''; ?>>
                Aktif
            </option>
        </select>

    </form>

    <table>
        <tr>
            <?php
            $field = mysqli_fetch_fields($result);

            foreach ($field as $kolom) {
                echo "<th>" . $kolom->name . "</th>";
            }

            if ($idrole == 1) {
                echo "<th>Aksi</th>";
            }
            ?>
        </tr>

        <?php
        while ($data = mysqli_fetch_assoc($result)) {
            echo "<tr>";

            foreach ($data as $kolom => $nilai) {

                // Format harga menjadi Rupiah
                if ($kolom == 'HARGA') {
                    $nilai = 'Rp ' . number_format($nilai, 0, ',', '.');
                }

                echo "<td>" . htmlspecialchars((string) $nilai) . "</td>";
            }

            // Tombol aksi khusus Admin
            if ($idrole == 1) {

                echo "<td>";

                echo "<a href='edit_barang.php?idbarang=" . (int) $data['ID'] . "' class='button'>Edit</a>";

                echo "<a href='hapus_barang.php?idbarang=" . (int) $data['ID'] . "' class='button'
                    onclick=\"return confirm('Apakah Anda yakin ingin mengubah status barang ini menjadi tidak aktif?')\">Hapus</a>";

                echo "</td>";
            }

            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>