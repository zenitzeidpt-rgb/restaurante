<?php
session_start();

// 1. Seguridad
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// 2. Conexión y Cálculos
require_once "../config/conexion.php";

// Ventas de hoy
$totalDia = $conexion->query("
    SELECT IFNULL(SUM(total),0) total 
    FROM ventas 
    WHERE DATE(fecha) = CURDATE()
")->fetch_assoc()['total'];

// Plato estrella
$platoTop = $conexion->query("
    SELECT c.nombre, SUM(d.cantidad) total
    FROM detalle_venta d
    JOIN comidas c ON d.id_comida = c.id_comida
    GROUP BY c.id_comida
    ORDER BY total DESC
    LIMIT 1
")->fetch_assoc();

// Cajero del mes
$cajeroTop = $conexion->query("
    SELECT u.nombre, COUNT(v.id_venta) ventas
    FROM ventas v
    JOIN usuarios u ON v.id_usuario = u.id_usuario
    GROUP BY u.id_usuario
    ORDER BY ventas DESC
    LIMIT 1
")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador - Restaurante</title>
    <link rel="stylesheet" href="../public/css/estilo.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .resumen-metricas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .metrica {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 12px;
            border-left: 5px solid #fff;
        }
        .metrica h3 { font-size: 14px; text-transform: uppercase; opacity: 0.8; }
        .metrica p { font-size: 24px; font-weight: bold; margin-top: 5px; }
    </style>
</head>

<body class="dashboard-body">

<div class="dashboard-container">
    <header style="margin-bottom: 30px;">
        <h1>Bienvenido, <?= htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['usuario']) ?></h1>
        <p>Estado actual del restaurante al día de hoy</p>
    </header>

    <div class="resumen-metricas">
        <div class="metrica">
            <h3>💰 Ventas de Hoy</h3>
            <p><?= number_format($totalDia, 2) ?> Bs</p>
        </div>
        <div class="metrica">
            <h3>🍽 Plato Estrella</h3>
            <p><?= $platoTop['nombre'] ?? 'Sin datos' ?></p>
        </div>
        <div class="metrica">
            <h3>👤 Cajero Líder</h3>
            <p><?= $cajeroTop['nombre'] ?? 'Sin datos' ?></p>
        </div>
    </div>

    <hr style="opacity: 0.2; margin-bottom: 30px;">

    <h2 style="color: white; margin-bottom: 20px;">Gestión del Sistema</h2>
    <div class="cards">
        <a href="usuarios/listar.php" class="card">
            <span>👤</span>
            <p>Usuarios</p>
        </a>

        <a href="productos/listar.php" class="card">
            <span>🍽️</span>
            <p>Platillos</p>
        </a>

        <a href="ventas/reporte_diario.php" class="card">
            <span>📊</span>
            <p>Ventas</p>
        </a>

        <a href="estadisticas/index.php" class="card">
            <span>📈</span>
            <p>Estadísticas</p>
        </a>

        <a href="../logout.php" class="card salir">
            <span>🚪</span>
            <p>Cerrar Sesión</p>
        </a>
    </div>
</div>

</body>
</html>