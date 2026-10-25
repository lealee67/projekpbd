<?php

$host = "localhost";
$user = "root";
$password = "";
$db = "toko_le";

$conn = mysqli_connect($host, $user, $password, $db);

if(!$conn) {
    die("Koneksi database gagal: ". mysqli_connect_error());
}
//else {
 //   echo "Koneksi database berhasil";
//}
?>