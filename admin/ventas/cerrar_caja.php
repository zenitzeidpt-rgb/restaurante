<?php
session_start();

// 1. CONEXIÓN (Ruta corregida para subir dos niveles)
require_once "../../config/conexion.php";

// 2. SEGURIDAD: Verificar sesión activa
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../index.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$nombre_usuario = $_SESSION['nombre'];

// 3. BUSCAR ARQUEO ABIERTO
$query_arqueo = $conexion->query("SELECT * FROM arqueos WHERE id_usuario = '$id_usuario' AND estado = 'abierto' ORDER BY id_arqueo DESC LIMIT 1");
$arqueo = $query_arqueo->fetch_assoc();

if (!$arqueo) {
    // Si no hay arqueo, simplemente cerramos sesión
    session_destroy();
    header("Location: ../../index.php");
    exit;
}

$id_arqueo = $arqueo['id_arqueo'];
$fecha_apertura = $arqueo['fecha_apertura'];
$monto_inicial = $arqueo['monto_inicial'];

// 4. CALCULAR VENTAS DEL TURNO
$query_ventas = $conexion->query("SELECT SUM(total) as total_ventas FROM ventas 
                                 WHERE id_usuario = '$id_usuario' 
                                 AND fecha >= '$fecha_apertura'");
$datos_ventas = $query_ventas->fetch_assoc();
$total_ventas = $datos_ventas['total_ventas'] ?? 0;

// Lo que el sistema dice que DEBE haber
$total_esperado = $total_ventas + $monto_inicial;

// 5. PROCESAR EL CIERRE (Cuando presionan el botón)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $monto_real = $_POST['monto_real'];
    $diferencia = $monto_real - $total_esperado;
    
    // Actualizamos la tabla de arqueos en la base de datos
    $stmt = $conexion->prepare("UPDATE arqueos SET 
                                    fecha_cierre = NOW(), 
                                    monto_sistema = ?, 
                                    monto_real = ?, 
                                    diferencia = ?, 
                                    estado = 'cerrado' 
                                  WHERE id_arqueo = ?");
    $stmt->bind_param("dddi", $total_esperado, $monto_real, $diferencia, $id_arqueo);
    
    if ($stmt->execute()) {
        session_destroy(); // Ahora sí, destruimos la sesión
        echo "<script>
                alert('Cierre guardado. Diferencia: " . number_format($diferencia, 2) . " Bs.'); 
                window.location='../../index.php';
              </script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre de Caja Obligatorio</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .contenedor { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); width: 100%; max-width: 400px; text-align: center; }
        .user-info { background: #e8f4fd; padding: 10px; border-radius: 8px; margin-bottom: 20px; color: #2c3e50; }
        h2 { color: #d32f2f; margin-bottom: 5px; }
        p { color: #666; font-size: 0.9rem; }
        label { display: block; text-align: left; font-weight: bold; margin-bottom: 5px; color: #333; }
        input[type="number"] { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 6px; font-size: 1.5rem; text-align: center; margin-bottom: 20px; box-sizing: border-box; }
        button { background-color: #d32f2f; color: white; border: none; padding: 15px; width: 100%; border-radius: 6px; font-size: 1.1rem; font-weight: bold; cursor: pointer; transition: 0.3s; }
        button:hover { background-color: #b71c1c; }
    </style>
</head>
<body>

<div class="contenedor">
    <h2>Cierre de Caja</h2>
    <p>Declare el efectivo antes de salir</p>

    <div class="user-info">
        Cajero(a): <strong><?php echo htmlspecialchars($nombre_usuario); ?></strong><br>
        Turno iniciado: <?php echo date("H:i", strtotime($fecha_apertura)); ?>
    </div>

    <form method="POST">
        <label>Total Efectivo Contado (Bs):</label>
        <input type="number" step="0.01" name="monto_real" placeholder="0.00" required autofocus>
        
        <button type="submit">Finalizar Turno y Salir</button>
    </form>
</div>

</body>
</html>