<?php
require_once '../../auth.php';
require_role([1, 2]);
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $vendor = $_POST['nama'];
    $badan_hukum = $_POST['badan_hukum'];
    $status = $_POST['status'];

    $query = "INSERT INTO vendor(nama_vendor, badan_hukum, status)
              VALUES (?, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param($stmt, "ssi",$vendor, $badan_hukum, $status);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal menambahkan vendor: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html> 
<head>
    <title>Tambah Vendor</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <h1>Tambah Vendor Toko Kocak Gaming</h1>
    <div class="form-container">
    <form action="" method="POST">
        <label for="nama">Nama Vendor:</label>
        <input type="text" id="nama" name="nama" required>

        <label for="badan_hukum">Badan Hukum:</label>
       <select id="badan_hukum" name="badan_hukum" required>
       <option value="">Pilih Badan Hukum</option>
       <option value="P">Perseroan Terbatas (PT)</option>
       <option value="C">Commanditaire Vennootschap (CV)</option>
       </select>

        <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="">Pilih Status</option>
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>

        <button type="submit">Tambah Vendor</button>
    </form>
</div>
</body>
</html>