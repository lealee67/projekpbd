<?php
include "../../dbconnect.php";
global $conn;

$iduser = $_GET['iduser'];
$query = "DELETE FROM user
          WHERE iduser = $iduser";
$result = mysqli_query($conn, $query);
if ($result) {
    header("Location: index.php");
    exit();
} else {
    echo "Gagal menghapus user: " . mysqli_error($conn);
}