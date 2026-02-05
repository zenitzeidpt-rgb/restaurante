<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Restaurante</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* (El estilo que ya tenías antes...) */
        body { font-family: sans-serif; background: #2d3748; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-card { background: white; padding: 30px; border-radius: 10px; width: 350px; text-align: center; }
        .input-group { text-align: left; margin-bottom: 15px; }
        .input-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .btn-login { width: 100%; padding: 10px; background: #3182ce; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .error-msg { color: red; background: #fee2e2; padding: 10px; margin-bottom: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Bienvenido</h2>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="error-msg">Datos incorrectos</div>
        <?php endif; ?>

        <form action="validar_login.php" method="POST">
            <div class="input-group">
                <label>Usuario</label>
                <input type="text" name="usuario" required>
            </div>
            <div class="input-group">
                <label>Contraseña</label>
                <input type="password" name="contraseña" required>
            </div>
            <button type="submit" class="btn-login">Entrar</button>
        </form>
    </div>
</body>
</html>
