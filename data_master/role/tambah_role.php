<?php
require_once '../../auth.php';
require_role([1]);
include "../../dbconnect.php";
global $conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $role = $_POST['nama'];

    $query = "INSERT INTO role(nama_role)
              VALUES (?)";

    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param($stmt, "s",$role);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal menambahkan role: " . mysqli_error($conn);
    }
}

include "../../navbar.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Role</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <div class="pengadaan-container">

        <h1>Tambah Role Toko Kocak Gaming</h1>

        <form action="" method="POST" class="pengadaan-form">

            <div class="barang-section">

                <div class="form-field">
                    <label for="nama">Nama Role</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Masukkan nama role"
                        required
                    >
                </div>

            </div>

            <div class="pengadaan-actions">

                <a href="index.php" class="btn-batal">
                    Batal
                </a>

                <button type="submit" class="btn-simpan">
                    Tambah Role
                </button>
            </div>

        </form>
    </div>
</body>
</html>