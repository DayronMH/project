<?php
session_start();
require_once '../controllers/adminDashboardController.php';
require_once 'targetPage.php';
require_once '../models/treesModel.php';
require_once '../controllers/crudController.php';
require_once '../models/speciesModel.php';

// Instantiate controllers and models for handling operations
$controller = new AdminDashboardController();
$crud = new crudController();
$speciesModel = new speciesModel();
$trees = new TreesModel();

if (isset($_GET['id'])) {
    $treeId = htmlspecialchars($_GET['id'], ENT_QUOTES);
} else {
    setTargetMessage("error","ID no encontrada");
    header('Location: ../views/trees.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['action'])) {
        setTargetMessage('error', "Acción no especificada");
        header("Location: trees.php");
        exit();
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

    <title>Actualizar Nuevo Arbol</title>
</head>

<body>
    <div class="container">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-tree">
                <div class="form-group"></div>
                <div class="edit-header">
                    <h1>Actualizar ÁRBOL</h1>
                    <a href="trees.php" class="back-button">← VOLVER AL DASHBOARD</a>
                </div>

                <div class="form-trees">
                    <form method="POST" action="" enctype="multipart/form-data">

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
                        <div class="form-tree">
                            <label for="treePic">Tree Picture</label>
                            <input type="file" class="form-control" name="treeUpd" id="treeUpd">
                        </div>


                        <div class="form-actions">
                            <button type="submit" name="action" value="updateTrees" class="submit-button">
                                Actualizar
                            </button>

                            <button type="button" class="cancel-button" onclick="window.location.href='trees.php'">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
    </div>
</body>

</html>