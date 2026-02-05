<?php
session_start();

// 1. Verificación de seguridad
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

// 2. Conexión y consulta
require_once "../../config/conexion.php";
$usuarios = $conexion->query("SELECT * FROM usuarios ORDER BY fecha_creacion DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="../../public/css/estilo.css">
</head>
<body>

<div class="contenedor">
    <h2>👤 Gestión de Usuarios</h2>

    <div style="margin-bottom: 20px;">
        <a href="crear.php" class="btn btn-nuevo">➕ Nuevo Usuario</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($usuarios->num_rows > 0): ?>
            <?php while ($u = $usuarios->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><?= htmlspecialchars($u['usuario']) ?></td>
                    <td>
                        <span class="badge"><?= ucfirst($u['rol']) ?></span>
                    </td>
                    <td>
                        <strong><?= ucfirst($u['estado']) ?></strong>
                    </td>
                    <td><?= date('d/m/Y', strtotime($u['fecha_creacion'])) ?></td>
                    <td>
                        <a class="btn btn-estado" href="cambiar_estado.php?id=<?= $u['id_usuario'] ?>">
                            🔄 Estado
                        </a>
                        <a class="btn btn-eliminar" href="eliminar.php?id=<?= $u['id_usuario'] ?>" 
                           onclick="return confirm('¿Estás seguro de eliminar a este usuario?')">
                            ❌ Eliminar
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No hay usuarios registrados.</td>
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