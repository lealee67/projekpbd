<?php
include "../../dbconnect.php";

$nama = $_POST['nama'];
$jenis = $_POST['jenis'];
$idsatuan = $_POST['idsatuan'];
$harga = $_POST['harga'];

$query = "INSERT INTO barang (nama, jenis, status, idsatuan, harga) 
          VALUES ('$nama', '$jenis', 1, '$idsatuan', '$harga')";

$result = mysqli_query($conn, $query);

if ($result) {
    header("Location: index.php");
    exit();
} else {
    echo "Gagal menambahkan barang: " . mysqli_error($conn);
}
?>