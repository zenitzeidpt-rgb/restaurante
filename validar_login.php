<?php
session_start();
require_once "config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

// Recibimos los datos del formulario
$usuario = $_POST['usuario'];
$password = $_POST['contraseña']; // <--- CORREGIDO: Ahora coincide con el formulario

$sql = "SELECT id_usuario, usuario, contraseña, rol, nombre 
        FROM usuarios 
        WHERE usuario = ? AND estado = 'activo'";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    // Comparamos la contraseña de la DB con la del formulario
    if ($password === $user['contraseña']) {

        $_SESSION['id_usuario'] = $user['id_usuario'];
        $_SESSION['usuario']    = $user['usuario'];
        $_SESSION['rol']        = $user['rol'];
        $_SESSION['nombre']     = $user['nombre'];

        if ($user['rol'] === 'caja') {
            $id_user = $user['id_usuario'];
            
            $verificar = $conexion->query("SELECT id_arqueo FROM arqueos WHERE id_usuario = '$id_user' AND estado = 'abierto'");
            
            if ($verificar->num_rows == 0) {
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

// Si llegó aquí es porque falló: Redirige con error
header("Location: index.php?error=1");
exit;