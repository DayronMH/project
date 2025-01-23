<?php
session_start();
require_once 'targetPage.php';
require_once '../controllers/adminDashboardController.php';
require_once '../controllers/crudController.php';
require_once '../models/speciesModel.php';

// Instantiate controllers and models for handling operations
$crud = new CrudController();

// Validate species ID from GET request
$speciesId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$speciesId) {
    setTargetMessage('error', "ID de especie inválido");
    header("Location: adminDashboard.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['action'])) {
        $_SESSION['error'] = "Acción no especificada";
        header("Location: adminDashboard.php");
        exit();
    }

    $speciesId = filter_input(INPUT_POST, 'species_id', FILTER_VALIDATE_INT);

    switch ($_POST['action']) {
        case 'delete_species':
            $result = $crud->deleteSpecie($speciesId);
            if (isset($result['success'])) {
                setTargetMessage('success', "Especie actualizada");
                
            } elseif (isset($result['error'])) {
                setTargetMessage('error', $result['error']);
            }
            break;

        case 'updateSpecies':
            $commercialName = trim($_POST['commercial_name']);
            $scientificName = trim($_POST['scientific_name']);

            $species = new SpeciesModel();
            if ($species->editSpecies($speciesId, $commercialName, $scientificName)) {
                
                setTargetMessage('success', "Especie actualizada");
                
            } else {
                setTargetMessage('error', "Error al actualizar la especie");
            }
            break;

        default:
        setTargetMessage('error', "Accion no valida");
        
    }
}

$currentSpecies = $crud->getSpecieById($speciesId);
if (!$currentSpecies) {
    $_SESSION['error'] = "No se encontró la especie especificada";
    header("Location: adminDashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="http://mytrees.com/public/edit.css?v=1.0">
    <title>Editar Especie</title>
</head>
<body>
    <div class="container">
        <div class="edit-header">
            <h1>Editar Especie</h1>
            <a href="adminDashboard.php" class="back-button">← Volver al Dashboard</a>
        </div>

        <div class="form-species">
            <form method="POST" onsubmit="return validateForm(this);">
                <input type="hidden" name="species_id" value="<?php echo htmlspecialchars($speciesId); ?>">

                <div class="form-group">
                    <label for="commercial_name">Nombre Comercial:</label>
                    <input
                        type="text"
                        id="commercial_name"
                        name="commercial_name"
                        value="<?php echo htmlspecialchars($currentSpecies['commercial_name']); ?>"
                        required
                        class="form-input">
                </div>

                <div class="form-group">
                    <label for="scientific_name">Nombre Científico:</label>
                    <input
                        type="text"
                        id="scientific_name"
                        name="scientific_name"
                        value="<?php echo htmlspecialchars($currentSpecies['scientific_name']); ?>"
                        required
                        class="form-input">
                </div>

                <div class="form-actions">
                    <button type="submit" name="action" value="updateSpecies" class="submit-button">
                        Actualizar Especie
                    </button>
                    <button type="button" class="cancel-button" onclick="window.location.href='adminDashboard.php'">
                        Cancelar
                    </button>
                    <button type="submit" name="action" value="delete_species" class="delete-btn">
                        Eliminar
                    </button>
                </div>
            </form>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
    setTimeout(function() {
        var errorMessage = document.querySelector('.error-message');
        var successMessage = document.querySelector('.success-message');
        if (errorMessage) {
            errorMessage.style.display = 'none';
        }
        if (successMessage) {
            successMessage.style.display = 'none';
        }
    }, 3000);
</script>

    <script>
        function validateForm(form) {
            if (form.action.value === 'delete_species') {
                return confirm('¿Estás seguro de eliminar esta especie?');
            }
            return true;
        }
    </script>
</body>
</html>