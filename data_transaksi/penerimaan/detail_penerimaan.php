<?php
require_once '../../auth.php';
require_role([1, 2]);
include "../../dbconnect.php";
global $conn;

$idpenerimaan = (int) ($_GET['id'] ?? 0);

$query = mysqli_prepare($conn, "
    SELECT *
    FROM viewdetail_penerimaan
    WHERE `ID PENERIMAAN` = ?
");

mysqli_stmt_bind_param($query, "i", $idpenerimaan);
mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Penerimaan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head> 

<body>
    <?php include "../../navbar.php"; ?>
    <h1>Detail Penerimaan Toko Kocak Gaming</h1>
    <table>
        <tr>
            <?php
            $field = mysqli_fetch_fields($result);

            foreach ($field as $kolom) {
                echo "<th>" . $kolom->name . "</th>";
            }
            ?>
        </tr>

        <?php
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