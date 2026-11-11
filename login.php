
<?php
session_start();
include "dbconnect.php";
global $conn;

// Kalau sudah login, langsung ke halaman utama
if (isset($_SESSION['iduser'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT u.iduser, u.username, u.password, u.idrole, r.nama_role
              FROM user u
              JOIN role r ON u.idrole = r.idrole
              WHERE u.username = ?";

    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if ($data && password_verify($password, $data['password'])) {
        session_regenerate_id(true);

        $_SESSION['iduser'] = $data['iduser'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['idrole'] = $data['idrole'];
        $_SESSION['nama_role'] = $data['nama_role'];

        header("Location: index.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Toko Kocak Gaming</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="login-page">

    <div class="login-card">

        <div class="login-header">
            <h1>Toko Kocak Gaming</h1>
            <p>Silakan masuk ke akun Anda</p>
        </div>

        <?php if ($error != ""): ?>
            <div class="login-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">
                <label>Username</label>
                <input
                    type="text"
                    name="username"
                    placeholder="Masukkan username"
                    required
                >
            </div>

            <div class="form-group">
                <label>Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >
            </div>

            <button type="submit" class="login-button">
                Login
            </button>

        </form>

    </div>

</body>
</html>
