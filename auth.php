
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Memastikan pengguna sudah login
function require_login() {
    if (!isset($_SESSION['iduser'])) {
        header("Location: /projectpbd/login.php");
        exit();
    }
}

// Memeriksa hak akses berdasarkan role
function require_role($roles) {

    require_login();

    $idrole = (int) $_SESSION['idrole'];

    if (!in_array($idrole, $roles)) {
        http_response_code(403);
        exit("403 - Anda tidak memiliki hak akses ke halaman ini.");
    }
}

?>
