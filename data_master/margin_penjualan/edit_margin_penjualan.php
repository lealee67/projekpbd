<?php
require_once '../../auth.php';
require_role([1]);
include "../../dbconnect.php";
global $conn;

// ambil ID margin
$idmargin_penjualan = (int) ($_GET['idmargin_penjualan'] ?? $_POST['idmargin_penjualan'] ?? 0);

// ambil data margin dari VIEW
$query = "SELECT ID, `PERSENTASE (%)`, STATUS
          FROM viewmargin_penjualan
          WHERE ID = $idmargin_penjualan";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data margin penjualan tidak ditemukan!");
}

// nilai awal form
$persen = $data['PERSENTASE (%)'];
$status = ($data['STATUS'] == "Aktif") ? 1 : 0;
$pesan = "";

// edit margin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $persen = $_POST['persen'];
    $status = $_POST['status'];

    // cek persentase duplikat selain data yang sedang diedit
    $cek = mysqli_prepare(
        $conn,
        "SELECT idmargin_penjualan
         FROM margin_penjualan
         WHERE persen = ?
         AND idmargin_penjualan != ?"
    );

    mysqli_stmt_bind_param($cek, "di", $persen, $idmargin_penjualan);
    mysqli_stmt_execute($cek);

    $hasilcek = mysqli_stmt_get_result($cek);

    if (mysqli_num_rows($hasilcek) > 0) {

        $pesan = "Persentase margin tersebut sudah tersedia!";

    } else {

       // cek aturan margin aktif
        if ($status == 1) {

        // Tidak boleh ada margin aktif lainnya
        $cekaktif = mysqli_query($conn, "
        SELECT COUNT(*) AS total
        FROM margin_penjualan
        WHERE status = 1
        AND idmargin_penjualan != $idmargin_penjualan
        ");

        $dataaktif = mysqli_fetch_assoc($cekaktif);

            if ($dataaktif['total'] > 0) {
                $pesan = "Masih ada margin aktif! Nonaktifkan margin sebelumnya dulu.";
            }

            } else {

        // minimal harus ada satu margin aktif lainnya
        $cekaktif = mysqli_query($conn, "
            SELECT COUNT(*) AS total
            FROM margin_penjualan
            WHERE status = 1
            AND idmargin_penjualan != $idmargin_penjualan
        ");

        $dataaktif = mysqli_fetch_assoc($cekaktif);

        if ($dataaktif['total'] == 0) {
            $pesan = "Minimal harus ada satu margin penjualan yang aktif!";
        }
    }
        // update margin jika tidak ada masalah
        if ($pesan == "") {

            $query_update = "UPDATE margin_penjualan
                             SET persen = ?,
                                 status = ?
                             WHERE idmargin_penjualan = ?";

            $stmt = mysqli_prepare($conn, $query_update);

            mysqli_stmt_bind_param(
                $stmt,
                "dii",
                $persen,
                $status,
                $idmargin_penjualan
            );

            $result_update = mysqli_stmt_execute($stmt);

            if ($result_update) {
                header("Location: index.php");
                exit();
            } else {
                $pesan = "Gagal mengubah margin penjualan: " . mysqli_stmt_error($stmt);
            }
        }
    }
}

include "../../navbar.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Margin Penjualan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <div class="pengadaan-container">

        <h1>Edit Margin Penjualan Toko Kocak Gaming</h1>

        <form action="" method="POST" class="pengadaan-form">

            <input type="hidden" name="idmargin_penjualan"
                   value="<?php echo $idmargin_penjualan; ?>">

            <div class="barang-section">

                <div class="form-field">
                    <label for="persen">Persentase Margin (%)</label>
                    <input
                        type="number"
                        id="persen"
                        name="persen"
                        step="0.01"
                        min="0"
                        value="<?php echo htmlspecialchars($persen); ?>"
                        placeholder="Masukkan persentase margin"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>

                        <option value="1"
                            <?php if ($status == 1) echo 'selected'; ?>>
                            Aktif
                        </option>

                        <option value="0"
                            <?php if ($status == 0) echo 'selected'; ?>>
                            Tidak Aktif
                        </option>

                    </select>
                </div>

                <?php if ($pesan != "") { ?>
                    <p style="color: red;">
                        <?php echo htmlspecialchars($pesan); ?>
                    </p>
                <?php } ?>

            </div>

            <div class="pengadaan-actions">

                <a href="index.php" class="btn-batal">
                    Batal
                </a>

                <button type="submit" class="btn-simpan">
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>
</body>
</html>