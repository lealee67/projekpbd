<?php
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

$status = $_GET['status'] ?? 'Aktif';

if ($status == 'Aktif') {
    $query = "SELECT * FROM viewmargin_penjualan_aktif";
} else {
    $query = "SELECT * FROM viewmargin_penjualan";
}

$query .= " ORDER BY ID ASC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Margin Penjualan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head> 

<body>
    <h1>Data Margin Penjualan Toko Kocak Gaming</h1>
    <a href="tambah_margin_penjualan.php" class="button">+ Tambah Margin Penjualan</a>
    <br></br>

    <!-- filter status -->
    <form method="GET" style="margin: 15px 0;">

        <label>Filter Status:</label>

        <select name="status" onchange="this.form.submit()">

            <option value="Semua"
                <?= $status == 'Semua' ? 'selected' : ''; ?>>
                Semua Status
            </option>

            <option value="Aktif"
                <?= $status == 'Aktif' ? 'selected' : ''; ?>>
                Aktif
            </option>
        </select>

    </form>
    
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
            echo "<a href='edit_margin_penjualan.php?idmargin_penjualan=" . $data['ID'] . "' class='button'>Edit</a>";
            echo "<a href='hapus_margin_penjualan.php?idmargin_penjualan=" . $data['ID'] . "' class='button'    
                onclick=\"return confirm('Apakah Anda yakin ingin menghapus margin ini?')\">Hapus</a>";
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>