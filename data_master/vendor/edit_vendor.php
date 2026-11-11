<?php
require_once '../../auth.php';
require_role([1, 2]);
include "../../dbconnect.php";
include "../../navbar.php";
global $conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama_vendor = $_POST['nama_vendor'];
    $idvendor = $_POST['idvendor'];
    $badan_hukum = $_POST['badan_hukum'];
    $status = $_POST['status'];

    $query = "UPDATE vendor
              SET nama_vendor = '$nama_vendor', 
                  badan_hukum = '$badan_hukum',
                  status = '$status'
              WHERE idvendor = '$idvendor'";

    $result = mysqli_query($conn, $query);

    if ($result) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal mengubah vendor: " . mysqli_error($conn);
    }
    } else {

    $idvendor = $_GET['idvendor'];

    $query = "SELECT * FROM viewvendor 
              WHERE ID = '$idvendor'";

    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Vendor</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <h1>Edit Vendor</h1>
    <div class="form-container">
      <form action="" method="POST">
        <input type="hidden" name="idvendor" value="<?php echo $data['ID']; ?>">

        <label for="nama">Nama Vendor:</label>
        <input type="text" id="nama_vendor" name="nama_vendor" value="<?php echo $data['VENDOR']; ?>" required>


        <label for="badan_hukum">Badan Hukum:</label>
       <select id="badan_hukum" name="badan_hukum" required>
       <option value="P" <?php if ($data['BADAN HUKUM'] == "PT") echo 'selected'; ?>>  Perseroan Terbatas (PT)</option> 
       <option value="C" <?php if ($data['BADAN HUKUM'] == "CV") echo 'selected'; ?>> Commanditaire Vennootschap (CV)</option>
    </select>
       
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