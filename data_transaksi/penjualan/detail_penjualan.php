<?php
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

// ambil id penjualan dari web
$idpenjualan = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// detail penjualan berdasarkan ID
$query = "SELECT * FROM viewdetail_penjualan
          WHERE `ID PENJUALAN` = $idpenjualan
          ORDER BY ID";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Penjualan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <h1>Detail Penjualan Toko Kocak Gaming</h1>

    <h3>Penjualan #<?= $idpenjualan; ?></h3>

    <table>
        <tr>
            <?php
            $field = mysqli_fetch_fields($result);

            foreach ($field as $kolom) {
                echo "<th>" . htmlspecialchars($kolom->name) . "</th>";
            }
            ?>
        </tr>

        <?php
        if (mysqli_num_rows($result) > 0) {

            while ($data = mysqli_fetch_assoc($result)) {

                echo "<tr>";

                foreach ($data as $kolom => $nilai) {

                    // Format harga dan subtotal menjadi Rupiah
                    if ($kolom == 'HARGA SATUAN' || $kolom == 'SUBTOTAL') {
                        $nilai = 'Rp' . number_format($nilai, 0, ',', '.');
                    }

                    echo "<td>" . htmlspecialchars((string) $nilai) . "</td>";
                }

                echo "</tr>";
            }

        } else {
            echo "<tr><td colspan='6'>Detail penjualan tidak ditemukan.</td></tr>";
        }
        ?>
    </table>
    <br>
    
        <a href="index.php" class="button">Kembali</a>

</body>
</html>
