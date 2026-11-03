<?php
include "../../dbconnect.php";
include "../../navbar.php";

$query = "SELECT * FROM viewbarang
          WHERE status = 'Aktif'
          ORDER BY ID ASC"; 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Barang </title>
    <link rel="stylesheet" href="../../css/style.css">
</head> 

<body>
    <h1>Data Barang Toko Kocak Gaming</h1>
    <a href="tambah_barang.php" class="button">Tambah Barang</a>
    <br><br>

    <table>
        <tr>
            <?php
            $field = mysqli_fetch_fields(mysqli_query($conn, $query));

            foreach ($field as $kolom) {
                echo "<th>" . $kolom->name . "</th>";
            }
            echo "<th>Aksi</th>";
            ?>
        </tr>

        <?php
        $result = mysqli_query($conn, $query);
        while ($data = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($data as $nilai) {
                echo "<td>" . $nilai . "</td>";
            }
            echo"<td>";
            echo "<a href='edit_barang.php?idbarang=" . $data['ID'] . "' class='button'>Edit</a>";
            echo "<a href='hapus_barang.php?idbarang=" . $data['ID'] . "' class='button'    
                onclick=\"return confirm('Apakah Anda yakin ingin menghapus barang ini?')\">Hapus</a>";
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>