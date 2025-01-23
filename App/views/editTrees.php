<?php
session_start();
require_once '../models/treesModel.php';
require_once './targetPage.php';
// Instantiate controllers and models for handling operations
$trees = new TreesModel();

if (isset($_GET['id'])) {
    $treeId = htmlspecialchars($_GET['id'], ENT_QUOTES);
} else {
    header('Location: ../views/trees.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['action'])) {
        setTargetMessage('error', "Acción no especificada");
        header("Location: trees.php");
        exit();
    }
    if ($_POST['action'] === 'edit_tree') {
        $height = filter_input(INPUT_POST, 'height', FILTER_SANITIZE_STRING);
        $specie = filter_input(INPUT_POST, 'specie', FILTER_SANITIZE_STRING);
        $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
        $available = isset($_POST['available']) && $_POST['available'] === '1' ? 1 : 0;

        $result = $trees->editTree($treeId, $specie,$height, $location, $available);

        if ($result) {
            setTargetMessage('success', "Árbol editado correctamente");
            header("Location: trees.php");
            exit();
        } else {
            setTargetMessage('error', "Ocurrió un error al editar el árbol");
        }
    }
}
$tree = $trees->getTreeById($treeId);
if (!$tree) {
    setTargetMessage('error', "No encontramos el árbol");
    header("Location: trees.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="http://mytrees.com/public/edit.css">
    <title>Editar Árbol</title>
</head>

<body>
    <div class="container">
        <div class="edit-header">
            <h1>EDITAR ÁRBOL</h1>
            <a href="trees.php" class="back-button">← VOLVER AL DASHBOARD</a>
        </div>

        <div class="form-trees">
            <form method="POST">
                <input type="hidden" name="tree_id" value="<?php echo htmlspecialchars($treeId); ?>">

                <div class="form-group">
                    <label for="height">Altura:</label>
                    <input
                        type="text"
                        id="height"
                        name="height"
                        value="<?php echo isset($tree[0]['height']) ? htmlspecialchars($tree[0]['height']) : ''; ?>"
                        required
                        class="form-input">
                </div>

                <div class="form-group">
                    <label for="specie">Especie:</label>
                    <input
                        type="text"
                        id="specie"
                        name="specie"
                        value="<?php echo isset($tree[0]['commercial_name']) ? htmlspecialchars($tree[0]['commercial_name']) : ''; ?>"
                        required
                        class="form-input">
                </div>

                <div class="form-group">
                    <label for="location">Ubicación:</label>
                    <input
                        type="text"
                        id="location"
                        name="location"
                        value="<?php echo isset($tree[0]['location']) ? htmlspecialchars($tree[0]['location']) : ''; ?>"
                        required
                        class="form-input">
                </div>

                <div class="mydict">
                    <div>
                        <label>
                            <input type="radio" name="available" value="1" <?php echo (!isset($tree[0]['available']) || $tree[0]['available'] == 1) ? 'checked' : ''; ?>>
                            <span>Disponible</span>
                        </label>
                        <label>
                            <input type="radio" name="available" value="0" <?php echo (isset($tree[0]['available']) && $tree[0]['available'] == 0) ? 'checked' : ''; ?>>
                            <span>Vendido</span>
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" name="action" value="edit_tree" class="submit-button">
                        Guardar Cambios
                    </button>
                    <button type="button" class="cancel-button" onclick="window.location.href='trees.php'">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>