<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}
require_once "../../config/conexion.php";

$comidas = $conexion->query("SELECT * FROM comidas");
?>

<h2>Platillos</h2>
<a href="crear.php">➕ Nuevo Platillo</a>

<table border="1" cellpadding="5">
<tr>
    <th>Nombre</th>
    <th>Categoría</th>
    <th>Precio</th>
    <th>Imagen</th>
    <th>Estado</th>
    <th>Fecha</th>
    <th>Acciones</th>
</tr>

<?php while ($c = $comidas->fetch_assoc()): ?>
<tr>
    <td><?= $c['nombre'] ?></td>
    <td><?= $c['categoria'] ?></td>
    <td><?= $c['precio'] ?> Bs</td>
    <td><img src="../../public/img/platos/<?= $c['imagen'] ?>" width="50"></td>
    <td><?= $c['estado'] ?></td>
    <td><?= $c['fecha_creacion'] ?></td>
    <td>
        <a href="cambiar_estado.php?id=<?= $c['id_comida'] ?>">
            <?= $c['estado'] === 'activo' ? 'No disponible' : 'Disponible' ?>
        </a> |
        <a href="eliminar.php?id=<?= $c['id_comida'] ?>"
           onclick="return confirm('¿Eliminar platillo?')">
           Eliminar
        </a>
    </td>
</tr>
<?php endwhile; ?>
</table>

<a href="../dashboard.php">⬅ Volver</a>
