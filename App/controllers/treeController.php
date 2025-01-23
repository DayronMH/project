<?php

require_once '../models/TreesModel.php';

class TreeController {

     /**
     * Constructor for the controller
     * 
     * Checks for incoming POST requests and dispatches them to appropriate methods based on the 'action' parameter
     */
    public function __construct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'])) {
                $action = $_POST['action'];
                switch ($action) {
                    case 'buy_tree':
                        $this->buyTree();
                        break;
                    default:
                        break;
                }
            }
        }
    }

     /**
     * Handles the purchase of a tree
     *  
     * @return void
     */
    public function buyTree() {
        $treeId = filter_input(INPUT_POST, 'tree_id', FILTER_VALIDATE_INT);
        $userId = $_SESSION['user_id'];
        $success = null;

        if ($treeId && $userId) {
            $treesModel = new TreesModel();
            $success = $treesModel->buyTree($treeId, $userId);
    
            if ($success) {
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            } else {
                echo "Error: Failed to buy the tree.";
            }
        } else {
            echo "Error: Invalid data received.";
        }
    }
}
$treeController = new TreeController();