<?php
require_once "../../config/conexion.php";

$consulta = $conexion->query("
    SELECT DATE(fecha) dia, SUM(total) total
    FROM ventas
    GROUP BY DATE(fecha)
    ORDER BY dia
");

$fechas = [];
$totales = [];

while ($row = $consulta->fetch_assoc()) {
    $fechas[] = $row['dia'];
    $totales[] = $row['total'];
}

echo json_encode([
    'fechas' => $fechas,
    'totales' => $totales
]);
