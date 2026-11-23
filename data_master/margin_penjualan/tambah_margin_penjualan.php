<?php
require_once '../../auth.php';
require_role([1]);
include "../../dbconnect.php";
global $conn;

$iduser = $_SESSION['iduser'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $persen = $_POST['persen'];
    $status = $_POST['status'];

    // cek persentase duplikat
    $cek = mysqli_prepare($conn,
        "SELECT idmargin_penjualan
         FROM margin_penjualan
         WHERE persen = ?"
    );

    mysqli_stmt_bind_param($cek, "d", $persen);
    mysqli_stmt_execute($cek);

    $hasilcek = mysqli_stmt_get_result($cek);

    if (mysqli_num_rows($hasilcek) > 0) {
        echo "<script>
            alert('Persentase margin tersebut sudah tersedia!');
            window.history.back();
        </script>";
        exit();
    }

    // cek apakah sudah ada margin aktif
    if ($status == 1) {

        $resultaktif = mysqli_query($conn,
            "SELECT idmargin_penjualan
             FROM margin_penjualan
             WHERE status = 1"
        );

        if (mysqli_num_rows($resultaktif) > 0) {
            echo "<script>
                alert('Masih ada margin aktif! Nonaktifkan margin sebelumnya dulu.');
                window.history.back();
            </script>";
            exit();
        }
    }

    // insert margin
    $query = "INSERT INTO margin_penjualan
              (persen, status, iduser)
              VALUES (?, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param(
        $stmt,
        "dii",
        $persen,
        $status,
        $iduser
    );

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal menambahkan margin penjualan: " . mysqli_error($conn);
    }
}

include "../../navbar.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Margin Penjualan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <div class="pengadaan-container">

        <h1>Tambah Margin Penjualan Toko Kocak Gaming</h1>

        <form action="" method="POST" class="pengadaan-form">

            <div class="barang-section">

                <div class="form-field">
                    <label for="persen">Persentase Margin (%)</label>
                    <input
                        type="number"
                        id="persen"
                        name="persen"
                        step="0.01"
                        min="0"
                        placeholder="Masukkan persentase margin"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>

            </div>

            <div class="pengadaan-actions">

                <a href="index.php" class="btn-batal">
                    Batal
                </a>

                <button type="submit" class="btn-simpan">
                    Tambah Margin Penjualan
                </button>

            </div>

        </form>
    </div>
</body>
</html>