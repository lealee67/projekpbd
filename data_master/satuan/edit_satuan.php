<?php
require_once '../../auth.php';
require_role([1]);
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama_satuan = $_POST['nama_satuan'];
    $idsatuan = $_POST['idsatuan'];
    $status = $_POST['status'];

    $query = "UPDATE satuan
              SET nama_satuan = '$nama_satuan', 
                  status = '$status'
              WHERE idsatuan = '$idsatuan'";

    $result = mysqli_query($conn, $query);

    if ($result) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal mengubah satuan: " . mysqli_error($conn);
    }
    } else {

    $idsatuan = $_GET['idsatuan'];

    $query = "SELECT * FROM viewsatuanbarang
              WHERE ID = '$idsatuan'";

    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Satuan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <h1>Edit Satuan</h1>
    <div class="form-container">
        <form action="" method="POST">
            <input type="hidden" name="idsatuan" value="<?php echo $data['ID']; ?>">

            <label for="nama">Satuan:</label>
            <input type="text" id="nama_satuan" name="nama_satuan" value="<?php echo $data['SATUAN']; ?>" required>

            <label for="status">Status:</label>
            <select id="status" name="status" required>
                <option value="1" <?php if ($data['STATUS'] == "Aktif") echo 'selected'; ?>>Aktif</option>
                <option value="0"<?php if ($data['STATUS'] == "Tidak Aktif") echo 'selected'; ?>>Tidak Aktif</option>
            </select>

            <button type="submit">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>
