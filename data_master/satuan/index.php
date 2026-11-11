<?php
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

$query = "SELECT * FROM viewsatuanbarang
          WHERE STATUS = 'Aktif'
          ORDER BY ID";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Satuan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head> 

<body>
    <h1>Data Satuan Toko Kocak Gaming</h1>
    <a href="tambah_satuan.php"
    class="button">Tambah Satuan</a>
    <br></br>

    <table>
        <tr>
            <?php
            $field = mysqli_fetch_fields(mysqli_query($conn, $query));

            foreach ($field as $kolom) {
                echo "<th>" . $kolom->name . "</th>";
            }
            echo "<th>Aksi</th>"
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
            echo "<a href='edit_satuan.php?idsatuan=" . $data['ID'] . "'class='button'>Edit</a>";
            echo "<a href='hapus_satuan.php?idsatuan=" . $data['ID'] . "' class='button'    
                onclick=\"return confirm('Apakah Anda yakin ingin menghapus satuan ini?')\">Hapus</a>";
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>