<?php
require_once '../../auth.php';
require_role([1, 2]);
include "../../dbconnect.php";

$idvendor = $_GET['idvendor'];
$query = "DELETE FROM vendor
          WHERE idvendor = $idvendor";
$result = mysqli_query($conn, $query);
if ($result) {
    header("Location: index.php");
    exit();
} else {
    echo "Gagal menghapus vendor: " . mysqli_error($conn);
}