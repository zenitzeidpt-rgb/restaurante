<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../index.php");
    exit;
}

require_once "../../config/conexion.php";

$id_venta = $_GET['id'];

// Datos de la venta
$venta = $conexion->query("
    SELECT v.id_venta, v.fecha, v.total, u.nombre
    FROM ventas v
    JOIN usuarios u ON v.id_usuario = u.id_usuario
    WHERE v.id_venta = $id_venta
")->fetch_assoc();

// Detalle
$detalles = $conexion->query("
    SELECT c.nombre, d.cantidad, d.precio_unitario, d.subtotal
    FROM detalle_venta d
    JOIN comidas c ON d.id_comida = c.id_comida
    WHERE d.id_venta = $id_venta
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura</title>
</head>
<body>

<h2>🧾 FACTURA</h2>

<p><strong>Venta #:</strong> <?= $venta['id_venta'] ?></p>
<p><strong>Fecha:</strong> <?= $venta['fecha'] ?></p>
<p><strong>Atendido por:</strong> <?= $venta['nombre'] ?></p>

<table border="1" cellpadding="5">
<tr>
    <th>Producto</th>
    <th>Cant.</th>
    <th>Precio</th>
    <th>Subtotal</th>
</tr>

<?php while ($d = $detalles->fetch_assoc()): ?>
<tr>
    <td><?= $d['nombre'] ?></td>
    <td><?= $d['cantidad'] ?></td>
    <td><?= number_format($d['precio_unitario'],2) ?> Bs</td>
    <td><?= number_format($d['subtotal'],2) ?> Bs</td>
</tr>
<?php endwhile; ?>

<tr>
    <td colspan="3"><strong>Total</strong></td>
    <td><strong><?= number_format($venta['total'],2) ?> Bs</strong></td>
</tr>
</table>

<br>
<button onclick="window.print()">🖨️ Imprimir</button>
<a href="registrar_venta.php">⬅ Nuevo pedido</a>

</body>
</html>
