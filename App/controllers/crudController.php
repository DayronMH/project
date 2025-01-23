<?php
require_once '../models/speciesModel.php';
require_once '../models/treesModel.php';
require_once '../models/treeUpdateModel.php';
require_once '../views/targetPage.php';
class CrudController
{
    private $speciesModel;
    private $treesModel;
    private $updateModel;

     /**
     * Constructor for the controller
     *
     * Starts a session if necessary, initializes models for species, trees, and tree updates
     * Calls a private method `handlePostActions` to handle incoming POST requests
     */
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->speciesModel = new SpeciesModel();
        $this->treesModel = new treesModel();
        $this->updateModel = new treesUpdatesModel() ;
        $this->handlePostActions();
    }

     /**
     * Handles incoming POST requests based on the action parameter
     *
     * It sets session messages for success or error and redirects the user accordingly
     */
    private function handlePostActions()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'createTrees':
                    $this->createTree();
                    break;

                case 'updateTrees':
                    if (isset($_POST['tree_id'])) {
                        $treeId = $_POST['tree_id'];
                        $this->updateTree($treeId);
                    }
                    break;
                case 'edit_species':
                    if (isset($_POST['species_id'])) {
                        $speciesId = $_POST['species_id'];
                        header("Location: ../views/edit.php?id=$speciesId");
                        exit();
                    }
                    break;
                case 'delete_species':
                    if (isset($_POST['species_id'])) {
                        $result = $this->deleteSpecie($_POST['species_id']);
                        $_SESSION['message'] = $result['success'] ?? $result['error'];
                        if (isset($result['success'])) {
                            header('Location: adminDashboard.php');
                        } else {
                            header('Location: ' . $_SERVER['HTTP_REFERER']);
                        }
                        exit();
                    }
                    break;
                case 'createSpecies':
                    $commercialName = trim($_POST['commercial_name']);
                    $scientificName = trim($_POST['scientific_name']);
                    $speciesId = trim($_POST['species_id']);
                    if (empty($commercialName) || empty($scientificName)) {
                        $_SESSION['error'] = "Todos los campos son requeridos";
                    } else {

                        $success = $this->createSpecie($speciesId, $commercialName, $scientificName);

                        if ($success) {
                            $_SESSION['success'] = "Especie creada correctamente";

                            exit();
                        } else {
                            $_SESSION['error'] = "Error al crear la especie";
                        }
                    }
            }
        }
    }

     /**
     * Creates a new species in the database
     *
     * Validates species name emptiness, checks for existing trees associated with the species (to prevent deletion),
     * and calls the `SpeciesModel` to create the species
     * Sets session messages and redirects based on success or failure
     */
    public function createSpecie($speciesId, $commercial_name, $scientific_name)
    {
        try {
            if (empty($commercial_name) || empty($scientific_name)) {
                return ['error' => 'Los nombres comercial y científico son requeridos.'];
            }
            if ($this->speciesModel->hasTreesAssociated($speciesId)) {
                return ['error' => 'La especie ya existe en la base de datos.'];
            }
            if ($this->speciesModel->createSpecie($commercial_name, $scientific_name)) {
                $_SESSION['message'] = 'Especie creada correctamente';
                header('Location: ../views/admindashboard.php');
                exit();
            } else {
                return ['error' => 'Error al crear la especie'];
            }
        } catch (Exception $e) {
            return ['error' => 'Error en el servidor: ' . $e->getMessage()];
        }
    }

     /**
     * Updates an existing tree in the database
     *
     * Handles file upload, validates data, and calls the `treesUpdatesModel` to update the tree information
     * Sets session messages for success or failure and redirects the user
     */
    public function updateTree($tree_id)
    {
        date_default_timezone_set('America/Costa_Rica');
        $updateDate = date("Y-m-d H:i:s");
        if ($_FILES['treeUpd']['name']) {
            $fileName = basename($_FILES['treeUpd']['name']);
            $targetDir = "http://mytrees.com/public/images/";
            $image_url = $targetDir . $fileName;
        } else {
            // Usar la imagen existente
            $image_url = $this->updateModel->getTreePhoto($tree_id);
        }
        if (isset($_POST['available'])) {
            $status = isset($_POST['available']) && $_POST['available'] === '1' ? 1 : 0;
        }
        if (isset($_POST['height'])) {
            $height = $_POST['height'];
        }
        try {
            $height = filter_input(INPUT_POST, 'height', FILTER_SANITIZE_STRING);
            $status = isset($_POST['available']) && $_POST['available'] === '1' ? 1 : 0;
            $result = $this->updateModel->createTreeUpdate($tree_id, $height, $image_url, $status, $updateDate);
            if ($result) {
                setTargetMessage('success', 'Árbol actualizado correctamente');
            } else {
                setTargetMessage('error', 'Error al actualizar el árbol. Por favor, inténtalo de nuevo.');
            }
        } catch (Exception $e) {
            setTargetMessage('error', 'Error en el servidor: ' . $e->getMessage());
            return ['error' => 'Error en el servidor: ' . $e->getMessage()];
        }
    }


     /**
     * Creates a new tree in the database
     *
     * Validates species ID, location, and price, handles file upload, and calls the `treesModel` to create the tree
     * Sets session messages and redirects based on success or failure
     */
    private function createTree()
    {
        try {
            $speciesId = filter_var($_POST['species_id'] ?? null, FILTER_VALIDATE_INT);
            $location = trim($_POST['location'] ?? '');
            $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);            
            $fileName = basename($_FILES['treepic']['name']);
            $targetDir = "http://mytrees.com/public/images/";
            $photo_url =  $targetDir . $fileName ;
            echo $fileName;
            if (!$speciesId) {
                throw new Exception('El ID de la especie es requerido y debe ser válido');
            }
            if (empty($location)) {
                throw new Exception('La ubicación es requerida');
            }
            if ($price <= 0) {
                throw new Exception('El precio debe ser mayor a 0');
            }
            $success = $this->treesModel->createTreeBasic(
                $speciesId,
                $location,
                $price,
                $photo_url,
            );

            if (!$success) {
                throw new Exception('Error al crear el árbol en la base de datos');
            }
            setTargetMessage('success', 'Árbol creado correctamente');
            header("Location: ../views/createTree.php");
            exit();
        } catch (Exception $e) {
            setTargetMessage('error', $e->getMessage());
            header("Location: ../views/createTree.php");
            exit();
        }
    }
    
     /**
     * Deletes a species from the database
     *
     * Checks for existing associated trees and prevents deletion if there are any
     * Calls the `SpeciesModel` to delete the species and sets session messages for success or failure
     */
    public function deleteSpecie($speciesId)
    {
        try {
            $specie = $this->speciesModel->getSpeciesById($speciesId);
            if (!$specie) {
                return ['error' => 'La especie no existe.'];
            }
            if ($this->speciesModel->hasTreesAssociated($speciesId)) {
                return ['error' => 'No se puede eliminar la especie porque tiene árboles asociados.'];
            }
            if ($this->speciesModel->deleteSpecie($speciesId)) {
                return ['success' => 'Especie eliminada correctamente'];
            } else {
                return ['error' => 'Error al eliminar la especie'];
            }
        } catch (Exception $e) {
            return ['error' => 'Error en el servidor: ' . $e->getMessage()];
        }
    }

     /**
     * Retrieves a specific species information by ID
     *
     */
    public function getSpecieById($id)
    {
        return $this->speciesModel->getSpeciesById($id);
    }

     /**
     * Retrieves a list of commercial names for species
     *
     * Likely delegates the data retrieval to the `SpeciesModel`
     */
    public function getSpeciesNames($id)
    {
        return $this->speciesModel->getCommercialNames();
    }

     /**
     * Retrieves information for an editable tree by ID.
     *
     * Fetches tree data and sets session variables for pre-populating an edit form
     */
    public function getEditableTreeById($treeId)
    {
        $tree = $this->getEditableTreeById($treeId);
        $_SESSION['height'] = $tree['height'];
        $_SESSION['specie'] = $tree['commercial_name'];
        $_SESSION['location'] = $tree['location'];
        $_SESSION['available'] = $tree['available'];
    }
}
