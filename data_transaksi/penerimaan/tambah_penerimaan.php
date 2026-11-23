<?php
require_once '../../auth.php';
require_role([1, 2]);
include "../../dbconnect.php";
global $conn;

// data pengadaan yang masih diproses
$query_pengadaan = mysqli_query($conn, "
    SELECT p.idpengadaan, v.nama_vendor
    FROM pengadaan p
    JOIN vendor v ON p.vendor_idvendor = v.idvendor
    WHERE p.status = 'P'
    ORDER BY p.idpengadaan DESC
");

$pengadaan_list = mysqli_fetch_all($query_pengadaan, MYSQLI_ASSOC);

// Mengambil ID pengadaan yang dipilih
$idpengadaan = (int) ($_GET['id'] ?? 0);

$detail_list = [];
$pesan = "";

// Jika pengadaan sudah dipilih
if ($idpengadaan > 0) {

    // Cek apakah pengadaan masih diproses
    $cek = mysqli_query($conn, "
        SELECT idpengadaan
        FROM pengadaan
        WHERE idpengadaan = $idpengadaan
        AND status = 'P'
    ");


    if ($cek && mysqli_num_rows($cek) > 0) {

        // Mengambil barang dan jumlah yang sudah diterima
        $query_detail = mysqli_query($conn, "
            SELECT dp.idbarang,
            b.nama,
            SUM(dp.jumlah) AS jumlah_pesan,
            ROUND(
                SUM(dp.jumlah * dp.harga_satuan) / SUM(dp.jumlah)
            ) AS harga_modal,
                COALESCE(terima.jumlah_diterima, 0) AS jumlah_diterima,
                SUM(dp.jumlah) - COALESCE(terima.jumlah_diterima, 0) AS sisa
            FROM detail_pengadaan dp
            JOIN barang b
                ON dp.idbarang = b.idbarang
            LEFT JOIN (
                SELECT
                    p.idpengadaan,
                    dpr.barang_idbarang,
                    SUM(dpr.jumlah_terima) AS jumlah_diterima
                FROM penerimaan p
                JOIN detail_penerimaan dpr
                    ON p.idpenerimaan = dpr.idpenerimaan
                GROUP BY p.idpengadaan, dpr.barang_idbarang
            ) terima
                ON dp.idpengadaan = terima.idpengadaan
                AND dp.idbarang = terima.barang_idbarang
            WHERE dp.idpengadaan = $idpengadaan
            GROUP BY dp.idbarang, b.nama, terima.jumlah_diterima
            ORDER BY b.nama
        ");

        if ($query_detail) {
            $detail_list = mysqli_fetch_all($query_detail, MYSQLI_ASSOC);
        }

    } else {
        $pesan = "Pengadaan tidak ditemukan atau sudah selesai.";
    }
    // proses simpan penerimaan
if (isset($_POST['simpan'])) {

    $idpengadaan = (int) $_POST['idpengadaan'];
    $idbarang = $_POST['idbarang'];
    $jumlah = $_POST['jumlah_terima'];
    $harga = $_POST['harga_satuan_terima'];

    $ada_barang = false;
    $pesan = "";

    // validasi jumlah dan harga
    foreach ($detail_list as $i => $item) {

        $jml = (int) $jumlah[$i];
        $hrg = (int) $harga[$i];
        $sisa = (int) $item['sisa'];

        if ((int) $idbarang[$i] != (int) $item['idbarang']) {
            $pesan = "Data barang tidak sesuai.";
            break;
        }

        if ($jml < 0 || $jml > $sisa) {
            $pesan = "Jumlah penerimaan melebihi sisa pesanan.";
            break;
        }

        if ($jml > 0) {

            if ($hrg <= 0) {
                $pesan = "Harga satuan wajib diisi untuk barang yang diterima.";
                break;
            }

            $ada_barang = true;
        }
    }

    if (!$ada_barang && $pesan == "") {
        $pesan = "Minimal harus ada satu barang yang diterima.";
    }

    // jika validasi berhasil
    if ($pesan == "") {

        mysqli_begin_transaction($conn);

        $iduser = (int) $_SESSION['iduser'];

        // simpan data utama penerimaan
        $query = mysqli_query($conn, "
            INSERT INTO penerimaan
            (status, idpengadaan, iduser)
            VALUES ('S', $idpengadaan, $iduser)
        ");

        if ($query) {

            $idpenerimaan = mysqli_insert_id($conn);

            // simpan setiap barang yang diterima
            foreach ($detail_list as $i => $item) {

                $id = (int) $idbarang[$i];
                $jml = (int) $jumlah[$i];
                $hrg = (int) $harga[$i];

                // barang dengan jumlah 0 tidak disimpan
                if ($jml > 0) {

                    $query_detail = mysqli_query($conn, "
                        INSERT INTO detail_penerimaan
                        (jumlah_terima, harga_satuan_terima,
                         sub_total_terima, idpenerimaan, barang_idbarang)
                        VALUES ($jml, $hrg, 0, $idpenerimaan, $id)
                    ");

                    if (!$query_detail) {
                        $pesan = mysqli_error($conn);
                        break;
                    }
                }
            }

        } else {
            $pesan = mysqli_error($conn);
        }
    }

        // panggil stored procedure
        if ($pesan == "") {

            $query_sp = mysqli_query($conn, "
                CALL sp_penerimaan($idpengadaan)
            ");

            if (!$query_sp) {
                $pesan = mysqli_error($conn);
            }
        }

        // simpan atau batalkan transaksi
        if ($pesan == "") {

            mysqli_commit($conn);

            header("Location: index.php");
            exit();

        } else {

            mysqli_rollback($conn);

        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Penerimaan</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <?php include "../../navbar.php"; ?>

<div class="penerimaan-container">

    <h1>Tambah Penerimaan Barang</h1>

    <form method="GET" class="penerimaan-form">

        <div class="penerimaan-section">

            <h3>Informasi Penerimaan</h3>

            <div class="form-field">
                <label>Pilih Pengadaan</label>

                <select name="id" required>
                    <option value="">-- Pilih Pengadaan --</option>

                    <?php foreach ($pengadaan_list as $pengadaan): ?>

                        <option value="<?= $pengadaan['idpengadaan']; ?>">
                            Pengadaan #<?= $pengadaan['idpengadaan']; ?>
                            - <?= htmlspecialchars($pengadaan['nama_vendor']); ?>
                        </option>

                    <?php endforeach; ?>

                </select>
            </div>

            <button type="submit" class="btn-simpan">
                Pilih Pengadaan
            </button>

        </div>

    </form>

<?php if ($pesan != ""): ?>

    <p><?= htmlspecialchars($pesan); ?></p>

<?php elseif ($idpengadaan > 0): ?>

    <form method="POST" class="penerimaan-detail-form" action="tambah_penerimaan.php?id=<?= $idpengadaan; ?>">

        <input type="hidden" name="idpengadaan"
               value="<?= $idpengadaan; ?>">

        <div class="penerimaan-section penerimaan-detail">

            <h3>Barang yang Diterima</h3>

            <table class="penerimaan-table">

                <tr>
                    <th>Barang</th>
                    <th>Jumlah Pesanan</th>
                    <th>Sudah Diterima</th>
                    <th>Sisa</th>
                    <th>Jumlah Diterima</th>
                    <th>Harga Satuan</th>
                </tr>

                <?php foreach ($detail_list as $item): ?>

                    <tr>
                        <td>
                            <?= htmlspecialchars($item['nama']); ?>

                            <input type="hidden" name="idbarang[]"
                                   value="<?= $item['idbarang']; ?>">
                        </td>

                        <td><?= $item['jumlah_pesan']; ?></td>
                        <td><?= $item['jumlah_diterima']; ?></td>
                        <td><?= $item['sisa']; ?></td>

                        <td>
                            <input type="number"
                                   name="jumlah_terima[]"
                                   min="0"
                                   max="<?= $item['sisa']; ?>"
                                   value="0"
                                   required>
                        </td>

                        <td>
                            <input type="number"name="harga_satuan_terima[]"min="1"
                            value="<?= (int) $item['harga_modal']; ?>"placeholder="Harga aktual">
                        </td>
                    </tr>

                <?php endforeach; ?>

            </table>

            <button type="submit" name="simpan" class="btn-simpan">
                Simpan Penerimaan
            </button>

        </div>

    </form>

<?php endif; ?>

</div>
