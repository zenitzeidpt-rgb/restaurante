<?php
session_start();
require_once "config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$usuario = $_POST['usuario'];
$password = $_POST['password'];

$sql = "SELECT id_usuario, usuario, contraseña, rol, nombre 
        FROM usuarios 
        WHERE usuario = ? AND estado = 'activo'";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    if ($password === $user['contraseña']) {

        // 1. Guardamos sesión
        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['usuario']    = $user['usuario'];
        $_SESSION['rol']        = $user['rol'];
        $_SESSION['nombre']     = $user['nombre']; // Asegúrate de guardar el nombre para el saludo

        // 2. SI ES CAJERO, ABRIMOS LA CAJA AUTOMÁTICAMENTE
        if ($user['rol'] === 'caja') {
            $id_user = $user['id_usuario'];
            
            // Verificamos si ya tiene una caja abierta para no duplicar
            $verificar = $conexion->query("SELECT id_arqueo FROM arqueos WHERE id_usuario = '$id_user' AND estado = 'abierto'");
            
            if ($verificar->num_rows == 0) {
                // Si no hay ninguna abierta, la creamos con 0 de monto inicial
                $conexion->query("INSERT INTO arqueos (id_usuario, fecha_apertura, monto_inicial, estado) 
                                 VALUES ('$id_user', NOW(), 0, 'abierto')");
            }
            
            header("Location: admin/ventas/registrar_venta.php");
        } elseif ($user['rol'] === 'admin') {
            header("Location: admin/dashboard.php");
        }
        exit;
    }
}

header("Location: index.php?error=1");
exit;