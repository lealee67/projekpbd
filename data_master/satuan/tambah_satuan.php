<?php
require_once '../../auth.php';
require_role([1]);
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $satuan = $_POST['nama'];
    $status = $_POST['status'];

    $query = "INSERT INTO satuan(nama_satuan, status)
              VALUES (?, ?)";

    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param($stmt, "si",$satuan, $status);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal menambahkan satuan: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html> 
<head>
    <title>Tambah Satuan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <h1>Tambah Satuan Barang Toko Kocak Gaming</h1>
    <div class="form-container">
    <form action=" " method="POST">
        <label for="nama">Nama Satuan:</label>
        <input type="text" id="nama" name="nama" required>

        <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="">Pilih Status</option>
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>

        <button type="submit">Tambah Satuan</button>
    </form>
</div>
</body>
</html>