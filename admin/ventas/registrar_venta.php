<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'caja') {
    header("Location: ../../index.php");
    exit;
}

require_once "../../config/conexion.php";

// Obtener platos activos ordenados por categoría
$platos = $conexion->query("
    SELECT id_comida, nombre, precio, imagen, categoria
    FROM comidas
    WHERE estado = 'disponible'
    ORDER BY FIELD(categoria, 'sopa', 'segundo', 'bebida', 'postre'), nombre
");

// Procesar pedido
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id_usuario = $_SESSION['id_usuario'];
    $total = 0;
    $detalles = [];

    foreach ($_POST['cantidad'] as $id_comida => $cantidad) {
        if ($cantidad > 0) {
            $id_comida = (int)$id_comida; // Seguridad
            $res = $conexion->query(
                "SELECT nombre, precio FROM comidas WHERE id_comida = $id_comida"
            );
            $fila = $res->fetch_assoc();

            $subtotal = $fila['precio'] * $cantidad;
            $total += $subtotal;

            $detalles[] = [
                'id_comida' => $id_comida,
                'nombre' => $fila['nombre'],
                'precio' => $fila['precio'],
                'cantidad' => $cantidad,
                'subtotal' => $subtotal
            ];
        }
    }

    if ($total > 0) {
        // Guardar venta
        $conexion->query("
            INSERT INTO ventas (fecha, id_usuario, total, metodo_pago)
            VALUES (NOW(), $id_usuario, $total, 'efectivo')
        ");

        $id_venta = $conexion->insert_id;

        // Guardar detalle
        foreach ($detalles as $d) {
            $conexion->query("
                INSERT INTO detalle_venta
                (id_venta, id_comida, cantidad, precio_unitario, subtotal)
                VALUES
                ($id_venta, {$d['id_comida']}, {$d['cantidad']}, {$d['precio']}, {$d['subtotal']})
            ");
        }

        header("Location: factura.php?id=$id_venta");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Caja - Pedidos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: auto; }
        
        .header { display: flex; justify-content: space-between; align-items: center; background: white; padding: 15px 25px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        
        .caja-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); 
            gap: 20px; 
        }

        .categoria-titulo { 
            grid-column: 1 / -1; 
            background: #2d3748; 
            color: white; 
            padding: 10px 15px; 
            border-radius: 5px; 
            margin-top: 20px;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .plato { 
            background: white; 
            border-radius: 12px; 
            padding: 15px; 
            text-align: center; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: 0.3s;
            border: 1px solid #e2e8f0;
        }
        
        .plato:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.1); }

        .plato img { width: 100%; height: 140px; object-fit: cover; border-radius: 8px; margin-bottom: 10px; }
        
        .plato strong { display: block; color: #2d3748; margin-bottom: 5px; font-size: 1.1rem; }
        
        .plato span { color: #3182ce; font-weight: bold; font-size: 1rem; }

        .cantidad-control { margin-top: 15px; display: flex; justify-content: center; align-items: center; gap: 10px; }
        
        .cantidad-control input { width: 60px; padding: 8px; border: 1px solid #cbd5e0; border-radius: 5px; text-align: center; font-weight: bold; }

        .btn-submit { 
            position: fixed; bottom: 30px; right: 30px; 
            background: #38a169; color: white; border: none; 
            padding: 15px 40px; border-radius: 50px; font-size: 1.2rem; 
            font-weight: bold; cursor: pointer; box-shadow: 0 10px 20px rgba(56, 161, 105, 0.3);
            transition: 0.3s;
        }
        .btn-submit:hover { background: #2f855a; transform: scale(1.05); }
        
        .logout-link { text-decoration: none; color: #e53e3e; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2><i class="fas fa-clipboard-list"></i> Nuevo Pedido</h2>
        <a href="http://localhost/restaurante/admin/ventas/cerrar_caja.php" class="logout-link">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
    </div>

    <form method="POST">
        <div class="caja-grid">
            <?php 
            $categoria_actual = "";
            while ($p = $platos->fetch_assoc()): 
                if ($categoria_actual !== $p['categoria']): 
                    $categoria_actual = $p['categoria'];
            ?>
                <div class="categoria-titulo"><?= $categoria_actual ?></div>
            <?php endif; ?>

                <div class="plato">
                    <img src="../../public/img/platos/<?= $p['imagen'] ?>" alt="<?= $p['nombre'] ?>">
                    <strong><?= $p['nombre'] ?></strong>
                    <span><?= number_format($p['precio'], 2) ?> Bs</span>
                    
                    <div class="cantidad-control">
                        <label for="cant_<?= $p['id_comida'] ?>">Cant:</label>
                        <input type="number" 
                               id="cant_<?= $p['id_comida'] ?>"
                               name="cantidad[<?= $p['id_comida'] ?>]" 
                               min="0" 
                               value="0">
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <button type="submit" class="btn-submit">
            <i class="fas fa-check-circle"></i> Registrar Venta
        </button>
    </form>
</div>

</body>
</html>
