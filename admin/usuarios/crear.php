<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}
require_once "../../config/conexion.php";

if ($_POST) {
    $nombre = $_POST['nombre'];
    $usuario = $_POST['usuario'];
    $clave = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    $conexion->query("
        INSERT INTO usuarios (nombre, usuario, contraseña, rol, estado)
        VALUES ('$nombre','$usuario','$clave','$rol','activo')
    ");
    header("Location: listar.php");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo Usuario</title>
<link rel="stylesheet" href="../../public/css/estilo.css">
</head>

<body class="form-body">

<div class="form-card">
<h2>👤 Nuevo Usuario</h2>

<form method="POST">
<input type="text" name="nombre" placeholder="Nombre completo" required>
<input type="text" name="usuario" placeholder="Usuario" required>
<input type="password" name="password" placeholder="Contraseña" required>

<select name="rol">
    <option value="admin">Administrador</option>
    <option value="caja">Cajero</option>
</select>

<button class="btn btn-nuevo">Guardar</button>
<a href="listar.php" class="btn btn-volver">Cancelar</a>
</form>
</div>

</body>
</html>
