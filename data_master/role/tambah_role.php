<?php
include "../../dbconnect.php";
include "../../navbar.php";
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Role</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <h1>Tambah Role Toko Kocak Gaming</h1>

    <div class="form-container">
        <form action="" method="POST">

            <label for="nama">Role:</label>
            <input type="text" id="nama" name="nama" required>

            <button type="submit">Tambah Role</button>

        </form>
    </div>
</body>
</html>