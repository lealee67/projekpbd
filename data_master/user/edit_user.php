<?php
require_once '../../auth.php'; 
include "../../dbconnect.php";
global $conn;

// ambil ID user
$iduser = (int) ($_GET['iduser'] ?? $_POST['iduser'] ?? 0);

// ambil data user lama
$query = "SELECT ID, USERNAME, ROLE
          FROM viewuser
          WHERE ID = $iduser";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data user tidak ditemukan!");
}

// ambil idrole berdasarkan nama role dari VIEW
$query_role = "SELECT idrole
               FROM role
               WHERE nama_role = ?";

$stmt_role = mysqli_prepare($conn, $query_role);
mysqli_stmt_bind_param($stmt_role, "s", $data['ROLE']);
mysqli_stmt_execute($stmt_role);

$result_role = mysqli_stmt_get_result($stmt_role);
$data_role = mysqli_fetch_assoc($result_role);

if (!$data_role) {
    die("Role user tidak ditemukan!");
}

// nilai awal form
$username = $data['USERNAME'];
$selectedRole = $data_role['idrole'];
$pesan = "";

// edit user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $idrole = $_POST['idrole'];
    $selectedRole = $idrole;

    // cek username sudah digunakan user lain
    $querycek = "SELECT iduser FROM user
                 WHERE username = ? AND iduser != ?";

    $stmtcek = mysqli_prepare($conn, $querycek);
    mysqli_stmt_bind_param($stmtcek, "si", $username, $iduser);
    mysqli_stmt_execute($stmtcek);
    $resultcek = mysqli_stmt_get_result($stmtcek);

    if (mysqli_num_rows($resultcek) > 0) {

        $pesan = "Username sudah digunakan user lain!";

    } else {

        if ($password != "") {
            $password = password_hash($password, PASSWORD_DEFAULT);

            $query_update = "UPDATE user
                             SET username = ?,
                                 password = ?,
                                 idrole = ?
                             WHERE iduser = ?";

            $stmt = mysqli_prepare($conn, $query_update);

            mysqli_stmt_bind_param(
                $stmt,
                "ssii",
                $username,
                $password,
                $idrole,
                $iduser
            );

        } else {

            $query_update = "UPDATE user
                             SET username = ?,
                                 idrole = ?
                             WHERE iduser = ?";

            $stmt = mysqli_prepare($conn, $query_update);

            mysqli_stmt_bind_param(
                $stmt,
                "sii",
                $username,
                $idrole,
                $iduser
            );
        }

        $result_update = mysqli_stmt_execute($stmt);

        if ($result_update) {
            header("Location: index.php");
            exit();
        } else {
            $pesan = "Gagal mengubah user: " . mysqli_stmt_error($stmt);
        }
    }
}

// ambil data role
$queryrole = "SELECT idrole, nama_role
              FROM role
              ORDER BY nama_role ASC";

$resultrole = mysqli_query($conn, $queryrole);

include "../../navbar.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <div class="pengadaan-container">

        <h1>Edit User Toko Kocak Gaming</h1>

        <form action="" method="POST" class="pengadaan-form">

            <input type="hidden" name="iduser"
                   value="<?php echo $iduser; ?>">

            <div class="barang-section">

                <div class="form-field">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?php echo htmlspecialchars($username); ?>"
                        placeholder="Masukkan username"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="password">Password Baru</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Kosongkan jika tidak ingin mengganti"
                    >
                </div>

                <div class="form-field">
                    <label for="idrole">Role</label>
                    <select id="idrole" name="idrole" required>

                        <option value="">-- Pilih Role --</option>

                        <?php
                        while ($role = mysqli_fetch_assoc($resultrole)) {

                            $selected = ($role['idrole'] == $selectedRole)
                                ? 'selected'
                                : '';

                            echo "<option value='" . $role['idrole'] . "' $selected>"
                                . htmlspecialchars($role['nama_role']) .
                                "</option>";
                        }
                        ?>

                    </select>
                </div>

                <?php if ($pesan != "") { ?>
                    <p style="color: red;">
                        <?php echo htmlspecialchars($pesan); ?>
                    </p>
                <?php } ?>

            </div>

            <div class="pengadaan-actions">

                <a href="index.php" class="btn-batal">
                    Batal
                </a>

                <button type="submit" class="btn-simpan">
                    Simpan Perubahan
                </button>

            </div>

        </form>
    </div>
</body>
</html>
