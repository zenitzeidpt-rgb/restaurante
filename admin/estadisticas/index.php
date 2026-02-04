<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

require_once "../../config/conexion.php";

// ===============================
// LÓGICA DEL FILTRO
// ===============================
// Si el usuario elige fechas, las usamos. Si no, usamos el día de hoy.
$fecha_inicio = $_GET['desde'] ?? date('Y-m-d');
$fecha_fin    = $_GET['hasta'] ?? date('Y-m-d');

// Escapamos las fechas para evitar inyecciones (Seguridad básica)
$desde = $conexion->real_escape_string($fecha_inicio);
$hasta = $conexion->real_escape_string($fecha_fin);

// ===============================
// 1. VENTA TOTAL EN EL RANGO
// ===============================
$ventaRango = $conexion->query("
    SELECT IFNULL(SUM(total),0) AS total_periodo
    FROM ventas
    WHERE DATE(fecha) BETWEEN '$desde' AND '$hasta'
")->fetch_assoc();

// ===============================
// 2. PLATO MÁS VENDIDO EN EL RANGO
// ===============================
$platoTop = $conexion->query("
    SELECT c.nombre, SUM(dv.cantidad) AS total_vendidos
    FROM detalle_venta dv
    INNER JOIN comidas c ON dv.id_comida = c.id_comida
    INNER JOIN ventas v ON dv.id_venta = v.id_venta
    WHERE DATE(v.fecha) BETWEEN '$desde' AND '$hasta'
    GROUP BY c.nombre
    ORDER BY total_vendidos DESC
    LIMIT 1
")->fetch_assoc();

// ===============================
// 3. RENDIMIENTO DE CAJEROS EN EL RANGO
// ===============================
$cajeros = $conexion->query("
    SELECT u.nombre,
           COUNT(v.id_venta) AS total_ventas,
           IFNULL(SUM(v.total),0) AS total_bs
    FROM ventas v
    INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
    WHERE DATE(v.fecha) BETWEEN '$desde' AND '$hasta'
    GROUP BY u.nombre
    ORDER BY total_bs DESC
");

// ===============================
// 4. LISTADO DETALLADO DE PLATILLOS
// ===============================
$listaPlatos = $conexion->query("
    SELECT c.nombre, SUM(dv.cantidad) AS cantidad, SUM(dv.cantidad * dv.precio_unitario) as subtotal
    FROM detalle_venta dv
    INNER JOIN comidas c ON dv.id_comida = c.id_comida
    INNER JOIN ventas v ON dv.id_venta = v.id_venta
    WHERE DATE(v.fecha) BETWEEN '$desde' AND '$hasta'
    GROUP BY c.nombre
    ORDER BY cantidad DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <style>
        :root { --primary: #2c3e50; --accent: #27ae60; --bg: #f4f7f6; }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; }
        .buscador { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; display: flex; gap: 15px; align-items: flex-end; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .buscador input, .buscador button { padding: 10px; border-radius: 5px; border: 1px solid #ddd; }
        .buscador button { background: var(--primary); color: white; border: none; cursor: pointer; }
        .buscador button:hover { background: #34495e; }
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .big-number { font-size: 2.5rem; font-weight: bold; color: var(--accent); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #eee; }
        .header-acciones { display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-acciones">
        <h1>📊 Reporte Personalizado</h1>
        <a href="../dashboard.php" style="text-decoration:none; color: #7f8c8d;">⬅ Volver</a>
    </div>

    <form class="buscador" method="GET">
        <div>
            <label>Desde:</label><br>
            <input type="date" name="desde" value="<?= $fecha_inicio ?>">
        </div>
        <div>
            <label>Hasta:</label><br>
            <input type="date" name="hasta" value="<?= $fecha_fin ?>">
        </div>
        <button type="submit">🔍 Filtrar Datos</button>
        <a href="index.php" style="font-size: 12px; color: #e74c3c;">Limpiar</a>
    </form>

    <div class="stats-grid">
        <div class="card">
            <h3>💰 Recaudación Total</h3>
            <div class="big-number"><?= number_format($ventaRango['total_periodo'], 2) ?> Bs</div>
            <p>Del <?= $fecha_inicio ?> al <?= $fecha_fin ?></p>
        </div>

        <div class="card">
            <h3>⭐ Producto más vendido</h3>
            <?php if ($platoTop): ?>
                <div class="big-number" style="font-size: 1.8rem; color: #e67e22;"><?= $platoTop['nombre'] ?></div>
                <p>Vendido <strong><?= $platoTop['total_vendidos'] ?></strong> veces en este periodo</p>
            <?php else: ?>
                <p>No hay datos en estas fechas.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>🍽️ Desglose de Productos Vendidos</h3>
        <table>
            <thead>
                <tr style="background: #f8f9fa;">
                    <th>Nombre del Platillo</th>
                    <th>Cantidad</th>
                    <th>Subtotal (Estimado)</th>
                </tr>
            </thead>
            <tbody>
                <?php while($lp = $listaPlatos->fetch_assoc()): ?>
                <tr>
                    <td><?= $lp['nombre'] ?></td>
                    <td><strong><?= $lp['cantidad'] ?></strong></td>
                    <td><?= number_format($lp['subtotal'], 2) ?> Bs</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="card" style="margin-top: 20px;">
        <h3>👤 Rendimiento por Usuario</h3>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Nro. de Ventas</th>
                    <th>Recaudado</th>
                </tr>
            </thead>
            <tbody>
                <?php while($c = $cajeros->fetch_assoc()): ?>
                <tr>
                    <td><?= $c['nombre'] ?></td>
                    <td><?= $c['total_ventas'] ?> tickets</td>
                    <td><strong><?= number_format($c['total_bs'], 2) ?> Bs</strong></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>