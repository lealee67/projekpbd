<?php
require_once '../../auth.php';
require_role([1, 2]);
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

// Ambil ID retur dari URL
$idretur = (int) ($_GET['id'] ?? 0);

// Ambil detail berdasarkan ID retur
$query = "
    SELECT *
    FROM viewdetail_retur
    WHERE `ID RETUR` = $idretur
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Retur Barang</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
<div class="retur-container">

    <h1>Detail Retur Barang Toko Kocak Gaming</h1>

    <?php if ($idretur <= 0) { ?>

        <p style="color: red;">
            ID retur tidak valid.
        </p>

    <?php } else { ?>
        <h3>Retur #<?php echo $idretur; ?></h3>

        <table>
            <tr>
                <?php
                $field = mysqli_fetch_fields($result);

                foreach ($field as $kolom) {
                    echo "<th>" . htmlspecialchars($kolom->name) . "</th>";
                }
                ?>
            </tr>

            <?php if (mysqli_num_rows($result) > 0) { ?>

                <?php while ($data = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <?php foreach ($data as $nilai) { ?>
                            <td>
                                <?php echo htmlspecialchars($nilai ?? '-'); ?>
                            </td>
                        <?php } ?>

                    </tr>
                <?php } ?>

            <?php } else { ?>
                <tr>
                    <td colspan="<?php echo count($field); ?>">
                        Tidak ada detail barang untuk transaksi retur ini.
                    </td>
                </tr>
            <?php } ?>

        </table>
    <?php } ?>
    <br>

    <a href="index.php" class="btn-batal">
        Kembali
    </a>

</div>
</body>
</html>