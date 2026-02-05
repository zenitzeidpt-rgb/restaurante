<?php
session_start();

// 1. Verificación de seguridad: Solo admin puede gestionar el menú
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header("Location: ../../index.php");
    exit;
}

require_once "../../config/conexion.php";

// 2. Consulta a la base de datos
$comidas = $conexion->query("SELECT * FROM comidas ORDER BY categoria, nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Platillos</title>
    <link rel="stylesheet" href="../../public/css/estilo.css">
</head>
<body>

<div class="contenedor">
    <h2>🍽️ Gestión de Platillos</h2>

    <div style="margin-bottom: 20px;">
        <a href="crear.php" class="btn btn-nuevo">➕ Nuevo Platillo</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Imagen</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($comidas->num_rows > 0): ?>
            <?php while ($c = $comidas->fetch_assoc()): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($c['nombre']) ?></strong></td>
                    <td><?= ucfirst(htmlspecialchars($c['categoria'])) ?></td>
                    <td><?= number_format($c['precio'], 2) ?> Bs</td>
                    <td>
                        <?php 
                        $ruta_img = "../../public/img/platos/" . $c['imagen'];
                        $imagen = (!empty($c['imagen']) && file_exists($ruta_img)) ? $ruta_img : "../../public/img/platos/default.png";
                        ?>
                        <img class="img-plato" src="<?= $imagen ?>" alt="Plato">
                    </td>
                    <td>
                        <span class="badge"><?= ucfirst($c['estado']) ?></span>
                    </td>
                    <td>
                        <a class="btn btn-estado" href="cambiar_estado.php?id=<?= $c['id_comida'] ?>">
                            🔄 Estado
                        </a>
                        <a class="btn btn-eliminar" href="eliminar.php?id=<?= $c['id_comida'] ?>" 
                           onclick="return confirm('¿Estás seguro de eliminar este platillo?')">
                            ❌ Eliminar
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No hay platillos registrados en el menú.</td>
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