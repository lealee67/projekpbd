
<?php
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $iduser = $_POST['iduser'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $idrole = $_POST['idrole'];

    // Cek apakah username sudah digunakan user lain
    $querycek = "SELECT iduser FROM user
                 WHERE username = ? AND iduser != ?";

    $stmtcek = mysqli_prepare($conn, $querycek);
    mysqli_stmt_bind_param($stmtcek, "si", $username, $iduser);
    mysqli_stmt_execute($stmtcek);
    $resultcek = mysqli_stmt_get_result($stmtcek);

    if (mysqli_num_rows($resultcek) > 0) {
        echo "Username sudah digunakan user lain!";
    } else {

        if ($password != "") {
            $password = password_hash($password, PASSWORD_DEFAULT);

            $query = "UPDATE user
                      SET username = ?,
                          password = ?,
                          idrole = ?
                      WHERE iduser = ?";

            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssii", $username, $password, $idrole, $iduser);

        } else {

            $query = "UPDATE user
                      SET username = ?,
                          idrole = ?
                      WHERE iduser = ?";

            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sii", $username, $idrole, $iduser);
        }

        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            header("Location: index.php");
            exit();
        } else {
            echo "Gagal mengubah user: " . mysqli_stmt_error($stmt);
        }
    }

} else {

    $iduser = $_GET['iduser'];

    $query = "SELECT iduser, username, idrole
              FROM user
              WHERE iduser = '$iduser'";

    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);

    $idrole = $data['idrole'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <h1>Edit User Toko Kocak Gaming</h1>

    <div class="form-container">
        <form action="" method="POST">

            <input type="hidden" name="iduser"
                   value="<?php echo $data['iduser']; ?>">

            <label for="username">Username:</label>
            <input type="text" id="username" name="username"
                   value="<?php echo $data['username']; ?>" required>

            <label for="password">Password Baru:</label>
            <input type="password" id="password" name="password"
                   placeholder="Kosongkan jika tidak ingin mengganti">

            <label for="idrole">Role:</label>
            <select id="idrole" name="idrole" required>
                <option value="">Pilih Role</option>

                <?php
                $queryrole = "SELECT idrole, nama_role
                              FROM role
                              ORDER BY nama_role ASC";

                $resultrole = mysqli_query($conn, $queryrole);

                while ($role = mysqli_fetch_assoc($resultrole)) {
                    $selected = ($role['idrole'] == $data['idrole']) ? 'selected' : '';

                    echo "<option value='" . $role['idrole'] . "' $selected>"
                         . $role['nama_role'] . "</option>";
                }
                ?>
            </select>

            <button type="submit">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>
