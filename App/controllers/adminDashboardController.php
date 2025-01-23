<?php
require_once '../models/usersModel.php';
require_once '../models/treesModel.php';
require_once '../models/speciesModel.php';
require_once '../views/targetPage.php';

class AdminDashboardController
{
    private $userModel;
    private $treeModel;
    private $speciesModel;

    public $friendsCount, $availableTreesCount, $soldTreesCount;

     /**
     * Constructor for the controller
     *
     * Starts a session if necessary, initializes models for users, trees, and species
     * Calls private methods for authentication check, fetching statistics, species management, retrieving friend information, and handling POST requests
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        $this->userModel = new UsersModel();
        $this->treeModel = new TreesModel();
        $this->speciesModel = new SpeciesModel();

        $this->checkAuth();
        $this->fetch_stats();
        $this->speciesCRUD();
        $this->getFriends();
        $this->handlePostActions();
    }

     /**
     * Checks if the user is logged in and redirects them if not
     *
     * Fetches user information from the database based on the session user ID and sets a username session variable 
     */
    private function checkAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: http://mytrees.com");
            exit();
        }
        
        $user = $this->userModel->getUserById($_SESSION['user_id']);
        $_SESSION['username'] = $user['name'];
    }

     /**
     * Handles incoming POST requests based on the action parameter
     *
     * This method currently handles toggling species visibility for the user
     */
    public function handlePostActions() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'view_species':
                    if (isset($_POST['species_id'])) {
                        $this->toggleSpeciesVisibility($_POST['species_id']);
                        header("Location: " . $_SERVER['PHP_SELF']);
                        exit();
                    }
                    break;
            }
        }
    }
    
     /**
     * Toggles the visibility of a species for the user in the session.¿
     *
     * Maintains a list of visible species IDs in the session and updates it based on the requested species ID.¿
     */
    private function toggleSpeciesVisibility($speciesId) {
        if (!isset($_SESSION['visible_species'])) {
            $_SESSION['visible_species'] = [];
        }
        
        $speciesId = (int)$speciesId;
        
        if (in_array($speciesId, $_SESSION['visible_species'])) {
            $_SESSION['visible_species'] = array_values(array_diff($_SESSION['visible_species'], [$speciesId]));
        } else {
            $_SESSION['visible_species'][] = $speciesId;
        }
    }

     /**
     * Retrieves a list of friends for the user and sets it in the session
     *
     */
    public function getFriends()
    {
        $_SESSION['friends'] = $this->userModel->getFriends();
    }

     /**
     * Fetches user dashboard statistics and sets them in the session
     *
     * Retrieves the number of friends, available trees, and sold trees
     */
    public function fetch_stats()
    {
        $_SESSION['friendsCount'] = $this->userModel->countFriends();
        $_SESSION['availableTreesCount'] = $this->treeModel->countAvailableTrees();
        $_SESSION['soldTreesCount'] = $this->treeModel->countSoldTrees();
    }

     /**
     * Retrieves species information and sets it in the session
     *
     * Fetches lists of commercial and scientific names for all species
     */
    public function speciesCRUD()
    {
        $_SESSION['commercial_names'] = $this->speciesModel->getCommercialNames();
        $_SESSION['scientific_names'] = $this->speciesModel->getScientificNames();
    }
}