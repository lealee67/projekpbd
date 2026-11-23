<?php
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

$query = "SELECT * FROM viewpenjualan ORDER BY ID DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Penjualan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <h1>Data Penjualan Toko Kocak Gaming</h1>

    <a href="tambah_penjualan.php" class="button"> + Tambah Penjualan</a>
    <br><br>

    <table>
        <tr>
            <?php
            $field = mysqli_fetch_fields($result);

            foreach ($field as $kolom) {
                echo "<th>" . htmlspecialchars($kolom->name) . "</th>";
            }
            ?>
            <th>Aksi</th>
        </tr>

        <?php
        while ($data = mysqli_fetch_assoc($result)) {

            echo "<tr>";

            foreach ($data as $kolom=> $nilai) {
            
                if ($kolom == 'ID') {
                    $nilai = 'Penjualan #' . $nilai;
                }

                if ($kolom == 'SUBTOTAL' || $kolom == 'TOTAL') {
                    $nilai = 'Rp' . number_format($nilai, 0, ',', '.');
                }

                echo "<td>" . htmlspecialchars((string) $nilai) . "</td>";
            }

            echo "<td>
                <a href='detail_penjualan.php?id=" . (int) $data['ID'] . "' class='button'>Lihat Detail</a>
            </td>";

            echo "</tr>";
        }
        ?>

    </table>

</body>
</html>
