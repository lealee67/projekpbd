<?php
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

$query = "SELECT * FROM viewpenerimaan";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Penerimaan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head> 

<body>
    <h1>Data Penerimaan Toko Kocak Gaming</h1>
    <a href="tambah_penerimaan.php" class="button">+ Tambah Penerimaan</a>
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
            foreach ($data as $nama_kolom => $nilai) {

                if ($nama_kolom == 'ID') {
                    $nilai = 'Penerimaan #' . $nilai;
                }

                if ($nama_kolom == 'ID PENGADAAN') {
                    $nilai = 'Pengadaan #' . $nilai;
                }

                    echo "<td>" . htmlspecialchars((string) $nilai) . "</td>";            
                }
            echo "<td>
                <a href='detail_penerimaan.php?id=" . (int) $data['ID'] ."'class='button'>Lihat Detail</a>
            </td>
            
            </tr>";
        }
        ?>
    </table>
</body>
</html>