<?php
session_start();
require_once '../controllers/friendDashboardController.php';
require_once '../models/treesModel.php';
require_once '../controllers/salesController.php';
require_once '../controllers/treeController.php';
require_once 'targetPage.php';

if (!isset($_SESSION['user_id'])) {
    setTargetMessage('error', 'User must be');
    header('Location: http://mytrees.com');
    exit();
}
// Instantiate controllers and models for handling operations
$friendDashboardController = new FriendDashboardController();
$salesController = new SalesController();
$treeController = new TreeController();

$userId = $_SESSION['user_id'] ?? null;
$user = $friendDashboardController->getUserById($userId);
$getAvailableTrees = $friendDashboardController->getAvailableTrees();
$getPurchasedTrees = $friendDashboardController->getTreesByOwnerId($user['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'buy_tree' && isset($_POST['tree_id'])) {
    $treeId = (int)$_POST['tree_id'];
    $result = $salesController->createSale($userId, $treeId);

    if ($result) {
        $_SESSION['success'] = "Árbol comprado exitosamente.";
    } else {
        $_SESSION['error'] = "Ocurrió un problema al intentar comprar el árbol.";
    }
    
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/friend.css">
    <title>Amigo - Panel de Control</title>

</head>

<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Bienvenido <?php echo htmlspecialchars($user['name']); ?></h1>
            <a href="login.php" class="back-button">Back</a>
            <hr>
            <h2>Árboles Disponibles</h2>
        </div>
    <div class="trees-container">
            <?php foreach ($getAvailableTrees as $tree): ?>
                <div class="tree-card">
                    <img src="<?php echo htmlspecialchars($tree['photo_url']); ?>" alt="Árbol" class="tree-image">
                    <div class="tree-info">
                        <span class="tree-name"><?php echo htmlspecialchars($tree['commercial_name']); ?></span>
                        <div class="tree-details">
                            <div class="detail-item">
                                <span class="detail-label">ID</span>
                                <span class="detail-value"><?php echo htmlspecialchars($tree['id']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">OwnerId</span>
                                <span class="detail-value"><?php echo htmlspecialchars($tree['owner_id']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Altura</span>
                                <span class="detail-value"><?php echo htmlspecialchars($tree['height']); ?> metros</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Ubicación</span>
                                <span class="detail-value"><?php echo htmlspecialchars($tree['location']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Precio</span>
                                <span class="detail-value">$<?php echo htmlspecialchars($tree['price']); ?></span>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <form method="POST" action="">
                                <input type="hidden" name="tree_id" value="<?php echo htmlspecialchars($tree['id']); ?>">
                                <button type="submit" name="action" value="buy_tree" class="action-button buy-btn">Comprar</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="dashboard-header">
            <h2>Árboles Comprados</h2>
        </div>
        <div class="trees-container">
            <?php foreach ($getPurchasedTrees as $pTree): ?>
                <div class="tree-card">

                    <?php echo $tree['photo_url']; ?>
                    <img src="<?php echo htmlspecialchars($pTree['photo_url']); ?>" alt="Árbol" class="tree-image">
                    <div class="tree-info">
                        <span class="tree-name"><?php echo htmlspecialchars($pTree['commercial_name']); ?></span>
                        <div class="tree-details">
                            <div class="detail-item">
                                <span class="detail-label">ID</span>
                                <span class="detail-value"><?php echo htmlspecialchars($pTree['id']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">OwnerId</span>
                                <span class="detail-value"><?php echo htmlspecialchars($pTree['owner_id']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Altura</span>
                                <span class="detail-value"><?php echo htmlspecialchars($pTree['height']); ?> metros</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Ubicación</span>
                                <span class="detail-value"><?php echo htmlspecialchars($pTree['location']); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Precio</span>
                                <span class="detail-value">$<?php echo htmlspecialchars($pTree['price']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>