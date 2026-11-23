<?php
require_once '../../auth.php';
require_role([1]);
include "../../dbconnect.php";
global $conn;

// ambil data role
$queryrole = "SELECT idrole, nama_role
              FROM role
              ORDER BY nama_role ASC";

$resultrole = mysqli_query($conn, $queryrole);

// tambah user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $idrole = $_POST['idrole'];

    $query = "INSERT INTO user (username, password, idrole)
              VALUES (?, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param($stmt, "ssi", $username, $password, $idrole);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal menambahkan user: " . mysqli_error($conn);
    }
}

include "../../navbar.php";
?>

<!DOCTYPE html>
<html> 
<head>
    <title>Tambah User</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <div class="pengadaan-container">

        <h1>Tambah User Toko Kocak Gaming</h1>

        <form action="" method="POST" class="pengadaan-form">

            <div class="barang-section">

                <div class="form-field">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Masukkan username"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="idrole">Role</label>
                    <select id="idrole" name="idrole" required>
                        <option value="">-- Pilih Role --</option>

                        <?php
                        while ($role = mysqli_fetch_assoc($resultrole)) {
                            echo "<option value='" . $role['idrole'] . "'>"
                                . htmlspecialchars($role['nama_role']) .
                                "</option>";
                        }
                        ?>

                    </select>
                </div>

            </div>

            <div class="pengadaan-actions">
                <a href="index.php" class="btn-batal">Batal</a>

                <button type="submit" class="btn-simpan">
                    Tambah User
                </button>
            </div>
        </form>
    </div>
</body>
</html>