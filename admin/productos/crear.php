<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}
require_once "../../config/conexion.php";

if ($_POST) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];

    $imagen = $_FILES['imagen']['name'];
    move_uploaded_file($_FILES['imagen']['tmp_name'],
        "../../public/img/platos/".$imagen);

    $conexion->query("
        INSERT INTO comidas (nombre, categoria, precio, imagen, estado)
        VALUES ('$nombre','$categoria','$precio','$imagen','activo')
    ");

    header("Location: listar.php");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nuevo Platillo</title>
<link rel="stylesheet" href="../../public/css/estilo.css">
</head>

<body class="form-body">

<div class="form-card">
<h2>🍽 Nuevo Platillo</h2>

<form method="POST" enctype="multipart/form-data">
<input type="text" name="nombre" placeholder="Nombre del platillo" required>

<select name="categoria">
    <option value="sopa">Sopa</option>
    <option value="segundo">Segundo</option>
    <option value="bebida">Bebida</option>
    <option value="postre">Postre</option>
</select>

<input type="number" step="0.01" name="precio" placeholder="Precio Bs" required>
<input type="file" name="imagen" required>

<button class="btn btn-nuevo">Guardar</button>
<a href="listar.php" class="btn btn-volver">Cancelar</a>
</form>
</div>

</body>
</html>
