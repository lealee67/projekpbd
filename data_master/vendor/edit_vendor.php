<?php
require_once '../../auth.php';
require_role([1, 2]);
include "../../dbconnect.php";
global $conn;

// ambil ID vendor
$idvendor = (int) ($_GET['idvendor'] ?? $_POST['idvendor'] ?? 0);

// ambil data vendor dari VIEW
$query = "SELECT ID, VENDOR, `BADAN HUKUM`, STATUS
          FROM viewvendor
          WHERE ID = $idvendor";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Data vendor tidak ditemukan!");
}

// nilai awal form
$nama_vendor = $data['VENDOR'];
$badan_hukum = ($data['BADAN HUKUM'] == "PT") ? "P" : "C";
$status = ($data['STATUS'] == "Aktif") ? 1 : 0;
$pesan = "";

// proses edit vendor
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama_vendor = $_POST['nama_vendor'];
    $badan_hukum = $_POST['badan_hukum'];
    $status = $_POST['status'];

    $query_update = "UPDATE vendor
                     SET nama_vendor = ?,
                         badan_hukum = ?,
                         status = ?
                     WHERE idvendor = ?";

    $stmt = mysqli_prepare($conn, $query_update);

    mysqli_stmt_bind_param(
        $stmt,
        "ssii",
        $nama_vendor,
        $badan_hukum,
        $status,
        $idvendor
    );

    $result_update = mysqli_stmt_execute($stmt);

    if ($result_update) {
        header("Location: index.php");
        exit();
    } else {
        $pesan = "Gagal mengubah vendor: " . mysqli_stmt_error($stmt);
    }
}

include "../../navbar.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Vendor</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <div class="pengadaan-container">

        <h1>Edit Vendor Toko Kocak Gaming</h1>

        <form action="" method="POST" class="pengadaan-form">

            <input type="hidden" name="idvendor"
                   value="<?php echo $idvendor; ?>">

            <div class="barang-section">

                <div class="form-field">
                    <label for="nama_vendor">Nama Vendor</label>
                    <input
                        type="text"
                        id="nama_vendor"
                        name="nama_vendor"
                        value="<?php echo htmlspecialchars($nama_vendor); ?>"
                        placeholder="Masukkan nama vendor"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="badan_hukum">Badan Hukum</label>
                    <select id="badan_hukum" name="badan_hukum" required>

                        <option value="P"
                            <?php if ($badan_hukum == "P") echo 'selected'; ?>>
                            Perseroan Terbatas (PT)
                        </option>

                        <option value="C"
                            <?php if ($badan_hukum == "C") echo 'selected'; ?>>
                            Commanditaire Vennootschap (CV)
                        </option>

                    </select>
                </div>

                <div class="form-field">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>

                        <option value="1"
                            <?php if ($status == 1) echo 'selected'; ?>>
                            Aktif
                        </option>

                        <option value="0"
                            <?php if ($status == 0) echo 'selected'; ?>>
                            Tidak Aktif
                        </option>

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