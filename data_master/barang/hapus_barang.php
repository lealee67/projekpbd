<?php
require_once '../../auth.php';
require_role([1]);
include "../../dbconnect.php";
global $conn;

$idbarang = $_GET['idbarang'];
$query = "UPDATE barang
          SET status = 0
          WHERE idbarang = $idbarang";
$result = mysqli_query($conn, $query);
if ($result) {
    header("Location: index.php");
    exit();
} else {
    echo "Gagal menghapus barang: " . mysqli_error($conn);
}