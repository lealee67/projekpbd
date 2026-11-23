
<?php
require_once '../../auth.php';
require_role([1, 2]);
include "../../dbconnect.php";
global $conn;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Ambil daftar penerimaan
$query_penerimaan = mysqli_query($conn, "
    SELECT idpenerimaan, created_at
    FROM penerimaan
    ORDER BY idpenerimaan DESC
");

// Ambil penerimaan yang dipilih
$idpenerimaan = (int) ($_GET['idpenerimaan'] ?? $_POST['idpenerimaan'] ?? 0);

// Ambil detail barang dari penerimaan yang dipilih
$detail_list = [];

if ($idpenerimaan > 0) {
    $query_detail = mysqli_query($conn, "
        SELECT
            dp.iddetail_penerimaan,
            dp.barang_idbarang,
            b.nama,
            dp.jumlah_terima,

            COALESCE((
                SELECT SUM(dr.jumlah)
                FROM detail_retur dr
                WHERE dr.iddetail_penerimaan = dp.iddetail_penerimaan
            ), 0) AS sudah_diretur,

            (
                dp.jumlah_terima - COALESCE((
                    SELECT SUM(dr.jumlah)
                    FROM detail_retur dr
                    WHERE dr.iddetail_penerimaan = dp.iddetail_penerimaan
                ), 0)
            ) AS sisa_retur,

            COALESCE((
                SELECT ks.stock
                FROM kartu_stok ks
                WHERE ks.idbarang = dp.barang_idbarang
                ORDER BY ks.idkartu_stok DESC
                LIMIT 1
            ), 0) AS stock

        FROM detail_penerimaan dp
        JOIN barang b
            ON dp.barang_idbarang = b.idbarang
        WHERE dp.idpenerimaan = $idpenerimaan
    ");

    $detail_list = mysqli_fetch_all($query_detail, MYSQLI_ASSOC);
}

// Proses simpan retur
if (isset($_POST['simpan'])) {

    $jumlah = $_POST['jumlah'] ?? [];
    $alasan = $_POST['alasan'] ?? [];

    try {

        if ($idpenerimaan <= 0) {
            throw new Exception("Pilih transaksi penerimaan terlebih dahulu.");
        }

        if (count($detail_list) == 0) {
            throw new Exception("Penerimaan tidak memiliki detail barang.");
        }

        $data_retur = [];
        $jumlah_per_barang = [];

        // Validasi barang yang akan diretur
        foreach ($detail_list as $detail) {

            $iddetail = $detail['iddetail_penerimaan'];
            $jml = (int) ($jumlah[$iddetail] ?? 0);
            $alasan_retur = trim($alasan[$iddetail] ?? '');

            if ($jml > 0) {

                if ($jml > $detail['sisa_retur']) {
                    throw new Exception(
                        "Jumlah retur {$detail['nama']} melebihi sisa barang yang bisa diretur."
                    );
                }

                if ($alasan_retur == "") {
                    throw new Exception(
                        "Alasan retur untuk {$detail['nama']} wajib diisi."
                    );
                }

                $idbarang = (int) $detail['barang_idbarang'];

                // Hitung total retur per barang
                if (!isset($jumlah_per_barang[$idbarang])) {
                    $jumlah_per_barang[$idbarang] = 0;
                }

                $jumlah_per_barang[$idbarang] += $jml;

                $data_retur[] = [
                    'iddetail' => $iddetail,
                    'jumlah' => $jml,
                    'alasan' => $alasan_retur
                ];
            }
        }

        if (count($data_retur) == 0) {
            throw new Exception("Pilih minimal satu barang untuk diretur.");
        }

        // Validasi stok tersedia
        foreach ($detail_list as $detail) {

            $idbarang = (int) $detail['barang_idbarang'];

            if (isset($jumlah_per_barang[$idbarang])) {

                if ($jumlah_per_barang[$idbarang] > $detail['stock']) {
                    throw new Exception(
                        "Stok {$detail['nama']} tidak mencukupi untuk retur."
                    );
                }

                // Cukup cek satu kali untuk barang yang sama
                unset($jumlah_per_barang[$idbarang]);
            }
        }

        // Mulai transaksi database
        mysqli_begin_transaction($conn);

        // Simpan data utama retur
        $iduser = (int) $_SESSION['iduser'];

        $query_retur = mysqli_prepare($conn, "
            INSERT INTO retur (idpenerimaan, iduser)
            VALUES (?, ?)
        ");

        mysqli_stmt_bind_param(
            $query_retur,
            "ii",
            $idpenerimaan,
            $iduser
        );

        mysqli_stmt_execute($query_retur);

        // Ambil ID retur yang baru dibuat
        $idretur = mysqli_insert_id($conn);

        // Simpan detail retur
        $query_detail_retur = mysqli_prepare($conn, "
            INSERT INTO detail_retur
            (jumlah, alasan, idretur, iddetail_penerimaan)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($data_retur as $data) {

            $jml = $data['jumlah'];
            $alasan_retur = $data['alasan'];
            $iddetail = $data['iddetail'];

            mysqli_stmt_bind_param(
                $query_detail_retur,
                "isii",
                $jml,
                $alasan_retur,
                $idretur,
                $iddetail
            );

            mysqli_stmt_execute($query_detail_retur);
        }

        mysqli_stmt_close($query_retur);
        mysqli_stmt_close($query_detail_retur);

        // Simpan transaksi
        mysqli_commit($conn);

        header("Location: index.php");
        exit();

    } catch (Exception $e) {

        mysqli_rollback($conn);
        $pesan_error = $e->getMessage();
    }
}

include "../../navbar.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Retur</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>

<div class="pengadaan-container">

    <h1>Tambah Retur Barang</h1>

    <?php if (isset($pesan_error)) { ?>
        <p style="color: red;">
            <?php echo htmlspecialchars($pesan_error); ?>
        </p>
    <?php } ?>

    <form method="GET" class="pengadaan-form">

        <div class="pengadaan-section">
            <h3>Informasi Penerimaan</h3>

            <div class="form-field">
                <label>Transaksi Penerimaan</label>

                <select name="idpenerimaan" onchange="this.form.submit()" required>
                    <option value="">-- Pilih Penerimaan --</option>

                    <?php while ($p = mysqli_fetch_assoc($query_penerimaan)) { ?>
                        <option
                            value="<?php echo $p['idpenerimaan']; ?>"
                            <?php echo ($idpenerimaan == $p['idpenerimaan']) ? 'selected' : ''; ?>
                        >
                            Penerimaan #<?php echo $p['idpenerimaan']; ?>
                            - <?php echo $p['created_at']; ?>
                        </option>
                    <?php } ?>

                </select>
            </div>
        </div>

    </form>

    <?php if ($idpenerimaan > 0) { ?>

        <?php if (count($detail_list) > 0) { ?>

            <form method="POST"
                  action="?idpenerimaan=<?php echo $idpenerimaan; ?>"
                  class="pengadaan-form">

                <input type="hidden"
                       name="idpenerimaan"
                       value="<?php echo $idpenerimaan; ?>">

                <div class="pengadaan-section">

                    <h3>Detail Barang yang Diretur</h3>

                    <table>
                        <tr>
                            <th>Barang</th>
                            <th>Jumlah Diterima</th>
                            <th>Sudah Diretur</th>
                            <th>Sisa Bisa Diretur</th>
                            <th>Stok Sekarang</th>
                            <th>Jumlah Retur</th>
                            <th>Alasan Retur</th>
                        </tr>

                        <?php foreach ($detail_list as $detail) { ?>

                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($detail['nama']); ?>
                                </td>

                                <td>
                                    <?php echo $detail['jumlah_terima']; ?>
                                </td>

                                <td>
                                    <?php echo $detail['sudah_diretur']; ?>
                                </td>

                                <td>
                                    <?php echo $detail['sisa_retur']; ?>
                                </td>

                                <td>
                                    <?php echo $detail['stock']; ?>
                                </td>

                                <td>
                                    <input
                                        type="number"
                                        name="jumlah[<?php echo $detail['iddetail_penerimaan']; ?>]"
                                        min="0"
                                        max="<?php echo min($detail['sisa_retur'], $detail['stock']); ?>"
                                        value="0"
                                    >
                                </td>

                                <td>
                                    <input
                                        type="text"
                                        name="alasan[<?php echo $detail['iddetail_penerimaan']; ?>]"
                                        maxlength="200"
                                        placeholder="Alasan retur"
                                    >
                                </td>
                            </tr>

                        <?php } ?>

                    </table>

                </div>

                <button type="submit" name="simpan" class="button">
                    Simpan Retur
            </button>

            </form>

        <?php } else { ?>

            <p>Tidak ada detail barang pada penerimaan ini.</p>

        <?php } ?>

    <?php } ?>

</div>

</body>
</html>
