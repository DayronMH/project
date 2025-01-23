<?php
session_start();
require_once '../controllers/adminDashboardController.php';
require_once './targetPage.php';
require_once '../controllers/crudController.php';
require_once '../models/speciesModel.php';

// Instantiate controllers and models for handling operations
$controller = new AdminDashboardController();
$crud = new crudController();
$speciesModel = new speciesModel();
$species = $speciesModel->getAllSpecies();


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="http://mytrees.com/public/edit.css">

    <title>Crear Nuevo Arbol</title>
</head>

<body>
    <div class="container">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="edit-header">
                <h1>Crear Nuevo Arbol</h1>
                <a href="adminDashboard.php" class="back-button">← Volver al Dashboard</a>
            </div>
            <div class="form-species">
                <div class="form-group">
                    <label for="location">Ubicacion Geografica:</label>
                    <input
                        type="text"
                        id="location"
                        name="location"
                        class="form-input"
                        placeholder="Ingrese la ubicacion"
                        value="<?php echo isset($_POST['']) ? htmlspecialchars($_POST['location']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="price">Precio:</label>
                    <input
                        type="number"
                        id="price"
                        name="price"
                        class="form-input"
                        placeholder="Ingrese el precio"
                        value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="specie">Especie:</label>
                    <?php foreach ($species as $specie): ?>
                        <div>
                            <label for="species_<?php echo htmlspecialchars($specie['id']); ?>">
                                <?php echo htmlspecialchars($specie['commercial_name']); ?>
                            </label>
                            <input type="radio"
                                id="species_<?php echo htmlspecialchars($specie['id']); ?>"
                                name="species_id"
                                value="<?php echo htmlspecialchars($specie['id']); ?>"
                                required>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-tree">
                    <label for="treePic">Tree Picture</label>
                    <input type="file" class="form-control" name="treepic" id="treepic">
                </div>

                <div class="form-actions">
                    <button type="submit" name="action" value="createTrees" class="submit-button">
                        Crear Arbol
                    </button>
                    <a href="adminDashboard.php" class="cancel-button">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</body>