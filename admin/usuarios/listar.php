<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}
require_once "../../config/conexion.php";

$usuarios = $conexion->query("SELECT * FROM usuarios");
?>

<h2>Usuarios</h2>
<a href="crear.php">➕ Nuevo Usuario</a>

<table border="1" cellpadding="5">
<tr>
    <th>Nombre</th>
    <th>Usuario</th>
    <th>Rol</th>
    <th>Estado</th>
    <th>Fecha</th>
    <th>Acciones</th>
</tr>

<?php while ($u = $usuarios->fetch_assoc()): ?>
<tr>
    <td><?= $u['nombre'] ?></td>
    <td><?= $u['usuario'] ?></td>
    <td><?= $u['rol'] ?></td>
    <td><?= $u['estado'] ?></td>
    <td><?= $u['fecha_creacion'] ?></td>
    <td>
        <a href="cambiar_estado.php?id=<?= $u['id_usuario'] ?>">
            <?= $u['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?>
        </a> |
        <a href="eliminar.php?id=<?= $u['id_usuario'] ?>" 
           onclick="return confirm('¿Eliminar usuario?')">
           Eliminar
        </a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<a href="../dashboard.php">⬅ Volver</a>

