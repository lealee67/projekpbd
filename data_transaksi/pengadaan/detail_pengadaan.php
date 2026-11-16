<?php
require_once '../../auth.php';
require_role([1, 2]);
include "../../dbconnect.php";
global $conn;

$idpengadaan = (int) ($_GET['id'] ?? 0);

$query = mysqli_prepare($conn, "
    SELECT *
    FROM viewdetail_pengadaan
    WHERE `ID PENGADAAN` = ?
");

mysqli_stmt_bind_param($query, "i", $idpengadaan);
mysqli_stmt_execute($query);

$result = mysqli_stmt_get_result($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Pengadaan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head> 

<body>
    <?php include "../../navbar.php"; ?>

    <h1>Detail Pengadaan Toko Kocak Gaming</h1>
    <a href="index.php">Kembali</a>

    <table>
        <tr>
            <?php
            $field = mysqli_fetch_fields($result);

            foreach ($field as $kolom) {
                echo "<th>" . $kolom->name . "</th>";
            }
            ?>
        </tr>

        <?php if (mysqli_num_rows($result) > 0): ?>

            <?php while ($data = mysqli_fetch_assoc($result)): ?>

                <tr>
                    <?php foreach ($data as $nilai): ?>
                        <td><?= htmlspecialchars((string) $nilai); ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endwhile; ?>

        <?php else: ?>
            <tr>
                <td colspan="<?= count($field); ?>">
                    Belum ada detail barang untuk pengadaan ini.
                </td>
            </tr>
        <?php endif; ?>
    </table>

</body>
</html>