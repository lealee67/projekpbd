<?php
session_start();
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

$iduser = $_SESSION['iduser'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $persen = $_POST['persen'];
    $status = $_POST['status'];

    // Cek persentase duplikat
    $cek = mysqli_prepare(
        $conn,
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

    // Cek apakah sudah ada margin aktif
    if ($status == 1) {

        $resultaktif = mysqli_query(
            $conn,
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

    // INSERT margin
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Margin Penjualan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <h1>Tambah Margin Penjualan Toko Kocak Gaming</h1>

    <div class="form-container">
        <form action="" method="POST">

            <label for="persen">Persentase Margin (%)</label>
            <input type="number" id="persen" name="persen" required>

            <label for="status">Status:</label>
            <select id="jenis" name="status" required>
                <option value="">Pilih Status</option>
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>
            <button type="submit">Tambah Margin Penjualan</button>
        </form>
    </div>
</body>
</html>