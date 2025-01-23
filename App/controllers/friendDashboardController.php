<?php
require_once '../models/usersModel.php';
require_once '../models/treesModel.php';
require_once '../models/speciesModel.php';
require_once '../models/salesModel.php';

class FriendDashboardController {
    private $userModel;
    private $treeModel;
    private $salesModel;
    private $speciesModel;
    private $user;
    public $purchasedTrees;


     /**
     * Constructor for the controller
     *
     * Starts a session if necessary, initializes models for users, trees, species, and sales
     * Fetches purchased trees for the logged-in user (if any) based on the session user ID
     * Handles a potential incoming POST request with the 'action' parameter:
     * - 'buy-tree': currently redirects to the homepage 
     */
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->userModel = new UsersModel();
        $this->treeModel = new TreesModel();
        $this->speciesModel = new SpeciesModel();
        $this->salesModel = new SalesModel();

        if (isset($_SESSION['user_id'])) {
            $this->user = (int)$_SESSION['user_id'];
            $this->purchasedTrees = $this->getTreesByOwnerId($this->user);
        } else {
            $this->user = null;
            $this->purchasedTrees = [];
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'buy-tree':
                    header('location: http://mytrees.com');
                    break;
            }
        }
    }

     /**
     * Retrieves a list of available trees with their corresponding species information
     *
     */
    public function getAvailableTrees() {
        return $this->treeModel->getAvailableTreesWithSpecies();
    }

     /**
     * Retrieves a list of trees purchased by a specific user.
     *
     */
    public function getTreesByOwnerId(int $userId) {
       return $this->treeModel->getPurchasedTreesByUser($userId);
    }

     /**
     * Attempts to purchase a tree for a user
     *
     * Checks if the tree is available, creates a sale record in the database, and marks the tree as sold
     * Returns `true` on success, `false` otherwise
     */
    public function buyTree($userId, $treeId) {
        if ($this->salesModel->isTreeAvailable($treeId)) {
            if ($this->salesModel->createSale($userId, $treeId)) {
                $this->salesModel->markTreeAsSold($treeId);
                return true;
            }
        }
        return false;
    }

     /**
     * Retrieves user information by ID
     *
     */
    public function getUserById($UserId) {

        return $this->userModel->getUserById($UserId);
    }
}