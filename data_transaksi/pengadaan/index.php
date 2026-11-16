<?php
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

$query = "SELECT * FROM viewpengadaan";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Pengadaan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head> 

<body>
    <h1>Data Pengadaan Toko Kocak Gaming</h1>
    <a href="tambah_pengadaan.php" class="button">+ Tambah Pengadaan</a>
    <br></br>
    <table>
        <tr>
            <?php
            $field = mysqli_fetch_fields(mysqli_query($conn, $query));

            foreach ($field as $kolom) {
                echo "<th>" . $kolom->name . "</th>";
            }
            ?>
            <th>Aksi</th>
        </tr>

        <?php
        $result = mysqli_query($conn, $query);
        while ($data = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($data as $nilai) {
                echo "<td>" . $nilai . "</td>";
            }
            echo "<td>
                <a href='detail_pengadaan.php?id=" . (int) $data['ID'] ."'class='button'>Lihat Detail</a>
            </td>

            </tr>";
        }
        ?>
    </table>
</body>
</html>