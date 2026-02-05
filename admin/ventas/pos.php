<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'caja') {
    header("Location: ../../index.php");
    exit;
}

require_once "../../config/conexion.php";

$comidas = $conexion->query("
    SELECT * FROM comidas 
    WHERE estado='activo'
    ORDER BY categoria
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>POS</title>
<link rel="stylesheet" href="../../public/css/estilo.css">
</head>

<body>

<h2>🧾 Caja</h2>

<form method="POST">
<div class="pos-grid">
<?php while ($c = $comidas->fetch_assoc()): ?>
    <div class="pos-card">
        <img src="../../public/img/platos/<?= $c['imagen'] ?>">
        <h4><?= $c['nombre'] ?></h4>
        <p><?= $c['precio'] ?> Bs</p>
        <input type="number" name="cantidad[<?= $c['id_comida'] ?>]" min="0" value="0">
    </div>
<?php endwhile; ?>
</div>

<button class="btn btn-nuevo">Registrar Pedido</button>
</form>

<a href="cerrar_caja.php" class="btn btn-eliminar">Cerrar Caja</a>

</body>
</html>
