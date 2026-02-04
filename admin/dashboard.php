<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador</title>
</head>
<body>

<h1>Panel Administrador</h1>

<ul>
    <li><a href="usuarios/listar.php">Usuarios</a></li>
    <li><a href="productos/listar.php">Platillos</a></li>
    <li><a href="ventas/registrar_venta.php">Ventas</a></li>
    <li><a href="estadisticas/index.php" class="card">
    <p>Estadísticas</p>
</a></li>
    <li><a href="../logout.php">Cerrar sesión</a></li>
</ul>

</body>
</html>
