<?php
require_once '../../auth.php';
require_role([1]);
include "../../dbconnect.php";
global $conn;

// ambil ID role
$idrole = (int) ($_GET['idrole'] ?? $_POST['idrole'] ?? 0);

// ambil data role dari VIEW
$query = "SELECT ID, ROLE
          FROM viewrole
          WHERE ID = $idrole";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data role tidak ditemukan!");
}

// nilai awal form
$nama = $data['ROLE'];
$pesan = "";

// edit role
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama = $_POST['nama'];

    $query_update = "UPDATE role
                     SET nama_role = ?
                     WHERE idrole = ?";

    $stmt = mysqli_prepare($conn, $query_update);

    mysqli_stmt_bind_param($stmt, "si", $nama, $idrole);

    $result_update = mysqli_stmt_execute($stmt);

    if ($result_update) {
        header("Location: index.php");
        exit();
    } else {
        $pesan = "Gagal mengubah role: " . mysqli_stmt_error($stmt);
    }
}

include "../../navbar.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Role</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <div class="pengadaan-container">

        <h1>Edit Role Toko Kocak Gaming</h1>

        <form action="" method="POST" class="pengadaan-form">

            <input type="hidden" name="idrole"
                   value="<?php echo $idrole; ?>">

            <div class="barang-section">

                <div class="form-field">
                    <label for="nama">Nama Role</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="<?php echo htmlspecialchars($nama); ?>"
                        placeholder="Masukkan nama role"
                        required
                    >
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
