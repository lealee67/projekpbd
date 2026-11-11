<?php
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $idrole = $_POST['idrole'];

    $query = "INSERT INTO user (username, password, idrole)
              VALUES (?, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);

    mysqli_stmt_bind_param($stmt, "ssi",$username, $password, $idrole);

    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal menambahkan user: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html> 
<head>
    <title>Tambah User</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <h1>Tambah User Toko Kocak Gaming</h1>

    <div class="form-container">
        <form action="" method="POST">
        
            <label for="nama">Username:</label>
            <input type="text" id="username" name="username" required>

        <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

        <label for="idrole">Role:</label>
        <select id="idrole" name="idrole" required>
            <option value="">Pilih Role</option>

            <?php
            $queryrole = "SELECT idrole, nama_role 
            FROM role
            ORDER BY nama_role ASC";

            $resultrole = mysqli_query($conn, $queryrole);

            while ($role = mysqli_fetch_assoc($resultrole)) {
                echo "<option value='" . $role['idrole'] . "'>" . $role['nama_role'] . "</option>";
            }
            ?> 

        </select> 

        <button type="submit">Tambah User</button>
    </form>
</div>
</body>
</html>