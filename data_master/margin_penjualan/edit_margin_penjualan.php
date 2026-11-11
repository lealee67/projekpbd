<?php
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

$iduser = $_SESSION['iduser'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $persen = $_POST['persen'];
    $idmargin_penjualan = $_POST['idmargin_penjualan'];
    $status = $_POST['status'];

    $query = "UPDATE margin_penjualan
              SET persen = '$persen',
                  status = '$status'
              WHERE idmargin_penjualan = '$idmargin_penjualan'";

    $result = mysqli_query($conn, $query);

    if ($result) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal mengubah margin penjualan: " . mysqli_error($conn);
    }
    } else {

$idmargin_penjualan = $_GET['idmargin_penjualan'];

$query = "SELECT * FROM viewmargin_penjualan
          WHERE ID = '$idmargin_penjualan'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Margin Penjualan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <h1>Edit Margin Penjualan</h1>
    <div class = "form-container">
        <form action=" " method="POST">
            <input type="hidden" name="idmargin_penjualan" value="<?php echo $data['ID']; ?>">

            <label for="persen">Persentase Margin (%):</label>
            <input type="number" id="persen" name="persen" value="<?php echo $data['PERSENTASE']; ?>" required>

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