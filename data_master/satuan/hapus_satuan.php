<?php
require_once '../../auth.php';
require_role([1]);
include "../../dbconnect.php";
global $conn;

$idsatuan = $_GET['idsatuan'];
$query = "DELETE FROM satuan
          WHERE idsatuan = $idsatuan";
$result = mysqli_query($conn, $query);
if ($result) {
    header("Location: index.php");
    exit();
} else {
    echo "Gagal menghapus satuan: " . mysqli_error($conn);
}