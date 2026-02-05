<?php
session_start();

// 1. Seguridad: Solo el administrador puede ver los reportes financieros
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

require_once "../../config/conexion.php";

// 2. Consulta con JOIN para obtener el nombre del cajero en lugar de solo su ID
$ventas = $conexion->query("
    SELECT v.id_venta, v.fecha, u.nombre as cajero, v.total 
    FROM ventas v
    JOIN usuarios u ON v.id_usuario = u.id_usuario
    ORDER BY v.fecha DESC
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
    <link rel="stylesheet" href="../../public/css/estilo.css">
</head>
<body>

<div class="contenedor">
    <h2>📊 Reporte General de Ventas</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha y Hora</th>
                <th>Cajero / Atendido por</th>
                <th>Total cobrado</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($ventas->num_rows > 0): ?>
            <?php while ($v = $ventas->fetch_assoc()): ?>
                <tr>
                    <td><strong>#<?= $v['id_venta'] ?></strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($v['fecha'])) ?></td>
                    <td><?= htmlspecialchars($v['cajero']) ?></td>
                    <td>
                        <span style="color: var(--verde); font-weight: bold;">
                            <?= number_format($v['total'], 2) ?> Bs
                        </span>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No se han registrado ventas todavía.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <a href="../dashboard.php" class="btn btn-volver">⬅ Volver al Panel</a>
    </div>
</div>

</body>
</html>