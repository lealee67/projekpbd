<?php
require_once "../../auth.php";
require_login();
include "../../dbconnect.php";
include "../../navbar.php";

$idrole = (int) $_SESSION['idrole'];

$query = "SELECT * FROM viewvendor
          WHERE STATUS = 'Aktif'
          ORDER BY ID";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Vendor</title>
    <link rel="stylesheet" href="../../css/style.css">
</head> 

<body>
    <h1>Data Vendor Toko Kocak Gaming</h1>
    <?php if ($idrole == 1 OR 2) : ?>
    <a href="tambah_vendor.php" class="button">Tambah Vendor</a>
    <br></br>
    <?php endif;?>

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
            echo "<td>";

            if ($idrole == 1 OR 2){
            echo "<a href='edit_vendor.php?idvendor=" . $data['ID'] . "'class='button'>Edit</a>";
            echo "<a href='hapus_vendor.php?idvendor=" . $data['ID'] . "' class='button'    
                onclick=\"return confirm('Apakah Anda yakin ingin menghapus vendor ini?')\">Hapus</a>";
            echo "</td>";
            }

            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>