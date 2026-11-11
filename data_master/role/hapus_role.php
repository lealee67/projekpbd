<?php
include "../../dbconnect.php";
global $conn;

$idrole = $_GET['idrole'];
$query = "DELETE FROM role
          WHERE idrole = ?";

$stmt = mysqli_prepare($conn, $query);

mysqli_stmt_bind_param($stmt, "i", $idrole);

$result = mysqli_stmt_execute($stmt);

if ($result) {
    header("Location: index.php");
    exit();
} else {
    echo "Gagal menghapus margin penjualan: " . mysqli_error($conn);
}