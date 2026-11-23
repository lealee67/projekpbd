<?php
require_once '../../auth.php';
require_role([1]);
include "../../dbconnect.php";
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

include "../../navbar.php";
?>

<!DOCTYPE html>
<html> 
<head>
    <title>Tambah Satuan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <div class="pengadaan-container">

        <h1>Tambah Satuan Barang Toko Kocak Gaming</h1>

        <form action="" method="POST" class="pengadaan-form">

            <div class="barang-section">

                <div class="form-field">
                    <label for="nama">Nama Satuan</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Masukkan nama satuan"
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
                <a href="index.php" class="btn-batal">Batal</a>
                <button type="submit" class="btn-simpan">
                    Tambah Satuan
                </button>
            </div>

        </form>

    </div>

</body>
</html>