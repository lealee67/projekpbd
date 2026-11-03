<?php
include "../../dbconnect.php";

$nama = $_POST['nama'];
$jenis = $_POST['jenis'];
$idsatuan = $_POST['idsatuan'];
$harga = $_POST['harga'];

$query = "UPDATE barang
          SET nama = '$nama', jenis = '$jenis', idsatuan = '$idsatuan', harga = '$harga'
          WHERE idbarang = '$_POST[idbarang]'";
$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: index.php");
    exit();
} else {
    echo "Gagal mengedit barang: " . mysqli_error($conn);
}