<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: /restaurante/index.php");
    exit;
}
function soloAdmin() {
    if ($_SESSION['rol'] !== 'admin') {
        header("Location: /restaurante/index.php");
        exit;
    }
}
