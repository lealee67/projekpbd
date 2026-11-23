<?php
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

$status = $_GET['status'] ?? 'Semua Status';

$query = "SELECT * FROM viewpengadaan";

if ($status == 'Diproses') {
    $query .= " WHERE STATUS = 'Diproses'";
} elseif ($status == 'Selesai') {
    $query .= " WHERE STATUS = 'Selesai'";
}

$query .= " ORDER BY ID";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Pengadaan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <h1>Data Pengadaan Toko Kocak Gaming</h1>

    <a href="tambah_pengadaan.php" class="button">
        + Tambah Pengadaan
    </a>

    <br><br>

    <form method="GET">
    <label>Filter Status:</label>

    <select name="status" onchange="this.form.submit()">
        <option value="Semua Status"
            <?= $status == 'Semua Status' ? 'selected' : ''; ?>>
            Semua Status
        </option>

        <option value="Diproses"
            <?= $status == 'Diproses' ? 'selected' : ''; ?>>
            Diproses
        </option>

        <option value="Selesai"
            <?= $status == 'Selesai' ? 'selected' : ''; ?>>
            Selesai
        </option>
    </select>
</form>

<br>

    <table>
        <tr>
            <?php
            $field = mysqli_fetch_fields($result);

            foreach ($field as $kolom) {
                echo "<th>" . $kolom->name . "</th>";
            }
            ?>
            <th>Aksi</th>
        </tr>

        <?php
        while ($data = mysqli_fetch_assoc($result)) {
            echo "<tr>";

            foreach ($data as $nama_kolom => $nilai) {

                  if ($nama_kolom == 'ID') {
                    $nilai = 'Pengadaan #' . $nilai;
                }

                // Format nominal menjadi Rupiah
                if ($nama_kolom == 'SUBTOTAL' || $nama_kolom == 'TOTAL') {
                    $nilai = 'Rp ' . number_format($nilai, 0, ',', '.');
                }

                if ($nama_kolom == 'STATUS') {
                    if ($nilai == 'Selesai') {
                        $nilai = "<span class='status-selesai'>Selesai</span>";
                    } else {
                        $nilai = "<span class='status-diproses'>Diproses</span>";
                    }
                }
                echo "<td>" . $nilai . "</td>";
            }
            echo "<td>
                <a href='detail_pengadaan.php?id=" . (int) $data['ID'] . "' class='button'>
                    Lihat Detail
                </a>
            </td>";

            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>