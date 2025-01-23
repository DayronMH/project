<?php
session_start();
require_once 'targetPage.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - MyTrees</title>
    <link rel="stylesheet" href="http://mytrees.com/public/register.css?v=1.0">
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
        <div class="tree-icon">
        </div>
        <h2>Registro de Usuario</h2>
        <form method="POST" action="http://mytrees.com/app/controllers/registerController.php">
            <input type="text" name="name" placeholder="Nombre completo" >
            <input type="email" name="email" placeholder="Email" >
            <input type="password" name="password" placeholder="Contraseña">
            <input type="text" name="phone" placeholder="Número de teléfono">
            <input type="text" name="address" placeholder="Dirección">
            <input type="text" name="country" placeholder="Pais">
            <button type="submit" name="action" value="register">Registrar</button>
            <button type="submit" name="action" value="login" class="register-button">Atrás</button>
        </form>
    </div>
</body>
</html>