<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}
require_once "../../config/conexion.php";

if ($_POST) {
    $nombre = $_POST['nombre'];
    $categoria = $_POST['categoria'];
    $precio = $_POST['precio'];

    $imagen = $_FILES['imagen']['name'];
    move_uploaded_file(
        $_FILES['imagen']['tmp_name'],
        "../../public/img/platos/" . $imagen
    );

    $conexion->query("
        INSERT INTO comidas 
        (nombre, categoria, precio, imagen, estado, fecha_creacion)
        VALUES 
        ('$nombre','$categoria','$precio','$imagen','activo',NOW())
    ");

    header("Location: listar.php");
}
?>

<h2>Nuevo Platillo</h2>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="nombre" placeholder="Nombre" required><br>

    <select name="categoria">
        <option value="sopa">Sopa</option>
        <option value="segundo">Segundo</option>
        <option value="bebida">Bebida</option>
        <option value="postre">Postre</option>
    </select><br>

    <input type="number" step="0.01" name="precio" placeholder="Precio" required><br>
    <input type="file" name="imagen" required><br>

    <button type="submit">Guardar</button>
</form>

<a href="listar.php">⬅ Volver</a>
