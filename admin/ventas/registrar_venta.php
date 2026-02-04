<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'caja') {
    header("Location: ../../index.php");
    exit;
}

require_once "../../config/conexion.php";

// Obtener platos activos ordenados por categoría
$platos = $conexion->query("
    SELECT id_comida, nombre, precio, imagen, categoria
    FROM comidas
    WHERE estado = 'disponible'
    ORDER BY FIELD(categoria, 'sopa', 'segundo', 'bebida', 'postre'), nombre
");

// Procesar pedido
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_usuario = $_SESSION['id_usuario'];
    $total = 0;
    $detalles = [];

    foreach ($_POST['cantidad'] as $id_comida => $cantidad) {
        if ($cantidad > 0) {

            $res = $conexion->query(
                "SELECT nombre, precio FROM comidas WHERE id_comida = $id_comida"
            );
            $fila = $res->fetch_assoc();

            $subtotal = $fila['precio'] * $cantidad;
            $total += $subtotal;

            $detalles[] = [
                'id_comida' => $id_comida,
                'nombre' => $fila['nombre'],
                'precio' => $fila['precio'],
                'cantidad' => $cantidad,
                'subtotal' => $subtotal
            ];
        }
    }

    if ($total > 0) {

        // Guardar venta
        $conexion->query("
            INSERT INTO ventas (fecha, id_usuario, total, metodo_pago)
            VALUES (NOW(), $id_usuario, $total, 'efectivo')
        ");

        $id_venta = $conexion->insert_id;

        // Guardar detalle
        foreach ($detalles as $d) {
            $conexion->query("
                INSERT INTO detalle_venta
                (id_venta, id_comida, cantidad, precio_unitario, subtotal)
                VALUES
                ($id_venta, {$d['id_comida']}, {$d['cantidad']}, {$d['precio']}, {$d['subtotal']})
            ");
        }

        header("Location: factura.php?id=$id_venta");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Caja</title>
</head>
<body>

<h2>📋 Pedido</h2>

<a href="http://localhost/restaurante/admin/ventas/cerrar_caja.php" style="display:inline-block;margin-bottom:15px;">
    🚪 Cerrar sesión
</a>


<form method="POST">
<?php
$categoria_actual = "";

while ($p = $platos->fetch_assoc()):
    if ($categoria_actual !== $p['categoria']):
        $categoria_actual = $p['categoria'];
?>
        <h3><?= strtoupper($categoria_actual) ?></h3>
<?php endif; ?>

    <div>
        <img src="../../public/img/platos/<?= $p['imagen'] ?>" width="70">
        <strong><?= $p['nombre'] ?></strong>
        (<?= number_format($p['precio'],2) ?> Bs)
        <input type="number" name="cantidad[<?= $p['id_comida'] ?>]" min="0" value="0">
    </div>

<?php endwhile; ?>

<br>
<button type="submit">🧾 Pedido completo</button>
</form>




</body>
</html>
