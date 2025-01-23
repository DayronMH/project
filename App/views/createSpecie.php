<?php
session_start();

require_once '../controllers/adminDashboardController.php';
require_once '../controllers/crudController.php';

// Instantiate controllers and models for handling operations
$controller = new AdminDashboardController();
$crud = new crudController();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="http://mytrees.com/public/edit.css">
    <title>Crear Nueva Especie</title>
</head>
<body>
    <div class="container">
        <div class="edit-header">
            <h1>Crear Nueva Especie</h1>
            <a href="adminDashboard.php" class="back-button">← Volver al Dashboard</a>
        </div>
        <div class="form-species">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="commercial_name">Nombre Comercial:</label>
                    <input
                        type="text"
                        id="commercial_name"
                        name="commercial_name"
                        required
                        class="form-input"
                        placeholder="Ingrese el nombre comercial"
                        value="<?php echo isset($_POST['commercial_name']) ? htmlspecialchars($_POST['commercial_name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="scientific_name">Nombre Científico:</label>
                    <input
                        type="text"
                        id="scientific_name"
                        name="scientific_name"
                        required
                        class="form-input"
                        placeholder="Ingrese el nombre científico"
                        value="<?php echo isset($_POST['scientific_name']) ? htmlspecialchars($_POST['scientific_name']) : ''; ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" name="action" value="createSpecies" class="submit-button">
                        Crear Especie
                    </button>
                    <a href="adminDashboard.php" class="cancel-button">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>