<?php
require_once '../../auth.php';
require_role([1]);
include "../../dbconnect.php";
global $conn;

// ambil ID satuan dari URL atau POST
$idsatuan = (int) ($_GET['idsatuan'] ?? $_POST['idsatuan'] ?? 0);

// ambil data satuan lama
$query = "SELECT * FROM viewsatuanbarang WHERE ID = $idsatuan";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// update satuan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nama_satuan = $_POST['nama_satuan'];
    $status = $_POST['status'];

    $query_update = "UPDATE satuan
                     SET nama_satuan = ?, status = ?
                     WHERE idsatuan = ?";

    $stmt = mysqli_prepare($conn, $query_update);

    mysqli_stmt_bind_param(
        $stmt,
        "sii",
        $nama_satuan,
        $status,
        $idsatuan
    );

    $result_update = mysqli_stmt_execute($stmt);

    if ($result_update) {
        header("Location: index.php");
        exit();
    } else {
        echo "Gagal mengubah satuan: " . mysqli_error($conn);
    }
}

include "../../navbar.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Satuan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

    <div class="pengadaan-container">

        <h1>Edit Satuan Barang Toko Kocak Gaming</h1>

        <form action="" method="POST" class="pengadaan-form">

            <input type="hidden" name="idsatuan"
                   value="<?php echo $data['ID']; ?>">

            <div class="barang-section">

                <div class="form-field">
                    <label for="nama_satuan">Nama Satuan</label>
                    <input
                        type="text"
                        id="nama_satuan"
                        name="nama_satuan"
                        value="<?php echo htmlspecialchars($data['SATUAN']); ?>"
                        placeholder="Masukkan nama satuan"
                        required
                    >
                </div>

                <div class="form-field">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="1"
                            <?php if ($data['STATUS'] == "Aktif") echo 'selected'; ?>>
                            Aktif
                        </option>

                        <option value="0"
                            <?php if ($data['STATUS'] == "Tidak Aktif") echo 'selected'; ?>>
                            Tidak Aktif
                        </option>
                    </select>
                </div>

            </div>

            <div class="pengadaan-actions">
                <a href="index.php" class="btn-batal">Batal</a>

                <button type="submit" class="btn-simpan">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</body>
</html>
