<?php
include "../../dbconnect.php";

$query = "SELECT * FROM viewsatuanbarang";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Satuan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head> 

<body>
    <h1>Data Satuan Toko Kocak Gaming</h1>
    <table>
        <tr>
            <?php
            $field = mysqli_fetch_fields(mysqli_query($conn, $query));

            foreach ($field as $kolom) {
                echo "<th>" . $kolom->name . "</th>";
            }
            ?>
        </tr>

        <?php
        $result = mysqli_query($conn, $query);
        while ($data = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            foreach ($data as $nilai) {
                echo "<td>" . $nilai . "</td>";
            }
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>