
<?php
require_once '../../auth.php';
require_role([1, 2]);
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

$query = "SELECT * FROM viewretur ORDER BY ID DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Retur Barang</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

<div class="retur-container">

    <h1>Data Retur Barang Toko Kocak Gaming</h1>

    <a href="tambah_retur.php" class="button">
    + Tambah Retur
    </a>
    <br></br>

    <table>

        <tr>
            <?php
            $field = mysqli_fetch_fields($result);

            foreach ($field as $kolom) {
                echo "<th>" . ($kolom->name) . "</th>";
            }
            ?>
            <th>Aksi</th>
        </tr>

        <?php while ($data = mysqli_fetch_assoc($result)) { 
            echo "<tr>";
                foreach ($data as $nama_kolom => $nilai) { 

                    if ($nama_kolom == 'ID') {
                    $nilai = 'Retur #' . $nilai;
                }

                if ($nama_kolom == 'ID PENERIMAAN') {
                    $nilai = 'Penerimaan #' . $nilai;
                }

                    echo "<td>" . htmlspecialchars((string) $nilai) . "</td>";            
                }

                echo "<td>
                    <a href='detail_retur.php?id=" . (int) $data['ID'] . "' class='button'>Lihat Detail
                    </a>
                </td>";
            echo "</tr>";
        } ?>
    </table>
</div>
</body>
</html>