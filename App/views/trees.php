<?php
session_start();
require_once '../controllers/friendDashboardController.php';
require_once '../models/treesModel.php';
require_once '../controllers/salesController.php';
require_once '../controllers/treeController.php';
require_once 'targetPage.php';
// Instantiate controllers and models for handling operations
$friendDashboardController = new FriendDashboardController();
$salesController = new SalesController();
$treeController = new TreeController();
$userId = $_SESSION['user_id'] ?? null;
$friend_id = $_GET['friend_id'] ?? null;
$treesModel = new TreesModel();

if (is_null($friend_id)) {
    header('Location: adminDashboard.php');
    exit;
}

$trees = $treesModel->getTreesByOwner($friend_id);

if (empty($trees)) {
    setTargetMessage("error","Este usuario no posee árboles");
    header('Location: adminDashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/tree.css?v=1.0">
    <title>Árboles de <?php echo htmlspecialchars($trees[0]['owner_name'] ?? ''); ?></title>
</head>

<body>
    <div class="container">
        <div class="view-header">
            <h1>Árboles de <?php echo htmlspecialchars($trees[0]['owner_name'] ?? ''); ?></h1>
            <div class="header-buttons">
                <a href="adminDashboard.php" class="back-button">Volver al Dashboard</a>
            </div>
        </div>

        <div class="trees-container">
            <?php foreach ($trees as $tree):
                $treeId = $tree['id']; ?>

                <div class="tree-card">
                    <?php if (!empty($tree['photo_url'])): ?>
                        <img src="<?php echo htmlspecialchars($tree['photo_url']); ?>" alt="Árbol" class="tree-image">
                    <?php endif; ?>

                    <div class="tree-info">
                        <span class="tree-name">
                            <?php echo htmlspecialchars($tree['commercial_name'] ?? 'Nombre no disponible'); ?>
                        </span>

                        <div class="tree-details">
                            <div class="detail-item">
                                <span class="detail-label">ID:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($tree['id']); ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">Altura:</span>
                                <span class="detail-value">
                                    <?php echo htmlspecialchars($tree['height'] ?? '0'); ?> metros
                                </span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">Ubicación:</span>
                                <span class="detail-value">
                                    <?php echo htmlspecialchars($tree['location'] ?? 'No especificada'); ?>
                                </span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">Precio:</span>
                                <span class="detail-value">
                                    $<?php echo htmlspecialchars($tree['price'] ?? '0'); ?>
                                </span>
                            </div>
                        </div>

                        <div class="trees-actions">
                            <button onclick="window.location.href='../views/editTrees.php?id=<?php echo htmlspecialchars($tree['id'], ENT_QUOTES); ?>'"
                                action="editTree"
                                class="edit-tree">
                                Editar
                            </button>
                            <button onclick="window.location.href='../views/updateTree.php?id=<?php echo htmlspecialchars($tree['id'], ENT_QUOTES); ?>'"
                                action="updateTree"
                                class="update-tree">
                                Actualizar
                            </button>


                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
                    </div>
        
    </div>
</body>

</html>