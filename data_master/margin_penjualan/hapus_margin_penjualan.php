<?php
include "../../dbconnect.php";
global $conn;

$idmargin_penjualan = $_GET['idmargin_penjualan'];
$query = "DELETE FROM margin_penjualan
          WHERE idmargin_penjualan = ?";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $idmargin_penjualan);

$result = mysqli_stmt_execute($stmt);

if ($result) {
    header("Location: index.php");
    exit();
} else {
    echo "Gagal menghapus margin penjualan: " . mysqli_error($conn);
}