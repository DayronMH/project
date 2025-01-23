<?php
session_start();
require_once 'targetPage.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyTrees</title>
    <link rel="stylesheet" href="http://mytrees.com/public/login.css?v=1.0">
</head>
<body>
    
    <div class="leaf"><span>🍂</span></div>
    <div class="leaf"><span>🍁</span></div>
    <div class="leaf"><span>🍂</span></div>
    <div class="leaf"><span>🍁</span></div>
    <div class="leaf"><span>🍂</span></div>
    <div class="leaf"><span>🍁</span></div>
    <div class="leaf"><span>🍂</span></div>
    <div class="leaf"><span>🍁</span></div>
    <div class="leaf"><span>🍂</span></div>
    <div class="leaf"><span>🍁</span></div>

    <div class="login-container">
        <div class="left-section">
            <div class="tree-icon">
                <div class="tree-circle">
                    <span>🌳</span>
                </div>
            </div>
            <h1>MyTrees</h1>
        </div>
        <div class="right-section">
            <h1>Bienvenido</h1>
            <form method="POST" action="http://mytrees.com/app/controllers/loginController.php">
                <input type="text" name="email" placeholder="Usuario">
                <input type="password" name="password" placeholder="Contraseña">
                <button type="submit" name="action" value="login">Iniciar sesión</button>
                <p>¿No tienes usuario?</p>
                <button type="submit" name="action" value="register" class="register-button">Registrarse</button>
            </form>
        </div>
    </div>
</body>
</html>