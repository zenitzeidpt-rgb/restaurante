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
    $clave = password_hash($_POST['clave'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    $conexion->query("
        INSERT INTO usuarios (nombre, usuario, contraseña, rol, estado, fecha_creacion)
        VALUES ('$nombre', '$usuario', '$clave', '$rol', 'activo', NOW())
    ");

    header("Location: listar.php");
}
?>

<h2>Nuevo Usuario</h2>

<form method="POST">
    <input type="text" name="nombre" placeholder="Nombre" required><br>
    <input type="text" name="usuario" placeholder="Usuario" required><br>
    <input type="password" name="clave" placeholder="Contraseña" required><br>

    <select name="rol">
        <option value="admin">Administrador</option>
        <option value="caja">Caja</option>
    </select><br>

    <button type="submit">Guardar</button>
</form>

<a href="listar.php">⬅ Volver</a>
