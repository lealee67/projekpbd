<?php
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama = $_POST['nama'];
    $idrole = $_POST['idrole'];

    $query = "UPDATE role
              SET nama_role = '$nama'
              WHERE idrole = '$idrole'";

    $result = mysqli_query($conn, $query);

    if ($result) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal mengubah role: " . mysqli_error($conn);
    }
    } else {

    $idrole = $_GET['idrole'];

    $query = "SELECT * FROM viewrole
              WHERE ID = '$idrole'";

    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Role</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <h1>Edit Role</h1>
    <div class="form-container">
        <form action="" method="POST">
            <input type="hidden" name="idrole" value="<?php echo $data['ID']; ?>">

            <label for="nama">Role:</label>
            <input type="text" id="nama" name="nama" value="<?php echo $data['ROLE']; ?>" required>

            <button type="submit">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>
