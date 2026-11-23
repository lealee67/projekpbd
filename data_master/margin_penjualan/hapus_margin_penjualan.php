<?php
include "../../dbconnect.php";
require_once '../../auth.php';
require_role([1]);
global $conn;

$idmargin_penjualan = $_GET['idmargin_penjualan'];
$query = "UPDATE margin_penjualan
          SET status = 0
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